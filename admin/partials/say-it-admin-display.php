<?php
include 'parts/help.php';

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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <!-- The title -->
    <h1>Say It! <?php _e('Options', $this->plugin_name); ?></h1>
    
    <!-- The tabs -->
    <h2 class="nav-tab-wrapper say-it-tabs">
        <a href="#general" class="nav-tab nav-tab-active">General</a>
        <a href="#skin" class="nav-tab">Skin</a>
        <a href="#google_tts" class="nav-tab">Google TTS</a>
        <a href="#amazon_key" class="nav-tab">Amazon Polly</a>
        <a href="#help" class="nav-tab">Help</a>
    </h2>

    <!-- The main form -->
    <form method="post" name="cleanup_options" action="options.php">

        <?php
            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
        ?>

        <div id="google_tts" class="say-it-tab">
            <?php $this->display_plugin_setup_google_key(); ?>
        </div>

        <div id="amazon_key" class="say-it-tab">
            <?php $this->display_plugin_setup_amazon_key(); ?>
        </div>

        <div id="skin" class="say-it-tab">
            <?php $this->display_plugin_setup_skin(); ?>
        </div>

        <div id="general" class="say-it-tab active">
            <?php $this->display_plugin_setup_mode(); ?>

            <div id="sayit_html5_wrapper" class="sayit_admin_wrapper">
                <?php $this->display_plugin_setup_html5(); ?>
            </div>

            <div id="sayit_google_wrapper" class="sayit_admin_wrapper">
                <?php $this->display_plugin_setup_google(); ?>
            </div>

            <div id="sayit_amazon_wrapper" class="sayit_admin_wrapper">
                <?php $this->display_plugin_setup_amazon(); ?>
            </div>

        </div>

        <?php submit_button( __( 'Save all changes', $this->plugin_name ), 'primary','submit', TRUE ); ?>
    </form>
    

    <div id="help" class="say-it-tab">
        <?php print_help($this) ?>
        <?php $this->display_plugin_setup_debug(); ?>
    </div>

</div>