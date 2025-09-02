<?php
defined('ABSPATH') || exit;

class Snuhy_Licensing {
  const OPT_KEY = 'snuhy_license';
  const API     = 'https://your-license-server.example/validate'; // بدّلها لاحقًا

  public function init(){
    add_action('admin_init', [$this,'maybe_validate']);
  }

  public function maybe_validate(){
    $lic = get_option(self::OPT_KEY, ['key'=>'','status'=>'inactive','domain'=>home_url()]);
    if (empty($lic['key'])) return;

    // تحقّق دوري كل 24 ساعة
    $last = get_transient('snuhy_license_checked');
    if ($last) return;
    set_transient('snuhy_license_checked', 1, DAY_IN_SECONDS);

    $resp = wp_remote_post(self::API, [
      'timeout'=>10,
      'body'=>[
        'key'   => $lic['key'],
        'domain'=> parse_url(home_url(), PHP_URL_HOST),
        'site'  => home_url()
      ]
    ]);
    if (is_wp_error($resp)) return;

    $code = wp_remote_retrieve_response_code($resp);
    $data = json_decode(wp_remote_retrieve_body($resp), true);
    if ($code===200 && !empty($data['valid'])){
      $lic['status']='active';
    } else {
      $lic['status']='inactive';
    }
    update_option(self::OPT_KEY, $lic);
  }

  public static function is_active(){
    $lic = get_option(self::OPT_KEY, []);
    return ($lic['status'] ?? '')==='active';
  }
}
