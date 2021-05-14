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
    $response[] = $messages;
}

echo json_encode($response);



