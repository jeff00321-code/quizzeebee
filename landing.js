/* ════════════════════════════════════════════
   QuizzyBee 5Q — landing.js (FIXED)
   Key fixes:
     Bug 1: openModal/closeModal now use classList, not inline style
            (.hidden has !important so inline style was always overridden)
     Bug 5: After successful login, redirect to dashboard.html
════════════════════════════════════════════ */

/* ── Navbar scroll ── */
window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', scrollY > 20);
});

/* ── Hamburger ── */
document.getElementById('burger').onclick = () => {
  document.getElementById('mobileNav').classList.toggle('open');
};

/* ─────────────────────────────────────────────
   BUG 1 FIX — Modal open/close
   PROBLEM: openModal() was setting style.display='flex'
            but .hidden { display:none!important } always won.
   FIX:    Use classList.remove('hidden') / classList.add('hidden')
           so the CSS cascade works correctly.
───────────────────────────────────────────── */
function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}

// Close modal when clicking the dark backdrop outside the box
window.addEventListener('click', function(event) {
  document.querySelectorAll('.modal').forEach(modal => {
    // Only close if the click was directly on the modal overlay, not on its content
    if (event.target === modal) {
      modal.classList.add('hidden');
    }
  });
});

/* ── Hero mini quiz ── */
function heroTap(el, correct) {
  const all = document.querySelectorAll('.mini-opt');
  all.forEach(o => o.disabled = true);
  const res = document.getElementById('miniResult');

  if (correct) {
    el.classList.add('correct');
    res.textContent = '🎉 Correct! Great job!';
    res.style.color = '#52C46A';
  } else {
    el.classList.add('wrong');
    res.textContent = '💛 Try again!';
    res.style.color = '#FFAA00';
    setTimeout(() => {
      el.classList.remove('wrong');
      all.forEach(o => o.disabled = false);
      res.textContent = '';
    }, 900);
  }
}

