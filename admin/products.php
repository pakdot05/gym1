<?php
require('inc/essentials.php');

$db_host = "localhost";
$db_user = "root";  
$db_pass = "";  // Use your actual password
$db_name = "gymko";  // Use your actual database name
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

adminLogin();

// Add product functionality
if(isset($_POST['add_product'])){
   $name = $_POST['name'];
   $price = $_POST['price'];
   $quantity = $_POST['quantity'];
   $unit = $_POST['unit']; // Get the unit from the form

   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../images/'.$image;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = 'Product name already exists!';
   }else{
      if($image_size > 2000000){
         $message[] = 'Image size is too large';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
         $insert_product = $conn->prepare("INSERT INTO `products`(name, quantity, price, unit, image) VALUES(?,?,?,?,?)"); // Add unit to the query
         if($insert_product->execute([$name, $quantity, $price, $unit, $image])){ // Pass unit to the query
            $message[] = 'NEW PRODUCT ADDED!!';
        } else {
            $message[] = 'Failed to add product!';
        }
   }
}
}


// Delete product functionality
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../images/'.$fetch_delete_image['image']);

   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);

   header('location:products.php');
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Product</title>
   <?php require('inc/links.php');?>
</head>
<body class="bg-light">
<?php require('inc/header.php');?>


<div class="container-fluid" id="main-content">
    <div class="row">
        <div class="col-lg-10 ms-auto p-4">
            <h3 class="mb-4">Manage Products</h3>

            <!-- Add/Update Product Section -->
     <!-- Add/Update Product Section -->
<section class="add-products">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        Add Product
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">
                        <span id="modalTitle">Add Product</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data" id="addProductForm">
                        <div class="mb-3">
                            <label for="nameInput" class="form-label">Product Name:</label>
                            <input type="text" required placeholder="Product name" name="name" maxlength="100" class="form-control" id="nameInput" value="">
                        </div>
                        <div class="mb-3">
                            <label for="priceInput" class="form-label">Product Price:</label>
                            <input type="number" min="0" max="9999999999" required placeholder="Product Price ₱" name="price" class="form-control" id="priceInput" value="">
                        </div>
                        <div class="mb-3">
                            <label for="quantityInput" class="form-label">Quantity:</label>
                            <input type="number" min="1" required placeholder="Quantity" name="quantity" class="form-control" id="quantityInput" value="">
                        </div>
                        <div class="mb-3">
                            <label for="unitInput" class="form-label">Unit:</label>
                            <input type="text" required placeholder="Unit (e.g., 100mg, 100g)" name="unit" class="form-control" id="unitInput" value="">
                        </div>
                        <div class="mb-3">
                            <label for="imageInput" class="form-label">Image:</label>
                            <input type="file" name="image" class="form-control" accept="image/jpg, image/jpeg, image/png" id="imageInput">
                        </div>
                        <div class="mb-3">
                            <label for="imagePreview" class="form-label">Image Preview:</label>
                            <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px;">
                        </div>
                        <input type="submit" value="Add Product" class="btn btn-dark" id="submitBtn" name="add_product">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProductModalLabel">
                    <span id="modalTitle">Update Product</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data" id="updateProductForm">
                    <input type="hidden" name="update_id" id="updateId" value="">
                    <div class="mb-3">
                        <label for="nameInput" class="form-label">Product Name:</label>
                        <input type="text" required placeholder="Product name" name="name" maxlength="100" class="form-control" id="nameInput" value="">
                    </div>
                    <div class="mb-3">
                        <label for="priceInput" class="form-label">Product Price:</label>
                        <input type="number" min="0" max="9999999999" required placeholder="Product Price ₱" name="price" class="form-control" id="priceInput" value="">
                    </div>
                    <div class="mb-3">
                        <label for="quantityInput" class="form-label">Quantity:</label>
                        <input type="number" min="1" required placeholder="Quantity" name="quantity" class="form-control" id="quantityInput" value="">
                    </div>
                    <div class="mb-3">
                        <label for="unitInput" class="form-label">Unit:</label>
                        <input type="text" required placeholder="Unit (e.g., 100mg, 100g)" name="unit" class="form-control" id="unitInput" value="">
                    </div>
                    <div class="mb-3">
                        <label for="imageInput" class="form-label">Image:</label>
                        <input type="file" name="image" class="form-control" accept="image/jpg, image/jpeg, image/png" id="imageInput">
                    </div>
                    <div class="mb-3">
                        <label for="imagePreview" class="form-label">Image Preview:</label>
                        <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px;">
                    </div>
                    <input type="submit" value="Update Product" class="btn btn-dark" id="submitBtn" name="update_product">
                </form>
            </div>
        </div>
    </div>
