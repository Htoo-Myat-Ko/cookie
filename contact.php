<?php
  require_once("class/tools.php");
  use hmk\tools\tools as hmktools;
  $base_url = "/";
  $hmktools = new hmktools();
  $hmktools-> set_cookie_if_new("CONTACT HMK", $base_url);
  echo "<pre>";
  $json = json_decode($_COOKIE["links"], true);
  var_dump($json);
