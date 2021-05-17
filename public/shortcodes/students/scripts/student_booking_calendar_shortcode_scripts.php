<script type="text/javascript">
    jQuery(document).ready(function($){

        init_teacher_calendar();

        // SET BASE CREDIT VALUES
        sc_utils.base_used_credits = parseInt( $('#used_credit_string p').text() );
        sc_utils.base_available_credits = parseInt( $('#remaining_credit_string p').text() );

        // SET BASE RANK
        sc_utils.base_current_rank =  $('#current_rank_string p').text() ;
        sc_utils.base_next_rank = $('#next_rank_string p').text();


        $('#teetch_student_booking_calendar').teetchapp({
            displayEvent:true,
            events: [],
            onDateSelect:function (date, events) {
                
                // RESET CREDITS PROGRESS BAR FOR NON-INCURRED CHANGES
                sc_utils.student_calendar.set_circle_progress_bar(sc_utils.base_used_credits, sc_utils.base_available_credits);
                $('#used_credit_string p').text(sc_utils.base_used_credits);
                $('#remaining_credit_string p').text(sc_utils.base_available_credits);
                sc_utils.student_calendar.set_rank_bar(sc_utils.base_used_credits);


                // RESET RANK PROGRESS BAR FOR NON-INCURRED CHANGES
                $('#current_rank_string p').text(sc_utils.base_current_rank);
                $('#next_rank_string p').text(sc_utils.base_next_rank);

                // Clear the local timeslot data and reset the timeslot widget
                sc_utils.current_day_timeslots = [];
                sc_utils.reserved_timeslot_total = 0;
                $('.teetch-student-booking-container .checkbox-container').html("<p class='status_icon'><i class='fas fa-times'></i></p>")
                


                // Parse the selected date
                var selected_date = sc_utils.dateParser(date);

                // Loop through the selected teacher's timeslots and filter out those that do not belong to the selected day
                // fills the "current_day_timeslots" data
                sc_utils.current_teacher_timeslots.forEach((timeslot) => {
                    if(timeslot.startDate == selected_date){
                        sc_utils.current_day_timeslots.push(timeslot);
                    }
                })
                
                // loop through the timeslots of the selected teacher for the selected day
                // and set these so that the widget would render checkboxes for the open-status timeslots
                sc_utils.current_day_timeslots.forEach((timeslot)=>{
                    var schedule_id = timeslot.scheduleID;
                    var open_timeslot = timeslot.timeSlot;

                    if(timeslot.status == 'reserved'){
                        $('#today-schedule-list #slot-' + pad(open_timeslot) ).html("<p class='status_icon reserved'><i class='fas fa-circle'></i></p>");
                        sc_utils.reserved_timeslot_total++;
                    }else{
                        $('#today-schedule-list #slot-' + pad(open_timeslot) ).html('<span class="checkbox-el"><input class="timeslot-checkbox" type="checkbox" name="scheds" value="'+ schedule_id +'"><div class="checkbox-circle"></div></span>');
                    }

                })
                
                // Set the timeslot-total counter
                $('.tts-open-timeslots .number').html(sc_utils.current_day_timeslots.length - sc_utils.reserved_timeslot_total);
                
            }
        });

        function init_teacher_calendar(){
                            

            let teacher_card_ids = [];

            $('.teacher_card').each(function(){
                teacher_card_ids.push($(this).attr('id'));
            });

            let random_teacher_id = teacher_card_ids[Math.floor(Math.random() * teacher_card_ids.length)];

            let args = {
                action: 'get_teacher_availabilities',
                nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
                teacher_id: random_teacher_id
            }

            $.ajax({
                type:'post',
                dataTye:'JSON',
                url:teetchAjax.ajaxurl,
                data: args,
                beforeSend: function(){
                    sc_utils.student_calendar.loader_show();
                },
                success: function(response){
                    sc_utils.processResponse(response, "#teetch_student_booking_calendar");
                    $("#" + random_teacher_id + ".teacher_card").trigger('click');
                    $(".calendar .today").trigger('click');
                },
                complete:function(response){
                    // location.reload();
                    $('.selected-teacher-image img').prop('src', sc_utils.selected_teacher_image);
                    $('.selected-teacher-info .teacher-name').html(sc_utils.selected_teacher_name);
                    $('.selected-teacher-info .teacher-country').html(sc_utils.selected_teacher_country);
                    $('.selected-teacher-info .star-rating').html(sc_utils.selected_teacher_rating)
                }
            
            });
        }


        // STUDENT CALENDAR - RELEVANT LISTENERS
        /*
            Listens for a card click to set the selected teacher id
        */
        $('.teacher_card').click(function(){
            
            let teacher_id = $(this).attr('id');
            sc_utils.selected_teacher_id = teacher_id;
            sc_utils.reserved_timeslot_total = 0;

            $('.teacher_card.tc_active').removeClass('tc_active');
            $('.teacher_card#'+sc_utils.selected_teacher_id).addClass('tc_active');


            // Clear timeslot
            $('.teetch-student-booking-container .checkbox-container').html("<p class='status_icon'><i class='fas fa-times'></i></p>")

            // SET SELECTED TEACHER INFO
            sc_utils.selected_teacher_image = $(this).find('#teetcher-image img').prop('src');
            sc_utils.selected_teacher_name = $(this).find('#teetcher_name .jet-listing-dynamic-field__content').html();
            sc_utils.selected_teacher_country = $(this).find('#teetcher_country .jet-listing-dynamic-field__content').html();
            sc_utils.selected_teacher_rating = $(this).find('#teetcher_star_rating .elementor-star-rating__wrapper').html();

            let args = {
                action: 'get_teacher_availabilities',
                nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
                teacher_id: teacher_id
            }

            $.ajax({
                type:'post',
                dataTye:'JSON',
                url:teetchAjax.ajaxurl,
                data: args,
                beforeSend: function(){
                    sc_utils.student_calendar.loader_show();
                },
                success: function(response){

                    sc_utils.processResponse(response, "#teetch_student_booking_calendar");
                    $('.day[data-value=' + sc_utils.current_selected_day + ']').trigger('click');

                },
                complete:function(response){
                    // location.reload();
                    
                    $('.selected-teacher-image img').prop('src', sc_utils.selected_teacher_image);
                    $('.selected-teacher-info .teacher-name').html(sc_utils.selected_teacher_name);
                    $('.selected-teacher-info .teacher-country').html(sc_utils.selected_teacher_country);
                    $('.selected-teacher-info .star-rating').html(sc_utils.selected_teacher_rating)
                    sc_utils.student_calendar.loader_hide();
                }
            
            });
        
        })
        
        $("#reserve_schedule").click(function(){

            
            
            var checkedValues = $('input[name=scheds]:checked').map(function() {
                return this.value;
            }).get();

            if(checkedValues.length == 0){

                toastr.error("Please select at least one timeslot to reserve.", "No timeslot selected");
                return false;

            }else{

                let args = {
                    sched_ids: checkedValues,
                    action: 'reserve_schedules',
                    nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>'
                }

                $.ajax({
                    type:'post',
                    dataTye:'JSON',
                    url:teetchAjax.ajaxurl,
                    data: args,
                    beforeSend: function(){
                        sc_utils.student_calendar.loader_show();
                    },
                    success: function(response){
                
                        // SET NEW BASE CREDIT VALUES
                        sc_utils.base_used_credits = parseInt( $('#used_credit_string p').text() );
                        sc_utils.base_available_credits = parseInt( $('#remaining_credit_string p').text() );

                        // SET NEW BASE RANKS
                        sc_utils.base_current_rank =  $('#current_rank_string p').text() ;
                        sc_utils.base_next_rank =  $('#next_rank_string p').text() ;


                        var str= String(response);
                        var x = str.slice(-1);

                        if(x != "]"){
                            var data = str.substring(0,str.length - 1);
                            var response = JSON.parse(data);

                            response.forEach((schedule_id) => {
                                $('.timeslot-checkbox').each(function(){
                                    if( $(this).attr('value') == schedule_id){
                                        $(this).parents('.checkbox-container').html("<p class='status_icon reserved'><i class='fas fa-circle'></i></p>");
                                    } 
                                })
                            })
                            
                        }//end if 

                        sc_utils.reserved_timeslot_total;

                        $("#" + sc_utils.selected_teacher_id + ".teacher_card").trigger('click');
                        $('.day[data-value=' + sc_utils.current_selected_day + ']').trigger('click');

                        if(response[0] == 'insufficient_credits'){
                            toastr.error("Credits insufficient. Reservation failed.", "Bummer!");
                        }else{
                            toastr.success("Your reservation is now pending approval", "Awesome!");
                        }
                        setTimeout(function(){
                            sc_utils.student_calendar.loader_hide();
                        }, 3000)
                        get_number_of_unopened_messages();
                    },
                    complete:function(response){								

                    }
                
                });

            }




        });

        $(document).on("click", ".timeslot-checkbox", function(){

            let available_lessons_for_the_day = parseInt($('.tts-open-timeslots .number').html());
            let available_credits = parseInt($('#remaining_credit_string p').html());
            let used_credits = parseInt($('#used_credit_string p').html());


            if($(this).prop('checked')){
                if(available_credits == 0){
                    toastr.warning("You don't have enough credits to reserve another timeslot." ,"Oh, no!")
                    $(this).removeAttr('checked');
                }else{
                    $('#remaining_credit_string p').html(available_credits - 1);
                    $('#used_credit_string p').html(used_credits + 1);
                    $('.tts-open-timeslots .number').html( available_lessons_for_the_day - 1);

                    // CALCULATE AND UPDATE PROGRESS BARS
                    sc_utils.student_calendar.set_circle_progress_bar(parseInt($('#used_credit_string p').html()), parseInt($('#remaining_credit_string p').html()));
                    sc_utils.student_calendar.set_rank_bar(parseInt($('#used_credit_string p').html()));

                    


                }
                
            }else{
                $('.tts-open-timeslots .number').html(available_lessons_for_the_day + 1);
                $('#used_credit_string p').html(used_credits - 1);
                $('#remaining_credit_string p').html(available_credits + 1);

                // CALCULATE AND UPDATE PROGRESS BARS
                sc_utils.student_calendar.set_circle_progress_bar(parseInt($('#used_credit_string p').html()), parseInt($('#remaining_credit_string p').html()));
                sc_utils.student_calendar.set_rank_bar(parseInt($('#used_credit_string p').html()));

            }

            
        });
        
        $(document).on("click", "#teetch_student_booking_calendar .day.correct-month", function(){
            sc_utils.current_selected_day = $(this).attr('data-value');
        })

    })	

</script>
