<div id='teacher_today_schedule' class='teetchform-wrapper'>
    <header>
        <h4>My lessons of the day</h4>
    </header>
    <div id="today_date_wrapper">
        <div class='today_label'>DATE</div>
        <div class='today_date'><?php echo Date('d-m-Y'); ?></div>
    </div>

    <ul id="today-schedule-list" class="today-schedule-list">

        <?php

            $query_args = array( 
                'author'=> get_current_user_id(),
                'numberposts'     => -1,
                'post_type'       => 'schedule',
                'meta-key'        => 'date',
                'meta_query'      => array(
                array(
                    'key'     => 'date',
                    'value'   => Date('F d, Y'),
                    'compare' => 'LIKE',
                )
                ),
                'orderby'   => 'meta_value',
                'order'     => 'ASC',
            );
            $data = new WP_Query( $query_args );

            $todays_timeslot = array();
            if($data->have_posts()){
                while($data->have_posts()){
                    $data->the_post();
                    $timeslot["id"] = get_post_meta(get_the_ID(), 'time-slot', true);
                    $timeslot["status"] = get_post_meta(get_the_ID(), 'status', true);

                    array_push($todays_timeslot, $timeslot);	
                }
            }
            wp_reset_postdata();
        ?>

        <?php for($i = 7; $i < 24; $i++){ ?>
        <li class='today-schedule-item schedule-item-<?php echo $i;?>'>
                <?php if($i<10){ $x= "0".$i;}else{$x=$i;}?>
            <div class='schedule-timeslot'><?php echo $x;?>h00 - <?php echo $x;?>h45</div>
            <?php 
                $status = "<i class='fas fa-times'></i>";
                $status_text = 'unavailable';

                foreach($todays_timeslot as $key => $value){
                    if($value['id'] == $i){
                        $status = "<i class='fas fa-circle'></i>";
                        $status_text = $value['status'];
                    }
                }//end for each ?>
            <div class='schedule-status <?php echo $status_text; ?>'><p> <?php echo $status; ?> </p></div>
        </li>
        <?php } ?>
    </ul>
</div>