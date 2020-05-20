<?php
$url = 'http://universities.hipolabs.com/search?country=canada';
// Create a new cURL resource
$ch = curl_init($url);
// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Execute the POST request
$result = curl_exec($ch);
$datacanada = json_decode($result, true);
$country = 'Canada';
$update = store_data($datacanada, $country);
$url = 'http://universities.hipolabs.com/search?country=us';

$ch = curl_init($url);
// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$dataus = json_decode($result, true);
$country = 'US';
$update = store_data($dataus, $country);

exit();

Function store_data($data, $country) {
// Function to store the data in the database;
  $servername =  'localhost';
  $username = 'nautikos';
  $password  = 'carlin';
  $dbname = 'apitest';

  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
  } 
  foreach ($data as $uc) {
      $alphacode = $uc['alpha_two_code'];
      $prov = $uc['state-province'];
      $ucname = $uc['name'];
      $wplist = $uc['web_pages'];
      $domains = $uc['domains']; 
      $sql = "INSERT INTO university (alphacode, prov, ucname, country)
              VALUES ($alphacode, $prov, $ucname, $country)";
      
      if ($conn->query($sql) === TRUE) {
          $universityid = $conn->insert_id;
          foreach ($wplist as $wp) {
             $sqlwp = "INSERT into webpage ('iduniversity', 'webpage') 
                       VALUES ($universityid, $wp )";
             if ($conn->query($sqlwp) === TRUE) {
             } else {
                echo "Error: " . $sqlwp . "<br>" . $conn->error;      
                exit();
             }                               
          }
          foreach ($domains as $domain) {
             $sqldomain = "INSERT into domains ('iduniversity', 'domainscol') 
                       VALUES ($universityid, $domain )";
             if ($conn->query($sqldomain) === TRUE) {
             } else {
                echo "Error: " . $sqldomain . "<br>" . $conn->error;      
                exit();
             }                               
          }
          
      } else{
          echo "Error: " . $sql . "<br>" . $conn->error;      
      }
      exit();    
   }
      exit();
   }
  
