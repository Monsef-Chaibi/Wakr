# Landing Page Setup with Laravel Localization & Vite

## 📋 Overview

The landing page has been refactored to use:
- **Laravel Localization** (JSON files) for English and Arabic
- **Vite** for CSS and JavaScript bundling
- **Blade templating** with localization helpers

## 🗂️ File Structure

```
resources/
├── lang/
│   ├── en.json          # English translations
│   └── ar.json          # Arabic translations
├── css/
│   └── landing.css      # Landing page styles (processed by Vite)
├── js/
│   └── landing.js       # Landing page scripts (processed by Vite)
└── views/
    └── welcome.blade.php    # Landing page template
```

## 🔧 Configuration

### Vite Configuration
The `vite.config.js` has been updated to include:
```javascript
input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/landing.css', 'resources/js/landing.js']
```

### Routes
Routes are configured to support language prefixes:
- `/` - Default (English)
- `/en` - English
- `/ar` - Arabic

### Localization Files
- `resources/lang/en.json` - English strings
- `resources/lang/ar.json` - Arabic strings

## 🚀 Usage

### In Blade Templates
Use the `__()` helper to access translations:
```blade
{{ __('hero_title') }}
{{ __('nav_features') }}
```

### Language Switching
Click language switcher buttons to navigate:
- English: `switchLanguage('en')` → redirects to `/en`
- Arabic: `switchLanguage('ar')` → redirects to `/ar`

### Checking Current Language
```blade
{{ app()->getLocale() }}  <!-- Returns 'en' or 'ar' -->
```

## 📝 Translation Keys

All keys follow a consistent naming pattern:
- Navigation: `nav_*` (e.g., `nav_home`, `nav_features`)
- Hero section: `hero_*` (e.g., `hero_title`, `hero_subtitle`)
- Features: `feature_*` (e.g., `feature_birds_title`)
- Footer: `footer_*` (e.g., `footer_privacy`)

## 🛠️ Development

### Build Assets
```bash
npm run dev    # Development mode
npm run build  # Production build
```

### View Localization Files
Edit translation strings in:
- `resources/lang/en.json`
- `resources/lang/ar.json`

### Styling
CSS is in `resources/css/landing.css` and is automatically processed by Vite.

### JavaScript
JavaScript is in `resources/js/landing.js` with functions like:
- `switchLanguage(lang)` - Switch between languages
- `initializeLanguageSwitcher()` - Setup language switcher UI
- `initializeSmoothScrolling()` - Smooth scroll animations

## 🌍 RTL Support

RTL (Right-to-Left) layouts are automatically handled:
1. When Arabic is selected, `.rtl` class is added to `<html>`
2. CSS includes RTL-specific rules prefixed with `html.rtl`
3. Direction and text-align are automatically adjusted

## ✨ Features

- ✅ Full bilingual support (English & Arabic)
- ✅ Automatic RTL layout switching
- ✅ Language preference stored in localStorage
- ✅ Smooth page transitions with language switching
- ✅ Vite asset processing for optimization
- ✅ Clean separation of concerns (CSS, JS, HTML, Translations)
- ✅ Laravel localization standards

## 🔄 How Language Switching Works

1. User clicks language button (EN/AR)
2. `switchLanguage()` JavaScript function is called
3. Language preference is stored in localStorage
4. Page redirects to `/en` or `/ar` route
5. Laravel sets app locale via `App::setLocale($locale)`
6. Blade templates render with appropriate translations
7. HTML lang attribute and RTL class are set
8. CSS applies RTL-specific styles if needed
