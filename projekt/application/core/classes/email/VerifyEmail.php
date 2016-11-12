<?php

class VerifyEmail
{

    private $result;

    function __construct($toEmail, $fromEmail, $getDetails = false)
    {
        $email_arr = explode("@", $toEmail);
        $domain = array_slice($email_arr, -1);
        $domain = $domain[0];
        
        $details = "";

        // Trim [ and ] from beginning and end of domain string, respectively
        $domain = ltrim($domain, "[");
        $domain = rtrim($domain, "]");

        if ("IPv6:" == substr($domain, 0, strlen("IPv6:"))) {
            $domain = substr($domain, strlen("IPv6") + 1);
        }

        $mxHosts = array();
        
        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            $mx_ip = $domain;
        } else {
            getmxrr($domain, $mxHosts, $mxWeight);
        }
        
        if (!empty($mxHosts)) {
            $mx_ip = $mxHosts[array_search(min($mxWeight), $mxHosts)];
        }
        else {
            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $record_a = dns_get_record($domain, DNS_A);
            } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $record_a = dns_get_record($domain, DNS_AAAA);
            }

            if (!empty($record_a))
                $mx_ip = $record_a[0]['ip'];
            else {

                $result = false;
                $details .= "No suitable MX records found.";

                return ((true == $getDetails) ? array($result, $details) : $result);
            }
        }

        $connect = @fsockopen($mx_ip, 25);
        if ($connect) {
            if (preg_match("/^220/i", $out = fgets($connect, 1024))) {
                fputs($connect, "HELO $mx_ip\r\n");
                $out = fgets($connect, 1024);
                $details .= $out . "\n";

                fputs($connect, "MAIL FROM: <$fromEmail>\r\n");
                $from = fgets($connect, 1024);
                $details .= $from . "\n";

                fputs($connect, "RCPT TO: <$toEmail>\r\n");
                $to = fgets($connect, 1024);
                $details .= $to . "\n";

                fputs($connect, "QUIT");
                fclose($connect);

                if (!preg_match("/^250/i", $from) || !preg_match("/^250/i", $to)) {
                    $result = false;
                } else {
                    $result = true;
                }
            }
        } else {
            $result = false;
            $details .= "Could not connect to server";
        }
        if ($getDetails) {
            $this->result = array($result, $details);
        } else {
            $this->result = $result;
        }
    }

    public function getResult(){


       return $this->result;

    }
}
?>
