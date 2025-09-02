<?php
defined('ABSPATH') || exit;

class Snuhy_Gutenberg {
  public function init(){
    add_action('enqueue_block_editor_assets', [$this,'editor_assets']);
  }
  public function editor_assets(){
    // نعيد استخدام نفس Nonce/REST vars
    wp_enqueue_script('snuhy-editor', SNUHY_URL.'assets/editor.js', ['wp-plugins','wp-edit-post','wp-element','wp-components','wp-data'], SNUHY_VER, true);
    wp_localize_script('snuhy-editor','SnuhyVars',[
      'nonce'=> wp_create_nonce('wp_rest'),
      'rest' => esc_url_raw( rest_url('/snuhy/v1') )
    ]);
  }
}
