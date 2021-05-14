<?php
    $mtg_param = array(
        'userId'                    => $zoom_teacher_id,
        'meetingTopic'              => 'Teetch Meeting',
        'start_date'                => $meeting_datetime,
        'timezone'                  => '',
        'duration'                  => '',
        'password'                  => mt_rand(1111, 9999),
        'meeting_authentication'    => '',
        'join_before_host'          => '',
        'option_host_video'         => '',
        'option_participants_video' => '',
        'option_mute_participants'  => '',
        'option_auto_recording'     => '',
        'alternative_host_ids'      => '',
        'disable_waiting_room'      => ''
    );


    $meeting_created = json_decode( zoom_conference()->createAMeeting( $mtg_param ) );
    if ( empty( $meeting_created->code ) ) {

        update_post_meta( $meeting_id, '_meeting_zoom_details', $meeting_created );
        update_post_meta( $meeting_id, '_meeting_zoom_join_url', $meeting_created->join_url );
        update_post_meta( $meeting_id, '_meeting_zoom_start_url', $meeting_created->start_url );
        update_post_meta( $meeting_id, '_meeting_zoom_meeting_id', $meeting_created->id );

        update_post_meta( $meeting_id, '_meeting_zoom_start_time', $meeting_datetime );


        $meeting_join_id = $meeting_created->id;
        $meeting_password = $meeting_created->password;
        $meeting_start_url = $meeting_created->start_url;
        $meeting_join_url = $meeting_created->join_url;


        update_post_meta($schedule_id, 'meeting-join-id', $meeting_join_id);
        update_post_meta($schedule_id, 'meeting-password',$meeting_password);
        update_post_meta($schedule_id, 'meeting-start-url',$meeting_start_url);
        update_post_meta($schedule_id, 'meeting-join-url',$meeting_join_url);

        $teacher_email  = get_post_meta($schedule_id, 'teacher-email', true);
        $student_email  = get_post_meta($schedule_id, 'student-email', true);
        
        $args = (object) [
            'teacher_email' => $teacher_email,
            'student_email' => $student_email,
            'meeting_id' => $meeting_join_id,
            'meeting_password' => $meeting_password, 
            'meeting_start_url' => $meeting_start_url,
            'meeting_join_url' => $meeting_join_url,
            'meeting_datetime' => $meeting_datetime
        ];

        $this->send_mail( $args );


    } else {
        //Store Error Message
        update_post_meta( $meeting_id, '_meeting_zoom_details', $meeting_created );
    }