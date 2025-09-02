<?php
defined('ABSPATH') || exit;

class Snuhy_Logger {
  public static function log($msg, $context=[]){
    if (defined('WP_DEBUG') && WP_DEBUG){
      error_log('[SNUHY] '. $msg .' | '. wp_json_encode($context));
    }
  }
}
