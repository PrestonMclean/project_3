<?php

  ini_set('display_errors', 'On');

  try {
    $db = new PDO('sqlite:./inc/journal.db');
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  } catch (Exception $e) {
    echo 'Error!: ' . $e->getMessage();
    die();
  }
