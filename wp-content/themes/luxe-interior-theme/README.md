# Luxe Interior — WordPress Theme

A sophisticated editorial WordPress theme crafted for interior design studios.
Warm ivory palette · Editorial typography · Portfolio-focused layouts

---

## INSTALLATION

1. Download `luxe-interior-theme.zip`
2. In WordPress Admin → **Appearance → Themes → Add New → Upload Theme**
3. Upload the zip and click **Activate**

---

## INITIAL SETUP

### 1. Import Sample Data
Go to **Tools → Import Sample Data** and click the button.
This creates:
- 6 Portfolio projects (Residential, Commercial, Retail)
- 5 Blog posts with full content
- 3 Testimonials
- Sample pages (About, Services, Portfolio, Journal, Contact)

### 2. Set Homepage
- Go to **Settings → Reading**
- Set "Your homepage displays" to **A static page**
- Homepage: **Home** | Posts page: **Journal**

### 3. Set Navigation Menu
- Go to **Appearance → Menus**
- Create a menu with: About, Services, Portfolio, Journal, Contact
- Assign to **Primary Navigation** location

### 4. Customize Theme
Go to **Appearance → Customize** and configure:

**Hero Section**
- Hero eyebrow text, title, subtitle
- Upload a hero background image (recommended: 1920×1080)

**Contact Information**
- Studio address, phone, email, hours

**Social Media Links**
- Instagram, Pinterest, Facebook, Houzz URLs

**Theme Colors**
- Accent color (default: #B08D6A warm gold)

---

## THEME FEATURES

- ✅ Custom Post Types: Portfolio, Testimonials
- ✅ Portfolio taxonomy filter (AJAX)
- ✅ WordPress Customizer settings
- ✅ Full Gutenberg/Block Editor support
- ✅ Responsive (Mobile-first)
- ✅ Custom image sizes
- ✅ Smooth reveal animations
- ✅ Custom cursor (desktop)
- ✅ Lightbox for portfolio images
- ✅ 3 navigation menu locations
- ✅ 3 widget areas
- ✅ SEO-friendly markup
- ✅ Accessible (ARIA labels, semantic HTML)

---

## PAGE TEMPLATES

| Template File | Description |
|---|---|
| `front-page.php` | Homepage with hero, portfolio grid, testimonials, blog |
| `page-about.php` | About page with stats, values, team |
| `page-portfolio.php` | Full portfolio archive with filtering |
| `page-contact.php` | Contact page with form and info |
| `page-services.php` | Services overview |
| `single.php` | Individual blog post |
| `index.php` | Blog archive |
| `404.php` | Custom error page |

---

## RECOMMENDED PLUGINS

- **Contact Form 7** — for the contact form
- **Yoast SEO** — search engine optimization
- **WP Smush** — image optimization
- **WooCommerce** — if you want to sell products

---

## CUSTOMIZATION

The theme uses CSS custom properties (variables) for all design tokens.
To change colors, fonts, or spacing, edit the `:root {}` block in `style.css`.

Key variables:
```css
--color-accent:    #B08D6A;   /* Gold accent */
--color-charcoal:  #1C1C1A;   /* Dark text */
--color-cream:     #F7F3EE;   /* Section backgrounds */
--font-display:    'Cormorant Garamond', serif;
--font-body:       'Jost', sans-serif;
```

---

## ADDING PORTFOLIO PROJECTS

1. Go to **Portfolio → Add New**
2. Add title, description, and featured image
3. Assign a Portfolio Category (Residential / Commercial / Retail / Hospitality)
4. Click Publish

---

## SUPPORT & CUSTOMIZATION

This theme is production-ready. For further customization, a child theme is recommended:
create a folder `luxe-interior-child` with a `style.css` that references the parent theme.

---

*Theme Version: 1.0.0 | Requires WordPress 6.0+ | PHP 8.0+*
