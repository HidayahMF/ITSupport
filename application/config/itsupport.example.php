<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| IT Support - application settings (template)
|--------------------------------------------------------------------------
| Copy this file to application/config/itsupport.php and fill in the
| values for your environment. itsupport.php is gitignored and will NOT
| be pushed to the repository, so it is safe to store real secrets there.
|
| Optional: every key below also honours a real environment variable of the
| same name (kebab-cased here for documentation; see the PHP file for names):
|   GEMINI_API_KEY, GEMINI_MODEL, WHATSAPP_GATEWAY_URL, WHATSAPP_RECIPIENTS
| Values are resolved with getenv() first and fall back to these defaults.
*/

// ------------------------------------------------------------------------
// Google Gemini API (SystemRequest "Tanya AI" feature)
// ------------------------------------------------------------------------
// Get a key at https://aistudio.google.com/. Leave the empty value below.
$config['gemini_api_key'] = getenv('GEMINI_API_KEY') ?: '';
$config['gemini_model']   = getenv('GEMINI_MODEL') ?: 'gemini-flash-lite-latest';

// ------------------------------------------------------------------------
// Internal WhatsApp notification gateway (Bantuan helpdesk)
// ------------------------------------------------------------------------
// Leave empty to disable WhatsApp notifications (tickets are still saved).
// Example: http://10.0.0.5:3000/api/send
$config['wa_gateway_url'] = getenv('WHATSAPP_GATEWAY_URL') ?: '';

// Recipient phone numbers, international format (628...).
// Env alternative WHATSAPP_RECIPIENTS=628...,628... (comma separated).
$env_recipients = getenv('WHATSAPP_RECIPIENTS');
$config['wa_recipients'] = $env_recipients
    ? array_map('trim', explode(',', $env_recipients))
    : array();