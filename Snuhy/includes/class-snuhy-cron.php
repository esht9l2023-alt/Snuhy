<?php
defined('ABSPATH') || exit;

class Snuhy_Cron {
  const HOOK = 'snuhy_cron_tick';

  public function init(){
    add_action('init', [$this,'ensure_schedule']);
    add_action(self::HOOK, [$this,'tick']);
  }

  public function ensure_schedule(){
    $enabled  = (int) Snuhy_Utils::opt('schedule_enabled', 1);
    $interval = Snuhy_Utils::opt('schedule_interval', 'hourly'); // hourly|twicedaily|daily

    if ($enabled && !wp_next_scheduled(self::HOOK)) {
      wp_schedule_event(time()+300, $interval, self::HOOK);
    }

    if (!$enabled && wp_next_scheduled(self::HOOK)) {
      wp_clear_scheduled_hook(self::HOOK);
    }
  }

  public function tick(){
    // TODO: هنا ضع منطق الإضافة/الإزالة التدريجي للروابط
    do_action('snuhy/cron_tick'); // hook للتوسعة
  }
}
