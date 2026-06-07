# Filament CMS Pro (`nepal360/filament-cms-pro`)

An enterprise-grade, WordPress-class Content Management System (CMS) plugin package designed for Laravel and Filament v5. It features a Gutenberg-style block editor workspace, configurable editorial workflows, multilingual support, advanced SEO schema outputs, sitemaps automation, and a non-intrusive server-side analytics tracking engine.

## Key Features

1. **Gutenberg-Style Block Editor**: Renders visual block layouts (Heading, Paragraph, Hero, Quote, CTA, Images, Galleries, FAQs, and Polls).
2. **Editorial Approval Workflow**: Gated transitions (Draft -> Review -> Fact Check -> Editor Approval -> Publisher Approval -> Scheduled/Published) to control editorial routing.
3. **Non-Intrusive Server-Side Analytics**: Logs page views and unique users asynchronously in middleware without executing heavy tracking Javascript libraries in the browser. Zero impact on GSC Core Web Vitals (INP, CLS, LCP). Complies with GDPR cookieless privacy.
4. **Automated Sitemaps & Schema**: Auto-generates standard sitemaps, Google News sitemaps (last 48h posts), and injects JSON-LD article schemas.
5. **Polymorphic Translation Mapping**: Fully localized articles, categories, and tags database schemas.

## Documentation

Interactive developer guides, installation procedures, and block extension tutorials are hosted live at: [https://tsrgtm.github.io/Filament-CMS-PRO/](https://tsrgtm.github.io/Filament-CMS-PRO/)

## Installation and Quickstart

Since the package is hosted on GitHub, you need to define it as a repository inside your Laravel application's `composer.json` file before running composer require:

### Step 1: Add Repository to `composer.json`
Open your Laravel application's `composer.json` and add the `repositories` block:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Tsrgtm/Filament-CMS-PRO.git"
    }
]
```

### Step 2: Install Package
Run the following composer require command:

```bash
composer require nepal360/filament-cms-pro:dev-main
```

Please refer to the installation steps and Custom Block Extension guide detailed inside the [live documentation page](https://tsrgtm.github.io/Filament-CMS-PRO/).
