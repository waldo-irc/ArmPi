<?php $this->layout('page_template', ['title' => 'Terminal']) ?>

    <script>
      document.domain = document.domain;
    </script>
    <h1 style="text-align:center; margin-top:50px;">Web Shell</h1>
    <iframe src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:8088/terminal/" width="100%" height="100%" frameborder="0" style="position:absolute;"></iframe>
