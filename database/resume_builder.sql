-- Resume Builder Database Schema
-- Version 1.0
-- Date: 2025-11-13

-- Create database
CREATE DATABASE IF NOT EXISTS resume_builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE resume_builder;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100),
    reset_token VARCHAR(100),
    reset_token_expiry DATETIME,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_verification_token (verification_token),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Templates Table
CREATE TABLE templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    folder_path VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    popularity_score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resumes Table
CREATE TABLE resumes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    title VARCHAR(200) NOT NULL DEFAULT 'Untitled Resume',
    is_favorite BOOLEAN DEFAULT FALSE,
    share_token VARCHAR(64) UNIQUE,
    share_expiry DATETIME,
    is_public BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id),
    INDEX idx_user_id (user_id),
    INDEX idx_share_token (share_token),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Personal Info Table
CREATE TABLE personal_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    professional_title VARCHAR(150),
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    linkedin_url VARCHAR(255),
    portfolio_url VARCHAR(255),
    summary TEXT,
    profile_photo VARCHAR(255),
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Education Table
CREATE TABLE education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    degree VARCHAR(150) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    location VARCHAR(200),
    start_date DATE,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    gpa VARCHAR(20),
    description TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_order (resume_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Experience Table
CREATE TABLE experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    company VARCHAR(200) NOT NULL,
    job_title VARCHAR(150) NOT NULL,
    employment_type ENUM('Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance') DEFAULT 'Full-time',
    location VARCHAR(200),
    start_date DATE,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    responsibilities TEXT,
    achievements TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_order (resume_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Skills Table
CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    skill_category ENUM('Technical', 'Soft Skills', 'Languages', 'Tools') DEFAULT 'Technical',
    proficiency_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_id (resume_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects Table
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    project_title VARCHAR(200) NOT NULL,
    description TEXT,
    technologies TEXT,
    project_url VARCHAR(255),
    github_url VARCHAR(255),
    start_date DATE,
    end_date DATE,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_order (resume_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Certifications Table
CREATE TABLE certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    certificate_name VARCHAR(200) NOT NULL,
    issuing_organization VARCHAR(200),
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(100),
    credential_url VARCHAR(255),
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_order (resume_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- References Table
CREATE TABLE reference (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    job_title VARCHAR(150),
    company VARCHAR(200),
    email VARCHAR(150),
    phone VARCHAR(50),
    relationship VARCHAR(100),
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE,
    INDEX idx_resume_order (resume_id, display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Log Table
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    resume_id INT,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE SET NULL,
    INDEX idx_user_date (user_id, created_at),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default templates
INSERT INTO templates (name, slug, description, folder_path, thumbnail, is_active, popularity_score) VALUES
('Classic', 'classic', 'Traditional single-column layout with elegant serif fonts. Perfect for academic and traditional industries.', 'templates/classic/', 'assets/img/template-thumbs/classic.png', TRUE, 0),
('Modern', 'modern', 'Two-column layout with sidebar. Clean, contemporary design ideal for tech and creative fields.', 'templates/modern/', 'assets/img/template-thumbs/modern.png', TRUE, 0),
('Elegant', 'elegant', 'Professional layout with top banner and section icons. Great for executive and management roles.', 'templates/elegant/', 'assets/img/template-thumbs/elegant.png', TRUE, 0),
('Creative', 'creative', 'Bold, colorful design with visual elements. Perfect for design, marketing, and creative industries.', 'templates/creative/', 'assets/img/template-thumbs/creative.png', TRUE, 0),
('Minimal', 'minimal', 'Ultra-clean design focused on content and white space. Versatile for any industry.', 'templates/minimal/', 'assets/img/template-thumbs/minimal.png', TRUE, 0);

-- Create a demo user (password: Demo@123)
-- Password hash for 'Demo@123'
INSERT INTO users (name, email, password, email_verified, created_at) VALUES
('Demo User', 'demo@resumebuilder.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, NOW());

-- Grant appropriate permissions (run as MySQL root user)
-- CREATE USER IF NOT EXISTS 'resume_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON resume_builder.* TO 'resume_user'@'localhost';
-- FLUSH PRIVILEGES;