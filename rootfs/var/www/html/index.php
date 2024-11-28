<?php
require_once '_functions.php';

if (isset($_GET['action'])) {
  $f = new fritzbox();
  if($_GET['action'] == "enable") {
    print_r($f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_enable"));
  }
  if($_GET['action'] == "disable") {
    print_r($f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_disable"));
  }
  if($_GET['action'] == "status") {
    print_r($f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_status"));
  }
}
?>
