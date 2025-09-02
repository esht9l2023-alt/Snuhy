<?php
defined('ABSPATH') || exit;

class Snuhy_REST {
  public function init(){
    add_action('rest_api_init', function(){
      register_rest_route('snuhy/v1','/links',[
        'methods'  => WP_REST_Server::READABLE,
        'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
        'callback' => [$this,'list_links']
      ]);

      register_rest_route('snuhy/v1','/links',[
        'methods'  => WP_REST_Server::CREATABLE,
        'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
        'callback' => [$this,'create_link'],
        'args'     => $this->link_args()
      ]);

      register_rest_route('snuhy/v1','/links/(?P<id>\d+)',[
        'methods'  => WP_REST_Server::EDITABLE, // PUT/PATCH
        'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
        'callback' => [$this,'update_link'],
        'args'     => $this->link_args(true)
      ]);

      register_rest_route('snuhy/v1','/links/(?P<id>\d+)',[
        'methods'  => WP_REST_Server::DELETABLE,
        'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
        'callback' => [$this,'delete_link']
      ]);

      register_rest_route('snuhy/v1','/links/(?P<id>\d+)/toggle',[
        'methods'  => WP_REST_Server::EDITABLE,
        'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
        'callback' => [$this,'toggle_link']
      ]);
    });
  }

  private function link_args($partial=false){
    $args = [
      'keyword' => [
        'sanitize_callback' => 'sanitize_text_field',
        'required' => !$partial
      ],
      'target_url' => [
        'sanitize_callback' => 'esc_url_raw',
        'required' => !$partial
      ],
      'rel' => [
        'sanitize_callback' => 'sanitize_text_field',
        'required' => false
      ],
      'enabled' => [
        'validate_callback' => function($v){ return in_array(intval($v),[0,1],true); },
        'required' => false
      ],
      'type' => [
        'sanitize_callback' => 'sanitize_text_field',
        'required' => false
      ],
    ];
    return $args;
  }

  public function list_links(WP_REST_Request $r){
    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $rows = $wpdb->get_results("SELECT * FROM $t ORDER BY id DESC LIMIT 500", ARRAY_A);
    return rest_ensure_response($rows);
  }

  public function create_link(WP_REST_Request $r){
    $keyword = $r->get_param('keyword');
    $url     = $r->get_param('target_url');
    if (!$keyword || !$url) return new WP_Error('snuhy','missing params',['status'=>400]);

    $type = $r->get_param('type');
    if (!$type) $type = Snuhy_References::is_external($url) ? 'external':'internal';

    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $ok = $wpdb->insert($t, [
      'keyword'    => mb_strtolower($keyword),
      'target_url' => $url,
      'type'       => $type,
      'rel'        => sanitize_text_field($r->get_param('rel') ?: ''),
      'enabled'    => 1
    ]);
    if (!$ok) return new WP_Error('snuhy','db insert failed',['status'=>500]);
    return rest_ensure_response(['ok'=>1,'id'=>$wpdb->insert_id]);
  }

  public function update_link(WP_REST_Request $r){
    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $id = intval($r['id']);
    if ($id<=0) return new WP_Error('snuhy','bad id',['status'=>400]);

    $data = [];
    foreach (['keyword','target_url','rel','enabled','type'] as $k){
      if (null !== $r->get_param($k)){
        $v = $r->get_param($k);
        if ($k==='keyword') $v = mb_strtolower($v);
        $data[$k] = $v;
      }
    }
    if (!$data) return rest_ensure_response(['ok'=>1,'id'=>$id,'noop'=>1]);
    $ok = $wpdb->update($t, $data, ['id'=>$id]);
    if ($ok===false) return new WP_Error('snuhy','db update failed',['status'=>500]);
    return rest_ensure_response(['ok'=>1,'id'=>$id]);
  }

  public function delete_link(WP_REST_Request $r){
    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $id = intval($r['id']);
    if ($id<=0) return new WP_Error('snuhy','bad id',['status'=>400]);
    $ok = $wpdb->delete($t, ['id'=>$id]);
    if (!$ok) return new WP_Error('snuhy','db delete failed',['status'=>500]);
    return rest_ensure_response(['ok'=>1,'id'=>$id]);
  }

  public function toggle_link(WP_REST_Request $r){
    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $id = intval($r['id']);
    if ($id<=0) return new WP_Error('snuhy','bad id',['status'=>400]);

    $row = $wpdb->get_row($wpdb->prepare("SELECT enabled FROM $t WHERE id=%d",$id));
    if (!$row) return new WP_Error('snuhy','not found',['status'=>404]);

    $new = $row->enabled ? 0 : 1;
    $wpdb->update($t, ['enabled'=>$new], ['id'=>$id]);

    return rest_ensure_response(['ok'=>1,'id'=>$id,'enabled'=>$new]);
  }
}
    register_rest_route('snuhy/v1','/links/export',[
  'methods'  => WP_REST_Server::READABLE,
  'permission_callback'=> function(){ return current_user_can('edit_snuhy_links'); },
  'callback' => function(){
    global $wpdb; $t = $wpdb->prefix.'snuhy_links';
    $rows = $wpdb->get_results("SELECT id,keyword,target_url,type,rel,enabled FROM $t ORDER BY id DESC", ARRAY_A);
    $csv = "id,keyword,target_url,type,rel,enabled\n";
    foreach ($rows as $r){
      $line = [
        $r['id'],
        str_replace('"','""',$r['keyword']),
        str_replace('"','""',$r['target_url']),
        $r['type'],
        $r['rel'],
        $r['enabled']
      ];
      $csv .= implode(',', array_map(function($v){ return '"'.$v.'"'; }, $line))."\n";
    }
    return new WP_REST_Response($csv, 200, [
      'Content-Type' => 'text/csv; charset=utf-8',
      'Content-Disposition' => 'attachment; filename="snuhy-links.csv"'
    ]);
  }
]);
