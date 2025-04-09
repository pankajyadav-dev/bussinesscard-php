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

$designs = [
    [
        'id' => 1,
        'name' => 'Professional Design',
        'category' => 'Professional',
        'bg_color' => 'bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-blue-500',
        'text' => 'Modern and professional business card design with refined gradients'
    ],
    [
        'id' => 2,
        'name' => 'Creative Design',
        'category' => 'Creative',
        'bg_color' => 'bg-gradient-to-br from-pink-500 via-purple-500 to-purple-600',
        'text_color' => 'text-white',
        'accent_color' => 'bg-pink-400',
        'text' => 'Eye-catching creative design with vibrant color transitions'
    ],
    [
        'id' => 3,
        'name' => 'Minimalist Design',
        'category' => 'Minimalist',
        'bg_color' => 'bg-gradient-to-r from-gray-800 to-gray-900',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-700',
        'text' => 'Clean and elegant minimalist design'
    ],
    [
        'id' => 4,
        'name' => 'Corporate Design',
        'category' => 'Corporate',
        'bg_color' => 'bg-gradient-to-r from-gray-600 to-gray-800',
        'text_color' => 'text-white',
        'accent_color' => 'bg-gray-500',
        'text' => 'Professional corporate design with modern elements'
    ]
];

$categories = [];
foreach($designs as $design) {
    if(!isset($categories[$design['category']])) {
        $categories[$design['category']] = [];
    }
    $categories[$design['category']][] = $design;
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">Choose a Card Design</h2>
        <a href="<?php echo url('pages/profile/dashboard.php'); ?>" class="text-blue-600 hover:underline whitespace-nowrap">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <?php if(count($designs) > 0): ?>
        <?php foreach($categories as $category => $cat_designs): ?>
            <h3 class="text-lg sm:text-xl font-bold mb-4 mt-8"><?php echo $category; ?> Designs</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <?php foreach($cat_designs as $design): ?>
                    <div class="border rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="h-36 sm:h-48 <?php echo $design['bg_color']; ?> flex items-center justify-center p-4 sm:p-6 relative">
                            <div class="absolute top-0 left-0 w-full h-full bg-black opacity-10 pattern-dots"></div>
                            <div class="relative z-10 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                                    <i class="fas fa-user text-2xl text-white/80"></i>
                                </div>
                                <div class="text-left">
                                    <span class="text-lg sm:text-xl font-bold text-white"><?php echo $design['name']; ?></span>
                                    <p class="text-sm text-white/80 mt-1">Sample Card</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <h4 class="font-bold text-lg mb-2"><?php echo $design['name']; ?></h4>
                            <p class="text-gray-600 mb-4 text-sm sm:text-base"><?php echo $design['text']; ?></p>
                            <a href="<?php echo url('pages/cards/create.php?design_id=' . $design['id']); ?>" 
                               class="inline-block w-full sm:w-auto text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                Use This Design
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-8 bg-white rounded-lg shadow">
            <p class="text-gray-600 mb-4">No designs found in the selected category.</p>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Show All Designs
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>