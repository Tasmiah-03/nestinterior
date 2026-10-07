# NestInterior

A WordPress-based interior design website project for showcasing premium residential and commercial spaces.

## Overview

This repository contains a complete WordPress installation customized for an interior design brand. It includes the core WordPress files, database structure, and theme assets needed to run a polished design-focused website.

## Features

- WordPress-powered content management
- Interior design and lifestyle branding
- Custom theme-ready structure
- Flexible page and portfolio layouts
- Database-backed publishing workflow

## Project Structure

```text
.
├── .htaccess
├── README.md
├── index.php
├── license.txt
├── wp-config.php
├── wp-config-sample.php
├── wp-content/
├── wp-admin/
├── wp-includes/
├── database/
├── wp-activate.php
├── wp-blog-header.php
├── wp-settings.php
└── ...
```

## Requirements

- PHP 7.4 or newer
- MySQL 5.5.5 or newer
- Apache or compatible web server
- Recommended: `mod_rewrite` and HTTPS

## Quick Start

1. Clone the repository to your local web server directory.
2. Create a database for the project.
3. Copy `wp-config-sample.php` to `wp-config.php` and update your database credentials.
4. Open the site in your browser.
5. Complete the WordPress installation wizard.

## Example Configuration

```php
define('DB_NAME', 'nestinterior');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
```

## Theme and Customization

This repository includes a theme directory under `wp-content/themes/` for interior design-focused pages and layouts. You can customize the visual style and content through the WordPress admin panel or directly in the theme files.

## Updating

Before updating WordPress:

- Back up the database
- Back up custom theme files
- Review plugin compatibility
- Run the site update through the WordPress admin workflow

## Resources

- [WordPress Documentation](https://wordpress.org/documentation/)
- [WordPress Support Forums](https://wordpress.org/support/forums/)
- [WordPress Developer Resources](https://developer.wordpress.org/)

## License

This project is released under the GNU General Public License v2 or later. See `license.txt` for the full license text.
