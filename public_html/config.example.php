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

// AlgoLead
define('ALGO_AUTH', 'your-algo-auth-key');
define('ALGO_TOKEN', 'your-algo-token');
define('ALGO_PARTNER_ID', '18');
define('ALGO_TRACKING_ID', '1085987');
define('ALGO_SUBCAMPAIGN_ID', '58391');

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
