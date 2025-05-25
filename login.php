<?php
session_start();
require_once 'db.php';

$errors = [];
$login = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login)) {
        $errors[] = "Username or Email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Determine if login is email or username
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE email = :login LIMIT 1");
        } else {
            $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = :login LIMIT 1");
        }

        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Invalid username/email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>GreenReminder - Login</title>
<style>
html {
  box-sizing: border-box;
  height: 100%;
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
  padding: 0;
  overflow-x: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #00bb77;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cpolygon fill='%23000' fill-opacity='.1' points='120 0 120 60 90 30 60 0 0 0 0 0 60 60 0 120 60 120 90 90 120 60 120 0'/%3E%3C/svg%3E");
  padding: 20px;
  box-sizing: border-box;
}

.container {
  max-width: 400px;
  width: 100%;
  padding: 2.5em 3em;
  background: white;
  border-radius: 15px;
  box-shadow: 0 8px 15px rgba(0, 128, 0, 0.2);
  transform: translateZ(0);
}

h1.brand-title {
  margin: 0 0 0.5em;
  font-weight: 900;
  font-size: 2.4rem;
  text-align: center;
  color: #2f7a2f;
  letter-spacing: 1.5px;
}

h2 {
  margin: 0 0 1.5em;
  font-weight: 600;
  font-size: 1.5rem;
  color: #4caf50;
  text-align: center;
}

label {
  display: block;
  margin-bottom: 0.3em;
  font-weight: 600;
  color: #2f7a2f;
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

.register-link {
  display: block;
  text-align: center;
  margin-top: 1em;
  font-size: 0.95rem;
  color: #2f7a2f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.register-link:hover,
.register-link:focus {
  color: #1e4e1e;
  outline: none;
}

/* Accessibility helper */
.sr-only {
  position: absolute !important;
  width: 1px !important;
  height: 1px !important;
  padding: 0 !important;
  margin: -1px !important;
  overflow: hidden !important;
  clip: rect(0,0,0,0) !important;
  border: 0 !important;
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
  .register-link {
    font-size: 0.9rem;
  }
}

</style>
</head>
<body>
  <div class="container" role="main" aria-labelledby="login-title">
    <h1 id="login-title" class="brand-title">GreenReminder</h1>
    <h2>Login to your account</h2>

    <?php if ($errors): ?>
      <div class="error" role="alert" aria-live="assertive">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?=htmlspecialchars($error)?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate autocomplete="off" aria-describedby="login-desc">
      <label for="login">Username or Email</label>
      <input
        type="text"
        id="login"
        name="login"
        value="<?= htmlspecialchars($login ?? '') ?>"
        required
        autofocus
        aria-required="true"
      />
      
      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        aria-required="true"
      />

      <button type="submit">Log In</button>
    </form>

    <a class="register-link" href="register.php">Don't have an account? Register</a>
  </div>
</body>
</html>
