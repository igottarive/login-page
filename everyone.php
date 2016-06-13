<?php
/**
 * @file
 * This file should be accessible to everyone
 */
require 'includes/includes.php';
?>
<!DOCTYPE HTML>
<html>
<?php require 'template/head.html'; ?>
<body>
  <div id="main">
    <?php require 'includes/header.php'; ?>
    <div id="site_content">
      <?php require 'template/everyone.html'; ?>
    </div>
    <?php require 'template/footer.html'; ?>
  </div>
  <div id="popup"></div>
</body>
</html>