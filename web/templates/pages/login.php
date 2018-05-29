<?php $this->layout('page_template', ['title' => 'Login']) ?>


  <?php
  //logout using /logout.  /logout includes login.php using the dispatcher.php
  if ($_SERVER['REQUEST_URI'] == "/logout" && (!empty($_SESSION["authenticated"]) && $_SESSION["authenticated"] == "true")) {
    session_destroy();
    session_write_close();
    header('Location: /login');
  }

  //login stuff
  $username = null;
  $password = null;
  $guest = $database->querySingle('SELECT setting from settings WHERE name="guest"');
  $accounting = $database->querySingle('SELECT setting from settings WHERE name="accounting"');

  //If method is POST continue
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //If username and password arent empty continue
    if(!empty($_POST["username"]) && !empty($_POST["password"])) {
      $username = SQLite3::escapeString($_POST["username"]);
      $password = hash('sha256', $_POST["password"]);
      $result = $database->querySingle('SELECT * FROM auth WHERE username = "'.$username.'" AND password="'.$password.'"');
      $authvalue = count($result);
      //If guest setting is disabled echo failure else attempt to authenticate
      if($guest != "True" && $username == "guest"){
        echo "<h1 class='centerheader'>Guest Account Disabled</h1>
             <p style='text-align:center;'>Return to <a href='login'>login</a></p>";
      } elseif($accounting != "True" && ($username != "root" && $username != "guest")) {
        echo "<h1 class='centerheader'>Accounting Disabled</h1>
             <p style='text-align:center;'>Return to <a href='login'>login</a></p>";
      } elseif($authvalue == 1) {
          $folder = md5($username);
          if (!file_exists("/home/pi/debug/tmp/".$folder)) {
            mkdir("/home/pi/debug/tmp/".$folder,0755,true);
          }
          session_start();
          $_SESSION["authenticated"] = 'true';
          $_SESSION["user"] = $username;
          header('Location: /');
      }
      else {
          header('Location: login');
      }

  } else {
      header('Location: login');
  }
  } else {
  ?>

    <h1 class="centerheader">ArmPi <span style="font-size:8px;">(revision T)</span></h1>
    <p style="text-align:center;">Login to start reversing ARM binaries</p>

    <div class="login-form">
        <form action="/login" method="post">
            <h4 class="text-center">Login</h4>
            <div class="form-group">
                <input name="username" type="text" class="form-control" placeholder="Username" required="required" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAkCAYAAADo6zjiAAAAAXNSR0IArs4c6QAAAbNJREFUWAntV8FqwkAQnaymUkpChB7tKSfxWCie/Yb+gbdeCqGf0YsQ+hU95QNyDoWCF/HkqdeiIaEUqyZ1ArvodrOHxanQOiCzO28y781skKwFW3scPV1/febP69XqarNeNTB2KGs07U3Ttt/Ozp3bh/u7V7muheQf6ftLUWyYDB5yz1ijuPAub2QRDDunJsdGkAO55KYYjl0OUu1VXOzQZ64Tr+IiPXedGI79bQHdbheCIAD0dUY6gV6vB67rAvo6IxVgWVbFy71KBKkAFaEc2xPQarXA931ot9tyHphiPwpJgSbfe54Hw+EQHMfZ/msVEEURjMfjCjbFeG2dFxPo9/sVOSYzxmAwGIjnTDFRQLMQAjQ5pJAQkCQJ5HlekeERxHEsiE0xUUCzEO9AmqYQhiF0Oh2Yz+ewWCzEY6aYKKBZCAGYs1wuYTabKdNNMWWxnaA4gp3Yry5JBZRlWTXDvaozUgGTyQSyLAP0dbb3DtQlmcan0yngT2ekE9ARc+z4AvC7nauh9iouhpcGamJeX8XF8MaClwaeROWRA7nk+tUnyzGvZrKg0/40gdME/t8EvgG0/NOS6v9NHQAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;" autocomplete="off">
            </div>
            <div class="form-group">
                <input name="password" type="password" class="form-control" placeholder="Password" required="required" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAkCAYAAADo6zjiAAAAAXNSR0IArs4c6QAAAbNJREFUWAntV8FqwkAQnaymUkpChB7tKSfxWCie/Yb+gbdeCqGf0YsQ+hU95QNyDoWCF/HkqdeiIaEUqyZ1ArvodrOHxanQOiCzO28y781skKwFW3scPV1/febP69XqarNeNTB2KGs07U3Ttt/Ozp3bh/u7V7muheQf6ftLUWyYDB5yz1ijuPAub2QRDDunJsdGkAO55KYYjl0OUu1VXOzQZ64Tr+IiPXedGI79bQHdbheCIAD0dUY6gV6vB67rAvo6IxVgWVbFy71KBKkAFaEc2xPQarXA931ot9tyHphiPwpJgSbfe54Hw+EQHMfZ/msVEEURjMfjCjbFeG2dFxPo9/sVOSYzxmAwGIjnTDFRQLMQAjQ5pJAQkCQJ5HlekeERxHEsiE0xUUCzEO9AmqYQhiF0Oh2Yz+ewWCzEY6aYKKBZCAGYs1wuYTabKdNNMWWxnaA4gp3Yry5JBZRlWTXDvaozUgGTyQSyLAP0dbb3DtQlmcan0yngT2ekE9ARc+z4AvC7nauh9iouhpcGamJeX8XF8MaClwaeROWRA7nk+tUnyzGvZrKg0/40gdME/t8EvgG0/NOS6v9NHQAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;" autocomplete="off">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-secondary btn-block">Log in</button>
            </div>
            <?php
              if ($accounting == "True") {
                echo '<p style="text-align:center;"><a href="register">Register Now</a></p>';
              }
            ?>
        </form>
    </div>


  <?php } ?>

