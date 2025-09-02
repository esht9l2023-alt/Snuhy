<?php
$refs = Snuhy_References::collect_external_links($content ?? '');
if (!$refs) return;
?>
<section class="snuhy-refbox" aria-label="<?php esc_attr_e('References','snuhy'); ?>">
  <h3><?php esc_html_e('References','snuhy'); ?></h3>
  <ol>
    <?php foreach ($refs as $r): ?>
      <li><a href="<?php echo esc_url($r['url']); ?>" rel="noopener" target="_blank"><?php echo esc_html($r['text']); ?></a></li>
    <?php endforeach; ?>
  </ol>
</section>
<?php echo Snuhy_Schema::references_jsonld($refs); ?>
