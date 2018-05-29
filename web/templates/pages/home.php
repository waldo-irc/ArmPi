<?php $this->layout('page_template', ['title' => 'Home']) ?>
  <?php
    $persistence = $database->querySingle('SELECT setting FROM settings WHERE name = "persistence"');
    $gdbserver = $database->querySingle('SELECT setting from settings WHERE name= "gdbserver"');
  ?>

  <?php
    exec("ps aux | grep '[r]2' | tr -s ' ' | cut -d ' ' -f 2", $currpid);
    exec("ps aux | grep '[r]2' | tr -s ' ' | cut -d ' ' -f 16", $currprogname);
    exec("ps aux | grep '[w]ebsrv' | grep -v '/bin/bash' | grep -v 'r2' | tr -s ' ' | cut -d ' ' -f 2", $currpid_armpwn);
    exec("ps aux | grep '[g]dbserver' | tr -s ' ' | cut -d ' ' -f 2", $currpid_gdbserver);
  ?>

  <div id="alert" style="text-align:center;"></div>
  <div id="alert2" style="text-align:center;"></div>
  <h1 class="centerheader">Reverse Engineer a Binary!</h1>
    <p style="text-align:center;">
    <?php
      // Check for running programs and output them in the dashboard
      if(isset($currpid[0])) {
        echo "Current PID: <strong>".$currpid[0]."</strong>";
        echo "<br /><a target='_blank' href='http://".$_SERVER['HTTP_HOST'].":9090/'>Click here to go to the debugger</a>";
      }
      if(isset($currprogname[0])) {
        echo "<br /><strong>Currently running</strong>: ".basename($currprogname[0]);
      }
      if (!empty($currpid_gdbserver[0])) {
        echo "<h5 style='text-align:center;'>GDB Server running on port 10100.</h5>";
      }
    ?>
    </p>

    <div style="text-align:center;">
      <h3>Debug a binary (must provide absolute paths.  EX: /bin/ls)... </h3>
      <div class="container center_div">
        <form action="/" method="post" enctype="multipart/form-data">
          Select binary to debug:
          <input type="text" name="fileToDebug" id="fileToDebug">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
          <input type="submit" value="Debug Binary" name="submit">
        </form>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col center_div" style="text-align:center;">
          <h3>Upload a binary... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
            <input type="submit" value="Upload Binary" name="submit">
          </form>
        </div>

        <div class="col center_div" style="text-align:center;">
          <h3>Exit existing sessions... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
            <input type="submit" value="Exit running session" name="submit_exit">
          </form>
        </div>
      </div>
    </div>

    <div style="text-align:center;">
      <h3>Do the ARMPWN Challenge!</h3>
      <div class="container center_div">
        <form action="/" method="post" enctype="multipart/form-data">
          <?php
            if(!isset($currpid_armpwn[0])) {
              echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
              echo "<input type='submit' value='Begin the challenge' name='challenge_begin'>";
            } else {
              echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
              echo "<input type='submit' value='End the challenge' name='challenge_end'>";
              echo "<p class='greenalert'>ARMPWN Challenge is running on port 8000 (<a href='http://".$_SERVER['HTTP_HOST'].":8000' target='_blank'>goto challenge</a>) and pid ".$currpid_armpwn[0].".</p>";
            }
            if(!isset($currpid[0]) && isset($currpid_armpwn[0])) {
              echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
              echo "<input type='submit' value='Start debug on armpwn again' name='challenge_begin_debug'>";
            }
          ?>
        </form>
      </div>
    </div>

