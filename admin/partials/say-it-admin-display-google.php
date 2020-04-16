<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.david-manson.com
 * @since      1.0.0
 *
 * @package    Say_It
 * @subpackage Say_It/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

$google_langs = $this->get_google_languages();
$google_languages = Array();
foreach ($google_langs as $key => $value){
    $google_languages[$key] = $value['formated'];
}
$google_voices = $this->get_voices();

$options = get_option($this->plugin_name);
$google_language = ( isset( $options['google_language'] ) && ! empty( $options['google_language'] ) ) ? esc_attr( $options['google_language'] ) : 'en-US';
$google_gender = ( isset( $options['google_gender'] ) && ! empty( $options['google_gender'] ) ) ? esc_attr( $options['google_gender'] ) : 'male';
$google_speed = ( isset( $options['google_speed'] ) && ! empty( $options['google_speed'] ) ) ? esc_attr( $options['google_speed'] ) : 1;
$google_custom_voice = ( isset( $options['google_custom_voice'] ) && ! empty( $options['google_custom_voice'] ) ) ? esc_attr( $options['google_custom_voice'] ) : '';

?>

<?php if($this->google_tts_error): ?>
    <p class="notice notice-warning inline">Oups, something wrong with Google TTS Configuration, please check the Google TTS Tab</p>
<?php else: ?>

    <table class="form-table">
        <tbody>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>[google_language]">
                        <?php _e( 'Google language', $this->plugin_name ); ?>
                    </label>
                </th>
                <td>
                <select name="<?php echo $this->plugin_name; ?>[google_language]" id="<?php echo $this->plugin_name; ?>-google_language">
                    <?php foreach ($google_languages as $key => $value): ?>
                        <option <?php if ( $google_language == $key ) echo 'selected="selected"'; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>[google_gender]">
                        <?php _e( 'Google gender', $this->plugin_name ); ?>
                    </label>
                </th>
                <td>
                <select name="<?php echo $this->plugin_name; ?>[google_gender]" id="<?php echo $this->plugin_name; ?>-google_gender">
                    <option <?php if ( $google_gender == 'male' ) echo 'selected="selected"'; ?> value="male">Male</option>
                    <option <?php if ( $google_gender == 'female' ) echo 'selected="selected"'; ?> value="female">Female</option>
                </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>[google_custom_voice]">
                        <?php _e( 'Google Voice', $this->plugin_name ); ?>
                    </label>
                </th>
                <td>
                    <select name="<?php echo $this->plugin_name; ?>[google_custom_voice]" id="<?php echo $this->plugin_name; ?>-google_custom_voice">
                        <?php foreach ($google_voices as $value): ?>
                            <option data-lang="<?php echo $value['language']; ?>" data-gender="<?php echo $value['gender']; ?>" <?php if ( $google_custom_voice == $value['name'] ) echo 'selected="selected"'; ?> value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>[google_speed]">
                        <?php _e( 'Google Voice speed', $this->plugin_name ); ?>
                    </label>
                </th>
                <td>
                    <input type="range" min="0.4" max="1.6" step="0.2" name="<?php echo $this->plugin_name; ?>[google_speed]" value="<?php if( ! empty( $google_speed ) ) echo $google_speed; else echo '1'; ?>"/>
                </td>
            </tr>

        </tbody>
    </table>
<?php endif; ?>