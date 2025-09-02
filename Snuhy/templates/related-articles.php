<?php
$related = get_posts(['post_type'=>'post','numberposts'=>3,'orderby'=>'rand']);
if (!$related) return;
?>
<div class="snuhy-related">
  <h3><?php esc_html_e('Related Articles','snuhy'); ?></h3>
  <ul>
    <?php foreach ($related as $p): ?>
      <li><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php echo esc_html(get_the_title($p)); ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
