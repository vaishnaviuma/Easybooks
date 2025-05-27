<?php
session_start();
if (!isset($_SESSION["redirect_target"])) {
    header("Location: login.php");
    exit();
}
$redirectPage = $_SESSION["redirect_target"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging In...</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #e3f2fd, #f1f8e9, #e0f7fa, #fff3e0);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
      text-align: center;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    h2 {
      font-size: 2rem;
      color: #333;
      margin-bottom: 20px;
    }

    p {
      font-size: 1rem;
      color: #444;
      margin-top: 10px;
    }
  </style>
  <script>
    setTimeout(function () {
      window.location.href = "<?php echo $redirectPage; ?>";
    }, 3000); // 3 seconds
  </script>
</head>
<body>

  <lottie-player 
    src="https://lottie.host/6f0661c5-1c84-4764-bf59-74804b2f20cf/4FO3weCCH4.json"  
    background="transparent" 
    speed="1"  
    style="width: 450px; height: 450px;"  
    loop  
    autoplay>
  </lottie-player>
  <p>Hold on tight... Redirecting you to our dashboard!</p>
</body>
</html>
