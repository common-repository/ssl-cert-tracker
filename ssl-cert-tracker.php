<?php
/*
Plugin Name: SSL Cert Tracker
Plugin URI: http://charlesdamonwarren.com/projects/ssl-cert-tracker/
Description: Tracks the experation dates for SSL Certificates
Version: 1.0.2
Author: Damon Warren
Author URI: http://charlesdamonwarren.com/about/
License: GPL2

    Copyright 2010  Damon Warren  (email : damon1977@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

//error_reporting(E_ALL);
add_action("widgets_init", array('SSLCertTracker', 'register'));
add_action("admin_menu", array('SSLCertTracker', 'addOptionsPage'));
add_action("admin_init", array('SSLCertTracker', 'adminInit'));
register_activation_hook( __FILE__, array('SSLCertTracker', 'activate'));
register_deactivation_hook( __FILE__, array('SSLCertTracker', 'deactivate'));

class SSLCertTracker {
  function adminInit(){
    register_setting('SSLCertTracker_options', 'SSLCertTracker_options', array('SSLCertTracker', 'validateOptions'));
    add_settings_section('SSLCertTracker_hosts', 'Hosts', array('SSLCertTracker', 'displayHostsSection'), 'SSLCertTracker');
    add_settings_field('SSLCertTracker_newhost', 'Add a host', array('SSLCertTracker', 'displayHostField'), 'SSLCertTracker', 'SSLCertTracker_hosts');
  }

  function validateOptions($input) {
    $opts = get_option('SSLCertTracker');
    
    if (isset($input['host'])) {
      $host = $input['host'];
      if (!empty($host)) $opts['hosts'][$host] = self::getExpDate($host);
    }

    if (isset($opts['hosts'])) {
      foreach($opts['hosts'] as $host => $date) {
        if (isset($input[$host])) {
          if ($input[$host] == 'delete') {
            unset($opts['hosts'][$host]);
          } else {
            $opts['hosts'][$host] = self::getExpDate($host);
          }
        }
      }
    }

    asort($opts['hosts']);
    update_option('SSLCertTracker', $opts);

    return $input;
  }

  function addOptionsPage(){
    add_options_page('SSL Cert Tracker Options', 'SSL Cert Tracker', 'manage_options', 'SSLCertTracker', array('SSLCertTracker', 'displayOptions'));
  }

  function displayHostField(){ ?>
    <input class="regular-text code" type="text" id="SSLCertTracker_host" name="SSLCertTracker_options[host]" value="" />
    <?php
  }

  function displayHostsSection(){
    $opts = get_option('SSLCertTracker');
    if (isset($opts['hosts'])) {
      foreach($opts['hosts'] as $host => $date) {
      $d = ($date instanceof DateTime) ? $date->format('m/d/Y') : 'Unknown';
      ?>
      <input class="button-secondary" type="submit" name="SSLCertTracker_options[<?php echo $host?>]" value="delete" />
      <input class="button-secondary" type="submit" name="SSLCertTracker_options[<?php echo $host?>]" value="refresh" />
      <?php
      echo "$d $host<br/>";
      }
    }
  }

  function displayOptions(){ ?>
    <div class="wrap">
      <div id="icon-options-general" class="icon32"><br></div>
      <h2>SSL Cert Tracker Options</h2>
      <form action="options.php" method="post">
        <?php settings_fields('SSLCertTracker_options'); ?>
        <?php do_settings_sections('SSLCertTracker'); ?>
        <input class="button-primary" name="Submit" type="submit" value="Add Host" />
      </form>
    </div> <?php
  }

  function activate(){
    global $wpdb;

    $defaultOpts = array(
        'title' => 'SSL Certs',
        'version' => '1.0.1');

    if ( ! get_option('SSLCertTracker')){
      add_option('SSLCertTracker' , $defaultOpts);
    } else {
      update_option('SSLCertTracker' , $defaultOpts);
    }
  }

  function deactivate(){
    delete_option('SSLCertTracker');
  }

  function control(){
    $data = get_option('SSLCertTracker');
    ?>
    <p>
      <label>Title
        <input name="SSLCertTracker_title" type="text" value="<?php echo $data['title']; ?>" />
      </label>
    </p>
    <?php
     if (isset($_POST['SSLCertTracker_title'])){
      $data['title'] = attribute_escape($_POST['SSLCertTracker_title']);
      update_option('SSLCertTracker', $data);
    }
  }

  function widget($args){
    global $wpdb;
    $opts = get_option('SSLCertTracker');
    $table_name = $wpdb->prefix . "sslcerthosts";

    echo $args['before_widget'];
    echo $args['before_title'] . $opts['title'] . $args['after_title'];

    if (isset($opts['hosts'])) {
      echo '<ul>';
      foreach ($opts['hosts'] as $host => $date) {
        $d = ($date instanceof DateTime) ? $date->format('m/d/Y') : 'Unknown';
        echo '<li>' . $d . ' ' . $host . '</li>';
      }
      echo '</ul>';
    } else {
      echo 'No hosts have been defined';
    }
    echo $args['after_widget'];
  }

  function register(){
    register_sidebar_widget('SSL Cert Tracker', array('SSLCertTracker', 'widget'));
    register_widget_control('SSL Cert Tracker', array('SSLCertTracker', 'control'));
  }

  function getExpDate($host){
    try {
      $context = stream_context_create(array('ssl' => array('capture_peer_cert' => TRUE)));
      @$html = file_get_contents('https://'.$host, NULL, $context);
      $opts = stream_context_get_options($context);
      $ssl = openssl_x509_parse($opts['ssl']['peer_certificate']);
      if (!$ssl) return false;
      return DateTime::createFromFormat('ymd', substr($ssl['validTo'], 0, 6));
    } catch (Exception $e) { }
    return false;
  }
}

?>