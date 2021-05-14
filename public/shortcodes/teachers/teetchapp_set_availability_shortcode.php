<div id="teetchform" class="teetchform-wrapper">
    <header>
        <h4>My Availabilities</h4>
        <p>please select days and times you are available to teach</p>
    </header>
    <div class="teetchform-container">
        <div class="date-wrapper">
                <div id="selected-day" class="selected-date"></div>
                <div id="selected-month" class="selected-date"></div>
                <div id="selected-year" class="selected-date"></div>
        </div>
        <div id="timeslot">
        <?php for($i = 7; $i < 24; $i++){ if($i<10){ $x= "0".$i;}else{$x=$i;} ?>
            <div class="round">
                <input type="checkbox" id="timeslot-<?php echo $i;?>" name="timeslot" value="<?php echo $i;?>" />
                <label for="timeslot-<?php echo $i;?>"></label><span><?php echo $x;?>h00 to <?php echo $x;?>h45</span>
            </div>
            <?php $hr='<div class="hr"></div>'; $s= array('12','19'); if(in_array($i,$s)){echo $hr;} }//end for?>
        </div>		
        <div class="teetch-button-wrapper">
            <?php 
                $user_id = get_current_user_id();
                $nonce = wp_create_nonce("teetchapp_nonce"); 
                $link = admin_url('admin-ajax.php?action=update_schedule&nonce='.$nonce);
            ?>
            <input type="hidden" id="schedule_id" name="schedule_id" value=""/>
            <input type="hidden" id="date_id" name="date_id" value=""/>
            <a id="update_schedule" data-user-id="<?php echo $user_id;?>" href="<?php echo $link; ?>" data-nonce="<?php echo $nonce;?>" class="button">Add</a>
        </div>
    </div>
</div>	