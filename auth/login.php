<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Login - TRAGO Employee Monitoring</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .login-container {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    .app-logo {
      max-width: 100%;
      height: auto;
      margin-bottom: 10px;
    }

    .app-name {
      font-weight: 600;
      font-size: 28px;
      color: #007bff;
      margin-top: 10px;
      margin-bottom: 10px;
      letter-spacing: 1px;
    }

    h5 {
      font-weight: 500;
      margin-bottom: 25px;
    }
  </style>

</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5 login-container text-center">
        <!-- Gambar/logo aplikasi -->
        <img src="../assets/images/TRAGO.png" alt="TRAGO Logo" class="app-logo">


        <!-- Nama aplikasi -->
        <div class="app-name"></div>

        <h5 class="mb-4">Employee Monitoring Login</h5>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <form action="process_login.php" method="POST" class="text-left">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>