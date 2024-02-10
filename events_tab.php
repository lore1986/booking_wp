<?php

if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

function get_previous_month($ddate) {
    $current_month_number = (int) date('m', strtotime($ddate));
  
    $months = [
      0 => 'December',
      1 => 'January',
      2 => 'February',
      3 => 'Mach',
      4 => 'April',
      5 => 'May',
      6 => 'June',
      7 => 'July',
      8 => 'August',
      9 => 'September',
      10 => 'October',
      11 => 'November',
    ];
  
    return $months[$current_month_number - 1];
  }


  function irish_pub_firenze_query_events($startdate, $enddate)
  { 
    global $wpdb;

    $start_date = date('Y-m-d', strtotime($startdate));
    $end_date = date('Y-m-d', strtotime($enddate));

    $events = [];

    $query = $wpdb->prepare("
        SELECT *
        FROM {$wpdb->prefix}ipf_events
        WHERE date_event BETWEEN %s AND %s
    ", $start_date, $end_date);

    $results = $wpdb->get_results($query);

    if ($results) {
        foreach ($results as $event) {
            $single_ev = new stdClass();
            $single_ev->event_id = $event->event_id;
            $single_ev->event_title = $event->event_title;

            // Assuming date_event is stored as a DATE type
            $single_ev->date_event = $event->date_event;
            $single_ev->time_event = ''; // No time component available for DATE type

            $single_ev->type_event = $event->type_event;
            $single_ev->maxnum = $event->maxnum;
            $single_ev->booked = $event->booked;
            $single_ev->description_event = $event->description_event;
            $single_ev->image_path = $event->image_path;

            array_push($events, $single_ev);
        }
    }

    return $events;

  }


  function irish_pub_dynamic_calendar($startingDate, $html)
  {
    
    $firstDayOfMonth = date('N', strtotime(date('01-m-Y', strtotime($startingDate))));

    $month = date('m', strtotime('-1 month', strtotime($startingDate)));
    $year = date('Y', strtotime('-1 month', strtotime($startingDate)));
    
    $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    $startingDay = $totalDaysInMonth - $firstDayOfMonth + 1;
    
    $lastXDays = array();
    
    for ($day = $startingDay; $day <= $totalDaysInMonth; $day++) {
        $lastXDays[] = $day;
    }

    $real_start_date = null;
    if(!empty($lastXDays) && count($lastXDays) > 1)
    {
        $previous_first_counting_day = $lastXDays[1];
        $real_start_date = date('Y-m-d', strtotime("$previous_first_counting_day-$month-$year"));
    }

    if($real_start_date == null)
    {
        $real_start_date = $startingDate;
    }

    $real_end_date = null;
    $lastDayOfMonth = date('t-m-Y', strtotime($startingDate));
    $sunday_this = date('w', strtotime($lastDayOfMonth));

    if ($sunday_this != 0) {
        $_next_month = date('Y-m-01', strtotime('+1 month', strtotime($startingDate)));
        $day_to_sunday = 7 - $sunday_this;
        $real_end_date = date('Y-m-d', strtotime("+$day_to_sunday days", strtotime($_next_month)));
    }

    if($real_end_date == null)
    {
        $real_end_date = $lastDayOfMonth;
    }

    $events = irish_pub_firenze_query_events($real_start_date, $real_end_date);

    $html .= '<div class="row"> <div class="col-12" id="dynamic-calendar">';
    // Generate the calendar grid
    $currentDay = 1;
    $currentExtra = 1;
    $daysInMonth = date('t', strtotime($startingDate));
    for ($i = 1; $i <= 5; $i++) { 
        $html .= '<div class="row">';
        for ($j = 1; $j <= 7; $j++) {
            if ($i == 1 && $j < $firstDayOfMonth) {
                if (!empty($lastXDays)) {
                    $prev_d = array_shift($lastXDays) + 1;
                    $prev_month_date = date('Y-m-d',  strtotime("$prev_d-$month-$year"));
                    $html .= '<div class="col border">';
                    $html .= '<p class="float-right my-auto">' . date('d-m', strtotime($prev_month_date)) . '</p>';
                    foreach ($events as $event) {
                        if ( date('Y-m-d', strtotime($event->date_event ))== $prev_month_date) {
                                $html .= '<p>' . $event->event_title . '</p>'; 
                                $html .= '<p>' . $event->type_event . '</p>'; 
                            }
                        }
                    $html .= '</div>';
                }
            } else {
                
                if ($currentDay <= $daysInMonth) {
                    $current_date = date('Y-m-d', strtotime(date('Y-m-', strtotime($startingDate)) . $currentDay));

                    $html .= '<div class="col border">';
                    $html .= '<p class="float-right my-auto">' . date('d-m', strtotime($current_date)) . '</p>';
                    foreach ($events as $event) {
                    if ( date('Y-m-d', strtotime($event->date_event ))== $current_date) {
                            $html .= '<p>' . $event->event_title . '</p>'; 
                            $html .= '<p>' . $event->type_event . '</p>'; 
                        }
                    }
                    $html .= '</div>';
                    $currentDay++;
                }else
                {
                    $date_next_month = date('m', strtotime('+1 month', strtotime($startingDate)));
                    $actual_today_next_month = date('Y-m-d', mktime(0, 0, 0, $date_next_month, $currentExtra));
                    $html .= '<div class="col border">';
                    $html .= '<p class="float-right my-auto">' . date('d-m', strtotime($actual_today_next_month)) . '</p>';
                    foreach ($events as $event) {
                        if ( date('Y-m-d', strtotime($event->date_event ))== $actual_today_next_month) {
                                $html .= '<p>' . $event->event_title . '</p>'; 
                                $html .= '<p>' . $event->type_event . '</p>'; 
                            }
                        }
                    $html .= '</div>';
                    $currentExtra++;
                }
            }
        }
        $html .= '</div>'; 
    }

    $html .= '</div></div></div>';

    return $html;
  }

  function irish_pub_base_calendar($html)
  {
     // Define the array of days
     $daysArr = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
     // Assuming $startingDate is already defined
     
 
     // Start generating the HTML
     $html = '<div class="container">';
     $html .= '<div class="row m-2"> 
                   <div class="col-1 offset-4">
                       <button class="btn btn-primary" id="next-month"> < </button>
                   </div>
                   <div class="col-1">

                   </div>
                   <div class="col-1">
                       <button class="btn btn-primary" id="next-month"> > </button>
                   </div>
               </div>';
 
     // Generate the row for days
     $html .= '<div class="row mb-2">';
     foreach ($daysArr as $day) {
         $html .= '<div class="col border">';
         $html .= '<p class="my-auto">' . $day . '</p>';
         $html .= '</div>';
     }
     $html .= '</div>'; // Close row for days

     return $html;
  }
  
  
  function irish_pub_firenze_show_calendar()
  {
    $html = irish_pub_base_calendar($html);
    $html = irish_pub_dynamic_calendar(date('d-m-Y'), $html);
    return $html;
  }
  



function irish_pub_firenze_add_event()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitAddEvent'])) {
    
        $event_data = new stdClass();

        $event_data->event_title = sanitize_text_field($_POST['eventName']);
        $event_data->eventPlaces = sanitize_text_field( $_POST['eventPlaces'] );

        $date = new DateTime($_POST['eventDate']);
        $time = new DateTime($_POST['eventTime']);
        $event_datetime = $date->format('Y-m-d') . ' ' . $time->format('H:i:s');
        $event_data->date_event = $event_datetime;
    
        $image_file = $_FILES['eventImage'];

        $event_data->event_type = $_POST['eventType'];
        $event_data->event_description = $_POST['eventDescription'];

        if ($image_file['error'] === UPLOAD_ERR_OK) {
            $upload_overrides = array('test_form' => false);
        
            // Let WordPress handle the upload
            $attachment_id = media_handle_upload('eventImage', 0, $upload_overrides);
        
            if (is_wp_error($attachment_id)) {
                // There was an error uploading the file
                echo "Error uploading file: " . $attachment_id->get_error_message();
            } else {

                $image_url = wp_get_attachment_url($attachment_id);
                $event_data->image_url = $image_url;
        
                save_irish_pub_firenze_event($event_data);
            }

        } else {
            echo "Error uploading file. Error code: " . $image_file['error'];
        }
    }


    ob_start(); 
    ?>
    
    <form method="post" action="" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="eventName">Titolo Evento:</label>
            <input type="text" class="form-control" id="eventName" name="eventName" required>
        </div>

        <div class="form-group">
            <label for="eventDescription">Descrizione:</label>
            <textarea class="form-control" id="eventDescription" name="eventDescription" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="eventDate">Data Evento:</label>
            <input type="date" class="form-control" id="eventDate" name="eventDate" required>
        </div>

        <div class="form-group">
            <label for="eventTime">Orario Evento:</label>
            <input type="time" class="form-control" id="eventTime" name="eventTime" required>
        </div>

        <div class="form-group">
            <label for="eventPlaces">Numero Posti:</label>
            <input type="number" class="form-control" id="eventPlaces" name="eventPlaces" required>
        </div>

        <div class="form-group">
            <label for="eventImage">Carica Immagine:</label>
            <input type="file" class="form-control-file" id="eventImage" name="eventImage" accept="image/*" required>
            <small class="form-text text-muted">Scegli un'immagine per l'evento.</small>
        </div>

        <div class="form-group">
            <label for="eventType">Tipo di Evento:</label>
            <select class="form-control" id="eventType" name="eventType" required>
                <option value="sport">Sport</option>
                <option value="food">Cibo</option>
                <option value="music">Musica</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" name="submitAddEvent">Submit Event</button>

    </form>


    <?php
    
    $html = ob_get_clean();
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
                            myDiv.innerHTML = <?php echo json_encode(irish_pub_firenze_show_calendar()); ?>;
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

    print_r($event);

    // Save the event data to the database
    $wpdb->insert(
        $table_name,
        array(
            'event_title' => $event->event_title,
            'date_event' =>  $event->date_event,
            'type_event' => $event->event_type,
            'maxnum' => $event->eventPlaces,
            'description_event' => $event->event_description,
            'image_path' =>  $event->image_url,
        ),
        array('%s', '%s', '%s', '%d', '%s', '%s')
    );
}


function irish_pub_firenze_edit_event_tab() {
    
}

function irish_pub_firenze_delete_event_tab() {

}

?>
