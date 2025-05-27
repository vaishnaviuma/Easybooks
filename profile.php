<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$name = $age = $gender = $address = $fav_genre = $phone = '';
$isNewUser = false;

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM allusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $isNewUser = true;
} else {
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $age = $user['age'];
    $gender = $user['gender'];
    $address = $user['address'];
    $fav_genre = $user['fav_genre'];
    $suggestions = [];
if (!empty($fav_genre)) {
    $stmtBooks = $conn->prepare("SELECT id, title, author, genre, image_url FROM books WHERE genre = ? LIMIT 8");
    $stmtBooks->bind_param("s", $fav_genre);
    $stmtBooks->execute();
    $resultBooks = $stmtBooks->get_result();
    while ($row = $resultBooks->fetch_assoc()) {
        $suggestions[] = $row;
    }
    $stmtBooks->close();
}

    $phone = $user['phone_no'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    /* Basic Reset and Global Styles */
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f7fa;
      color: #333;
      box-sizing: border-box;
    }

    /* Container for Flex Layout */
    .container {
      display: flex;
      justify-content: space-between;
      max-width: 1200px;
      margin:  auto;
      gap: 30px;
      padding: 0px 20px;
    }

    /* Profile Section Styling */
    .profile-box {
      background: linear-gradient(145deg, #ffffff, #f1f1f1);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      flex: 0 0 60%;
      animation: fadeIn 1s ease-in-out;
    }

    .suggestions-box {
      background: linear-gradient(145deg, #ffffff, #f1f1f1);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      flex: 0 0 35%;
      animation: fadeIn 1.5s ease-in-out;
    }

    /* Heading and Labels */
    h2, h3 {
      color: #333;
      margin-top: 0;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 10px;
      color: #555;
    }

    /* Input and Select Styling */
    input, select {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ddd;
      background-color: #fafafa;
      font-size: 16px;
      transition: all 0.3s ease-in-out;
    }

    input:focus, select:focus {
      border-color: #0077cc;
      background-color: #fff;
      box-shadow: 0 0 5px rgba(0, 119, 204, 0.5);
    }

    /* Button Styling */
    button {
      padding: 12px 20px;
      border: none;
      background-color: #0077cc;
      color: white;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }

    button:hover {
      background-color: #005fa3;
    }

    button:disabled {
      background-color: #aaa;
      cursor: not-allowed;
    }

    /* Animations */
    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Order History and Suggestions Section */
    

    
    

    /* Book Suggestion Styling */
    /* Book Suggestion Styling */
.book-suggestion {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  transition: transform 0.3s ease-in-out;
  justify-content: flex-start;
}

.book-suggestion img {
  width: 60px;
  height: 90px;
  object-fit: cover;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: block;
  margin: 0 auto;
}

.book-suggestion div {
  flex: 1;
}

/* Added margin for space between the image and the text */
.book-suggestion .book-info {
  margin-left: 15px;  /* Space between the image and the book title */
}

.book-suggestion:hover {
  transform: translateX(10px);
}



    /* Multi-select Styling */
    select[multiple] {
  min-height: 100px;
  height: auto;
  background: #eaf6ff; /* light pastel blue background */
  transition: all 0.3s ease-in-out;
  border: 1px solid #b3d7f5;
  color: #333;
  font-size: 16px;
  border-radius: 8px;
  padding: 10px;
}

    select[multiple]:focus,
select[multiple]:hover {
  background: #d9efff;
  border-color: #90caff;
}

    button:hover {
  background-color: #005fa3;
  transform: scale(1.05);
}

input:hover, select:hover {
  background-color: #f0f0f0;
}
.profile-box, .suggestions-box {
  background: linear-gradient(145deg, #ffffff, #f8f9fa);
}
.order-history .order-item {
  background: #fff;
  padding: 15px;
  margin-bottom: 10px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.order-history .order-item:hover {
  transform: scale(1.05);
}

/* Header */
  header {
    background-color: transparent;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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

.order-item {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  padding: 10px;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.order-item img {
  width: 60px;
  height: 90px;
  object-fit: cover;
  border-radius: 5px;
}

.order-details {
  font-size: 14px;
  color: #333;
}   
#seeMoreBtn {
  display: block;
  margin: 15px auto 0;
  padding: 10px 20px;
  background-color: #0077cc;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s ease-in-out;
}

#seeMoreBtn:hover {
  background-color: #005fa3;
}
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
}

.page-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}


  </style>
</head>
<body>
  <div class="page-wrapper">
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
          <li><a href="profile.html"><i class="fas fa-user-circle"></i> Profile</a></li>
           <li>
          <a href="logout.php" title="Logout">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>
  <br>
   <main>
  <div class="container">
    <!-- Profile Section -->
    <div class="profile-box">
      <h2>User Profile</h2>
      <form id="profileForm"  action="save_profile.php" method="POST">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div class="form-group">
           <label for="address">Address:</label>
          <input type="text" name="address" id="address" value="<?= htmlspecialchars($address) ?>" required>
        </div>
        <div class="form-group">
            <label for="fav_genre">Favorite Genre:</label>
      <select name="fav_genre" id="fav_genre" required>
        <?php
          $genres = ["Thriller", "Romance", "Mystery", "Fantasy", "Classic", "Biography", "Self-Help","Science Fiction", "Suspense"];
          foreach ($genres as $genre) {
            $selected = ($fav_genre === $genre) ? 'selected' : '';
            echo "<option value=\"$genre\" $selected>$genre</option>";
          }
        ?>
      </select>

          </div>
          
        <div class="form-group">
           <label for="gender">Gender:</label>
      <select name="gender" id="gender" required>
        <option value="">Select</option>
        <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
      </select>
        </div>
        <div class="form-group">
         <label for="age">Age:</label>
           <input type="number" name="age" id="age" value="<?= htmlspecialchars($age) ?>" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number:</label>
          <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone) ?>" required>
        </div>
         <button type="submit"><?= $isNewUser ? "Save Profile" : "Update Profile" ?></button>
      </form>
<br>
     <!-- Order History -->
      <div class="order-history">
        <h3>Order History</h3>

           <div class="order-item">
              <img src="https://cdn.24.co.za/files/Cms/General/d/9367/0a29b08bff4a4abca259568100070fd7.jpg" alt="The Silent Patient">
              <div class="order-details">
                <strong>#1001</strong> - The Silent Patient<br>
                   ₹399 - Mar 12, 2025
              </div>
            </div>

           <div class="order-item">
             <img src="https://m.media-amazon.com/images/I/91SDZ2eUj+L.jpg" alt="Verity">
             <div class="order-details">
               <strong>#1002</strong> - Verity<br>
                   ₹299 - Apr 4, 2025
              </div>
            </div>
            <a href="orderhistory.php">
  <button id="seeMoreBtn">See More</button>
</a>


      </div>
    </div>
      
           <!-- Book Suggestions Section -->
       <div class="suggestions-box">
         <h2>📚 You may also like:</h2>
         <div id="suggestedBooks" class="book-suggestions" style="display: flex; flex-wrap: wrap; gap: 20px;">
  <?php if (empty($suggestions)): ?>
    <p>No suggestions found for your favorite genre yet.</p>
  <?php else: ?>
    <?php foreach ($suggestions as $book): ?>
      <a href="book-detail.php?id=<?= urlencode($book['id']) ?>" class="book-suggestion" style="flex-direction: column; width: 150px; text-align: center; text-decoration: none; color: inherit;">
      <div class="book-suggestion" style="flex-direction: column; width: 150px; text-align: center;">
        <img src="<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" style="width:100%; height: auto; border-radius: 6px; margin-bottom: 8px;">
        <div class="book-info">
          <strong><?= htmlspecialchars($book['title']) ?></strong><br>
          <small><?= htmlspecialchars($book['author']) ?></small>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

  
    
  </div>
</main>
  <script>
    const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const inputs = document.querySelectorAll('#profileForm input, #profileForm select');

// Enable inputs when the Edit button is clicked
editBtn.addEventListener('click', () => {
  inputs.forEach(input => input.disabled = false);
  editBtn.style.display = 'none';
  saveBtn.style.display = 'inline-block';
});

// Disable inputs and save changes when Save button is clicked
saveBtn.addEventListener('click', () => {
  inputs.forEach(input => input.disabled = true);
  saveBtn.style.display = 'none';
  editBtn.style.display = 'inline-block';
});

  </script>
  
<!-- <script>
  document.addEventListener("DOMContentLoaded", function () {
   
  });
</script> -->
<!-- <script>
  const genreSelect = document.getElementById('genre');

// Retrieve the genres from localStorage, or default to an empty array
let savedGenres = JSON.parse(localStorage.getItem('favoriteGenres'));
if (!savedGenres || savedGenres.length === 0) {
  // If no genres saved, get from the default selected options in the form
  const defaultSelected = Array.from(genreSelect.options)
    .filter(opt => opt.selected)
    .map(opt => opt.value.toLowerCase());
  localStorage.setItem('favoriteGenres', JSON.stringify(defaultSelected));
  savedGenres = defaultSelected;
}

// Update the genre options in the profile page based on saved genres
Array.from(genreSelect.options).forEach(option => option.selected = false); // Unselect all options
savedGenres.forEach(genre => {
  const option = Array.from(genreSelect.options).find(opt => opt.value.toLowerCase() === genre);
  if (option) option.selected = true;
});

// Function to save the updated genres to localStorage
function saveGenres() {
  const selectedGenres = Array.from(genreSelect.selectedOptions).map(option => option.value.toLowerCase());
  localStorage.setItem('favoriteGenres', JSON.stringify(selectedGenres));
  displayBookSuggestions();  // Update suggestions after saving
}



// Display book suggestions based on saved genres
function displayBookSuggestions() {
  const userGenres = JSON.parse(localStorage.getItem('favoriteGenres')) || [];
  const allBooks = JSON.parse(localStorage.getItem('books')) || [];
  allBooks.forEach(b => console.log(`${b.title}: ${b.genre}`));
  const suggestedBooks = allBooks.filter(book => {
    const bookGenre = (book.genre || '').trim().toLowerCase();
    return userGenres.includes(bookGenre);
  });

  const suggestionsBox = document.querySelector('.suggestions-box');
  suggestionsBox.innerHTML = '';  // Clear previous suggestions

  if (suggestedBooks.length === 0) {
    suggestionsBox.innerHTML = `<p>No books available for the selected genres.</p>`;
  } else {
    suggestedBooks.forEach(book => {
      const suggestionDiv = document.createElement('div');
      suggestionDiv.classList.add('book-suggestion');
      suggestionDiv.innerHTML = `
      <a href="book-detail.html?title=${encodeURIComponent(book.title)}" style="text-decoration: none; color: inherit;">
        <img src="${book.imageUrl || book.image || 'https://via.placeholder.com/50x75?text=No+Image'}" alt="${book.title}">
        <br>
        <div class="book-info">
          <strong>${book.title}</strong><br>
          <p><strong>Author:</strong> ${book.author}</p>
          <p><strong>Genre:</strong> ${book.genre}</p>
          <p><strong>Price:</strong> Rs.${book.price}</p>
        </div>
      `;
      suggestionsBox.appendChild(suggestionDiv);
    });
  }
}


// Call displayBookSuggestions to show suggestions based on saved genres when the
displayBookSuggestions();


</script> -->
  <script>
  const editBtn = document.getElementById('editBtn');
  const saveBtn = document.getElementById('saveBtn');
  const inputs = document.querySelectorAll('#profileForm input, #profileForm select');

  // Enable inputs when the Edit button is clicked
  if (editBtn) {
    editBtn.addEventListener('click', () => {
      inputs.forEach(input => input.disabled = false);
      editBtn.style.display = 'none';
      saveBtn.style.display = 'inline-block';
    });
  }
</script>




<!-- <script>

// Load saved profile data
const profileFields = ['name', 'address', 'gender', 'age', 'phone'];
const profileData = JSON.parse(localStorage.getItem('profileData')) || {};

profileFields.forEach(field => {
  const input = document.getElementById(field);
  if (input && profileData[field]) {
    input.value = profileData[field];
  }
});

// Save profile data
saveBtn.addEventListener('click', () => {
  // Save profile inputs to localStorage
  const updatedProfile = {};
  profileFields.forEach(field => {
    const input = document.getElementById(field);
    updatedProfile[field] = input.value;
  });
  localStorage.setItem('profileData', JSON.stringify(updatedProfile));

  // Save genres
  saveGenres();

  // Disable inputs
  inputs.forEach(input => input.disabled = true);
  saveBtn.style.display = 'none';
  editBtn.style.display = 'inline-block';
});
</script> -->

<!-- <script>
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

</script>   -->
   <!-- Footer Section -->
   <!-- Footer -->
    
  <footer style="background-color: transparent; border-top: 1px solid #eaeaea; padding: 60px 0; font-size: 15px; color: #555;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px; padding-left: 100px; ">
    
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
</div>

</body>
</html>
