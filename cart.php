<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Remove from Cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_book_id'])) {
    $remove_book_id = intval($_POST['remove_book_id']);
    $stmt_remove = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND book_id = ?");
    $stmt_remove->bind_param("ii", $user_id, $remove_book_id);
    $stmt_remove->execute();
    // Optional: Redirect to avoid resubmission on refresh
    header("Location: cart.php");
    exit();
}

// Fetch cart items for this user
$stmt = $conn->prepare("SELECT books.* FROM cart_items JOIN books ON cart_items.book_id = books.id WHERE cart_items.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total items and total amount
$stmt_totals = $conn->prepare("SELECT COUNT(*) AS total_items, IFNULL(SUM(books.price), 0) AS total_amount
                              FROM cart_items 
                              JOIN books ON cart_items.book_id = books.id 
                              WHERE cart_items.user_id = ?");
$stmt_totals->bind_param("i", $user_id);
$stmt_totals->execute();
$result_totals = $stmt_totals->get_result();
$totals = $result_totals->fetch_assoc();

// Get genres from the cart
$genres = array_column($cart_items, 'genre');
$genres = array_unique($genres);
$suggested_books = [];

if (!empty($genres)) {
    $placeholders = implode(',', array_fill(0, count($genres), '?'));
    $types = str_repeat('s', count($genres)) . 'i'; // genre types + user_id
    $params = [...$genres, $user_id];

    $suggestion_query = "SELECT * FROM books WHERE genre IN ($placeholders) 
                         AND id NOT IN (SELECT book_id FROM cart_items WHERE user_id = ?)";

    $stmt = $conn->prepare($suggestion_query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggested_books = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Cart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
 body {
            font-family: 'Inter', sans-serif;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.1);
        }
        .transition-smooth {
            transition: all 0.3s ease-in-out;
        }
        .button-shadow {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }
        .container {
    max-width: 1200px;
    margin: auto;
    padding: 0 20px;
    
  }

   /* Header */
  header {
    background-color: transparent;
    box-shadow: none;
    padding: 15px 0;
  }

  .logo {
    display: flex;
    align-items: center;
  }
  .logo img {
    height: 100px;
    width: auto;
  }

  nav {
    float: right;
  }

  .nav-links {
    list-style: none;
    display: flex;
    gap: 60px; /* Increased gap for more space */
    align-items: center;
  }

  .nav-links a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: color 0.3s ease;
  }

  .nav-links a:hover {
    color: #ff6f61;
  }

  header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }


 /* Dropdown Styles */
  .dropdown {
    position: relative;
  }

  .dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    border: 1px solid #eee;
    padding: 10px 20px;
    list-style: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    min-width: 400px;
    max-width: 600px;
    column-count: 2;
    column-gap: 30px;
    overflow: hidden;
    max-height: 400px;
    overflow-y: auto;
  }

  .dropdown-menu li {
    padding: 6px 0;
  }

  .dropdown-menu li a {
    color: #333;
    text-decoration: none;
    display: block;
    transition: background 0.3s ease;
  }

  .dropdown-menu li a:hover {
    background-color: #f4f4f4;
  }

  /* Hover Effect to Show Dropdown */
  .dropdown:hover .dropdown-menu {
    display: block;
  }

    @media (max-width: 768px) {
    .nav-links {
      flex-direction: column;
      gap: 20px; /* Adjusted gap for mobile */
    }
    .dropdown-menu {
      min-width: 100%;
      column-count: 1; /* Single column on smaller screens */
    }  
  }
  /* Animated gradient background */
.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(-45deg, #e0e7ff, #f3e8ff, #fff1f2, #e0f2fe);
    background-size: 400% 400%;
    z-index: -1;
    animation: gradientFlow 15s ease infinite;
    opacity: 0.3; /* subtle */
}

/* Gradient Animation Keyframes */
@keyframes gradientFlow {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}


  .center-animation {
    display: flex;
    justify-content: center;
    align-items: center;
    
  }

    </style>
</head>
<body class="bg-gray-100 text-gray-900 tracking-tight">
<div class="animated-background"></div>
    
<header>
    <div class="container">
      <div class="logo">
        <img src="https://images.g2crowd.com/uploads/product/image/social_landscape/social_landscape_2ed0526924f72fc1210e53b335fe268c/easy-books.png" alt="EasyBooks Logo">
      </div>
      <nav>
        <ul class="nav-links">
          <li><a href="home.php"><i class="fa-solid fa-house"></i> Home</a></li>
          <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
          <!-- Profile Icon (before Login) -->
          <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a></li>
           <li>
          <a href="logout.php" title="Logout">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>


   <div class="container mx-auto p-6 md:flex md:space-x-8 min-h-screen">
    <div class="flex-1">
        <!-- Main Cart Area -->
        <div class="md:col-span-3">
            <h1 class="text-4xl font-bold mb-10 text-center md:text-left text-gray-800 border-b pb-4 border-gray-300">
    🛒 Your Shopping Cart
