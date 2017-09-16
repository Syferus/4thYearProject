<?php
    require_once "vendor/autoload.php";
    require_once 'tempdbconfig.php';
    use Twilio\Twiml;

    $response = new Twiml;
    $body = $_REQUEST['Body'];
    $from_number = $_REQUEST['From'];

    try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    if($body == 'Temp'){
        $sql="SELECT templog.temp_id, templog.temperature, phonegap_login.MobileNumber
        FROM templog
        INNER JOIN phonegap_login ON phonegap_login.SerialNumber = templog.SerialNumber
        WHERE templog.temp_id = (SELECT max(temp_id) FROM templog) AND phonegap_login.MobileNumber = $from_number";
    }

    if($body == 'Water level'){
        $sql="SELECT distancelog.distance_id, distancelog.distance, phonegap_login.MobileNumber, phonegap_login.height_of_container_cm
        FROM distancelog
        INNER JOIN phonegap_login ON phonegap_login.SerialNumber = distancelog.SerialNumber
        WHERE distancelog.distance_id = (SELECT max(distance_id) FROM distancelog) AND phonegap_login.MobileNumber = $from_number";
    }

    if($body == 'Pump on' OR $body == 'Pump off'){
        $sql="SELECT device_info.DeviceURL
        FROM phonegap_login
        INNER JOIN device_info ON phonegap_login.SerialNumber = device_info.SerialNumber
        WHERE phonegap_login.MobileNumber = $from_number";
    }


    $result = $conn->prepare($sql);
    
    $result->execute();

    $data = $result->fetchAll();

    $i = 0;

    foreach($data as $row) {
        if($body == 'Temp' ){
            $temp = $row['temperature'];

            $response->message('The water temperature of your system is ' . $temp . ' degrees celsius');
                     
        }else if( $body == 'Water level' ){
            $distance = $row['distance'];
            $height_container = $row['height_of_container_cm'];
            $percentage_full = ($distance / $height_container) * 100;

            $response->message('Your water container is ' . $percentage_full . '% empty');

        }else if( $body == 'Pump on' ){
            $device_url_pump_on = $row['DeviceURL'] . "pumpOn.php/";
            $ch = curl_init();

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, $device_url_pump_on);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            // grab URL and pass it to the browser
            curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);

            $response->message('Your pump has been turn off sucessfully!');

        }else if( $body == 'Pump off' ){
            $device_url_pump_on = $row['DeviceURL'] . "pumpOff.php/";
            $ch = curl_init();

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, $device_url_pump_on);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            // grab URL and pass it to the browser
            curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);
            
            $response->message('Your pump has been turned off sucessfully!');
        }

        $i++;
    }

    if ($i == 0) {
        $response->message('Opps - your number is not registered with ecoWater!' + $data);
    }
    print $response;

    } catch (PDOException $pe) {
        die("Could not connect to the database $dbname :" . $pe->getMessage());
    }
?>