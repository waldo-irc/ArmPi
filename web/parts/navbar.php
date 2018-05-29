    <?php
      //Grab our database and prepare a function to output current storage availability
      $database = new SQLite3('armpi.sqlite');

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

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg">
      <a class="navbar-brand" href="/">
        <img src="/files/images/logo.png" width="auto" height="40" alt="">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/uploads">Uploaded Binaries</a>
          </li>
          <?php
          //If webshell setting is enabled show the webshell link
          $webshell = $database->querySingle('SELECT setting from settings WHERE name = "webshell"');
          $accounting = $database->querySingle('SELECT setting from settings WHERE name = "accounting"');
          $chat = $database->querySingle('SELECT setting from settings WHERE name = "chat"');
          exec("ps aux | grep '[w]eb-terminal' | grep -v 'r2' | tr -s ' ' | cut -d ' ' -f 2", $webterminal);
          if (empty($_SERVER['HTTPS']) && $webshell == "True" && !empty($webterminal[0])) {
            echo '<li class="nav-item"><a class="nav-link" href="/terminal">GDB/MSF Limited WebShell</a></li>';
          } elseif ($webshell == "True" && !empty($webterminal[0])) {
            echo '<li class="nav-item"><a class="nav-link" href="http://'.$_SERVER['HTTP_HOST'].':8088/terminal/">GDB/MSF Limited WebShell</a></li>';
          }
          ?>
          <li class="nav-item">
            <a class="nav-link" href="/git">Git Repos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/lessons">Intro to ARM</a>
          </li>
          <?php
            //Show JS editor if True
            $jsedit = $database->querySingle('SELECT setting from settings WHERE name = "jsedit"');
            if (!empty($_SESSION['user']) && $_SESSION['user'] == "root") {
              echo '<li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Settings
                      </a>
                      <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="/settings">General Settings</a>
                        <a class="dropdown-item" href="/settings?update=user">User Settings</a>
                        <a class="dropdown-item" href="/settings?update=wifi">Wifi Settings</a>
                        <a class="dropdown-item" href="/csseditor?editor=css">Custom CSS</a>';
                if ( $jsedit == "True" ) {
                  echo '<a class="dropdown-item" href="/csseditor?editor=js">Custom JS</a>';
                }
                if ( $accounting == "True" ) {
                  echo '<a class="dropdown-item" href="/settings?update=accounting">Accounts</a>';
                }
                echo '</div>
                    </li>';
            }
            if (!empty($_SESSION["authenticated"]) && $_SESSION["authenticated"] == "true" && $_SESSION["user"] != "root" && $_SESSION["user"] != "guest") {
              echo '<li class="nav-item"><a class="nav-link" href="/settings?update=user">Settings</a></li>';
            }
            //Show Logout when authenticated
            if (!empty($_SESSION["authenticated"]) && $_SESSION["authenticated"] == "true") {
              echo '<li class="nav-item"><a class="nav-link" href="/logout">Logout('.$_SESSION['user'].')</a></li>';
            }
            if (!empty($chat) && $chat == "True") {
              echo '<li class="nav-item"><a class="nav-link" href="/c27a99edeafa1a822c8f10f39ed6ecec/app/index.php" target="_blank">Chat</a></li>';
            }
          ?>
        </ul>
        <?php
          $df = HumanSize(disk_free_space("/"));
          if (!empty($_SESSION["user"]) && $_SESSION["user"] == "root") {
            echo '<ul class="navbar-nav ml-auto">
                    <p style="margin-right: 10px;"><strong>'.$df.' available.</strong></p>
                    <div class="dropdown show">
                      <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Power Options
                      </a>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="/reboot?reboot=rnow">Restart</a>
                        <a class="dropdown-item" href="/reboot?reboot=shutdown">Shutdown</a>
                      </div>
                    </div>
                  </ul>';
          }
        ?>
                </div>
              </nav>
    <!--End Navbar-->
