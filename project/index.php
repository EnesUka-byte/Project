<?php
session_start();
require_once 'db.php'; // Your DB connection file

$username = null;
$current_score = null;
$overall_score = null;
$user_rank = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch username, current_score, overall_score
    $stmt = $pdo->prepare("SELECT username, current_score, overall_score FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $username = $user['username'];
        $current_score = (int)$user['current_score'];
        $overall_score = (int)$user['overall_score'];

        // Calculate user rank by counting how many users have higher overall_score
        $stmt = $pdo->prepare("SELECT COUNT(*) AS rank FROM users WHERE overall_score > :overall_score");
        $stmt->execute(['overall_score' => $overall_score]);
        $rank_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_rank = $rank_data['rank'] + 1; // rank starts at 1
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Project</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        /* Custom CSS for profile and leaderboard */
        #profile-square {
            position: fixed;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            width: 110px;
            height: auto;
            padding: 10px 0;
            background: rgba(0, 80, 0, 0.9);
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 80, 0, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 2.8rem;
            user-select: none;
            cursor: default;
            color: #d4f4d4;
            font-weight: 700;
            z-index: 1000;
            gap: 4px;
            text-align: center;
        }
        #profile-square span {
            font-size: 1.1rem;
            margin-top: 6px;
            font-weight: 500;
            color: #a6dba6;
            user-select: none;
        }
        #profile-score, #profile-rank {
            font-size: 1.4rem;
            font-weight: 700;
            color: #d4f4d4;
            user-select: none;
            margin-top: 0;
        }
        .auth-btn {
            background-color: #2f7a2f;
            color: #d4f4d4;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            box-shadow: 0 3px 8px rgba(0, 80, 0, 0.7);
            transition: background-color 0.3s ease;
            user-select: none;
        }
		
		html {
  scroll-behavior: smooth;
}

    /* Fade-in CSS */
    .fade-in-section {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
      will-change: opacity, transform;
    }
    .fade-in-section.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
    /* Profile box at right side */
    #profile-square {
      position: fixed;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      width: 110px;
      height: auto;
      padding: 10px 0;
      background: rgba(0, 80, 0, 0.9);
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 80, 0, 0.9);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-size: 2.8rem;
      user-select: none;
      cursor: default;
      color: #d4f4d4;
      font-weight: 700;
      z-index: 1000;
      gap: 4px;
      text-align: center;
    }
    #profile-square span {
      font-size: 1.1rem;
      margin-top: 6px;
      font-weight: 500;
      color: #a6dba6;
      user-select: none;
    }
    #profile-score {
      font-size: 1.4rem;
      font-weight: 700;
      color: #d4f4d4;
      user-select: none;
      margin-top: 0;
    }
    /* Ad boxes rounded borders */
    .Ad {
      border-radius: 12px;
      padding: 15px;
      background-color: #f0fff0;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      display: flex;
      align-items: center;
      gap: 20px;
    }
    /* Motivation container grow on hover (not the image) */
    .motivation-container {
      display: flex;
      align-items: center;
      gap: 15px;
      transition: transform 0.3s ease;
      cursor: pointer;
      transform-origin: center center;
    }
    .motivation-container:hover {
      transform: scale(1.05);
      z-index: 10;
    }
    .motivation-image {
      flex-shrink: 0;
      transition: none !important;
      transform: none !important;
      will-change: auto;
    }
    .motivation-text {
      margin: 0;
      font-size: 1.1rem;
      color: #2f7a2f;
    }
    #auth-buttons {
      position: fixed;
      top: 15px;
      right: 15px;
      z-index: 1100;
      display: flex;
      gap: 10px;
    }
    .auth-btn {
      background-color: #2f7a2f;
      color: #d4f4d4;
      padding: 8px 14px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      box-shadow: 0 3px 8px rgba(0, 80, 0, 0.7);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .auth-btn:hover {
      background-color: #245e24;
    }
	
	.green-glowing-text {
  font-size: 3rem; /* Adjust the size as needed */
  color: #32CD32; /* Bright Green color */
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 2px;
  animation: glowing 1.5s ease-in-out infinite;
}

/* Glowing animation */
@keyframes glowing {
  0% { text-shadow: 0 0 5px #32CD32, 0 0 10px #32CD32, 0 0 15px #32CD32, 0 0 20px #32CD32, 0 0 30px #32CD32; }
 50% { text-shadow: 0 0 10px #32CD32, 0 0 15px #32CD32, 0 0 20px #32CD32, 0 0 25px #32CD32, 0 0 40px #32CD32; }
  100% { text-shadow: 0 0 5px #32CD32, 0 0 10px #32CD32, 0 0 15px #32CD32, 0 0 20px #32CD32, 0 0 30px #32CD32; }
}
  </style>
</head>
<body>

<?php if ($username): ?>
  <div id="profile-square" role="region" aria-label="User profile">
    <span id="profile-emoji-side">🌿</span>
    <span><?= htmlspecialchars($username) ?>'s scores</span>
    <span id="profile-current-score">Current: <?= htmlspecialchars($current_score !== null ? $current_score : '-') ?></span>
    <span id="profile-overall-score">Overall: <?= htmlspecialchars($overall_score !== null ? $overall_score : '-') ?></span>
    <span id="profile-rank">Rank: <?= htmlspecialchars($user_rank !== null ? $user_rank : '-') ?></span>
  </div>
  <div id="auth-buttons">
    <a href="logout.php" class="auth-btn" style="background-color:#b03030;">Sign Out</a>
  </div>
<?php else: ?>
  <div id="auth-buttons">
    <a href="login.php" class="auth-btn">Sign In</a>
    <a href="register.php" class="auth-btn">Sign Up</a>
  </div>
<?php endif; ?>


<!-- Your existing sections here -->
<section class="side-by-side fade-in-section">
  <div class="image-container">
    <img src="images/1.png" alt="Hero Image" />
  </div>
  <div class="text-container">
    <h1 class="green-glowing-text">Green Reminder</h1> <!-- Add this class to your H1 title -->
    <p>
      Our mission is to inspire and motivate users to care for the environment through engaging visuals and meaningful messages. Join us in taking small actions that create a big impact.
    </p>
    <nav class="menu">
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="contact.html">Contact</a>
      <a href="leaderboard.php">Check LeaderBoard</a>
    </nav>
    <a href="#test" style="
      display: inline-block;
      margin-top: 15px;
      padding: 8px 14px;
      background-color: #2f7a2f;
      color: #d4f4d4;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      box-shadow: 0 3px 8px rgba(0, 80, 0, 0.7);
      cursor: pointer;
      user-select: none;
      transition: background-color 0.3s ease;
    " onmouseover="this.style.backgroundColor='#245e24'" onmouseout="this.style.backgroundColor='#2f7a2f'">
      Take The Test
    </a>
  </div>
</section>


<section style="display:flex; justify-content:center; align-items: center;" class="ad fade-in-section">
  <div class="Ad">
    <img style="height:auto; width:420px;" src="images/2.png" alt="Image 2" />
    <p>
      Our website is designed to inspire and motivate users to take an active role in caring for the environment. Through engaging visuals, meaningful messages, and educational content, we emphasize the importance of preserving nature for future generations. Each section of the site highlights simple, impactful actions that individuals can take in their daily lives to reduce waste, conserve resources, and protect wildlife. By promoting awareness and offering practical tips, we aim to empower visitors to make environmentally conscious decisions and become advocates for a greener planet. Our goal is to create a community of like-minded individuals who are passionate about making a positive difference in the world around them.
    </p>
  </div>
</section>

<section style="display:flex; justify-content:center; align-items: center;" class="ad fade-in-section">
  <div style="flex-direction: row-reverse;" class="Ad ">
    <img style="height:auto; width:420px;" src="images/4.png" alt="Image 4" />
    <p style="margin-left:50px;">
      One of the unique features of our website is a daily interactive test that presents users with thought-provoking moral questions related to environmental choices and real-life scenarios. This test is designed not only to engage users but also to help them reflect on their values and attitudes toward nature. At the end of each test, users receive personalized feedback based on their responses, offering encouragement and practical suggestions tailored to their mindset. Whether it's through simple lifestyle changes, community involvement, or deeper personal reflection, the goal is to motivate each individual to take meaningful steps toward protecting the environment. This daily habit fosters a sense of responsibility and growth, turning small decisions into lasting impact.
    </p>
  </div>
</section>

<section style="display:flex; justify-content:center; align-items: center;" class="ad fade-in-section">
  <div class="Ad">
    <img style="height:auto; width:420px;" src="images/3.png" alt="Image 3" />
    <p>
      To make the experience even more engaging, our website features a dynamic score and achievement system that rewards users based on their moral test results. Each time you complete the daily test, you earn points that reflect how thoughtful, responsible, or environmentally conscious your answers are. Over time, these points accumulate, unlocking badges, ranks, and special achievements that highlight your commitment to protecting nature. You can proudly display your scores and accomplishments on your profile, allowing you to compare your progress with friends or other users. It’s a fun and competitive way to stay motivated, build a sense of pride in your efforts, and inspire others to join you in making a positive impact on the environment.
    </p>
  </div>
</section>

<section style="gap:20px;" class="Motivation fade-in-section">
  <div class="motivation-container">
    <img src="images/6.png" alt="Motivational Image" class="motivation-image" />
    <p class="motivation-text">
      Cleaning up the Earth is the first step to healing it — every piece of trash removed is a victory for nature.
    </p>
  </div>

  <div class="motivation-container">
    <img src="images/5.png" alt="Motivational Image" class="motivation-image" />
    <p class="motivation-text">
      Every small action counts — your choices today shape a greener tomorrow. Keep pushing forward and protect our planet with passion!
    </p>
  </div>

  <div class="motivation-container">
    <img src="images/7.png" alt="Motivational Image" class="motivation-image" />
    <p class="motivation-text">
      As pollution clouds our skies and waters, it’s time to act — the planet’s health depends on what we do today
    </p>
  </div>
</section>

<section id="test" class="special-quote-container fade-in-section">
  <img src="images/8.png" alt="Motivational Visual" class="special-quote-image" />
  <p class="special-quote-text">
    "Cleaning up the Earth is the first step to healing it — every piece of trash removed is a victory for nature."
  </p>
  <a href="test.php">
    <button id="takeTestBtn">Take the Test</button>
  </a>
</section>

<!-- Footer -->
<footer style="background-color: #2f7a2f; color: white; text-align: center; padding: 20px 10px; font-family: Verdana, Geneva, Tahoma, sans-serif; font-size: 14px;">
  <p>© 2025 GreenReminder. All rights reserved.</p>
  <p>Created with ❤️ to inspire care for our planet.</p>
</footer>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Fade-in animations for all .fade-in-section elements
    const fadeEls = document.querySelectorAll(".fade-in-section");
    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    fadeEls.forEach((el) => observer.observe(el));

    // Only update emoji if user is logged in (profile-square exists)
    const profileSquare = document.getElementById('profile-square');
    if (!profileSquare) return;

    const profileEmojiSide = document.getElementById('profile-emoji-side');
    const profileScore = document.getElementById('profile-score');

    function getEmojiByScore(score) {
      if(score >= 9) return '🌟';
      if(score >= 7) return '😊';
      if(score >= 4) return '😐';
      if(score >= 1) return '😕';
      return '🌿';
    }

    // Parse score text content to number
    let score = parseInt(profileScore.textContent);
    if (!isNaN(score)) {
      profileEmojiSide.textContent = getEmojiByScore(score);
    }
  });
</script>

</body>
</html>
