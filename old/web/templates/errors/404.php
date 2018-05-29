<?php $this->layout('page_template', ['title' => 'Not Found!']) ?>
<h1 style="font-family:arial;">
<!--span style="font-size:2em;">LOST?</span-->
<br />
<!--img src="/../../img/lost.jpg" alt="lost" /-->
<br />
<?php
$url = $_SERVER['REQUEST_URI'];
$url = trim($url, '/');
$array = explode('/',$url);
$the_category = end($array);

echo "<h1>/$url Does Not Exist!</h1>";
?>
</h1>
