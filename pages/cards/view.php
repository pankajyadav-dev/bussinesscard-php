<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage("Invalid card ID.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ?");
$stmt->execute([$card_id]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card = $stmt->fetch();

$is_owner = isLoggedIn() && $card['user_id'] == $_SESSION['user_id'];

$is_public_view = isset($_GET['share']) && $_GET['share'] == true;

if(!$is_owner && !$is_public_view) {
    setMessage("You don't have permission to view this card.", "error");
    header("Location: /pages/auth/login.php");
    exit;
}

$custom_fields = json_decode($card['custom_fields'], true);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$card['user_id']]);
$user = $stmt->fetch();

$designs = [
    1 => [
        'name' => 'Professional Design',
        'bg_color' => 'bg-gradient-to-r from-blue-600 to-blue-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-blue-500',
        'card_shadow' => 'shadow-blue-200'
    ],
    2 => [
        'name' => 'Creative Design',
        'bg_color' => 'bg-gradient-to-r from-pink-500 to-purple-600',
        'text_color' => 'text-white',
        'accent_color' => 'bg-pink-400',
        'card_shadow' => 'shadow-pink-200'
    ],
    3 => [
        'name' => 'Minimalist Design',
        'bg_color' => 'bg-gradient-to-r from-gray-800 to-gray-900',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-700',
        'card_shadow' => 'shadow-gray-200'
    ],
    4 => [
        'name' => 'Corporate Design',
        'bg_color' => 'bg-gradient-to-r from-gray-600 to-gray-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-500',
        'card_shadow' => 'shadow-gray-200'
    ]
];

$design = $designs[$card['design_id']]; 
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[70vh]">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold"><?php echo $card['card_name']; ?></h2>
        
        <?php if($is_owner): ?>
            <div class="flex flex-wrap gap-2">
                <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline whitespace-nowrap">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
                <button onclick="downloadCardImage()" class="bg-green-600 text-white px-3 sm:px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-download mr-1"></i> Download Image
                </button>
                <a href="/pages/cards/edit.php?id=<?php echo $card_id; ?>" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-edit mr-1"></i> Edit Card
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-2/3 mx-auto">
                <div id="business-card" class="border rounded-xl overflow-hidden shadow-2xl <?php echo $design['card_shadow']; ?> transform hover:scale-105 transition-transform duration-300">
                    <div class="h-56 <?php echo $design['bg_color']; ?> flex flex-col items-center justify-center p-6 <?php echo $design['text_color']; ?> relative">
                        <div class="absolute top-0 left-0 w-full h-full bg-black opacity-10 pattern-dots"></div>
                        <div class="relative z-10 text-center">
                            <h2 class="text-3xl font-bold mb-2"><?php echo $custom_fields['name']; ?></h2>
                            <?php if(!empty($custom_fields['job_title'])): ?>
                                <p class="text-xl opacity-90 mb-1"><?php echo $custom_fields['job_title']; ?></p>
                            <?php endif; ?>
                            <?php if(!empty($custom_fields['company'])): ?>
                                <p class="text-lg opacity-80"><?php echo $custom_fields['company']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if(!empty($custom_fields['phone'])): ?>
                                <a href="tel:<?php echo $custom_fields['phone']; ?>" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <span class="text-gray-700"><?php echo $custom_fields['phone']; ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['email'])): ?>
                                <a href="mailto:<?php echo $custom_fields['email']; ?>" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span class="text-gray-700"><?php echo $custom_fields['email']; ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['website'])): ?>
                                <a href="<?php echo $custom_fields['website']; ?>" target="_blank" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <span class="text-gray-700"><?php echo $custom_fields['website']; ?></span>
                                </a>
                            <?php endif; ?>
                            
                            <?php if(!empty($custom_fields['address'])): ?>
                                <div class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                    <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <span class="text-gray-700"><?php echo $custom_fields['address']; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($is_owner): ?>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">Share Your Business Card</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-bold mb-3">QR Code</h4>
                <div id="qrcode" class="mb-3"></div>
                <button id="downloadQR" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 mt-2">
                    <i class="fas fa-download mr-1"></i> Download QR Code
                </button>
            </div>
            
            <div>
                <h4 class="font-bold mb-3">Share via Email</h4>
                <form id="shareForm" method="POST" action="/pages/cards/share.php">
                    <input type="hidden" name="card_id" value="<?php echo $card_id; ?>">
                    
                    <div class="mb-4">
                        <label for="recipient_email" class="block text-gray-700 font-medium mb-2">Recipient Email</label>
                        <input type="email" id="recipient_email" name="recipient_email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message (Optional)</label>
                        <textarea id="message" name="message" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">I'd like to share my business card with you.</textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-1"></i> Send
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="font-bold mb-3">Direct Link</h4>
            <div class="flex">
                <input type="text" id="share_link" class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $_SERVER['HTTP_HOST'] . '/pages/cards/view.php?id=' . $card_id . '&share=true'; ?>" readonly>
                <button id="copyLink" class="bg-blue-600 text-white py-2 px-4 rounded-r-md hover:bg-blue-700">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo $_SERVER['HTTP_HOST'] . '/pages/cards/view.php?id=' . $card_id . '&share=true'; ?>",
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

    window.downloadCardImage = function() {
        const card = document.getElementById('business-card');
        // Remove hover effect temporarily
        card.style.transform = 'none';
        
        html2canvas(card, {
            scale: 2, // Better quality
            backgroundColor: null,
            logging: false
        }).then(canvas => {
            const image = canvas.toDataURL("image/png");
            const link = document.createElement('a');
            link.download = 'business-card.png';
            link.href = image;
            link.click();
            
            // Restore hover effect
            card.style.transform = '';
        });
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>