<?php

if ( !wp_verify_nonce( $_REQUEST['nonce'], "teetchapp_nonce")) {
    exit("No naughty business please");
} 

$data_timeslots = array();
$response = array();
$user_id  = $_REQUEST["user_id"];
$date     = $_REQUEST["date"];
$statuses   = $_REQUEST["status"];
$action   = $_REQUEST["command"];
$timeslots = $_REQUEST["timeslot"];

$data_timeslots['status'] = $statuses;
$data_timeslots['timeslots'] = $timeslots;

if($action != "Add" ){

    $query_args = array(
        'author'=> get_current_user_id(),
        'numberposts'     => -1,
        'post_type'       => 'schedule',
        'meta-key'        => 'date',
        'meta_query'      => array(
        array(
            'key'     => 'date',
            'value'   => $date,
            'compare' => 'LIKE',
        )
        ),
        'orderby'   => 'meta_value',
        'order'     => 'ASC',
    );
    $data = new WP_Query( $query_args );
    if($data->have_posts()){
        while($data->have_posts()){
            $data->the_post();
                wp_delete_post(get_the_ID());
        }
    }
    $response[] = 'Cleared';
    wp_reset_postdata();
}

$teacher_host_zoom_id = get_user_meta($user_id, 'teacher-host-zoom-id', true);

// TEACHER EMAIL
$user_info = get_userdata($user_id);
$teacher_email = $user_info->user_email;

if(sizeof($timeslots) > 0){
    

    $response = [];
    for($i = 0; $i < sizeof($timeslots); $i++){

        $title    = $user_id.'|'.strtotime($date).'|'.$data_timeslots['timeslots'][$i];
        $create_sched = array(
            'post_title' => wp_strip_all_tags($title),
            'post_status'   => 'publish',
            'post_author'   => $user_id,
            'post_type' => 'schedule'
        );

        $sched_id = wp_insert_post( $create_sched );
        update_post_meta($sched_id,'teacher-id',$user_id );
        update_post_meta($sched_id,'teacher-host-zoom-id', $teacher_host_zoom_id );
        update_post_meta($sched_id,'teacher-email', $teacher_email );
        update_post_meta($sched_id,'date',$date);
        update_post_meta($sched_id,'time-slot', $data_timeslots['timeslots'][$i]);
        update_post_meta($sched_id,'status',$data_timeslots['status'][$i] );

        $response[] = array('startDate' =>  $date,'endDate' =>  $date,'timeSlot' =>  $data_timeslots['timeslots'][$i] ,'scheduleID' =>  $sched_id, 'status' => $data_timeslots['status'][$i]);
        
    }//end for


}//end if

echo json_encode($response);