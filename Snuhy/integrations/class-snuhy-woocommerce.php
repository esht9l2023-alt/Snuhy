<?php
defined('ABSPATH') || exit;

class Snuhy_Woocommerce {
  public function init(){
    add_action('init', [$this,'maybe_hook']);
  }

  private function has_woo(){
    return class_exists('WooCommerce');
  }

  public function maybe_hook(){
    if (!$this->has_woo()) return;

    // طبّق فلاتر الربط التلقائي على الوصف القصير للمنتج
    add_filter('woocommerce_short_description', function($html){
      if (is_admin()) return $html;
      return apply_filters('the_content', $html);
    }, 20);
  }
}
