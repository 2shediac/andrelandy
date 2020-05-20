<?php
// Connection object.
$servername =  'localhost';
$username = 'nautikos';
$password  = 'carlin';
$dbname = 'apitest';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
  
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
$update = store_data($datacanada, $country, $conn);
$url = 'http://universities.hipolabs.com/search?country=us';

$ch = curl_init($url);
// Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
// Return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$dataus = json_decode($result, true);
$country = 'US';
$update = store_data($dataus, $country, $conn);

echo '<table>';
echo '<tr><td> Name</td><td>Number of websites</td><td>Number of Domains</td></tr>';


$query = mysqli_query($conn,"SELECT ucname, iduniversity FROM  university order by ucname");
while ($row = mysqli_fetch_array($query)) { 
    $id = $row[1];
    // Get the domain count;
    $web = mysqli_query($conn, "SELECT * FROM  webpage where iduniverisity = ".$id); 
    $webcount = mysqli_num_rows($web);
    // Get the domain count;
    $resultdomain = mysqli_query($conn, "SELECT * FROM  domains where iduniversity = ".$id); 
    $domaincount = mysqli_num_rows($resultdomain);
    $style1 = '<span style="color: red">';
    if ($webcount > 1) {
        $style1 = '<span style="color: green">';
    }
    $style2 = '<span style="color: blue">';
    if ($domaincount > 1) {
        $style2 = '<span style="color: orange">';
    }

	 echo '<tr><td>'.$row[0].'</td><td '.$style1.'>'.$webcount.'</span></td><td '.$style2.$domaincount.'</span></td></tr>';
}
echo '</table>';
exit();

Function store_data($data, $country, $conn) {
// Function to store the data in the database;
  foreach ($data as $uc) {
      $alphacode = $uc['alpha_two_code'];
      $prov = $uc['state-province'];
      $ucname = $uc['name'];
      $alphacode = mysqli_real_escape_string($conn, $alphacode);
      $prov = mysqli_real_escape_string($conn, $prov);
      $ucname = mysqli_real_escape_string($conn, $ucname);
      $wplist = $uc['web_pages'];
      $domains = $uc['domains']; 
      $sql = "INSERT INTO university (alpha_code, prov, ucname, country)
              VALUES ('$alphacode', '$prov', '$ucname', '$country')";
      
      if ($conn->query($sql) === TRUE) {
          $universityid = $conn->insert_id;
          foreach ($wplist as $wp) {
             $wp = mysqli_real_escape_string($conn, $wp);

             $sqlwp = "INSERT into webpage (iduniverisity, webpage) 
                       VALUES ('$universityid', '$wp' )";
             if (!$conn->query($sqlwp) === TRUE) {
                 echo "Error: " . $sqlwp . "<br>" . $conn->error;      
                 exit();
             }                               
          }
          foreach ($domains as $domain) {
      	    $domain = mysqli_real_escape_string($conn, $domain);
             $sqldomain = "INSERT into domains (iduniversity, domainscol) 
                           VALUES ('$universityid', '$domain' )";
             if (!$conn->query($sqldomain) === TRUE) {
                 echo "Error: " . $sqldomain . "<br>" . $conn->error;      
                 exit();
             }                               
          }
          
      } else{
          echo "Error: " . $sql . "<br>" . $conn->error;
          exit();      
      }
   }
   return true;
}
  
