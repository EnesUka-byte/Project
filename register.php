<?php
session_start();
require 'db.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken.";
        } else {
            // Insert new user with current_score and overall_score set to 0
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, current_score, overall_score) VALUES (?, ?, ?, 0, 0)");
            $stmt->execute([$username, $email, $hashed_password]);

            // Auto-login (optional)
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;

            header('Location: index.php');
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>GreenReminder - Register</title>
<style>

html {
  box-sizing: border-box;
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

*, *::before, *::after {
  box-sizing: inherit;
}

body {
  height: 100%;
  margin: 0;
  padding: 20px;
  overflow-x: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #00bb77;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cpolygon fill='%23000' fill-opacity='.1' points='120 0 120 60 90 30 60 0 0 0 0 0 60 60 0 120 60 120 90 90 120 60 120 0'/%3E%3C/svg%3E");
  background-repeat: repeat;
  background-size: 120px 120px;
  box-sizing: border-box;
}

.container {
  max-width: 400px;
  width: 100%;
  padding: 2.5em 3em;
  background: white;
  border-radius: 15px;
  box-shadow: 0 8px 20px rgba(0, 128, 0, 0.15);
  box-sizing: border-box;
  transform: translateZ(0);
}

h1.brand-title {
  margin: 0 0 0.5em 0;
  font-weight: 900;
  font-size: 2.4rem;
  text-align: center;
  color: #2f7a2f;
  letter-spacing: 1.5px;
}

h2 {
  margin-top: 0;
  margin-bottom: 1.5em;
  font-weight: 600;
  font-size: 1.5rem;
  color: #4caf50;
  text-align: center;
}

input, button {
  width: 100%;
  padding: 14px 15px;
  margin-bottom: 1.2em;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 500;
  border: 2px solid #a6dba6;
  box-sizing: border-box;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  outline-offset: 2px;
  line-height: normal;
}

input:focus {
  border-color: #2f7a2f;
  box-shadow: 0 0 8px rgba(47, 122, 47, 0.3);
  outline: none;
}

button {
  background-color: #2f7a2f;
  color: white;
  font-weight: 700;
  border: none;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(47, 122, 47, 0.4);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

button:hover,
button:focus {
  background-color: #1e4e1e;
  box-shadow: 0 8px 20px rgba(30, 78, 30, 0.6);
  outline: none;
}

.error {
  background-color: #ffdddd;
  color: #a70000;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 1.2em;
  box-shadow: 0 2px 8px rgba(255, 0, 0, 0.1);
  font-weight: 600;
  font-size: 0.9rem;
}

.error ul {
  padding-left: 1.2em;
  margin: 0;
}

.error li {
  margin-bottom: 0.4em;
}

/* Responsive */
@media (max-width: 480px) {
  .container {
    padding: 2em 1.5em;
  }
  h1.brand-title {
    font-size: 1.8rem;
  }
  h2 {
    font-size: 1.2rem;
    margin-bottom: 1em;
  }
  input, button {
    padding: 12px 10px;
    font-size: 0.95rem;
  }
}

  

</style>
</head>
<body>

<div class="container">

  <h1 class="brand-title">GreenReminder</h1>
  <h2>Register</h2>

  <?php if (!empty($errors)): ?>
      <div class="error">
          <ul>
              <?php foreach ($errors as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
          </ul>
      </div>
  <?php endif; ?>

  <form method="POST" action="register.php" autocomplete="off">
      <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required />
      <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit">Register</button>
  </form>

</div>

</body>
</html>

