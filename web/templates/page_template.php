<?php
//Page template stuff.  Include head.php first, then navbar, then the content from pages and finally our footer.
include './parts/head.php';
$this->insert('/../parts/navbar'); ?>
<?=$this->section('content'); ?>
<?php
include './parts/footer.php';

//Check HTTPSForce value and do an HTTPS redirect if True
$database = new SQLite3('armpi.sqlite');
$httpsset = $database->querySingle('SELECT setting FROM settings WHERE name = "httpsforce"');

if (trim($httpsset) == "True" && empty($_SERVER['HTTPS'])) {
  $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: ' . $redirect);
  exit();
}
