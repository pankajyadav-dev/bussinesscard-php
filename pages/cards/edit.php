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

$stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ?");
$stmt->execute([$card_id]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card = $stmt->fetch();

if($card['user_id'] != $_SESSION['user_id']) {
    setMessage("You don't have permission to edit this card.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

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
    
    if(empty($card_name)) {
        $errors[] = "Card name is required";
    }
    
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
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
            
            if(!empty($custom_fields['image'])) {
                $old_image = "../../uploads/cards/" . $custom_fields['image'];
                if(file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            
        
            $image_path = 'card_' . time() . '.' . $imageFileType;
            $target_file = $target_dir . $image_path;
            
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors[] = "Sorry, there was an error uploading your file.";
            } else {
                $custom_fields['image'] = $image_path;
            }
        }
    }
    
    if(empty($errors)) {
        $updated_custom_fields = json_encode([
            'name' => $name,
            'job_title' => $job_title,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'address' => $address,
            'image' => $custom_fields['image'] ?? null
        ]);
        
        $stmt = $pdo->prepare("
            UPDATE user_cards 
            SET card_name = ?, custom_fields = ? 
            WHERE id = ? AND user_id = ?
        ");
        
        if($stmt->execute([$card_name, $updated_custom_fields, $card_id, $_SESSION['user_id']])) {
            setMessage("Your business card has been updated successfully.", "success");
            header("Location: /pages/cards/view.php?id=" . $card_id);
            exit;
        } else {
            $errors[] = "Failed to update business card. Please try again.";
        }
    }
}
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-xl sm:text-2xl font-bold">Edit Business Card</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/pages/cards/view.php?id=<?php echo $card_id; ?>" class="text-blue-600 hover:underline whitespace-nowrap">
                <i class="fas fa-arrow-left mr-1"></i> Back to Card
            </a>
            <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline whitespace-nowrap">
                <i class="fas fa-th-large mr-1"></i> Dashboard
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-4 sm:p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="w-full lg:w-1/3">
                <h3 class="text-lg sm:text-xl font-bold mb-4">Card Design</h3>
                <div class="border rounded-lg overflow-hidden shadow-md mb-4">
                    <div class="h-48 <?php echo $design['bg_color']; ?> flex items-center justify-center">
                        <span class="text-white text-lg font-bold"><?php echo $design['name']; ?></span>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-lg"><?php echo $design['name']; ?></h4>
                        <p class="text-gray-600">Category: <?php echo ucfirst(strtolower(str_replace(' Design', '', $design['name']))); ?></p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm">
                    <i class="fas fa-info-circle mr-1"></i> To change the card design, create a new card with your preferred design.
                </p>
            </div>
            
            <div class="w-full lg:w-2/3">
                <h3 class="text-lg sm:text-xl font-bold mb-4">Card Information</h3>
                
                <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                        <ul class="list-disc list-inside">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $card_id); ?>" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="card_name" class="block text-gray-700 font-medium mb-2">Card Name <span class="text-red-500">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $card['card_name']; ?>" required>
                        <p class="text-gray-500 text-sm mt-1">This name is for your reference only</p>
                    </div>
                    
                    <hr class="my-6">
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['name']; ?>" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                            <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['job_title']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['company']; ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['phone']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                        <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['website']; ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"><?php echo $custom_fields['address']; ?></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label for="image" class="block text-gray-700 font-medium mb-2">Profile Image</label>
                        <?php if(!empty($custom_fields['image'])): ?>
                            <div class="mb-3">
                                <img src="<?php echo url('uploads/cards/' . $custom_fields['image']); ?>" 
                                     alt="Current profile image" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-gray-500 text-sm mt-1">Maximum file size: 3MB. Allowed formats: JPG, PNG, GIF</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700">
                            Update Business Card
                        </button>
                        <a href="/pages/cards/view.php?id=<?php echo $card_id; ?>" 
                           class="w-full sm:w-auto text-center bg-gray-200 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-300">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>