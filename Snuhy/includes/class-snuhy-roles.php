<?php
defined('ABSPATH') || exit;

class Snuhy_Roles {
  public function add_caps(){
    $caps = ['manage_snuhy','edit_snuhy_links','view_snuhy_reports'];
    foreach (['administrator','editor'] as $role_name){
      if ($role = get_role($role_name)){
        foreach ($caps as $cap){ $role->add_cap($cap); }
      }
    }
  }
}
