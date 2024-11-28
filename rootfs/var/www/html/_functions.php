<?php
if (getenv("IS_PRODUCTION")) {
  // Disable error reports which are not an immediate issue
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

$fb_host = getenv("FRITZBOX_HOST");
$fb_port = getenv("FRITZBOX_PORT");
$fb_prot = getenv("FRITZBOX_PROT");
$fb_user = getenv("FRITZBOX_USER");
$fb_pass = getenv("FRITZBOX_PASS");
$fb_wlan = getenv("FRITZBOX_WLAN");

class fritzbox {

  protected $host = null;
  protected $port = null;
  protected $prot = null;
  protected $user = null;
  protected $pass = null;
  protected $wlan = null;
  protected $data = null;

  function send($fb_host = null, $fb_port = null, $fb_prot = null, $fb_user = null, $fb_pass = null, $fb_wlan = null, $fb_data = null) {
    if ($fb_host === null || $fb_user === null || $fb_pass === null || $fb_data === null)
      return false;
    if ($fb_port === null)
      $this->port = 49000;
    if ($fb_prot === null)
      $this->prot = "http";
    if ($fb_wlan === null)
      $this->wlan = 3;
    $this->host = $fb_host;
    $this->port = $fb_port;
    $this->prot = $fb_prot;
    $this->user = $fb_user;
    $this->pass = $fb_pass;
    $this->wlan = $fb_wlan;
    $this->data = $fb_data;
    $uri = "urn:dslforum-org:service:WLANConfiguration:".$this->wlan;
    $location = "upnp/control/wlanconfig".$this->wlan;
    $fullurl = $this->prot."://".$this->host.":".$this->port."/".$location;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullurl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($this->data == "guest_enable") {
      $tog = 1;
      $post_data = "<?xml version='1.0' encoding='utf-8'?><s:Envelope s:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/' xmlns:s='http://schemas.xmlsoap.org/soap/envelope/'><s:Body><u:SetEnable xmlns:u='".$uri."'><NewEnable>".$tog."</NewEnable></u:SetEnable></s:Body></s:Envelope>";
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset="utf-8"',
        'SoapAction:'.$uri.'#SetEnable'
      ));
    } elseif ($this->data == "guest_disable") {
      $tog = 0;
      $post_data = "<?xml version='1.0' encoding='utf-8'?><s:Envelope s:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/' xmlns:s='http://schemas.xmlsoap.org/soap/envelope/'><s:Body><u:SetEnable xmlns:u='".$uri."'><NewEnable>".$tog."</NewEnable></u:SetEnable></s:Body></s:Envelope>";
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset="utf-8"',
        'SoapAction:'.$uri.'#SetEnable'
      ));
    } elseif ($this->data == "guest_status") {
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset="utf-8"',
        'SoapAction:'.$uri.'#GetInfo'
      ));
      $post_data = "<?xml version='1.0' encoding='utf-8'?><s:Envelope s:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/' xmlns:s='http://schemas.xmlsoap.org/soap/envelope/'><s:Body><u:GetInfo xmlns:u='".$uri."'></u:GetInfo></s:Body></s:Envelope>";
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
    curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->pass);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // only show the relevant part (0=off|1=on) of 'guest_status'
    if (str_contains($result, 'GetInfoResponse')) {
      preg_match("/(?<=<NewEnable>)[0-1]?(?=<\/NewEnable>)/", $result, $result);
      $result = $result[0];
    } elseif (str_contains($result, 'SetEnableResponse')) {
      $result = 'successfully send.';
    } else {
      $result = 'error sending.';
    }

    if ($result === null) {
      return $status_code;
    } else {
      return $result;
    }
  }
}
?>
