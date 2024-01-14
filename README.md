# WhatsApp Patron Connect

This repository contains a simple web application designed for connecting with patrons via WhatsApp. The application allows library staff to send image along with description, and this information is sent in bulk to a list of WhatsApp numbers present in the Koha database using the WhatsApp API.

## Dependencies

- [Bootstrap 4.5.2](https://getbootstrap.com/)
- [jQuery 3.5.1](https://jquery.com/)
- [Popper.js 2.5.2](https://popper.js.org/)

## Server Requirements

- PHP
- MySQL

## Installation

1. Install necessary dependencies:

    ```bash
    sudo apt-get install -y git php libapache2-mod-php php-{bcmath,bz2,intl,gd,mbstring,mysql,zip}
    ```

2. Navigate to the Koha OPAC htdocs directory:

    ```bash
    cd /var/www/html
    ```

3. Clone the project repository:

    ```bash
    sudo git clone https://github.com/maheshpalamuttath/wpc.git
    ```

4. Set appropriate permissions:

    ```bash
    sudo chmod 755 -R wpc
    sudo chown www-data:www-data -R wpc
    cd wpc && sudo mkdir file_upload
    sudo chown www-data:www-data -R file_upload/ && sudo chmod 755 -R file_upload/
    ```

## Configuration

1. Configure the database connection in the PHP script (`process.php`). Modify the following lines with your database details:

    ```php
    $pdo = new PDO('mysql:host=localhost;dbname=koha_library', 'koha_library', 'koha123');
    ```

2. Set up the WhatsApp API details in the PHP script (`process.php`). Modify the following lines with your API key and authentication details:

    ```php
    'appkey' => 'your_app_key',
    'authkey' => 'your_auth_key',
    ```

## Usage

1. Open the `http://kohaopacurl/wpc` file in a web browser to access the WhatsApp Connect form.

2. Fill in the required information, including uploading an image and providing a description.

3. Click the "Send" button to initiate the process.

4. The application will send the information to the specified phone numbers via WhatsApp.


Feel free to contribute or report issues!
