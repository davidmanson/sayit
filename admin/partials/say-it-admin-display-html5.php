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
$default_language = ( isset( $options['default_language'] ) && ! empty( $options['default_language'] ) ) ? esc_attr( $options['default_language'] ) : 'en-US';
$default_speed = ( isset( $options['default_speed'] ) && ! empty( $options['default_speed'] ) ) ? esc_attr( $options['default_speed'] ) : 1;

$languages = array(
    'en-US' => __('English (United States)', $this->plugin_name),
    'en-GB' => __('English (United Kingdom)', $this->plugin_name),
    'fr-FR' => __('French (France)', $this->plugin_name),
    'de-DE' => __('German (Germany)', $this->plugin_name),
    'ko-KR' => __('Korean (South Korea)', $this->plugin_name),
    'es-ES' => __('Spanish (Spain)', $this->plugin_name)
);
?>

<table class="form-table">
    <tbody>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[default_language]">
                    <?php _e( 'Default language', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
            <select name="<?php echo $this->plugin_name; ?>[default_language]" id="<?php echo $this->plugin_name; ?>-default_language">
                <?php foreach ($languages as $key => $value): ?>
                    <option <?php if ( $default_language == $key ) echo 'selected="selected"'; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[default_speed]">
                    <?php _e( 'Speed', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <input type="range" min="0.6" max="1.4" step="0.2" name="<?php echo $this->plugin_name; ?>[default_speed]" value="<?php if( ! empty( $default_speed ) ) echo $default_speed; else echo '1'; ?>"/>
            </td>
        </tr>

    </tbody>
</table>