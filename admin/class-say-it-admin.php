<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.david-manson.com
 * @since      1.0.0
 *
 * @package    Say_It
 * @subpackage Say_It/admin
 */

/**
 * Those functions are provided for demonstration purposes only.
 *
 * An instance of this class should be passed to the run() function
 * defined in Say_It_Loader as all of the hooks are defined
 * in that particular class.
 *
 * The Say_It_Loader will then create the relationship
 * between the defined hooks and the functions defined in this
 * class.
 */

 // Imports the Cloud Client Library
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

 // Imports the AWS Client Library
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Say_It
 * @subpackage Say_It/admin
 * @author     David Manson <david.manson@me.com>
 */
class Say_It_Admin {

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
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $options;

	/**
	 * The Folder of this plugin.
	 *
	 * @since    2.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $folder;

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $folder ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->folder = $folder;
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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/say-it-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style('wp-codemirror');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'application/json'));
		wp_localize_script('jquery', 'cm_settings', $cm_settings);
		wp_enqueue_script('wp-theme-plugin-editor');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/say-it-admin.js', array( 'jquery' ), $this->version, false );
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
			$synthesis_input = new SynthesisInput();
			$synthesis_input->setText('test');
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode('en-US');
			$voice->setSsmlGender(SsmlVoiceGender::MALE);
			$audioConfig = new AudioConfig();
			$audioConfig->setAudioEncoding(AudioEncoding::MP3);
			$response = $google_tts_client->synthesizeSpeech($synthesis_input, $voice, $audioConfig);
			return $google_tts_client;
		} catch (Exception $e) {
			$this->google_tts_error = $e->getMessage();
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
			$this->amazon_voices = $client->describeVoices();
			return $client;
		} catch (Exception $e) {
			$this->amazon_aws_error = $e->getMessage();
			return null;
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_submenu_page( 'options-general.php', 'Say It! Options', 'Say It!', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
		);
		return array_merge(  $settings_link, $links );
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display.php' );
	}

	/**
	 * Render the HTML5 Options partials
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_html5() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-html5.php' );
	}

	/**
	 * Render the Google Settings partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_google() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-google.php' );
	}


	/**
	 * Render the Amazon Settings partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_amazon() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-amazon.php' );
	}


	/**
	 * Render the Google Key TTS Options partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_google_key() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-google-key.php' );
	}

	/**
	 * Render the Amazon Polly Key Options partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_amazon_key() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-amazon-key.php' );
	}

	/**
	 * Render the Skin Options partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_skin() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-skin.php' );
	}

	/**
	 * Render the Skin Options partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_debug() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-debug.php' );
	}

	/**
	 * Render the Mode selector partial
	 *
	 * @since    2.2.0
	 */
	public function display_plugin_setup_mode() {
		include_once( 'partials/' . $this->plugin_name . '-admin-display-mode.php' );
	}