/* ════════════════════════════════════════════
   DEMO QUIZ — exactly 5 questions each
   (This is frontend-only; no DB needed for the demo)
════════════════════════════════════════════ */
const QUIZ_DATA = {
  animals: [
    { q: 'Which one is a CAT? 🐱',      correct: '🐱', opts: ['🐱','🐶','🐢','🐥'],  labels: ['Cat','Dog','Turtle','Chick'] },
    { q: 'Which one is an ELEPHANT?',   correct: '🐘', opts: ['🦁','🐯','🐘','🦊'],  labels: ['Lion','Tiger','Elephant','Fox'] },
    { q: 'Which one is a RABBIT? 🐰',   correct: '🐰', opts: ['🐸','🐰','🦆','🐻'],  labels: ['Frog','Rabbit','Duck','Bear'] },
    { q: 'Which one is a FISH? 🐟',     correct: '🐟', opts: ['🐧','🐟','🦋','🐌'],  labels: ['Penguin','Fish','Butterfly','Snail'] },
    { q: 'Which one is a MONKEY? 🐒',   correct: '🐒', opts: ['🐒','🐮','🐑','🦒'],  labels: ['Monkey','Cow','Sheep','Giraffe'] },
  ],
  colors: [
    { q: 'Which color is RED? ❤️',      correct: '🔴', opts: ['🔵','🔴','🟢','🟡'],  labels: ['Blue','Red','Green','Yellow'] },
    { q: 'Which color is BLUE? 💙',     correct: '🔵', opts: ['🟠','🟢','🔵','🟣'],  labels: ['Orange','Green','Blue','Purple'] },
    { q: 'Which color is YELLOW? 💛',   correct: '🟡', opts: ['🟡','🟤','⚫','🔴'],  labels: ['Yellow','Brown','Black','Red'] },
    { q: 'Which color is GREEN? 💚',    correct: '🟢', opts: ['🔵','🟢','🟠','🟣'],  labels: ['Blue','Green','Orange','Purple'] },
    { q: 'Which color is ORANGE? 🧡',   correct: '🟠', opts: ['🔴','🟡','🟠','🟢'],  labels: ['Red','Yellow','Orange','Green'] },
  ],
  shapes: [
    { q: 'Which one is a CIRCLE? ⭕',   correct: '⭕', opts: ['⭕','🔺','⬛','🔷'],  labels: ['Circle','Triangle','Square','Diamond'] },
    { q: 'Which one is a STAR? ⭐',     correct: '⭐', opts: ['🔶','⭐','⬜','🔸'],  labels: ['Diamond','Star','Square','Small Diamond'] },
    { q: 'Which one is a TRIANGLE? 🔺', correct: '🔺', opts: ['⬛','🔵','🔺','🔷'],  labels: ['Square','Circle','Triangle','Diamond'] },
    { q: 'Which one is a HEART? ❤️',    correct: '❤️', opts: ['❤️','⭐','⬜','🔴'],  labels: ['Heart','Star','Square','Circle'] },
    { q: 'Which one is a SQUARE? ⬛',   correct: '⬛', opts: ['🔵','🔺','⬛','⭐'],  labels: ['Circle','Triangle','Square','Star'] },
  ],
  numbers: [
    { q: 'Which shows the number ONE? 1️⃣',   correct: '1️⃣', opts: ['1️⃣','2️⃣','3️⃣','4️⃣'],  labels: ['1','2','3','4'] },
    { q: 'Which shows the number TWO? 2️⃣',   correct: '2️⃣', opts: ['5️⃣','2️⃣','3️⃣','4️⃣'],  labels: ['5','2','3','4'] },
    { q: 'Which shows the number THREE? 3️⃣', correct: '3️⃣', opts: ['1️⃣','2️⃣','3️⃣','5️⃣'],  labels: ['1','2','3','5'] },
    { q: 'Which shows the number FOUR? 4️⃣',  correct: '4️⃣', opts: ['4️⃣','3️⃣','2️⃣','1️⃣'],  labels: ['4','3','2','1'] },
    { q: 'Which shows the number FIVE? 5️⃣',  correct: '5️⃣', opts: ['2️⃣','5️⃣','1️⃣','4️⃣'],  labels: ['2','5','1','4'] },
  ],
  fruits: [
    { q: 'Which one is an APPLE? 🍎',   correct: '🍎', opts: ['🍎','🍌','🍇','🍓'],  labels: ['Apple','Banana','Grapes','Strawberry'] },
    { q: 'Which one is a BANANA? 🍌',   correct: '🍌', opts: ['🍊','🍌','🍋','🍑'],  labels: ['Orange','Banana','Lemon','Peach'] },
    { q: 'Which one is a STRAWBERRY?',  correct: '🍓', opts: ['🍒','🍓','🍇','🍈'],  labels: ['Cherry','Strawberry','Grapes','Melon'] },
    { q: 'Which one is an ORANGE? 🍊',  correct: '🍊', opts: ['🍋','🍊','🍎','🍌'],  labels: ['Lemon','Orange','Apple','Banana'] },
    { q: 'Which one is a WATERMELON?',  correct: '🍉', opts: ['🍉','🍇','🍑','🥭'],  labels: ['Watermelon','Grapes','Peach','Mango'] },
  ],
};

const TOTAL_Q    = 5;
let currentTopic = 'animals';
let currentIndex = 0;
let score        = 0;
let answered     = false;

