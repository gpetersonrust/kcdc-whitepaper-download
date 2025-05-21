<?php

class Kcdc_Whitepaper_Helper {

    /**
     * Get the current user's IP address.
     *
     * @return string The IP address of the client.
     */
    public static function get_user_ip() {
        // Check for HTTP_CLIENT_IP first
        if ( ! empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP) ) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // Then check for HTTP_X_FORWARDED_FOR
        if ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ( $ip_list as $ip ) {
                $ip = trim($ip);
                if ( filter_var($ip, FILTER_VALIDATE_IP) ) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
