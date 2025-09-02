<?php
defined('ABSPATH') || exit;

class Snuhy_AI {
  public function init(){ /* place for future AI-powered admin UIs */ }

  // Placeholder: يستخرج كلمات مفتاحية بسيطة من المحتوى (بدون ML حقيقي الآن)
  public static function extract_keywords($text, $limit=10){
    $text = mb_strtolower(wp_strip_all_tags($text));
    preg_match_all('~[a-zA-Z\p{Arabic}]{4,}~u', $text, $m);
    $freq = array_count_values($m[0] ?? []);
    arsort($freq);
    return array_slice(array_keys($freq),0,$limit);
  }

  // Placeholder: يقدّم مرادفات بسيطة (يمكن ربط API لاحقًا)
  public static function anchor_variations($kw){
    $vars = [$kw];
    if (strpos($kw,' ')!==false){
      $vars[] = ucwords($kw);
    }
    return array_unique($vars);
  }
}