function setTopic(topic, btn) {
  document.querySelectorAll('.topic-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentTopic = topic;
  resetDemo();
}

function resetDemo() {
  currentIndex = 0;
  score        = 0;
  answered     = false;
  document.getElementById('quizDone').classList.add('hidden');
  document.getElementById('quizBox').classList.remove('hidden');
  renderQuestion();
}

function renderQuestion() {
  const data = QUIZ_DATA[currentTopic];
  const q    = data[currentIndex];

  const pct = (currentIndex / TOTAL_Q) * 100;
  document.getElementById('progFill').style.width = pct + '%';
  for (let i = 0; i < TOTAL_Q; i++) {
    const bee = document.getElementById('pb' + i);
    if (bee) bee.classList.toggle('done', i <= currentIndex);
  }
  document.getElementById('qbCounter').textContent = `Question ${currentIndex + 1} of ${TOTAL_Q}`;
  document.getElementById('qbQuestion').textContent  = q.q;
  document.getElementById('qbFeedback').textContent  = '';

  const indices = shuffle([0, 1, 2, 3]);
  const opts    = document.getElementById('qbOpts');
  opts.innerHTML = '';
  answered = false;

  indices.forEach(i => {
    const btn       = document.createElement('button');
    btn.className   = 'qb-opt';
    btn.innerHTML   = `<span style="font-size:46px">${q.opts[i]}</span><span class="opt-label">${q.labels[i]}</span>`;
    btn.dataset.emoji = q.opts[i];
    btn.onclick     = () => handleAnswer(btn, q.opts[i] === q.correct, opts);
    opts.appendChild(btn);
  });
}

function handleAnswer(el, isCorrect, container) {
  if (answered) return;
  answered = true;

  const fb  = document.getElementById('qbFeedback');
  const all = container.querySelectorAll('.qb-opt');
  all.forEach(o => o.classList.add('disabled'));

  if (isCorrect) {
    el.classList.add('correct');
    score++;
    fb.textContent = pickPraise();
    fb.style.color = '#52C46A';
    setTimeout(() => {
      currentIndex++;
      if (currentIndex >= TOTAL_Q) {
        showCompletion();
      } else {
        renderQuestion();
      }
    }, 1100);
  } else {
    el.classList.add('wrong');
    fb.textContent = '💛 Keep trying — you can do it!';
    fb.style.color = '#FFAA00';
    setTimeout(() => {
      el.classList.remove('wrong');
      all.forEach(o => o.classList.remove('disabled'));
      answered = false;
      fb.textContent = '';
    }, 900);
  }
}

function pickPraise() {
  const p = ['🎉 Amazing!','⭐ You got it!','🌟 Super smart!','🏆 Brilliant!','💛 Correct! Yay!'];
  return p[Math.floor(Math.random() * p.length)];
}

function showCompletion() {
  document.getElementById('quizBox').classList.add('hidden');
  document.getElementById('progFill').style.width = '100%';
  for (let i = 0; i < TOTAL_Q; i++) {
    const b = document.getElementById('pb' + i);
    if (b) b.classList.add('done');
  }

  const pct   = Math.round((score / TOTAL_Q) * 100);
  const stars = pct === 100 ? '⭐⭐⭐⭐⭐'
              : pct >= 80   ? '⭐⭐⭐⭐'
              : pct >= 60   ? '⭐⭐⭐'
              : '⭐⭐';
  const title = pct === 100 ? 'Perfect Score! 🏆'
              : pct >= 80   ? 'Superstar! 🌟'
              : 'Great Try! 💛';
  const msg   = pct === 100
    ? 'You got all 5 right! You\'re a genius!'
    : `You got ${score} out of 5 right! Keep practising!`;

  document.getElementById('doneTitle').textContent = title;
  document.getElementById('doneScore').textContent = `${score}/5`;
  document.getElementById('doneMsg').textContent   = msg;
  document.getElementById('doneStars').textContent = stars;
  spawnConfetti();
  document.getElementById('quizDone').classList.remove('hidden');
}

function restartDemo() {
  currentIndex = 0;
  score        = 0;
  answered     = false;
  document.getElementById('quizDone').classList.add('hidden');
  document.getElementById('quizBox').classList.remove('hidden');
  renderQuestion();
}

function spawnConfetti() {
  const container = document.getElementById('doneConfetti');
  container.innerHTML = '';
  const colors = ['#FFAA00','#FF6B6B','#52C46A','#C9B8FF','#00B8A9','#FFD700'];
  for (let i = 0; i < 60; i++) {
    const p = document.createElement('div');
    p.className   = 'confetti-piece';
    p.style.cssText = `
      left:${Math.random()*100}%;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      width:${6+Math.random()*8}px;
      height:${6+Math.random()*8}px;
      border-radius:${Math.random()>.5?'50%':'2px'};
      animation-duration:${1.5+Math.random()*2}s;
      animation-delay:${Math.random()*.8}s;
    `;
    container.appendChild(p);
  }
}

function shuffle(arr) {
  const a = [...arr];
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}

/* ── Email lead capture ── */
function submitLead() {
  const email = document.getElementById('leadEmail').value.trim();
  if (!email || !email.includes('@')) {
    alert('Please enter a valid email 📧');
    return;
  }
  const form = document.getElementById('leadForm');
  form.innerHTML = `
    <div style="color:#fff;font-family:'Boogaloo',cursive;font-size:22px;padding:14px 0;text-align:center">
      🎁 Check your inbox! Free quiz pack is on its way!
    </div>`;
  fetch('php/subscribe.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email })
  }).catch(() => {});
}

