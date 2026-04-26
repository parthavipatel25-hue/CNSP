CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) DEFAULT NULL,
    
    note_type ENUM('PDF', 'DOC', 'PPT') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    version INT DEFAULT 1,
    is_pinned TINYINT(1) DEFAULT 0,
    
    file_hash VARCHAR(255),   
    file_size INT,            
    
    INDEX idx_uploaded_by (uploaded_by),
    
    CONSTRAINT fk_uploaded_by
        FOREIGN KEY (uploaded_by)
        REFERENCES users(id)
        ON DELETE CASCADE
);