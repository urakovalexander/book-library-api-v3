CREATE TABLE IF NOT EXISTS access (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      owner_id INT NOT NULL,
                                      user_id INT NOT NULL,
                                      UNIQUE KEY (owner_id, user_id),
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );