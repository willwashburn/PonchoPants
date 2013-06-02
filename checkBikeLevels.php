<?php

    $stationsUrl = 'http://citibikenyc.com/stations/json';
    $host        = '127.0.0.1';
    $dbname      = 'citi_bike';
    $user        = 'citibike';
    $passwd      = 'citibikewoo';

    /*
     * Grab that shiznip
     */


    try {

        $DBO = new PDO(
            'mysql:host=' . $host . ';dbname=' . $dbname . ';port=3306',
            $user,
            $passwd
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $stationsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $stationData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $time = time();

        echo $time. PHP_EOL;

        foreach ($stationData['stationBeanList'] as $station) {

            $sql    = 'select id from stations where id = :id';
            $STH    = $DBO->prepare($sql);
            $result = $STH->execute(array(':id' => $station['id']));

            if ($STH->rowCount() == 0) {

                $sql = "insert into stations set
                            id = :id,
							stationName = :stationName,
							latitude = :latitude,
							longitude = :longitude,
							stAddress1 = :streetAddress1,
							stAddress2 = :streetAddress2,
							landmark = :landmark,
							city = :city,
							postalCode = :postalCode,
							location = :location,
							altitude = :altitude";

                $STH = $DBO->prepare($sql);

                $STH->bindParam(':id', $station['id']);
                $STH->bindParam(':stationName', $station['stationName']);
                $STH->bindParam(':latitude', $station['latitude']);
                $STH->bindParam(':longitude', $station['longitude']);
                $STH->bindParam(':streetAddress1', $station['stAddress1']);
                $STH->bindParam(':streetAddress2', $station['stAddress2']);
                $STH->bindParam(':landmark', $station['landMark']);
                $STH->bindParam(':city', $station['city']);
                $STH->bindParam(':postalCode', $station['postalCode']);
                $STH->bindParam(':location', $station['location']);
                $STH->bindParam(':altitude', $station['altitude']);

                $STH->execute();

            }

            $sql = 'select totalDocks,availableDocks,status,statusCode from station_status where station_id =:id order by timestamp DESC limit 1';
            $STH = $DBO->prepare($sql);
            $STH->execute(array(':id' => $station['id']));

            if ($STH->rowCount() > 0) {

                $result                 = $STH->fetch();
                $previousDocks          = $result['totalDocks'];
                $previousAvailableDocks = $result['availableDocks'];
                $previousStatus         = $result['status'];
                $previousStatusCode     = $result['statusCode'];

            } else {
                $previousAvailableDocks = false;
                $previousDocks          = false;
                $previousStatus         = false;
                $previousStatusCode     = false;
            }


            if ($previousAvailableDocks != $station['availableDocks']
                OR  $previousDocks != $station['totalDocks']
                OR $previousStatusCode != $station['statusKey']
                OR $previousStatus != $station['statusValue']
            ) {

                $availableBikes = $station['totalDocks'] - $station['availableDocks'];

                $sql = "insert into station_status set station_id = :id,
							totalDocks = :totalDocks,
							availableDocks = :availableDocks, 
							availableBikes = :availableBikes,
							status = :status,
							statusCode =:statusCode,
							lastCommunicationTime = :lastCommunicationTime,
							testStation = :testStation,
							timestamp = :timestamp";
                $STH = $DBO->prepare($sql);

                $STH->bindParam(':id', $station['id']);
                $STH->bindParam(':totalDocks', $station['totalDocks']);
                $STH->bindParam(':availableDocks', $station['availableDocks']);
                $STH->bindParam(':availableBikes', $availableBikes);
                $STH->bindParam(':status', $station['statusValue']);
                $STH->bindParam(':statusCode', $station['statusKey']);
                $STH->bindParam(':lastCommunicationTime', $station['lastCommunicationTime']);
                $STH->bindParam(':testStation', $station['testStation']);
                $STH->bindParam(':timestamp', $time);

                $STH->execute();

                echo $station['stationName'] . ' updated.' . PHP_EOL;
            } else {
                echo $station['stationName']. ' has nothing to update'. PHP_EOL;
            }

        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    echo 'Script completed' . PHP_EOL;
