<?php
    
  // Start PHP session and include Composer, which also brings in our Google Sheets PHP stuffs
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';
  
  
  
  // Store our names in this array
  $names = array();
  
  $names[] = array("1" => "TEST");
  
  // encode our PHP array into a JSON format and send that puppy back to the AJAX call
  echo json_encode($names);
