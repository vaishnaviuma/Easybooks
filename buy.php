<?php
session_start();
include 'db.php'; // your DB connection setup

// Check user login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = $_SESSION['user_id'];

// Get buyer info from allusers table
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM allusers WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$buyer = $userResult->fetch_assoc();

// Get book info by id from GET param
$bookId = $_GET['id'] ?? null;
if (!$bookId) {
  die("Book ID not provided");
}
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$bookResult = $stmt->get_result();
$book = $bookResult->fetch_assoc();

if (!$book) {
  die("Book not found");
}

// Get buyer info from allusers table
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM allusers WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$buyer = $userResult->fetch_assoc();

// If no buyer found, initialize as empty array
if (!$buyer) {
    $buyer = [
        'name' => '',
        'email' => '',
        'phone_no' => '',
        'address' => ''
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buy Book - Checkout</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

   <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f4f9; /* Light background for a professional look */
      color: #333; /* Darker text for better readability */
    }

      /* Header */
  header {
    background-color:transparent;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 15px 0;
  }

    h1 {
      padding: 1rem;
      background: transparent; /* Light header with a subtle shadow */
      text-align: center;
      font-size: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
    }
    .container1 {
    max-width: 1000px;
    margin: auto;
    padding: 0 20px;
  }


    .container {
      display: flex;
      flex-wrap: wrap;
      max-width: 1000px;
      margin: 2rem auto;
      gap: 2rem;
      padding: 1rem;
    }

    .section {
      background: #ffffff; /* White background for sections */
      border-radius: 12px;
      padding: 1.5rem;
      flex: 1 1 45%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1); /* Subtle shadow effect */
      border: 1px solid #e1e1e1; /* Light border for a polished look */
    }

    .section h2 {
      margin-top: 0;
      margin-bottom: 1rem;
      color: #007bff; /* Professional blue color */
    }

    .book-img {
      width: 100px;
      border-radius: 8px;
      margin-right: 1rem;
    }

    .order-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .order-details p {
      margin: 4px 0;
    }

    form input, form textarea {
      width: 100%;
      padding: 0.7rem;
      margin-bottom: 1rem;
      border-radius: 6px;
      border: 1px solid #ddd; /* Lighter border for input fields */
      outline: none;
      background: #f9f9f9; /* Light background for inputs */
      color: #333; /* Dark text inside inputs for readability */
    }

    button {
      width: 100%;
      padding: 1rem;
      background: #007bff; /* Blue button for primary action */
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #0056b3; /* Darker blue on hover */
    }

    .success {
      color: #28a745;
      margin-top: 1rem;
      font-weight: bold;
      text-align: center;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
    }

    .proceed-btn {
      padding: 1rem 2rem;
      background: #28a745; /* Green button for proceeding */
      color: white;
      font-size: 1.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
      margin-top: 2rem;
      display: block;
      width: 100%;
    }

    .proceed-btn:hover {
      background: #218838; /* Darker green on hover */
    }
    a {
  text-decoration: none; /* Removes the underline from all anchor tags */
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


  <h1>📚 Book Checkout</h1>

 <div class="container">
    <!-- Order Summary -->
    <div class="section" id="orderSummary">
      <h2>Order Summary</h2>
      <div class="order-item" id="bookDetails">
        <img src="<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-img" />
        <div>
          <p><strong><?= htmlspecialchars($book['title']) ?></strong></p>
          <p>by <?= htmlspecialchars($book['author']) ?></p>
          <p>₹<?= number_format($book['price'], 2) ?></p>
          <p><strong>Quantity:</strong> 
            <select id="bookQuantity">
              <?php for ($q=1; $q<=5; $q++): ?>
                <option value="<?= $q ?>"><?= $q ?></option>
              <?php endfor; ?>
            </select>
          </p>
          <p><strong>Description:</strong> <?= htmlspecialchars($book['book_description']) ?></p>
          <!-- You can add average rating & reviews here if you have that data -->
        </div>
      </div>
    </div>

     <!-- Pricing Details -->
    <div class="section" id="pricingDetails">
      <h2>Pricing Details</h2>
      <div class="order-details">
        <p><strong>Total Quantity:</strong> <span id="quantity">1</span></p>
        <p><strong>Subtotal:</strong> ₹<span id="subtotal"><?= number_format($book['price'], 2) ?></span></p>
        <p><strong>Tax (5%):</strong> ₹<span id="tax"><?= number_format($book['price'] * 0.05, 2) ?></span></p>
        <p><strong>Delivery Fee:</strong> ₹<span id="delivery">50</span></p>
        <p><strong>Total:</strong> ₹<span id="total"><?= number_format($book['price'] * 1.05 + 50, 2) ?></span></p>
        <p><strong>Arriving Date:</strong> <span id="arrivingDate"></span></p>
      </div>
    </div>

     <!-- Buyer Details -->
    <div class="section">
      <h2>Buyer Details</h2>
      <form id="buyForm" method="POST" action="place_order.php">
        <label> Name:
<input type="text" name="user_name" id="buyerName" value="<?= htmlspecialchars($buyer['name']) ?>" required readonly /></label>
       <label> Email
<input type="email" name="user_email" id="buyerEmail" value="<?= htmlspecialchars($buyer['email']) ?>" required readonly /></label>

<input type="tel" name="contact_number" id="buyerPhone" value="<?= htmlspecialchars($buyer['phone_no']) ?>" required readonly /> </label>
<label> Address: 
<textarea name="shipping_address" rows="3" id="buyerAddress" required readonly><?= htmlspecialchars($buyer['address']) ?></textarea></label>


        <button type="button" id="editBtn">Edit</button>
        <button type="button" id="saveBtn" style="display: none;">Save</button>

        <!-- Proceed to Payment Button -->
       <input type="hidden" name="book_id" value="<?= $bookId ?>">
<input type="hidden" name="quantity" id="quantityInput" value="1">
<input type="hidden" name="totalAmount" id="totalAmountInput" value="<?= number_format($book['price'] * 1.05 + 50, 2) ?>">

<button type="submit" class="proceed-btn">Proceed to Pay</button>

      </form>
    </div>
  </div>
<script>
  // Delivery date calculation same as before
  const today = new Date();
  const arrivingDate = new Date(today.setDate(today.getDate() + 7));
  document.getElementById("arrivingDate").textContent = arrivingDate.toLocaleDateString();

  const price = <?= json_encode($book['price']) ?>;
  const delivery = 50;

  function updatePricing(quantity) {
    const subtotal = (price * quantity).toFixed(2);
    const tax = (price * 0.05 * quantity).toFixed(2);
    const total = (parseFloat(subtotal) + parseFloat(tax) + delivery).toFixed(2);

    document.getElementById("quantity").textContent = quantity;
    document.getElementById("subtotal").textContent = subtotal;
    document.getElementById("tax").textContent = tax;
  document.getElementById("totalAmountInput").value = total;

  }

  document.getElementById("bookQuantity").addEventListener("change", function () {
    const quantity = parseInt(this.value);
    updatePricing(quantity);
    document.getElementById("quantityInput").value = quantity;
});


  updatePricing(1);

  // Edit / Save buyer details (can use AJAX to update DB or keep local edits)
  document.addEventListener("DOMContentLoaded", () => {
    const inputs = [
      document.getElementById("buyerName"),
      document.getElementById("buyerEmail"),
      document.getElementById("buyerPhone"),
      document.getElementById("buyerAddress"),
    ];

    document.getElementById("editBtn").addEventListener("click", () => {
      inputs.forEach(i => i.removeAttribute("readonly"));
      document.getElementById("saveBtn").style.display = "inline-block";
      document.getElementById("editBtn").style.display = "none";
    });
   

    document.getElementById("saveBtn").addEventListener("click", () => {
      // For real update, send AJAX request to update user details in DB
      // For now, just toggle read-only mode
      inputs.forEach(i => i.setAttribute("readonly", true));
      document.getElementById("saveBtn").style.display = "none";
      document.getElementById("editBtn").style.display = "inline-block";
    });
  });
  </script>
  <script>
document.addEventListener("DOMContentLoaded", () => {
  const nameField = document.getElementById("buyerName");
  const emailField = document.getElementById("buyerEmail");
  const phoneField = document.getElementById("buyerPhone");
  const addressField = document.getElementById("buyerAddress");
  const proceedBtn = document.getElementById("proceedBtn");
  const editBtn = document.getElementById("editBtn");
  const saveBtn = document.getElementById("saveBtn");
  const bookId = document.getElementById("bookId").value;

  const inputs = [nameField, emailField, phoneField, addressField];

  // Enable fields on Edit
  editBtn.addEventListener("click", () => {
    inputs.forEach(input => input.removeAttribute("readonly"));
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    checkInputs(); // Run once in case fields are already valid
  });

  // Save action — just disable inputs back for now
  saveBtn.addEventListener("click", () => {
    inputs.forEach(input => input.setAttribute("readonly", true));
    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    checkInputs(); // recheck
  });

  // Add input listeners to check validity
  inputs.forEach(input => {
    input.addEventListener("input", checkInputs);
  });

  function checkInputs() {
    const allFilled = inputs.every(input => input.value.trim() !== "");
    const allEditable = inputs.every(input => !input.hasAttribute("readonly"));
    proceedBtn.disabled = !(allFilled && allEditable);
  }

  // Redirect manually if button is clicked and enabled
  proceedBtn.addEventListener("click", () => {
    if (!proceedBtn.disabled) {
      window.location.href = `payment.php?book_id=${bookId}`;
    }
  });

});
</script>

<!-- <script>
  // Extract book title from URL
  const params = new URLSearchParams(window.location.search);
  const bookTitle = params.get("title");

  const books = JSON.parse(localStorage.getItem("books")) || [];
  const book = books.find(b => b.title === bookTitle);

  if (book) {
    const ratings = book.ratings || [];
    const averageRating = ratings.length > 0 ? (ratings.reduce((sum, rating) => sum + rating, 0) / ratings.length).toFixed(1) : 'No ratings yet';
    // Default reviews to use if the book has none
const defaultReviews = [
  "Absolutely loved it! Highly recommend 📚",
  "Great read, well-written and engaging.",
  "Interesting plot and strong characters.",
  "A bit slow in the middle but overall satisfying.",
  "Would definitely purchase from this author again!"
];

const reviews = book.reviews && book.reviews.length > 0 ? book.reviews : defaultReviews;

    const description = book.description || 'No description available';

    document.getElementById("bookDetails").innerHTML = ` 
      <img src="${book.imageUrl}" alt="${book.title}" class="book-img" />
      <div>
        <p><strong>${book.title}</strong></p>
        <p>by ${book.author}</p>
        <p>₹${book.price}</p>
        <p><strong>Quantity:</strong> <select id="bookQuantity">
          ${[1, 2, 3, 4, 5].map(q => `<option value="${q}">${q}</option>`).join('')}
        </select></p>
        <p><strong>Average Rating:</strong> 4⭐</p>
        <p><strong>Description:</strong> ${book.bookDesc}</p>
        <p><strong>Reviews:</strong><br>${reviews.map(r => `⭐ ${r}`).join('<br>')}</p>

      </div>
    `;

    const price = parseFloat(book.price);
    const delivery = 50; // Flat delivery charge

    // Function to update pricing details
    function updatePricing(quantity) {
      const subtotal = (price * quantity).toFixed(2);
      const tax = (price * 0.05 * quantity).toFixed(2);
      const total = (parseFloat(subtotal) + parseFloat(tax) + delivery).toFixed(2);

      document.getElementById("quantity").textContent = quantity;
      document.getElementById("subtotal").textContent = subtotal;
      document.getElementById("tax").textContent = tax;
      document.getElementById("delivery").textContent = delivery;
      document.getElementById("total").textContent = total;
    }

    // Initialize pricing with the default quantity (1)
    updatePricing(1);

    // Listen for quantity changes
    document.getElementById("bookQuantity").addEventListener("change", function () {
      const quantity = parseInt(this.value, 10);
      updatePricing(quantity);
    });

    // Calculate and display arriving date (1 week from now)
    const today = new Date();
    const arrivingDate = new Date(today.setDate(today.getDate() + 7)); // Add 7 days
    const arrivingDateStr = arrivingDate.toLocaleDateString();
    document.getElementById("arrivingDate").textContent = arrivingDateStr;
  }

  // Handle form submission
  document.getElementById("buyForm").addEventListener("submit", function (e) {
    e.preventDefault();
    // Clear form and handle order
    this.reset();
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Load buyer info from localStorage (e.g., saved from profile.html)
    const buyerData = JSON.parse(localStorage.getItem("buyerDetails")) || {};

    document.getElementById("buyerName").value = buyerData.name || "";
    document.getElementById("buyerEmail").value = buyerData.email || "";
    document.getElementById("buyerPhone").value = buyerData.phone || "";
    document.getElementById("buyerAddress").value = buyerData.address || "";

    // Edit functionality
    const inputs = [
      document.getElementById("buyerName"),
      document.getElementById("buyerEmail"),
      document.getElementById("buyerPhone"),
      document.getElementById("buyerAddress")
    ];
    document.getElementById("editBtn").addEventListener("click", function () {
      inputs.forEach(input => input.removeAttribute("readonly"));
      document.getElementById("saveBtn").style.display = "inline-block";
      this.style.display = "none";
    });

    // Save functionality
    document.getElementById("saveBtn").addEventListener("click", function () {
      const updatedData = {
        name: document.getElementById("buyerName").value,
        email: document.getElementById("buyerEmail").value,
        phone: document.getElementById("buyerPhone").value,
        address: document.getElementById("buyerAddress").value
      };

      localStorage.setItem("buyerDetails", JSON.stringify(updatedData));
      inputs.forEach(input => input.setAttribute("readonly", true));
      document.getElementById("editBtn").style.display = "inline-block";
      this.style.display = "none";
    });
  });
</script> -->
  <!-- Footer Section -->
   <!-- Footer -->
  <footer style="background-color: #f8f8f8; border-top: 1px solid #eaeaea; padding: 60px 0; font-size: 15px; color: #555;">
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
