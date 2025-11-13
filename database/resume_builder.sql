-- Resume Builder Database Schema

CREATE DATABASE IF NOT EXISTS resume_builder;
USE resume_builder;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Resumes table
CREATE TABLE resumes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    template_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Personal Information
CREATE TABLE personal_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    linkedin VARCHAR(255),
    website VARCHAR(255),
    summary TEXT,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Education
CREATE TABLE education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    institution VARCHAR(150) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(100),
    start_date DATE,
    end_date DATE,
    description TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Experience
CREATE TABLE experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    start_date DATE,
    end_date DATE,
    current_job BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Skills
CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    proficiency ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Projects
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    project_name VARCHAR(150) NOT NULL,
    description TEXT,
    technologies VARCHAR(255),
    url VARCHAR(255),
    start_date DATE,
    end_date DATE,
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Certifications
CREATE TABLE certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resume_id INT NOT NULL,
    certification_name VARCHAR(150) NOT NULL,
    issuing_organization VARCHAR(150),
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(100),
    credential_url VARCHAR(255),
    display_order INT DEFAULT 0,
    FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
);

-- Templates
CREATE TABLE templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    preview_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default templates
INSERT INTO templates (name, description, is_active) VALUES
('Modern Professional', 'Clean and modern design with accent colors', TRUE),
('Classic Elegant', 'Traditional layout with elegant typography', TRUE),
('Creative Bold', 'Bold and creative design for creative professionals', TRUE),
('Minimal Clean', 'Minimalist design focusing on content', TRUE);

-- Create indexes for better performance
CREATE INDEX idx_user_id ON resumes(user_id);
CREATE INDEX idx_resume_id_personal ON personal_info(resume_id);
CREATE INDEX idx_resume_id_education ON education(resume_id);
CREATE INDEX idx_resume_id_experience ON experience(resume_id);
CREATE INDEX idx_resume_id_skills ON skills(resume_id);
CREATE INDEX idx_resume_id_projects ON projects(resume_id);
CREATE INDEX idx_resume_id_certifications ON certifications(resume_id);