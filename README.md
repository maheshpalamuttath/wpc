# WhatsApp Patron Connect

This repository contains a straightforward web application designed for connecting with patrons via WhatsApp. The application allows library staff to send images along with descriptions, and this information is sent in bulk to a list of WhatsApp numbers present in the Koha database using the WhatsApp API.

## Dependencies

- [Bootstrap 4.5.2](https://getbootstrap.com/)
- [jQuery 3.5.1](https://jquery.com/)
- [Popper.js 2.5.2](https://popper.js.org/)

## Server Requirements

- PHP
- MySQL

## Installation

1. Navigate to the Koha opac htdocs directory:

```bash
cd /usr/share/koha/opac/htdocs
```

2. Clone the project repository:

```bash
sudo git clone https://github.com/maheshpalamuttath/bss.git
```

3. Set the appropriate permissions:

```bash
sudo chmod 755 -R inout
sudo chown www-data:www-data -R inout
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

1. Open the `http://serverurl/index.html` file in a web browser to access the WhatsApp Connect form.

2. Fill in the required information, including uploading an image and providing a description.

3. Click the "Send" button to initiate the process.

4. The application will send the information to the specified phone numbers via WhatsApp.


Feel free to contribute or report issues!
