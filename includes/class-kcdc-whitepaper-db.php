<?php

/**
 * Kcdc_Whitepaper_DB Class
 *
 * Handles all database interactions for the KCDC Whitepaper plugin,
 * including creating custom tables, inserting and retrieving request data,
 * and managing blocked IP addresses.
 */
class Kcdc_Whitepaper_DB {

    /**
     * @var string The name of the whitepaper requests database table.
     */
    private $request_table;

    /**
     * @var string The name of the blocked IP addresses database table.
     */
    private $blocked_ip_table;

    /**
     * @var string The database character set and collation.
     */
    private $charset_collate;

    /**
     * Kcdc_Whitepaper_DB constructor.
     *
     * Initializes the database table names and character set/collation.
     * Uses the global $wpdb object to get WordPress table prefix and charset.
     */
    public function __construct() {
        global $wpdb; // Access the global WordPress database object.

        // Set table names with the WordPress prefix for unique identification.
        $this->request_table    = $wpdb->prefix . 'kcdc_whitepaper_requests';
        $this->blocked_ip_table = $wpdb->prefix . 'kcdc_whitepaper_blocked_ips';

        // Get the character set and collation for database table creation.
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    /**
     * Creates the custom database tables required by the plugin.
     *
     * This method creates two tables:
     * 1. `kcdc_whitepaper_requests`: Stores information about whitepaper download requests.
     * 2. `kcdc_whitepaper_blocked_ips`: Stores IP addresses blocked due to suspicious activity.
     *
     * It uses `dbDelta()` for safe table creation and updates.
     */
    public function create_tables() {
    global $wpdb;

    if ( ! function_exists( 'dbDelta' ) ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }

    // Check if the requests table already exists
    $table_check_1 = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $wpdb->esc_like( $this->request_table )
    ));