	/**
	 * Validate fields from admin area plugin settings form
	 * @param  mixed $input as field form settings form
	 * @return mixed as validated fields
	 */
	public function validate($input) {
		$valid = array();
		$valid['mode'] = ( isset($input['mode'] ) && ! empty( $input['mode'] ) ) ? esc_attr($input['mode']) : 'html5';
		$valid['default_language'] = ( isset($input['default_language'] ) && ! empty( $input['default_language'] ) ) ? esc_attr($input['default_language']) : 'en-US';
		$valid['default_speed'] = ( isset( $input['default_speed'] ) && ! empty( $input['default_speed'] ) ) ? esc_attr($input['default_speed']) : '1';
		$valid['google_tts_key'] = ( isset($input['google_tts_key'] ) && ! empty( $input['google_tts_key'] ) ) ? esc_attr($input['google_tts_key']) : null;
		$valid['google_language'] = ( isset($input['google_language'] ) && ! empty( $input['google_language'] ) ) ? esc_attr($input['google_language']) : 'en-US';
		$valid['google_gender'] = ( isset($input['google_gender'] ) && ! empty( $input['google_gender'] ) ) ? esc_attr($input['google_gender']) : 'male';
		$valid['google_speed'] = ( isset( $input['google_speed'] ) && ! empty( $input['google_speed'] ) ) ? esc_attr( $input['google_speed'] ) : '1';
		$valid['google_custom_voice'] = ( isset( $input['google_custom_voice'] ) && ! empty( $input['google_custom_voice'] ) ) ? esc_attr( $input['google_custom_voice'] ) : null;
		$valid['amazon_polly_region'] = ( isset( $input['amazon_polly_region'] ) && ! empty( $input['amazon_polly_region'] ) ) ? esc_attr( $input['amazon_polly_region'] ) : null;
		$valid['amazon_polly_key'] = ( isset( $input['amazon_polly_key'] ) && ! empty( $input['amazon_polly_key'] ) ) ? esc_attr( $input['amazon_polly_key'] ) : null;
		$valid['amazon_polly_secret'] = ( isset( $input['amazon_polly_secret'] ) && ! empty( $input['amazon_polly_secret'] ) ) ? esc_attr( $input['amazon_polly_secret'] ) : null;
		$valid['amazon_voice'] = ( isset( $input['amazon_voice'] ) && ! empty( $input['amazon_voice'] ) ) ? esc_attr( $input['amazon_voice'] ) : 'Kimberly';
		$valid['tooltip_text'] = ( isset( $input['tooltip_text'] ) && ! empty( $input['tooltip_text'] ) ) ? esc_attr( $input['tooltip_text'] ) : 'Listen';

		$valid['skin'] = ( isset( $input['skin'] ) && ! empty( $input['skin'] ) ) ? esc_attr( $input['skin'] ) : null;

		return $valid;
	}
	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name, array( $this, 'validate' ) );
	}

	public function get_voices()
	{
		if(!isset($this->google_tts_client)) return array();

		// perform list voices request
		$response = $this->google_tts_client->listVoices();
		$voices = $response->getVoices();

		// Init the return array
		$formated_voices = Array();

		foreach ($voices as $voice) {

			$voice_name = $voice->getName();
			$ssmlVoiceGender = ['UNSPECIFIED', 'MALE', 'FEMALE', 'NEUTRAL'];
			$gender = $voice->getSsmlGender();
			$voice_gender = $ssmlVoiceGender[$gender];

			foreach ($voice->getLanguageCodes() as $languageCode) {
				array_push($formated_voices, array(
					'language' => $languageCode,
					'name' => $voice_name,
					'gender' => $voice_gender
				));
				
			}

		}

		return $formated_voices;
	}

	public function get_google_languages()
	{
		if(!isset($this->google_tts_client)) return array();

		// perform list voices request
		$response = $this->google_tts_client->listVoices();
		$voices = $response->getVoices();

		// Init the return array
		$formated_voices = Array();
		$language_codes = include 'inc/language_codes.php';


		foreach ($voices as $voice) {

			$voice_name = $voice->getName();
			$ssmlVoiceGender = ['UNSPECIFIED', 'MALE', 'FEMALE', 'NEUTRAL'];
			$gender = $voice->getSsmlGender();
			$voice_gender = $ssmlVoiceGender[$gender];

			// display the supported language codes for this voice. example: 'en-US'
			foreach ($voice->getLanguageCodes() as $languageCode) {
				if(!isset($language_codes[$languageCode])){continue;};
				if(!isset($formated_voices[$languageCode])){
					$formated_voices[$languageCode] = Array(
						'formated' => $language_codes[$languageCode],
						'voices' => Array()
					);
				}
				array_push($formated_voices[$languageCode]['voices'], array(
					'name' => $voice_name,
					'gender' => $voice_gender
				));
				
			}

		}
		
		return $formated_voices;
	}
}