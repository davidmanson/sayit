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
$skin = ( isset( $options['skin'] ) && ! empty( $options['skin'] ) ) ? esc_attr( $options['skin'] ) : 0;
$theme_root = $this->folder . 'public/css/themes';
$skins = @ scandir( $theme_root );
?>

<?php 
if ( ! $skins ) {
    echo "$theme_root is not readable";
}
?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="<?php echo $this->plugin_name; ?>[skin]">
                    <?php _e( 'Skin', $this->plugin_name ); ?>
                </label>
            </th>
            <td>
                <select name="<?php echo $this->plugin_name; ?>[skin]" id="<?php echo $this->plugin_name; ?>-skin">
                    <?php foreach ($skins as $key => $value): ?>
                        <?php if($value != '.' && $value != '..'): ?>
                            <option <?php if ( $skin == $value ) echo 'selected="selected"'; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>