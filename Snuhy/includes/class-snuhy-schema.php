<?php
defined('ABSPATH') || exit;

class Snuhy_Schema {
  public static function references_jsonld($refs){
    if (!$refs) return '';
    $items = [];
    foreach ($refs as $r){
      $items[] = [
        '@type'=>'WebPage',
        'url' => $r['url'],
        'name'=> $r['text']
      ];
    }
    return '<script type="application/ld+json">'.wp_json_encode([
      '@context'=>'https://schema.org',
      '@type'=>'CreativeWork',
      'citation'=>$items
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';
  }
}
