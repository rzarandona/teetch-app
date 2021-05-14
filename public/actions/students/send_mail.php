<?php

$teacher_link = "<a href='" . $args->meeting_start_url . "' target='_blank'> Start Meeting </a>";
$student_link = "<a href='" . $args->meeting_join_url . "' target='_blank'> Join Meeting </a>";

for($i = 0; $i < 2; $i++){

    if($i == 0){
        $link = $teacher_link;
        $email_to = $args->teacher_email;
    }else{
        $link = $student_link;
        $email_to = $args->student_email;
    }

    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    $subject = 'Teetch Meeting Details';
    $message = "

        <table>

            <tr>
                <td>Zoom Meeting ID:</td> <td>" . $args->meeting_id . "</td>
            </tr>
            <tr>
                <td>Password:</td> <td>" . $args->meeting_password . "</td>
            </tr>
            <tr>
                <td>Date/Time:</td> <td>" . $args->meeting_datetime . "</td>
            </tr>
            <tr>
                <td></td> " . $link . " <td></td>
            </tr>

        </table>

    ";
    
    
    wp_mail($email_to, $subject, $message, $headers);


}