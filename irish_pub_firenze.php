<?php
/**
 * Plugin Name: Irish Pub Firenze
 * Description: A WordPress plugin for managing events at Irish Pub Firenze.
 * Version: 1.0
 * Author: Your Name
 */

//require_once plugin_dir_path(__FILE__) . 'admin-functions.php';
include( plugin_dir_path( __FILE__ ) . 'admin_page.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Register activation hook
register_activation_hook(__FILE__, 'irish_pub_firenze_activate');
add_shortcode('irish_pub_firenze', 'irish_pub_firenze_shortcode');
add_action('after_setup_theme', 'irish_pub_firenze_enqueue_scripts');
add_action('admin_menu', 'irish_pub_firenze_admin_menu');

// Activation callback function
function irish_pub_firenze_activate() {
    // Create database table on plugin activation
    create_irish_pub_firenze_table();
    
    // Optionally, you can set default values or perform other activation tasks
}

// Function to create the database table

function create_irish_pub_firenze_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Create Event Table
    $event_table_name = $wpdb->prefix . 'ipf_events';
    $sql_event = "CREATE TABLE $event_table_name (
        event_id INT AUTO_INCREMENT,
        event_title TEXT,
        date_event DATETIME,
        type_event ENUM('sport', 'food', 'music'),
        maxnum INT,
        description_event TEXT,
        image_path VARCHAR(255),
        PRIMARY KEY (event_id)
    ) $charset_collate;";
    dbDelta($sql_event);

    $users_table_name = $wpdb->prefix . 'ipf_users';
    $sql_users = "CREATE TABLE $users_table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255),
        role VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    dbDelta($sql_users);

    $booking_table_name = $wpdb->prefix . 'ipf_bookings';
    $sql_booking = "CREATE TABLE $booking_table_name (
        booking_id INT AUTO_INCREMENT,
        date_event TIMESTAMP,
        attendees INT,
        user_id INT,
        event_id INT,
        PRIMARY KEY (booking_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}ipf_users(id),
        FOREIGN KEY (event_id) REFERENCES {$wpdb->prefix}ipf_events(event_id)
    ) $charset_collate;";
    dbDelta($sql_booking);
    
}


// Enqueue scripts and styles
function irish_pub_firenze_enqueue_scripts() {
    // Enqueue FullCalendar library
    wp_enqueue_script('jquery');
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js');
    
    // Enqueue plugin script
    //wp_enqueue_script('irish-pub-firenze-script', plugin_dir_url(__FILE__) . 'js/irish-pub-firenze.js', array('jquery', 'fullcalendar'), '1.0', true);
    wp_enqueue_script('irish-pub-firenze-script', plugin_dir_url(__FILE__) . 'js/irish-pub-firenze.js');

    // Localize script to pass data to JavaScript
    // wp_localize_script('irish-pub-firenze-script', 'irish_pub_firenze_data', array(
    //     'events' => get_irish_pub_firenze_events(),
    // ));
}



// Create a shortcode for the plugin
function irish_pub_firenze_shortcode() {
    ob_start(); ?>

    <!-- Interactive Calendar -->
    <div id="calendar"></div>

    <?php
    return ob_get_clean();
}




// Create an admin page
function irish_pub_firenze_admin_menu() {
    add_menu_page(
        'Irish Pub Firenze Events',
        'Pub Events',
        'manage_options',
        'irish_pub_firenze_admin',
        'irish_pub_firenze_admin_page'
    );
}



// Localize script to pass data to JavaScript
function irish_pub_firenze_localize_script() {
    wp_localize_script('irish-pub-firenze-script', 'irish_pub_firenze_data', array(
        'events' => get_irish_pub_firenze_events(),
    ));
}

add_action('wp_enqueue_scripts', 'irish_pub_firenze_localize_script');
