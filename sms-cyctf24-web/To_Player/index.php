<?php 
include 'header.php'; 
session_start(); 
require('Captcha.php');
use Phppot\Captcha;

$captcha = new Captcha(); 
$captcha_code = $captcha->getCaptchaCode(5); 
$captcha->setSession('captcha_code', $captcha_code);
?>

<div class="text-center">
    <h1 class="my-5">Welcome to Our System!</h1>
    <p class="lead">We launch exciting competitions and value your feedback for continuous improvement.</p>
    <p class="lead">Join us in shaping a better experience together!</p>

    <img src="assets/images/image.png" class="img-fluid rounded-pill" alt="Description Image" style="max-width: 500px;">
</div>

<div class="my-5">
    <h2 class="text-center">Users Feedback</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="feedback-message feedback-blue">
                "Great app! It has improved my productivity immensely. I love how intuitive it is to navigate through different features!"<br> - John Doe
            </div>
        </div>
        <div class="col-md-4">
            <div class="feedback-message feedback-green">
                "Loved the interface! It's so user-friendly and aesthetically pleasing. I find myself spending less time figuring things out!" <br> - Sara Smith
            </div>
        </div>
        <div class="col-md-4">
            <div class="feedback-message feedback-yellow">
                "Easy to use and very helpful! The tutorial made all the difference, and now I feel confident using it daily." <br> - Mesbaha
            </div>
        </div>
        <div class="col-md-4">
            <div class="feedback-message feedback-orange">
                "Impressive design! It looks fantastic and really stands out. I've already recommended it to my friends!" <br> - Jessica Lee
            </div>
        </div>
        <div class="col-md-4">
            <div class="feedback-message feedback-purple">
                "Helpful support! The team is amazing. They responded to my queries promptly, making my experience much smoother." <br> - Tinker Hell
            </div>
        </div>
        <div class="col-md-4">
            <div class="feedback-message feedback-pink">
                "Fast and reliable! I can count on it for all my needs. The updates keep getting better, and I'm excited for future improvements!" <br> - Emily White
            </div>
        </div>
    </div>
</div>

<div class="my-5 row">
    <div class="col-md-6">
        <h2>Submit Your Feedback</h2>
        <form action="submit_feedback.php" method="POST">
            <div class="form-group">
                <label for="username">Your Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="feedback">Your Feedback:</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
            </div>
            <!-- CAPTCHA Image -->
            <div class="form-group row">
                <div class="col-sm-12 mb-3 mb-sm-0">
                    <img src="captchaImageSource.php" alt="CAPTCHA Image">
                    <input type="text" name="captcha" class="form-control form-control-user" id="captcha" placeholder="Enter CAPTCHA" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
    </div>

    <div class="col-md-6 competition-section">
        <h2>Logo Competition</h2>
        <p class="motivational-description">
            Unleash your creativity! Submit your best logo design and stand a chance to win exciting prizes.
            Your design could be the face of our brand, showcasing your talent and innovation!
        </p>
        <div>
            <img src="assets/images/logo_competition_image.png" class="img-fluid" alt="Logo Competition" style="max-width: 400px;">
        </div>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="logo">Upload Logo:</label>
                <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="fileHash">File Hash:</label>
                <input type="text" class="form-control" id="fileHash" name="hash" required>
            </div>
            <!-- CAPTCHA Image for Upload -->
            <div class="form-group row">
                <div class="col-sm-12 mb-3 mb-sm-0">
                    <img src="captchaImageSource.php" alt="CAPTCHA Image">
                    <input type="text" name="captcha" class="form-control form-control-user" id="captcha_upload" placeholder="Enter CAPTCHA" required>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="submit">
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
