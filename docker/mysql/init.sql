DROP DATABASE IF EXISTS posse;
CREATE DATABASE posse;
USE posse;

-- Core user table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT "default-profile.jpeg",
    is_admin BOOLEAN DEFAULT FALSE,
    date_of_birth DATE,
    phone_number VARCHAR(15),
    college VARCHAR(255),
    request_count INT DEFAULT 0,
    give_count INT DEFAULT 0,
    exp_point INT UNSIGNED DEFAULT 0,
    `level` INT AS (FLOOR(SQRT(20*exp_point+25)/10+1/2)) STORED,
    `rank` INT DEFAULT 2,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE user_socials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    line_link VARCHAR(255),
    instagram_link VARCHAR(255),
    twitter_link VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Core circle table
CREATE TABLE circles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT "default-profile.jpeg",
    type ENUM('intercollegiate','intramural') NOT NULL,
    exp_point INT UNSIGNED DEFAULT 0,
    `level` INT AS (FLOOR(SQRT(20*exp_point+25)/10+1/2)) STORED,
    request_count INT DEFAULT 0,
    give_count INT DEFAULT 0,
    `rank` INT DEFAULT 2,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE circle_socials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    circle_id INT NOT NULL,
    line_link VARCHAR(255),
    instagram_link VARCHAR(255),
    twitter_link VARCHAR(255),
    FOREIGN KEY (circle_id) REFERENCES circles(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Circle-college relationship
CREATE TABLE circle_colleges (
    circle_id INT NOT NULL,
    college VARCHAR(255) NOT NULL,
    PRIMARY KEY (circle_id, college),
    FOREIGN KEY (circle_id) REFERENCES circles(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

-- User-circle membership
CREATE TABLE circle_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    circle_id INT NOT NULL,
    exp_point INT UNSIGNED DEFAULT 0,
    `level` INT AS (FLOOR(SQRT(20*exp_point+25)/10+1/2)) STORED,
    UNIQUE KEY unique_membership (user_id, circle_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (circle_id) REFERENCES circles(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

-- Request categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(255) NOT NULL UNIQUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

-- Request table
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    circle_member_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    request TEXT NOT NULL,
    pay INT NOT NULL,
    due_date DATE NOT NULL,
    comment TEXT,
    is_completed BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) CHARSET=utf8;

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prizes TEXT,
    submission_deadline DATE NOT NULL,
    presentation_date DATE NOT NULL,
    created_by INT NOT NULL,
    is_public BOOLEAN DEFAULT TRUE,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) CHARSET=utf8;

CREATE TABLE event_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('メインビジュアル', 'バナー', 'サムネイル', 'ギャラリー') NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) CHARSET=utf8;

CREATE TABLE event_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE event_tag_map (
    event_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (event_id, tag_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES event_tags(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE event_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(255),
    created_by_circle_member_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE
) CHARSET=utf8;

CREATE TABLE event_team_members (
    team_id INT NOT NULL,
    circle_member_id INT NOT NULL,
    PRIMARY KEY (team_id, circle_member_id),
    FOREIGN KEY (team_id) REFERENCES event_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE event_invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    team_id INT,
    inviter_id INT NOT NULL,
    invitee_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    invitation_sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (inviter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (invitee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES event_teams(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

CREATE TABLE event_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    team_id INT, -- NULL if submitted individually
    circle_member_id INT, -- NULL if submitted as team
    submission TEXT,
    status ENUM('pending', 'selected', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES event_teams(id) ON DELETE SET NULL,
    FOREIGN KEY (circle_member_id) REFERENCES circle_members(id) ON DELETE SET NULL
) CHARSET=utf8;

CREATE TABLE event_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    submission_id INT NOT NULL,
    voted_by_circle_member_id INT NOT NULL,
    score INT CHECK (score BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (submission_id, voted_by_circle_member_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES event_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (voted_by_circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

-- Submission table with status tracking
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    circle_member_id INT NOT NULL,
    submission TEXT,
    status ENUM('pending', 'selected', 'rejected') NOT NULL DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_submission (request_id, circle_member_id),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE
) CHARSET=utf8;

-- Review criteria
CREATE TABLE review_criteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    criteria VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    `for` ENUM('giver','requester', 'both') NOT NULL DEFAULT 'both',
    point_range INT NOT NULL CHECK (point_range BETWEEN 0 AND 5),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARSET=utf8;

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_circle_member_id INT NOT NULL,
    reviewee_circle_member_id INT NOT NULL,
    review_criteria_id INT NOT NULL,
    point INT NOT NULL CHECK (point BETWEEN 0 AND 5),
    towards ENUM('giver','requester') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE,
    FOREIGN KEY (review_criteria_id) REFERENCES review_criteria(id) ON DELETE CASCADE
) CHARSET=utf8;

CREATE TABLE review_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    reviewer_circle_member_id INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reviewer (submission_id, reviewer_circle_member_id),
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_circle_member_id) REFERENCES circle_members(id) ON DELETE CASCADE
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sender_id INT,
    request_id INT,
    event_id INT,
    event_team_id INT,
    notification TEXT NOT NULL,
    redirects_to TEXT,
    external_link VARCHAR(255),
    action VARCHAR(255) DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (event_team_id) REFERENCES event_teams(id) ON DELETE CASCADE
) CHARSET=utf8;

-- Sample data
INSERT INTO users (name, is_admin, email, password, date_of_birth, phone_number, college) VALUES
('原 瑞生', TRUE, 'admin@example.com', "$2y$10$3qRwaHh8YlTiiHoJOvFP0.FOizdZ8qpxfnYHWSmaKuie7jkkz8rxm", '1990-05-15', '2234567890', '上智大学'),
('小見山 悠', FALSE, 'test@example.com', "$2y$10$3qRwaHh8YlTiiHoJOvFP0.FOizdZ8qpxfnYHWSmaKuie7jkkz8rxm", '1998-05-15', '1234567890', '慶應大学'),
('寺山 文乃', FALSE, 'test2@example.com', "$2y$10$3qRwaHh8YlTiiHoJOvFP0.FOizdZ8qpxfnYHWSmaKuie7jkkz8rxm", '1997-08-20', '0987654321', '法政大学'), 
('山田 優介', FALSE, 'test3@example.com', "$2y$10$3qRwaHh8YlTiiHoJOvFP0.FOizdZ8qpxfnYHWSmaKuie7jkkz8rxm", '1999-03-10', '1112223333', '慶應大学'),
('西尾 佳也', FALSE, 'test4@example.com', "$2y$10$3qRwaHh8YlTiiHoJOvFP0.FOizdZ8qpxfnYHWSmaKuie7jkkz8rxm", '0001-01-01', '2112223333', '慶應大学');

INSERT INTO user_socials (user_id,line_link, instagram_link, twitter_link) VALUES
(1, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/"),
(2, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/"),
(3, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/");

INSERT INTO circles (name, type) VALUES
('アニメーションサークル', 'intercollegiate'),
('プログラミングサークル', 'intercollegiate'),
('クラシックサークル', 'intramural'),
('グラフィックデザインサークル', 'intercollegiate'),
('動画編集サークル', 'intercollegiate');

INSERT INTO circle_socials (circle_id,line_link, instagram_link, twitter_link) VALUES
(1, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/"),
(2, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/"),
(3, "https://www.line.me/ja/", "https://www.instagram.com", "https://x.com/");

INSERT INTO circle_members (user_id, circle_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

INSERT INTO circle_colleges (circle_id, college) VALUES
(1, '上智大学'),
(2, '慶應大学'),
(3, '法政大学'),
(4, '慶應大学'),
(5, '慶應大学');

INSERT INTO categories (category) VALUES
('プログラミング'),
('デザイン'),
('動画編集'),
('SNS');

INSERT INTO requests (circle_member_id, category_id, title, request, pay, due_date, comment) VALUES
(1, 1, 'Webサイト作成', '新歓のためにサークルを紹介するウェブサイトを作成してほしい', 800, '2025-05-01', 'クールな雰囲気のウェブサイトにしてほしい'),
(2, 2, 'ロゴ作成', 'サークルを設立したのでかっこいいロゴを作って欲しい', 800, '2025-07-31', '青と水色を使ったロゴにして欲しい'),
(3, 3, '紹介動画', 'サークル紹介の動画を作って欲しい', 600, '2025-08-02', '時間は2分ぐらいで、写真を送るのでそれを使って作って欲しい'),
(4, 1, '横断幕のデザイン', '学園祭で出展する際に横断幕を掲げるので、それのデザインを考えてほしい', 2000, '2025-11-01', '大きさは縦1m、横3mで、壮大なものを作って欲しい'),
(5, 2, 'サークルTシャツのデザイン', '公演の際にサークル全員で着るTシャツのデザインを考え欲しい', 1000, '2025-10-15', 'Tシャツの色はオレンジでデザインは白で作って欲しい'),
(1, 4, 'SNS投稿', 'サークル紹介の投稿のデザインを考えて欲しい', 500, '2025-05-01', '人を惹きつけるようなデザインの投稿のデザインを考えて欲しい');

INSERT INTO review_criteria (criteria, description, `for`, point_range) VALUES
('おすすめ度', '連絡のやり取りは適切であったか', 'both', 5),
('納品・決済のやり取りがスムーズ', '納品、決済は期日を守り、スムーズであったか', 'both', 5),
('仕事ぶり', 'またこのサークル・人と取引したいか？', 'both', 5),
('成果物の質', '作品の質はどうか（依頼人の要求に沿っているか）', 'requester', 5),
('おすすめ度', '他の人にどれくらいおすすめしたいか', 'requester', 5),
('進捗共有', '適度に進捗の共有はされていたか？', 'requester', 5);

INSERT INTO events (title, description, prizes, submission_deadline, presentation_date, created_by, is_public, is_closed) VALUES 
(
    'おもいでの食事', 
    '思い出のご飯についてその時の感情やその時のご飯の味、エピソードなどを自分の得意な媒体で表現しよう', 
    '1位: Udemyギフト1万円分, 2位: 図書カード3000円, 3位: 図書カード1000円', 
    '2025-05-01', 
    '2025-05-05', 
    1, 
    TRUE, 
    FALSE
);

INSERT INTO event_tags (name) VALUES 
('プログラミング'),
('絵'),
('アニメーション'),
('音楽'),
('動画編集');

INSERT INTO event_images (event_id, image_path, image_type) VALUES
(1, 'assets/events/event1-main.svg', 'メインビジュアル'),
(1, 'assets/events/event1-banner.svg', 'バナー');
INSERT INTO event_tag_map (event_id, tag_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5);

INSERT INTO notifications (user_id, event_id, notification, redirects_to) VALUES
(2, 1, 'イベント告知!:「おもいでの食事」 あなたも参加してみませんか？', 'event'),
(3, 1, 'イベント告知!:「おもいでの食事」 あなたも参加してみませんか？', 'event'),
(4, 1, 'イベント告知!:「おもいでの食事」 あなたも参加してみませんか？', 'event'),
(5, 1, 'イベント告知!:「おもいでの食事」 あなたも参加してみませんか？', 'event');
