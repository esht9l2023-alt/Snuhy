<?php
defined('ABSPATH') || exit;

class Snuhy_Deactivator {
  public static function deactivate(){
    // لا نحذف الداتا الآن. فقط نوقف الكرون.
    $ts = wp_next_scheduled('snuhy_cron_tick');
    if ($ts) wp_unschedule_event($ts, 'snuhy_cron_tick');
  }
}
