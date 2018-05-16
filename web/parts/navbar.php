    <!--Navbar-->
    <ul>
      <li><a class="active" href="/">Home</a></li>
      <?php
      if(empty($_SERVER['HTTPS'])) {
        echo '<li><a href="/terminal">GDB/MSF Limited WebShell</a></li>';
      } else {
        echo '<li><a href="http://'.$_SERVER['HTTP_HOST'].':8088/terminal/">GDB/MSF Limited WebShell</a></li>';
      }
      ?>
      <li><a href="/git">Git Repos</a></li>
      <li><a href="/lessons">Intro to ARM</a></li>
      <!--li><a href="/help">Help</a></li-->
      <li><a href="/settings">Settings</a></li>
      <li><a href="/reboot">Reboot</a></li>
    </ul>
    <!--End Navbar-->
