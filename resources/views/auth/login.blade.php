<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HRM - Login</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
      body {
           background-image: url('{{ asset('images/login.jpeg') }}'); 
          height: 90vh;
          display: flex;
          justify-content: center;
          align-items: center;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      .login-container {
          max-width: 420px;
          width: 100%;
          padding: 40px 30px;
          background: #fff;
          border-radius: 20px;
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
          animation: fadeIn 0.8s ease-in-out;
      }
      .login-title {
          text-align: center;
          margin-bottom: 25px;
          font-weight: 700;
          font-size: 28px;
          color: #2575fc;
      }
      .form-group label {
          font-weight: 500;
          color: #444;
      }
      .form-control {
          border-radius: 30px;
          padding: 12px 18px;
          border: 1px solid #ddd;
          transition: 0.3s ease;
      }
      .form-control:focus {
          border-color: #2575fc;
          box-shadow: 0 0 8px rgba(37, 117, 252, 0.2);
      }
      .btn-primary {
          border-radius: 30px;
          padding: 12px;
          background: linear-gradient(135deg, #6a11cb, #2575fc);
          border: none;
          font-size: 16px;
          font-weight: 600;
          transition: transform 0.2s ease, box-shadow 0.3s ease;
      }
      .btn-primary:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 15px rgba(37, 117, 252, 0.4);
      }
      .text-center a {
          color: #6a11cb;
          font-weight: 500;
      }
      .text-center a:hover {
          text-decoration: underline;
      }
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(-20px); }
          to { opacity: 1; transform: translateY(0); }
      }
  </style>
</head>
<body>

<div class="login-container">
  <h2 class="login-title">Welcome Back</h2>
  <form id="login-form" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
      <div class="text-center mt-3">
          <a href="{{ route('password.request') }}">Forgot Password?</a>
      </div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
