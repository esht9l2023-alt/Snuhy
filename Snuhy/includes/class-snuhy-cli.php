<?php
defined('ABSPATH') || exit;

if (defined('WP_CLI') && WP_CLI) {
  WP_CLI::add_command('snuhy scan', function(){
    $q = new WP_Query(['post_type'=>'any','posts_per_page'=>5,'no_found_rows'=>true]);
    WP_CLI::success('Scanned '.$q->post_count.' posts (demo).');
  });
}
