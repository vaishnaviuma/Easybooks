<?php
// db.php - include your DB connection here
session_start();
include 'db.php';

$bookId = isset($_GET['id']) ? intval($_GET['id']) : 1;

$sql = "SELECT * FROM books WHERE id = $bookId";
$result = $conn->query($sql);

$book = $result->num_rows > 0 ? $result->fetch_assoc() : null;


// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     die("Please log in to add items to your cart.");
// }
  

$message = ""; // Add this before the if block
    // Check login first and redirect before doing anything
$isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';

if (isset($_POST['add_to_cart'])) {
    // Only run below if user is logged in
    $book_id = $_POST['book_id'];
    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
 }
    $user_id = $_SESSION['user_id'];

    $check = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO cart_items (user_id, book_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $book_id);
        $insert->execute();
        $message = "✅ Book added to cart!";
        } else {
           $message = "⚠️ Book is already in your cart.";
        }
    
}
$reviewmessage="";
$sql = "SELECT * FROM books WHERE id = $bookId";
$result = $conn->query($sql);
$book = $result->num_rows > 0 ? $result->fetch_assoc() : null;
$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit;
    }
    $review_text = trim($_POST['review_text']);
    if ($review_text !== "") {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_id, review_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $bookId, $user_id, $review_text);
        $stmt->execute();
       // $message = "✅ Review submitted successfully!";
        $stmt->close();
    } else {
        $reviewmessage = "⚠️ Review cannot be empty.";
    }
}

// Fetch reviews for this book
$reviews = [];
$stmt = $conn->prepare("SELECT r.review_text, r.created_at, u.name FROM reviews r JOIN allusers u ON r.user_id = u.id WHERE r.book_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt->close();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Detail</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 40px 20px;
      background: linear-gradient(135deg, #f9f9f9, #e0e0e0);
      color: #1e1e1e;
    }

    .detail-container {
      max-width: 1000px;
      margin: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      background: rgba(255, 255, 255, 0.85);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      border-radius: 20px;
      padding: 30px;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease-in-out;
    }

    .book-img {
      flex: 1;
      min-width: 280px;
      position: relative;
    }

    .book-img img {
      width: 100%;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.4s ease, box-shadow 0.3s ease;
    }

    /* Typing effect for book title */
    .book-info h1 {
  font-size: 2rem;
  margin-bottom: 16px;
  font-weight: 600;
  color: #333;
  white-space: nowrap; /* Prevent line breaks */
  overflow: hidden; /* Hide extra text */
  border-right: 2px solid transparent; /* Initially no cursor/indicator */
  animation: typing 3s steps(40, end), blink 0.75s step-end infinite;
  visibility: visible; /* Ensure the title stays visible after animation */
}

/* Typing animation */
@keyframes typing {
  from {
    width: 0;
  }
  to {
    width: 100%;
  }
}

/* Blink effect (keeps cursor effect after typing) */
@keyframes blink {
  50% {
    border-color: transparent;
  }
  100% {
    border-color: #333;
  }
}


    .book-info {
      flex: 2;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .book-info p {
      margin: 10px 0;
      line-height: 1.6;
      color: #444;
    }

    .book-info p strong {
      color: #111;
    }

    .actions {
      margin-top: 30px;
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .actions button {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease, box-shadow 0.3s ease, background-color 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }

    .add-cart {
      background: #28a745;
      color: #fff;
    }

    .buy {
      background: #ff6f61;
      color: #fff;
    }

    .share {
      background: #007bff;
      color: #fff;
    }

    .actions button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
      animation: pulse 0.4s ease-in-out;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.04);
      }
      100% {
        transform: scale(1);
      }
    }

    /* Hover Ripple effect for book icon */
    .book-img img:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      animation: ripple 1s ease-out;
    }

    @keyframes ripple {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
      }
    }

    @keyframes fadeSlideIn {
      0% {
        opacity: 0;
        transform: translateY(40px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .detail-container {
      animation: fadeSlideIn 0.8s ease forwards;
    }

    /* Button click pop effect */
    @keyframes popClick {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(0.92);
      }
      100% {
        transform: scale(1);
      }
    }

    .actions button:active {
      animation: popClick 0.2s ease-out;
    }

    .not-found {
      max-width: 600px;
      margin: 100px auto;
      padding: 40px;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      animation: fadeSlideIn 0.6s ease;
    }

    .not-found h2 {
      font-size: 2rem;
      color: #ff6f61;
      margin-bottom: 10px;
    }

    .not-found p {
      font-size: 1.1rem;
      color: #444;
      margin-bottom: 20px;
    }

    .not-found .go-back {
      display: inline-block;
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.3s ease;
    }

    .not-found .go-back:hover {
      background: #0056b3;
    }

    .animated-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    .blob {
      position: absolute;
      border-radius: 50%;
      opacity: 0.3;
      mix-blend-mode: multiply;
      filter: blur(60px);
      animation: floatBlobs 25s infinite ease-in-out;
    }

    .blob1 {
      width: 400px;
      height: 400px;
      background: #ff6f61;
      top: -100px;
      left: -100px;
    }

    .blob2 {
      width: 500px;
      height: 500px;
      background: #007bff;
      top: 50%;
      left: 70%;
      animation-delay: 10s;
    }

    .blob3 {
      width: 300px;
      height: 300px;
      background: #28a745;
      bottom: -80px;
      right: -100px;
      animation-delay: 20s;
    }

    @keyframes floatBlobs {
      0%, 100% {
        transform: translateY(0) scale(1);
      }
      50% {
        transform: translateY(-50px) scale(1.1);
      }
    }
    .book-info h1 {
    font-size: 2rem;
    margin-bottom: 16px;
    font-weight: 600;
    color: #333;
    white-space: nowrap; /* Prevent line breaks */
    overflow: hidden; /* Hide extra text */
    border-right: 2px solid transparent; /* Initially no cursor/indicator */
    animation: typing 3s steps(40, end), blink 0.75s step-end infinite;
}

/* Remove the blinking cursor (arrow) */
.book-info h1::after {
    content: none; /* This removes the arrow */
}
/* Base button style */
.actions button {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  position: relative;
}

/* Low effect on hover */
.actions button:hover {
  transform: translateY(2px); /* Push the button slightly down on hover */
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

/* Pulse animation */
@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1); /* Grow to 1.1x its size */
  }
  100% {
    transform: scale(1);
  }
}

