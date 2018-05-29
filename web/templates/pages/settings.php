<?php $this->layout('page_template', ['title' => 'Settings']) ?>

    <?php
      //No access if not logged in as root.
      if ($_SESSION["user"] == "guest") {
        header("Location: home");
      } elseif ($_SESSION["user"] != "root" && $_GET["update"] != "user") {
        header("Location: settings?update=user");
      }

      //Other Variables.
      exec("ps aux | grep '[g]dbserver' | grep 'multi' | tr -s ' ' | cut -d ' ' -f 2", $gdbservermulti);
      exec("ps aux | grep '[w]eb-terminal' | grep -v 'r2' | tr -s ' ' | cut -d ' ' -f 2", $webterminal);
      $username = $_SESSION["user"];
      $password = $database->querySingle('SELECT password from auth WHERE username="'.$username.'"');

      //Fetch each row in settings and create it as a variable and give it its value as the var value.
      $results = $database->query('SELECT * from settings');
      $data = array();
      while ($res = $results->fetchArray(1)) {
        array_push($data, $res);
      }
      foreach ($data as $key => $value) {
        $name = $value["name"];
        $$name = $value["setting"];
        if ($$name == "True") {
          echo "<span id='".$name."on' class='settingval'></span>";
        }
      }
      if (!empty($gdbservermulti)) {
        echo "<span id='gdbservermultion' class='settingval'></span>";
      }

      // Create a new CSRF token.
      if (! isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
      }

      //Set all defaults - from here on out ONLY root can access settings.
      if ($username == "root" && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          unset($_SESSION['csrf_token']);
          $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
          if (!empty($_POST['defaults'])) {
            $database->query('UPDATE settings SET setting = "False" WHERE name = "persistence"');
            $database->query('UPDATE settings SET setting = "True" WHERE name = "httpsforce"');
            $database->query('UPDATE settings SET setting = "True" WHERE name = "gdbserver"');
            $database->query('UPDATE settings SET setting = "False" WHERE name = "jsedit"');
            $database->query('UPDATE settings SET setting = "False" WHERE name = "guest"');
            $database->query('UPDATE settings SET setting = "False" WHERE name = "webshell"');
            $database->query('UPDATE settings SET setting = "False" WHERE name = "accounting"');
            $database->query('UPDATE settings SET setting = "False" WHERE name = "chat"');
            $database->query('UPDATE auth SET password = "'.hash('sha256','toor').'" WHERE username = "root"');
            $database->query('UPDATE auth SET password = "'.hash('sha256','guest').'" WHERE username = "guest"');
            $database->query('DELETE FROM auth WHERE username !="root" AND username !="guest"');
            system('/bin/sed -ie "s/^wpa_passphrase=.*/wpa_passphrase=armpilocal/" /etc/hostapd/hostapd.conf');
            system('/bin/sed -ie "s/^ssid=.*/ssid=ArmPI/" /etc/hostapd/hostapd.conf');
            system('sudo /usr/sbin/service hostapd restart');
            echo "<h1 class='redalert2'>Defaults restored.</h1>";
            echo "<h5 class='greenalert2'>Default Wireless: ArmPI armpi</h5>";
            echo "<h5 class='greenalert2'>Default login: root toor</h5>";
          }

        $wl = ['True','False', True, False];
        //Function to update Database from setting changes.
          function DBUpdate($param, $wl) {
            global $database;
            if (!empty($_POST[$param]) && in_array($_POST[$param],$wl)) {
              $sanitized_param = SQLite3::escapeString($_POST[$param]);
              $database->query('UPDATE settings SET setting = "'.$sanitized_param.'" WHERE name = "'.$param.'"');
            }
          }
          DBUpdate('persistence', $wl);
          DBUpdate('httpsforce', $wl);
          DBUpdate('gdbserver', $wl);
          DBUpdate('jsedit', $wl);
          DBUpdate('guest', $wl);
          DBUpdate('webshell', $wl);
          DBUpdate('accounting', $wl);
          DBUpdate('chat', $wl);

          //Custom setting update requests below.
          if (!empty($_POST['webshell']) && $_POST['webshell'] == "True" && empty($webterminal[0])) {
            system("sudo -u debuguser /usr/local/bin/webshell");
          } elseif (!empty($_POST['webshell']) && $_POST['webshell'] == "False" && !empty($webterminal[0])) {
            foreach ($webterminal as &$value) {
              system("sudo -u debuguser /bin/kill -9 ".$value);
            }
          }
          //The below starts/stops the GDBServer (multithreaded).
          if (!empty($_POST['gdbservermulti']) && in_array($_POST['gdbservermulti'],$wl) && $_POST['gdbservermulti'] == "True") {
            system("cd /tmp && gdbserver --multi 0.0.0.0:23000");
          } elseif (!empty($_POST['gdbservermulti'])) {
            foreach ($gdbservermulti as &$killmulti) {
              system("kill -9 ".$killmulti);
            }
          }

          //The below changes the ssid of the AP.
          if (!empty($_POST['ssid'])) {
            system('/bin/sed -ie "s/^ssid=.*/ssid='.$_POST['ssid'].'/" /etc/hostapd/hostapd.conf');
            system('sudo /usr/sbin/service hostapd restart');
            echo "<h1 class='greenalert' style='text-align:center;margin-top:20px;'>SSID Changed!</h1>";
          }
          //The below changes the password of the AP.
          if (!empty($_POST['wifipass'])) {
            $currentpassword = shell_exec("cat /etc/hostapd/hostapd.conf | grep -w wpa_passphrase | cut -d '=' -f2");
            if (trim($currentpassword) != trim($_POST['wifipasscurrent'])){
              echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Must enter current password correctly!</h1>";
            } elseif (empty($_POST['wifipassconf'])) {
              echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Must re-confirm password.</h1>";
            } elseif ($_POST['wifipassconf'] == $_POST['wifipass']) {
              system('/bin/sed -ie "s/^wpa_passphrase=.*/wpa_passphrase='.$_POST['wifipass'].'/" /etc/hostapd/hostapd.conf');
              system('sudo /usr/sbin/service hostapd restart');
              echo "<h1 class='greenalert' style='text-align:center;margin-top:20px;'>Password Changed Succesfully!</h1>";
            } else {
              echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Passwords don't match!!</h1>";
            }
          }

          //The below changes the guest login password assuming the guest setting is enabled.
          if (!empty($_POST['guestpassword']) && $guest == "True") {
            if (empty($_POST['guestpasswordconf'])) {
              echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Must re-confirm guest password.</h1>";
            } elseif ($_POST['guestpasswordconf'] == $_POST['guestpassword']) {
              $database->query('UPDATE auth SET password = "'.hash('sha256',$_POST['guestpassword']).'" WHERE username = "guest"');
              echo "<h1 class='greenalert' style=text-align:center;margin-top:20px;'>Guest Password Changed Succesfully.</h1>";
            } else {
              echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Guest Passwords don't match!!</h1>";
            }
          }

          if (!empty($_POST["remove_user"]) && $_POST["remove_user"] != "root" && $_POST["remove_user"] != "guest" && $accounting == "True") {
            $database->query('DELETE FROM auth WHERE username = "'.$_POST["remove_user"].'"');
            if (file_exists("/home/pi/debug/tmp/".md5($_POST["remove_user"]))) {
              rmdir("/home/pi/debug/tmp/".md5($_POST["remove_user"]));
            }
          }

          if (!empty($_POST["newpass"]) && !empty($_POST["edituserpass"])) {
            $newpass = hash('sha256',$_POST["newpass"]);
            $user = SQLite3::escapeString($_POST["edituserpass"]);
            $database->query('UPDATE auth SET password = "'.$newpass.'" WHERE username = "'.$user.'"');
            echo "<h1 class='greenalert' style=text-align:center;margin-top:20px;'>User Password Changed Succesfully.</h1>";
          }

        } elseif(!isset($_POST['password'])) {
          echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>CSRF Detected!</h1>";
        }
      }

      if ($username != "guest") {
        //The below changes the login password.
        if (!empty($_POST['password'])) {
          if (count($database->querySingle('SELECT * from auth WHERE username= "'.$username.'" AND password="'.hash('sha256',$_POST["passwordcurrent"]).'"')) != 1) {
            echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Must enter current password correctly!</h1>";
          } elseif (empty($_POST['passwordconf'])) {
            echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Must re-confirm password.</h1>";
          } elseif ($_POST['passwordconf'] == $_POST['password']) {
            $password = $database->query('UPDATE auth SET password = "'.hash('sha256',$_POST['password']).'" WHERE username = "'.$username.'"');
            echo "<h1 class='greenalert' style=text-align:center;margin-top:20px;'>Password Changed Succesfully.</h1>";
          } else {
            echo "<h1 class='redalert' style='text-align:center;margin-top:20px;'>Passwords don't match!!</h1>";
          }
        }
      }

    ?>

    <h1 style="text-align:center; margin-top:50px;">Settings</h1>
    <noscript><p style="text-align:center;"><strong>(This page won't function properly without javascript.)</strong></p></noscript>
    <?php
      if (!empty($_SESSION["user"]) && $_SESSION["user"] == "root") {
        echo '<form style="text-align:center; margin-top: 15px;" action="/settings" method="post">
                <input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />
                <p><input type="submit" value="Default Settings" name="defaults"></p>
              </form>';
      }
    ?>
    <br />

    <div class="container">
      <div class="row">
    <?php
      //The Below routes different setting pages based on the GET request made to /settings.
      if (!empty($_GET['update']) && $_GET['update'] == "user") {
        echo '<div class="col">
                <form style="text-align:center; margin-top: 15px;" action="/settings?update=user" method="post">
                  <h4>Login Settings</h4>
                  <p>Current Login Password: <input type="password" name="passwordcurrent"></p><br>
                  <p>Change Login Password: <input type="password" name="password"></p><br>
                  <p>Confirm Login Password: <input type="password" name="passwordconf"></p><br>';
        if ($guest == "True" && $username == "root") {
          echo '  <br />
                  <h4>Guest Login Settings</h4>
                  <p>Change Guest Password: <input type="password" name="guestpassword"></p><br>
                  <p>Confirm Guest Password: <input type="password" name="guestpasswordconf"></p><br>
                  <input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />';
        }
        echo '    <input type="submit">
                </form>
              </div>';
      } elseif (!empty($_GET['update']) && $_GET['update'] == "wifi") {
          echo '<div class="col">
                  <form style="text-align:center; margin-top: 15px;" action="/settings?update=wifi" method="post">
                    <h4>Wifi Settings</h4>
                    <p>Change SSID: <input type="text" name="ssid"></p><br>
                    <p>Current Wireless Password: <input type="password" name="wifipasscurrent"></p><br>
                    <p>Change Wireless Password: <input type="password" name="wifipass" pattern=".{10,}" title="10 characters minimum"></p><br>
                    <p>Confirm Wireless Password: <input type="password" name="wifipassconf" pattern=".{10,}" title="10 characters minimum"></p><br>
                    <input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'" />
                    <input type="submit">
                  </form>
                </div>';
      } elseif (!empty($_GET['update']) && $_GET['update'] == "accounting" && $accounting == "True") {
          echo '<div class="table-responsive">
                  <table style="heigh:100%;width:100%;">
                    <tr>
                      <th>Account Name</th>
                      <th>Sha256Hash</th>
                      <th>Action</th>
                      <th>Change Password</th>
                    </tr>';
          $results = $database->query('SELECT * FROM auth WHERE username != "root" AND username != "guest"');
          $data = array();
          while ($res = $results->fetchArray(1)) {
            array_push($data, $res);
          }
          foreach ($data as $key => $value) {
            $name = $value["username"];
            $$name = $value["password"];
            echo "<tr><td>".$name."</td><td>".$$name."</td><td><a href='#' onclick=delUser('".$name."','".$_SESSION["csrf_token"]."')><button type='button' class='btn'>Delete User</button></a></td>
                  <td><form action='/settings?update=accounting' method='post'><input type='password' name='newpass'><input type='hidden' name='edituserpass' value='".$name."'>
                  <input type='hidden' name='csrf_token' value='".$_SESSION['csrf_token']."' /><input type='submit' value='Change'></form></td></tr>";
          }
          echo '  </table>
                </div>';
      } else {
          $token = $_SESSION["csrf_token"];
          echo '<div class="col">
                  <form style="text-align:center; margin-top: 15px;" action="/settings" method="post">
                    <p><strong>Force HTTPS: </strong><br />Enabled by default for security.<label class="switch"><input onclick="changeSetting(\'httpsforce\',\''.$token.'\')" type="checkbox" id="httpsforce"><span class="slider round"></span></label></p><br/>
                    <p><strong>Guest Account: </strong><br />Enable a restricted guest account.<label class="switch"><input onclick="changeSetting(\'guest\',\''.$token.'\')" type="checkbox" id="guest"><span class="slider round"></span></label></p><br/>
                    <p><strong>Account Management: </strong><br />Enable account management system.<label class="switch"><input onclick="changeSetting(\'accounting\',\''.$token.'\')" type="checkbox" id="accounting"><span class="slider round"></span></label></p><br/>
                    <p><strong>File Persistence: </strong><br />Due to limited storage this is disabled by default.<label class="switch"><input onclick="changeSetting(\'persistence\',\''.$token.'\')" type="checkbox" id="persistence"><span class="slider round"></span></label></p><br/>
                    <p><strong>Chat: </strong><br />Chat server used for collaboration.<label class="switch"><input onclick="changeSetting(\'chat\',\''.$token.'\')" type="checkbox" id="chat"><span class="slider round"></span></label></p><br/>
                </div>
                <div class="col">
                    <p><strong>GDB Server: </strong><br />Debugging a binary auto starts GDB Server over port 10100.<label class="switch"><input onclick="changeSetting(\'gdbserver\',\''.$token.'\')" type="checkbox" id="gdbserver"><span class="slider round"></span></label></p><br/>
                    <p><strong>GDB Server Multi: </strong><br />Threaded GDB server using --multi over port 23000.<label class="switch"><input onclick="changeSetting(\'gdbservermulti\',\''.$token.'\')" type="checkbox" id="gdbservermulti"><span class="slider round"></span></label></p><br/>
                    <p><strong>JS Editor: </strong><br />JS Editor in web.  Disabled by default for security.<label class="switch"><input onclick="changeSetting(\'jsedit\',\''.$token.'\')" type="checkbox" id="jsedit"><span class="slider round"></span></label></p><br/>
                    <p><strong>Web Shell: </strong><br />Limited Web Shell ran by restricted user.  Disabled by default for security.<label class="switch"><input onclick="changeSetting(\'webshell\',\''.$token.'\')" type="checkbox" id="webshell"><span class="slider round"></span></label></p><br/>
                </div>
                  </form>
              </div>';
      }
    ?>
      </div>
    </div>
