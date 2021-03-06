<div id="header">
  <div id="logo">
    <h1><a href="index.php">Best.site.inthe.world</a></h1>
    <h2>You found the best</h2>
      <?php if (isset($_SESSION) && isset($_SESSION['userName'])): ?>
        <div class="right">
          <span class="login">Hello <?php echo $_SESSION['userName']; ?></span>
          <a href="/logout.php" class="">Logout</span>
        </div>
      <?php else: ?>
        <div class="right">
          <span class="login">Login</span> / 
          <span class="login register">Register</span>
        </div>
      <?php endif; ?>
  </div>
  <div id="menubar">
    <ul id="menu">
      <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
      <li><a href="/index.php">Home</a></li>
      <li><a href="/members.php">Members</a></li>
      <li><a href="/everyone.php">Everyone</a></li>
      <li><a href="/members2.php">Members2</a></li>
      <li><a href="/everyone2.php">Everyone2</a></li>
    </ul>
  </div>
</div>