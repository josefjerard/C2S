CREATE DATABASE IF NOT EXISTS c2s_accounts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS c2s_mentees CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS c2s_accounts.users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS c2s_mentees.mentees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    mentee_name VARCHAR(150) NOT NULL,
    status ENUM('Active', 'Inactive', 'Transferred to Other Ministry') NOT NULL DEFAULT 'Active',
    contact_number VARCHAR(30) NULL,
    birthday DATE NULL,
    address TEXT NULL,
    module_lesson VARCHAR(255) NULL,
    cldp_1 ENUM('Unenrolled', 'Ongoing', 'Incomplete', 'Completed') NOT NULL DEFAULT 'Unenrolled',
    cldp_2 ENUM('Unenrolled', 'Ongoing', 'Incomplete', 'Completed') NOT NULL DEFAULT 'Unenrolled',
    cldp_3 ENUM('Unenrolled', 'Ongoing', 'Incomplete', 'Completed') NOT NULL DEFAULT 'Unenrolled',
    potential_mentor ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
    c2s_101 ENUM('Lesson 1', 'Lesson 2', 'Lesson 3', 'Lesson 4', 'Lesson 5', 'Completed') NOT NULL DEFAULT 'Lesson 1',
    other_trainings TEXT NULL,
    remarks TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
) ENGINE = InnoDB;

INSERT INTO c2s_accounts.users (username, password_hash)
VALUES ('admin', '$2y$10$Zee62BFr2GFKHLDNCa98MuszvVa5Uu.UZxl6X.D3fdtThNDq0jGhG');

INSERT INTO c2s_mentees.mentees
    (user_id, mentee_name, status, contact_number, birthday, address, module_lesson, cldp_1, cldp_2, cldp_3, potential_mentor, c2s_101, other_trainings, remarks)
SELECT id, 'Juan Dela Cruz', 'Active', '09171234567', '2004-03-15', 'Quezon City', 'Module 1 - Lesson 3', 'Completed', 'Ongoing', 'Unenrolled', 'Yes', 'Lesson 2', 'Youth Camp 2025', 'Very consistent in attendance.'
FROM c2s_accounts.users WHERE username = 'admin';

INSERT INTO c2s_mentees.mentees
    (user_id, mentee_name, status, contact_number, birthday, address, module_lesson, cldp_1, cldp_2, cldp_3, potential_mentor, c2s_101, other_trainings, remarks)
SELECT id, 'Maria Santos', 'Active', '09981234567', '2007-08-02', 'Makati City', 'Module 2 - Lesson 1', 'Ongoing', 'Unenrolled', 'Unenrolled', 'No', 'Lesson 1', '', 'On hold due to school schedule.'
FROM c2s_accounts.users WHERE username = 'admin';

INSERT INTO c2s_mentees.mentees
    (user_id, mentee_name, status, contact_number, birthday, address, module_lesson, cldp_1, cldp_2, cldp_3, potential_mentor, c2s_101, other_trainings, remarks)
SELECT id, 'Carlo Reyes', 'Inactive', '09051234567', '2001-01-30', 'Pasig City', 'Module 4 - Lesson 6', 'Completed', 'Completed', 'Completed', 'Yes', 'Completed', 'Life Coaching Seminar 2024', 'Ready to be deployed as mentor.'
FROM c2s_accounts.users WHERE username = 'admin';
