<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the script should send the message
    if (isset($_POST['description']) && isset($_FILES['image'])) {

        try {
            // Connect to the database using prepared statements
            $pdo = new PDO('mysql:host=localhost;dbname=koha_library', 'koha_library', 'koha123');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('SELECT phone, surname FROM borrowers WHERE phone IS NOT NULL AND TRIM(phone) <> :phone');
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Close the database connection
            $pdo = null;

            // Process the uploaded image
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo 'Error uploading the file.';
                exit;
            }

            $uploadedImage = $_FILES['image']['tmp_name'];

            // Validate file type
            $allowedFileTypes = ['image/png', 'image/jpeg', 'image/gif'];
            $fileType = mime_content_type($uploadedImage);

            if (!in_array($fileType, $allowedFileTypes)) {
                echo 'Invalid file type. Allowed types: ' . implode(', ', $allowedFileTypes);
                exit;
            }

            // Generate a unique image name
            $imageName = uniqid('image_') . '.png';

            // Save the image in the "upload_image" folder on the server
            $uploadPath = '/var/www/html/bss/upload_image/';

            if (!move_uploaded_file($uploadedImage, $uploadPath . $imageName)) {
                echo 'Failed to move the uploaded file to the server.';
                exit;
            }

            // Return the URL of the uploaded image
            $imageUrl = 'http://139.84.136.102:8080/upload_image/' . $imageName;

            // Description from the form
            $description = $_POST['description'];

            // Send messages to each phone number
            foreach ($rows as $row) {
                sendInformation($row['phone'], $imageUrl, $description, $row['surname']);
            }

            // Output success message
            echo 'Messages sent successfully!<br>';
            echo '<p>&nbsp;</p>';
            echo '<a href="index.html"><button>Go Back to Index</button></a>';

        } catch (PDOException $e) {
            echo 'Error connecting to the database: ' . $e->getMessage();
        }

    } else {
        echo 'Invalid form submission.';
    }

} else {
    echo 'Invalid request.';
}

// Function to send information
function sendInformation($to, $imageUrl, $description, $surname) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://whats-api.rcsoft.in/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'appkey' => '9b775612-aaac-45b1-b3d6-d1d7370d9a54',
            'authkey' => 'pUtcMF1wfCotkOvhutAgFg6NYJOjt3XoPGuetuX9V9cy82Lic2',
            'to' => $to,
            'message' => "Hey, {$surname}\n\n{$description}\n\nFr. Francis Sales Library",
            'file' => $imageUrl,
            'sandbox' => 'false'
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'cURL error: ' . curl_error($curl);
    }

    curl_close($curl);
    echo $response;
}
?>
