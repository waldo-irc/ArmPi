<?php $this->layout('page_template', ['title' => 'Settings']) ?>

    <?php
      $persistence = $database->querySingle('SELECT setting FROM settings WHERE name = "persistence"');
      $htpasswd = $database->querySingle('SELECT setting from settings WHERE name= "htpasswd"');
      $httpsforce = $database->querySingle('SELECT setting from settings WHERE name= "https"');
      $gdbserver = $database->querySingle('SELECT setting from settings WHERE name= "gdbserver"');

      if (!empty($_POST['defaults'])) {
        $persistence = $database->query('UPDATE settings SET setting = "False" WHERE name = "persistence"');
        $httpsforce = $database->query('UPDATE settings SET setting = "True" WHERE name = "https"');
        $htpasswd = $database->query('UPDATE settings SET setting = "toor" WHERE name = "htpasswd"');
        $gdbserver = $database->query('UPDATE settings SET setting = "True" WHERE name = "gdbserver"');
        system("cp /var/www/html/.htpasswd_bkup /var/www/html/.htpasswd");
        system("sudo /bin/cp /etc/hostapd/hostapd.conf.bk.armpi /etc/hostapd/hostapd.conf");
        echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Defaults restored.  Restart Required!</h1>";
        echo "<h5 style='color:green;text-align:center;margin-top:20px;'>Default Wireless: ArmPI armpi</h5>";
        echo "<h5 style='color:green;text-align:center;margin-top:20px;'>Default login: root toor</h5>";
        //header("Refresh:0");
      }

      $wl = ['True','False', True, False];
      if (isset($_POST['persistence']) && in_array($_POST['persistence'],$wl)) {
        $persistence = $database->query('UPDATE settings SET setting = "'.$_POST['persistence'].'" WHERE name = "persistence"');
      }

      if (!empty($_POST['httpsforce']) && in_array($_POST['httpsforce'],$wl)) {
        $httpsforce = $database->query('UPDATE settings SET setting = "'.$_POST['httpsforce'].'" WHERE name = "https"');
      }

      if (!empty($_POST['gdbserver']) && in_array($_POST['gdbserver'],$wl)) {
        $gdbserver = $database->query('UPDATE settings SET setting = "'.$_POST['gdbserver'].'" WHERE name = "gdbserver"');
      }

      if (!empty($_POST['ssid'])) {
        system('sudo /bin/sed -ie "s/^ssid=.*/ssid='.$_POST['ssid'].'/" /etc/hostapd/hostapd.conf');
        echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Restart Required!</h1>";
      }

      if (!empty($_POST['wifipass'])) {
        $currentpassword = shell_exec("cat /etc/hostapd/hostapd.conf | grep -w wpa_passphrase | cut -d '=' -f2");
        if (trim($currentpassword) != trim($_POST['wifipasscurrent'])){
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Must enter current password correctly!</h1>";
        } elseif (empty($_POST['wifipassconf'])) {
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Must re-confirm password.</h1>";
        } elseif ($_POST['wifipassconf'] == $_POST['wifipass']) {
          system('sudo /bin/sed -ie "s/^wpa_passphrase=.*/wpa_passphrase='.$_POST['wifipass'].'/" /etc/hostapd/hostapd.conf');
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Password Changed Succesfully.  Restart Required!</h1>";
        } else {
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Passwords don't match!!</h1>";
        }
      }

      if (!empty($_POST['httppass'])) {
        if (trim($htpasswd) != trim($_POST['httppasscurrent'])) {
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Must enter current password correctly!</h1>";
        } elseif (empty($_POST['httppassconf'])) {
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Must re-confirm password.</h1>";
        } elseif ($_POST['httppassconf'] == $_POST['httppass']) {
          $newpass = shell_exec("htpasswd -nmb root ".$_POST['httppass']." | tr -d '\n'");
          $htpasswd = $database->query('UPDATE settings SET setting = "'.$_POST['httppass'].'" WHERE name = "htpasswd"');
          $htpasswdfile = '/var/www/html/.htpasswd';
          $handle = fopen($htpasswdfile, 'w');
          fwrite($handle, $newpass);
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Password Changed Succesfully.  Restart Required!</h1>";
        } else {
          echo "<h1 style='color:red;text-align:center;margin-top:20px;'>Passwords don't match!!</h1>";
        }
      }

      function HumanSize($Bytes) {
        $Type=array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
        $Index=0;
        while($Bytes>=1024) {
          $Bytes/=1024;
          $Index++;
        }
        return("".$Bytes." ".$Type[$Index]."bytes");
      }
    ?>

    <h1 style="text-align:center; margin-top:50px;">Settings</h1>
    <p style="text-align:center;"><strong>(This page won't function properly without javascript.)</strong></p>
    <p style="text-align:center;">Current disk usage: <span style="color:blue;"><?php $df = HumanSize(disk_free_space("/")); echo $df; ?></span></p>

    <br />

    <div class="container">
      <div class="row">
        <div class="col">
          <form style="text-align:center; margin-top: 15px;" action="/settings" method="post">
            <h4>Wifi Settings</h4>
            <p>Change SSID: <input type="text" name="ssid"></p><br>
            <p>Current Wireless Password: <input type="password" name="wifipasscurrent"></p><br>
            <p>Change Wireless Password: <input type="password" name="wifipass"></p><br>
            <p>Confirm Wireless Password: <input type="password" name="wifipassconf"></p><br>
            <h4>Login Settings</h4>
            <p>Current HTTP Password: <input type="password" name="httppasscurrent"></p><br>
            <p>Change HTTP Password: <input type="password" name="httppass"></p><br>
            <p>Confirm HTTP Password: <input type="password" name="httppassconf"></p><br>
            <input type="submit">
          </form>
        </div>

        <div class="col">
          <form style="text-align:center; margin-top: 15px;" action="/settings" method="post">
            <p><strong>File Persistence: </strong><br />Due to limited storage this is disabled by default.<label class="switch"><input onclick="changepersistence()" type="checkbox" id="persistence"><span class="slider round"></span></label></p><br/>
            <p><strong>Force HTTPS: </strong><br />Enabled by default for security.<label class="switch"><input onclick="changehttpsforce()" type="checkbox" id="httpsforce"><span class="slider round"></span></label></p><br/>
            <p><strong>GDB Server: </strong><br />Debugging a binary auto starts GDB Server over port 10100.<label class="switch"><input onclick="changegdbserver()" type="checkbox" id="gdbserver"><span class="slider round"></span></label></p><br/>
            <p><input type='submit' value='Default Settings' name='defaults'></p>
          </form>
        </div>
      </div>
    </div>

    <script src="/files/js/jquery.min.js"></script>
    <script type="text/javascript">

      function checkpersistence() {
        // Get the checkbox
        var PersistenceCheckBox = document.getElementById("persistence");

        <?php
          if ($persistence == "True") {
            echo "PersistenceCheckBox.checked = true;";
          }
        ?>
      }

      function checkhttpsforce() {
        // Get the checkbox
        var HTTPSCheckBox = document.getElementById("httpsforce");

        <?php
          if ($httpsforce == "True") {
            echo "HTTPSCheckBox.checked = true;";
          }
        ?>
      }

      function checkgdbserver() {
        // Get the checkbox
        var gdbserverCheckBox = document.getElementById("gdbserver");

        <?php
          if ($gdbserver == "True") {
            echo "gdbserverCheckBox.checked = true;";
          }
        ?>
      }

      function changepersistence() {
        // Get the checkbox
        var PersistenceCheckBox = document.getElementById("persistence");

        // If the checkbox is checked do X
        if (PersistenceCheckBox.checked == true){
          $.ajax
            ({
              url: '/settings',
              data: {"persistence": "True"},
              type: 'post',
            });
          } else {
          $.ajax
            ({
              url: '/settings',
              data: {"persistence": "False"},
              type: 'post',
            });
          }
      }

      function changehttpsforce() {
        // Get the checkbox
        var HTTPSCheckBox = document.getElementById("httpsforce");

        // If the checkbox is checked do X
        if (HTTPSCheckBox.checked == true){
          $.ajax
            ({
              url: '/settings',
              data: {"httpsforce": "True"},
              type: 'post',
            });
          } else {
          $.ajax
            ({
              url: '/settings',
              data: {"httpsforce": "False"},
              type: 'post',
            });
          }
      }

      function changegdbserver() {
        // Get the checkbox
        var gdbserverCheckBox = document.getElementById("gdbserver");

        // If the checkbox is checked do X
        if (gdbserverCheckBox.checked == true){
          $.ajax
            ({
              url: '/settings',
              data: {"gdbserver": "True"},
              type: 'post',
            });
          } else {
          $.ajax
            ({
              url: '/settings',
              data: {"gdbserver": "False"},
              type: 'post',
            });
          }
      }

      window.onload = checkpersistence()
      window.onload = checkhttpsforce()
      window.onload = checkgdbserver()

    </script>
