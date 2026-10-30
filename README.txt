SME DIGITAL COLLECTIVE - PHP + MySQL VERSION
===================================================
Plain PHP 8+ / MySQL (MariaDB) / HTML / CSS / JavaScript. No build tools. Works on cPanel / shared hosting, XAMPP, WAMP, MAMP.

FOLDERS
  /                 public pages: index, services, service, pricing, about, contact, request-quote, thank-you, faq, privacy, terms
  /admin            login, dashboard, inquiries, inquiry (details), clients, client, projects, services, team, settings, logout
  /css              style.css  (all styling, brand colours as variables)
  /js               main.js (menu, loading states)   quote.js (multi-step quote form)
  /includes         functions.php (database, security, helpers), header/footer, admin layout, quote_fields.php
  /config           config.php  <-- EDIT: database details, base URL, image file names
  /database         schema.sql (tables)   seed.sql (optional starter data; currently empty)
  /images           logo.svg, favicon.svg + put your photos here
  /uploads          reserved for future uploads (PHP execution disabled)

SETUP AND HOSTING
  1. Create a MySQL database and a dedicated, non-root database user with a strong password.
  2. Configure DB_HOST, DB_NAME, DB_USER, and DB_PASS in the hosting environment. The app allows root/blank-password
    defaults only on localhost; remote requests fail closed until dedicated credentials are configured.
  3. Export the existing local sme_agency database with structure and data using phpMyAdmin, then import it into the
      hosting database. This preserves the admin account, services, and settings. For an existing database, also run
      database/remember_me_migration.sql. Do not export test or personal data.
  4. Upload the application files over HTTPS. This project has no web installer; do not add one to the public server.
    Configure the virtual host/subfolder and verify BASE_URL links after upload.
  5. Sign in at /admin/login.php, rotate the admin password through Admin > Settings, and verify contact/quote forms.

CUSTOMISE
  Prices / features / service text ..... Admin > Services      (nothing hard-coded in pages)
  Contact details, colours, footer ..... Admin > Settings
  Admin login email/password ........... Admin > Settings > Admin Account
  Remember admin login ................. Optional 30-day token; revoked when the admin logs out
  Team ................................. Admin > Team
  Photos ............................... upload to /images and set names in config/config.php (hero, about, services)
                                         and in Admin > Services / Team. Use warm, professional business photos.

SECURITY BUILT IN
  PDO prepared statements, output escaping (htmlspecialchars), CSRF tokens on every form, password_hash/password_verify,
  session regeneration at login, HttpOnly + SameSite cookies, honeypot + database rate limiting on public forms and login,
  server-side validation, protected admin pages, .htaccess blocking config/includes/database folders.
  Use HTTPS on your host (free via Let's Encrypt in cPanel). Review the Privacy Policy and Terms drafts before launch.

NOT INCLUDED (by design)
  Online payments, email notifications, file uploads, password reset. Contact-form messages are saved as inquiries.

NOTE
  Email notifications, online payments, automated inquiry retention, and password reset are not implemented. New enquiries
  are stored in the database; an administrator must sign in to review them. Legal pages are drafts, not legal advice.