</h1>
        <?php if (empty($cart_items)): ?>
          <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <!-- Flex container -->
        <div class="center-animation">
          <lottie-player src="https://lottie.host/9471d59f-68d3-4519-a50a-4c8e165c0926/y5YwOeZ8fz.json" background="transparent" speed="1" style="width: 300px; height: 300px; " loop autoplay>
          </lottie-player>
        </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <?php foreach ($cart_items as $item): ?>
                   <div class="bg-white rounded-2xl shadow-lg p-6 card-hover transition-smooth relative hover:ring-2 hover:ring-indigo-300 h-full flex flex-col">
    <a href="book-detail.php?id=<?= $item['id'] ?>">
        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Book Cover" class="w-full aspect-[2/3] object-cover rounded-md mb-4" />
        <h2 class="text-xl font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($item['title']) ?></h2>
        <p class="text-sm text-gray-600 mt-1 mb-1">by <span class="italic"><?= htmlspecialchars($item['author']) ?></span></p>
        <div class="mt-1">
            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-1 rounded-full"><?= htmlspecialchars($item['genre']) ?></span>
        </div>
        <p class="text-lg font-bold text-green-600 mt-3">Rs. <?= number_format($item['price'], 2) ?></p>
    </a>

    <form method="post" class="mt-auto pt-4">
        <input type="hidden" name="remove_book_id" value="<?= $item['id'] ?>">
        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md button-shadow transition-smooth">
            ❌ Remove from Cart
        </button>
    </form>
</div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <h2 class="text-3xl font-semibold mb-6 text-gray-800 border-b border-gray-300 pb-3 mt-12">📚 You Might Also Like</h2>

        <?php if (!empty($suggested_books)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($suggested_books as $sugg): ?>
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover transition-smooth hover:ring-1 hover:ring-gray-200 h-full flex flex-col">
    <img src="<?= htmlspecialchars($sugg['image_url']) ?>" alt="Book Cover" class="w-full aspect-[2/3] object-cover rounded-lg mb-4" />
    <h3 class="text-lg font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($sugg['title']) ?></h3>
    <p class="text-sm text-gray-600 mt-1">by <span class="italic"><?= htmlspecialchars($sugg['author']) ?></span></p>
    <span class="inline-block mt-2 bg-indigo-100 text-indigo-800 text-xs font-semibold px-3 py-1 rounded-full"><?= htmlspecialchars($sugg['genre']) ?></span>
    <p class="text-base font-bold text-green-600 mt-4">Rs. <?= number_format($sugg['price'], 2) ?></p>
    <a href="book-detail.php?id=<?= $sugg['id'] ?>" class="mt-auto inline-block text-indigo-600 hover:text-indigo-800 font-medium underline pt-4">
        View Details →
    </a>
</div>

                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 text-lg mt-12">Add books to your cart to see suggestions!</p>
        <?php endif; ?>
    </div>
 </div>
    <!-- Sidebar Summary -->
    <aside class="w-full md:w-80 mt-10 md:mt-0 bg-white rounded-2xl shadow-xl p-8 sticky top-6 self-start border border-gray-200">
<h2 class="text-2xl font-bold mb-6 text-indigo-700">Cart Summary</h2>
<div class="space-y-4 text-gray-700">
    <p><strong>Total Items:</strong> <span class="text-indigo-600"><?= $totals['total_items'] ?></span></p>
    <p><strong>Total Amount:</strong> <span class="text-green-600 font-semibold">Rs. <?= number_format($totals['total_amount'], 2) ?></span></p>
</div>
<?php if ($totals['total_items'] > 0): ?>
    <a href="checkout.php"
       class="block mt-8 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white text-center font-bold py-3 rounded-lg transition-smooth button-shadow">
        Proceed to Checkout
    </a>
<?php else: ?>
    <p class="mt-6 text-gray-400 text-sm">Your cart is currently empty.</p>
<?php endif; ?>

</aside>

</div>
 <!-- Footer Section -->
   <!-- Footer -->
  <footer style="background-color:transparent; border-top: 1px solid #eaeaea; padding: 60px 0; font-size: 15px; color: #555;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px;">
    
      <!-- About Section -->
      <div style="flex: 1 1 250px;">
        <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">About BookStore</h3>
        <p style="line-height: 1.7;">EasyBooks is your digital escape into the world of stories, ideas, and imagination. Curated collections for every kind of reader.</p>
      </div>

      <!-- Quick Links -->
      <div style="flex: 1 1 150px;">
       <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Quick Links</h3>
       <ul style="list-style: none; padding: 0;">
        <li><a href="#" style="text-decoration: none; color: #555; display: block; margin-bottom: 10px;">Home</a></li>
        <li><a href="#" style="text-decoration: none; color: #555; display: block; margin-bottom: 10px;">Categories</a></li>
        <li><a href="#" style="text-decoration: none; color: #555; display: block; margin-bottom: 10px;">Contact</a></li>
        <li id="auth-section"></li>

       </ul>
      </div>

      <!-- Contact Info -->
      <div style="flex: 1 1 200px;">
        <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Contact</h3>
        <p style="margin-bottom: 10px;"><i class="fas fa-envelope"></i> support@easybooks.com</p>
        <p style="margin-bottom: 10px;"><i class="fas fa-phone"></i> +91-98765-43210</p>
        <p><i class="fas fa-map-marker-alt"></i> New Delhi, India</p>
      </div>

    <!-- Social Media -->
      <div style="flex: 1 1 180px;">
        <h3 style="color: #333; font-size: 18px; margin-bottom: 15px;">Follow Us</h3>
        <div style="display: flex; gap: 15px;">
          <a href="#" style="color: #555; font-size: 18px;"><i class="fab fa-facebook-f"></i></a>
          <a href="#" style="color: #555; font-size: 18px;"><i class="fab fa-twitter"></i></a>
          <a href="#" style="color: #555; font-size: 18px;"><i class="fab fa-instagram"></i></a>
          <a href="#" style="color: #555; font-size: 18px;"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>

    <div style="text-align: center; margin-top: 40px; border-top: 1px solid #e0e0e0; padding-top: 20px; font-size: 14px; color: #888;">
      &copy; 2025 Easybooks. All rights reserved.
    </div>
  </footer>


</body>
</html>
