<?php
/**
 * Mokawa Group — Contact Form Handler
 * Pure PHP SMTP — No external libraries needed
 * Zoho Mail: smtp.zoho.com | Port: 465 | SSL
 */

// ─── CONFIGURATION ───────────────────────────────────────────────
define('SMTP_HOST',     'smtp.zoho.com');
define('SMTP_PORT',     465);
define('SMTP_SECURE',   'ssl');
define('SMTP_USER',     'martin@mokawagroup.com');
define('SMTP_PASS',     '3bYM1dp8vg8p');
define('MAIL_FROM',     'martin@mokawagroup.com');
define('MAIL_FROM_NAME','Mokawa Group Website');
define('MAIL_TO',       'martin@mokawagroup.com');
define('MAIL_TO_NAME',  'Martin - Mokawa Group');
// ─────────────────────────────────────────────────────────────────

header('Content-Type: text/plain; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo 'Invalid request.';
    exit;
}

// ─── SANITIZE & VALIDATE ─────────────────────────────────────────
$name    = strip_tags(trim($_POST['name']    ?? ''));
$email   = filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL);
$subject = strip_tags(trim($_POST['subject'] ?? 'General Enquiry'));
$message = strip_tags(trim($_POST['message'] ?? ''));
$phone   = strip_tags(trim($_POST['phone']   ?? ''));

if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please fill in all required fields with valid information.';
    exit;
}

if (strlen($message) > 5000) {
    http_response_code(400);
    echo 'Message too long.';
    exit;
}

// ─── EMAIL CONTENT ───────────────────────────────────────────────
$emailSubject = "New Enquiry via Mokawa Website: $subject";

$htmlBody = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;color:#333;max-width:600px;margin:0 auto;">
  <div style="background:linear-gradient(135deg,#2C2416,#8B6F47);padding:30px;text-align:center;">
    <h1 style="color:#C9A961;margin:0;font-size:24px;">Mokawa Group</h1>
    <p style="color:rgba(255,255,255,0.8);margin:8px 0 0;font-size:14px;">New Website Enquiry</p>
  </div>
  <div style="padding:30px;background:#fff;">
    <table style="width:100%;border-collapse:collapse;">
      <tr>
        <td style="padding:10px 0;border-bottom:1px solid #eee;width:130px;"><strong style="color:#8B6F47;">Name</strong></td>
        <td style="padding:10px 0;border-bottom:1px solid #eee;">'.htmlspecialchars($name).'</td>
      </tr>
      <tr>
        <td style="padding:10px 0;border-bottom:1px solid #eee;"><strong style="color:#8B6F47;">Email</strong></td>
        <td style="padding:10px 0;border-bottom:1px solid #eee;"><a href="mailto:'.htmlspecialchars($email).'" style="color:#C9A961;">'.htmlspecialchars($email).'</a></td>
      </tr>
      '.($phone ? '<tr><td style="padding:10px 0;border-bottom:1px solid #eee;"><strong style="color:#8B6F47;">Phone</strong></td><td style="padding:10px 0;border-bottom:1px solid #eee;">'.htmlspecialchars($phone).'</td></tr>' : '').'
      <tr>
        <td style="padding:10px 0;border-bottom:1px solid #eee;"><strong style="color:#8B6F47;">Subject</strong></td>
        <td style="padding:10px 0;border-bottom:1px solid #eee;">'.htmlspecialchars($subject).'</td>
      </tr>
    </table>
    <div style="margin-top:25px;">
      <strong style="color:#8B6F47;display:block;margin-bottom:10px;">Message:</strong>
      <div style="background:#f9f7f4;padding:20px;border-left:4px solid #C9A961;border-radius:4px;line-height:1.7;white-space:pre-wrap;">'.htmlspecialchars($message).'</div>
    </div>
    <div style="margin-top:25px;text-align:center;">
      <a href="mailto:'.htmlspecialchars($email).'?subject=Re: '.htmlspecialchars($subject).'" style="background:#C9A961;color:#2C2416;padding:12px 30px;text-decoration:none;border-radius:25px;font-weight:bold;display:inline-block;">
        Reply to '.htmlspecialchars($name).'
      </a>
    </div>
  </div>
  <div style="background:#1a1410;padding:20px;text-align:center;">
    <p style="color:#aaa;font-size:12px;margin:0;">Sent from mokawagroup.com &nbsp;·&nbsp; '.date('d M Y, H:i').' EAT</p>
  </div>
</body></html>';

$plainBody = "NEW MOKAWA WEBSITE ENQUIRY\n==========================\n\n";
$plainBody .= "Name:    $name\n";
$plainBody .= "Email:   $email\n";
if ($phone) $plainBody .= "Phone:   $phone\n";
$plainBody .= "Subject: $subject\n\n";
$plainBody .= "Message:\n$message\n\n";
$plainBody .= "--\nSent: ".date('d M Y H:i')." EAT via mokawagroup.com";

// ─── SEND ────────────────────────────────────────────────────────
$result = zohoSmtpSend(
    SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS,
    MAIL_FROM, MAIL_FROM_NAME,
    MAIL_TO,   MAIL_TO_NAME,
    $emailSubject, $htmlBody, $plainBody,
    $email, $name
);

