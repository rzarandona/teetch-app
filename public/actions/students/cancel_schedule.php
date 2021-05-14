<?php

$schedule_id = trim($_REQUEST['schedule_id']);
$user_id = get_current_user_id();

$date_reserved = get_post_meta($schedule_id, 'date-reserved', true);
$date_cancelled = date('d F Y, h:i:s A');
$_reserved = strtotime($date_reserved);
$_cancelled = strtotime($date_cancelled);

$hour = abs($_cancelled - $_reserved)/(60*60);

if($hour < 24){

    update_post_meta($schedule_id, 'status', 'open');
    update_post_meta($schedule_id, 'student-id', '');

    $base_credits = get_user_meta($user_id, 'credits', true);
    update_user_meta($user_id, 'credits', $base_credits + 1);

    $response = [[ 'Message' => 'success' ]];

}else{

    $response = [[ 'Message' => 'not_allowed' ]];

}

echo json_encode($response);
