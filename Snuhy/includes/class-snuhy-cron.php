<?php
defined('ABSPATH') || exit;

class Snuhy_Cron {
  public function init(){
    add_action('snuhy_cron_tick', [$this,'run']);
  }

  public function run(){
    if (!Snuhy_Utils::opt('schedule_enabled',1)) return;
    // مكان لإضافة/إزالة/تدوير الروابط تلقائيًا
    Snuhy_Logger::log('cron_tick', ['interval'=>Snuhy_Utils::opt('schedule_interval','hourly')]);
  }
}
