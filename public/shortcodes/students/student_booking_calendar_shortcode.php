<!-- RENDER -->
<div class="teetch-student-booking-container">

    <div class="tts-left">
        <div class="selected-teacher-container">
            
            <div class="selected-teacher-image">
                <img src="<?php echo bloginfo('url'); ?>/wp-content/uploads/2021/04/nobody.jpg" alt="">
            </div>
            <div class="selected-teacher-info">
                <p class='teacher-name'>Guy Hawkins</p>
                <small class='teacher-country'>from New York</small>
                <div class="star-rating"></div>
            </div>
            
        </div>
        <div id="teetch_student_booking_calendar" class="calendar-container"></div>
    </div>

    <div class="teacher-today-timeslots">

        <div class="tts-open-timeslots">
            <p class="number">3</p>
            <p class='label'>LECONS LIBRES</p>



            <ul id="today-schedule-list" class="today-schedule-list">

                <?php for($i = 7; $i < 24; $i++){ ?>
                <li class='today-schedule-item schedule-item-<?php echo $i;?> teacher'>
                        <?php if($i<10){ $x= "0".$i;}else{$x=$i;}?>
                    <div class='schedule-timeslot'><?php echo $x;?>h00 - <?php echo $x;?>h45</div>
                    
                    <div id='slot-<?php echo $x ?>' class="checkbox-container">
                        <p style='text-align:right'><i class="fas fa-times"></i></p>
                    </div>

                </li>
                <?php } ?>
            </ul>
            
                                
            <div class="teetch-button-wrapper">
                <a id="reserve_schedule" class="button">RESERVER</a>
            </div>


        </div>

    </div>

</div>


<?php include 'scripts/student_booking_calendar_shortcode_scripts.php' ?>