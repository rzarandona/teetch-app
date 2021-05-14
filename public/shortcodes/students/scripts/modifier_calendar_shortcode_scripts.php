<script>

    function load_schedules(){

        // GET ALL SCHEDULES
        let args = {
            action: 'get_student_schedules',
            nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
            user_id: <?php echo get_current_user_id(); ?>
        }

        jQuery.ajax({
            type:'post',
            dataTye:'JSON',
            url:teetchAjax.ajaxurl,
            data: args,
            beforeSend: function(){
                sc_utils.student_calendar.loader_show();
            },
            success: function(response){
                sc_utils.student_modifier.all_schedules = sc_utils.processResponse(response, "#teetch_modifier_calendar");
                jQuery('.day.today').trigger('click');
                sc_utils.student_calendar.loader_hide();
                
            },
            complete:function(response){

            },
            error: function(err){
                console.log(err);
            }
        
        });
    }

    function map_today_schedules(){
        
        sc_utils.student_modifier.current_day_schedules.forEach(schedule => {

            console.log(schedule);

            jQuery('.student-modifier-wrapper .list-items').append(`
                <div class="list-item">

                    <div class="item-image">
                        <img src="${schedule.teacher_profile_picture}">
                    </div>
                    <div class="item-name">
                        <p><strong>${schedule.teacher_name}</strong></p>
                        <p><small>from ${schedule.teacher_city}</small></p>
                    </div>
                    <div class="item-date">
                        <p><strong>${schedule.startDate}</strong></p>
                    </div>
                    <div class="item-hour">
                        <p><strong>${schedule.timeSlot}h00 - ${schedule.timeSlot}h45</strong></p>
                    </div>
                    <div id='item-modify-$ID' class="item-modify">
                        <button

                            data-teacher-name='`+ schedule.teacher_name + `'
                            data-teacher-profile-picture='`+ schedule.teacher_profile_picture + `'
                            data-schedule-date='`+ schedule.startDate + `'
                            data-timeslot = '`+ schedule.timeSlot +`'
                            data-schedule-id='`+ schedule.scheduleID + `'
                            data-teacher-id='` + schedule.teacher_id + `'
                            
                            class="item-modify-button"

                        >MODIFIER</button>

                        <a

                            href='` + schedule.meeting_join_url + `'
                            target='_blank'
                            class="item-join-button"

                        >JOIN</a>

                    </div>

                </div>

            `)
        })

        



    }

    jQuery('#teetch_modifier_calendar').teetchapp({	
        displayEvent:true,
        events: [],
        onDateSelect: function (date, events) {
            
            // clear current_day_schedules
            sc_utils.student_modifier.current_day_schedules = [];
            jQuery('.student-modifier-wrapper .list-items').empty();

            // Filter out those objects that do not belong to the day
            sc_utils.student_modifier.all_schedules.forEach(schedule => {
                
                if(sc_utils.dateParser(date) == schedule.startDate){
                    sc_utils.student_modifier.current_day_schedules.push(schedule);
                }
                
            })

            map_today_schedules();

        }
    });

    jQuery(document).ready(function($){

        
        load_schedules();


    });


    // POPUP EVENT LISTENERS

    jQuery(document).on('click', '.item-modify-button', function(){

        // jQuery(".sm-popup-container").css('transform', 'scale(1)');

        let teacher_name = jQuery(this).data('teacherName');
        let teacher_profile_picture = jQuery(this).data('teacherProfilePicture');
        let schedule_date = jQuery(this).data('scheduleDate');
        let schedule_id = jQuery(this).data('scheduleId');
        let timeslot = jQuery(this).data('timeslot');
        let teacher_id = jQuery(this).data('teacherId');




        jQuery('.sm-popup-container').empty();
        jQuery('.sm-popup-container').append(`

            <div class="sm-popup-options">
                
                <div class='options-wrapper'>
                    <button class="sm-popup-close">x</button>
                    <h4>MODIFIER <hr> MON RENDEZ-VOUS</h4>
                    <img src="${teacher_profile_picture}" alt="">
                    <p class="teacher_name">${teacher_name}</p>
                    <p class="schedule-datetime">${schedule_date} - ${timeslot}:00 am / ${timeslot}:45 am</p>

                    <div class="options">
                        <div class="date">
                            <p class="label">Modifier la date</p>
                            <div class="option-setting">
                                <p>00 / 00 / 0000</p>
                                <small><i>voir les disponibilités</i></small>
                            </div>
                        </div>
                        <div class="hour">
                            <p class="label">Modifier l'heure</p>
                            <div class="option-setting">
                                <p>00 / 00 / 0000</p>
                                <small><i>voir les disponibilités</i></small>
                            </div>
                        </div>
                    </div>

                    <div class="cancel-container">
                        <p>Annuler le cours</p>
                        <div class="checkbox-container">
                            
                            <span class="checkbox-el">
                                <input class="cancel-checkbox" type="checkbox">
                                <div class="checkbox-circle"></div>
                            </span>

                        </div>
                    </div>

                    <button data-new-schedule-id='' data-schedule-id=' ${schedule_id} ' class='submit-sm-popup'><b>VALIDER</b></button>
                
                </div>
                
                <div id='modifier_current_teacher_calendar'>
                
                    <div class="teetch-student-booking-container">

                        <div class="tts-left">
                            <div class="selected-teacher-container">
                                <div class="selected-teacher-info">
                                    <p>Select another available timeslot:</p>
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


                            </div>

                        </div>

                        </div>

                    </div>

                </div>

            </div>

        `);


        jQuery('#modifier_current_teacher_calendar #teetch_student_booking_calendar').teetchapp({	
            displayEvent:true,
            events: [],
            onDateSelect: function (date, events) {
                
                var selected_date = sc_utils.dateParser(date);

                // SET SELECTED DAY
                jQuery('.date .option-setting p').html(selected_date);

                // Reset
                jQuery('.hour .option-setting p').html('00 / 00 / 0000');
                jQuery('#modifier_current_teacher_calendar .checkbox-container').html("<p class='status_icon'><i class='fas fa-times'></i></p>")
                sc_utils.current_day_timeslots = [];
                sc_utils.reserved_timeslot_total = 0;

                events.forEach((timeslot) => {
                    if(timeslot.startDate == selected_date){
                        sc_utils.current_day_timeslots.push(timeslot);
                    }
                })
                
                // loop through the timeslots of the selected teacher for the selected day
                // and set these so that the widget would render checkboxes for the open-status timeslots
                events.forEach((timeslot)=>{
                    var schedule_id = timeslot.scheduleID;
                    var open_timeslot = timeslot.timeSlot;

                    if(timeslot.status == 'reserved'){
                        jQuery('#today-schedule-list #slot-' + pad(open_timeslot) ).html("<p class='status_icon reserved'><i class='fas fa-times'></i></p>");
                        sc_utils.reserved_timeslot_total++;
                    }else{
                        jQuery('#today-schedule-list #slot-' + pad(open_timeslot) ).html('<span class="checkbox-el"><input data-timeslot="' + pad(open_timeslot) + 'h00 - ' + pad(open_timeslot) + 'h45" class="timeslot-checkbox" type="radio" name="schedule_id" value="'+ schedule_id +'"><div class="checkbox-circle"></div></span>');
                    }

                })

                console.log(sc_utils.current_day_timeslots);

                jQuery('.tts-open-timeslots .number').html(sc_utils.current_day_timeslots.length - sc_utils.reserved_timeslot_total);

            }
        });


        let args = {
            action: 'get_teacher_availabilities',
            nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
            teacher_id: teacher_id
        }

        jQuery.ajax({
            type:'post',
            dataTye:'JSON',
            url:teetchAjax.ajaxurl,
            data: args,
            beforeSend: function(){
                sc_utils.student_calendar.loader_show();
            },
            success: function(response){
                sc_utils.processResponse(response, "#modifier_current_teacher_calendar #teetch_student_booking_calendar");
                sc_utils.student_calendar.loader_hide();
                jQuery('.sm-popup-container').css('transform', 'scale(1)');
                jQuery('.sm-popup-container .day.today').trigger('click');
            },
            complete:function(response){
            }
        
        });




    })

    jQuery(document).on('click', '.submit-sm-popup', function(){
        let schedule_id = jQuery(this).data('scheduleId');

        // check if cancel checkbox is checked
        if(jQuery('.cancel-checkbox').prop('checked')){

            console.log('deleting...');
            let args = {
            action: 'cancel_schedule',
            nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
            schedule_id: schedule_id
            }
            jQuery.ajax({
                type:'post',
                dataTye:'JSON',
                url:teetchAjax.ajaxurl,
                data: args,
                beforeSend: function(){
                    jQuery('.sm-popup-container').css('transform', 'scale(0)');
                    sc_utils.student_calendar.loader_show();
                },
                success: function(response){
                    sc_utils.student_modifier.current_day_schedules = [];
                    load_schedules();
                    map_today_schedules();
                    
                },
                complete:function(response){
                    
                    setTimeout(function(){
                        sc_utils.student_calendar.loader_hide();
                    }, 2000)
                    
                }
            
            });
        }else{

            // if(sc_utils.current_selected_timeslot){
            // }else{
            // 	toastr.error('Please select a timeslot.');
            // }
            
            toastr.warning('Please confirm schedule cancellation.');

        }



    });

    jQuery(document).on('change', '.cancel-checkbox', function(){
        // if(jQuery(this).prop('checked')){
        // 	jQuery('.sm-popup-options .options').css('height', '0px');
        // 	jQuery('.sm-popup-options .options').css('height', '0px');
        // 	jQuery('.sm-popup-options').css({
        // 		'grid-template-columns': '100% 0%',
        // 		'width': '200px'
        // 	});
        // }else{
        // 	jQuery('.sm-popup-options .options').css('height', '128px');
        // 	jQuery('.sm-popup-options').css({
        // 		'grid-template-columns': '30% 70%',
        // 		'width': '950px'
        // 	});
        // }
    })

    jQuery(document).on('click', '.sm-popup-close', function(){
        jQuery('.sm-popup-container').css('transform', 'scale(0)');
        jQuery('.sm-popup-container').empty();
        sc_utils.current_day_timeslots = [];
        sc_utils.reserved_timeslot_total = 0;
    });

    jQuery(document).on('change', '[name=schedule_id]', function(){
        let timeslot = jQuery('[name=schedule_id]:checked').data('timeslot');
        let schedule_id = jQuery('[name=schedule_id]:checked').prop('value');
        sc_utils.current_selected_timeslot = schedule_id;

        jQuery('.submit-sm-popup').attr('data-new-schedule-id', schedule_id);
        jQuery('.hour .option-setting p').html(timeslot);
    })


</script>