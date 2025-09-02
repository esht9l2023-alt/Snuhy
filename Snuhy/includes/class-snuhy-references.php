<?php
defined('ABSPATH') || exit;

class Snuhy_References {
  public static function is_external($url){
  $h = home_url();
  return (strpos($url,$h)!==0 && strpos($url,'/')!==0);
}
  
  public function init(){
    add_action('wp_enqueue_scripts', function(){
      wp_enqueue_style('snuhy-box', SNUHY_URL.'assets/box.css',[],SNUHY_VER);
    });
  }

  public static function collect_external_links($content){
    // اجمع كل الروابط الخارجية داخل المحتوى
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>'.$content);
    libxml_clear_errors();

    $links = [];
    foreach ($dom->getElementsByTagName('a') as $a){
      $href = $a->getAttribute('href');
      if (!$href) continue;
      if (0 === strpos($href, home_url())) continue; // داخلي
      if (0 === strpos($href, '/')) continue;        // داخلي نسبي

      $text = trim($a->textContent);
      $links[md5($href)] = ['url'=>$href,'text'=>$text ?: $href];
    }
    return array_values($links);
  }

  public static function append_reference_box($content){
    $refs = self::collect_external_links($content);
    if (!$refs) return $content;

    ob_start();
    include SNUHY_PATH.'templates/reference-box.php';
    $box = ob_get_clean();

    return $content . $box;
  }
}
