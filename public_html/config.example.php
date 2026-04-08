<?php
// ============================================================
// SECRETS CONFIG — EXAMPLE FILE
// Copy this to config.php and fill in your real values
// ============================================================

// Admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('CHANGE_ME', PASSWORD_DEFAULT));

// OpenAFF
define('OPENAFF_BEARER_TOKEN', 'your-bearer-token-here');
define('OPENAFF_AFF_ID', '30045');
define('OPENAFF_OFFER_ID', '1737');

// TrackingWebDo (CH)
define('CH_API_KEY', 'your-ch-api-key');

// YourBestNetwork (QA)
define('QA_API_KEY', 'your-qa-api-key');
define('QA_REG_API_KEY', 'your-qa-reg-api-key');
define('QA_AFFC', 'your-affc');
define('QA_BXC', 'your-bxc');
define('QA_VTC', 'your-vtc');
define('QA_FTD_TOKEN', 'your-ftd-token');

// KV (QA sub-campaign)
define('KV_API_KEY', 'your-kv-api-key');
define('KV_AFFC', 'your-kv-affc');
define('KV_BXC', 'your-kv-bxc');
define('KV_VTC', 'your-kv-vtc');
define('KV_FTD_TOKEN', 'your-kv-ftd-token');

// Altstream (Trackbox)
define('ALTSTREAM_TB_USERNAME', 'your-tb-username');
define('ALTSTREAM_TB_PASSWORD', 'your-tb-password');
define('ALTSTREAM_TB_URL', 'https://tb.sendmetraffic.ink');
define('ALTSTREAM_AI', 'your-ai');
define('ALTSTREAM_CI', 'your-ci');
define('ALTSTREAM_GI', 'your-gi');
define('ALTSTREAM_TB_PUSH_API_KEY', 'your-push-key');
define('ALTSTREAM_TB_PULL_API_KEY', 'your-pull-key');

// 13Partners (QuakeAI)
define('PARTNERS13_USER_ID', 'your-user-id');
define('PARTNERS13_SOURCE', 'your-source-name');
define('PARTNERS13_BEARER_TOKEN', 'your-bearer-token');
define('PARTNERS13_API_URL', 'https://api.13partners.net');
define('PARTNERS13_LANDING', 'https://your-landing-url');
define('PARTNERS13_LANDING_NAME', 'YourLandingName');
