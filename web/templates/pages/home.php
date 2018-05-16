<?php $this->layout('page_template', ['title' => 'Home']) ?>
  <?php
    $persistence = $database->querySingle('SELECT setting FROM settings WHERE name = "persistence"');
    $gdbserver = $database->querySingle('SELECT setting from settings WHERE name= "gdbserver"');
  ?>

  <?php
    exec("ps aux | grep '[r]2' | tr -s ' ' | cut -d ' ' -f 2", $currpid);
    exec("ps aux | grep '[w]ebsrv' | grep -v '/bin/bash' | grep -v 'r2' | tr -s ' ' | cut -d ' ' -f 2", $currpid_armpwn);
    exec("ps aux | grep '[g]dbserver' | tr -s ' ' | cut -d ' ' -f 2", $currpid_gdbserver);
  ?>

  <div id="alert" style="text-align:center;"></div>
  <h1 style="text-align:center; margin-top:50px;"><a style="text-decoration:none;color:black;" href="/">Reverse Engineer a Binary!</a></h1>
    <p style="text-align:center;">
    <?php
      // Check for running
      if(isset($currpid[0])) {
        echo "Current PID: <strong>".$currpid[0]."</strong>";
        echo "<br /><a target='_blank' href='http://".$_SERVER['HTTP_HOST'].":9090/'>Click here to go to the debugger</a>";
      }
      if (!empty($currpid_gdbserver[0])) {
        echo "<h5 style='text-align:center;'>GDB Server running on port 10100.</h5>";
      }
    ?>
    </p>

    <div class="container">
      <div class="row">
        <div class="col">
          <h3>Upload a binary... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Binary" name="submit">
          </form>
        </div>

        <div class="col">
          <h3>Remove an uploaded binary (remove using relative path or hit all to remove all)... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
            Name of binary to delete:
            <input type="text" name="fileToRemove" id="fileToRemove">
            <input type="submit" value="Delete Binary" name="submit">
            <input type="submit" value="all" name="delete">
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <h3>Debug a binary (must provide absolute paths.  EX: /bin/ls)... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
            Select binary to debug:
            <input type="text" name="fileToDebug" id="fileToDebug">
            <input type="submit" value="Debug Binary" name="submit">
          </form>
        </div>

        <div class="col">
          <h3>Exit existing sessions... </h3>
          <form action="/" method="post" enctype="multipart/form-data">
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
              echo "<input type='submit' value='Begin the challenge' name='challenge_begin'>";
            } else {
              echo "<input type='submit' value='End the challenge' name='challenge_end'>";
              echo "<p style='color:green;'>ARMPWN Challenge is running on port 8000 (<a href='http://".$_SERVER['HTTP_HOST'].":8000' target='_blank'>goto challenge</a>) and pid ".$currpid_armpwn[0].".</p>";
            }
            if(!isset($currpid[0]) && isset($currpid_armpwn[0])) {
              echo "<input type='submit' value='Start debug on armpwn again' name='challenge_begin_debug'>";
            }
          ?>
        </form>
      </div>
    </div>

    <div style="text-align:center;">
      <h3 style="text-align:center;">Uploaded Binaries - click to debug - <?php echo "Persistence is set to: ".$persistence; ?> </h3>
      <div class="container center_div">
        <form action="/" method="post" enctype="multipart/form-data">
          <?php
            $fileList = glob('/home/pi/debug/tmp/*');
            foreach($fileList as $filename){
              //Use the is_file function to make sure that it is not a directory.
              if(is_file($filename)){
                echo '<input type="submit" value="', basename($filename), '" name="fileToDebug"><br /><br />';
              }
            }
          ?>
        </form>
      </div>
    </div>

