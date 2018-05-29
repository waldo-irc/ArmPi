<?php $this->layout('page_template', ['title' => 'Reboot']) ?>

<?php
  //Set CSRF Token
  if (! isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
  }
  $csrftoken = $_SESSION['csrf_token'];

  if (!empty($_SESSION['user']) && $_SESSION['user'] == "root") {
    // Reboot
    if(isset($_GET["reboot"]) && $_GET["reboot"] == "rnow" && !empty($_POST["confirm"])) {
      if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        system("sudo /sbin/reboot");
      } else {
        echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>CSRF Detected!</h1>";
      }
    } elseif (isset($_GET["reboot"]) && $_GET["reboot"] == "rnow") {
      echo "<h1 class='centerheader'>Are you sure you wanna reboot?</h1>";
      echo "<div style='text-align:center;'>";
      echo "<a onclick=\"postreboot('".$csrftoken."')\" href='#'><button type='button' class='btn btn-success'>Continue</button></a>";
      echo "<a href='/'><button style='margin-left:10px;' type='button' class='btn btn-danger'>Cancel</button></a>";
      echo "</div>";
    }

    // Shutdown
    if(isset($_GET["reboot"]) && $_GET["reboot"] == "shutdown" && !empty($_POST["confirm"])) {
      if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        system("sudo /sbin/shutdown -h now");
      } else {
        echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>CSRF Detected!</h1>";
      }
    } elseif (isset($_GET["reboot"]) && $_GET["reboot"] == "shutdown") {
      echo "<h1 class='centerheader'>Are you sure you wanna shutdown?</h1>";
      echo "<div style='text-align:center;'>";
      echo "<a onclick=\"postshutdown('".$csrftoken."')\" href='#'><button type='button' class='btn btn-success'>Continue</button></a>";
      echo "<a href='/'><button style='margin-left:10px;' type='button' class='btn btn-danger'>Cancel</button></a>";
      echo "</div>";
    }
  }
