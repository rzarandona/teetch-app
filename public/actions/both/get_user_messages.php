<?php

$user = wp_get_current_user();
$user_id = get_current_user_id();

$response = [];


// if user is teacher
if(in_array( 'teacher', (array) $user->roles )){

$messages = get_posts( 
    [
        'posts_per_page' => 20,
        'post_type'=> 'notifications',
        'meta_query' => [

            [
                'key'   => 'teacher-id',
                'value' => $user_id,
            ]

        ]
    ]
    );

    foreach($messages as $message){

        $message_item = [];

        $m_id = $message->ID;

        $rel_student_id = get_post_meta($m_id, 'student-id', true);
        $rel_student_fname = get_user_meta($rel_student_id, 'first_name', true);
        $rel_student_lname = get_user_meta($rel_student_id, 'last_name', true);
        $rel_student_full_name = $rel_student_fname . ' ' . $rel_student_lname;
        $message_item['related_user_name'] = $rel_student_full_name;

        $rel_schedule_id = get_post_meta($m_id, 'schedule-id', true);
        $rel_zoom_link = get_post_meta($rel_schedule_id, 'meeting-join-url', true);
        $message_item['zoom_link'] = $rel_zoom_link;

        $rel_timeslot = get_post_meta($rel_schedule_id, 'time-slot', true);
        $message_item['timeslot'] = $rel_timeslot;

        $status = get_post_meta($m_id, 'teacher-status', true);
        $message_item['status'] = $status;

        $response[] = $message_item;

    }

    echo json_encode($response);
}
else if(in_array( 'student', (array) $user->roles )){

    $messages = get_posts( 
        [
            'posts_per_page' => 20,
            'post_type'=> 'notifications',
            'meta_query' => [

                [
                    'key'   => 'student-id',
                    'value' => $user_id,
                ]

            ]
        ]
     );

     foreach($messages as $message){

        $message_item = [];

        $m_id = $message->ID;

        $rel_teacher_id = get_post_meta($m_id, 'teacher-id', true);
        $rel_teacher_fname = get_user_meta($rel_teacher_id, 'first_name', true);
        $rel_teacher_lname = get_user_meta($rel_teacher_id, 'last_name', true);
        $rel_teacher_full_name = $rel_teacher_fname . ' ' . $rel_teacher_lname;
        $message_item['related_user_name'] = $rel_teacher_full_name;

        $rel_schedule_id = get_post_meta($m_id, 'schedule-id', true);
        $rel_zoom_link = get_post_meta($rel_schedule_id, 'meeting-join-url', true);
        $message_item['zoom_link'] = $rel_zoom_link;

        $rel_timeslot = get_post_meta($rel_schedule_id, 'time-slot', true);
        $message_item['timeslot'] = $rel_timeslot;

        $status = get_post_meta($m_id, 'student-status', true);
        $message_item['status'] = $status;

        $response[] = $message_item;

     }

     echo json_encode($response);
    
}






