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
        $m_id = $message->ID;
        update_post_meta($m_id, 'teacher-status', 'opened');
    }
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
        $m_id = $message->ID;
        update_post_meta($m_id, 'student-status', 'opened');
    }
    
}






