<?php

$user = wp_get_current_user();
$user_id = get_current_user_id();

$response = [];

// if user is teacher
if(in_array( 'teacher', (array) $user->roles )){
    $response['role'] = 'teacher';
}
else if(in_array( 'student', (array) $user->roles )){

    $messages = get_posts( 
        [
            'posts_per_page' => -1,
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

        $m_id = $message->ID;

        $rel_teacher_id = get_post_meta($m_id, 'teacher-id', true);
        $rel_teacher_fname = get_user_meta($rel_teacher_id, 'first_name', true);
        $rel_teacher_lname = get_user_meta($rel_teacher_id, 'last_name', true);
        $rel_teacher_full_name = $rel_teacher_full_name . ' ' . $rel_teacher_lname;

        
        $response[] = $rel_teacher_full_name;

     }
}


echo json_encode($response);



