<?php
require_once '_functions.php';

$fb_host = getenv("FRITZBOX_HOST");
$fb_port = getenv("FRITZBOX_PORT");
$fb_prot = getenv("FRITZBOX_PROT");
$fb_user = getenv("FRITZBOX_USER");
$fb_pass = getenv("FRITZBOX_PASS");
$fb_wlan = getenv("FRITZBOX_WLAN");

// function send($fb_host = null, $fb_port = null, $fb_prot = null, $fb_user = null, $fb_pass = null, $fb_wlan = null, $fb_data = null) {
$f = new fritzbox("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_status");
print_r($fritz->send());
?>