<?php
  // Create a new CSRF token.
  if (! isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $directory = md5($_SESSION["user"]);
    // The below are the PHP Magic lines
    $target_dir = "/home/pi/debug/tmp/".$directory."/";
    if (!empty($_FILES["fileToUpload"])) {
      $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
      $binFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    }
    $uploadOk = 1;

    // File Upload
    if (!file_exists($target_dir)) {
      mkdir("/home/pi/debug/tmp/".$directory, 0755, true);
    }
    if(isset($_FILES["fileToUpload"])) {
      // Check if file already exists
      if (file_exists($target_file)) {
        $alert2 = "<br />Sorry, file already exists.";
        $uploadOk = 0;
      }
      // Check file size
      if ($_FILES["fileToUpload"]["size"] > 500000) {
        $alert2 = "<br />Sorry, your file is too large.";
        $uploadOk = 0;
      }
      // Check MimeType
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $_FILES["fileToUpload"]["tmp_name"]);
      if (strpos($mime_type, 'application') === false) {
        $alert2 = "<br />Bad File.";
        $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
        $alert = "<br />Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
      } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          $alert = "<br /><h4>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</h4>";
          chmod("/home/pi/debug/tmp/".$directory."/".basename( $_FILES["fileToUpload"]["name"]),0755);
          header("Refresh:0; url=/uploads");
        } else {
          $alert = "<br />Sorry, there was an error uploading your file.";
        }
      }
    }

    // File Removal
    if(!empty($_POST["fileToRemove"])) {
      $delfile = $_POST["fileToRemove"];
      // Check if file already exists and make sure it isnt being debugged.
      if (!empty($currprogname[0]) && basename($currprogname[0]) == $delfile) {
        $alert = "<br />Cannot delete a program being debugged.";
      } elseif (!file_exists("/home/pi/debug/tmp/".$directory."/".$delfile)) {
        $alert = "<br />Sorry, file ".$delfile." doesn't exist to remove..";
        $uploadOk = 0;
      } else {
        $alert = "<br />File ".$delfile." has been deleted!";
        unlink("/home/pi/debug/tmp/".$directory."/".$delfile);
      }
    }

    // All File Removal
    if(!empty($_POST["delete"])) {
      if (!empty($currprogname[0])) {
        $alert = "<br />Stop all running debug processes before deleting all files.";
      } elseif ($_POST["delete"] == "all") {
        $alert = "<br />All uploaded files have been deleted!";
        array_map('unlink', glob("/home/pi/debug/tmp/".$directory."/*"));
        header("Refresh:0;");
      }
    }

    // File Debug
    if(!empty($_POST["fileToDebug"])) {
      // Check if file is already debugging
      if (isset($currpid[0])) {
        $alert = "<br /><p style='color: red;'>Sorry, debugger already running under PID ".$currpid[0].".  Must exist session to continue.</p>";
      } elseif (file_exists("/home/pi/debug/tmp/".md5($_SESSION["user"])."/".$_POST["fileToDebug"])) {
        $alert = "<br />File ".$_POST["fileToDebug"]." is being debugged!";
        if ($gdbserver == "True") {
          system("/usr/bin/gdbserver 0.0.0.0:10100 /home/pi/debug/tmp/".$_POST["fileToDebug"]." > /dev/null &");
        }
        system("/usr/local/bin/re /home/pi/debug/tmp/".md5($_SESSION["user"])."/".$_POST["fileToDebug"]." > /dev/null &");
        header("Refresh:0;");
      } elseif (!file_exists($_POST["fileToDebug"])) {
        $alert = "<br />Sorry, file ".$_POST["fileToDebug"]." doesn't exist to debug..";
      } else {
        $alert = "<br />File ".$_POST["fileToDebug"]." is being debugged!";
        if ($gdbserver == "True") {
          system("/usr/bin/gdbserver 0.0.0.0:10100 ".$_POST["fileToDebug"]." > /dev/null &");
        }
        system("/usr/local/bin/re ".$_POST["fileToDebug"]." > /dev/null &");
        header("Refresh:0;");
      }
    }


    // File Debug Exit
    if(isset($_POST["submit_exit"])) {
      if(isset($currpid[0])) {
        if(isset($_SESSION["user"]) && $_SESSION["user"] != "guest") {
          $alert = "<br /><p class='redalert'>Exiting debug session.</p>";
          system("kill -9 ".$currpid[0]);
          if ($gdbserver == "True") {
            system("kill -9 ".$currpid_gdbserver[0]);
          }
          header("Refresh:0");
        } else {
          $alert = "<br /><p class='redalert'>Guest cannot end sessions.</p>";
        }
      } else {
        $alert = "<br /><p class='redalert'>Not currently running!</p>";
      }
    }

    // Start ARMPWN
    if(isset($_POST["challenge_begin"])) {
      if(isset($currpid[0])) {
        $alert = "<br /><p class='redalert'>Debug session currently running, cannot begin....</p>";
      } else {
        $alert = "<br /><p class='greenalert'>Challenge has begun!</p>";
        if ($gdbserver == "True") {
          system("/usr/bin/gdbserver 0.0.0.0:10100 /var/www/html/websrv > /dev/null &");
        }
        system("/usr/local/bin/re /var/www/html/websrv > /dev/null &");
        system("/var/www/html/websrv > /dev/null &");
        header("Refresh:0;");
      }
    }

    // End ARMPWN
    if(isset($_POST["challenge_end"])) {
      if(isset($currpid[0])) {
        $alert = "<br /><p class='redalert'>Exiting debug session for ARMPWN challenge.</p>";
        system("kill -9 ".$currpid[0]);
      }
      if(isset($currpid_armpwn[0])) {
        $alert = "<br /><p class='redalert'>Exiting ARMPWN challenge.</p>";
        foreach ($currpid_armpwn as &$value) {
          system("kill -9 ".$value);
        }
        header("Refresh:0;");
      } else {
        $alert = "<br /><p class='redalert'>Challenge not currently running!</p>";
      }
    }

    // Debug ARMPWN
    if(isset($_POST["challenge_begin_debug"])) {
      if(isset($currpid[0])) {
        $alert = "<br /><p class='redalert'>Debug session currently running, cannot begin....</p>";
      } else {
        $alert = "<br /><p class='greenalert'>Challenge debug has been initiated.</p>";
        if ($gdbserver == "True") {
          system("/usr/bin/gdbserver 0.0.0.0:10100 /var/www/html/websrv > /dev/null &");
        }
        system("/usr/local/bin/re /var/www/html/websrv > /dev/null &");
        header("Refresh:0;");
      }
    }
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
  } elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alert = "<br /><p class='redalert'>CSRF Detected!</p>";
  }

  // Error Alerts
  if(isset($alert)) {
    echo '<script>document.getElementById("alert").innerHTML += "'.$alert.'"</script>';
  }
  if(isset($alert2)) {
    echo '<script>document.getElementById("alert2").innerHTML += "'.$alert2.'"</script>';
  }
