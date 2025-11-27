# Global Insights - News Website

A production-ready news portal built with PHP, MySQL, Bootstrap, and JavaScript. Features a public-facing news site and secure admin panel for content management.

## 🚀 Features

### Public Features
- **Homepage**: Paginated article listing with featured articles
- **Article Pages**: Full article view with image, content, tags, social sharing
- **Categories & Tags**: Organized content navigation
- **Search**: Full-text search across articles
- **Social Sharing**: Facebook, Twitter, WhatsApp, Telegram, Copy Link
- **Like System**: One like per device/IP (cookie + IP-based)
- **Comments**: User comments with moderation and spam protection
- **SEO Optimized**: Meta tags, Open Graph, Twitter Cards, JSON-LD schema
- **Responsive Design**: Mobile-first Bootstrap 5 layout
- **AdSense Ready**: Multiple ad placement zones

### Admin Features
- **Secure Login**: Password-hashed authentication
- **Dashboard**: Statistics overview (views, likes, comments)
- **Article Management**: Create, edit, delete articles with rich text editor
- **Image Upload**: Secure image handling with validation
- **Comment Moderation**: Approve, reject, mark as spam
- **Category Management**: Organize content by categories
- **Tag System**: Multi-tag support for articles
- **Featured Articles**: Pin articles to homepage
- **SEO Controls**: Meta descriptions, canonical URLs
- **Sitemap Generation**: Automatic XML sitemap

### Security Features
- **SQL Injection Protection**: PDO prepared statements
- **XSS Prevention**: Output escaping with htmlspecialchars
- **CSRF Protection**: Token-based form validation
- **Rate Limiting**: IP-based throttling for likes/comments
- **Secure File Uploads**: MIME type validation, safe filenames
- **Session Security**: HttpOnly, Secure, SameSite cookies
- **Input Sanitization**: Server-side validation
- **Password Hashing**: bcrypt (password_hash)

## 📋 Requirements

- **PHP**: 7.4+ (8.0+ recommended)
- **MySQL**: 5.7+ or MariaDB 10.2+
- **Apache**: 2.4+ with mod_rewrite enabled
- **PHP Extensions**: PDO, pdo_mysql, gd, mbstring, fileinfo
- **Disk Space**: Minimum 100MB for application and uploads

## 🔧 Installation

### Step 1: Download and Extract

```bash
# Extract the project to your web server directory
cd /var/www/html
unzip global-insights.zip
cd global-insights
```

### Step 2: Configure Database

```bash
# Create MySQL database
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE global_insights CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gi_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON global_insights.* TO 'gi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema with sample data
mysql -u gi_user -p global_insights < global_insights_schema.sql
```

### Step 3: Configure Application

```bash
# Copy environment file
cp .env.example .env

# Edit .env with your settings
nano .env
```

**Required .env Settings:**
```env
DB_HOST=localhost
DB_NAME=global_insights
DB_USER=gi_user
DB_PASS=your_secure_password

SITE_URL=https://yourdomain.com
SITE_NAME=Global Insights
ADMIN_EMAIL=admin@yourdomain.com

DEBUG_MODE=false
```

### Step 4: Set File Permissions

```bash
# Make uploads directory writable
chmod 755 uploads/
chmod 755 uploads/articles/

# Secure sensitive files
chmod 600 .env
chmod 644 config.php

# Secure admin directory (optional - server dependent)
# chmod 750 admin/
```

### Step 5: Configure Apache Virtual Host

Create `/etc/apache2/sites-available/globalinsights.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/global-insights

    <Directory /var/www/html/global-insights>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Redirect to HTTPS (after SSL setup)
    # RewriteEngine On
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    ErrorLog ${APACHE_LOG_DIR}/globalinsights_error.log
    CustomLog ${APACHE_LOG_DIR}/globalinsights_access.log combined
</VirtualHost>

# SSL Configuration (after obtaining certificate)
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/global-insights

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem

    <Directory /var/www/html/global-insights>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/globalinsights_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/globalinsights_ssl_access.log combined
</VirtualHost>
```

Enable site and restart Apache:

```bash
sudo a2ensite globalinsights.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 6: SSL Certificate (Production)

```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

