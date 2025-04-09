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

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT * FROM user_cards WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cards = $stmt->fetchAll();

$designs = [
    1 => [
        'name' => 'Professional Design',
        'category' => 'Professional',
        'bg_color' => 'bg-gradient-to-r from-blue-600 to-blue-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-blue-500',
        'text' => 'Modern and professional business card design with gradient accents',
        'card_shadow' => 'shadow-blue-200'
    ],
    2 => [
        'name' => 'Creative Design',
        'category' => 'Creative',
        'bg_color' => 'bg-gradient-to-r from-pink-500 to-purple-600',
        'text_color' => 'text-white',
        'accent_color' => 'bg-pink-400',
        'text' => 'Eye-catching creative design with vibrant colors',
        'card_shadow' => 'shadow-pink-200'
    ],
    3 => [
        'name' => 'Minimalist Design',
        'category' => 'Minimalist',
        'bg_color' => 'bg-gradient-to-r from-gray-800 to-gray-900',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-700',
        'text' => 'Clean and elegant minimalist design',
        'card_shadow' => 'shadow-gray-200'
    ],
    4 => [
        'name' => 'Corporate Design',
        'category' => 'Corporate',
        'bg_color' => 'bg-gradient-to-r from-gray-600 to-gray-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-500',
        'text' => 'Professional corporate design with modern elements',
        'card_shadow' => 'shadow-gray-200'
    ]
];
?>

<div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold">Welcome, <?php echo $user['name']; ?>!</h2>
            <p class="text-gray-600">Manage your business cards</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">Create New Card</a>
            <a href="<?php echo url('pages/profile/account.php'); ?>" class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition-colors">Edit Profile</a>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-4 sm:p-6 min-h-[63vh]">
    <h3 class="text-xl font-bold mb-4">Your Business Cards</h3>
    
    <?php if(count($cards) > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <?php foreach($cards as $card): ?>
                <?php 
                $design = $designs[$card['design_id']] ;
                $custom_fields = json_decode($card['custom_fields'], true);
                ?>
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                    <div class="p-3 sm:p-4">
                        <div class="w-full mx-auto">
                            <div id="business-card-<?php echo $card['id']; ?>" class="border rounded-xl overflow-hidden shadow-2xl <?php echo $design['card_shadow']; ?> transform hover:scale-105 transition-transform duration-300">
                                <div class="h-56 <?php echo $design['bg_color']; ?> flex flex-col items-center justify-center p-6 <?php echo $design['text_color']; ?> relative">
                                    <div class="absolute top-0 left-0 w-full h-full bg-black opacity-10 pattern-dots"></div>
                                    <div class="relative z-10 w-full max-w-md flex items-center gap-4">
                                        <?php if(!empty($custom_fields['image'])): ?>
                                            <div class="w-24 h-24 rounded-full overflow-hidden flex-shrink-0 border-2 border-white">
                                                <img src="<?php echo url('uploads/cards/' . $custom_fields['image']); ?>" 
                                                     alt="Profile" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow text-left">
                                            <h2 class="text-2xl font-bold mb-2 truncate"><?php echo $custom_fields['name']; ?></h2>
                                            <?php if(!empty($custom_fields['job_title'])): ?>
                                                <p class="text-lg opacity-90 mb-1 truncate"><?php echo $custom_fields['job_title']; ?></p>
                                            <?php endif; ?>
                                            <?php if(!empty($custom_fields['company'])): ?>
                                                <p class="text-md opacity-80 truncate"><?php echo $custom_fields['company']; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-6 bg-white">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php if(!empty($custom_fields['phone'])): ?>
                                            <a href="tel:<?php echo $custom_fields['phone']; ?>" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                                <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3 flex-shrink-0">
                                                    <i class="fas fa-phone"></i>
                                                </div>
                                                <span class="text-gray-700 truncate group-hover:text-clip"><?php echo $custom_fields['phone']; ?></span>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($custom_fields['email'])): ?>
                                            <a href="mailto:<?php echo $custom_fields['email']; ?>" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                                <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3 flex-shrink-0">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <span class="text-gray-700 truncate group-hover:text-clip"><?php echo $custom_fields['email']; ?></span>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($custom_fields['website'])): ?>
                                            <a href="<?php echo $custom_fields['website']; ?>" target="_blank" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                                <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3 flex-shrink-0">
                                                    <i class="fas fa-globe"></i>
                                                </div>
                                                <span class="text-gray-700 truncate group-hover:text-clip"><?php echo $custom_fields['website']; ?></span>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($custom_fields['address'])): ?>
                                            <div class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors group">
                                                <div class="w-10 h-10 <?php echo $design['accent_color']; ?> rounded-full flex items-center justify-center <?php echo $design['text_color']; ?> mr-3 flex-shrink-0">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <span class="text-gray-700 truncate group-hover:text-clip"><?php echo $custom_fields['address']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold mb-2"><?php echo $card['card_name']; ?></h4>
                            <div class="flex gap-2 mb-4">
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                    <?php echo $design['name']; ?>
                                </span>
                                <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                                    Created <?php echo date('M j, Y', strtotime($card['created_at'])); ?>
                                </span>
                            </div>
                            
                            <div class="flex gap-2">
                                
                                <a href="/pages/cards/view.php?id=<?php echo $card['id']; ?>" 
                                   class="flex-1 inline-block text-center bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="/pages/cards/edit.php?id=<?php echo $card['id']; ?>" 
                                   class="flex-1 inline-block text-center bg-gray-100 text-gray-700 px-3 py-2 rounded hover:bg-gray-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <a href="/pages/cards/delete.php?id=<?php echo $card['id']; ?>" 
                                   class="inline-block text-center bg-red-100 text-red-600 w-10 py-2 rounded hover:bg-red-200">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8 ">
            <img src="image.png" alt="No cards" class="w-64 mx-auto mb-4 opacity-50">
            <p class="text-gray-600 mb-4">You haven't created any business cards yet.</p>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Your First Card</a>
        </div>
    <?php endif; ?>
</div>



<?php require_once '../../includes/footer.php'; ?>