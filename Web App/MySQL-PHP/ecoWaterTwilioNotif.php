<?php
    require_once "vendor/autoload.php";
    require_once 'tempdbconfig.php';
    use Twilio\Rest\Client;
    
    $AccountSid = "AC17b15c2d680a9ac298d4fe9a7b4d2a7c";
    $AuthToken = "81d3d03eea9517e28194e3ac949e6f66";

    $client = new Client($AccountSid, $AuthToken);

    $mobile_numbers_array = array();

try {
    $phone_conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $phone_sql="SELECT MobileNumber, SerialNumber, height_of_container_cm FROM phonegap_login";

    $phone_result = $phone_conn->prepare($phone_sql);
    
    $phone_result->execute();

    $phone_data = $phone_result->fetchAll();

    foreach($phone_data as $phone_row) {
        $mobile_number = $phone_row['MobileNumber'];
        $serial_number = $phone_row['SerialNumber'];
        $height = $phone_row['height_of_container_cm'];

        //Temp checks
        try {

        $sql="SELECT * FROM templog WHERE temp_id = (SELECT max(temp_id) FROM templog) AND SerialNumber = '$serial_number'";

        $result = $phone_conn->prepare($sql);
        
        $result->execute();

        $data = $result->fetchAll();

        foreach($data as $row) {
            $datetime =$row['datetime'];
            $temp = $row['temperature'];

            
            if($temp < 19){

                $sms = $client->account->messages->create(

                $mobile_number,

                array(
                    'from' => "++353861802982", 
                    
                    // the sms body
                    'body' => "WARNING - your water temperature is $temp - your system is in danger of over-heating"
                    )
                );

             }

            if($temp < 2){

                $sms = $client->account->messages->create(

                $mobile_number,

                array(
                    'from' => "++353861802982", 
                    
                    'body' => "WARNING - your water temperature is $temp degrees celsius - the water is in danger of freezing"
                )
            );

        }
        }

        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }

        //Level checks
        try {

            $level_sql="SELECT * FROM distancelog WHERE distance_id = (SELECT max(distance_id) FROM distancelog) AND SerialNumber = '$serial_number'";

            $level_result = $phone_conn->prepare($level_sql);
            
            $level_result->execute();

            $level_data = $level_result->fetchAll();

            foreach($level_data as $level_row) {
                $current_level =$level_row['distance'];

                $percent_full = 100 - (($current_level / $height) * 100);
                echo $height;

                if($percent_full > 10){

                    $pump_on_sql="SELECT device_info.DeviceURL
                        FROM phonegap_login
                        INNER JOIN device_info ON phonegap_login.SerialNumber = device_info.SerialNumber
                        WHERE phonegap_login.MobileNumber = '$mobile_number'";

                    $pump_on_result = $phone_conn->prepare($pump_on_sql);
            
                    $pump_on_result->execute();

                    $pump_on_data = $pump_on_result->fetchAll();

                    foreach($pump_on_data as $pump_on_row) {
                        $device_url_pump_on = $pump_on_row['DeviceURL'] . "pumpOn.php/";

                        // set URL and other appropriate options
                        curl_setopt($ch, CURLOPT_URL, $device_url_pump_on);
                        curl_setopt($ch, CURLOPT_HEADER, 0);

                        // grab URL and pass it to the browser
                        curl_exec($ch);

                        // close cURL resource, and free up system resources
                        curl_close($ch);

                        $sms = $client->account->messages->create(

                        $mobile_number,

                        array(
                            'from' => "++353861802982", 
                            
                            // the sms body
                            'body' => "WARNING - your water container is only $percent_full % filled! The pump will be turned on for 5 minutes to refill the container"
                            )
                        );
                    }

                 }

                 if($percent_full > 95){

                    $pump_off_sql="SELECT device_info.DeviceURL
                        FROM phonegap_login
                        INNER JOIN device_info ON phonegap_login.SerialNumber = device_info.SerialNumber
                        WHERE phonegap_login.MobileNumber = '$mobile_number'";

                    $pump_off_result = $phone_conn->prepare($pump_off_sql);
            
                    $pump_off_result->execute();

                    $pump_off_data = $pump_off_result->fetchAll();

                    foreach($pump_off_data as $pump_off_row) {
                        $device_url_pump_off = $pump_off_row['DeviceURL'] . "pumpOff.php/";

                        // set URL and other appropriate options
                        curl_setopt($ch, CURLOPT_URL, $device_url_pump_off);
                        curl_setopt($ch, CURLOPT_HEADER, 0);

                        // grab URL and pass it to the browser
                        curl_exec($ch);

                        // close cURL resource, and free up system resources
                        curl_close($ch);

                        $sms = $client->account->messages->create(

                        $mobile_number,

                        array(
                            'from' => "++353861802982", 
                            
                            // the sms body
                            'body' => "WARNING - your water container is $percent_full % full and in danger of over-filling! Your pump has been automatically turned off."
                            )
                        );
                    }
                 }
            }

        } catch (PDOException $pe) {
            die("Could not connect to the database $dbname :" . $pe->getMessage());
        }

    }

} catch (PDOException $pe) {
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}
?>