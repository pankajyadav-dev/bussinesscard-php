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

if(!isset($_GET['design_id']) || empty($_GET['design_id'])) {
    setMessage("Please select a design first.", "error");
    header("Location: " . BASE_URL . "pages/cards/designs.php");
    exit;
}

$design_id = (int)$_GET['design_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
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

$selected_design = $designs[$design_id];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_name = sanitizeInput($_POST['card_name']);
    $name = sanitizeInput($_POST['name']);
    $job_title = sanitizeInput($_POST['job_title']);
    $company = sanitizeInput($_POST['company']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $website = sanitizeInput($_POST['website']);
    $address = sanitizeInput($_POST['address']);
    
    $errors = [];
    $image_path = '';
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../uploads/cards/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        
        if($check === false) {
            $errors[] = "File is not an image.";
        } elseif ($_FILES["image"]["size"] > 3000000) {
            $errors[] = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
        if(empty($errors)) {
            
            $image_path = 'card_' . time() . '.' . $imageFileType;
            $target_file = $target_dir . $image_path;
            
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    if(empty($card_name)) {
        $errors[] = "Card name is required";
    }
    
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    if(empty($errors)) {
        $custom_fields = json_encode([
            'name' => $name,
            'job_title' => $job_title,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'address' => $address,
            'image' => $image_path
        ]);
        
        $stmt = $pdo->prepare("
            INSERT INTO user_cards (user_id, design_id, card_name, custom_fields) 
            VALUES (?, ?, ?, ?)
        ");
        
        if($stmt->execute([$_SESSION['user_id'], $design_id, $card_name, $custom_fields])) {
            $card_id = $pdo->lastInsertId();
            setMessage("Your business card has been created successfully.", "success");
            header("Location: " . BASE_URL . "pages/cards/view.php?id=" . $card_id);
            exit;
        } else {
            $errors[] = "Failed to create business card. Please try again.";
        }
    }
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">Create New Business Card</h2>
        <a href="<?php echo url('pages/cards/designs.php'); ?>" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Designs
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="w-full lg:w-1/3">
                <h3 class="text-xl font-bold mb-4">Selected Design</h3>
                <div class="border rounded-xl overflow-hidden shadow-lg <?php echo $selected_design['card_shadow']; ?>">
                    <div class="h-48 <?php echo $selected_design['bg_color']; ?> flex items-center justify-center relative">
                        <div class="absolute top-0 left-0 w-full h-full bg-black opacity-10 pattern-dots"></div>
                        <div class="relative z-10 text-center <?php echo $selected_design['text_color']; ?>">
                            <span class="text-xl font-bold"><?php echo $selected_design['name']; ?></span>
                            <p class="text-sm opacity-80 mt-2">Preview your card design</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-lg"><?php echo $selected_design['name']; ?></h4>
                        <p class="text-gray-600">Premium business card template</p>
                    </div>
                </div>
            </div>
            
            <div class="w-full lg:w-2/3">
                <h3 class="text-xl font-bold mb-4">Card Information</h3>
                
                <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                        <ul class="list-disc list-inside">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?design_id=' . $design_id); ?>" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="card_name" class="block text-gray-700 font-medium mb-2">Card Name <span class="text-red-500">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($card_name) ? $card_name : 'My Business Card'; ?>" required>
                        <p class="text-gray-500 text-sm mt-1">This name is for your reference only</p>
                    </div>
                    
                    <hr class="my-6">
                    
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="image" class="block text-gray-700 font-medium mb-2">Profile Image</label>
                            <input type="file" id="image" name="image" accept="image/jpeg, image/png, gif" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   onchange="previewImage(this);">
                            <p class="text-gray-500 text-sm mt-1">Maximum file size: 3MB. Allowed formats: JPG, PNG, GIF</p>
                            <div id="imagePreview" class="mt-4 w-32 h-32 rounded-full overflow-hidden hidden">
                                <img id="preview" src="#" alt="Preview" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="<?php echo isset($name) ? $name : $user['name']; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                                <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="<?php echo isset($job_title) ? $job_title : $user['job_title']; ?>">
                            </div>

                            <div class="mb-4">
                                <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                                <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="<?php echo isset($company) ? $company : $user['company']; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($phone) ? $phone : $user['phone']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($email) ? $email : $user['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                        <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($website) ? $website : $user['website']; ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"><?php echo isset($address) ? $address : $user['address']; ?></textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Business Card</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>