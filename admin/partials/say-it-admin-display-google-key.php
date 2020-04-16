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

$options = get_option($this->plugin_name);
$google_tts_key = ( isset($options['google_tts_key']) ) ? $options['google_tts_key'] : '';

?>

<table class="form-table">
    <tbody>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[google_tts_key]">
                    <?php _e( 'Google Text To Speech Json Key', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <div style="max-width: 500px;">
                    <textarea id="<?php echo $this->plugin_name; ?>-google_tts_key" cols="10" rows="6" class="code google_tts_area" name="<?php echo $this->plugin_name; ?>[google_tts_key]"><?php echo $google_tts_key; ?></textarea>
                    <p class="description">
                        To get the Json Key content, please follow the first 4 steps of the following instructions :<br>
                        <a target="_blank" href="https://cloud.google.com/text-to-speech/docs/quickstart-protocol">https://cloud.google.com/text-to-speech/docs/quickstart-protocol</a><br>
                        Then past the content of the json here.
                    </p>
                </div>
            </td>
        </tr>
    </tbody>
</table>