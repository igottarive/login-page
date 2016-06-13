<?php
/**
 * @file
 * This file shouldn't be accessible unless logged in
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
      <?php
      //Check for logged in and redirect if not
      if(isset($_SESSION) && isset($_SESSION['username'])) {
        require 'template/members.html';
      } else {
        //Else show cool stuff
        require 'template/locked.html';
      }
      ?>
    </div>
    <?php require 'template/footer.html'; ?>
  </div>
  <div id="popup"></div>
</body>
</html>