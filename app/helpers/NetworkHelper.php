<?php
/**
 * NetworkHelper
 * IP ve MAC adresi almak için yardımcı sınıf
 */
class NetworkHelper {
    
    /**
     * Kullanıcının gerçek IP adresini al
     */
    public static function getClientIP() {
        $ip = null;
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        }
        elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        if ($ip === '127.0.0.1' || $ip === '::1' || empty($ip)) {
            $localIP = self::getServerLocalIP();
            if ($localIP) {
                $ip = $localIP;
            }
        }
        
        return $ip ?: 'unknown';
    }

    /**
     * Server'ın local ağdaki IP adresini al
     */
    public static function getServerLocalIP() {
        $ip = null;
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = [];
            @exec('ipconfig', $output);
            
            foreach ($output as $line) {
                if (preg_match('/IPv4.*?:\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $line, $matches)) {
                    if (!preg_match('/^(127\.|169\.254\.)/', $matches[1])) {
                        $ip = $matches[1];
                        break;
                    }
                }
            }
        } else {
            $output = [];
            @exec("hostname -I 2>/dev/null", $output);
            if (!empty($output[0])) {
                $ips = preg_split('/\s+/', trim($output[0]));
                foreach ($ips as $testIP) {
                    if (filter_var($testIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        if (!preg_match('/^(127\.|169\.254\.)/', $testIP)) {
                            $ip = $testIP;
                            break;
                        }
                    }
                }
            }
        }
        
        return $ip;
    }

    /**
     * IP adresinden MAC adresi al
     */
    public static function getMacAddress($ip) {
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'unknown' || empty($ip)) {
            return self::getServerMac();
        }

        $mac = null;
        $output = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @exec("ping -n 1 -w 500 " . escapeshellarg($ip) . " >nul 2>&1");
            @exec("arp -a " . escapeshellarg($ip), $output);
            
            foreach ($output as $line) {
                if (strpos($line, $ip) !== false) {
                    if (preg_match('/([0-9a-fA-F]{2}[:-]){5}[0-9a-fA-F]{2}/', $line, $matches)) {
                        $mac = strtoupper(str_replace('-', ':', $matches[0]));
                        break;
                    }
                }
            }
        } else {
            @exec("ping -c 1 -W 1 " . escapeshellarg($ip) . " >/dev/null 2>&1");
            @exec("arp -n " . escapeshellarg($ip) . " 2>/dev/null", $output);
            
            foreach ($output as $line) {
                if (strpos($line, $ip) !== false) {
                    if (preg_match('/([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}/', $line, $matches)) {
                        $mac = strtoupper($matches[0]);
                        break;
                    }
                }
            }
        }

        return $mac ?: self::getServerMac();
    }

    /**
     * Server'ın kendi MAC adresini al
     */
    public static function getServerMac() {
        $mac = null;
        $output = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @exec('getmac /fo csv /nh', $output);
            if (!empty($output[0])) {
                $parts = str_getcsv($output[0]);
                if (!empty($parts[0])) {
                    $mac = strtoupper(str_replace('-', ':', $parts[0]));
                }
            }
        } else {
            @exec("cat /sys/class/net/$(ip route show default 2>/dev/null | awk '/default/ {print \$5}')/address 2>/dev/null", $output);
            if (!empty($output[0]) && preg_match('/([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}/', $output[0], $matches)) {
                $mac = strtoupper($matches[0]);
            }
        }

        return $mac ?: 'LOCAL-SERVER';
    }
    
    /**
     * IP ve MAC'i array olarak döndür
     */
    public static function getClientInfo() {
        $ip = self::getClientIP();
        $mac = self::getMacAddress($ip);
        
        return [
            'ip' => $ip,
            'mac' => $mac
        ];
    }
}
