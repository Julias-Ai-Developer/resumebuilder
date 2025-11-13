<?php
require_once 'includes/db_connect.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "Welcome to Resume Builder";
include 'includes/header.php';
?>

<div class="row align-items-center min-vh-75">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <h1 class="display-4 fw-bold mb-4" style="color: var(--primary-color);">
            Build Your Perfect Resume
        </h1>
        <p class="lead mb-4">
            Create professional resumes in minutes with our easy-to-use builder. 
            Choose from multiple templates and customize every detail.
        </p>
        <div class="d-grid gap-2 d-sm-flex">
            <a href="register.php" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-rocket-takeoff"></i> Get Started
            </a>
            <a href="login.php" class="btn btn-outline-primary btn-lg px-5">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow-lg">
            <div class="card-body p-0">
                <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&h=600&fit=crop" 
                     alt="Resume Building" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<div class="row mt-5 g-4">
    <div class="col-md-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <i class="bi bi-palette display-3 mb-3" style="color: var(--primary-color);"></i>
                <h4 class="card-title">Multiple Templates</h4>
                <p class="card-text">Choose from various professional resume templates designed by experts.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <i class="bi bi-pencil-square display-3 mb-3" style="color: var(--primary-color);"></i>
                <h4 class="card-title">Easy Customization</h4>
                <p class="card-text">Edit and customize every section of your resume with our intuitive interface.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 text-center p-4">
            <div class="card-body">
                <i class="bi bi-download display-3 mb-3" style="color: var(--primary-color);"></i>
                <h4 class="card-title">Download & Share</h4>
                <p class="card-text">Download your resume as PDF or share it directly with potential employers.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body p-5 text-center">
                <h2 class="mb-3" style="color: var(--primary-color);">How It Works</h2>
                <div class="row mt-4">
                    <div class="col-md-3 mb-3">
                        <div class="display-6 fw-bold mb-2" style="color: var(--primary-color);">1</div>
                        <h5>Sign Up</h5>
                        <p class="small">Create your free account</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="display-6 fw-bold mb-2" style="color: var(--primary-color);">2</div>
                        <h5>Choose Template</h5>
                        <p class="small">Select your favorite design</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="display-6 fw-bold mb-2" style="color: var(--primary-color);">3</div>
                        <h5>Fill Details</h5>
                        <p class="small">Add your information</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="display-6 fw-bold mb-2" style="color: var(--primary-color);">4</div>
                        <h5>Download</h5>
                        <p class="small">Get your professional resume</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>