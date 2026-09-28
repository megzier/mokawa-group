<?php
/**
 * ============================================================
 * MOKAWA GROUP — Email Test File
 * Upload this to your server root alongside contact-api.php
 * Open in browser: yourdomain.com/test-email.php
 * DELETE THIS FILE after testing is confirmed working!
 * ============================================================
 */

// Quick security: only run from browser (not CLI)
if (php_sapi_name() === 'cli') { die('Run from browser only.'); }

// ─── CONFIG (same as contact-api.php) ───────────────────────────
$SMTP_HOST = 'smtp.zoho.com';
$SMTP_PORT = 465;
$SMTP_USER = 'martin@mokawagroup.com';
$SMTP_PASS = '3bYM1dp8vg8p';
$MAIL_FROM = 'martin@mokawagroup.com';
$MAIL_TO   = 'martin@mokawagroup.com';

$testMode = isset($_GET['send']);
$result   = null;
$log      = [];

if ($testMode) {
    // Run connectivity + send test
    $log[] = "⏱ " . date('Y-m-d H:i:s') . " — Starting test...";
    $log[] = "🔧 PHP Version: " . PHP_VERSION;
    $log[] = "🔧 OpenSSL: " . (extension_loaded('openssl') ? '✅ Available' : '❌ MISSING — SSL connections will fail!');

    // Test 1: Socket connect
    $ctx = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]]);
    $log[] = "🔌 Connecting to ssl://{$SMTP_HOST}:{$SMTP_PORT}...";

    $socket = @stream_socket_client("ssl://{$SMTP_HOST}:{$SMTP_PORT}", $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $ctx);

    if (!$socket) {
        $log[] = "❌ CONNECTION FAILED: $errstr ($errno)";
        $log[] = "💡 Check: Is outbound port 465 open on your server? Contact your hosting provider.";
        $result = 'fail_connect';
    } else {
        $log[] = "✅ Connected to SMTP server!";
        stream_set_timeout($socket, 20);

        $read = function() use ($socket) {
            $r = '';
            while ($line = fgets($socket, 515)) {
                $r .= $line;
                if ($line[3] === ' ') break;
            }
            return trim($r);
        };

        $cmd = function($c, $label = '') use ($socket, $read, &$log) {
            if ($label) $log[] = "📤 $label";
            fwrite($socket, $c . "\r\n");
            $r = $read();
            $log[] = "📥 " . htmlspecialchars($r);
            return $r;
        };

        $banner = $read();
        $log[] = "📥 Banner: " . htmlspecialchars($banner);

        $ehlo = $cmd('EHLO mokawagroup.com', 'Sending EHLO...');

        $r = $cmd('AUTH LOGIN', 'Sending AUTH LOGIN...');
        if (substr($r, 0, 3) === '334') {
            $r = $cmd(base64_encode($SMTP_USER), 'Sending username...');
            if (substr($r, 0, 3) === '334') {
                $r = $cmd(base64_encode($SMTP_PASS), 'Sending password...');
                if (substr($r, 0, 3) === '235') {
                    $log[] = "✅ AUTHENTICATION SUCCESSFUL!";

                    // Now send a real test email
                    $cmd("MAIL FROM:<{$MAIL_FROM}>", "MAIL FROM...");
                    $cmd("RCPT TO:<{$MAIL_TO}>",   "RCPT TO...");
                    $r = $cmd('DATA', 'DATA command...');

                    if (substr($r, 0, 3) === '354') {
                        $boundary = 'TestBoundary_' . md5(time());
                        $ts = date('d M Y H:i:s');

                        $html = "<html><body style='font-family:Arial;color:#333;'>
                          <div style='background:#2C2416;padding:20px;text-align:center;'>
                            <h2 style='color:#C9A961;'>✅ Mokawa Email Test</h2>
                          </div>
                          <div style='padding:20px;'>
                            <p>This is a test email from your Mokawa Group website.</p>
                            <p><strong>If you received this, your email is working correctly! 🎉</strong></p>
                            <p style='color:#999;font-size:12px;'>Sent: $ts EAT</p>
                          </div>
                        </body></html>";

                        $plain = "Mokawa Email Test\n\nIf you received this, your email is working correctly!\n\nSent: $ts EAT";

                        $msg  = "Date: " . date('r') . "\r\n";
                        $msg .= "Message-ID: <test." . time() . "@mokawagroup.com>\r\n";
                        $msg .= "From: Mokawa Website Test <{$MAIL_FROM}>\r\n";
                        $msg .= "To: Martin - Mokawa Group <{$MAIL_TO}>\r\n";
                        $msg .= "Subject: =?UTF-8?B?" . base64_encode('✅ Mokawa Website Email Test — ' . $ts) . "?=\r\n";
                        $msg .= "MIME-Version: 1.0\r\n";
                        $msg .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n";
                        $msg .= "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                        $msg .= chunk_split(base64_encode($plain)) . "\r\n";
                        $msg .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                        $msg .= chunk_split(base64_encode($html)) . "\r\n";
                        $msg .= "--$boundary--\r\n";

                        foreach (explode("\r\n", $msg) as $line) {
                            if (strlen($line) && $line[0] === '.') $line = '.' . $line;
                            fwrite($socket, $line . "\r\n");
                        }

                        $r = $cmd('.', 'End of DATA...');
                        if (substr($r, 0, 3) === '250') {
                            $log[] = "✅ TEST EMAIL SENT SUCCESSFULLY to {$MAIL_TO}!";
                            $result = 'success';
                        } else {
                            $log[] = "❌ Message rejected: $r";
                            $result = 'fail_send';
                        }
                    } else {
                        $log[] = "❌ DATA command failed";
                        $result = 'fail_data';
                    }
                } else {
                    $log[] = "❌ WRONG PASSWORD — Check your Zoho password or create an App Password in Zoho settings.";
                    $result = 'fail_pass';
                }
            } else {
                $log[] = "❌ Username rejected";
                $result = 'fail_user';
            }
        } else {
            $log[] = "❌ AUTH LOGIN failed: $r";
            $result = 'fail_auth';
        }

        $cmd('QUIT', 'QUIT');
        fclose($socket);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mokawa Email Test</title>
<style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#f5f0e8;margin:0;padding:20px;}
  .card{background:white;max-width:700px;margin:30px auto;border-radius:12px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.12);}
  .header{background:linear-gradient(135deg,#2C2416,#8B6F47);padding:30px;text-align:center;}
  .header h1{color:#C9A961;margin:0;font-size:24px;}
  .header p{color:rgba(255,255,255,.7);margin:8px 0 0;font-size:14px;}
  .body{padding:30px;}
  .warning{background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:15px;margin-bottom:25px;font-size:14px;color:#856404;}
  .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:25px;}
  .info-item{background:#f9f7f4;padding:12px 15px;border-radius:8px;border-left:3px solid #C9A961;}
  .info-item small{color:#999;font-size:11px;text-transform:uppercase;display:block;margin-bottom:4px;}
  .info-item strong{color:#2C2416;font-size:15px;}
  .btn{display:inline-block;background:linear-gradient(135deg,#C9A961,#8B6430);color:#2C2416;padding:14px 35px;border-radius:30px;font-weight:bold;font-size:16px;text-decoration:none;border:none;cursor:pointer;width:100%;text-align:center;box-sizing:border-box;margin-top:10px;}
  .btn:hover{opacity:.9;}
  .log{margin-top:25px;background:#1a1410;border-radius:10px;padding:20px;max-height:400px;overflow-y:auto;}
  .log h3{color:#C9A961;margin:0 0 15px;font-size:14px;text-transform:uppercase;letter-spacing:1px;}
  .log-line{font-family:'Courier New',monospace;font-size:13px;color:#ccc;margin:4px 0;line-height:1.5;}
  .success-box{background:#d4edda;border:1px solid #28a745;border-radius:8px;padding:20px;text-align:center;color:#155724;margin-top:20px;}
  .fail-box{background:#f8d7da;border:1px solid #dc3545;border-radius:8px;padding:20px;color:#721c24;margin-top:20px;}
  .fail-box ul{margin:10px 0;padding-left:20px;}
  .tips{background:#e8f4f8;border:1px solid #17a2b8;border-radius:8px;padding:15px;margin-top:15px;}
  .tips h4{color:#0c5460;margin:0 0 10px;}
  .tips ol{margin:0;padding-left:20px;color:#0c5460;font-size:14px;}
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <h1>🔧 Mokawa Email Test</h1>
    <p>Zoho SMTP Connection & Send Test</p>
  </div>
  <div class="body">

    <div class="warning">
      ⚠️ <strong>Security Notice:</strong> Delete this file from your server after testing is complete!
      It contains SMTP credentials.
    </div>

    <div class="info-grid">
      <div class="info-item">
        <small>SMTP Server</small>
        <strong>smtp.zoho.com</strong>
      </div>
      <div class="info-item">
        <small>Port / Security</small>
        <strong>465 / SSL</strong>
      </div>
      <div class="info-item">
        <small>Sending From</small>
        <strong><?= htmlspecialchars($SMTP_USER) ?></strong>
      </div>
      <div class="info-item">
        <small>Sending To</small>
        <strong><?= htmlspecialchars($MAIL_TO) ?></strong>
      </div>
      <div class="info-item">
        <small>PHP Version</small>
        <strong><?= PHP_VERSION ?></strong>
      </div>
      <div class="info-item">
        <small>OpenSSL</small>
        <strong><?= extension_loaded('openssl') ? '✅ Available' : '❌ Missing' ?></strong>
      </div>
    </div>

    <?php if (!$testMode): ?>
      <p style="color:#666;font-size:15px;margin-bottom:20px;">
        Click the button below to test the SMTP connection and send a real test email to 
        <strong><?= htmlspecialchars($MAIL_TO) ?></strong>.
      </p>
      <a href="?send=1" class="btn">📧 Send Test Email Now</a>

    <?php else: ?>

      <?php if ($result === 'success'): ?>
        <div class="success-box">
          <h2 style="margin:0 0 10px;">🎉 Email Sent Successfully!</h2>
          <p style="margin:0;">Check your inbox at <strong><?= htmlspecialchars($MAIL_TO) ?></strong>.<br>
          Your contact form is now working correctly!</p>
        </div>

      <?php elseif ($result === 'fail_connect'): ?>
        <div class="fail-box">
          <h3>❌ Cannot Connect to Zoho SMTP</h3>
          <ul>
            <li>Your hosting server may be blocking outbound port <strong>465</strong></li>
            <li>Contact your hosting provider and ask them to allow outbound SMTP on port 465</li>
            <li>Try port <strong>587</strong> with TLS as an alternative</li>
          </ul>
        </div>

      <?php elseif ($result === 'fail_pass'): ?>
        <div class="fail-box">
          <h3>❌ Password Rejected by Zoho</h3>
          <ul>
            <li>If you have 2FA enabled on Zoho, you must use an <strong>App Password</strong></li>
            <li>Go to: Zoho Mail → Settings → Security → App Passwords → Generate</li>
            <li>Use the generated app password instead of your regular password</li>
          </ul>
        </div>

      <?php else: ?>
        <div class="fail-box">
          <h3>❌ Test Failed</h3>
          <p>See the log below for details.</p>
        </div>
      <?php endif; ?>

      <div class="log">
        <h3>📋 Connection Log</h3>
        <?php foreach ($log as $line): ?>
          <div class="log-line"><?= $line ?></div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:20px;text-align:center;">
        <a href="test-email.php" class="btn" style="background:#666;color:white;">🔄 Run Test Again</a>
      </div>

      <div class="tips">
        <h4>💡 If tests fail — common Zoho SMTP fixes:</h4>
        <ol>
          <li>Log in to <strong>mail.zoho.com</strong> → Settings → Security → enable <em>IMAP/SMTP Access</em></li>
          <li>If 2FA is enabled → Settings → Security → <em>App Passwords</em> → generate one and use it here</li>
          <li>Ask your hosting provider to open outbound port <strong>465</strong> (SSL) or <strong>587</strong> (TLS)</li>
          <li>Check Zoho's sending limits (free plan: 200 emails/day)</li>
        </ol>
      </div>
    <?php endif; ?>

  </div>
</div>
</body>
</html>