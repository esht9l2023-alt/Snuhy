<?php
defined('ABSPATH') || exit;

class Snuhy_Utils {
  public static function opt($key, $default=null){
    $opt = get_option('snuhy_settings', []);
    return isset($opt[$key]) ? $opt[$key] : $default;
  }
  public static function is_admin_cap(){ return current_user_can('manage_snuhy'); }
  public static function sanitize_multiline_domains($text){
    $lines = array_filter(array_map('trim', explode("\n", (string)$text)));
    $lines = array_map(fn($d)=>preg_replace('~^https?://~i','',$d), $lines);
    return implode("\n", array_unique($lines));
  }
}
