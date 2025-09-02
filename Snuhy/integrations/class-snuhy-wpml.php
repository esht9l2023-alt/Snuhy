<?php
defined('ABSPATH') || exit;

class Snuhy_Wpml {
  public function init(){
    add_action('current_screen', [$this,'expose_language']);
  }

  private function has_wpml(){
    return defined('ICL_SITEPRESS_VERSION') || function_exists('icl_object_id');
  }

  public function expose_language(){
    if (!$this->has_wpml()) return;
    add_action('admin_print_footer_scripts', function(){
      $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';
      ?>
      <script>
      window.SnuhySEO = window.SnuhySEO || {};
      window.SnuhySEO.lang = <?php echo wp_json_encode($lang); ?>;
      </script>
      <?php
    });
  }
}
