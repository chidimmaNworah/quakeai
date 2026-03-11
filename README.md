# QuakeAI — Multi-Campaign Affiliate Platform

A multi-campaign affiliate lead generation platform built with PHP, HTML, CSS, and JavaScript. Each campaign has its own landing page, registration proxy, and leads/FTD dashboard.

## Campaigns

| Folder | Network | Description |
|--------|---------|-------------|
| `openaff/` | OpenAFF | Landing page + admin-protected dashboard |
| `algo/` | AlgoLead | Lead registration + leads/FTD tracking |
| `ch/` | TrackingWebDo | Lead registration + leads/FTD tracking |
| `qa/` | YourBestNetwork | Lead registration + leads/FTD tracking |
| `qa/kv/` | YourBestNetwork (KV) | Sub-campaign variant |

## Project Structure

```
public_html/
├── index.html              # Campaign hub page
├── config.php              # Secrets (gitignored)
├── config.example.php      # Secrets template
├── openaff/
│   ├── index.html          # Landing page
│   ├── script.js           # Form logic & validation
│   ├── reg_leads.php       # Lead registration proxy
│   ├── leads_proxy.php     # Status pulling proxy (auth-protected)
│   ├── dashboard.php       # Admin dashboard (session-protected)
│   ├── login.php           # Admin login
│   └── auth_check.php      # Auth helpers
├── algo/                   # AlgoLead campaign
├── ch/                     # TrackingWebDo campaign
└── qa/                     # YourBestNetwork campaign
    └── kv/                 # KV sub-campaign
```

## Setup

### 1. Configure secrets

```bash
cp public_html/config.example.php public_html/config.php
```

Edit `config.php` and fill in your API keys, tokens, and admin credentials.

### 2. Run locally

```bash
cd public_html
php -S localhost:8000
```

Open `http://localhost:8000` to see the campaign hub.

### 3. Deploy to cPanel

Upload the `public_html/` folder contents to your server's `public_html/` directory via File Manager or FTP. Make sure `config.php` is uploaded — all PHP files depend on it.

## Security

- All API keys and tokens are stored in `config.php` (server-side only, gitignored)
- No secrets are exposed in client-side JavaScript
- Admin dashboard is protected with PHP sessions and password hashing
- Proxies handle all API communication server-side

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 8.x (cURL for API calls)
- **Auth:** PHP sessions with `password_hash` / `password_verify`
- **Hosting:** Namecheap shared hosting (cPanel)