/* Button hover pulse */
.actions button:hover {
  animation: pulse 0.6s ease-in-out;
}

/* Button active click effect */
@keyframes popClick {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.95); /* Scale down a bit for click effect */
  }
  100% {
    transform: scale(1);
  }
}

.actions button:active {
  animation: popClick 0.2s ease-out;
}
#particle-canvas {
  position: fixed;
  top: 0;
  left: 0;
  z-index: -1;
  width: 100%;
  height: 100%;
  background: #fafafa; /* Or set to transparent if you want blob + particle combo */
}
.close-btn {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 32px;
  text-decoration: none;
  color: #888;
  font-weight: bold;
  z-index: 1000;
}
.close-btn:hover {
  color: #000;
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

  .glass-review-box {
    max-width: 1000px;
    margin: 40px auto;
  margin-top: 40px auto;
  padding: 30px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  animation: fadeSlideIn 0.6s ease;
  align-items: center
}

.glass-review-box h3 {
  font-size: 1.4rem;
  margin-bottom: 16px;
}

.glass-review-box form textarea {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ccc;
  resize: vertical;
  margin-bottom: 16px;
  background: rgba(255, 255, 255, 0.6);
  font-family: 'Inter', sans-serif;
}

.glass-review-box form button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.glass-review-box form button:hover {
  background: #0056b3;
}

.review-item {
  background: rgba(255, 255, 255, 0.4);
  padding: 10px 15px;
  margin-bottom: 10px;
  border-radius: 10px;
  border-left: 5px solid #007bff;
}

.message-popup {
  background: rgba(40, 167, 69, 0.9); /* Green success background */
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  position: fixed;
  top: 20px;
  right: 20px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  z-index: 999;
  animation: fadeInOut 3s ease-in-out forwards;
  font-weight: bold;
}

@keyframes fadeInOut {
  0% { opacity: 0; transform: translateY(-10px); }
  10% { opacity: 1; transform: translateY(0); }
  90% { opacity: 1; transform: translateY(0); }
  100% { opacity: 0; transform: translateY(-10px); }
}


  </style>
</head>
<body>
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


  <canvas id="particle-canvas"></canvas>

  <div class="animated-bg">
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
  </div>
  

  <div class="detail-container">
    <a href="home.php" class="close-btn" title="Close">&times;</a>

    <div class="book-img">
      <img src="<?= htmlspecialchars($book['image_url']) ?>" alt="Book Cover">
    </div>
    <div class="book-info">
      <h1><?= htmlspecialchars($book['title']) ?></h1>
     <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
<p><strong>Description:</strong> <?= htmlspecialchars($book['book_description']) ?></p>
<p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
<p><strong>Author Description:</strong> <?= htmlspecialchars($book['author_description']) ?></p>
<p><strong>Price:</strong> ₹<?= htmlspecialchars($book['price']) ?></p>

      <div class="actions"><?php if (!empty($message)): ?>
  <div style="margin-bottom: 20px; color: #d9534f; font-weight: bold;">
  </div>
<?php endif; ?>


        <form method="POST" action="">
    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
    <button type="submit"  class="add-cart" name="add_to_cart">Add to Cart</button>
</form>

        <!-- <button class="add-cart">Add to Cart</button> -->
         </form>
         <?php if (!empty($message)): ?>
  <div id="successMessage" class="message-popup">
    <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>
        <button class="buy" data-title="" id="buyNowBtn" onclick="window.location.href='buy.php?id=<?= $book['id'] ?>'">Buy Now</button>
         <button id="reviews-btn" type="button" class="share">Reviews</button>
        <button class="share"><i class="fas fa-share-alt"></i> Share</button>
      </div>
    </div>
  </div>

  <script>
  const isLoggedIn = <?= $isLoggedIn ?>;
  const addToCartForm = document.getElementById('addToCartForm');

  addToCartForm.addEventListener('submit', function(event) {
    if (!isLoggedIn) {
      event.preventDefault();  // Stop form submission
      alert('Please log in to add items to your cart.');
      window.location.href = 'login.php'; // Redirect to login page
    }
  });
</script>


  <!-- <script>
    // Retrieve the books from localStorage
const books = JSON.parse(localStorage.getItem("books")) || [];

// Retrieve the title from the URL
const title = decodeURIComponent(new URLSearchParams(window.location.search).get("title") || "").trim().toLowerCase();

// Find the selected book based on the title
const book = books.find(b => (b.title || "").trim().toLowerCase() === title);

    if (book) {
      // Support both old and new keys using fallback (|| operator)
      document.getElementById('bookImage').src = book.imageUrl || book.image || '';
      document.getElementById('bookTitle').textContent = book.title || 'Unknown Title';
      document.getElementById('bookGenre').textContent = book.genre || 'Unknown Genre';
      document.getElementById('bookDescription').textContent = book.bookDesc || book.description || 'No description available.';
      document.getElementById('bookAuthor').textContent = book.author || 'Unknown Author';
      document.getElementById('authorDesc').textContent = book.authorDesc || book.authorBio || 'No author information available.';
      document.getElementById('bookPrice').textContent = book.price || 'N/A';
    } else {
      document.body.innerHTML =  
        <div class="not-found">
          <h2>📚 Oops! Book Not Found</h2>
          <p>The book you're looking for doesn't exist or wasn't selected properly.</p>
          <a href="index.html" class="go-back">← Go back to home</a>
        </div>
      ;
    }
  </script> -->

  <?php if (!$book): ?>
  <div class="not-found">
    <h2>📚 Oops! Book Not Found</h2>
    <p>The book you're looking for doesn't exist or wasn't selected properly.</p>
    <a href="home.html" class="go-back">← Go back to home</a>
  </div>
<?php else: ?>
  <!-- Render book info normally -->
<?php endif; ?>

  <script>
    // for background
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas.getContext('2d');
    let particlesArray;
    
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    window.addEventListener('resize', () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      init();
    });
    
    class Particle {
      constructor(x, y, directionX, directionY, size, color) {
        this.x = x;
        this.y = y;
        this.directionX = directionX;
        this.directionY = directionY;
        this.size = size;
        this.color = color;
      }
    
      draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
        ctx.fillStyle = '#aaa';
        ctx.fill();
      }
    
      update() {
        if (this.x + this.size > canvas.width || this.x - this.size < 0)
          this.directionX = -this.directionX;
        if (this.y + this.size > canvas.height || this.y - this.size < 0)
          this.directionY = -this.directionY;
    
        this.x += this.directionX;
        this.y += this.directionY;
        this.draw();
      }
    }
    
    function connect() {
      for (let a = 0; a < particlesArray.length; a++) {
        for (let b = a; b < particlesArray.length; b++) {
          const dx = particlesArray[a].x - particlesArray[b].x;
          const dy = particlesArray[a].y - particlesArray[b].y;
          const distance = dx * dx + dy * dy;
          if (distance < 8000) {
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(150,150,150,0.1)';
            ctx.lineWidth = 1;
            ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
            ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
            ctx.stroke();
          }
        }
      }
    }
    
    function init() {
      particlesArray = [];
      const numParticles = Math.floor((canvas.width * canvas.height) / 20000);
      for (let i = 0; i < numParticles; i++) {
        const size = Math.random() * 2 + 1;
        const x = Math.random() * (canvas.width - size * 2) + size;
        const y = Math.random() * (canvas.height - size * 2) + size;
        const directionX = (Math.random() - 0.5) * 0.8;
        const directionY = (Math.random() - 0.5) * 0.8;
        particlesArray.push(new Particle(x, y, directionX, directionY, size, '#ccc'));
      }
    }
    
    function animate() {
      requestAnimationFrame(animate);
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particlesArray.forEach(p => p.update());
      connect();
    }
    
    init();
    animate();
    </script>
