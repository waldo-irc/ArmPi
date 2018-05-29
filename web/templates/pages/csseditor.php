<?php $this->layout('page_template', ['title' => 'Help']) ?>

    <?php
    //Grab value of jseditor and disable if False
    $jsedit = $database->querySingle('SELECT setting from settings WHERE name= "jsedit"');
    if ( $_GET["editor"] == "js" && $jsedit != "True") {
      echo "<h1 class='centerheader'>JS Editor disabled in settings.</h1>";
    }

    // Create a new CSRF token.
    if (! isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
    }
    ?>

    <!-- HTML form -->
    <form action="" method="post" id="editorform">

    <?php
    //Only run if user is root (security)
    if (!empty($_SESSION['user']) && $_SESSION['user'] == "root") {
      //Based on the GET value route either css or JS editor (if enabled)
      if ( $_GET["editor"] == "css") {
        echo '<h1 class="centerheader">CSS Editor</h1>';
       // configuration
        $url = 'http://armpi.local/csseditor';
        $file = '/var/www/html/files/css/custom.css';

        // check if form has been submitted
        if (isset($_POST['text']))
        {
          if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            // save the text contents
            file_put_contents($file, $_POST['text']);

            // redirect to form again
            header(sprintf('Location: %s?editor=css', $url));
            printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
            exit();
          } else {
            echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>CSRF Detected!</h1>";
          }
        }

        // read the textfile
        $text = file_get_contents($file);
      }

      //If Get is JS route js editor (if enabled)
      if ( $_GET["editor"] == "js" && $jsedit == "True") {
        echo '<h1 class="centerheader">JS Editor</h1>';
        // configuration
        $url = 'http://armpi.local/csseditor';
        $file = '/var/www/html/files/js/user.js';

        // check if form has been submitted
        if (isset($_POST['text']))
        {
          if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            // save the text contents
            file_put_contents($file, $_POST['text']);

            // redirect to form again
            header(sprintf('Location: %s?editor=js', $url));
            printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
            exit();
          } else {
            echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>CSRF Detected!</h1>";
          }
        }

        // read the textfile
        $text = file_get_contents($file);
      } elseif ( $_GET["editor"] == "js" ) {
        echo '<script>document.getElementById("editorform").style.display = "none";</script>';
      }
    }
    ?>

    <textarea style="width:100%;height:500px;" name="text"><?php echo htmlspecialchars($text) ?></textarea>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
    <input type="submit" />
    <input type="reset" />
    </form>

    <?php if(!empty($unset_csrf) && $unset_csrf == 1){unset($_SESSION['csrf_token']);} ?>
