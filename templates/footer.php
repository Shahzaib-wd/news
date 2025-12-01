<!-- ========================= FOOTER START ========================= -->
<footer class="bg-dark text-light py-5 mt-5 border-top">
    <div class="container">
        <div class="row gy-4 mb-4">
            <!-- LOGO + TAGLINE -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-logo mb-3">
                    <a href="<?= BASE_URL ?>/public/" class="text-decoration-none">
                        <i class="bi bi-globe2 text-primary fs-3"></i>
                        <span class="text-white fw-bold fs-5"><?= e(SITE_NAME) ?></span>
                    </a>
                </div>
                <p class=" small"><?= e(SITE_TAGLINE) ?></p>
                <p class=" small">Your trusted source for global insights, news & stories that matter.</p>
            </div>

            <!-- QUICK LINKS -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-chevron-right"></i> Home</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/aboutus.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-chevron-right"></i> About Us</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/contactus.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-chevron-right"></i> Contact</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/search.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-chevron-right"></i> Search</a></li>
                </ul>
            </div>

            <!-- LEGAL LINKS -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Legal</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/privacy.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-shield-check"></i> Privacy Policy</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/terms.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-file-earmark-text"></i> Terms & Conditions</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/public/disclamer.php" class="text-light text-decoration-none" style="transition: 0.3s;"><i class="bi bi-exclamation-triangle"></i> Disclaimer</a></li>
                </ul>
            </div>

            <!-- SOCIAL LINKS -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3 fw-bold">Follow Us</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light text-decoration-none" style="transition: 0.3s;" title="Facebook"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-light text-decoration-none" style="transition: 0.3s;" title="Twitter"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="text-light text-decoration-none" style="transition: 0.3s;" title="Instagram"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-light text-decoration-none" style="transition: 0.3s;" title="YouTube"><i class="bi bi-youtube fs-5"></i></a>
                    <a href="#" class="text-light text-decoration-none" style="transition: 0.3s;" title="LinkedIn"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-top pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class=" small mb-2 mb-md-0">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class=" small mb-0">Designed with <i class="bi bi-heart-fill text-danger"></i> by Global Insights Team</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ========================= FOOTER END =========================