</div>
</section>

            <section class="search-section my-4">
            <form action="" method="get" class="search-form">
            <input type="text" id="searchInput" name="search" placeholder="Search..." class="form-control shadow-none w-25 ms-auto" >
            </form>
            </section>
            <section class="show-products">
               <div class="table-responsive">
                  <table class="table table-hover">
                     <thead class="bg-dark text-white">
                        <tr>
                           <th scope="col">#</th>
                           <th scope="col">Name</th>
                           <th scope="col">Quantity</th>
                           <th scope="col">Price</th>
                           <th scope="col">Unit</th>
                           <th scope="col">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
                           if (isset($_GET['search'])) {
                              $search = $_GET['search'];
                              $select_products = $conn->prepare("SELECT * FROM `products` WHERE `name` LIKE :search");
                              $select_products->bindValue(':search', "%$search%", PDO::PARAM_STR);
                           } else {
                              $select_products = $conn->prepare("SELECT * FROM `products`");
                           }

                           $select_products->execute();
                           $productsCount = $select_products->rowCount();

                           if ($productsCount > 0) {
                              while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tr>
                           <td><?= $fetch_products['id']; ?></td>
                           <td><?= $fetch_products['name']; ?></td>
                           <td><?= $fetch_products['quantity']; ?></td>
                           <td>₱<?= $fetch_products['price']; ?></td>
                           <td><?= $fetch_products['unit']; ?></td> 
                           <td>
                              <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateProductModal" 
                                 data-product-id="<?= $fetch_products['id']; ?>"
                                 data-product-name="<?= $fetch_products['name']; ?>"
                                 data-product-price="<?= $fetch_products['price']; ?>"
                                 data-product-quantity="<?= $fetch_products['quantity']; ?>"
                                 data-product-unit="<?= $fetch_products['unit']; ?>"
                                 data-product-image="<?= $fetch_products['image']; ?>">Edit</a>
                              <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                           </td>
                        </tr>
                        <?php
                              }
                           } else {
                              echo '<tr><td colspan="6" class="text-center">No Products Found!</td></tr>';
                           }
                        ?>
                     </tbody>
                  </table>
               </div>
            </section>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchInput = document.getElementById('searchInput');
const boxContainer = document.querySelector('.table');
const emptyMessage = document.querySelector('.empty');

searchInput.addEventListener('input', searchProducts);

function searchProducts() {
  const filter = searchInput.value.toUpperCase();
  const boxes = boxContainer.getElementsByTagName('tr');

  let productsFound = false;

  for (let i = 1; i < boxes.length; i++) {
    const name = boxes[i].getElementsByTagName('td')[1].textContent.toUpperCase();
    const price = boxes[i].getElementsByTagName('td')[3].textContent.toUpperCase();

    if (name.includes(filter) || price.includes(filter)) {
      boxes[i].style.display = '';
      productsFound = true;
    } else {
      boxes[i].style.display = 'none';
    }
  }

  if (productsFound) {
    emptyMessage.style.display = 'none';
  } else {
    emptyMessage.style.display = 'block';
  }
}

// Modal Initialization
const addProductModal = document.getElementById('addProductModal');
const modalTitle = document.getElementById('modalTitle');
const nameInput = document.getElementById('nameInput');
const priceInput = document.getElementById('priceInput');
const quantityInput = document.getElementById('quantityInput');
const unitInput = document.getElementById('unitInput'); // Fixed duplicate variable
const imageInput = document.getElementById('imageInput');
const submitBtn = document.getElementById('submitBtn');
const saveBtn = document.getElementById('saveBtn');

// Handle Add Button Click
document.getElementById('addFoodBtn').addEventListener('click', () => {
  modalTitle.innerText = 'Add Product';
  nameInput.value = '';
  priceInput.value = '';
  quantityInput.value = '';
  unitInput.value = '';
  imageInput.value = ''; // Clear image input
  submitBtn.value = 'Add Product';
  saveBtn.innerText = 'Save Changes';

  addProductModal.show();
});

const imagePreview = document.getElementById('imagePreview');
document.getElementById('imageInput').addEventListener('change', function(event) {
    const imagePreview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        imagePreview.src = URL.createObjectURL(file);
        imagePreview.style.display = 'block';
    }
});
const addProductForm = document.getElementById('addProductForm');

addProductForm.addEventListener('submit', (event) => {
  event.preventDefault();

  const name = nameInput.value;
  const price = priceInput.value;
  const quantity = quantityInput.value;
  const unit = unitInput.value;
  const image = imageInput.files[0]; // Handle image file

  let formData = new FormData();
  formData.append('name', name);
  formData.append('price', price);
  formData.append('quantity', quantity);
  formData.append('unit', unit);
  if (image) {
    formData.append('image', image); // Append image only if selected
  }
  formData.append('action', 'add_product');

  $.ajax({
    url: 'products.php', // URL of your PHP file for handling the request
    type: 'POST',
    data: formData,
    contentType: false, // Don't set contentType
    processData: false, // Don't process the data
    success: function (response) {
      console.log(response); // Log server response for debugging
      window.location.href = 'products.php'; // Redirect after success
    },
    error: function (error) {
      console.error(error); // Handle any errors
      // Display an error message to the user
    }
  });
});
document.querySelectorAll('a[data-bs-target="#updateProductModal"]').forEach(button => {
    button.addEventListener('click', function() {
        // Get product data from data-* attributes
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');
        const productPrice = this.getAttribute('data-product-price');
        const productQuantity = this.getAttribute('data-product-quantity');
        const productUnit = this.getAttribute('data-product-unit');
        const productImage = this.getAttribute('data-product-image');

        // Set values in modal input fields
        document.getElementById('updateId').value = productId;
        document.getElementById('nameInput').value = productName;
        document.getElementById('priceInput').value = productPrice;
        document.getElementById('quantityInput').value = productQuantity;
        document.getElementById('unitInput').value = productUnit;

        // Set the image preview (if required)
        if (productImage) {
            document.getElementById('imagePreview').src = `../images/${productImage}`;
        } else {
            document.getElementById('imagePreview').src = '';
        }
    });
});
</script>
<?php require('inc/scripts.php'); ?>
</body>
</html>