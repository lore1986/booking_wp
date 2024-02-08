<?php

if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}


function irish_pub_firenze_add_event()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitAddEvent'])) {
    
        $event_data = new stdClass();
    
        // Sanitize event name
        $event_data->event_title = sanitize_text_field($_POST['eventName']);
        // Convert event date to Unix timestamp
        $event_data->date_event = strtotime(sanitize_text_field($_POST['eventDate']));
    
        $image_file = $_FILES['eventImage'];

        if ($image_file['error'] === UPLOAD_ERR_OK) {
            $upload_overrides = array('test_form' => false);
        
            // Let WordPress handle the upload
            $attachment_id = media_handle_upload('eventImage', 0, $upload_overrides);
        
            if (is_wp_error($attachment_id)) {
                // There was an error uploading the file
                echo "Error uploading file: " . $attachment_id->get_error_message();
            } else {
                // File upload was successful, $attachment_id now contains the ID of the attachment
                echo "File uploaded successfully. Attachment ID: " . $attachment_id;
            }
        } else {
            // There was an error uploading the file
            echo "Error uploading file. Error code: " . $image_file['error'];
        }
    
        // if ($image_file['error'] === UPLOAD_ERR_OK) {
        //     $upload_overrides = array('test_form' => false);
    
        //     //$upload_result = wp_handle_upload($image_file, $upload_overrides);
        //     $attachment_id = media_handle_upload($image_file, 0, $upload_overrides);

        //     print_r($attachment_id);
        //     // if (!empty($upload_result['error'])) {
        //     //     print($upload_result['error']);
        //     //     die();
        //     // } else {
        //     //     $image_path = $upload_result['file'];
    
        //     //     // Set image path in event data
        //     //     $event_data->image_path = $image_path;
                
        //     //     // Debugging: Print event data
        //     //     print_r($event_data);
                
        //     //     // Save event data to the database
        //     //     // Assuming save_irish_pub_firenze_event is a custom function
        //     //     save_irish_pub_firenze_event($event_data);
        //     // }
        // }
    }


    ob_start(); // Start output buffering
    ?>
    
    <!-- Event Submission Form for Add Event Tab -->
    <form method="post" action="" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="eventName">Titolo Evento:</label>
            <input type="text" class="form-control" id="eventName" name="eventName" required>
        </div>

        <div class="form-group">
            <label for="eventDate">Data Evento:</label>
            <input type="date" class="form-control" id="eventDate" name="eventDate" required>
        </div>

        <div class="form-group">
            <label for="eventImage">Carica Immagine:</label>
            <input type="file" class="form-control-file" id="eventImage" name="eventImage" accept="image/*" required>
            <small class="form-text text-muted">Scegli un'immagine per l'evento.</small>
        </div>

        <button type="submit" class="btn btn-primary" name="submitAddEvent">Submit Event</button>

    </form>

    <?php
    
    $html = ob_get_clean(); // Get the content from the output buffer and clean it
    return $html;
}

function irish_pub_firenze_event_tab() {
    ?>
    <div class="row">
        <div class="col-10" id="event-tab-container"></div>
        <div class="col-2">
            <ul>
                <li><a href="#" data-l="calendar" class="eventcaller">Calendar</a></li>
                <li><a href="#" data-l="add_event" class="eventcaller">Add Event</a></li>
                <li><a href="#" data-l="edit_event" class="eventcaller">Edit Event</a></li>
            </ul>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myLinks = document.querySelectorAll(".eventcaller");
            myLinks.forEach(function(link) {
                link.addEventListener("click", function(event) {
                    event.preventDefault(); 
                    var linkId = link.getAttribute('data-l');
                    var myDiv = document.getElementById("event-tab-container");
                    
                    switch (linkId) {
                        case "calendar":
                            function_to_call();
                            break;
                        case "add_event":
                            myDiv.innerHTML = <?php echo json_encode(irish_pub_firenze_add_event()); ?>;
                            break;
                        case "edit_event":
                            // Handle edit event
                            break;
                    }
                });
            });
        });
    </script>
    <?php
}


// Function to retrieve events for the calendar
function get_irish_pub_firenze_events() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'irish_pub_firenze_events';

    // Retrieve events from the database
    $events = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // print_r($events);
    // die();
    return $events;
}

// Function to save an event to the database
function save_irish_pub_firenze_event($event) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ipf_events';

    // Save the event data to the database
    $wpdb->insert(
        $table_name,
        array(
            'event_title' => $event->event_name,
            'event_date' =>  $event->event_date,
            'image_path' =>  $event->image_path
        ),
        array('%s', '%s', '%s')
    );
}


function irish_pub_firenze_edit_event_tab() {
    
}

function irish_pub_firenze_delete_event_tab() {

}

?>
