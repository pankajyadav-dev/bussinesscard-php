# Business Card Creator

A web application built with PHP, HTML, JavaScript, and Tailwind CSS that allows users to create, manage, and share digital business cards.

## Features

- User registration with email verification via OTP
- Login and logout functionality
- Browse different business card designs by category
- Create custom business cards with your information
- Edit and delete your business cards
- Share business cards via email
- Generate QR code for your business card
- View public shared business cards
- Modern and responsive design using Tailwind CSS

## Requirements

- PHP 7.4 or higher
- MySQL
- Composer 
- Web server 

## Installation

1. Clone the repository:
```
git clone https://github.com/pankajyadav-dev/bussinesscard-php.git
cd business-card-creator
```

2. Install PHP dependencies using Composer:
```
composer require phpmailer/phpmailer
```

3. Create a MySQL database and import the schema:
```
mysql -u username -p your_database_name < database/schema.sql
```

4. Configure the database connection:
   Edit `config/database.php` and update the database connection details:
   ```php
   $host = "localhost";     // Your database host
   $dbname = "business_card_db";  // Your database name
   $username = "root";      // Your database username
   $password = "";          // Your database password
   ```

5. Configure email settings:
   Edit `includes/functions.php` and update the SMTP settings in the `sendEmail()` function:
   ```php
   $mail->Host       = 'smtp.gmail.com';          // SMTP server
   $mail->Username   = 'your-email@gmail.com';    // SMTP username
   $mail->Password   = 'your-password';           // SMTP password
   $mail->setFrom('your-email@gmail.com', 'Business Card Creator');
   ```

6. Set up the web server:
   - For Apache, ensure that the document root points to the project directory
   - For Nginx, configure the server block to point to the project directory

## Usage

1. Open your browser and navigate to the project URL
2. Register a new account using your email
3. Verify your email using the OTP sent to your inbox
4. Browse card designs and create your business card
5. Share your business card via email or QR code


