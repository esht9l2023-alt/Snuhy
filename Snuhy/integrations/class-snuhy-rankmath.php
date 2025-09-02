<?php
defined('ABSPATH') || exit;

class Snuhy_Rankmath {
  public function init(){
    add_action('current_screen', [$this,'maybe_inject_editor_vars']);
  }

  private function has_rankmath(){
    return defined('RANK_MATH_VERSION') || class_exists('RankMath');
  }

  public function maybe_inject_editor_vars($screen){
    if (!$this->has_rankmath()) return;
    if (!isset($screen->base) || !in_array($screen->base, ['post','page'])) return;

    add_action('admin_print_footer_scripts', function(){
      $post_id = get_the_ID();
      if (!$post_id) return;
      $raw = (string) get_post_meta($post_id, 'rank_math_focus_keyword', true);
      $arr = array_values(array_filter(array_map('trim', explode(',', $raw))));
      ?>
      <script>
      window.SnuhySEO = window.SnuhySEO || {};
      window.SnuhySEO.rankmath = { focus: <?php echo wp_json_encode($arr); ?> };
      </script>
      <?php
    });
  }
}
