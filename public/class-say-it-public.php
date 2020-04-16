<?php

// Imports the Cloud Client Library
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

// Import the AWS Library
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.david-manson.com
 * @since      1.0.0
 *
 * @package    Say_It
 * @subpackage Say_It/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Say_It
 * @subpackage Say_It/public
 * @author     David Manson <david.manson@me.com>
 */
class Say_It_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $options;

	/**
	 * The Google TTS Client
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $version    The Google TTS Client
	 */
	private $google_tts_client;
	private $google_tts_error;

	/**
	 * The Amazon AWS Client
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $version    The Google TTS Client
	 */
	private $amazon_aws_client;
	private $amazon_aws_error;
	private $amazon_voices;

	/**
	 * The Google TTS Enabled
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $version    The Google TTS Client
	 */
	// private $google_tts_enabled = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->google_tts_error = null;
		$this->options = get_option($this->plugin_name);

		if( isset($this->options['mode']) && $this->options['mode'] == 'google' && isset($this->options['google_tts_key'] ) && ! empty( $this->options['google_tts_key'] ) ){
			$this->google_tts_client = $this->authenticate_google_service();
		}

		if( isset($this->options['mode']) && $this->options['mode'] == 'amazon' && isset($this->options['amazon_polly_key'] ) && ! empty( $this->options['amazon_polly_key'] ) ){
			$this->amazon_aws_client = $this->authenticateAmazonAWS();
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if (array_key_exists("skin",$this->options) && isset($this->options['skin'])){
			$skin = $this->options['skin'];
		}else{
			$skin = 'theme1.css';
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . "css/themes/$skin", array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.sayit.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Authenticate Google Service
	 * @since    1.2.0
	 */
	public function authenticate_google_service() {
		// Get the json credentials from config and load Google TTS
		try{
			$json_credentials = json_decode(html_entity_decode($this->options['google_tts_key']), true);
			$google_tts_client = new TextToSpeechClient(['credentials' => $json_credentials]);
			return $google_tts_client;
		} catch (Exception $e) {
			$this->google_tts_error = $e->getMessage(); // TODO: Make more explicite
			return null;
		}
	}


	/**
	 * Authenticate Amazon AWS
	 * @since    2.1.0
	 */
	public function authenticateAmazonAWS() {
		try{
			$connection = [
				'region'      => $this->options['amazon_polly_region'],
				'version'     => '2016-06-10',
				'credentials' => [
					'key'    => $this->options['amazon_polly_key'],
					'secret' => $this->options['amazon_polly_secret'],
				],
			];
			$client = new \Aws\Polly\PollyClient($connection);

			// One simple request to see if it works
			$this->amazon_voices = $client->describeVoices();

			return $client;
		} catch (Exception $e) {
			$this->amazon_aws_error = $e->getMessage();
			return null;
		}
	}

	/**
	 * Create Amazon Speech from Amazon AWS
	 * @since    1.3.0
	 */
	public static function get_amazon_mp3($aws_client, $text, $voice){

		// Get a uniq md5 based filepath
		$uploads = wp_upload_dir();

		$file_name = md5( strtolower( $text ) );
		
		$relative_path = "/sayit_cache/polly/$voice";

		$upload_url = $uploads['baseurl'] . $relative_path;
		$upload_path = $uploads['basedir'] . $relative_path;

		$file_path = "$upload_path/$file_name.mp3";
		$file_url = "$upload_url/$file_name.mp3";

		// If the file already exist, just return it !
		if(file_exists($file_path)){
			return $file_url;
		}

		// If the file doesn't exist and we don't have aws client, we return null
		if(!isset($aws_client)){
			return null;
		}

		// Make upload dir if no-exist
		wp_mkdir_p( $upload_path );

		$polly_args = [
			'OutputFormat' => 'mp3',
			'Text'         => $text,
			'TextType'     => 'text',
			'LanguageCode' => 'ko-KR',
			'VoiceId'      => $voice,
		];
		$result       = $aws_client->synthesizeSpeech($polly_args);
		$audioContent = $result->get('AudioStream')->getContents();

		// We upload the mp3 in his place
		file_put_contents($file_path, $audioContent);
		return $file_url;
	}


	/**
	 * Create Google Speech from Google Client
	 * @since    1.2.0
	 */
	public static function get_google_mp3($tts_client, $text, $lang="en-US", $sex="male", $speed=1, $custom_voice=null){

		// Get a uniq md5 based filepath
		$uploads = wp_upload_dir();

		$file_name = md5( strtolower( $text ) );
		$speed_folder_name = str_replace('.', '-', $speed);
		
		if($custom_voice){
			$relative_path = "/sayit_cache/$lang/$custom_voice/$speed_folder_name";
		}else{
			$relative_path = "/sayit_cache/$lang/$sex/$speed_folder_name";
		}

		$upload_url = $uploads['baseurl'] . $relative_path;
		$upload_path = $uploads['basedir'] . $relative_path;

		$file_path = "$upload_path/$file_name.mp3";
		$file_url = "$upload_url/$file_name.mp3";

		// If the file already exist, just return it !
		if(file_exists($file_path)){
			return $file_url;
		}
		
		// If the file doesn't exist and we don't have tts_client, we return null
		if(!isset($tts_client)){
			return null;
		}

		// Make upload dir if no-exist
		wp_mkdir_p( $upload_path );

		// sets text to be synthesised
		$synthesis_input = new SynthesisInput();
		$synthesis_input->setText($text);

		// build the voice request
		$voice = new VoiceSelectionParams();
		$voice->setLanguageCode($lang);
		if($sex == "male"){
			$voice->setSsmlGender(SsmlVoiceGender::MALE);
		}else{
			$voice->setSsmlGender(SsmlVoiceGender::FEMALE);
		}
		if(isset($custom_voice)){
			$voice->setName($custom_voice);
		}

		// select the type of audio file you want returned
		$audioConfig = new AudioConfig();
		$audioConfig->setAudioEncoding(AudioEncoding::MP3);
		$audioConfig->setSpeakingRate($speed); /* between 0.25 and 4 */

		// perform text-to-speech request on the text input with selected voice
		// parameters and audio file type
		$response = $tts_client->synthesizeSpeech($synthesis_input, $voice, $audioConfig);
		$audioContent = $response->getAudioContent();

		// the response's audioContent is binary
		file_put_contents($file_path, $audioContent);
		return $file_url;
	}

	/**
	 * Register main shortcode
	 * @since    1.0.0
	 */
	public function shortcode_function( $atts, $content = null ) {
		/* Say it not enable, we just return the content */
		if($this->options['mode'] == 'disabled'){
			return $content;
		}

		/* Get the parameters */
		$args = shortcode_atts(
			array(
				'lang' => $this->options['default_language'],
				'speed' => $this->options['default_speed'],
				'alt' => null,
				'mp3' => null,
				'block' => false
			),
			$atts
		);

		/* Choose between default or alternative text */
		if(isset($args['alt'])){
			$words = $args['alt'];
		}else{
			$words = strip_tags($content);
		}

		/* Choose between inline or block type */
		$blocktype = ($args['block'])?'div':'span';

		/* Default tts attribute to null */
		$google_tts_attribute = '';
		$mp3_attribute = '';

		/* Get mp3 url if google TTS is enabled */
		if($this->options['mode'] == 'google' && isset($this->google_tts_client)){
			try{
				$google_tts_url = $this->get_google_mp3($this->google_tts_client, $words, $this->options['google_language'], $this->options['google_gender'], $this->options['google_speed'], $this->options['google_custom_voice']);
				$mp3_attribute = 'alt-file = "' . $google_tts_url . '"';
			} catch (Exception $e) {
				$this->google_tts_error = $e->getMessage();
			}
		}

		/* Get mp3 url if Amazon Polly is enabled */
		if($this->options['mode'] == 'amazon' && isset($this->amazon_aws_client)){
			try{
				$amazon_polly_url = $this->get_amazon_mp3($this->amazon_aws_client, $words, $this->options['amazon_voice']);
				$mp3_attribute = 'alt-file = "' . $amazon_polly_url . '"';
			} catch (Exception $e) {
				$this->amazon_aws_error = $e->getMessage();
			}
		}

		/* Set alternative mp3 if option enable */
		if(!isset($mp3_attribute) || isset($args['mp3'])){
			if(isset($args['mp3'])){
				$mp3_attribute = 'alt-file = "' . $args['mp3'] . '"';
			}
		}

		/* Return everything we need for the javascript companion */
		$return = '<'.$blocktype.' class="sayit" data-say-content="'.$args['alt'].'" data-error="'.$this->google_tts_error.'" data-speed="'.$args['speed'].'" data-lang="'.$args['lang'].'" '.$google_tts_attribute.' '.$mp3_attribute.'>';
		$return .= $content;
		$return .= '<span class="sayit-tooltip">'.__('Écouter').'</span>';
		$return .= '</'.$blocktype.'>';
		return $return;
	}

}
