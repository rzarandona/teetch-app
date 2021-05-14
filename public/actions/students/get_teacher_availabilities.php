<?php

$user_id   = $_REQUEST['teacher_id'];

$schedules = get_posts( array(
    'posts_per_page' => -1,
    'post_type'=> 'schedule',
    'author' => $user_id
) );

$response = array();
if ( $schedules ) { 
    $data = array();
    foreach ( $schedules as $schedule ) {
        setup_postdata( $schedule );

        $data = explode('|',$schedule->post_title);
        
        $status = get_post_meta($schedule->ID, 'status', true);

        $response[] = array(
            'startDate' =>  date('F d, Y',$data[1]),
            'endDate' =>   date('F d, Y',$data[1]),
            'timeSlot' =>  $data[2],
            'scheduleID' =>  $schedule->ID,
            'status' => $status
        );
    }	

    echo json_encode($response);
}else{
    echo "[]";//return empty brackets
}	

wp_reset_postdata();
