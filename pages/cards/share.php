<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';
require_once '../../config/config.php';

if(!isLoggedIn()) {
    setMessage("You must be logged in to access this page.", "error");
    header("Location: " . BASE_URL . "pages/auth/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_id = isset($_POST['card_id']) ? (int)$_POST['card_id'] : 0;
    $recipient_email = sanitizeInput($_POST['recipient_email']);
    $message = sanitizeInput($_POST['message']);

    $errors = [];
    
    if(empty($card_id)) {
        $errors[] = "Invalid card selected";
    }
    
    if(empty($recipient_email)) {
        $errors[] = "Recipient email is required";
    } elseif(!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    $stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ? AND user_id = ?");
    $stmt->execute([$card_id, $_SESSION['user_id']]);
    
    if($stmt->rowCount() == 0) {
        $errors[] = "Card not found or you don't have permission to share it";
    } else {
        $card = $stmt->fetch();
        $custom_fields = json_decode($card['custom_fields'], true);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
    if(empty($errors)) {
        $subject = $user['name'] . " shared a business card with you";
        $card_url = $_SERVER['HTTP_HOST'] . '/' . ltrim(BASE_URL, 'http://'. $_SERVER['HTTP_HOST'] . '/') . 'pages/cards/view.php?id=' . $card_id . '&share=true';

        // Improved image handling with base64 encoding
        $imageData = '';
        $imageType = '';
        if(!empty($custom_fields['image'])) {
            $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/' . $custom_fields['image'];
            if(file_exists($image_path)) {
                $imageType = pathinfo($image_path, PATHINFO_EXTENSION);
                $imageData = base64_encode(file_get_contents($image_path));
                
                // Handle various image types correctly
                switch(strtolower($imageType)) {
                    case 'jpg':
                    case 'jpeg':
                        $imageType = 'jpeg';
                        break;
                    case 'png':
                        $imageType = 'png';
                        break;
                    case 'gif':
                        $imageType = 'gif';
                        break;
                    case 'webp':
                        $imageType = 'webp';
                        break;
                    default:
                        $imageType = 'jpeg'; // Default to JPEG for unsupported formats
                }
            }
        }

        $body = "
                <!DOCTYPE html>
                <html lang=\"en\">
                <head>
                    <meta charset=\"UTF-8\">
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    <title>$subject</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
                        .container { max-width: 600px; margin: 0 auto; background-color: #f3f4f6; padding: 20px; }
                        .card { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); background-color: #ffffff; }
                        .card-inner { background-color: #ffffff; border-radius: 12px; overflow: hidden; }
                        .header { min-height: 224px; padding: 32px; position: relative; color: #ffffff; }
                        .header-table { width: 100%; background-color: #2563eb; background: linear-gradient(to bottom right, #2563eb, #1d4ed8, #1e40af); }
                        .pattern-dots { 
                            position: absolute; 
                            top: 0; left: 0; right: 0; bottom: 0;
                            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
                            background-size: 8px 8px;
                        }
                        .header-content { 
                            position: relative; 
                            z-index: 10;
                            display: table;
                            width: 100%;
                        }
                        .profile-section {
                            width: 128px;
                            height: 128px;
                            display: table-cell;
                            vertical-align: top;
                            padding-right: 24px;
                        }
                        .profile-image {
                            width: 100%;
                            height: 100%;
                            border-radius: 50%;
                            border: 4px solid white;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            overflow: hidden;
                        }
                        .profile-image img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .header-text { 
                            display: table-cell;
                            vertical-align: middle;
                        }
                        .header-text h2 { 
                            font-size: 30px;
                            line-height: 36px;
                            font-weight: bold;
                            margin: 0 0 8px 0;
                            color: #ffffff;
                        }
                        .header-text p { 
                            margin: 4px 0;
                            font-size: 20px;
                            line-height: 28px;
                            color: #ffffff;
                        }
                        .contact-grid {
                            padding: 32px;
                            width: 100%;
                        }
                        .contact-table {
                            width: 100%;
                            border-collapse: separate;
                            border-spacing: 16px;
                        }
                        .contact-cell {
                            width: 50%;
                            vertical-align: top;
                        }
                        .contact-item {
                            display: table;
                            width: 100%;
                            padding: 12px;
                            background-color: #f8f9fa;
                            border-radius: 8px;
                            margin-bottom: 16px;
                            text-decoration: none;
                        }
                        .icon-circle {
                            width: 48px;
                            height: 48px;
                            border-radius: 50%;
                            margin-right: 16px;
                            display: table-cell;
                            vertical-align: middle;
                            text-align: center;
                            background-color: #2563eb;
                        }
                        .icon-circle svg {
                            display: inline-block;
                            vertical-align: middle;
                            color: #ffffff;
                        }
                        .contact-text {
                            display: table-cell;
                            vertical-align: middle;
                            font-size: 14px;
                            color: #374151;
                        }
                        .btn {
                            display: inline-block;
                            background-color: #3b82f6;
                            color: #ffffff;
                            padding: 12px 24px;
                            border-radius: 6px;
                            text-decoration: none;
                            margin-top: 24px;
                            font-weight: bold;
                        }
                        h1 {
                            color: #1e40af;
                            margin-top: 32px;
                        }
                        p {
                            margin: 16px 0;
                            color: #000000;
                        }
                        .message {
                            font-style: italic;
                            margin: 16px 0;
                            color: #000000;
                        }
                        .link-info {
                            margin-top: 20px;
                            color: #000000;
                        }
                        .footer {
                            margin-top: 20px;
                            border-top: 1px solid #e5e7eb;
                            padding-top: 20px;
                            color: #000000;
                        }
                    </style>
                </head>
                <body style=\"margin: 0; padding: 0; background-color: #f3f4f6;\">
                    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#f3f4f6\">
                        <tr>
                            <td align=\"center\" style=\"padding: 20px;\">
                                <table class=\"container\" width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#f3f4f6\">
                                    <tr>
                                        <td>
                                            <p style=\"color: #000000;\">Hello,</p>
                                            <p style=\"color: #000000;\">{$user['name']} has shared a business card with you:</p>
                                            " . (!empty($message) ? "<p class=\"message\" style=\"font-style: italic; color: #000000;\">\"$message\"</p>" : "") . "
                                            
                                            <table class=\"card\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\" style=\"border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin: 20px 0;\">
                                                <tr>
                                                    <td>
                                                        <table class=\"header-table\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#2563eb\" style=\"background-color: #2563eb; color: #ffffff;\">
                                                            <tr>
                                                                <td style=\"padding: 32px;\">
                                                                    <table class=\"header-content\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                        <tr>
                                                                            " . (!empty($imageData) ? "
                                                                            <td class=\"profile-section\" width=\"128\" style=\"padding-right: 24px;\">
                                                                                <div class=\"profile-image\" style=\"width: 128px; height: 128px; border-radius: 50%; border: 4px solid white; overflow: hidden;\">
                                                                                    <img src=\"data:image/{$imageType};base64,{$imageData}\" alt=\"Profile\" style=\"width: 100%; height: 100%; object-fit: cover;\">
                                                                                </div>
                                                                            </td>" : "") . "
                                                                            <td class=\"header-text\" style=\"vertical-align: middle;\">
                                                                                <h2 style=\"font-size: 30px; line-height: 36px; font-weight: bold; margin: 0 0 8px 0; color: #ffffff;\">{$custom_fields['name']}</h2>
                                                                                " . (!empty($custom_fields['job_title']) ? "<p style=\"margin: 4px 0; font-size: 20px; line-height: 28px; color: #ffffff;\">{$custom_fields['job_title']}</p>" : "") . "
                                                                                " . (!empty($custom_fields['company']) ? "<p style=\"margin: 4px 0; font-size: 20px; line-height: 28px; color: #ffffff;\">{$custom_fields['company']}</p>" : "") . "
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        
                                                        <table class=\"contact-table\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"padding: 32px;\">
                                                            <tr>
                                                                <td class=\"contact-cell\" width=\"50%\" style=\"vertical-align: top;\">
                                                                    " . (!empty($custom_fields['phone']) ? "
                                                                    <a href=\"tel:{$custom_fields['phone']}\" class=\"contact-item\" style=\"display: block; padding: 12px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 16px; text-decoration: none;\">
                                                                        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                                <td width=\"48\" style=\"vertical-align: middle;\">
                                                                                    <div class=\"icon-circle\" style=\"width: 48px; height: 48px; border-radius: 50%; background-color: #2563eb; text-align: center; vertical-align: middle;\">
                                                                                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"display: inline-block; vertical-align: middle;\">
                                                                                            <path d=\"M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z\"></path>
                                                                                        </svg>
                                                                                    </div>
                                                                                </td>
                                                                                <td class=\"contact-text\" style=\"vertical-align: middle; font-size: 14px; color: #374151;\">
                                                                                    {$custom_fields['phone']}
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </a>" : "") . "
                                                                    
                                                                    " . (!empty($custom_fields['website']) ? "
                                                                    <a href=\"{$custom_fields['website']}\" target=\"_blank\" class=\"contact-item\" style=\"display: block; padding: 12px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 16px; text-decoration: none;\">
                                                                        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                                <td width=\"48\" style=\"vertical-align: middle;\">
                                                                                    <div class=\"icon-circle\" style=\"width: 48px; height: 48px; border-radius: 50%; background-color: #2563eb; text-align: center; vertical-align: middle;\">
                                                                                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"display: inline-block; vertical-align: middle;\">
                                                                                            <circle cx=\"12\" cy=\"12\" r=\"10\"></circle>
                                                                                            <line x1=\"2\" y1=\"12\" x2=\"22\" y2=\"12\"></line>
                                                                                            <path d=\"M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z\"></path>
                                                                                        </svg>
                                                                                    </div>
                                                                                </td>
                                                                                <td class=\"contact-text\" style=\"vertical-align: middle; font-size: 14px; color: #374151;\">
                                                                                    {$custom_fields['website']}
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </a>" : "") . "
                                                                </td>
                                                                <td class=\"contact-cell\" width=\"50%\" style=\"vertical-align: top;\">
                                                                    " . (!empty($custom_fields['email']) ? "
                                                                    <a href=\"mailto:{$custom_fields['email']}\" class=\"contact-item\" style=\"display: block; padding: 12px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 16px; text-decoration: none;\">
                                                                        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                                <td width=\"48\" style=\"vertical-align: middle;\">
                                                                                    <div class=\"icon-circle\" style=\"width: 48px; height: 48px; border-radius: 50%; background-color: #2563eb; text-align: center; vertical-align: middle;\">
                                                                                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"display: inline-block; vertical-align: middle;\">
                                                                                            <path d=\"M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z\"></path>
                                                                                            <polyline points=\"22,6 12,13 2,6\"></polyline>
                                                                                        </svg>
                                                                                    </div>
                                                                                </td>
                                                                                <td class=\"contact-text\" style=\"vertical-align: middle; font-size: 14px; color: #374151;\">
                                                                                    {$custom_fields['email']}
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </a>" : "") . "
                                                                    
                                                                    " . (!empty($custom_fields['address']) ? "
                                                                    <div class=\"contact-item\" style=\"display: block; padding: 12px; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 16px;\">
                                                                        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                                <td width=\"48\" style=\"vertical-align: middle;\">
                                                                                    <div class=\"icon-circle\" style=\"width: 48px; height: 48px; border-radius: 50%; background-color: #2563eb; text-align: center; vertical-align: middle;\">
                                                                                        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"#ffffff\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" style=\"display: inline-block; vertical-align: middle;\">
                                                                                            <path d=\"M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z\"></path>
                                                                                            <circle cx=\"12\" cy=\"10\" r=\"3\"></circle>
                                                                                        </svg>
                                                                                    </div>
                                                                                </td>
                                                                                <td class=\"contact-text\" style=\"vertical-align: middle; font-size: 14px; color: #374151;\">
                                                                                    {$custom_fields['address']}
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>" : "") . "
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            <p class=\"link-info\" style=\"margin-top: 20px; color: #000000;\">To view the full business card, click the button below:</p>
                                            <a href=\"http://$card_url\" class=\"btn\" style=\"display: inline-block; background-color: #3b82f6; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 24px; font-weight: bold;\">View Business Card</a>
                                            
                                            <p class=\"link-info\" style=\"margin-top: 20px; color: #000000;\">If the button doesn't work, you can copy and paste this link into your browser:<br>http://$card_url</p>
                                            
                                            <div class=\"footer\" style=\"margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 20px;\">
                                                <p style=\"color: #000000;\">Regards,<br>Business Card Creator</p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
            ";

        if(sendEmail($recipient_email, $subject, $body)) {
            setMessage("Business card has been shared successfully.", "success");
            header("Location: " . BASE_URL . "pages/cards/view.php?id=" . $card_id);
            exit;
        } else {
            $errors[] = "Failed to send email. Please try again.";
        }
    }
}


if(isset($_GET['id']) && !empty($_GET['id'])) {
    $card_id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ? AND user_id = ?");
    $stmt->execute([$card_id, $_SESSION['user_id']]);
    
    if($stmt->rowCount() == 0) {
        setMessage("Card not found or you don't have permission to share it", "error");
        header("Location: " . BASE_URL . "pages/profile/dashboard.php");
        exit;
    }
    
    $card = $stmt->fetch();
    $custom_fields = json_decode($card['custom_fields'], true);
    
    // Get card design information
    $design_id = $card['design_id'];
    $stmt = $pdo->prepare("SELECT * FROM card_designs WHERE id = ?");
    $stmt->execute([$design_id]);
    $design = $stmt->fetch();
} else {
    setMessage("Invalid card ID.", "error");
    header("Location: " . BASE_URL . "pages/profile/dashboard.php");
    exit;
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">Share Your Business Card</h2>
        <a href="<?php echo url('pages/cards/view.php?id=' . $card_id); ?>" class="text-blue-600 hover:underline whitespace-nowrap">
            <i class="fas fa-arrow-left mr-1"></i> Back to Card
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mb-6">
        <?php if(isset($errors) && !empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <div>
                <h3 class="text-lg sm:text-xl font-bold mb-4">Card Preview</h3>
                
                <div class="border rounded-lg overflow-hidden shadow-md">
                    <div class="h-32 sm:h-40 <?php echo $design['bg_color']; ?> flex flex-col items-center justify-center p-4 text-white">
                        <?php if(!empty($custom_fields['image'])): ?>
                            <div class="w-16 h-16 mb-2 rounded-full overflow-hidden border-2 border-white shadow-md">
                                <?php
                                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/cards/' . $custom_fields['image'];
                                if(file_exists($image_path)) {
                                    $imageType = pathinfo($image_path, PATHINFO_EXTENSION);
                                    $imageData = base64_encode(file_get_contents($image_path));
                                    echo '<img src="data:image/'.$imageType.';base64,'.$imageData.'" alt="Profile" class="w-full h-full object-cover">';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <h2 class="text-lg sm:text-xl font-bold mb-1"><?php echo $custom_fields['name']; ?></h2>
                        <?php if(!empty($custom_fields['job_title'])): ?>
                            <p class="text-sm sm:text-md"><?php echo $custom_fields['job_title']; ?></p>
                        <?php endif; ?>
                        <?php if(!empty($custom_fields['company'])): ?>
                            <p class="text-xs sm:text-sm mt-1"><?php echo $custom_fields['company']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            <?php if(!empty($custom_fields['phone'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['phone']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['email'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['email']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['website'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-globe text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['website']; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['address'])): ?>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-600 mr-2"></i>
                                    <span><?php echo $custom_fields['address']; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg sm:text-xl font-bold mb-4">Email This Card</h3>
                
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $card_id; ?>" class="space-y-4">
                    <input type="hidden" name="card_id" value="<?php echo $card_id; ?>">
                    
                    <div class="mb-4">
                        <label for="recipient_email" class="block text-gray-700 font-medium mb-2">Recipient's Email <span class="text-red-500">*</span></label>
                        <input type="email" id="recipient_email" name="recipient_email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Personal Message</label>
                        <textarea id="message" name="message" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4">I'd like to share my business card with you.</textarea>
                    </div>
                    
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-1"></i> Send Card
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-bold mb-4">Other Sharing Options</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <div>
                <h4 class="font-bold mb-3">QR Code</h4>
                <p class="text-gray-600 text-sm sm:text-base mb-3">Share your business card in person using this QR code.</p>
                <div id="qrcode" class="mb-3"></div>
                <button id="downloadQR" class="w-full sm:w-auto bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors mt-2">
                    <i class="fas fa-download mr-1"></i> Download QR Code
                </button>
            </div>

            <div>
                <h4 class="font-bold mb-3">Direct Link</h4>
                <p class="text-gray-600 text-sm sm:text-base mb-3">Copy this link to share your business card on social media or messaging apps.</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="share_link" 
                           class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md sm:rounded-r-none rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim(BASE_URL, 'http://'. $_SERVER['HTTP_HOST'] . '/') . 'pages/cards/view.php?id=' . $card_id . '&share=true'; ?>" 
                           readonly>
                    <button id="copyLink" 
                            class="bg-blue-600 text-white py-2 px-4 rounded-r-md sm:rounded-l-none rounded-l-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim(BASE_URL, 'http://'. $_SERVER['HTTP_HOST'] . '/') . 'pages/cards/view.php?id=' . $card_id . '&share=true'; ?>",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    document.getElementById('copyLink').addEventListener('click', function() {
        var shareLink = document.getElementById('share_link');
        shareLink.select();
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    });
    
    document.getElementById('downloadQR').addEventListener('click', function() {
        var img = document.querySelector('#qrcode img');
        var link = document.createElement('a');
        link.download = 'business-card-qr.png';
        link.href = img.src;
        link.click();
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>