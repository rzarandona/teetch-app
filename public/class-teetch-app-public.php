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



	/* *****************************************************************************************
	// *****************************************************************************************
	// TEACHER DASHBOARD SHORTCODES
	// *****************************************************************************************
	*******************************************************************************************/

		/*
			required in page: ESPACE TEETCHER
			-> outputs teacher availability setting calendar
		*/
		function teetchapp_calendar_shortcode(){
			
			require "shortcodes/teachers/teetchapp_calendar_shortcode.php";

		}


		/*
			required in page: ESPACE TEETCHER
			-> outputs teacher availability setting checkboxes list 
		*/
		function teetchapp_set_availability_shortcode(){
			
			require "shortcodes/teachers/teetchapp_set_availability_shortcode.php";

		}

		/*
			required in page: ESPACE TEETCHER
			-> outputs teacher availabilities for the current day
		*/
		function teacher_show_today_schedule_shortcode(){
			
			require "shortcodes/teachers/teacher_show_today_schedule_shortcode.php";


		}


	/* *****************************************************************************************
	// *****************************************************************************************
	// TEACHER DASHBOARD ACTIONS
	// *****************************************************************************************
	*******************************************************************************************/

		/*
			POST (ADD/UPDATE):
			-> if submitted day has saved schedules -> ADD the new set schedules on top of the initial schedules
			-> if submitted day has no schedules -> ADD the new set schedules
		*/
		function update_schedule(){
				
			require "actions/teachers/update_schedule.php";

		}


		/* GET : -> returns all availabilities of the logged-in teacher */
		function get_schedule(){

			require "actions/teachers/get_schedule.php";

		}





	/* *****************************************************************************************
	// *****************************************************************************************
	// STUDENT BOOKING DASHBOARD SHORTCODES
	// *****************************************************************************************
	*******************************************************************************************/

		/*
			required in page: ACCUEIL/PROFIL PAGE
			-> outputs student "booking" calendar
		*/
		function student_booking_calendar_shortcode(){
		
			require "shortcodes/students/student_booking_calendar_shortcode.php";
			
		}

		/*
			required in page: MES LECONS/MY LESSONS PAGE
			-> outputs student "modify schedules" calendar
		*/
		function modifier_calendar_shortcode(){

			require "shortcodes/students/modifier_calendar_shortcode.php";

		}


	/* *****************************************************************************************
	// *****************************************************************************************
	// STUDENT BOOKING DASHBOARD ACTIONS
	// *****************************************************************************************
	*******************************************************************************************/
	
		/* GET : -> gets logged-in student's schedules */
		function get_student_schedules(){

			require "actions/students/get_student_schedules.php";

		}
		
		/* POST (UPDATE) : -> cancels the submitted schedule and deducts the logged-in student's credits */
		function cancel_schedule(){

			require "actions/students/cancel_schedule.php";
			
		}


		/****************************
		* 
		* 	Below is the step-by-step
		*	server-side process in delegating
		*	a student schedule reservation.
		* 
		* ***************************/

		
			/* GET : -> returns all availabilities of a selected teacher */
			function get_teacher_availabilities(){

				require "actions/students/get_teacher_availabilities.php";

			}


			/*
				POST (ADD/UPDATE):
				-> 1. verifies if user has enough credits, as compared to the number of submitted reservations
				-> 2. if so, reserve the schedules;
				-> 3. create zoom-post-type and initialize a zoom link;
				-> 4. create relevant notification posts
				-> 5. deduct the credits; 
				-> 6. update user rank (if necessary)
			*/
			function reserve_schedules(){
				
				require "actions/students/reserve_schedules.php";
				
			}

			/* POST (ADD): Creates the zoom link */
			function teetch_create_zoom_meeting($zoom_teacher_id, $meeting_id, $meeting_datetime, $schedule_id) {

				require "actions/students/teetch_create_zoom_meeting.php";

			}

			/* SMTP RELAY: Sends confirmation email with zoom link to relevant student and teacher */
			function send_mail($args){
				
				require "actions/students/send_mail.php";
				
			}

	/* *****************************************************************************************
	// *****************************************************************************************
	// NOTIFICATIONS SHORTCODES
	// *****************************************************************************************
	*******************************************************************************************/
	public function mes_messages_popup_shortcode(){
		require "shortcodes/both/mes_messages_popup_shortcode.php";
	}

	/* *****************************************************************************************
	// *****************************************************************************************
	// NOTIFICATIONS ACTIONS
	// *****************************************************************************************
	*******************************************************************************************/
	public function get_user_messages(){
		require "actions/both/get_user_messages.php";
	}


}//end class
