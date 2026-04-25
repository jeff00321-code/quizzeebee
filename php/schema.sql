CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  plan VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE child_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  name VARCHAR(100),
  age INT,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50)
);

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  question_text VARCHAR(255),
  correct_answer VARCHAR(50),
  option1 VARCHAR(50),
  option2 VARCHAR(50),
  option3 VARCHAR(50),
  option4 VARCHAR(50),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE quiz_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  child_id INT,
  category_id INT,
  q1_id INT, q2_id INT, q3_id INT, q4_id INT, q5_id INT,
  score INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (child_id) REFERENCES child_profiles(id)
);

CREATE TABLE session_answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT,
  question_id INT,
  q_position INT,
  answer_emoji VARCHAR(10),
  is_correct BOOLEAN,
  FOREIGN KEY (session_id) REFERENCES quiz_sessions(id)
);
