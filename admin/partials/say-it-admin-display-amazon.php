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
// $amazon_voices = $this->amazon_voices['Voices'];
$amazon_voice = ( isset( $options['amazon_voice'] ) && ! empty( $options['amazon_voice'] ) ) ? esc_attr( $options['amazon_voice'] ) : 'Kimberly';
?>

<?php if($this->amazon_aws_error): ?>
    <p class="notice notice-warning inline">Oups, something wrong with Amazon AWS Configuration, please check the Amazon Tab<br><?php echo print_r($this->amazon_aws_error); ?></p>
<?php else: ?>

    <table class="form-table">
        <tbody>

            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>[amazon_voice]">
                        <?php _e( 'Amazon Voice', $this->plugin_name ); ?>
                    </label>
                </th>
                <td>
                <input type="text" value="<?php echo $amazon_voice; ?>" id="<?php echo $this->plugin_name; ?>-amazon_voice" name="<?php echo $this->plugin_name; ?>[amazon_voice]">
                <p class="description">
                    Please enter the Voice ID you want, you can find a list of Amazon Polly Voice here :<br>
                    <a target="_blank" href="https://docs.aws.amazon.com/polly/latest/dg/voicelist.html">https://docs.aws.amazon.com/polly/latest/dg/voicelist.html</a>
                </p>
                </td>
            </tr>

        </tbody>
    </table>
<?php endif; ?>