<?php

define('VOUCHER_SIGN_UPS_TABLE_NAME', 'ninja_voucher_sign_ups');

// Create database table if it doesn't exist
add_action('init', function(){
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;

    $charset = $wpdb->get_charset_collate();
    $tableName = $wpdb->prefix . VOUCHER_SIGN_UPS_TABLE_NAME;

    $sql = "CREATE TABLE $tableName (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        address_1 tinytext NOT NULL,
        address_2 tinytext NOT NULL,
        postcode tinytext NOT NULL,
        dob date DEFAULT '0000-00-00 00:00:00' NOT NULL,
        tel tinytext NOT NULL,
        email tinytext NOT NULL,
        last_exam date DEFAULT '0000-00-00 00:00:00' NOT NULL,
        created date DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset;";

    maybe_create_table($tableName, $sql);
});

//  intercept page and output html
add_action('template_include', function($template){
    global $wp;
    if ($wp->request === 'register-my-voucher') {

        $errors = [];
        $status = '';
        $pluginUri = plugin_dir_url(__FILE__);

        //process form
        if ($_POST) {
            // Validdation
            
            $required = ['name', 'address1', 'postcode', 'tel', 'email'];
            foreach ($required as $requiredField) {
                if (!isset($_POST[$requiredField]) || empty($_POST[$requiredField])) {
                    $errors[] = '<strong>' . $requiredField . '</strong> is required.';
                }
            }

            if (empty($errors)) {

                global $wpdb;
                $tableName = $wpdb->prefix . VOUCHER_SIGN_UPS_TABLE_NAME;

                // Sanitze
                $data = [];
                $data['email'] = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
                foreach (['name', 'address1', 'address2', 'postcode', 'tel', 'dob', 'last_test'] as $field) {
                    $data[$field] = filter_var($_POST[$field], FILTER_SANITIZE_STRING);
                }

                // Check for dup
                $record = $wpdb->get_results("SELECT id FROM $tableName WHERE email='" . $data['email'] . "'");
                if (empty($record)) {
                    // Insert
                    $wpdb->insert($tableName, [
                        'name' => $data['name'],
                        'address_1' => $data['address1'],
                        'address_2' => $data['address2'],
                        'postcode' => $data['postcode'],
                        'tel' => $data['tel'],
                        'dob' => $data['dob'],
                        'email' => $data['email'],
                        'last_exam' => $data['last_test'],
                        'created' => date('Y-m-d')
                    ]);

                    $status = 'submitted';
                } else {
                    $status = 'dup';
                }

            }
        }

        include_once 'voucher-sign-up.phtml';
        die();
    }

    return $template;
    
});

// process form, if email doesn't exist in db, write to google sheets

