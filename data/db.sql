CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    active INT NOT NULL DEFAULT 1,
    last_login DATETIME,
    UNIQUE(username)
);
/* default user: admin, password: supersecret */
INSERT INTO users (id, username, password, active) 
VALUES 
    (1, 'admin', '$2y$10$dafNpt7nugiu07HfOztFIetV1uXJlZacTtUIebVaHzdCDSKdQIQ6i', 1);