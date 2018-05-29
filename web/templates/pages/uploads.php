<?php $this->layout('page_template', ['title' => 'Uploaded Binaries']) ?>

    <?php
      //Get Persistence Value
      $persistence = $database->querySingle('SELECT setting FROM settings WHERE name = "persistence"');
      $directory = md5($_SESSION["user"]);
    ?>

    <?php
      //Get value of current running programs and uploaded files
      exec("ps aux | grep '[r]2' | tr -s ' ' | cut -d ' ' -f 16", $currprogname);
      exec("ls /home/pi/debug/tmp/".$directory."/", $uploadedfiles);
    ?>

    <h1 class="centerheader">Uploaded Binaries</h1>

    <div style="text-align:center;">
      <h3><?php echo "Persistence is set to: ".$persistence; ?></h3>
      <div class="container center_div">
        <form action="/" method="post" enctype="multipart/form-data">
          <?php
            //The below shows all uploaded files and displays them with delete and debug buttons
            if (!empty($uploadedfiles)) {
              echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
              echo '<button type="submit" value="all" name="delete">Delete all binaries</button><br /><br />';
            }
            $fileList = glob('/home/pi/debug/tmp/'.$directory.'/*');
            foreach($fileList as $filename) {
              echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
              //Use the is_file function to make sure that it is not a directory.
              if(!empty($currprogname[0]) && is_file($filename) && basename($filename) == basename($currprogname[0])) {
                echo basename($filename).' <button style="background-color:green;" type="submit" value="Exit running session" name="submit_exit">Debugging.  Click to stop.</button><br /><br />';
              } elseif(!empty($currprogname[0])){
                echo basename($filename).' <span style="color:red;">Unable to Debug</span>&nbsp;<button type="submit" value="', basename($filename), '" name="fileToRemove">Delete</button<br /><br />';
              } elseif(is_file($filename)){
                echo basename($filename).' <button type="submit" value="', basename($filename), '" name="fileToDebug">Debug</button>&nbsp;<button type="submit" value="', basename($filename), '" name="fileToRemove">Delete</button><br /><br />';
              }
            }
          ?>
        </form>
      </div>
    </div>
