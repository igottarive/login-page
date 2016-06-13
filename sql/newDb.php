<?php
  $db = new PDO('sqlite:/tmp/users.db'); // success

  $stm = "CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username varchar(255) UNIQUE NOT NULL,
    password char(32),
    email varchar(255),
    firstName varchar(255),
    lastName varchar(255),
    created INTEGER,
    lastLogin INTEGER
    );
  ";

  $ok = $db->query($stm);

  if (!$ok)
    die("Cannot execute query. $error");

  echo "Database Users created successfully";

?>