<!-- <script>
  const book = <?= json_encode($book) ?>;
  document.querySelector('.add-cart').addEventListener('click', function () {
  const cart = JSON.parse(localStorage.getItem('cartItems')) || [];

  const existing = cart.find(item => item.id === book.id);
  if (!existing) {
    cart.push(book);
    localStorage.setItem('cartItems', JSON.stringify(cart));
    alert('✅ Book added to cart successfully!');
  } else {
    alert('⚠️ This book is already in the cart.');
  }
});

</script> -->

<!-- <script>
  //functionality for add to cart button
  document.querySelector('.add-cart').addEventListener('click', function () {
    const cart = JSON.parse(localStorage.getItem('cartItems')) || [];

    // Avoid duplicate entries (optional)
    const existing = cart.find(item => item.title === book.title);
    if (!existing) {
      cart.push(book);
      localStorage.setItem('cartItems', JSON.stringify(cart));
      alert('✅ Book added to cart successfully!');
    } else {
      alert('⚠️ This book is already in the cart.');
    }
  });
</script> -->
<script>
  //buy now logic
  if (book) {
  // populate book details...
  document.getElementById('buyNowBtn').setAttribute('data-title', book.title);
}

  document.querySelectorAll('.buy').forEach(button => {
  button.addEventListener('click', function () {
    const title = this.getAttribute('data-title');
    window.location.href = buy.html?title=${encodeURIComponent(title)};
  });
});


