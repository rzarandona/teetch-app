<?php

$user_id   = $_REQUEST['user_id'];

$schedules = get_posts( 
    [
        'posts_per_page' => -1,
        'post_type'=> 'schedule',
        'meta_query' => [

            [
                'key'   => 'student-id',
                'value' => $user_id,
            ]

        ]
    ]
 );

$response = array();
if ( $schedules ) { 
    $data = array();
    foreach ( $schedules as $schedule ) {
        setup_postdata( $schedule );

        $data = explode('|',$schedule->post_title);
        
        $status = get_post_meta($schedule->ID, 'status', true);

        // process teacher's data
        $teacher_id = get_post_meta($schedule->ID, 'teacher-id', true);
        $teacher_fname = get_user_meta($teacher_id, 'first_name', true);
        $teacher_lname = get_user_meta($teacher_id, 'last_name', true);
        $teacher_name = $teacher_fname . " " . $teacher_lname;

        $teacher_city = get_user_meta($teacher_id, 'city', true);
        $teacher_profile_picture = get_user_meta($teacher_id, 'profile-picture', true);

        $meeting_join_url = get_post_meta($schedule->ID, 'meeting-join-url', true);
        $meeting_password = get_post_meta($schedule->ID, 'meeting-password', true);

        $response[] = array(
            'startDate' =>  date('F d, Y',$data[1]),
            'endDate' =>   date('F d, Y',$data[1]),
            'timeSlot' =>  $data[2],
            'scheduleID' =>  $schedule->ID,
            'status' => $status,
            'teacher_name' => $teacher_name,
            'teacher_city' => $teacher_city,
            'teacher_profile_picture' => $teacher_profile_picture,
            'teacher_id' => $teacher_id,
            'meeting_join_url' => $meeting_join_url,
            'meeting_password' => $meeting_password
        );
    }	

    echo json_encode($response);
}else{
    echo "[]";//return empty brackets
}	
