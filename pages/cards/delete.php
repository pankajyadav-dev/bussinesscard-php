<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';

if(!isLoggedIn()) {
    setMessage("You must be logged in to access this page.", "error");
    header("Location: /pages/auth/login.php");
    exit;
}

if(!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage("Invalid card ID.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ? AND user_id = ?");
$stmt->execute([$card_id, $_SESSION['user_id']]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found or you don't have permission to delete it.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $card = $stmt->fetch();
    $custom_fields = json_decode($card['custom_fields'], true);
    if(!empty($custom_fields['image'])) {
        $image_path = "../../uploads/cards/" . $custom_fields['image'];
        if(file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM user_cards WHERE id = ? AND user_id = ?");
    
    if($stmt->execute([$card_id, $_SESSION['user_id']])) {
        setMessage("Your business card has been deleted successfully.", "success");
    } else {
        setMessage("Failed to delete business card. Please try again.", "error");
    }
    
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ?");
$stmt->execute([$card_id]);
$card = $stmt->fetch();

$custom_fields = json_decode($card['custom_fields'], true);

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

$design = $designs[$card['design_id']] ?? $designs[1];
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[70vh]">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">Delete Business Card</h2>
        <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline whitespace-nowrap">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mb-6">
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Warning</p>
            <p class="text-sm sm:text-base mt-2">You are about to delete the business card "<?php echo $card['card_name']; ?>". This action cannot be undone.</p>
        </div>
        
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-3">Card Details</h3>
            <ul class="list-disc list-inside text-gray-700 text-sm sm:text-base space-y-2">
                <li>Card Name: <?php echo $card['card_name']; ?></li>
                <li>Design: <?php echo $design['name']; ?></li>
                <li>Created: <?php echo date('F j, Y', strtotime($card['created_at'])); ?></li>
                <li>Name on Card: <?php echo $custom_fields['name']; ?></li>
                <li>Job Title: <?php echo $custom_fields['job_title']; ?></li>
                <li>Company: <?php echo $custom_fields['company']; ?></li>
            </ul>
        </div>

        <?php if(!empty($custom_fields['image'])): ?>
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-3">Card Image</h3>
                <div class="w-32 h-32 rounded-lg overflow-hidden">
                    <img src="<?php echo url('uploads/cards/' . $custom_fields['image']); ?>" 
                         alt="Card image" class="w-full h-full object-cover">
                </div>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="?id=<?php echo $card_id; ?>&confirm=yes" 
               class="w-full sm:w-auto text-center bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-1"></i> Delete Card
            </a>
            <a href="/pages/profile/dashboard.php" 
               class="w-full sm:w-auto text-center bg-gray-200 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-300 transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>