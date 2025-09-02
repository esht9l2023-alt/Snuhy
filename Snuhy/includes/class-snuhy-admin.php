<?php
defined('ABSPATH') || exit;

class Snuhy_Admin {
  public function init(){
    add_action('admin_menu', [$this,'menu']);
    add_action('admin_enqueue_scripts', [$this,'assets']);
  }

  public function menu(){
    add_menu_page('Snuhy','Snuhy','manage_snuhy','snuhy',[$this,'dashboard'],'dashicons-admin-links',58);
    add_submenu_page('snuhy',__('Links','snuhy'),__('Links','snuhy'),'edit_snuhy_links','snuhy-links',[$this,'links']);
    add_submenu_page('snuhy',__('Settings','snuhy'),__('Settings','snuhy'),'manage_snuhy','snuhy-settings',[$this,'settings']);
  }

  public function assets($hook){
  if (strpos($hook,'snuhy')===false) return;
  wp_enqueue_style('snuhy-admin', SNUHY_URL.'assets/admin.css',[],SNUHY_VER);
  wp_enqueue_script('snuhy-admin', SNUHY_URL.'assets/admin.js',['jquery'],SNUHY_VER,true);
  wp_localize_script('snuhy-admin','SnuhyVars',[
    'nonce'=> wp_create_nonce('wp_rest'),
    'rest' => esc_url_raw( rest_url('/snuhy/v1') )
  ]);
}

  public function dashboard(){ include SNUHY_PATH.'templates/admin/dashboard.php'; }
  public function links(){ include SNUHY_PATH.'templates/admin/links-table.php'; }
  public function settings(){
    echo '<div class="wrap"><h1>Snuhy</h1><form method="post" action="options.php">';
    settings_fields('snuhy_group');
    do_settings_sections('snuhy');
    submit_button();
    echo '</form></div>';
  }
}
