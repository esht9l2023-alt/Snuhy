<?php
defined('ABSPATH') || exit;

class Snuhy_Activator {
  public static function activate(){
    // default options
    add_option('snuhy_settings', [
      'max_links_per_post' => 5,
      'anchor_variations'  => 1,
      'reference_box'      => 1,
      'trusted_domains'    => "wikipedia.org\nwho.int\nbbc.com",
      'schedule_enabled'   => 1,
      'schedule_interval'  => 'hourly',
    ]);

    // roles & caps
    if (class_exists('Snuhy_Roles')) { (new Snuhy_Roles())->add_caps(); }

    // db
    if (class_exists('Snuhy_Database')) { (new Snuhy_Database())->install(); }

    // cron
    if (!wp_next_scheduled('snuhy_cron_tick')) {
      wp_schedule_event(time()+300, 'hourly', 'snuhy_cron_tick');
    }
  }
}
