<?php $this->layout('page_template', ['title' => 'Reboot']) ?>

    <h1 style="text-align:center; margin-top:50px;">Reboot</h1>
    <h5 style="color:red; text-align:center; margin-top:50px;">Are you sure you want to reboot?</h5>
    <p style="text-align:center;"><a href="/reboot?reboot=rnow">CONTINUE</a></p>

    <h5 style="color:red; text-align:center; margin-top:50px;">Or maybe Shut Down?</h5>
    <p style="text-align:center;"><a href="/reboot?reboot=shutdown">CONTINUE</a></p>

<?php
  // Reboot
  if(isset($_GET["reboot"]) && $_GET["reboot"] == "rnow") {
    system("sudo /sbin/reboot");
  }

  // Shutdown
  if(isset($_GET["reboot"]) && $_GET["reboot"] == "shutdown") {
    system("sudo /sbin/shutdown -h now");
  }
