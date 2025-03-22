<?php 
$base_url = './';
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'config/config.php';
?>

<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 sm:py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 sm:mb-6 animate__animated animate__fadeIn">Create Professional Business Cards</h1>
        <p class="text-xl sm:text-2xl mb-8 sm:mb-10 animate__animated animate__fadeIn animate__delay-1s">Design, customize, and share your business cards in minutes</p>
        
        <?php if(!isLoggedIn()): ?>
            <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6">
                <a href="<?php echo url('pages/auth/register.php'); ?>" class="w-full sm:w-auto bg-white text-blue-600 px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">Get Started</a>
                <a href="<?php echo url('pages/auth/login.php'); ?>" class="w-full sm:w-auto bg-transparent border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transform hover:scale-105 transition-all duration-300">Log In</a>
            </div>
        <?php else: ?>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transform hover:scale-105 transition-all duration-300 shadow-lg">Create New Card</a>
        <?php endif; ?>
    </div>
</div>

<div class="py-12 sm:py-20 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12 sm:mb-16">How It Works</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-12">
            <div class="text-center transform hover:scale-105 transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-100 to-blue-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-user-plus text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Create an Account</h3>
                <p class="text-gray-600 text-lg">Sign up and verify your email to get started with our business card creator.</p>
            </div>
            
            <div class="text-center transform hover:scale-105 transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-100 to-blue-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-pencil-alt text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Design Your Card</h3>
                <p class="text-gray-600 text-lg">Choose from various templates and customize your business card with your information.</p>
            </div>
            
            <div class="text-center transform hover:scale-105 transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-100 to-blue-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="fas fa-share-alt text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Share with Everyone</h3>
                <p class="text-gray-600 text-lg">Share your business card via email or QR code to connect with others.</p>
            </div>
        </div>
    </div>
</div>

<div class="py-20 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-16">Our Card Designs</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="h-56 bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center p-6">
                    <span class="text-white text-2xl font-bold">Professional Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Professional Templates</h3>
                    <p class="text-gray-600 mb-4">Perfect for corporate environments and formal business settings.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Professional'); ?>" class="text-blue-600 font-bold hover:underline">Browse Professional</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="h-56 bg-gradient-to-r from-pink-400 to-pink-600 flex items-center justify-center p-6">
                    <span class="text-white text-2xl font-bold">Creative Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Creative Templates</h3>
                    <p class="text-gray-600 mb-4">Stand out with unique and eye-catching business card designs.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Creative'); ?>" class="text-blue-600 font-bold hover:underline">Browse Creative</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <div class="h-56 bg-gradient-to-r from-gray-700 to-gray-900 flex items-center justify-center p-6">
                    <span class="text-white text-2xl font-bold">Minimalist Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Minimalist Templates</h3>
                    <p class="text-gray-600 mb-4">Clean and simple designs that focus on essential information.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Minimalist'); ?>" class="text-blue-600 font-bold hover:underline">Browse Minimalist</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if(!isLoggedIn()): ?>
            <div class="text-center mt-16">
                <a href="pages/auth/register.php" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-4 rounded-full font-bold hover:opacity-90 transform hover:scale-105 transition-all duration-300 shadow-lg">Get Started Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>