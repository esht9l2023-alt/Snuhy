<?php
defined('ABSPATH') || exit;

class Snuhy_Settings {
  public function init(){
    add_action('admin_init', [$this,'register']);
  }

  public function register(){
    register_setting('snuhy_group','snuhy_settings',[$this,'sanitize']);

    add_settings_section('snuhy_main', __('General','snuhy'), function(){
      echo '<p>'.esc_html__('General options for Snuhy.','snuhy').'</p>';
    }, 'snuhy');

    add_settings_field('max_links_per_post', __('Max links per post','snuhy'), function(){
      $val = intval(Snuhy_Utils::opt('max_links_per_post',5));
      echo '<input type="number" name="snuhy_settings[max_links_per_post]" min="0" value="'.esc_attr($val).'">';
    }, 'snuhy','snuhy_main');

    add_settings_field('anchor_variations', __('Anchor variations','snuhy'), function(){
      $val = intval(Snuhy_Utils::opt('anchor_variations',1));
      echo '<input type="checkbox" name="snuhy_settings[anchor_variations]" value="1" '.checked($val,1,false).'>';
    }, 'snuhy','snuhy_main');

    add_settings_field('reference_box', __('Enable Reference Box','snuhy'), function(){
      $val = intval(Snuhy_Utils::opt('reference_box',1));
      echo '<input type="checkbox" name="snuhy_settings[reference_box]" value="1" '.checked($val,1,false).'>';
    }, 'snuhy','snuhy_main');

    add_settings_field('trusted_domains', __('Trusted domains (one per line)','snuhy'), function(){
      $val = Snuhy_Utils::opt('trusted_domains',"wikipedia.org\nwho.int\nbbc.com");
      echo '<textarea name="snuhy_settings[trusted_domains]" rows="5" cols="50">'.esc_textarea($val).'</textarea>';
    }, 'snuhy','snuhy_main');

    add_settings_field('schedule_enabled', __('Enable schedule','snuhy'), function(){
      $v = intval(Snuhy_Utils::opt('schedule_enabled',1));
      echo '<input type="checkbox" name="snuhy_settings[schedule_enabled]" value="1" '.checked($v,1,false).'>';
    }, 'snuhy','snuhy_main');

    add_settings_field('schedule_interval', __('Schedule interval','snuhy'), function(){
      $v = Snuhy_Utils::opt('schedule_interval','hourly');
      echo '<select name="snuhy_settings[schedule_interval]">';
      foreach (['hourly','twicedaily','daily'] as $i){
        echo '<option value="'.$i.'" '.selected($v,$i,false).'>'.$i.'</option>';
      }
      echo '</select>';
    }, 'snuhy','snuhy_main');
  }

  public function sanitize($opts){
    $opts['max_links_per_post'] = max(0, intval($opts['max_links_per_post'] ?? 5));
    $opts['anchor_variations']  = !empty($opts['anchor_variations']) ? 1 : 0;
    $opts['reference_box']      = !empty($opts['reference_box']) ? 1 : 0;
    $opts['schedule_enabled']   = !empty($opts['schedule_enabled']) ? 1 : 0;
    $opts['schedule_interval']  = in_array($opts['schedule_interval'] ?? 'hourly',['hourly','twicedaily','daily']) ? $opts['schedule_interval'] : 'hourly';
    $opts['trusted_domains']    = Snuhy_Utils::sanitize_multiline_domains($opts['trusted_domains'] ?? '');
    return $opts;
  }
}
