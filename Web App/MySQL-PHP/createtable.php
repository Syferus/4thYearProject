 <?php
 // DB connection info
 $host = "eu-cdbr-azure-north-e.cloudapp.net";
 $user = "bbd400e41bb894";
 $pwd = "68c0ba5cbf74ef7";
 $db = "acsm_7e1519900d8edad";
 try{
     $conn = new PDO( "mysql:host=$host;dbname=$db", $user, $pwd);
     $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
     $sql = "
        CREATE TABLE phonegap_login (
reg_id int(1) NOT NULL,
  reg_date varchar(20) NOT NULL,
  fullname varchar(50) NOT NULL,
  email varchar(50) NOT NULL,
  password varchar(50) NOT NULL,
  height_of_container_cm int(1)
)";
     $conn->query($sql);
 }
 catch(Exception $e){
     die(print_r($e));
 }
 echo "<h3>Table created.</h3>";
 ?>