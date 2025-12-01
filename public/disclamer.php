<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = "Disclaimer";
$meta_description = "Disclaimer - Important information about Global Insights content and usage.";

include __DIR__ . '/../templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:#f2f5f9;
            color:#2d2d2d;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                        url('https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress') center/cover no-repeat;
            padding:120px 0;
            text-align:center;
            color:#fff;
        }

        .hero-section h1 {
            font-size:3.2rem;
            font-weight:700;
        }

        .hero-section p {
            font-size:1.2rem;
            opacity:0.9;
        }

        /* Glass Card */
        .policy-card {
            background:rgba(255,255,255,0.85);
            backdrop-filter:blur(12px);
            box-shadow:0 10px 35px rgba(0,0,0,0.1);
            border-radius:18px;
            padding:40px;
            margin-top:-50px;
        }

        h2 {
            margin-top:35px;
            font-weight:600;
            color:#1a1a1a;
        }

        footer {
            background:#0f0f0f;
            color:#bbb;
            padding:20px 0;
            margin-top:60px;
            text-align:center;
        }

        footer a {
            color:#fff;
            text-decoration:none;
            font-weight:500;
        }

        footer a:hover {
            text-decoration:underline;
        }
    </style>
</head>

<body>
    <main>
        <!-- HERO -->
        <section class="hero-section">
        <div class="container">
            <h1>Disclaimer</h1>
            <p>Important information about how our content should be used.</p>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="container mb-5">
        <div class="policy-card">

            <p>
                The information provided on <strong>Global Insights</strong> is for general informational and educational purposes only. 
                By using our website, you acknowledge and agree to this disclaimer.
            </p>

            <h2>1. No Professional Advice</h2>
            <p>
                All content on our site—including news, analysis, opinions, and articles—is shared for informational purposes only.  
                It should not be considered professional advice of any kind (legal, financial, medical, political, etc.).
            </p>

            <h2>2. Accuracy of Information</h2>
            <p>
                While we strive to publish accurate and up-to-date information, we cannot guarantee that all content is always complete, correct, or updated.  
                News and global updates often change rapidly.
            </p>

            <h2>3. External Links</h2>
            <p>
                Our website may contain links to third-party websites.  
                We do not control or take responsibility for the content, reliability, or policies of external sites.
            </p>

            <h2>4. User-Generated Content</h2>
            <p>
                Comments, opinions, or posts made by users do not reflect the views of Global Insights.  
                Users are solely responsible for their content.
            </p>

            <h2>5. No Liability</h2>
            <p>
                Global Insights is not responsible for any losses, damages, or inconveniences resulting from:
            </p>
            <ul>
                <li>Use of our website or content</li>
                <li>Reliance on published information</li>
                <li>Errors, delays, or outdated data</li>
                <li>Technical issues or website downtime</li>
            </ul>

            <h2>6. Fair Use Notice</h2>
            <p>
                Some content (images, quotes, media) may be used under “Fair Use” for reporting, review, or educational purposes.  
                If you are the rightful owner and find any misuse, please contact us immediately for removal.
            </p>

            <h2>7. Consent</h2>
            <p>
                By using our website, you consent to this disclaimer and agree to its terms.
            </p>

            <h2>8. Updates to This Disclaimer</h2>
            <p>
                This disclaimer may be updated occasionally. We encourage you to check this page regularly for any changes.
            </p>

            <h2>9. Contact Us</h2>
            <p>
                If you have any questions related to this disclaimer, please  
                <a href="contactus.php">contact us here</a>.
            </p>

        </div>
    </section>
    </main>
</body>
</html>

<?php include __DIR__ . '/../templates/footer.php'; ?>
