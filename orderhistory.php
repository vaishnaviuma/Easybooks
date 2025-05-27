<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = $_SESSION['user_id'];

// Fetch orders for the user
$stmt = $conn->prepare("
  SELECT o.id AS order_id, o.quantity, o.total_amount, o.order_placed_at, b.title, b.author, b.image_url
  FROM orders o
  JOIN books b ON o.book_id = b.id
  WHERE o.user_id = ?
 ORDER BY o.order_placed_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order History</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Base styles */
body {
  font-family: 'Inter', sans-serif;
  margin: 0;
  padding: 40px 20px;
  background: linear-gradient(135deg, #f9f9f9, #e0e0e0);
  color: #1e1e1e;
  min-height: 100vh;
}

/* Container with glassmorphism effect */
.order-history-container {
  max-width: 1000px;
  margin: auto;
  background: rgba(255, 255, 255, 0.85);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  border-radius: 20px;
  padding: 30px;
  backdrop-filter: blur(10px);
  animation: fadeSlideIn 0.8s ease forwards;
}

/* Header with typing effect (like book title) */
.order-history-container h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 24px;
  white-space: nowrap;
  overflow: hidden;
  border-right: 2px solid transparent;
  animation: typing 3s steps(40, end), blink 0.75s step-end infinite;
}

/* Typing animations */
@keyframes typing {
  from { width: 0; }
  to { width: 100%; }
}
@keyframes blink {
  50% { border-color: transparent; }
  100% { border-color: #333; }
}

/* Order cards styled similarly to book cards */
.order-card {
  display: flex;
  align-items: center;
  gap: 24px;
  background: white;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: box-shadow 0.3s ease;
}

.order-card:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.order-card img {
  width: 120px;
  border-radius: 12px;
  transition: transform 0.4s ease;
}

.order-card img:hover {
  transform: scale(1.05);
}

.order-info p {
  margin: 6px 0;
  color: #444;
}

.order-info p strong {
  color: #111;
}

/* Buttons (if any) */
button {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  background: #28a745;
  color: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s ease, box-shadow 0.3s ease, background-color 0.3s ease;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  animation: pulse 0.4s ease-in-out;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.04); }
}

/* Fade Slide In for container */
@keyframes fadeSlideIn {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}

    h1 {
      text-align: center;
    }

    .order-list {
      max-width: 900px;
      margin: auto;
    }

   
    .order-card img {
      width: 100px;
      height: auto;
      border-radius: 8px;
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
      .container1 {
    max-width: 1000px;
    margin: auto;
    padding: 0 20px;
  }

      /* Header */
  header {
    background-color:transparent;
    /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); */
    padding: 15px 0;
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
  <canvas id="particle-canvas"></canvas>

  <div class="animated-bg">
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
  </div>
  

  <h1>📦 Your Order History</h1>
  <div class="order-list">
    <?php if (count($orders) > 0): ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <img src="<?= htmlspecialchars($order['image_url']) ?>" alt="Book cover">
          <div class="order-info">
            <p><strong>Title:</strong> <?= htmlspecialchars($order['title']) ?></p>
            <p><strong>Author:</strong> <?= htmlspecialchars($order['author']) ?></p>
            <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
            <p><strong>Total Paid:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
            <p><strong>Ordered On:</strong> <?= date("F j, Y", strtotime($order['order_placed_at'])) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center;">You haven't placed any orders yet.</p>
    <?php endif; ?>
  </div>

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
  <!-- Footer Section -->
   <!-- Footer -->
  <footer style="background-color:transparent;  padding: 60px 0; border-top: 1px ; font-size: 15px; color: #555;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px;  padding-left: 100px; ">
    
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

    <div style="text-align: center; margin-top: 40px; border-top: 1px ; padding-top: 20px; font-size: 14px; color: #888;">
      &copy; 2025 Easybooks. All rights reserved.
    </div>
  </footer>
</body>
</html>
