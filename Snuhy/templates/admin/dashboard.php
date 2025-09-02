<div class="wrap snuhy-admin">
  <h1>Snuhy – Dashboard</h1>
  <p><?php esc_html_e('Internal network overview & recent activity (demo).','snuhy'); ?></p>
  <div class="snuhy-cards">
    <div class="card"><strong><?php esc_html_e('Active Links','snuhy'); ?>:</strong>
      <?php global $wpdb; echo intval($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}snuhy_links WHERE enabled=1")); ?>
    </div>
    <div class="card"><strong><?php esc_html_e('Clicks (7d)','snuhy'); ?>:</strong>
      <?php echo intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}snuhy_clicks WHERE ts >= %s", gmdate('Y-m-d H:i:s', time()-7*DAY_IN_SECONDS)))); ?>
    </div>
  </div>
</div>
