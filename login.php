<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - BookStore</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #e3f2fd, #f1f8e9, #e0f7fa, #fff3e0);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
      transition: background 0.3s;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-container {
      background: #ffffff;
      padding: 3rem 2.5rem;
      border-radius: 18px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
      width: 100%;
      max-width: 420px;
      transition: all 0.3s ease-in-out;
      position: relative;
    }

    .login-container:hover {
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

    .form-group {
      position: relative;
      margin-top: 1.5rem;
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #666;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem 0.75rem 0.75rem 2.5rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 10px;
      background: none;
      transition: border-color 0.2s;
    }

    .form-group input:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

    .form-group label {
      position: absolute;
      top: 50%;
      left: 2.5rem;
      transform: translateY(-50%);
      background: #fff;
      padding: 0 5px;
      color: #777;
      pointer-events: none;
      transition: 0.2s;
    }

    .form-group input:focus + label,
    .form-group input:not(:placeholder-shown) + label {
      top: -8px;
      left: 12px;
      font-size: 0.75rem;
      color: #007bff;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
    }

    button {
      width: 100%;
      margin-top: 2rem;
      padding: 0.9rem;
      font-size: 1rem;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #0056b3;
    }

    .signup-link {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.95rem;
      color: #666;
    }

    .signup-link a {
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
    }

    .signup-link a:hover {
      text-decoration: underline;
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

    body.dark-mode .login-container {
      background: #1e1e1e;
      color: #eee;
    }

    body.dark-mode .form-group input {
      background: #2c2c2c;
      color: #eee;
      border-color: #444;
    }

    body.dark-mode .form-group label {
      background: #1e1e1e;
      color: #aaa;
    }

    body.dark-mode .toggle-password,
    body.dark-mode .form-group i {
      color: #aaa;
    }

    body.dark-mode .signup-link,
    body.dark-mode .signup-link a {
      color: #ccc;
    }

    body.dark-mode button {
      background: #0062cc;
    }

    body.dark-mode button:hover {
      background: #004a99;
    }
    .error-message {
      color: red;
      font-size: 1rem;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <span class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
      <i class="fa-solid fa-moon"></i>
    </span>
    <h2>Welcome Back</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="error-message">
        <?php echo $_SESSION['error_message']; ?>
        <?php unset($_SESSION['error_message']); ?>
      </div>
    <?php endif; ?>
    
    <form action="login_process.php" method="post">
      <div class="form-group">
        <i class="fa fa-envelope"></i>
        <input type="email" id="email" name="email" required placeholder=" " />
        <label for="email">Email Address</label>
      </div>

      <div class="form-group">
        <i class="fa fa-lock"></i>
        <input type="password" id="password" name="password" required placeholder=" " />
        <label for="password">Password</label>
        <span class="toggle-password" onclick="togglePassword()">
          <i class="fa fa-eye" id="eyeIcon"></i>
        </span>
      </div>

      <button type="submit">Login</button>
    </form>

    <div class="signup-link">
      New to BookStore? <a href="signup.html">Create an account</a>
    </div>
  </div>

  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      const icon = document.getElementById("eyeIcon");
      if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        password.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    function toggleTheme() {
      document.body.classList.toggle("dark-mode");
    }
  </script>
</body>
</html>