/* ── Init on load ── */
document.addEventListener('DOMContentLoaded', () => {
  renderQuestion();
  initForms();
});

/* ─────────────────────────────────────────────
   initForms() — called by DOMContentLoaded above
   Guarantees loginForm & signupForm exist in the DOM
   no matter where <script> sits in the HTML.
───────────────────────────────────────────── */
function initForms() {

/* ── LOGIN FORM ── */
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  const loginMessageDiv = document.getElementById('loginMessage');

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(loginForm);
    const btn      = loginForm.querySelector('button[type="submit"]');
    btn.disabled   = true;
    btn.textContent = 'Logging in…';

    try {
      const response = await fetch('php/auth.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.status === 'success') {
        loginMessageDiv.textContent = '✅ Login successful! Redirecting…';
        loginMessageDiv.className   = 'message success';
        loginForm.reset();
        // BUG 5 FIX: Redirect to dashboard after 1 second
        setTimeout(() => {
          window.location.href = result.redirect || 'dashboard.html';
        }, 1000);
      } else {
        loginMessageDiv.textContent = '⚠️ ' + (result.message || 'Invalid credentials');
        loginMessageDiv.className   = 'message error';
        btn.disabled   = false;
        btn.textContent = 'Log In 🚀';
      }

    } catch (err) {
      loginMessageDiv.textContent = '⚠️ Server error. Please try again.';
      loginMessageDiv.className   = 'message error';
      btn.disabled   = false;
      btn.textContent = 'Log In 🚀';
    }
  });
}

/* ── SIGNUP FORM AJAX ── */
const signupForm = document.getElementById('signupForm');
if (signupForm) {
  const signupMessageDiv = document.getElementById('signupMessage');

  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(signupForm);
    const btn      = signupForm.querySelector('button[type="submit"]');
    btn.disabled   = true;
    btn.textContent = 'Creating account…';

    try {
      const response = await fetch('php/auth.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.status === 'success') {
        signupMessageDiv.textContent = '🎉 Account created! Please log in.';
        signupMessageDiv.className   = 'message success';
        signupForm.reset();
        // Switch to login modal after 1.5 seconds
        setTimeout(() => {
          closeModal('signupModal');
          openModal('loginModal');
        }, 1500);
      } else {
        signupMessageDiv.textContent = '⚠️ ' + (result.message || 'Something went wrong');
        signupMessageDiv.className   = 'message error';
        btn.disabled   = false;
        btn.textContent = 'Sign Up 🚀';
      }

    } catch (err) {
      signupMessageDiv.textContent = '⚠️ Server error. Please try again.';
      signupMessageDiv.className   = 'message error';
      btn.disabled   = false;
      btn.textContent = 'Sign Up 🚀';
    }
  });
}

}