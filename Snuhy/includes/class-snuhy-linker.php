<?php
defined('ABSPATH') || exit;

class Snuhy_Linker {
  public function init(){
    add_filter('the_content', [$this,'autolink'], 20);
  }

  private function get_links_map(){
    global $wpdb;
    $table = $wpdb->prefix.'snuhy_links';
    $rows  = $wpdb->get_results("SELECT * FROM $table WHERE enabled=1 ORDER BY id DESC LIMIT 500");
    $map = [];
    foreach ($rows as $r){
      $map[mb_strtolower($r->keyword)][] = $r;
    }
    return $map;
  }

  public function autolink($content){
    if (is_admin() || is_feed() || is_singular()===false) return $content;

    $max = (int)Snuhy_Utils::opt('max_links_per_post',5);
    if ($max<=0) return $content;

    $map = $this->get_links_map();
    if (!$map) return $content;

    // لا نربط داخل عناوين/روابط/كود
    $placeholders = [];
    $content = $this->mask_tags($content, $placeholders, ['a','h1','h2','h3','h4','pre','code']);

    $added = 0;
    foreach ($map as $kw => $rows){
      if ($added >= $max) break;
      $regex = '~(?<!["\'>])\b('.preg_quote($kw,'~').')\b~iu';
      $row   = $rows[0]; // ببساطة أول هدف
      $rel   = esc_attr($row->rel);
      $url   = esc_url($row->target_url);

      $content = preg_replace_callback($regex, function($m) use (&$added,$max,$url,$rel){
        if ($added >= $max) return $m[0];
        $anchor = $m[1];
        $tag = '<a href="'.$url.'" '.($rel?'rel="'.$rel.'"':'').' class="snuhy-auto">'.$anchor.'</a>';
        $added++;
        return $tag;
      }, $content, -1, $count);
    }

    $content = $this->unmask_tags($content, $placeholders);

    // مرجع خارجي لاحقًا
    if (Snuhy_Utils::opt('reference_box',1)){
      $content = Snuhy_References::append_reference_box($content);
    }
    return $content;
  }

  private function mask_tags($html,&$store,$tags){
    foreach ($tags as $t){
      $html = preg_replace_callback("~<{$t}[^>]*>.*?</{$t}>~is", function($m) use (&$store){
        $k = '##SNUHY#'.count($store).'#';
        $store[$k] = $m[0];
        return $k;
      }, $html);
    }
    return $html;
  }
  private function unmask_tags($html,$store){
    return strtr($html,$store);
  }
}
