-- ════════════════════════════════════════════
--  QuizzyBee — schema.sql
--  FIX: Added utf8mb4 charset for emoji support
--  FIX: Added FK constraints on q1_id–q5_id
--  FIX: Added sample data so the quiz actually works
-- ════════════════════════════════════════════

-- Always create the DB with the right charset
CREATE DATABASE IF NOT EXISTS quizzybee_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE quizzybee_db;

-- ── USERS ──────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100)  NOT NULL,
  email      VARCHAR(255)  NOT NULL UNIQUE,
  password   VARCHAR(255)  NOT NULL,
  created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ── CHILD PROFILES ─────────────────────────
CREATE TABLE IF NOT EXISTS child_profiles (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT          NOT NULL,
  name    VARCHAR(100) NOT NULL,
  age     INT          NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ── CATEGORIES ─────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ── QUESTIONS ──────────────────────────────
-- FIX: correct_answer must match one of the option values exactly
CREATE TABLE IF NOT EXISTS questions (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  category_id    INT          NOT NULL,
  question_text  VARCHAR(255) NOT NULL,
  correct_answer VARCHAR(50)  NOT NULL,
  option1        VARCHAR(50)  NOT NULL,
  option2        VARCHAR(50)  NOT NULL,
  option3        VARCHAR(50)  NOT NULL,
  option4        VARCHAR(50)  NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ── QUIZ SESSIONS ──────────────────────────
-- FIX: Added FK constraints on q1_id–q5_id
CREATE TABLE IF NOT EXISTS quiz_sessions (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  child_id    INT NOT NULL,
  category_id INT NOT NULL,
  q1_id       INT,
  q2_id       INT,
  q3_id       INT,
  q4_id       INT,
  q5_id       INT,
  score       INT       DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (child_id)    REFERENCES child_profiles(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id)     ON DELETE CASCADE,
  FOREIGN KEY (q1_id)       REFERENCES questions(id)       ON DELETE SET NULL,
  FOREIGN KEY (q2_id)       REFERENCES questions(id)       ON DELETE SET NULL,
  FOREIGN KEY (q3_id)       REFERENCES questions(id)       ON DELETE SET NULL,
  FOREIGN KEY (q4_id)       REFERENCES questions(id)       ON DELETE SET NULL,
  FOREIGN KEY (q5_id)       REFERENCES questions(id)       ON DELETE SET NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ── SESSION ANSWERS ────────────────────────
CREATE TABLE IF NOT EXISTS session_answers (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  session_id   INT         NOT NULL,
  question_id  INT         NOT NULL,
  q_position   INT         NOT NULL,
  answer_emoji VARCHAR(50) NOT NULL,   -- FIX: increased size for multi-byte emoji
  is_correct   BOOLEAN     NOT NULL,
  FOREIGN KEY (session_id)  REFERENCES quiz_sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id)     ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ════════════════════════════════════════════
--  SAMPLE DATA — needed so the quiz doesn't crash (Bug 3 fix)
--  At least 5 questions per category required
-- ════════════════════════════════════════════

INSERT IGNORE INTO categories (name) VALUES
  ('Animals'),
  ('Colors'),
  ('Shapes'),
  ('Numbers'),
  ('Fruits');

-- Animals (category 1)
INSERT INTO questions (category_id, question_text, correct_answer, option1, option2, option3, option4) VALUES
  (1, 'Which one is a CAT?',      '🐱', '🐱', '🐶', '🐢', '🐥'),
  (1, 'Which one is an ELEPHANT?','🐘', '🦁', '🐯', '🐘', '🦊'),
  (1, 'Which one is a RABBIT?',   '🐰', '🐸', '🐰', '🦆', '🐻'),
  (1, 'Which one is a FISH?',     '🐟', '🐧', '🐟', '🦋', '🐌'),
  (1, 'Which one is a MONKEY?',   '🐒', '🐒', '🐮', '🐑', '🦒');

-- Colors (category 2)
INSERT INTO questions (category_id, question_text, correct_answer, option1, option2, option3, option4) VALUES
  (2, 'Which color is RED?',    '🔴', '🔵', '🔴', '🟢', '🟡'),
  (2, 'Which color is BLUE?',   '🔵', '🟠', '🟢', '🔵', '🟣'),
  (2, 'Which color is YELLOW?', '🟡', '🟡', '🟤', '⚫', '🔴'),
  (2, 'Which color is GREEN?',  '🟢', '🔵', '🟢', '🟠', '🟣'),
  (2, 'Which color is ORANGE?', '🟠', '🔴', '🟡', '🟠', '🟢');

-- Shapes (category 3)
INSERT INTO questions (category_id, question_text, correct_answer, option1, option2, option3, option4) VALUES
  (3, 'Which one is a CIRCLE?',   '⭕', '⭕', '🔺', '⬛', '🔷'),
  (3, 'Which one is a STAR?',     '⭐', '🔶', '⭐', '⬜', '🔸'),
  (3, 'Which one is a TRIANGLE?', '🔺', '⬛', '🔵', '🔺', '🔷'),
  (3, 'Which one is a HEART?',    '❤️', '❤️', '⭐', '⬜', '🔴'),
  (3, 'Which one is a SQUARE?',   '⬛', '🔵', '🔺', '⬛', '⭐');

-- Numbers (category 4)
INSERT INTO questions (category_id, question_text, correct_answer, option1, option2, option3, option4) VALUES
  (4, 'Which shows ONE?',   '1️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣'),
  (4, 'Which shows TWO?',   '2️⃣', '5️⃣', '2️⃣', '3️⃣', '4️⃣'),
  (4, 'Which shows THREE?', '3️⃣', '1️⃣', '2️⃣', '3️⃣', '5️⃣'),
  (4, 'Which shows FOUR?',  '4️⃣', '4️⃣', '3️⃣', '2️⃣', '1️⃣'),
  (4, 'Which shows FIVE?',  '5️⃣', '2️⃣', '5️⃣', '1️⃣', '4️⃣');

-- Fruits (category 5)
INSERT INTO questions (category_id, question_text, correct_answer, option1, option2, option3, option4) VALUES
  (5, 'Which one is an APPLE?',      '🍎', '🍎', '🍌', '🍇', '🍓'),
  (5, 'Which one is a BANANA?',      '🍌', '🍊', '🍌', '🍋', '🍑'),
  (5, 'Which one is a STRAWBERRY?',  '🍓', '🍒', '🍓', '🍇', '🍈'),
  (5, 'Which one is an ORANGE?',     '🍊', '🍋', '🍊', '🍎', '🍌'),
  (5, 'Which one is a WATERMELON?',  '🍉', '🍉', '🍇', '🍑', '🥭');