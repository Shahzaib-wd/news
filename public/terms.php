<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = "Terms & Conditions";
$meta_description = "Terms & Conditions - Read our usage guidelines and legal terms for Global Insights.";

include __DIR__ . '/../templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f2f5f9;
            color: #2d2d2d;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                        url('https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress') center/cover no-repeat;
            padding: 120px 0;
            text-align: center;
            color: #fff;
        }

        .hero-section h1 {
            font-size: 3.2rem;
            font-weight: 700;
        }

        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Terms Card */
        .terms-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.1);
            border-radius: 18px;
            padding: 40px;
            margin-top: -50px;
        }

        h2 {
            margin-top: 35px;
            font-weight: 600;
            color: #1a1a1a;
        }

        footer {
            background: #0f0f0f;
            color: #bbb;
            padding: 20px 0;
            margin-top: 60px;
            text-align: center;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>
    <main>
        <!-- HERO -->
        <section class="hero-section">
        <div class="container">
            <h1>Terms & Conditions</h1>
            <p>Your use of Global Insights means you agree to our guidelines.</p>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="container mb-5">
        <div class="terms-card">

            <p>Welcome to <strong>Global Insights</strong>. By using our website, you agree to the following terms. Please read them carefully to ensure a smooth and safe experience.</p>

            <h2>1. Acceptance of Terms</h2>
            <p>By accessing this site, you accept these Terms & Conditions in full. If you do not agree, please discontinue using the website.</p>

            <h2>2. Ownership & Intellectual Property</h2>
            <p>All content, including articles, images, branding, and layout, belongs to Global Insights unless otherwise stated. Unauthorized use is strictly prohibited.</p>

            <h2>3. User Responsibilities</h2>
            <ul>
                <li>You must not misuse the website or violate any laws.</li>
                <li>You must not post harmful, spam, or copyrighted content.</li>
                <li>You must respect other users and their rights.</li>
            </ul>

            <h2>4. Limitation of Liability</h2>
            <p>We do not guarantee accuracy or completeness of information. Global Insights is not responsible for damages resulting from the use of our content or website.</p>

            <h2>5. Privacy & Data</h2>
            <p>Your privacy matters to us. Read our <a href="privacy.php">Privacy Policy</a> for detailed information on how we handle your data.</p>

            <h2>6. Changes to Terms</h2>
            <p>We may revise these terms at any time without notice. Continued use indicates acceptance of updated terms.</p>

            <h2>7. Contact Us</h2>
            <p>If you have any questions, feel free to <a href="contactus.php">contact us</a>.</p>

        </div>
    </section>
    </main>
</body>
</html>

<?php include __DIR__ . '/../templates/footer.php'; ?>
