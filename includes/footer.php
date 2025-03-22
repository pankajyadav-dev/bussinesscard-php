</main>
    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-center md:text-left">
                    <h3 class="text-lg sm:text-xl font-bold">Business Card Creator</h3>
                    <p class="text-gray-400 text-sm sm:text-base">Create professional business cards in minutes</p>
                </div>
                
                <div class="flex space-x-4 sm:space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-lg sm:text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter text-lg sm:text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-lg sm:text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin-in text-lg sm:text-xl"></i>
                    </a>
                </div>
            </div>
            
            <hr class="border-gray-700 my-4 sm:my-6">
            
            <div class="text-center text-gray-400 text-sm sm:text-base">
                <p>&copy; <?php echo date('Y'); ?> Business Card Creator. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="<?php echo JS_PATH; ?>/main.js"></script>
    <script>
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const isHidden = mobileMenu.classList.contains('hidden');
    
    if (isHidden) {
        mobileMenu.classList.remove('hidden');
        this.innerHTML = '<i class="fas fa-times text-2xl"></i>';
    } else {
        mobileMenu.classList.add('hidden');
        this.innerHTML = '<i class="fas fa-bars text-2xl"></i>';
    }
});
</script>
</body>
</html>