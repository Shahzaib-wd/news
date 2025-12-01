<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = "Privacy Policy";
$meta_description = "Privacy Policy - Learn how Global Insights protects your data and privacy.";

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
                        url('https://images.pexels.com/photos/3184431/pexels-photo-3184431.jpeg?auto=compress') center/cover no-repeat;
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

        /* Card */
        .policy-card {
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
            <h1>Privacy Policy</h1>
            <p>Your privacy is our priority — learn how we protect your data.</p>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="container mb-5">
        <div class="policy-card">

            <p>
                At <strong>Global Insights</strong>, we value and respect your privacy.  
                This Privacy Policy explains what information we collect, how we use it, 
                and the rights you have regarding your personal data.
            </p>

            <h2>1. Information We Collect</h2>
            <ul>
                <li><strong>Personal Information:</strong> Name, email address, and any information submitted in forms.</li>
                <li><strong>Usage Data:</strong> Pages viewed, device info, browser type, IP address.</li>
                <li><strong>Cookies:</strong> To enhance user experience and analyze traffic.</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <ul>
                <li>To respond to contact form submissions.</li>
                <li>To improve website performance and user experience.</li>
                <li>To send updates or newsletters (only if you choose to subscribe).</li>
                <li>To monitor analytics and prevent spam or fraudulent activity.</li>
            </ul>

            <h2>3. How We Protect Your Data</h2>
            <p>
                We use modern security techniques including encryption, secure hosting, 
                and strict access control to ensure your information stays safe.
            </p>

            <h2>4. Cookies & Tracking</h2>
            <p>
                We use cookies to personalize content, analyze traffic, and improve performance.  
                You may disable cookies from your browser settings anytime.
            </p>

            <h2>5. Sharing of Information</h2>
            <p>
                We do <strong>not</strong> sell, trade, or share your personal information with third parties  
                except trusted services necessary for website functionality (analytics, email tools).
            </p>

            <h2>6. Third-Party Links</h2>
            <p>
                Our website may contain external links.  
                We are not responsible for the privacy practices or content of external sites.
            </p>

            <h2>7. Your Rights</h2>
            <ul>
                <li>Access your data.</li>
                <li>Request corrections or deletion.</li>
                <li>Withdraw consent for data usage.</li>
                <li>Disable tracking & cookies.</li>
            </ul>

            <h2>8. Children’s Privacy</h2>
            <p>
                We do not knowingly collect data from children under 13.  
                If you believe a child submitted data, contact us immediately.
            </p>

            <h2>9. Updates to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time.  
                Continued use of our website means you accept any changes.
            </p>

            <h2>10. Contact Us</h2>
            <p>
                If you have any questions, please <a href="contactus.php">contact us here</a>.
            </p>

        </div>
    </section>
    </main>
</body>
</html>

<?php include __DIR__ . '/../templates/footer.php'; ?>