</script>
<p style="color:green; font-weight:bold; margin-top: 10px;"><?= htmlspecialchars($reviewmessage) ?></p>

<!-- Reviews container, initially hidden -->

<div class="glass-review-box" id="reviews-container"  style="display: none;"> 
  <h2>Reviews for "<?= htmlspecialchars($book['title']) ?>"</h2>

  <?php if ($isLoggedIn): ?>
  <form method="post" style="margin-bottom: 30px;">
    <textarea name="review_text" rows="4" required placeholder="Write your review here..." style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #ccc;"></textarea>
    <button type="submit" name="add_review" style="margin-top: 10px; background:#007bff; color:white; border:none; padding: 12px 20px; border-radius: 8px; cursor:pointer;">Submit Review</button>
  </form>
  <?php else: ?>
  <p><a href="login.php">Log in</a> to submit a review.</p>
  <?php endif; ?>

  <?php if (count($reviews) === 0): ?>
    <p>No reviews yet. Be the first to review this book!</p>
  <?php else: ?>
    <ul style="list-style:none; padding-left:0;">
      <?php foreach ($reviews as $review): ?>
      <li style="border-bottom: 1px solid #ddd; padding: 15px 0;">
        <strong><?= htmlspecialchars($review['name']) ?></strong> <small style="color:#777;">(<?= htmlspecialchars(date("M j, Y, g:i a", strtotime($review['created_at']))) ?>)</small>
        <p style="margin-top: 8px; line-height: 1.4;"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
      
<script>
  // Hide message after 3s already handled by CSS animation

  // Toggle review visibility
  document.getElementById("toggleReviewBtn").addEventListener("click", function () {
    const container = document.getElementById("reviewContainer");
    container.style.display = (container.style.display === "none" || container.style.display === "") ? "block" : "none";
  });
</script>

<script>
  const reviewBtn = document.getElementById('reviews-btn');
  const reviewsContainer = document.getElementById('reviews-container');

  reviewBtn.addEventListener('click', () => {
    if (reviewsContainer.style.display === 'none' || reviewsContainer.style.display === '') {
      reviewsContainer.style.display = 'block';
      reviewBtn.textContent = 'Hide Reviews';
      reviewsContainer.scrollIntoView({behavior: "smooth"});
    } else {
      reviewsContainer.style.display = 'none';
      reviewBtn.textContent = 'Reviews';
    }
  });
</script>

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