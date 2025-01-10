<?php 
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

if (isset($_POST['image_search_btn'])) {
    // Handle Image Search
    $image = $_FILES['search_image']['tmp_name'];

    // Upload the image to your server or cloud storage.
    $image_url = 'path_to_image';  // Save the image temporarily on the server or get a URL

    // Call the function to process the image.
    $api_response = process_image_search($image_url); // This will handle API request
}

function process_image_search($image_url) {
    $api_key = 'YOUR_GOOGLE_VISION_API_KEY'; // Replace with your API Key

    // Prepare the request body for Google Vision API
    $body = json_encode([
        'requests' => [
            [
                'image' => [
                    'content' => base64_encode(file_get_contents($image_url))  // Base64 encode the image
                ],
                'features' => [
                    ['type' => 'LABEL_DETECTION', 'maxResults' => 5],  // Set to 5 labels for now
                ],
            ]
        ]
    ]);

    // Send request to Google Vision API
    $url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $api_key;
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => $body,
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Decode the response from the API
    return json_decode($response);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search page</title>
   
   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="search-form">
   <form action="" method="post" enctype="multipart/form-data">
      <input type="text" name="search_box" placeholder="Search here..." maxlength="100" class="box" required>
      <button type="submit" class="fas fa-search" name="search_btn"></button>
      
      <!-- Image search input -->
      <input type="file" name="search_image" accept="image/*" class="box" required>
      <button type="submit" class="search_by_img" name="image_search_btn">Search</button>
   </form>
</section>

<section class="products" style="padding-top: 0; min-height:100vh;">

   <div class="box-container">

   <?php
     if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
         // Text search logic
         $search_box = $_POST['search_box'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'"); 
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
                // Display text search results
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>TL.</span><?= $fetch_product['price']; ?><span></span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
            }
         }else{
            echo '<p class="empty">No products found!</p>';
         }
     }

     if (isset($api_response)) {
         // Image search logic
         $label = $api_response->responses[0]->labelAnnotations[0]->description; // Get first label

         // Check if label matches any category in the products database
         $select_categories = $conn->prepare("SELECT DISTINCT category FROM `products` WHERE category LIKE '%{$label}%'");
         $select_categories->execute();

         if($select_categories->rowCount() > 0){
            while($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)){
                // Display products based on the category matching the label
                $category = $fetch_category['category'];
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = '{$category}'");
                $select_products->execute();

                if($select_products->rowCount() > 0){
                    while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>TL.</span><?= $fetch_product['price']; ?><span></span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
                    }
                } else {
                    echo '<p class="empty">No products available in this category!</p>';
                }
            }
         } else {
            echo '<p class="empty">No products found for this image category!</p>';
         }
     }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
