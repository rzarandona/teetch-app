<?php

$user_id   = get_current_user_id();
	
			
$schedules = get_posts( array(
    'posts_per_page' => -1,
    'post_type'=> 'schedule',
    'author' => $user_id
) );

$response = array();
if ( $schedules ) { 
    foreach ( $schedules as $schedule ) {
        setup_postdata( $schedule );

        
        $status = get_post_meta($schedule->ID, 'status', true);
        $date = get_post_meta($schedule->ID, 'date', true);
        $time_slot = get_post_meta($schedule->ID, 'time-slot', true);
        

        $response[] = array(
            'startDate' =>  $date,
            'endDate' =>   $date,
            'timeSlot' =>  $time_slot,
            'scheduleID' =>  $schedule->ID,
            'status' => $status
        );
    }	
    echo json_encode($response);
}else{
    echo "[]";//return empty brackets
}	
wp_reset_postdata();