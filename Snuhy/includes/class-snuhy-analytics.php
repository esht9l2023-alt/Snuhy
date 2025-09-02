<?php
defined('ABSPATH') || exit;

class Snuhy_Analytics {
  public function init(){
    add_action('wp_enqueue_scripts', [$this,'front_js']);
    add_action('wp_ajax_snuhy_click', [$this,'log_click']);
    add_action('wp_ajax_nopriv_snuhy_click', [$this,'log_click']);
  }

  public function front_js(){
    wp_add_inline_script('jquery', "
      (function($){
        $(document).on('click','a.snuhy-auto',function(e){
          var post = ".(is_singular()? get_the_ID():0).";
          $.post('".admin_url('admin-ajax.php')."',{
            action:'snuhy_click',
            link: $(this).attr('href'),
            post_id: post
          });
        });
      })(jQuery);
    ");
  }

  public function log_click(){
    global $wpdb; $t = $wpdb->prefix.'snuhy_clicks';
    $url   = esc_url_raw($_POST['link'] ?? '');
    $post  = intval($_POST['post_id'] ?? 0);
    if (!$url) wp_send_json_success();

    // find link_id
    $lt = $wpdb->prefix.'snuhy_links';
    $row = $wpdb->get_row($wpdb->prepare("SELECT id FROM $lt WHERE target_url=%s LIMIT 1",$url));
    if ($row){
      $ip = inet_pton($_SERVER['REMOTE_ADDR'] ?? '');
      $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
      $wpdb->insert($t, ['link_id'=>$row->id,'post_id'=>$post,'ip'=>$ip,'ua'=>$ua]);
    }
    wp_send_json_success();
  }
}