# Obtain SSL certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is configured automatically
# Test renewal: sudo certbot renew --dry-run
```

## 🔐 Default Admin Credentials

**IMPORTANT**: Change these immediately after first login!

```
URL: https://yourdomain.com/admin/
Username: admin
Password: Admin@123456
```

**To change admin password:**
1. Login to admin panel
2. Or manually update via MySQL:

```bash
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
# Copy the hash output

mysql -u gi_user -p global_insights
UPDATE users_admin SET password_hash = 'paste_hash_here' WHERE username = 'admin';
```

## 📱 AdSense Integration

### Ad Placement Locations

The website includes the following AdSense-ready placeholders:

1. **Homepage Top Banner**: After header (728x90)
2. **Homepage In-Feed**: Between article cards (every 4 articles)
3. **Sidebar Squares**: Right sidebar (300x250) × 2
4. **Article Page Header**: After title (728x90)
5. **Article In-Content**: Within article body (336x280)
6. **Article End**: After content (336x280)

### Implementation Steps

1. **Apply for AdSense**: Visit [google.com/adsense](https://www.google.com/adsense)
2. **Add Site**: Add your domain to AdSense
3. **Create Ad Units**: Create responsive ad units for each placement
4. **Replace Placeholders**: Edit template files and replace placeholder divs:

```html
<!-- Replace this: -->
<div class="ad-placeholder">
    <small>Advertisement</small>
</div>

<!-- With your AdSense code: -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"
     data-ad-slot="XXXXXXXXXX"
     data-ad-format="auto"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
```

### AdSense Best Practices

✅ **DO:**
- Use responsive ad units
- Place ads in natural reading flow
- Maintain clear distinction between content and ads
- Monitor ad performance in AdSense dashboard
- Follow Google's ad placement policies

❌ **DON'T:**
- Place ads too close together
- Put ads above the fold exclusively
- Encourage clicks ("Click here", etc.)
- Use misleading ad labels
- Exceed recommended ad density

### Required: Privacy Policy Page

Create `/public/privacy.php` with:
- Data collection disclosure
- Cookie usage (AdSense cookies)
- Third-party services (Google AdSense, Analytics)
- User rights (GDPR, CCPA compliance)

**Template**: Use [Google's Privacy Policy Generator](https://www.privacypolicygenerator.info/)

## 🔍 SEO Configuration

### On-Page SEO (Automatic)
- ✅ Unique meta titles per page
- ✅ Meta descriptions (160 chars)
- ✅ Canonical URLs
- ✅ Open Graph tags (Facebook)
- ✅ Twitter Card tags
- ✅ JSON-LD Article schema
- ✅ Semantic HTML (H1, H2, etc.)
- ✅ Alt text for images (set in admin)
- ✅ Clean URL structure (slug-based)

### Sitemap Generation

**Manual generation:**
```php
// Add to admin panel or run via cron
require_once 'includes/functions.php';
generateSitemap();
```

**Automatic with cron:**
```bash
# Edit crontab
crontab -e

# Add line (regenerate daily at 2 AM):
0 2 * * * cd /var/www/html/global-insights && php -r "require 'includes/functions.php'; generateSitemap();"
```

**Submit to search engines:**
- Google Search Console: https://search.google.com/search-console
- Bing Webmaster Tools: https://www.bing.com/webmasters

### robots.txt

Located at `/public/robots.txt` - Update sitemap URL:
```
Sitemap: https://yourdomain.com/public/sitemap.xml
```

### Google Analytics (Optional)

Add before `</head>` in `/templates/header.php`:

```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

## ⚡ Performance Optimization

### Enable Gzip Compression

Already configured in `.htaccess`. Verify:
```bash
curl -H "Accept-Encoding: gzip" -I https://yourdomain.com
# Look for: Content-Encoding: gzip
```

### Image Optimization

**Before upload:**
- Use tools like TinyPNG, ImageOptim
- Recommended size: Max 1920px width
- Format: WebP preferred, JPG for photos, PNG for graphics

**Server-side (optional):**
Install ImageMagick for automatic compression:
```bash
sudo apt-get install imagemagick php-imagick
```

