<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// --- LOCAL TESTING (Mailtrap) ---
// Signup at https://mailtrap.io/ and use your credentials below
$config['protocol']  = 'smtp';
$config['smtp_host'] = 'smtp.aakilarose.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = 'familymatch@aakilarose.com'; // <-- change this
$config['smtp_pass'] = 'boo56,zib,;z';   // <-- change this
$config['mailtype']  = 'html';
$config['charset']   = 'utf-8';
$config['newline']   = "\r\n";
$config['wordwrap']  = TRUE;

// --- PRODUCTION (Bluehost) ---
// Uncomment below and fill with your domain email details for live server
// $config['protocol']  = 'smtp';
// $config['smtp_host'] = 'mail.yourdomain.com';
// $config['smtp_port'] = 465; // or 587 for TLS
// $config['smtp_user'] = 'no-reply@yourdomain.com'; // <-- change this
// $config['smtp_pass'] = 'your_email_password';     // <-- change this
// $config['mailtype']  = 'html';
// $config['charset']   = 'utf-8';
// $config['newline']   = "\r\n";
// $config['wordwrap']  = TRUE;
// $config['smtp_crypto'] = 'ssl'; // or 'tls' for port 587 