DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS creators_games;
DROP TABLE IF EXISTS scores;
DROP TABLE IF EXISTS games_tags;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS screenshots;
DROP TABLE IF EXISTS stats;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS creators;
DROP TABLE IF EXISTS games_comments;

CREATE TABLE creators (
    id_creator INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(48),
    username VARCHAR(48),
    password CHAR(32),
    email VARCHAR(32),
    image VARCHAR(16),
    description TEXT
);

CREATE TABLE games (
    id_game INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(32),
    link VARCHAR(128),
    header VARCHAR(16),
    price DECIMAL(5,2),
    trailer VARCHAR(128)
);

CREATE TABLE comments (
    id_comment INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_creator INT,
    id_game INT,
    FOREIGN KEY (id_creator) REFERENCES creators(id_creator) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE
);

CREATE TABLE games_comments (
    id_game INT NOT NULL,
    id_comment INT NOT NULL,
    PRIMARY KEY (id_game, id_comment),
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE,
    FOREIGN KEY (id_comment) REFERENCES comments(id_comment) ON DELETE CASCADE
);

CREATE TABLE creators_games (
    id_creator_name INT AUTO_INCREMENT PRIMARY KEY,
    id_creator INT,
    id_game INT,
    FOREIGN KEY (id_creator) REFERENCES creators(id_creator) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE
);

CREATE TABLE scores (
    id_score INT AUTO_INCREMENT PRIMARY KEY,
    score INT,
    datetime DATETIME,
    id_creator INT,
    id_game INT,
    FOREIGN KEY (id_creator) REFERENCES creators(id_creator) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE
);

CREATE TABLE tags (
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(16),
    color VARCHAR(6)
);

CREATE TABLE games_tags (
    id_game_tag INT AUTO_INCREMENT PRIMARY KEY,
    id_game INT,
    id_tag INT,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES tags(id_tag) ON DELETE CASCADE
);

CREATE TABLE screenshots (
    id_screenshot INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(16),
    description TEXT,
    id_game INT,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE
);

CREATE TABLE stats (
    id_stat INT AUTO_INCREMENT PRIMARY KEY,
    likes INT,
    views INT,
    downloads INT,
    id_game INT,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE
);
