<?php
defined('ABSPATH') || exit;

class Snuhy_Polylang {
  public function init(){
    add_action('current_screen', [$this,'expose_language']);
  }

  private function has_pll(){
    return function_exists('pll_current_language') || defined('POLYLANG_VERSION');
  }

  public function expose_language(){
    if (!$this->has_pll()) return;
    add_action('admin_print_footer_scripts', function(){
      $lang = function_exists('pll_current_language') ? pll_current_language('slug') : '';
      ?>
      <script>
      window.SnuhySEO = window.SnuhySEO || {};
      window.SnuhySEO.lang = <?php echo wp_json_encode($lang); ?>;
      </script>
      <?php
    });
  }
}
