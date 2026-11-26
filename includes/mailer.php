<?php
$phpMailerDir = is_dir(__DIR__ . '/libs/phpmailer/src')
    ? __DIR__ . '/libs/phpmailer/src'
    : __DIR__ . '/../PHPMailer/src';

require_once $phpMailerDir . '/Exception.php';
require_once $phpMailerDir . '/PHPMailer.php';
require_once $phpMailerDir . '/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$last_mail_error = '';

function readMailerEnv($envPath = null)
{
    if ($envPath === null) {
        $envPath = __DIR__ . '/../.env';
    }
    if (!is_file($envPath) || !is_readable($envPath)) {
        return [];
    }
    $parsed = @parse_ini_file($envPath, false, INI_SCANNER_RAW);
    if (!is_array($parsed)) {
        return [];
    }
    $config = [];
    foreach ($parsed as $key => $val) {
        $strVal = trim((string) $val);
        $len = strlen($strVal);
        if ($len >= 2 && (($strVal[0] === '"' && $strVal[$len - 1] === '"') || ($strVal[0] === "'" && $strVal[$len - 1] === "'"))) {
            $strVal = substr($strVal, 1, -1);
        }
        $config[$key] = $strVal;
    }
    return $config;
}

function sendEmail($to, $subject, $body, $file = null)
{
    global $last_mail_error;
    $last_mail_error = '';

    $cfg = readMailerEnv();

    if (isset($cfg['SMTP_ENABLED']) && in_array(strtolower($cfg['SMTP_ENABLED']), ['false', '0', 'no'], true)) {
        $last_mail_error = 'SMTP email sending is disabled.';
        return false;
    }

    $to = trim((string) $to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $last_mail_error = 'Invalid recipient email address.';
        return false;
    }

    $host = trim((string) ($cfg['SMTP_HOST'] ?? ''));
    $port = (int) ($cfg['SMTP_PORT'] ?? 587);
    $username = trim((string) ($cfg['SMTP_USERNAME'] ?? ''));
    $password = (string) ($cfg['SMTP_PASSWORD'] ?? '');
    $secure = strtolower(trim((string) ($cfg['SMTP_SECURE'] ?? ($cfg['SMTP_ENCRYPTION'] ?? 'tls'))));
    $fromEmail = trim((string) ($cfg['SMTP_FROM_EMAIL'] ?? ''));
    $fromName = trim((string) ($cfg['SMTP_FROM_NAME'] ?? 'KarmaSetu'));
    $timeout = (int) ($cfg['SMTP_TIMEOUT'] ?? 10);

    if ($host === '' || $port <= 0 || $username === '' || $password === '' || $fromEmail === '') {
        $last_mail_error = 'SMTP configuration is incomplete.';
        error_log('KarmaSetu Mailer error: ' . $last_mail_error);
        return false;
    }

    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $last_mail_error = 'Invalid SMTP sender email configuration.';
        error_log('KarmaSetu Mailer error: ' . $last_mail_error);
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->Timeout = $timeout > 0 ? $timeout : 10;
        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        if ($secure === 'ssl' || $secure === 'smtps') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $senderName = $fromName !== '' ? $fromName : 'KarmaSetu';
        $mail->setFrom($fromEmail, $senderName);
        $mail->addReplyTo($fromEmail, $senderName);
        $mail->addAddress($to);

        if (is_string($file) && $file !== '' && is_file($file) && is_readable($file)) {
            $mail->addAttachment($file);
        }

        $mail->isHTML(true);
        $mail->Subject = (string) $subject;
        $mail->Body = (string) $body;
        $mail->AltBody = trim(strip_tags((string) $body));

        if ($mail->send()) {
            return true;
        }

        $last_mail_error = 'Unable to send email.';
        error_log('KarmaSetu Mailer error: ' . ($mail->ErrorInfo !== '' ? $mail->ErrorInfo : $last_mail_error));
        return false;
    } catch (Exception $e) {
        $rawError = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
        if ($password !== '') {
            $rawError = str_replace($password, '[REDACTED]', $rawError);
        }
        $last_mail_error = stripos($rawError, 'authenticate') !== false
            ? 'SMTP authentication failed.'
            : $rawError;
        error_log('KarmaSetu Mailer error: ' . $last_mail_error);
        return false;
    } catch (\Throwable $e) {
        $last_mail_error = 'Unexpected email error.';
        error_log('KarmaSetu Mailer failure: ' . $e->getMessage());
        return false;
    }
}

function getMailError()
{
    global $last_mail_error;
    return $last_mail_error;
}
