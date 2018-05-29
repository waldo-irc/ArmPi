<?php $this->layout('page_template', ['title' => 'Not Found!']) ?>
<?php
$url = $_SERVER['REQUEST_URI'];
$url = trim($url, '/');
$array = explode('/',$url);
$the_category = end($array);

echo "<h1 class='centerheader'>/$url Does Not Exist!</h1>";
?>
