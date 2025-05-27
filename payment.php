<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "Order ID missing.";
    exit;
}

$orderId = intval($_GET['order_id']);
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment'])) {
        $paymentType = $_POST['payment'];

        // Update the order in the database
        $stmt = $conn->prepare("UPDATE orders SET payment_type = ?, is_order_placed_successfully = 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $paymentType, $orderId, $userId);

        if ($stmt->execute()) {
    $_SESSION['success_message'] = "✅ Order Placed Successfully!";
    header("Location: " . $_SERVER['PHP_SELF'] . "?order_id=" . $orderId);
    exit;
}
else {
         //   echo "Error updating order: " . $conn->error;
        }
    } else {
        echo "Please select a payment method.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Payment Method</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f9;
      color: #fff;
      margin: 0;
      padding: 0;
    }

    h1 {
      text-align: center;
      background: #2c2c3e;
      padding: 1.5rem;
      font-size: 1.8rem;
      font-weight: bold;
      color: #ffc107;
    }

    .payment-container {
      max-width: 700px;
      margin: 2rem auto;
      background: #2c2c3e;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: #ffc107;
    }

    .payment-option {
      margin-bottom: 2rem;
    }

    .payment-row {
      display: flex;
      align-items: center;
      margin-bottom: 1.2rem;
      padding: 0.5rem;
      border-radius: 8px;
      background-color: #3e3e4e;
      transition: background 0.3s;
    }

    .payment-row:hover {
      background-color: #505066;
    }

    .payment-row input {
      margin-right: 1rem;
      transform: scale(1.3);
      accent-color: #28a745;
    }

    .payment-row label {
      font-size: 1.1rem;
      cursor: pointer;
    }

    .payment-icons {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
      padding-left: 2rem;
    }

    .payment-icons img {
      width: 60px;
      height: auto;
      border-radius: 8px;
      background: #fff;
      padding: 5px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .place-order-btn {
      width: 100%;
      padding: 1rem;
      background: #007bff; /* Blue button for primary action */
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 2rem;
    }

    .place-order-btn:hover {
      background: #0056b3; /* Darker blue on hover */
    }

    .success {
      text-align: center;
      margin-top: 1.5rem;
      color: #28a745;
      font-weight: bold;
      
    }

    a {
      text-decoration: none; /* Removes underline from all anchor tags */
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

  header .container1 {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  

    @media (max-width: 768px) {
    .nav-links {
      flex-direction: column;
      gap: 20px; /* Adjusted gap for mobile */
    }
  
  }
      .container1 {
    max-width: 1000px;
    margin: auto;
    padding: 0 20px;
  }

      /* Header */
  header {
    background-color:transparent;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 15px 0;
  }
  </style>
</head>
<body>
  <header>
    <div class="container1">
      <div class="logo">
        <img src="https://images.g2crowd.com/uploads/product/image/social_landscape/social_landscape_2ed0526924f72fc1210e53b335fe268c/easy-books.png" alt="EasyBooks Logo">
      </div>
      <nav>
        <ul class="nav-links">
          <li><a href="home.php">Home</a></li>
          <li><a href="#">Contact</a></li>
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

<h1>🛒 Payment Method</h1>

<div class="payment-container">
  <h2>Select a Payment Method</h2>
  <form method="POST" id="paymentForm">
    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>" />
    <div class="payment-option">
      <div class="payment-row">
        <input type="radio" id="upi" name="payment" value="upi" required />
        <label for="upi">UPI Apps</label>
      </div>
      <div class="payment-icons">
        <img src="https://th.bing.com/th/id/OIP.Ue40NKqi8Lfd0lYYxoblrQHaD4?rs=1&pid=ImgDetMain" alt="GPay" title="GPay">
        <img src="https://d3pc1xvrcw35tl.cloudfront.net/images/1200x900/payment-app-phone-pay-has-launched-an-aggregator-service-know-here-everything_2023061030257.jpg" alt="PhonePe" title="PhonePe">
        <img src="https://d1.awsstatic.com/Paytm-Logo.516dcbea24a48dc1f0187700fbd0f6a48f9a18c3.png" alt="Paytm" title="Paytm">
      </div>
    </div>

    <div class="payment-row">
      <input type="radio" id="card" name="payment" value="card" />
      <label for="card">Credit / Debit Card</label>
    </div>

    <div class="payment-row">
      <input type="radio" id="netbanking" name="payment" value="netbanking" />
      <label for="netbanking">Net Banking</label>
    </div>

    <div class="payment-row">
      <input type="radio" id="cod" name="payment" value="cod" />
      <label for="cod">Cash on Delivery / Pay on Delivery</label>
    </div>

    <button type="submit" class="place-order-btn">Place Order</button>
    <?php if (isset($_SESSION['success_message'])): ?>
  <div class="success" id="successMessage"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

  </form>
</div>

<!-- <script>
  document.getElementById("paymentForm").addEventListener("submit", function(e) {
  //  e.preventDefault();
    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    if (selectedPayment) {
      document.getElementById("successMessage").style.display = "block";
      console.log("Payment Method Selected:", selectedPayment.value);
    } else {
      alert("Please select a payment method.");
    }
  });
</script> -->
 <footer style="background-color: #f8f8f8; border-top: 1px solid #eaeaea; padding: 60px 0; font-size: 15px; color: #555;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px;   padding-left: 100px; ">
    
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
