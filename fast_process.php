<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data

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

    // Get the selected branch code
    $selected_branch_code = $_POST['branch_code']; // Assuming branch code is submitted via the form

    // SQL query to get phone numbers with expiry date check for selected branch
    $sql = "SELECT b.phone, b.surname, br.branchname, b.dateexpiry
            FROM borrowers b
            LEFT JOIN branches br ON b.branchcode = br.branchcode
            WHERE b.phone IS NOT NULL
                AND TRIM(b.phone) <> ''
                AND CURDATE() <= b.dateexpiry
                AND b.branchcode = '$selected_branch_code'";
    $result = $mysqli->query($sql);

    // Initialize multi-cURL
    $multiCurl = curl_multi_init();

    // Initialize an array to store individual cURL handles
    $curlHandles = [];

    // Check if there are rows in the result
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // WhatsApp API URL
            $api_url = 'https://whats-api.rcsoft.in/api/create-message';

            // Prepare data for cURL
            $message = "Hey, {$row['surname']}\n\n{$_POST['description']}\n\n{$row['branchname']}";
            $postData = array(
                'appkey' => '361cb907-eafc-451d-b3ac-36dc4f2ab0b5',
                'authkey' => 'pUtcMF1wfCotkOvhutAgFg6NYJOjt3XoPGuetuX9V9cy82Lic2',
                'to' => $row['phone'],
                'message' => $message,
                'sandbox' => 'false'
            );

            // Check if file is uploaded
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                // Handle file upload

                // Directory for file upload
                $targetDir = '/var/www/html/wpc/file_upload/';

                // Generate a unique filename
                $uniqueFilename = uniqid() . '_' . basename($_FILES['file']['name']);

                $targetFile = $targetDir . $uniqueFilename;

                // Move uploaded file to target directory
                if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                    // File uploaded successfully
                    $fileUrl = 'http://139.84.140.179:8000/file_upload/' . $uniqueFilename;
                    $postData['file'] = $fileUrl;  // Use the URL obtained after file upload
                } else {
                    // Error uploading file
                    echo "Sorry, there was an error uploading your file.";
                    exit;
                }
            }

            // Initialize cURL handle for this request
            $curlHandle = curl_init();

            // Set cURL options
            curl_setopt_array($curlHandle, array(
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

            // Add the cURL handle to the array
            $curlHandles[] = $curlHandle;

            // Add the cURL handle to multi-cURL
            curl_multi_add_handle($multiCurl, $curlHandle);
        }

        // Execute all cURL handles simultaneously
        $running = null;
        do {
            curl_multi_exec($multiCurl, $running);
        } while ($running > 0);

        // Close all cURL handles
        foreach ($curlHandles as $curlHandle) {
            curl_multi_remove_handle($multiCurl, $curlHandle);
            curl_close($curlHandle);
        }

        // Close multi-cURL
        curl_multi_close($multiCurl);

        // Output a success message
        echo "WhatsApp messages sent successfully to users of branch with code $selected_branch_code!<br><br>";

        // Output a "Back to Home" button
        echo '<a href="index.html">Back to Home</a>';

    } else {
        echo "No phone numbers found for users of branch with code $selected_branch_code.";
    }

    // Close MySQL connection
    $mysqli->close();
}

?>