if ($result === true) {
    http_response_code(200);
    echo "Thank you, $name! Your message has been received. We will get back to you shortly.";
} else {
    error_log("Mokawa SMTP Error: $result");
    http_response_code(500);
    echo "Sorry, there was a problem sending your message. Please email us directly at martin@mokawagroup.com";
}

// ─────────────────────────────────────────────────────────────────
// PURE PHP SMTP FUNCTION — no PHPMailer needed
// ─────────────────────────────────────────────────────────────────
function zohoSmtpSend(
    $host, $port, $user, $pass,
    $from, $fromName, $to, $toName,
    $subject, $htmlBody, $plainBody,
    $replyTo = '', $replyToName = ''
) {
    // SSL context — disable peer verify for shared hosts
    $ctx = stream_context_create([
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]
    ]);

    $socket = @stream_socket_client(
        "ssl://{$host}:{$port}",
        $errno, $errstr, 30,
        STREAM_CLIENT_CONNECT, $ctx
    );

    if (!$socket) {
        return "SMTP connect failed: $errstr ($errno) — Check host/port/SSL settings";
    }

    stream_set_timeout($socket, 30);

    // Read one SMTP response (handles multi-line)
    $read = function() use ($socket) {
        $r = '';
        while ($line = fgets($socket, 515)) {
            $r .= $line;
            if ($line[3] === ' ') break;
        }
        return $r;
    };

    // Send command + read reply
    $cmd = function($c) use ($socket, $read) {
        fwrite($socket, $c . "\r\n");
        return $read();
    };

    // 1. Banner
    $banner = $read();
    if (substr($banner, 0, 3) !== '220') {
        fclose($socket); return "Bad banner: " . trim($banner);
    }

    // 2. EHLO
    $ehlo = $cmd('EHLO mokawagroup.com');
    if (substr($ehlo, 0, 3) !== '250') {
        fclose($socket); return "EHLO failed: " . trim($ehlo);
    }

    // 3. AUTH LOGIN
    $r = $cmd('AUTH LOGIN');
    if (substr($r, 0, 3) !== '334') {
        fclose($socket); return "AUTH LOGIN rejected: " . trim($r);
    }

    $r = $cmd(base64_encode($user));
    if (substr($r, 0, 3) !== '334') {
        fclose($socket); return "Username rejected: " . trim($r);
    }

    $r = $cmd(base64_encode($pass));
    if (substr($r, 0, 3) !== '235') {
        fclose($socket); return "Password rejected (wrong password or app password needed): " . trim($r);
    }

    // 4. Envelope
    $r = $cmd("MAIL FROM:<{$from}>");
    if (substr($r, 0, 3) !== '250') {
        fclose($socket); return "MAIL FROM rejected: " . trim($r);
    }

    $r = $cmd("RCPT TO:<{$to}>");
    if (substr($r, 0, 3) !== '250') {
        fclose($socket); return "RCPT TO rejected: " . trim($r);
    }

    $r = $cmd('DATA');
    if (substr($r, 0, 3) !== '354') {
        fclose($socket); return "DATA rejected: " . trim($r);
    }

    // 5. Build MIME message
    $boundary = '----MokawaMime_' . md5(uniqid(rand(), true));
    $msgId    = '<' . md5(uniqid()) . '@mokawagroup.com>';
    $date     = date('r');

    $encSubject  = '=?UTF-8?B?' . base64_encode($subject)  . '?=';
    $encFrom     = '=?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . '>';
    $encTo       = '=?UTF-8?B?' . base64_encode($toName)   . '?= <' . $to   . '>';
    $encReplyTo  = $replyTo
                 ? ('=?UTF-8?B?' . base64_encode($replyToName) . '?= <' . $replyTo . '>')
                 : $encFrom;

    $msg  = "Date: $date\r\n";
    $msg .= "Message-ID: $msgId\r\n";
    $msg .= "From: $encFrom\r\n";
    $msg .= "To: $encTo\r\n";
    $msg .= "Reply-To: $encReplyTo\r\n";
    $msg .= "Subject: $encSubject\r\n";
    $msg .= "MIME-Version: 1.0\r\n";
    $msg .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
    $msg .= "X-Mailer: Mokawa-PHP-Mailer/2.0\r\n";
    $msg .= "\r\n";

    // Plain part
    $msg .= "--$boundary\r\n";
    $msg .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $msg .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $msg .= chunk_split(base64_encode($plainBody)) . "\r\n";

    // HTML part
    $msg .= "--$boundary\r\n";
    $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
    $msg .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $msg .= chunk_split(base64_encode($htmlBody)) . "\r\n";

    $msg .= "--$boundary--\r\n";

    // Dot-stuffing & send
    foreach (explode("\r\n", $msg) as $line) {
        if (strlen($line) && $line[0] === '.') $line = '.' . $line;
        fwrite($socket, $line . "\r\n");
    }

    // End DATA
    $r = $cmd('.');
    if (substr($r, 0, 3) !== '250') {
        fclose($socket); return "Message rejected after DATA: " . trim($r);
    }

    $cmd('QUIT');
    fclose($socket);
    return true;
}