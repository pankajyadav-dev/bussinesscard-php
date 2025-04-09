<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Card Creator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/style.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="<?php echo BASE_URL; ?>/" class="text-lg sm:text-xl lg:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent hover:from-blue-700 hover:to-blue-900 transition-all">
                    Business Card Creator
                </a>
                
                <button id="mobile-menu-button" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none p-2">
                    <i class="fas fa-bars text-xl sm:text-2xl"></i>
                </button>
                
                <div class="hidden lg:flex items-center space-x-2 sm:space-x-4 lg:space-x-6">
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Card Designs</a>
                        <a href="<?php echo url('pages/profile/dashboard.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">My Cards</a>
                        <a href="<?php echo url('pages/profile/account.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Profile</a>
                        <a href="<?php echo url('pages/auth/logout.php'); ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo url('pages/auth/login.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Login</a>
                        <a href="<?php echo url('pages/auth/register.php'); ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="mobile-menu" class="hidden lg:hidden mt-4 pb-2">
                <div class="flex flex-col space-y-2">
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded hover:bg-gray-50">Card Designs</a>
                        <a href="<?php echo url('pages/profile/dashboard.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded hover:bg-gray-50">My Cards</a>
                        <a href="<?php echo url('pages/profile/account.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded hover:bg-gray-50">Profile</a>
                        <a href="<?php echo url('pages/auth/logout.php'); ?>" class="px-3 py-2 text-red-600 hover:text-red-700 transition-colors rounded hover:bg-red-50">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo url('pages/auth/login.php'); ?>" class="px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded hover:bg-gray-50">Login</a>
                        <a href="<?php echo url('pages/auth/register.php'); ?>" class="px-3 py-2 text-blue-600 hover:text-blue-700 transition-colors rounded hover:bg-blue-50">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-4 rounded text-sm sm:text-base <?php echo $_SESSION['message_type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>
