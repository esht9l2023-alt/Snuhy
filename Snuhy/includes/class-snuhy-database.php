<?php
defined('ABSPATH') || exit;

class Snuhy_Database {
  public function install(){
    global $wpdb;
    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    $charset = $wpdb->get_charset_collate();

    $links = $wpdb->prefix.'snuhy_links';
    $clicks = $wpdb->prefix.'snuhy_clicks';

    $sql_links = "CREATE TABLE $links(
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      keyword VARCHAR(190) NOT NULL,
      target_url TEXT NOT NULL,
      type ENUM('internal','external') DEFAULT 'internal',
      rel VARCHAR(50) DEFAULT '',
      enabled TINYINT(1) DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY(id),
      KEY keyword (keyword)
    ) $charset;";

    $sql_clicks = "CREATE TABLE $clicks(
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      link_id BIGINT UNSIGNED NOT NULL,
      post_id BIGINT UNSIGNED NOT NULL,
      ts DATETIME DEFAULT CURRENT_TIMESTAMP,
      ip VARBINARY(16) NULL,
      ua TEXT NULL,
      PRIMARY KEY(id),
      KEY link_idx (link_id),
      KEY post_idx (post_id)
    ) $charset;";

    dbDelta($sql_links);
    dbDelta($sql_clicks);
  }
}
