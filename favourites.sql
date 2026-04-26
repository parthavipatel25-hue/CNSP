CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    note_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);