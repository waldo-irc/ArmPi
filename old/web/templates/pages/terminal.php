<?php $this->layout('page_template', ['title' => 'Terminal']) ?>

    <script>
      document.domain = document.domain;
    </script>
    <h1 style="text-align:center; margin-top:50px;">Git</h1>
    <iframe src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:8088/terminal/" width="95%" height="75%" frameborder="0" style="position:absolute;"></iframe>
