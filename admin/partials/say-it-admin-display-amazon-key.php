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
$amazon_polly_region = ( isset($options['amazon_polly_region']) ) ? $options['amazon_polly_region'] : '';
$amazon_polly_key = ( isset($options['amazon_polly_key']) ) ? $options['amazon_polly_key'] : '';
$amazon_polly_secret = ( isset($options['amazon_polly_secret']) ) ? $options['amazon_polly_secret'] : '';
?>

<table class="form-table">
    <tbody>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[amazon_polly_region]">
                    <?php _e( 'Amazon AWS Region', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <div>
                    <input type="text" value="<?php echo $amazon_polly_region; ?>" id="<?php echo $this->plugin_name; ?>-amazon_polly_region" name="<?php echo $this->plugin_name; ?>[amazon_polly_region]">
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[amazon_polly_key]">
                    <?php _e( 'Amazon AWS Key', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <div>
                    <input type="text" value="<?php echo $amazon_polly_key; ?>" id="<?php echo $this->plugin_name; ?>-amazon_polly_key" name="<?php echo $this->plugin_name; ?>[amazon_polly_key]">
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[amazon_polly_secret]">
                    <?php _e( 'Amazon AWS Secret', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <div>
                    <input type="text" value="<?php echo $amazon_polly_secret; ?>" id="<?php echo $this->plugin_name; ?>-amazon_polly_secret" name="<?php echo $this->plugin_name; ?>[amazon_polly_secret]">
                </div>
            </td>
        </tr>
    </tbody>
</table>