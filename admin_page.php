<?php

// Admin page callback
include( plugin_dir_path( __FILE__ ) . 'events_tab.php');


function irish_pub_firenze_admin_page() {
    ?>
   
    <h2>Irish Pub Firenze Events</h2>


    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#events">Events</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#bookings">Bookings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#customers">Clients</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="events" class="tab-pane fade show active">
            <?php irish_pub_firenze_event_tab() ?>
        </div>
        <div id="bookings" class="tab-pane fade">
            <h1>Bookings Tab </h1>
        </div>
        <div id="customers" class="tab-pane fade">
            <h1>customer tab </h1>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Activate Bootstrap tab functionality
            $('.nav-tabs a').click(function(event) {
                event.preventDefault();
                $(this).tab('show');
            });
        });
    </script>

    <?php
}


