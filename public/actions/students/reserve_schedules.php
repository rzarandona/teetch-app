<?php

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "teetchapp_nonce")) {
        exit("No naughty business please");
    } 

    $response = array();
    $user_id = get_current_user_id();
    
    $base_user_credits = get_user_meta($user_id, 'credits', true);
    $base_user_used_credits = get_user_meta($user_id, 'used-credits', true);
    $credits_on_queue = 0;


    // -> 1. verifies if user has enough credits, as compared to the number of submitted reservations
    if(sizeof($_REQUEST["sched_ids"]) > $base_user_credits){

        $response[] = 'insufficient_credits';
        
    }else{
        if(sizeof($_REQUEST["sched_ids"]) > 0 ){
            
            $schedule_ids = $_REQUEST['sched_ids'];

            foreach($schedule_ids as $schedule_id){
                array_push($response, $schedule_id);

                $credits_on_queue++;

                // -> 2. if so, reserve the schedules;
                    update_post_meta( $schedule_id ,'status', "reserved" );
                    update_post_meta( $schedule_id ,'student-id', $user_id );
                    update_post_meta( $schedule_id, 'date-reserved', date('d F Y, h:i:s A'));



                // -> 3. create zoom-post-type and initialize a zoom link;
                    $teacher_id = get_post_meta($schedule_id, 'teacher-id', true);

                    // Construct meeting title
                    $date = get_post_meta($schedule_id, 'date', true);
                    $time = get_post_meta($schedule_id, 'time-slot', true);

                    $meeting_title = strtotime($date . ' ' .  $time . ':00') . '-' . $user_id . '-' . $teacher_id ;

                    // Construct formatted date
                    $meeting_datetime = date('Y-m-d H:i', strtotime($date . ' ' .  $time . ':00'));

                    // Finally, create the meeting post type
                    $create_meeting = array(
                        'post_title' => $meeting_title,
                        'post_status'   => 'publish',
                        'post_author'   => $user_id,
                        'post_type' => 'zoom-meetings',
                    );

                    $meeting_id = wp_insert_post( $create_meeting );
                    $zoom_teacher_id = get_post_meta($schedule_id, 'teacher-host-zoom-id',  true);

                                
                    // GET and SET STUDENT EMAIL
                    $user_info = get_userdata($user_id);
                    $student_email = $user_info->user_email;
                    update_post_meta($schedule_id,'student-email', $student_email );

                    $this->teetch_create_zoom_meeting($zoom_teacher_id, $meeting_id, $meeting_datetime, $schedule_id);
                    

                    // -> 4. create relevant notification posts
                    $create_notification = array(
                        'post_title' => $meeting_title,
                        'post_status'   => 'publish',
                        'post_author'   => $user_id,
                        'post_type' => 'notifications',
                    );

                    $notification_id = wp_insert_post( $create_notification );
                    update_post_meta($notification_id, 'student-id', $user_id);
                    update_post_meta($notification_id, 'teacher-id', $teacher_id);
       
            }

            // -> 5. deduct the credits; 
                update_user_meta($user_id, 'credits', $base_user_credits - $credits_on_queue);
                // Incrememnt user used-credits
                update_user_meta($user_id, 'used-credits', $base_user_used_credits + $credits_on_queue);

        
        }
    }
    
    // -> 6. update user rank (if necessary)
    if($base_user_used_credits > 30 && $base_user_used_credits < 61){
        // SET RANKS
        update_user_meta($user_id, 'current-ranking-code', 'A2');
        update_user_meta($user_id, 'next-ranking-code', 'B1');


    }else if($base_user_used_credits > 60 && $base_user_used_credits < 91){
        // SET RANKS
        update_user_meta($user_id, 'current-ranking-code', 'B1');
        update_user_meta($user_id, 'next-ranking-code', 'B2');
    }
    else if($base_user_used_credits > 90 && $base_user_used_credits < 121){
        // SET RANKS
        update_user_meta($user_id, 'current-ranking-code', 'B2');
        update_user_meta($user_id, 'next-ranking-code', 'C1');
    }
    else if($base_user_used_credits > 120 && $base_user_used_credits < 151){
        // SET RANKS
        update_user_meta($user_id, 'current-ranking-code', 'C1');
        update_user_meta($user_id, 'next-ranking-code', 'C2');
    }
    
    echo json_encode($response);