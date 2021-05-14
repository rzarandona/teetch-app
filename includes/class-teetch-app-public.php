<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       reinhard@wearetraction.io
 * @since      1.0.0
 *
 * @package    Teetch_App
 * @subpackage Teetch_App/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Teetch_App
 * @subpackage Teetch_App/public
 * @author     Rein Torres <reinhard@wearetraction.io>
 */
class Teetch_App_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Teetch_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Teetch_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/teetch-app-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Teetch_App_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Teetch_App_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/teetch-app-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'teetchAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
		wp_enqueue_script( $this->plugin_name);

	}

	public function require_methods (){
		require_once 'partials/shortcodes/teetch_app_calendar_shortcode.php';
	}

	//teetchapp calendar shortcode
	function teetchapp_calendar_shortcode(){ ob_start();?> 
		
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
		
		<?php return ob_get_clean();
	}//end function

	//teetchform shortcode
	function teetchapp_set_availability_shortcode(){ ob_start();?> 
			
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
		<?php return ob_get_clean();
	}//end function

	//teetchapp show today schedules
	function teacher_show_today_schedule_shortcode(){ ob_start(); ?>
					
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
		<?php return ob_get_clean();
	}//end function

	//teetchapp add/update/ schedule
	function update_schedule(){
			if ( !wp_verify_nonce( $_REQUEST['nonce'], "teetchapp_nonce")) {
				exit("No naughty business please");
			} 

			$data_timeslots = array();
			$response = array();
			$user_id  = $_REQUEST["user_id"];
			$date     = $_REQUEST["date"];
			$statuses   = $_REQUEST["status"];
			$action   = $_REQUEST["command"];
			$timeslots = $_REQUEST["timeslot"];

			$data_timeslots['status'] = $statuses;
			$data_timeslots['timeslots'] = $timeslots;

			//Note: this needs to be improve for code effeciency.
			if($action != "Add" ){
				//$schedule_ids  = explode(",",$_REQUEST["schedule_ids"]);
				$query_args = array(
					'author'=> get_current_user_id(),
					'numberposts'     => -1,
					'post_type'       => 'schedule',
					'meta-key'        => 'date',
					'meta_query'      => array(
					array(
						'key'     => 'date',
						'value'   => $date,
						'compare' => 'LIKE',
					)
					),
					'orderby'   => 'meta_value',
					'order'     => 'ASC',
				);
				$data = new WP_Query( $query_args );
				if($data->have_posts()){
					while($data->have_posts()){
						$data->the_post();
							wp_delete_post(get_the_ID());
					}
				}
				$response[] = 'Cleared';
				wp_reset_postdata();
			}
			
			
			if(sizeof($timeslots) > 0){
				

				$response = [];
				for($i = 0; $i < sizeof($timeslots); $i++){

					$title    = $user_id.'|'.strtotime($date).'|'.$data_timeslots['timeslots'][$i];
					$create_sched = array(
						'post_title' => wp_strip_all_tags($title),
						'post_status'   => 'publish',
						'post_author'   => $user_id,
						'post_type' => 'schedule'
					);
		
					$sched_id = wp_insert_post( $create_sched );
					update_post_meta($sched_id,'teacher-id',$user_id );
					update_post_meta($sched_id,'date',$date);
					update_post_meta($sched_id,'time-slot', $data_timeslots['timeslots'][$i]);
					update_post_meta($sched_id,'status',$data_timeslots['status'][$i] );

					$response[] = array('startDate' =>  $date,'endDate' =>  $date,'timeSlot' =>  $data_timeslots['timeslots'][$i] ,'scheduleID' =>  $sched_id, 'status' => $data_timeslots['status'][$i]);
					
				}//end for

			
			}//end if
			
			echo json_encode($response);

	} //end function

	//will supply the event json data
	function get_schedule(){
		
			$user_id   = get_current_user_id();
	
			
			$schedules = get_posts( array(
				'posts_per_page' => -1,
				'post_type'=> 'schedule',
				'author' => $user_id
			) );
			
			$response = array();
			if ( $schedules ) { 
				foreach ( $schedules as $schedule ) {
					setup_postdata( $schedule );

					
					$status = get_post_meta($schedule->ID, 'status', true);
					$date = get_post_meta($schedule->ID, 'date', true);
					$time_slot = get_post_meta($schedule->ID, 'time-slot', true);
					

					$response[] = array(
						'startDate' =>  $date,
						'endDate' =>   $date,
						'timeSlot' =>  $time_slot,
						'scheduleID' =>  $schedule->ID,
						'status' => $status
					);
				}	
				echo json_encode($response);
			}else{
				echo "[]";//return empty brackets
			}	
			wp_reset_postdata();
	}//end function





	// student booking calendar
	function student_booking_calendar_shortcode(){
	
		ob_start();
		?>
		
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
		
		
		
	
		<!-- SCRIPT -->
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
	

	
	
		<?php
		return ob_get_clean();
		
	}



	function get_teacher_availabilities(){

		$user_id   = $_REQUEST['teacher_id'];

		$schedules = get_posts( array(
			'posts_per_page' => -1,
			'post_type'=> 'schedule',
			'author' => $user_id
		) );
		
		$response = array();
		if ( $schedules ) { 
			$data = array();
			foreach ( $schedules as $schedule ) {
				setup_postdata( $schedule );

				$data = explode('|',$schedule->post_title);
				
				$status = get_post_meta($schedule->ID, 'status', true);

				$response[] = array(
					'startDate' =>  date('F d, Y',$data[1]),
					'endDate' =>   date('F d, Y',$data[1]),
					'timeSlot' =>  $data[2],
					'scheduleID' =>  $schedule->ID,
					'status' => $status
				);
			}	

			echo json_encode($response);
		}else{
			echo "[]";//return empty brackets
		}	

		wp_reset_postdata();

	}

	function reserve_schedules(){
		
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "teetchapp_nonce")) {
			exit("No naughty business please");
		} 

		$response = array();
		$user_id = get_current_user_id();
		
		$base_user_credits = get_user_meta($user_id, 'credits', true);
		$base_user_used_credits = get_user_meta($user_id, 'used-credits', true);
		$credits_on_queue = 0;

		if(sizeof($_REQUEST["sched_ids"]) > $base_user_credits){

			$response[] = 'insufficient_credits';
			
		}else{
			if(sizeof($_REQUEST["sched_ids"]) > 0 ){
				
				$schedule_ids = $_REQUEST['sched_ids'];
	
				foreach($schedule_ids as $schedule_id){
					array_push($response, $schedule_id);

					$credits_on_queue++;

					// Finally, reserve the schedule
					update_post_meta( $schedule_id ,'status', "reserved" );
					update_post_meta( $schedule_id ,'student-id', $user_id );
					update_post_meta( $schedule_id, 'date-reserved', date('d F Y, h:i:s A'));
					
				}//end for

				// Deduct user credits
				update_user_meta($user_id, 'credits', $base_user_credits - $credits_on_queue);

				// Incrememnt user used-credits
				update_user_meta($user_id, 'used-credits', $base_user_used_credits + $credits_on_queue);
	
			
			}//end if
		}


		// UPDATE RANK ALGO

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
		
	}

	 




	// MODIFIER PAGE
	function modifier_calendar_shortcode(){
		ob_start();
		?>

		<div class="student-modifier-wrapper">

			<div class="sm-popup-container">

			</div>

			<div class="student-modifier-calendar">

				<p class="title">Mon planning</p>
				<div id='teetch_modifier_calendar'></div>
				

			</div>

			<div class="student-modifier-teachers-container">
				
				<p class="title">Mes rendez-vous</p>
				<div class="student-modifier-teachers-list">

					<div class="list-header">
						<div></div>
						<div>Nom</div>
						<div>Date</div>
						<div>Heure</div>
						<div></div>
					</div>

					<div class='list-items'>

					</div>


				</div>

			</div>

		</div>


		<script>
			
			function map_today_schedules(){
				
				sc_utils.student_modifier.current_day_schedules.forEach(schedule => {


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
									
									class="item-modify-button"

								>MODIFIER</button>
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

				// GET ALL SCHEDULES
				let args = {
					action: 'get_student_schedules',
					nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
					user_id: <?php echo get_current_user_id(); ?>
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
						sc_utils.student_modifier.all_schedules = sc_utils.processResponse(response, "#teetch_modifier_calendar");
						$('.day.today').trigger('click');
						sc_utils.student_calendar.loader_hide();
						
					},
					complete:function(response){

					}
				
				});


			});


			jQuery(document).on('click', '.item-modify-button', function(){

				// jQuery(".sm-popup-container").css('transform', 'scale(1)');

				let teacher_name = jQuery(this).data('teacherName');
				let teacher_profile_picture = jQuery(this).data('teacherProfilePicture');
				let schedule_date = jQuery(this).data('scheduleDate');
				let schedule_id = jQuery(this).data('scheduleId');
				let timeslot = jQuery(this).data('timeslot');

				jQuery('.sm-popup-container').empty();
				jQuery('.sm-popup-container').append(`

					<div class="sm-popup-options">

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
								<input checked class="timeslot-checkbox" type="checkbox">
								<div class="checkbox-circle"></div>
							</span>

						</div>
					</div>

					<button data-schedule-id=' ${schedule_id} ' class='submit-sm-popup'>VALIDER</button>

					</div>

				`);

				jQuery('.sm-popup-container').css('transform', 'scale(1)');

			})

			jQuery(document).on('click', '.submit-sm-popup', function(){
				let schedule_id = jQuery(this).data('scheduleId');

				let args = {
					action: 'cancel_schedule',
					nonce: '<?php echo wp_create_nonce("teetchapp_nonce"); ?>',
					schedule_id: schedule_id
				}
				$.ajax({
					type:'post',
					dataTye:'JSON',
					url:teetchAjax.ajaxurl,
					data: args,
					beforeSend: function(){
						$('.sm-popup-container').style('transform', 'scale(0)');
						sc_utils.student_calendar.loader_show();
					},
					success: function(response){
						console.log(response)
						sc_utils.student_calendar.loader_hide();
						
					},
					complete:function(response){

					}
				
				});
			});

		</script>


		<?php return ob_get_clean();
	}

	function get_student_schedules(){

		$user_id   = $_REQUEST['user_id'];

		$schedules = get_posts( 
			[
				'posts_per_page' => -1,
				'post_type'=> 'schedule',
				'meta_query' => [

					[
						'key'   => 'student-id',
						'value' => $user_id,
					]

				]
			]
		 );
		
		$response = array();
		if ( $schedules ) { 
			$data = array();
			foreach ( $schedules as $schedule ) {
				setup_postdata( $schedule );

				$data = explode('|',$schedule->post_title);
				
				$status = get_post_meta($schedule->ID, 'status', true);

				// process teacher's data
				$teacher_id = get_post_meta($schedule->ID, 'teacher-id', true);
				$teacher_fname = get_user_meta($teacher_id, 'first_name', true);
				$teacher_lname = get_user_meta($teacher_id, 'last_name', true);
				$teacher_name = $teacher_fname . " " . $teacher_lname;

				$teacher_city = get_user_meta($teacher_id, 'city', true);
				$teacher_profile_picture = get_user_meta($teacher_id, 'profile-picture', true);

				$response[] = array(
					'startDate' =>  date('F d, Y',$data[1]),
					'endDate' =>   date('F d, Y',$data[1]),
					'timeSlot' =>  $data[2],
					'scheduleID' =>  $schedule->ID,
					'status' => $status,
					'teacher_name' => $teacher_name,
					'teacher_city' => $teacher_city,
					'teacher_profile_picture' => $teacher_profile_picture
				);
			}	

			echo json_encode($response);
		}else{
			echo "[]";//return empty brackets
		}	

		wp_reset_postdata();
		

		wp_reset_postdata();

	}
	
	function cancel_schedule(){
		$schedule_id = $_REQUEST['schedule_id'];
		echo $schedule_id;
	}





}//end class
