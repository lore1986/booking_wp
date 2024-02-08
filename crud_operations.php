<?php

// Add Event
function add_event($date, $type_event, $maxnum, $description) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event';
    $wpdb->insert($table_name, array(
        'date' => $date,
        'type_event' => $type_event,
        'maxnum' => $maxnum,
        'description' => $description
    ));
}

// Delete Event
function delete_event($event_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event';
    $wpdb->delete($table_name, array('event_id' => $event_id));
}

// Edit Event
function edit_event($event_id, $date, $type_event, $maxnum, $description) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event';
    $wpdb->update($table_name, array(
        'date' => $date,
        'type_event' => $type_event,
        'maxnum' => $maxnum,
        'description' => $description
    ), array('event_id' => $event_id));
}