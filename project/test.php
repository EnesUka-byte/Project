<?php
session_start();  // Start the session

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info from the database
require_once 'db.php'; // assuming this creates a $pdo variable

$user_id = $_SESSION['user_id'];
$username = null;
$user_score = 0; // default 0 if no score in DB

$stmt = $pdo->prepare("SELECT username, score FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $username = $user['username'];
    // If score is null or empty, treat as 0
    $user_score = is_numeric($user['score']) ? (int)$user['score'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GreenReminder-Test</title>
  <style>
    /* --- Your full original CSS styles here, unchanged --- */
    body {
      margin: 0;
      background-color: #ddffaa;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cpolygon fill='%23AE9' fill-opacity='0.57' points='120 120 60 120 90 90 120 60 120 0 120 0 60 60 0 0 0 60 30 90 60 120 120 120 '/%3E%3C/svg%3E");
      font-family: Arial, sans-serif;
      color: white;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
      position: relative;
    }

    h1.title {
      font-size: 80px;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
      text-align: center;
      margin: 50px 0 40px 0;
      padding: 20px 40px;
      background: rgba(0, 80, 0, 0.7);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 100, 0, 0.7);
      user-select: none;
    }

    #profile-top {
      position: fixed;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 100, 0, 0.85);
      border-radius: 12px;
      padding: 12px 25px;
      box-shadow: 0 6px 20px rgba(0, 80, 0, 0.7);
      font-size: 1.4rem;
      display: flex;
      align-items: center;
      gap: 12px;
      user-select: none;
      color: #d4f4d4;
      z-index: 1000;
      min-width: 250px;
      justify-content: center;
      font-weight: 600;
    }

    #profile-square {
      position: fixed;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      width: 70px;
      height: 70px;
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
    }
    #profile-square span {
      font-size: 1.1rem;
      margin-top: 6px;
      font-weight: 500;
      color: #a6dba6;
    }

    .question-block {
      width: 100%;
      max-width: 600px;
      background: rgba(0, 0, 0, 0.65);
      padding: 30px 40px 40px 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
      margin-bottom: 60px;
      text-align: center;
      position: relative;
      color: white;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
      /* Show questions by default */
      opacity: 1 !important;
      transform: translateY(0) !important;
      transition: none !important;
    }

    .image-placeholder {
      width: 100%;
      height: 300px;
      overflow: hidden;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: inset 0 0 15px rgba(255, 255, 255, 0.2);
      position: relative;
      background: rgba(255 255 255 / 0.1);
    }

    .image-placeholder img {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 12px;
      display: block;
    }

    .question-text {
      font-size: 1.4rem;
      margin-bottom: 30px;
      font-weight: 600;
      user-select: none;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 40px;
    }

    .choice-button {
      cursor: pointer;
      padding: 14px 48px;
      font-size: 1.2rem;
      font-weight: bold;
      border-radius: 30px;
      border: none;
      color: white;
      background: linear-gradient(90deg, #4caf50 0%, #81c784 100%);
      box-shadow: 0 5px 15px rgba(72, 180, 97, 0.6);
      transition: background-color 0.4s ease, color 0.4s ease;
      user-select: none;
      position: relative;
      overflow: hidden;
    }

    .choice-button::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, #81c784, #4caf50);
      transform: translateX(-100%);
      transition: transform 0.4s ease;
      z-index: 0;
      border-radius: 30px;
    }

    .choice-button:hover {
      background: linear-gradient(90deg, #81c784 0%, #4caf50 100%);
      color: #004d00;
      box-shadow: 0 6px 18px rgba(34, 100, 34, 0.8);
    }

    .choice-button.selected {
      background: #1e7e34 !important;
      box-shadow: 0 6px 18px rgba(30, 126, 52, 0.9);
      color: #d0f0c0 !important;
    }

    .choice-button > * {
      position: relative;
      z-index: 2;
    }

    #backBtn {
      padding: 18px 50px;
      font-size: 1.5rem;
      font-weight: bold;
      border-radius: 30px;
      border: none;
      cursor: pointer;
      color: white;
      background: #2e7d32;
      box-shadow: 0 8px 20px rgba(46, 125, 50, 0.7);
      transition: background-color 0.3s ease;
      margin-bottom: 60px;
      user-select: none;
    }
    #backBtn:hover {
      background-color: #4caf50;
    }

    #finishBtn {
      padding: 18px 50px;
      font-size: 1.5rem;
      font-weight: bold;
      border-radius: 30px;
      border: none;
      cursor: not-allowed;
      color: #ddd;
      background: #999;
      box-shadow: none;
      margin-bottom: 60px;
      user-select: none;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    #finishBtn.enabled {
      cursor: pointer;
      color: white;
      background: #2e7d32;
      box-shadow: 0 8px 20px rgba(46, 125, 50, 0.7);
    }
    #finishBtn.enabled:hover {
      background-color: #4caf50;
    }

    #resultBox {
      max-width: 600px;
      background: rgba(0,0,0,0.7);
      color: #d4f4d4;
      font-size: 1.3rem;
      text-align: center;
      padding: 25px 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.7);
      margin-bottom: 40px;
      user-select: none;
      display: none;
    }

    @media (max-width: 640px) {
      h1.title {
        font-size: 50px;
        padding: 15px 25px;
      }
      .question-block {
        padding: 20px 25px 30px 25px;
      }
      .question-text {
        font-size: 1.1rem;
      }
      .choice-button {
        padding: 12px 36px;
        font-size: 1rem;
      }
      .buttons {
        gap: 20px;
      }
      #backBtn, #finishBtn {
        width: 100%;
        padding: 16px 0;
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

  <!-- Profile at top center -->
  <div id="profile-top">
    <span id="profile-emoji-top">🌿</span>
    <span id="profile-text-top">Your Score: <?= $user_score ?>/10</span>
  </div>

  <h1 class="title">GreenReminder-Test</h1>

  <a href="index.php">
    <button id="backBtn">Back</button>
  </a>

  <!-- Questions -->
<div id="questions-container"></div>

<button id="finishBtn" disabled>Finish</button>


<script>
  const allQuestions = [
    { text: "Did you plant or water any plants today?", image: "images/16.png" },
    { text: "Did you recycle any materials today?", image: "images/32.png" },
    { text: "Did you avoid using disposable plastics today?", image: "images/6.png" },
    { text: "Did you walk, bike, or use public transport instead of driving?", image: "images/17.png" },
    { text: "Did you conserve water by turning off taps when not in use?", image: "images/36.png" },
    { text: "Did you compost organic waste today?", image: "images/30.png" },
    { text: "Did you use energy-efficient appliances or lights today?", image: "images/34.png" },
    { text: "Did you avoid wasting food today?", image: "images/25.png" },
    { text: "Did you participate in any environmental awareness activities?", image: "images/10.png" },
    { text: "Did you share any eco-friendly tips with friends or family?", image: "images/26.png" },
    { text: "Did you support a local eco-friendly business today?", image: "images/35.png" },
    { text: "Did you avoid fast fashion or buy secondhand clothing today?", image: "images/31.png" },
    { text: "Did you bring your own bags when shopping?", image: "images/31.png" },
    { text: "Did you turn off unused electronics today?", image: "images/27.png" },
    { text: "Did you eat more plant-based food today?", image: "images/25.png" },
    { text: "Did you avoid printing documents unnecessarily today?", image: "images/24.png" },
    { text: "Did you educate someone about climate change today?", image: "images/29.png" },
    { text: "Did you reduce your water use while showering today?", image: "images/36.png" },
    { text: "Did you avoid using your car today?", image: "images/17.png" },
    { text: "Did you clean up any trash or litter in your area today?", image: "images/9.png" }
  ];

  const selectedQuestions = allQuestions
    .sort(() => 0.5 - Math.random())
    .slice(0, 10);

  const userAnswers = new Array(10).fill(null);
  const questionsContainer = document.getElementById('questions-container');
  const finishBtn = document.getElementById('finishBtn');
  const resultBox = document.getElementById('resultBox');
  const scoreText = document.getElementById('profile-text-top');
  const scoreEmoji = document.getElementById('profile-emoji-top');

  // Inject questions into DOM
  selectedQuestions.forEach((q, index) => {
    const section = document.createElement('section');
    section.className = 'question-block';
    section.dataset.index = index;
    section.innerHTML = `
      <div class="image-placeholder">
        <img src="${q.image}" alt="Question image" />
      </div>
      <div class="question-text">${index + 1}. ${q.text}</div>
      <div class="buttons">
        <button class="choice-button" data-value="1">Yes</button>
        <button class="choice-button" data-value="0">No</button>
      </div>
    `;
    questionsContainer.appendChild(section);
  });

  // Enable Finish button if all answered
  function updateFinishButton() {
    const allAnswered = userAnswers.every(ans => ans !== null);
    finishBtn.disabled = !allAnswered;
    finishBtn.classList.toggle('enabled', allAnswered);
  }

  // Emoji score logic
  function updateScoreDisplay(score, total) {
    scoreText.textContent = `Your Score: ${score}/${total}`;
    let emoji = "🌿";
    if (score === total) emoji = "🏆";
    else if (score >= total * 0.8) emoji = "👍";
    else if (score >= total * 0.5) emoji = "🙂";
    else emoji = "😞";
    scoreEmoji.textContent = emoji;
  }

  // Button handler
  function onChoiceClick(event) {
    const btn = event.currentTarget;
    const questionBlock = btn.closest('.question-block');
    const qIndex = Number(questionBlock.dataset.index);
    const value = Number(btn.dataset.value);

    userAnswers[qIndex] = value;

    const buttons = questionBlock.querySelectorAll('.choice-button');
    buttons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    updateFinishButton();
  }

  // Attach to newly created buttons
  setTimeout(() => {
    document.querySelectorAll('.choice-button').forEach(button => {
      button.addEventListener('click', onChoiceClick);
    });
    updateFinishButton();
  }, 50);

  // Submit result
  finishBtn.addEventListener('click', () => {
    const total = userAnswers.length;
    const score = userAnswers.reduce((sum, val) => sum + (val || 0), 0);

    updateScoreDisplay(score, total);

    resultBox.style.display = 'block';
    resultBox.textContent = `You scored ${score} out of ${total}. ${
      score === total ? 'Perfect score! 🌟' :
      score >= total * 0.8 ? 'Great job! 👍' :
      score >= total * 0.5 ? 'Good effort! 🙂' : 'Keep trying! 🌱'
    }`;

    resultBox.scrollIntoView({ behavior: "smooth" });

    // Send to server
    fetch('update_score.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `score=${score}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.href = 'index.php';
      } else {
        alert('Error updating score. Please try again.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('There was an error updating your score.');
    });
  });
</script>


</body>
</html>
