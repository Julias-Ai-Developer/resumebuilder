<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'resume_builder');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function createIndexIfMissing($conn, $tableName, $indexName, $columns) {
    $databaseName = DB_NAME;
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.statistics
        WHERE table_schema = ?
          AND table_name = ?
          AND index_name = ?
    ");
    $stmt->bind_param("sss", $databaseName, $tableName, $indexName);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ((int) $result['total'] === 0) {
        $conn->query("CREATE INDEX `$indexName` ON `$tableName` ($columns)");
    }
}

function addColumnIfMissing($conn, $tableName, $columnName, $definition) {
    $databaseName = DB_NAME;
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.columns
        WHERE table_schema = ?
          AND table_name = ?
          AND column_name = ?
    ");
    $stmt->bind_param("sss", $databaseName, $tableName, $columnName);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ((int) $result['total'] === 0) {
        $conn->query("ALTER TABLE `$tableName` ADD COLUMN `$columnName` $definition");
    }
}

function initializeDatabase($conn) {
    $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db(DB_NAME);

    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS resumes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            template_id INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS personal_info (
            id INT PRIMARY KEY AUTO_INCREMENT,
            resume_id INT NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            linkedin VARCHAR(255),
            website VARCHAR(255),
            photo_path VARCHAR(255),
            summary TEXT,
            FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
        )
    ");

    addColumnIfMissing($conn, 'personal_info', 'photo_path', 'VARCHAR(255) NULL AFTER website');

    $conn->query("
        CREATE TABLE IF NOT EXISTS education (
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
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS experience (
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
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS skills (
            id INT PRIMARY KEY AUTO_INCREMENT,
            resume_id INT NOT NULL,
            skill_name VARCHAR(100) NOT NULL,
            proficiency ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') DEFAULT 'Intermediate',
            display_order INT DEFAULT 0,
            FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE CASCADE
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS projects (
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
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS certifications (
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
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS templates (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            description TEXT,
            preview_image VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    createIndexIfMissing($conn, 'resumes', 'idx_user_id', '`user_id`');
    createIndexIfMissing($conn, 'personal_info', 'idx_resume_id_personal', '`resume_id`');
    createIndexIfMissing($conn, 'education', 'idx_resume_id_education', '`resume_id`');
    createIndexIfMissing($conn, 'experience', 'idx_resume_id_experience', '`resume_id`');
    createIndexIfMissing($conn, 'skills', 'idx_resume_id_skills', '`resume_id`');
    createIndexIfMissing($conn, 'projects', 'idx_resume_id_projects', '`resume_id`');
    createIndexIfMissing($conn, 'certifications', 'idx_resume_id_certifications', '`resume_id`');

    $templates = $conn->query("SELECT COUNT(*) AS total FROM templates")->fetch_assoc();

    if ((int) $templates['total'] === 0) {
        $conn->query("
            INSERT INTO templates (name, description, is_active) VALUES
            ('Modern Professional', 'Clean and modern design with accent colors', TRUE),
            ('Classic Elegant', 'Traditional layout with elegant typography', TRUE),
            ('Creative Bold', 'Bold and creative design for creative professionals', TRUE),
            ('Minimal Clean', 'Minimalist design focusing on content', TRUE)
        ");
    }
}

function databaseHasTables($conn) {
    $result = $conn->query("SHOW TABLES");
    $hasTables = $result->num_rows > 0;
    $result->free();

    return $hasTables;
}

try {
    // Connect to MySQL first, then create the database and tables from PHP code.
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    $conn->set_charset("utf8mb4");
    initializeDatabase($conn);

    if (!databaseHasTables($conn)) {
        initializeDatabase($conn);
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Repair older sessions that have a user ID but are missing display fields.
if (isset($_SESSION['user_id']) && empty($_SESSION['username'])) {
    $sessionUserId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $sessionUserId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
    } else {
        session_unset();
        session_destroy();
        session_start();
    }
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Helper function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Helper function to get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? 'User';
}

// Helper function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Helper function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
