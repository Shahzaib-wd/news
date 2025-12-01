<?php
// Include main config (loads DB constants, starts session safely)
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/includes/functions.php';

// Database connection
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        setFlash('error', 'All fields are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Invalid email address.');
    } else {
        // Insert into database
        $stmt = $db->prepare("INSERT INTO contact_messages (name,email,subject,message,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$name, $email, $subject, $message]);
        setFlash('success', 'Your message has been sent successfully!');
        $name = $email = $subject = $message = ''; // clear form
    }
}

// Page variables
$page_title = "Contact Us - " . SITE_NAME;
include ROOT_PATH . '/templates/header.php';
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-container p-4 shadow rounded bg-white">
                <h2 class="mb-4">Contact Us</h2>

                <!-- Flash Messages -->

                <!-- Contact Form -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required><?= htmlspecialchars($message ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include ROOT_PATH . '/templates/footer.php'; ?>
