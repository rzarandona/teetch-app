<div id="teetch_calendar" class="calendar-container"></div>
<script id="teetchapp_data" type="text/javascript">

    jQuery(document).ready(function( $ ) {

        var container= $("#teetch_calendar").teetchapp({
            fixedStartDay: 0, 
            displayEvent:true,
            disableEmptyDetails: false,
            disableEventDetails: false,
            events: <?php echo $this->get_schedule();?>,
            onInit: function (calendar) {},
            onMonthChange: function (month, year) {}, 
            onDateSelect: function (date, events) {

                
            sc_utils.current_selected_day = sc_utils.dateParser(date);
                

            }, 
            onEventSelect: function() {},
            onEventCreate: function( $el ) {},
            onDayCreate:   function( $el, d, m, y ) {} 
        });

        $('div.today').removeClass("disabled");
        $('.day.wrong-month').addClass('disabled');
        $("#teetch_calendar .day.today").trigger('click');




        var selected_timeslots = new Array();
        var selday;



        function reload_teacher_calendar( user_id){
            var container= $("#teetch_calendar").teetchapp();
            let $calendar = container.data('plugin_teetchapp');
            let events_db = $calendar.settings.events;

            $.ajax({
                type:'post',
                dataTye:'JSON',
                url:teetchAjax.ajaxurl,
                data:{
                    action: 'get_schedule',
                    user_id: user_id,
                },
                beforeSend: function(){
                    
                    sc_utils.teacher_calendar.loader_show();
                    
                },
                success: function(response){
                    sc_utils.teacher_calendar.loader_hide();
                    var str=String(response);
                        var x = str.slice(-1);
                        if(x != "]"){
                            var data = str.substring(0,str.length - 1);
                            var response = JSON.parse(data);

                            var len = response.length;
                            if(response.length > 0){
                                
                                $calendar.removeAllEvents;
                                $calendar.settings.events = response;	

                            }
                        }
                },
                complete:function(response){
                    //location.reload();
                    
                }
            });
        }

    $("#update_schedule").click(function(e){ e.preventDefault();


        var selected_ids = new Array();
        var statuses = new Array();

        var checkedValues = $('input[name=timeslot]:checked').map(function() {
            selected_timeslots.push($(this).attr('id'));
            selected_ids.push($(this).attr('data-shedule-id'));
            
            if($(this).attr('data-status') == "" || $(this).attr('data-status') == undefined){
                statuses.push("open");
            }else{
                statuses.push($(this).attr('data-status'));
            }
            return this.value;
        }).get();
        var user_id=$(this).attr("data-user-id");
        var nonce=$(this).attr("data-nonce");
        var xmonth = $("#selected-month").html();
        var xday = $("#selected-day").html();
        var xyear = $("#selected-year").html();
        var command = $(this).html();
        
        if(!checkedValues.length && command == "Add"){
            toastr.error("Please select at least one timeslot.", "No timeslot selected");
            return false;
        } 
        
        console.log(statuses)

        var seldate= xmonth + " " +  xday + ", " + xyear;
        selday = $("#date_id").val();
    
        $.ajax({
            type:'post',
            dataTye:'JSON',
            url:teetchAjax.ajaxurl,
            data:{
                action: 'update_schedule',
                user_id: user_id,
                nonce: nonce,
                date: seldate,
                schedule_ids: selected_ids,
                timeslot: checkedValues,
                command: command,
                status: statuses
            },
            beforeSend: function(){

                sc_utils.teacher_calendar.loader_show();
            },
            success: function(response){
                sc_utils.teacher_calendar.loader_hide();
                    var str=String(response);
                    var x = str.slice(-1);
                    if(x != "]"){
                        var data = str.substring(0,str.length - 1);
                        var response = JSON.parse(data);

                        var len = response.length;
                        if(response.length > 0){
                            if(response[0] == 'Cleared'){
                                $('.day.active').removeClass("has-event");
                            }else{
                                $('.day.active').addClass("has-event");
                            }
                        }
                    }
                console.log(response);
            },
            complete:function(response){
                //location.reload();
                reload_teacher_calendar(user_id);
                toastr.success("Availabilities successfully updated.", "Success")
            }
        
        }); 

    });


    $('#timeslot input:checkbox').click(function(){
            if($(this).attr('data-status') == "reserved"){
                return false;
            }
    });
    


    });
</script>