### Enable OPcache

Edit `/etc/php/8.1/apache2/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### Database Optimization

```bash
# Regular optimization (monthly cron)
mysqlcheck -u gi_user -p --optimize --all-databases
```

### CDN Integration (Optional)

For static assets (CSS, JS, images):
- CloudFlare (free tier available)
- BunnyCDN
- AWS CloudFront

## 🛡️ Security Hardening

### PHP Security Settings

Edit `php.ini`:
```ini
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
file_uploads = On
upload_max_filesize = 5M
post_max_size = 6M
max_execution_time = 30
memory_limit = 128M
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

### MySQL Security

```bash
# Run MySQL security script
sudo mysql_secure_installation

# Remove test database and anonymous users
# Set strong root password
# Disallow root login remotely
```

### Firewall Configuration

```bash
# Enable UFW
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Regular Updates

```bash
# System updates
sudo apt-get update && sudo apt-get upgrade

# Check for WordPress-style security notices
# Subscribe to PHP security mailing list
```

### File Integrity Monitoring

Install AIDE or similar:
```bash
sudo apt-get install aide
sudo aideinit
```

### Backup Strategy

**Database backup (daily cron):**
```bash
#!/bin/bash
# /usr/local/bin/backup-gi-db.sh
DATE=$(date +%Y%m%d)
mysqldump -u gi_user -pYourPassword global_insights | gzip > /backups/gi_db_$DATE.sql.gz
# Keep only last 30 days
find /backups -name "gi_db_*.sql.gz" -mtime +30 -delete
```

**Files backup:**
```bash
# Weekly full backup
tar -czf /backups/gi_files_$(date +%Y%m%d).tar.gz /var/www/html/global-insights
```

## 🐛 Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Apache error log
sudo tail -f /var/log/apache2/error.log

# Common causes:
# 1. .htaccess syntax error - validate syntax
# 2. PHP error - enable error display temporarily
# 3. File permissions - check ownership
sudo chown -R www-data:www-data /var/www/html/global-insights
```

### Issue: Database Connection Failed

**Solution:**
```bash
# Verify credentials in .env
# Test MySQL connection
mysql -u gi_user -p global_insights

# Check MySQL service
sudo systemctl status mysql
```

### Issue: Images Not Uploading

**Solution:**
```bash
# Check upload directory permissions
ls -la uploads/
chmod 755 uploads/
chmod 755 uploads/articles/

# Check PHP upload settings
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Increase if needed in php.ini
```

### Issue: White Screen (No Error)

**Solution:**
```bash
# Enable error display temporarily
# Edit config.php:
define('DEBUG_MODE', true);

# Check PHP error log
sudo tail -f /var/log/php_errors.log
```

### Issue: Slow Page Load

**Solution:**
```bash
# Enable query logging temporarily
# In MySQL:
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

# Analyze slow queries
sudo mysqldumpslow /var/log/mysql/slow-query.log

# Add indexes if needed
# Check server resources
htop
```

## 📊 Features Implemented Checklist

### User-Side Features
- ✅ Homepage with featured article and pagination
- ✅ Individual article pages with full content
- ✅ Category pages with filtered articles
- ✅ Tag-based article filtering
- ✅ Search functionality (full-text)
- ✅ Like system (IP + cookie tracking)
- ✅ Comment system with moderation
- ✅ Social sharing (5 platforms)
- ✅ Responsive mobile design
- ✅ AdSense placeholder integration
- ✅ View counter per article
- ✅ Trending articles sidebar
- ✅ Related articles

### Admin Features
- ✅ Secure login with password hashing
- ✅ Dashboard with statistics
- ✅ Create/Edit/Delete articles
- ✅ Rich text editor (TinyMCE)
- ✅ Image upload and management
- ✅ Category management
- ✅ Tag system
- ✅ Comment moderation
- ✅ Featured article selection
- ✅ Draft/Published/Archived status
- ✅ SEO meta fields
- ✅ Article view/like stats

### Security Features
- ✅ PDO prepared statements
- ✅ XSS prevention (output escaping)
- ✅ CSRF token protection
- ✅ Rate limiting (likes, comments)
- ✅ Secure file uploads
- ✅ Session security
- ✅ Input validation
- ✅ Password hashing
- ✅ IP tracking for abuse prevention

