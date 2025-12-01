<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = "About Us";
$meta_description = "Learn about Global Insights - our mission, vision, and commitment to delivering trusted world news.";

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
                        url('https://images.pexels.com/photos/3184292/pexels-photo-3184292.jpeg?auto=compress') center/cover no-repeat;
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
            <h1>About Us</h1>
            <p>Who we are, what we do, and the mission behind Global Insights.</p>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="container mb-5">
        <div class="policy-card">

            <h2>Our Story</h2>
            <p>
                Global Insights was created with one mission:  
                <strong>to deliver accurate, trustworthy, and meaningful world news—without the noise.</strong>  
                In a time when information spreads fast and facts get blurred, we aim to make global knowledge simple and accessible for everyone.
            </p>

            <h2>What We Do</h2>
            <p>
                We cover a wide range of topics including:
            </p>
            <ul>
                <li>Global News & Current Events</li>
                <li>Technology & Innovation</li>
                <li>Politics & Global Affairs</li>
                <li>Science, Health, and Education</li>
                <li>Economics & Business</li>
                <li>Human Rights & Cultural Stories</li>
            </ul>

            <p>
                Our goal is to help you stay informed with clarity, accuracy, and a global perspective.
            </p>

            <h2>Our Mission</h2>
            <p>
                We believe information should empower people—not confuse them.  
                That's why we focus on:
            </p>
            <ul>
                <li>Verified sources</li>
                <li>Balanced storytelling</li>
                <li>Unbiased reporting</li>
                <li>Clear explanations</li>
            </ul>

            <h2>Why Choose Us?</h2>
            <p>Here’s what makes Global Insights different:</p>
            <ul>
                <li>We prioritize truth over trends</li>
                <li>No political bias or hidden agendas</li>
                <li>Human-centered, globally-aware journalism</li>
                <li>Fast but factual updates</li>
                <li>A clean, modern reading experience</li>
            </ul>

            <h2>Our Vision</h2>
            <p>
                To create a world where information bridges gaps instead of creating them.  
                A world where people understand each other better, no matter where they live.
            </p>


            <h2>Contact Us</h2>
            <p>
                Want to collaborate, share feedback, or ask questions?  
                <a href="contactus.php">Reach out to us here.</a>
            </p>

        </div>
    </section>
    </main>
</body>
</html>

<?php include __DIR__ . '/../templates/footer.php'; ?>
