            </main>
        </div>
    </div>
    <!-- Minimal Admin Footer -->
    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <a href="<?= BASE_URL ?>/public/" class="d-inline-block mb-2">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="<?= e(SITE_NAME) ?>" style="height:36px;">
            </a>
            <div>
                <small class="text-white">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?></small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/<?= EXTERNAL_API_KEY ?: 'no-api-key' ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>