### SEO Features
- ✅ Meta titles and descriptions
- ✅ Open Graph tags
- ✅ Twitter Cards
- ✅ JSON-LD schema
- ✅ Sitemap generation
- ✅ robots.txt
- ✅ Canonical URLs
- ✅ Semantic HTML
- ✅ Clean URL structure
- ✅ Breadcrumbs

### Performance
- ✅ Gzip compression
- ✅ Browser caching headers
- ✅ Lazy loading images
- ✅ Database indexing
- ✅ Efficient queries

## 🎨 Customization

### Changing Theme Colors

Edit `/public/assets/css/style.css`:
```css
:root {
    --primary-color: #0d6efd; /* Change to your brand color */
    --secondary-color: #6c757d;
    --dark-color: #212529;
    --light-color: #f8f9fa;
}
```

### Adding More Categories

```sql
INSERT INTO categories (name, slug, description) VALUES
('Your Category', 'your-category', 'Description here');
```

### Changing Items Per Page

Edit `.env`:
```env
ARTICLES_PER_PAGE=15
```

## 📧 Email Configuration (Optional)

For contact forms and notifications, configure SMTP:

**Install PHPMailer:**
```bash
composer require phpmailer/phpmailer
```

**Configure in functions.php:**
```php
use PHPMailer\PHPMailer\PHPMailer;

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';
    $mail->Password = 'your-app-password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('noreply@yourdomain.com', SITE_NAME);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;
    
    return $mail->send();
}
```

## 🔄 Updating the Application

```bash
# Backup first
mysqldump -u gi_user -p global_insights > backup_before_update.sql
tar -czf files_backup.tar.gz /var/www/html/global-insights

# Download new version
# Extract and copy .env from old version
# Run any new SQL migrations if provided

# Clear cache (if OPcache enabled)
sudo systemctl restart apache2
```

## 📞 Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor error logs
- Check disk space
- Review failed login attempts

**Weekly:**
- Review pending comments
- Check backup integrity
- Update content

**Monthly:**
- Update system packages
- Optimize database
- Review site analytics
- Check SSL expiration

## 📝 License

This project is built for production use. Modify as needed for your requirements.

## 🏆 Best Practices Summary

1. **Always use HTTPS** in production
2. **Change default admin password** immediately
3. **Enable backups** before making changes
4. **Monitor logs** regularly
5. **Keep software updated** (PHP, MySQL, Apache)
6. **Test on staging** before production updates
7. **Use strong passwords** for database and admin
8. **Enable rate limiting** to prevent abuse
9. **Configure proper error logging** (not display)
10. **Implement monitoring** (uptime, performance)

## 🌐 Production Deployment Checklist

- [ ] SSL certificate installed and configured
- [ ] .env file configured with production values
- [ ] DEBUG_MODE set to false
- [ ] Default admin password changed
- [ ] Database user has minimal required privileges
- [ ] File permissions set correctly (755 dirs, 644 files)
- [ ] Apache mod_rewrite enabled
- [ ] Firewall configured
- [ ] Backups scheduled (daily DB, weekly files)
- [ ] Error logging enabled and monitored
- [ ] robots.txt updated with correct sitemap URL
- [ ] Google Search Console configured
- [ ] Analytics installed (Google Analytics)
- [ ] AdSense implemented and ads.txt uploaded
- [ ] Privacy Policy page created
- [ ] Contact page functional
- [ ] Email notifications working
- [ ] 404 error page customized
- [ ] Sitemap submitted to search engines
- [ ] Performance tested (GTmetrix, PageSpeed Insights)
- [ ] Security tested (SQL injection, XSS, CSRF)
- [ ] Mobile responsiveness verified
- [ ] Cross-browser testing completed

---

**Project**: Global Insights News Website  
**Version**: 1.0.0  
**Stack**: PHP 8.1, MySQL 8.0, Apache 2.4, Bootstrap 5  
**Status**: Production Ready  

For issues or questions, refer to the troubleshooting section or consult the inline code documentation.
#   n e w s  
 