<?php
//The below are the PHP Magic lines
  $target_dir = "/home/pi/debug/tmp/";
  if (!empty($_FILES["fileToUpload"])) {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $binFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  }
  $uploadOk = 1;

  // File Upload
  if(isset($_FILES["fileToUpload"])) {
    // Check if file already exists
    if (file_exists($target_file)) {
      $alert = "Sorry, file already exists.";
      $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
      $alert = "Sorry, your file is too large.";
      $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      $alert = "<br />Sorry, your file was not uploaded.";
      // if everything is ok, try to upload file
    } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $alert = "<br /><h4>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</h4>";
        system("chmod +x /home/pi/debug/tmp/".basename( $_FILES["fileToUpload"]["name"]));
        header("Refresh:0;");
      } else {
        $alert = "Sorry, there was an error uploading your file.";
      }
    }
  }

  // File Removal
  if(!empty($_POST["fileToRemove"])) {
    // Check if file already exists
    if (!file_exists("/home/pi/debug/tmp/".$_POST["fileToRemove"])) {
      $alert = "<br />Sorry, file ".$_POST["fileToRemove"]." doesn't exist to remove..";
      $uploadOk = 0;
    } else {
      $alert = "<br />File ".$_POST["fileToRemove"]." has been deleted!";
      system("rm /home/pi/debug/tmp/".$_POST["fileToRemove"]);
    }
  }

  // All File Removal
  if(!empty($_POST["delete"])) {
    if ($_POST["delete"] == "all") {
      $alert = "<br />All uploaded files have been deleted!";
      system("rm /home/pi/debug/tmp/*");
    }
  }

  // File Debug
  if(!empty($_POST["fileToDebug"])) {
    // Check if file is already debugging
    if (isset($currpid[0])) {
      $alert = "<br /><p style='color: red;'>Sorry, debugger already running under PID ".$currpid[0].".  Must exist session to continue.</p>";
    } elseif (file_exists("/home/pi/debug/tmp/".$_POST["fileToDebug"])) {
      $alert = "<br />File ".$_POST["fileToDebug"]." is being debugged!";
      if ($gdbserver == "True") {
        system("/usr/bin/gdbserver 0.0.0.0:10100 /home/pi/debug/tmp/".$_POST["fileToDebug"]." > /dev/null &");
      }
      system("/usr/local/bin/re /home/pi/debug/tmp/".$_POST["fileToDebug"]." > /dev/null &");
      header("Refresh:0;");
    } elseif (!file_exists($_POST["fileToDebug"])) {
      $alert = "<br />Sorry, file ".$_POST["fileToDebug"]." doesn't exist to debug..";
      $uploadOk = 0;
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
      $alert = "<br /><p style='color:red;'>Exiting debug session.</p>";
      system("sudo /usr/local/bin/dbkill ".$currpid[0]);
      if ($gdbserver == "True") {
        system("sudo /usr/local/bin/dbkill ".$currpid_gdbserver[0]);
      }
      header("Refresh:0");
    } else {
      $alert = "<br /><p style='color:red;'>Not currently running!</p>";
    }
  }

  // Start ARMPWN
  if(isset($_POST["challenge_begin"])) {
    if(isset($currpid[0])) {
      $alert = "<br /><p style='color:red;'>Debug session currently running, cannot begin....</p>";
    } else {
      $alert = "<br /><p style='color:green;'>Challenge has begun!</p>";
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
      $alert = "<br /><p style='color:red;'>Exiting debug session for ARMPWN challenge.</p>";
      system("sudo /usr/local/bin/dbkill ".$currpid[0]);
    }
    if(isset($currpid_armpwn[0])) {
      $alert = "<br /><p style='color:red;'>Exiting ARMPWN challenge.</p>";
      foreach ($currpid_armpwn as &$value) {
        system("sudo /usr/local/bin/dbkill ".$value);
      }
      header("Refresh:0;");
    } else {
      $alert = "<br /><p style='color:red;'>Challenge not currently running!</p>";
    }
  }

  // Debug ARMPWN
  if(isset($_POST["challenge_begin_debug"])) {
    if(isset($currpid[0])) {
      $alert = "<br /><p style='color:red;'>Debug session currently running, cannot begin....</p>";
    } else {
      $alert = "<br /><p style='color:green;'>Challenge debug has been initiated.</p>";
      if ($gdbserver == "True") {
        system("/usr/bin/gdbserver 0.0.0.0:10100 /var/www/html/websrv > /dev/null &");
      }
      system("/usr/local/bin/re /var/www/html/websrv > /dev/null &");
      header("Refresh:0;");
    }
  }

  // Error Alerts
  if(isset($alert)) {
    echo '<script>document.getElementById("alert").innerHTML += "'.$alert.'"</script>';
  }
