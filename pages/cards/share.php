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

        $designs = [
            1 => [
                'name' => 'Professional Design',
                'category' => 'Professional',
                'bg_color' => 'bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800',
                'text_color' => 'text-white',
                'accent_color' => 'bg-blue-500'
            ],
            2 => [
                'name' => 'Creative Design',
                'category' => 'Creative',
                'bg_color' => 'bg-gradient-to-br from-pink-500 via-purple-500 to-purple-600',
                'text_color' => 'text-white',
                'accent_color' => 'bg-pink-400'
            ],
            3 => [
                'name' => 'Minimalist Design',
                'category' => 'Minimalist',
                'bg_color' => 'bg-gradient-to-r from-gray-800 to-gray-900',
                'text_color' => 'text-white',
                'accent_color' => 'bg-gray-700'
            ],
            4 => [
                'name' => 'Corporate Design',
                'category' => 'Corporate',
                'bg_color' => 'bg-gradient-to-r from-gray-600 to-gray-800',
                'text_color' => 'text-white',
                'accent_color' => 'bg-gray-500'
            ]
        ];

        $design = $designs[$card['design_id']] ?? $designs[1];
        
  
        $gradientColor = '';
        switch($design['bg_color']) {
            case 'bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800':
                $gradientColor = 'linear-gradient(to bottom right, #2563eb, #1d4ed8, #1e40af)';
                break;
            case 'bg-gradient-to-br from-pink-500 via-purple-500 to-purple-600':
                $gradientColor = 'linear-gradient(to bottom right, #ec4899, #a855f7, #9333ea)';
                break;
            case 'bg-gradient-to-r from-gray-800 to-gray-900':
                $gradientColor = 'linear-gradient(to right, #1f2937, #111827)';
                break;
            case 'bg-gradient-to-r from-gray-600 to-gray-800':
                $gradientColor = 'linear-gradient(to right, #4b5563, #1f2937)';
                break;
        }

        $body = "
            <html>
            <head>
                <title>$subject</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; }
                    .card { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin: 20px 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
                    .card-header { background-image: $gradientColor; color: white; padding: 40px 20px; text-align: center; }
                    .card-header h2 { margin: 0 0 10px 0; font-size: 24px; }
                    .card-body { padding: 20px; background: white; }
                    .btn { display: inline-block; background-color: " . ($design['accent_color'] === 'bg-blue-500' ? '#3b82f6' : 
                          ($design['accent_color'] === 'bg-pink-400' ? '#f472b6' : 
                          ($design['accent_color'] === 'bg-gray-700' ? '#374151' : '#6b7280'))) . "; 
                           color: white; padding: 12px 24px; text-decoration: none; 
                           border-radius: 6px; margin-top: 20px; font-weight: bold; }
                    .info-item { margin-bottom: 12px; }
                    .info-label { font-weight: bold; color: #374151; }
                    .profile-image { 
                        width: 80px; height: 80px; 
                        border-radius: 50%; 
                        margin: 0 auto 15px; 
                        border: 2px solid white;
                        overflow: hidden;
                    }
                    .profile-image img { width: 100%; height: 100%; object-fit: cover; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <p>Hello,</p>
                    <p>{$user['name']} has shared a business card with you:</p>
                    
                    <p><em>\"$message\"</em></p>
                    
                    <div class='card'>
                        <div class='card-header'>
                            " . (!empty($custom_fields['image']) ? "
                            <div class='profile-image'>
                                <img src='http://{$_SERVER['HTTP_HOST']}/uploads/cards/{$custom_fields['image']}' alt='Profile'>
                            </div>" : "") . "
                            <h2>{$custom_fields['name']}</h2>
                            <p>{$custom_fields['job_title']}</p>
                            <p>{$custom_fields['company']}</p>
                        </div>
                        <div class='card-body'>
                            <p class='info-item'><span class='info-label'>Email:</span> {$custom_fields['email']}</p>
                            <p class='info-item'><span class='info-label'>Phone:</span> {$custom_fields['phone']}</p>
                            <p class='info-item'><span class='info-label'>Website:</span> {$custom_fields['website']}</p>
                            <p class='info-item'><span class='info-label'>Address:</span> {$custom_fields['address']}</p>
                        </div>
                    </div>
                    
                    <p>To view the full business card, click the button below:</p>
                    <a href='http://$card_url' class='btn'>View Business Card</a>
                    
                    <p>If the button doesn't work, you can copy and paste this link into your browser: http://$card_url</p>
                    
                    <p>Regards,<br>Business Card Creator</p>
                </div>
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