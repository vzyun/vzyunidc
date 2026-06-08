<?php
/**
 * vzyunIDC - 简易邮件发送类
 * 支持 SMTP 和 PHP mail() 两种方式
 */

class Mailer {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $encryption;
    private $fromAddress;
    private $fromName;

    public function __construct() {
        $this->host = getSetting('mail_smtp_host', '');
        $this->port = (int)getSetting('mail_smtp_port', '465');
        $this->user = getSetting('mail_smtp_user', '');
        $this->pass = getSetting('mail_smtp_pass', '');
        $this->encryption = getSetting('mail_smtp_encryption', 'ssl');
        $this->fromAddress = getSetting('mail_from_address', '');
        $this->fromName = getSetting('mail_from_name', 'vzyunIDC');
    }

    public function send($to, $subject, $content) {
        if (!$this->host) {
            return $this->sendMail($to, $subject, $content);
        }
        return $this->sendSmtp($to, $subject, $content);
    }

    private function sendMail($to, $subject, $content) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromAddress}>\r\n";
        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $content, $headers);
    }

    private function sendSmtp($to, $subject, $content) {
        if (!function_exists('fsockopen')) return false;
        
        $errno = 0;
        $errstr = '';
        $host = $this->encryption === 'ssl' ? 'ssl://' . $this->host : $this->host;
        
        $fp = @fsockopen($host, $this->port, $errno, $errstr, 30);
        if (!$fp) return false;

        $this->smtpCommand($fp, null, 220);
        $this->smtpCommand($fp, "EHLO {$this->host}", 250);
        
        if ($this->encryption === 'tls') {
            $this->smtpCommand($fp, "STARTTLS", 220);
            stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->smtpCommand($fp, "EHLO {$this->host}", 250);
        }

        $this->smtpCommand($fp, "AUTH LOGIN", 334);
        $this->smtpCommand($fp, base64_encode($this->user), 334);
        $this->smtpCommand($fp, base64_encode($this->pass), 235);
        $this->smtpCommand($fp, "MAIL FROM:<{$this->fromAddress}>", 250);
        $this->smtpCommand($fp, "RCPT TO:<{$to}>", 250);
        $this->smtpCommand($fp, "DATA", 354);
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromAddress}>\r\n";
        $headers .= "To: <{$to}>\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "\r\n" . $content . "\r\n.";
        
        $this->smtpCommand($fp, $headers, 250);
        $this->smtpCommand($fp, "QUIT", 221);
        
        fclose($fp);
        return true;
    }

    private function smtpCommand($fp, $command, $expectedCode) {
        if ($command !== null) {
            fwrite($fp, $command . "\r\n");
        }
        $response = '';
        while ($line = fgets($fp, 512)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $response;
    }
}
