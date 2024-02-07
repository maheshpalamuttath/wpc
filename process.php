<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data

    // Handle file upload
    $targetDir = '/var/www/html/wpc/file_upload/';

    // Generate a unique filename
    $uniqueFilename = uniqid() . '_' . basename($_FILES['file']['name']);

    $targetFile = $targetDir . $uniqueFilename;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // File uploaded successfully

        // MySQL credentials
        $host = 'localhost';
        $dbname = 'koha_library';
        $username = 'koha_library';
        $password = 'kohalib';

        // Connect to MySQL
        $mysqli = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // SQL query to get phone numbers with expiry date check
        $sql = "SELECT b.phone, b.surname, br.branchname, b.dateexpiry FROM borrowers b LEFT JOIN branches br ON b.branchcode = br.branchcode WHERE b.phone IS NOT NULL AND TRIM(b.phone) <> '' AND CURDATE() <= b.dateexpiry";
        $result = $mysqli->query($sql);

        // Initialize cURL
        $curl = curl_init();

        // Check if there are rows in the result
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // WhatsApp API URL
                $api_url = 'https://whats-api.rcsoft.in/api/create-message';

                // Upload the file to a server or cloud storage and obtain the URL
                $fileUrl = 'http://139.84.140.179:8000/file_upload/' . $uniqueFilename;

                // Prepare data for cURL
                $message = "Hey, {$row['surname']}\n\n{$_POST['description']}\n\n{$row['branchname']}";
                $postData = array(
                    'appkey' => '361cb907-eafc-451d-b3ac-36dc4f2ab0b5',
                    'authkey' => 'pUtcMF1wfCotkOvhutAgFg6NYJOjt3XoPGuetuX9V9cy82Lic2',
                    'to' => $row['phone'],
                    'message' => $message,
                    'file' => $fileUrl,  // Use the URL obtained after file upload
                    'sandbox' => 'false'
                );

                // Set cURL options
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $postData,
                ));

                // Execute cURL
                $response = curl_exec($curl);

                if ($response === false) {
                    echo 'Curl error: ' . curl_error($curl);
                } else {
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                    if ($httpCode != 200) {
                        echo 'HTTP request failed with code ' . $httpCode;
                    } else {
                        // Output the response
                        echo $response;
                    }
                }
            }

            // Output a "Back to Home" button
            echo '<br><br><a href="index.html">Back to Home</a>';
        } else {
            echo "No phone numbers found in the database.";
        }

        // Close cURL
        curl_close($curl);

        // Close MySQL connection
        $mysqli->close();

    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>
