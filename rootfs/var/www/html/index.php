<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=yes"/>
    <title>Fritz!WLAN</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="default"/>
    <meta name="theme-color" content="#565454">
    <meta name="google" content="notranslate"/>
    <link rel="stylesheet" href="fb.css">
    <link rel="icon" type="image/png" href="/img/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/img/favicon.svg" />
    <link rel="shortcut icon" href="/img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Fritz!WLAN" />
    <link rel="manifest" href="/img/site.webmanifest" />
  </head>
  <body>

<?php
require_once '_functions.php';

if (isset($_GET['action'])) {
  $f = new fritzbox();
  if($_GET['action'] == "enable") {
    $output = $f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_enable");
  }
  if($_GET['action'] == "disable") {
    $output = $f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_disable");
  }
  if($_GET['action'] == "status") {
    $output = $f->send("$fb_host", "$fb_port", "$fb_prot", "$fb_user", "$fb_pass", "$fb_wlan", "guest_status");
  }

  $refreshtime = 3;
  echo '
<!DOCTYPE HTML>
  <html>
    <head>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="fb.css">
      <style>
        html, body {
          font-family: Arial, Helvetica, sans-serif;
          font-size: 14px;
          color: #EFEFEF;
        }
        tr, td {
          font-family: Arial, Helvetica, sans-serif;
          font-size: 14px;
          color: #EFEFEF;
        }
      </style>
      <meta http-equiv="refresh" content="'.$refreshtime.';url='.$_SERVER['PHP_SELF'].'">
    </head>
    <body>';

  if (isset($output) AND !empty($output) AND (count($output) > 1)) {
    print_r("<table width='360px'>\n".$output."</table>\n");
  }

  // HTML - Footer
  echo "</body>\n</html>";

}
elseif (!isset($_GET['action']) OR !isset($output)) {
?>

    <div class="centered">
      <center>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <h1>Guest Network</h1>
          <input class="button button1" type="submit" name="enable" value="WiFi ON">
          <input class="button button1" type="submit" name="disable" value="WiFi OFF">
          <input class="button button1" type="submit" name="status" value="STATUS">
          <input type="hidden" name="action">
        </form>
      </center>
    </div>

<?php
} else { print_r("ERROR!"); }
?>

</body>
</html>
