<?php
defined('ABSPATH') || exit;

class Snuhy_Yoast {
  public function init(){
    add_action('current_screen', [$this,'maybe_inject_editor_vars']);
  }

  private function has_yoast(){
    return defined('WPSEO_VERSION') || class_exists('WPSEO_Meta');
  }

  public function maybe_inject_editor_vars($screen){
    if (!$this->has_yoast()) return;
    if (!isset($screen->base) || !in_array($screen->base, ['post','page'])) return;

    add_action('admin_print_footer_scripts', function(){
      $post_id = get_the_ID();
      if (!$post_id) return;
      $focus = (string) get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
      ?>
      <script>
      window.SnuhySEO = window.SnuhySEO || {};
      window.SnuhySEO.yoast = { focus: <?php echo wp_json_encode($focus); ?> };
      </script>
      <?php
    });
  }
}
