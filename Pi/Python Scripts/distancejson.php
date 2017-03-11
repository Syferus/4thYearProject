<?php
require_once 'tempdbconfig.php';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

	$sql="SELECT * FROM distanceLog";

	$result = $conn->prepare($sql);
	
	$result->execute();
	  
	$data = $result->fetchAll();
	
	$tempValues = array();
	$i = 0;

	foreach($data as $row) {
		$dateAndTemps = array();

		$datetime =$row['datetime'];
		$temp = $row['distance'];

		$dateAndTemps["Date"] = $datetime;
	        $dateAndTemps["Distance"] = $temp;

		$tempValues[$i]=$dateAndTemps;
	        $i++;		
	}
	  
	echo json_encode($tempValues);
  
?>