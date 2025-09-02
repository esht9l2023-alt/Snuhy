<?php
/**
 * Plugin Name: Snuhy
 * Description: Smart internal/external linking with admin UI, REST API, and reference box.
 * Version: 0.3.0
 * Author: Snuhy
 * Text Domain: snuhy
 */
if (!defined('ABSPATH')) { exit; }

define('SNUHY_VER',  '0.3.0');
define('SNUHY_PATH', plugin_dir_path(__FILE__));
define('SNUHY_URL',  plugin_dir_url(__FILE__));

add_action('plugins_loaded', function(){
    load_plugin_textdomain('snuhy', false, dirname(plugin_basename(__FILE__)).'/languages/');
});

/** Activation/Deactivation */
register_activation_hook(__FILE__, function(){
    if (!get_option('snuhy_links'))   update_option('snuhy_links', array());
    if (!get_option('snuhy_options')) update_option('snuhy_options', array());
    if (!wp_next_scheduled('snuhy_cron_tick')) {
        wp_schedule_event(time()+300, 'hourly', 'snuhy_cron_tick');
    }
});
register_deactivation_hook(__FILE__, function(){
    wp_clear_scheduled_hook('snuhy_cron_tick');
});

/** Includes */
require_once SNUHY_PATH.'includes/class-snuhy-settings.php';
require_once SNUHY_PATH.'includes/class-snuhy-admin.php';
require_once SNUHY_PATH.'includes/class-snuhy-rest.php';
require_once SNUHY_PATH.'includes/class-snuhy-linker.php';
require_once SNUHY_PATH.'includes/class-snuhy-references.php';
require_once SNUHY_PATH.'includes/class-snuhy-cron.php';

/** Bootstrap */
add_action('plugins_loaded', function(){
    if (is_admin()) {
        (new Snuhy_Admin())->init();
    }
    (new Snuhy_Rest())->init();
    (new Snuhy_Linker())->init();
    (new Snuhy_References())->init();
    (new Snuhy_Cron())->init();
});
