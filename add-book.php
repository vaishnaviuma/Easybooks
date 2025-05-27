<?php
session_start();

// Assuming you store user role in session like: $_SESSION['role'] = 'admin';
if (!isset($_SESSION['user_id']) || $_SESSION['email'] !== 'admin2457@gmail.com') {
    // Redirect to login or home page if not admin
    header("Location: index.html");
    exit();
}
$showSuccess = false;
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $showSuccess = true;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add New Book</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      padding:40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #e3f2fd, #f1f8e9, #e0f7fa, #fff3e0);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
      transition: background 0.2s;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      background: #ffffff;
      padding: 3rem 2.5rem;
      border-radius: 18px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
      width: 100%;
      max-width: 480px;
      transition: all 0.3s ease-in-out;
      position: relative;
    }

    .container:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
      font-weight: 700;
      color: #333;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    input[type="url"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: none;
      transition: border-color 0.2s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    select:focus,
    input[type="file"]:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

    .image-options {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .image-options label {
      font-weight: 500;
    }

    .image-inputs {
      display: none;
      margin-top: 10px;
    }

    .btn-submit {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      border: none;
      color: #fff;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s;
    }

    .btn-submit:hover {
      background-color: #0056b3;
    }

    .theme-toggle {
      position: absolute;
      top: 20px;
      right: 20px;
      cursor: pointer;
      font-size: 1.2rem;
      color: #333;
    }

    /* Dark mode styles */
    body.dark-mode {
      background: #121212;
    }

    body.dark-mode .container {
      background: #1e1e1e;
      color: #eee;
    }

    body.dark-mode .image-options label {
      color: #aaa;
    }

    body.dark-mode .btn-submit {
      background: #0062cc;
    }

    body.dark-mode .btn-submit:hover {
      background: #004a99;
    }

    body.dark-mode input[type="text"],
    body.dark-mode input[type="number"],
    body.dark-mode input[type="url"],
    body.dark-mode select,
    body.dark-mode input[type="file"] {
      background: #2c2c2c;
      color: #eee;
      border-color: #444;
    }

    body.dark-mode input[type="text"]:focus,
    body.dark-mode input[type="number"]:focus,
    body.dark-mode input[type="url"]:focus,
    body.dark-mode select:focus,
    body.dark-mode input[type="file"]:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

    body.dark-mode .image-options label {
      color: #aaa;
    }

    body.dark-mode h2 {
     color: #fff;
    }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      width: 300px;
      text-align: center;
    }

    .modal button {
      background-color: #28a745;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .modal button:hover {
      background-color: #218838;
    }

    textarea {
     width: 100%;
     padding: 10px;
     margin-top: 5px;
     border: 1px solid #ccc;
     border-radius: 6px;
     resize: vertical;
     font-family: 'Inter', sans-serif;
     transition: border-color 0.2s;
     background: none;
    }

   textarea:focus {
     outline: none;
     border-color: #007bff;
     box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

    /* Dark mode textarea */
    body.dark-mode textarea {
     background: #2c2c2c;
     color: #eee;
     border-color: #444;
    }

   body.dark-mode textarea:focus {
     border-color: #007bff;
     box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

  </style>
</head>
<body>
  <div class="container">
    <span class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
      <i class="fa-solid fa-moon"></i>
    </span>
    <h2>Add New Book</h2>
    <form id="addBookForm" action="add_bookprocess.php" method="POST" enctype="multipart/form-data">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" required>

      <label for="author">Author</label>
      <input type="text" id="author" name="author" required>

      <label for="price">Price (Rs.)</label>
      <input type="number" id="price" name="price" required min="1">

      <label for="genre">Genre</label>
      <select id="genre" name="genre" required>
        <option value="">Select Genre</option>
        <option value="thriller">Thriller</option>
        <option value="suspense">Suspense</option>
        <option value="fantasy">Fantasy</option>
        <option value="science fiction">Science Fiction</option>
        <option value="non-fiction">Non-fiction</option>
        <option value="romance">Romance</option>
        <option value="mystery">Mystery</option>
        <option value="self-help">Self-Help</option>
        <option value="biography">Biography</option>
        <option value="poetry">Poetry</option>
        <option value="classic literature">Classic Literature</option>
        <option value="adventure">Adventure</option>
      </select>

      <label>Book Image</label>
      <div class="image-options">
        <label><input type="radio" name="imageType" value="url" checked> Use Image URL</label>
        <label><input type="radio" name="imageType" value="upload"> Upload from Device</label>
      </div>

      <div class="image-inputs" id="urlInput">
        <input type="url" id="imageUrl" name="imageUrl" placeholder="Enter image URL">
      </div>

      <div class="image-inputs" id="fileInput" style="display: none;">
        <input type="file" id="imageFile" name="imageFile" accept="image/*">
      </div>

      <label for="bookDesc">About Book</label>
      <textarea id="bookDesc" name="bookDesc" rows="4" required placeholder="Write at least 50 words about the book..."></textarea>

      <label for="authorDesc">About Author</label>
      <textarea id="authorDesc" name="authorDesc" rows="4" required placeholder="Write at least 50 words about the author..."></textarea>
      <button type="submit" class="btn-submit">Add Book</button>
    </form>
  </div>
  <!-- Success Message Modal -->
  <div id="successModal" class="modal">
    <div class="modal-content">
      <p>Book added successfully!</p>
      <button onclick="closeModal()">Close</button>
    </div>
  </div>
  <script>
    const urlInput = document.getElementById("urlInput");
    const fileInput = document.getElementById("fileInput");
    const imageTypeRadios = document.querySelectorAll("input[name='imageType']");
    const successModal = document.getElementById("successModal");

    imageTypeRadios.forEach(radio => {
      radio.addEventListener("change", function () {
        if (this.value === "url") {
          urlInput.style.display = "block";
          fileInput.style.display = "none";
        } else {
          urlInput.style.display = "none";
          fileInput.style.display = "block";
        }
      });
    });

//     document.getElementById("addBookForm").addEventListener("submit", function(e) {
//   e.preventDefault();

  // Collect form data
  const title = document.getElementById("title").value;
  const author = document.getElementById("author").value;
  const price = document.getElementById("price").value;
  const genre = document.getElementById("genre").value;
  const bookDesc = document.getElementById("bookDesc").value.trim();
const authorDesc = document.getElementById("authorDesc").value.trim();

const countWords = (text) => {
  return text.trim().match(/\b\w+\b/g)?.length || 0;
};

const bookWords = countWords(bookDesc);
const authorWords = countWords(authorDesc);

// if (bookWords < 50 || authorWords < 50) {
//   alert("Both descriptions must be at least 50 words.");
//   return;
// }

//   const imageUrl = document.querySelector("input[name='imageType']:checked").value === "url" ?
//     document.getElementById("imageUrl").value :
//     URL.createObjectURL(document.getElementById("imageFile").files[0]);

//   const newBook = {
//     title,
//     author,
//     price,
//     genre,
//     imageUrl,
//     bookDesc,
//     authorDesc
//   };
  //successModal.style.display = "flex";
  document.getElementById("addBookForm").reset();
const successModal = document.getElementById("successModal");
  <?php if ($showSuccess): ?>
    successModal.style.display = "flex";
  <?php endif; ?>

  function closeModal() {
    successModal.style.display = "none";
  }
  </script>
  <script>

  function toggleTheme() {
      document.body.classList.toggle("dark-mode");
      
    // Optional: Save preference
    if (document.body.classList.contains("dark-mode")) {
      localStorage.setItem("theme", "dark");
    } else {
      localStorage.setItem("theme", "light");
    }
    }
      // 👇 Auto-apply saved preference
  window.onload = function () {
    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark-mode");
    }

    <?php if ($showSuccess): ?>
      successModal.style.display = "flex";
    <?php endif; ?>
  };
  </script>
</body>
</html>
