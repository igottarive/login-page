<?php

/**
 * @file
 * Index page
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
      <?php require 'template/body.html'; ?>
    </div>
    <?php require 'template/footer.html'; ?>
  </div>
  <div id="popup"></div>
</body>
</html>