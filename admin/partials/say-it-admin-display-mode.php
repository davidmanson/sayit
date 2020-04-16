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
$mode = ( isset( $options['mode'] ) && ! empty( $options['mode'] ) ) ? esc_attr( $options['mode'] ) : 'html5';

?>

<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[mode]">
                    <?php _e( 'Mode', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
            <select name="<?php echo $this->plugin_name; ?>[mode]" id="<?php echo $this->plugin_name; ?>-mode">
                <option <?php if ( $mode == 'html5' ) echo 'selected="selected"'; ?> value="html5">HTML5</option>
                <option <?php if ( $mode == 'google' ) echo 'selected="selected"'; ?> value="google">Google TTS</option>
                <option <?php if ( $mode == 'amazon' ) echo 'selected="selected"'; ?> value="amazon">Amazon Polly</option>
                <option <?php if ( $mode == 'disabled' ) echo 'selected="selected"'; ?> value="disabled">disabled</option>
            </select>
            </td>
        </tr>
    </tbody>
</table>