    // Check if the blocked IPs table already exists
    $table_check_2 = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $wpdb->esc_like( $this->blocked_ip_table )
    ));

    ob_start(); // Suppress potential dbDelta output

    // Create requests table if it doesn't exist
    if ( $table_check_1 !== $this->request_table ) {
        $sql_requests = "CREATE TABLE {$this->request_table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT UNSIGNED NOT NULL,
            wp_nonce VARCHAR(64) NOT NULL,
            name VARCHAR(255) NOT NULL,
            agency VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(64) NOT NULL,
            used TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            used_at DATETIME DEFAULT NULL,
            KEY token_idx (token),
            UNIQUE KEY email_token_unique (email, token)
        ) {$this->charset_collate};";

        dbDelta($sql_requests);
    }

    // Create blocked IPs table if it doesn't exist
    if ( $table_check_2 !== $this->blocked_ip_table ) {
        $sql_blocked_ips = "CREATE TABLE {$this->blocked_ip_table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            block_reason TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY ip_address_unique (ip_address)
        ) {$this->charset_collate};";

        dbDelta($sql_blocked_ips);
    }

    ob_end_clean(); // Clear any unexpected output
}

    /**
     * Inserts a new whitepaper request into the database.
     *
     * @param array $data An associative array containing request data.
     * Expected keys: 'name', 'agency', 'email', 'token'.
     * @return int|false The number of rows inserted on success, false on error.
     */
    public function insert_request( $data ) {
        global $wpdb;

        // **Security Best Practice: Input Sanitization**
        // Sanitize all user-provided data before insertion to prevent XSS and other attacks.
        // Sanitize first and last name
        $first_name = sanitize_text_field($data['first_name']);
        $last_name = sanitize_text_field($data['last_name']);

        $sanitized_data = array(
            'name'       => trim($first_name . ' ' . $last_name), // Combine names
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'post_id'    => absint($data['post_id']),
            'wp_nonce'   => sanitize_text_field($data['wp_nonce']),
            'agency'     => sanitize_text_field($data['agency']),
            'email'      => sanitize_email($data['email']),
            'token'      => sanitize_key($data['token']),
            'used'       => 0,
            'created_at' => current_time('mysql'),
        );

        // Define the format of the data to be inserted.
        // '%s' for strings, '%d' for integers, '%f' for floats.
        $data_format = array(
            '%s', // name
            '%s', // first_name
            '%s', // last_name 
            '%d', // post_id
            '%s', // wp_nonce
            '%s', // agency 
            '%s', // email
            '%s', // token
            '%d', // used
            '%s', // created_at
        );

        // Perform the insert operation. `wpdb::insert` automatically prepares the query.
        $result = $wpdb->insert(
            $this->request_table,
            $sanitized_data,
            $data_format
        );

        // Check for database errors.
        if ( false === $result ) {
            // Log the error for debugging purposes.
            error_log( 'KCDC Whitepaper DB Error: Failed to insert request. ' . $wpdb->last_error );
        }

        return $result;
    }

    /**
     * Retrieves a whitepaper request by its unique token.
     *
     * @param string $token The unique token to search for.
     * @return object|null A row object representing the request on success, null if not found.
     */
    public function get_request_by_token( $token ) {
        global $wpdb;

        // **Security Best Practice: Input Sanitization**
        // Sanitize the token before using it in the query. `sanitize_key` is suitable for tokens.
        $sanitized_token = sanitize_key( $token );

        // **Security Best Practice: SQL Injection Prevention**
        // Use `$wpdb->prepare()` for all queries with dynamic values.
        // `%s` is the placeholder for string values.
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->request_table} WHERE token = %s",
            $sanitized_token
        );

        // `get_row()` retrieves a single row from the database.
        $result = $wpdb->get_row( $query );

        return $result;
    }

    /**
     * Marks a whitepaper request as 'used' and records the usage timestamp.
     *
     * @param string $token The unique token of the request to mark as used.
     * @return int|false The number of rows updated on success, false on error.
     */
    public function mark_request_as_used( $token ) {
        global $wpdb;

        // **Security Best Practice: Input Sanitization**
        // Sanitize the token before using it in the update query.
        $sanitized_token = sanitize_key( $token );

        // Perform the update operation. `wpdb::update` automatically prepares the query.
        $result = $wpdb->update(
            $this->request_table,
            array(
                'used'    => 1, // Set 'used' to true (1).
                'used_at' => current_time( 'mysql' ), // Record the time of usage.
            ),
            array( 'token' => $sanitized_token ), // WHERE clause: update where token matches.
            array( '%d', '%s' ), // Format for update data: used (integer), used_at (string).
            array( '%s' )        // Format for WHERE clause: token (string).
        );

        // Check for database errors.
        if ( false === $result ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to mark request as used. ' . $wpdb->last_error );
        }

        return $result;
    }

    /**
     * Inserts a new blocked IP address into the database.
     *
     * @param string $ip_address   The IP address to block.
     * @param string $user_agent   The user agent string associated with the blocked IP.
     * @param string $block_reason The reason for blocking the IP.
     * @return int|false The number of rows inserted on success, false on error.
     */
    public function insert_blocked_ip( $ip_address, $user_agent, $block_reason ) {
        global $wpdb;

        // **Security Best Practice: Input Validation and Sanitization**
        // Validate IP address format.
        if ( ! filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
            error_log( 'KCDC Whitepaper DB Error: Invalid IP address provided for blocking: ' . $ip_address );
            return false; // Return false for invalid IP address.
        }

        // Sanitize user agent and block reason for safe storage.
        $sanitized_ip_address = sanitize_text_field( $ip_address ); // Already validated, just to be consistent.
        $sanitized_user_agent = sanitize_textarea_field( $user_agent );
        $sanitized_block_reason = sanitize_textarea_field( $block_reason );

        // Perform the insert operation. `wpdb::insert` automatically prepares the query.
        $result = $wpdb->insert(
            $this->blocked_ip_table,
            array(
                'ip_address'   => $sanitized_ip_address,
                'user_agent'   => $sanitized_user_agent,
                'block_reason' => $sanitized_block_reason,
                'created_at'   => current_time( 'mysql' ),
            ),
            array(
                '%s', // ip_address
                '%s', // user_agent
                '%s', // block_reason
                '%s', // created_at
            )
        );

        // Check for database errors, especially for duplicate IP addresses (unique key constraint).
        if ( false === $result ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to insert blocked IP. ' . $wpdb->last_error );
        }

        return $result;
    }

    /**
     * Retrieves all blocked IP addresses from the database.
     *
     * @return array|object[] An array of row objects representing blocked IPs on success,
     * an empty array if no IPs are blocked.
     */
    public function get_blocked_ips() {
        global $wpdb;

        // **Security Best Practice: No dynamic input, so no prepare needed here.**
        // This query fetches all blocked IPs, assuming this is an admin-only function.
        $query = "SELECT * FROM {$this->blocked_ip_table} ORDER BY `created_at` DESC;";

        // `get_results()` retrieves multiple rows from the database.
        $results = $wpdb->get_results( $query );

        // Ensure results are always an array.
        return is_array( $results ) ? $results : array();
    }

    /**
     * Checks if a given IP address is currently blocked.
     *
     * @param string $ip_address The IP address to check.
     * @return bool True if the IP is blocked, false otherwise.
     */
    public function is_ip_blocked( $ip_address ) {
        global $wpdb;

        // **Security Best Practice: Input Validation and Sanitization**
        if ( ! filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
            return false; // Invalid IP format.
        }
        $sanitized_ip_address = sanitize_text_field( $ip_address );

        // **Security Best Practice: SQL Injection Prevention**
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->blocked_ip_table} WHERE ip_address = %s",
            $sanitized_ip_address
        );

        $count = $wpdb->get_var( $query );

        return (int) $count > 0;
    }

    /**
     * Anonymizes an IP address by setting the last octet to 0 for IPv4
     * or the last 80 bits to 0 for IPv6.
     *
     * This is a privacy measure, useful for data retention while
     * reducing personal identifiability.
     *
     * @param string $ip_address The original IP address.
     * @return string The anonymized IP address.
     */
    private function anonymize_ip_address( $ip_address ) {
        if ( filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            // Anonymize IPv4: Set last octet to 0.
            return preg_replace( '/\.\d+$/', '.0', $ip_address );
        } elseif ( filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
            // Anonymize IPv6: Set last 80 bits (5 groups of 16-bit) to 0.
            // This effectively makes it a /48 prefix, common for privacy.
            $parts = explode( ':', $ip_address );
            // Ensure we have at least 3 parts for a /48 prefix.
            // If less, pad with zeros or handle accordingly. For simplicity, just set the end to 0.
            if ( count( $parts ) > 3 ) {
                for ( $i = 3; $i < count( $parts ); $i++ ) {
                    $parts[ $i ] = '0';
                }
            }
            return implode( ':', $parts );
        }
        return $ip_address; // Return original if not a valid IP.
    }

    /**
     * Cleans up old request data from the database.
     *
     * This function should be called periodically (e.g., via a WP-Cron job)
     * to manage data retention and comply with privacy regulations.
     *
     * @param int $days_to_keep The number of days to retain request records.
     * Requests older than this will be deleted.
     * @return int|false The number of rows deleted on success, false on error.
     */
    public function cleanup_old_requests( $days_to_keep = 365 ) {
        global $wpdb;

        // Ensure `$days_to_keep` is a positive integer.
        $days_to_keep = absint( $days_to_keep );
        if ( $days_to_keep <= 0 ) {
            $days_to_keep = 365; // Default to one year if invalid.
        }

        // Calculate the cutoff date.
        $cutoff_date = current_time( 'mysql', true ) . ' - ' . $days_to_keep . ' DAY';

        // **Security Best Practice: SQL Injection Prevention**
        // `wpdb::query` can be used for DELETE statements. `prepare` is still recommended.
        $query = $wpdb->prepare(
            "DELETE FROM {$this->request_table} WHERE created_at < %s",
            $cutoff_date
        );

        $result = $wpdb->query( $query );

        if ( false === $result ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to cleanup old requests. ' . $wpdb->last_error );
        }

        return $result;
    }

    /**
     * Cleans up old blocked IP data from the database.
     * This helps manage the size of the blocked IP table and ensures relevance.
     *
     * @param int $days_to_keep The number of days to retain blocked IP records.
     * @return int|false The number of rows deleted on success, false on error.
     */
    public function cleanup_old_blocked_ips( $days_to_keep = 30 ) {
        global $wpdb;

        // Ensure `$days_to_keep` is a positive integer.
        $days_to_keep = absint( $days_to_keep );
        if ( $days_to_keep <= 0 ) {
            $days_to_keep = 30; // Default to 30 days if invalid.
        }

        $cutoff_date = current_time( 'mysql', true ) . ' - ' . $days_to_keep . ' DAY';

        $query = $wpdb->prepare(
            "DELETE FROM {$this->blocked_ip_table} WHERE created_at < %s",
            $cutoff_date
        );

        $result = $wpdb->query( $query );

        if ( false === $result ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to cleanup old blocked IPs. ' . $wpdb->last_error );
        }

        return $result;
    }


      /**
     * Drops (deletes) both custom database tables created by the plugin.
     *
     * This method should typically be called during plugin uninstallation,
     * not just deactivation, to ensure all plugin data is removed from the database.
     *
     * @return bool True if tables were dropped successfully, false otherwise.
     */
    public function drop_tables() {
        global $wpdb;

        $success = true;

        // SQL to drop the requests table.
        // Using IF EXISTS prevents errors if the table doesn't exist.
        $sql_drop_requests = "DROP TABLE IF EXISTS {$this->request_table};";
        $result_requests = $wpdb->query( $sql_drop_requests );

        if ( false === $result_requests ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to drop requests table. ' . $wpdb->last_error );
            $success = false;
        }

        // SQL to drop the blocked IPs table.
        $sql_drop_blocked_ips = "DROP TABLE IF EXISTS {$this->blocked_ip_table};";
        $result_blocked_ips = $wpdb->query( $sql_drop_blocked_ips );

        if ( false === $result_blocked_ips ) {
            error_log( 'KCDC Whitepaper DB Error: Failed to drop blocked IPs table. ' . $wpdb->last_error );
            $success = false;
        }

        return $success;
    }


    /**
     * Retrieves all whitepaper requests for a specific post ID.
     *
     * @param int $post_id The post ID to search for.
     * @return array Array of request objects, empty array if none found.
     */
    public function get_requests_by_post_id($post_id) {
        global $wpdb;

        $post_id = absint($post_id);

        $query = $wpdb->prepare(
            "SELECT * FROM {$this->request_table} WHERE post_id = %d ORDER BY created_at DESC",
            $post_id
        );

        $results = $wpdb->get_results($query);

        return is_array($results) ? $results : array();
    }



    /**
     * Updates the requests table to add first_name and last_name columns.
     * Preserves existing data by splitting the name field into first_name and last_name.
     */
    public function update_requests_table_names() {
        global $wpdb;

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        ob_start(); // Start output buffering

        // First add the new columns
        $wpdb->query("ALTER TABLE {$this->request_table} 
            ADD COLUMN first_name VARCHAR(255) AFTER name,
            ADD COLUMN last_name VARCHAR(255) AFTER first_name");

        // Split existing names into first and last name
        $wpdb->query("UPDATE {$this->request_table} 
            SET first_name = SUBSTRING_INDEX(name, ' ', 1),
            last_name = TRIM(SUBSTRING(name, LENGTH(SUBSTRING_INDEX(name, ' ', 1)) + 1))
            WHERE name IS NOT NULL");

        ob_end_clean(); // End and clean output buffer

        // Log any errors
        if ($wpdb->last_error) {
            error_log('KCDC Whitepaper DB Error: Failed to update table structure. ' . $wpdb->last_error);
            return false;
        }

        return true;
    }
}
?>