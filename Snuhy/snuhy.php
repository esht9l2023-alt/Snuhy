<?php
/**
 * Plugin Name: Snuhy – Smart Links & References
 * Description: إدارة الروابط الداخلية + صندوق المراجع + ذكاء اصطناعي + ترخيص دومين واحد.
 * Version: 0.1.0
 * Author: Snuhy
 * Text Domain: snuhy
 * Domain Path: /languages
 */
defined('ABSPATH') || exit;

if (!defined('SNUHY_FILE')) define('SNUHY_FILE', __FILE__);
if (!defined('SNUHY_PATH')) define('SNUHY_PATH', plugin_dir_path(__FILE__));
if (!defined('SNUHY_URL'))  define('SNUHY_URL',  plugin_dir_url(__FILE__));
if (!defined('SNUHY_VER'))  define('SNUHY_VER',  '0.1.0');

require_once SNUHY_PATH.'includes/class-snuhy-activator.php';
require_once SNUHY_PATH.'includes/class-snuhy-deactivator.php';
require_once SNUHY_PATH.'includes/class-snuhy-licensing.php';
require_once SNUHY_PATH.'includes/class-snuhy-admin.php';
require_once SNUHY_PATH.'includes/class-snuhy-settings.php';
require_once SNUHY_PATH.'includes/class-snuhy-linker.php';
require_once SNUHY_PATH.'includes/class-snuhy-references.php';
require_once SNUHY_PATH.'includes/class-snuhy-cron.php';
require_once SNUHY_PATH.'includes/class-snuhy-rest.php';
require_once SNUHY_PATH.'includes/class-snuhy-database.php';
require_once SNUHY_PATH.'includes/class-snuhy-roles.php';
require_once SNUHY_PATH.'includes/class-snuhy-utils.php';
require_once SNUHY_PATH.'includes/class-snuhy-logger.php';
require_once SNUHY_PATH.'includes/class-snuhy-schema.php';
require_once SNUHY_PATH.'includes/class-snuhy-analytics.php';
require_once SNUHY_PATH.'includes/class-snuhy-ai.php';
require_once SNUHY_PATH.'includes/class-snuhy-cli.php';

register_activation_hook(__FILE__, ['Snuhy_Activator','activate']);
register_deactivation_hook(__FILE__, ['Snuhy_Deactivator','deactivate']);

add_action('plugins_loaded', function(){
  load_plugin_textdomain('snuhy', false, dirname(plugin_basename(__FILE__)).'/languages');
  (new Snuhy_Admin())->init();
  (new Snuhy_Settings())->init();
  (new Snuhy_Licensing())->init();
  (new Snuhy_Linker())->init();
  (new Snuhy_References())->init();
  (new Snuhy_Cron())->init();
  (new Snuhy_REST())->init();
  (new Snuhy_Roles())->init();
  (new Snuhy_Analytics())->init();
  (new Snuhy_AI())->init();
});
