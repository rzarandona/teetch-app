	function pad(n){return n<10 ? '0'+n : n}
	//Utils
	let sc_utils = {
		
		selected_teacher_id: "",
		current_teacher_timeslots: [],
		current_day_timeslots: [],
		current_selected_day:"",
		current_selected_timeslot: "",
	
		selected_teacher_image: "",
		selected_teacher_name: "",
		selected_teacher_country: "",
		selected_teacher_rating: "",
	
		reserved_timeslot_total: 0,

		base_used_credits: 0,
		base_available_credits: 0,

		base_current_rank: '',
		base_next_rank: '',


		// STUDENT MODIFIER
		student_modifier: {
			current_day_schedules: [],
			all_schedules: []
		},
	
		dateParser: (date) => {

			let selected_date = new Date(date);
			let parsed_date = new Intl.DateTimeFormat('en-US', {month: 'long'}).format(date);
			parsed_date += " " + pad(selected_date.getDate()) + ", ";
			return parsed_date += selected_date.getFullYear();
		},

		processResponse(result, selector){

					
			var container= jQuery(selector).teetchapp();
			let $calendar = container.data('plugin_teetchapp');

			$calendar.removeAllEvents();

			var str=String(result);
			var x = str.slice(-1);
			if(x != "]"){
				var data = str.substring(0,str.length - 1);
				var response = JSON.parse(data);

				sc_utils.current_teacher_timeslots = response;

				var len = response.length;
				if(response.length > 0){
					for(var i=0; i<len; i++){
						var newEvent = {
							startDate: response[i].startDate,
							endDate: response[i].endDate,
							timeSlot:  response[i].timeSlot,
							scheduleID: response[i].scheduleID,
							'status': response[i].status
						}
						$calendar.addEvent(newEvent);	
					}
					
				}else{

					$calendar.removeAllEvents();
					sc_utils.current_teacher_timeslots = [];
					console.log(sc_utils)
				
				}
				
				return response;

			}//end if 

		},

		student_calendar: {
			loader_show(){
				jQuery('#student-calendar-loader').css('display', 'flex');
				setTimeout(function(){
					jQuery('#student-calendar-loader').css('opacity', '1');
				}, 150)
			},
			loader_hide(){
				jQuery('#student-calendar-loader').css('opacity', '0');
				setTimeout(function(){
					jQuery('#student-calendar-loader').css('display', 'none');
				}, 150)
			},

			// PROGRESS BAR METHODS
			set_circle_progress_bar(used_credits, available_credits){

				jQuery(document).ready(function(){
					let circle_total = used_credits + available_credits;

					let percentage = parseInt(used_credits) / parseInt(circle_total) * 100;
					percentage = parseInt(percentage);
					
					jQuery('#user-progress-circle').removeAttr('class');
		
					jQuery('#user-progress-circle').addClass('progress-circle');
					jQuery('#user-progress-circle').addClass('p'+percentage);
		
					if(percentage > 50){
						jQuery('#user-progress-circle').addClass('over50');
					}
	
					console.log("HEYHEY")
				})
			},
			set_rank_bar(credits_used){
				
        		let percentage = credits_used / 30 * 100;
				let ranks = [
					'A1', 
					'A2', // 30
					'B1', // 60
					'B2', // 90
					'C1', // 120
					'C2' // 150
				];
				let current_rank = jQuery('#current_rank_string p').text();
				let next_rank = jQuery('#next_rank_string p').text();
				
				if( percentage > 100){
					
					let deduction = Math.floor(percentage / 100) * 100;
					percentage = Math.floor(percentage ) -  deduction;

				}


				jQuery('.custom-progress').css(
					
					'background',
					
					"linear-gradient(to right, #3AB890 0%, #3AB890 "+ percentage +"%, #3AB89000 "+ percentage +"%)"
				
				)

				// NON-RECURSIVE RANKING FEATURE

				if(credits_used > 30 && credits_used < 61){
					// SET RANKS
					jQuery('#current_rank_string p').text('A2')
					jQuery('#next_rank_string p').text('B1');

				}else if(credits_used > 60 && credits_used < 91){
					// SET RANKS
					jQuery('#current_rank_string p').text('B1')
					jQuery('#next_rank_string p').text('B2');
				}
				else if(credits_used > 90 && credits_used < 121){
					// SET RANKS
					jQuery('#current_rank_string p').text('B2')
					jQuery('#next_rank_string p').text('C1');
				}
				else if(credits_used > 120 && credits_used < 150){
					// SET RANKS
					jQuery('#current_rank_string p').text('C1')
					jQuery('#next_rank_string p').text('C2');
				}
				else if(credits_used >= 150){
					percentage = 100;	
					jQuery('.custom-progress').css(
					
						'background',
						
						"linear-gradient(to right, #FF4F4E 0%, #FF4F4E "+ percentage +"%, #FF4F4E "+ percentage +"%)"
					
					)
				}
					
				let poulpy_adjust = percentage - 6;
				let poulpy_bubble_adjust = percentage - 8;
				
				if(percentage > 85){
					poulpy_adjust = 82;
					poulpy_bubble_adjust = 80;
				}
				if(percentage < 10){
					poulpy_adjust = 0;
					poulpy_bubble_adjust = 0;
				}
				
				jQuery('#poulpy-avatar').css('left', poulpy_adjust +'%');
				jQuery('#poulpy-bubble').css('left', poulpy_bubble_adjust +'%');

			}

		},
		teacher_calendar: {
		
			loader_show(){
				jQuery('#teacher-calendar-loader').css('display', 'flex');
				setTimeout(function(){
					jQuery('#teacher-calendar-loader').css('opacity', '1');
				}, 150)
			},
			loader_hide(){
				jQuery('#teacher-calendar-loader').css('opacity', '0');
				setTimeout(function(){
					jQuery('#teacher-calendar-loader').css('display', 'none');
				}, 150)
			},
			
		}

		}

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ($, window, document, undefined) {

	"use strict";

	// Create the defaults once
	var pluginName = "teetchapp",
	  defaults = {
		months: ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'], //string of months starting from january
		days: ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], //string of days starting from sunday
		displayYear: true, // display year in header
		fixedStartDay: true, // Week begin always by monday or by day set by number 0 = sunday, 7 = saturday, false = month always begin by first day of the month
		displayEvent: true, // display existing event
		disableEventDetails: false, // disable showing event details
		disableEmptyDetails: false, // disable showing empty date details
		events: [], // List of event
		onInit: function (calendar) {}, // Callback after first initialization
		onMonthChange: function (month, year) {}, // Callback on month change
		onDateSelect: function (date, events) {}, // Callback on date selection
		onEventSelect: function () {},              // Callback fired when an event is selected     - see $(this).data('event')
		onEventCreate: function( $el ) {},          // Callback fired when an HTML event is created - see $(this).data('event')
		onDayCreate:   function( $el, d, m, y ) {}  // Callback fired when an HTML day is created   - see $(this).data('today'), .data('todayEvents')
	  };
  
	// The actual plugin constructor
	function Plugin(element, options) {
	  this.element = element;
	  this.settings = $.extend({}, defaults, options);
	  this._defaults = defaults;
	  this._name = pluginName;
	  this.currentDate = new Date();
	  this.init();
	}
  
	// Avoid Plugin.prototype conflicts
	$.extend(Plugin.prototype, {
	  init: function () {
		var container = $(this.element);
		var todayDate = this.currentDate;
  
		var calendar = $('<div class="calendar"></div>');
		var header = $('<header>' +
		  '<h2 class="month"></h2>' +
		  '<a class="simple-calendar-btn btn-prev" href="#"></a>' +
		  '<a class="simple-calendar-btn btn-next" href="#"></a>' +
		  '</header>');
  
		this.updateHeader(todayDate, header);
		calendar.append(header);
  
		this.buildCalendar(todayDate, calendar);
		container.append(calendar);
		
		//calendar.append('<footer><div class="filter"></div></footer>');
		this.bindEvents();


		var today = new Date();
			var currDate = today.getDate().toString();
			var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			 if (currDate.length < 2) currDate = '0' + currDate;
			  $("#selected-day").html(currDate);
			  $("#selected-month").html(month[today.getMonth()]);
			  $("#selected-year").html( today.getFullYear());

		this.settings.onInit(this);
	  },
  
	  //Update the current month header
	  updateHeader: function (date, header) {
		var monthText = this.settings.months[date.getMonth()];
		monthText += this.settings.displayYear ? ' <span class="year">' + date.getFullYear() : '</span>';
		header.find('.month').html(monthText);
	  },
  
	  //Build calendar of a month from date
	  buildCalendar: function (fromDate, calendar) {
		var plugin = this;
		var todayDate = this.currentDate;
		var todaySched = plugin.getDateEvents(todayDate);
		if (todaySched.length){
			for (var i= 0; i < todaySched.length; i++){
				$('#timeslot-'+todaySched[i].timeSlot).prop('checked', true);
				$('#timeslot-'+todaySched[i].timeSlot).attr('data-shedule-id', todaySched[i].scheduleID);
			}
			$("#update_schedule").html("Update");
		}


		calendar.find('table').remove();
  
		var body = $('<table></table>');
		var thead = $('<thead></thead>');
		var tbody = $('<tbody></tbody>');
  
		//setting current year and month
		var y = fromDate.getFullYear(), m = fromDate.getMonth();
		var monthText = this.settings.months[fromDate.getMonth()];
  
		//first day of the month
		var firstDay = new Date(y, m, 1);
		//last day of the month
		var lastDay = new Date(y, m + 1, 0);
		// Start day of weeks
		var startDayOfWeek = firstDay.getDay();
  
		if (this.settings.fixedStartDay !== false) {
		  // Backward compatibility
		  startDayOfWeek =  this.settings.fixedStartDay ? 1 : this.settings.fixedStartDay;
  
		  // If first day of month is different of startDayOfWeek
		  while (firstDay.getDay() !== startDayOfWeek) {
			firstDay.setDate(firstDay.getDate() - 1);
		  }
		  // If last day of month is different of startDayOfWeek + 7
		  while (lastDay.getDay() !== ((startDayOfWeek + 6) % 7)) {
			lastDay.setDate(lastDay.getDate() + 1);
		  }
		}
  
		//Header day in a week ( (x to x + 7) % 7 to start the week by monday if x = 1)
		for (var i = startDayOfWeek; i < startDayOfWeek + 7; i++) {
		  thead.append($('<td>' + this.settings.days[i % 7].substring(0, 3) + '</td>'));
		}
  
		//For firstDay to lastDay
		for (var day = firstDay; day <= lastDay; day.setDate(day.getDate())) {
		  var tr = $('<tr></tr>');
		  //For each row
		  for (var i = 0; i < 7; i++) {
			var dd = day.getDate().toString();
			if (dd.length < 2) dd = '0' + dd;
			var td = $('<td><div data-value="'+ y + monthText + dd +'" class="day" data-date="' + day.toISOString() + '">' + day.getDate() + '</div></td>');
  
			var $day = td.find('.day');
			
			var currDate = this.currentDate;
			currDate.setHours(0,0,0,0);
			  if (Date.parse(day) < Date.parse(currDate)) {
			  $day.addClass("disabled");
			  $day.addClass("past");
			}
			//if today is this day
			if (day.toDateString() === (new Date).toDateString()) {
			  $day.addClass("today"); 
			}
			//if day is not in this month
			if (day.getMonth() != fromDate.getMonth()) {
			  $day.addClass("wrong-month");
			  $day.addClass("disabled");
			}else{
				$day.addClass("correct-month");
			}
  
			// filter today's events
			var todayEvents = plugin.getDateEvents(day);
  
			if (todayEvents.length && plugin.settings.displayEvent) {
			  $day.addClass(plugin.settings.disableEventDetails ? "has-event disabled" : "has-event");
			} else {
			  $day.addClass(plugin.settings.disableEmptyDetails ? "disabled" : "");
			}
			$day.data( 'todayEvents', todayEvents );


			// simplify further customization
			this.settings.onDayCreate( $day, day.getDate(), m, y );
  
			tr.append(td);
			day.setDate(day.getDate() + 1);
		  }
		  tbody.append(tr);
		}
  
		body.append(thead);
		body.append(tbody);
		
  
		var eventContainer = $('<div class="event-container"><div class="event-wrapper"></div></div>');
  
		calendar.append(body);
		$("#event-detail").append(eventContainer);
		
					// associate some data available from the onDayCreate callback   
	  },
	  changeMonth: function (value) {
		this.currentDate.setMonth(this.currentDate.getMonth() + value, 1);
		this.buildCalendar(this.currentDate, $(this.element).find('.calendar'));
		this.updateHeader(this.currentDate, $(this.element).find('.calendar header'));
		this.settings.onMonthChange(this.currentDate.getMonth(), this.currentDate.getFullYear())
	  },
	  //Init global events listeners
	  bindEvents: function () {
		var plugin = this;
  
		//Click previous month
		$(plugin.element).on('click', '.btn-prev', function ( e ) {
		  if( $('.event-container').size() > 1){
			$('.event-container:first-child').remove();
		  }
		  plugin.changeMonth(-1)
		  e.preventDefault();
		 
		});
  
		//Click next month
		$(plugin.element).on('click', '.btn-next', function ( e ) {
		  if( $('.event-container').size() > 1){
			$('.event-container:first-child').remove();
		  }
		  
		  plugin.changeMonth(1);
		  e.preventDefault();
		  
		});
  
		//Binding day event
		$(plugin.element).on('click', '.day', function (e) {

		  $('#timeslot input:checkbox').removeAttr('class');
		  $('#timeslot input:checkbox').attr('data-status','');
		  $('#timeslot input:checkbox').prop('disabled', false);
		  

		  $('td .day').removeClass("active");
		  var date = new Date($(this).data('date'));
		  var events = plugin.getDateEvents(date);

		  if($(this).hasClass('wrong-month')){	  
			  e.preventDefault();
			  return false;
		}
		  
		  if ($(this).hasClass('past') && $(this).hasClass('disabled')) {
			if( $('.event-container').size() > 1){
				$('.event-container:first-child').remove();
			}
			e.preventDefault();
			return false;
		  }else{
			$(this).addClass("active");
			$('.event-wrapper').empty();
			$('#timeslot input:checkbox').removeAttr('checked');
			$('#schedule_id').val('');
			var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
			var day = date.getDate().toString();
			if (day.length < 2) day = '0' + day;
			var year = date.getFullYear();    
			$("#selected-day").html(day);
			$("#selected-month").html(month[date.getMonth()]);
			$("#selected-year").html( year);
			$("#date_id").val($(this).attr("data-value"));
			plugin.displayEvents(events);
			if ($(this).hasClass('has-event')){
				$('#update_schedule').html('Update');
			  }else{
				$('#update_schedule').html('Add');
			}
		  }
		  
		  plugin.settings.onDateSelect(date, events);
		});
	  },
	  displayEvents: function (events) {
		var plugin = this;
		var container = $('.event-wrapper');
		var schedule_id = new Array();
		events.forEach(function (event) {
		  var startDate = new Date(event.startDate);
		  var endDate = new Date(event.endDate);
		  var $event = $('' +
		  '<div class="event">' +
		  ' <div class="event-hour">' + startDate.getHours() + ':' + (startDate.getMinutes() < 10 ? '0' : '') + startDate.getMinutes() + ' to ' + endDate.getHours() + ':' + (endDate.getMinutes() < 10 ? '0' : '') + endDate.getMinutes() + '</div>' +
		  ' <div class="event-date">' + plugin.formatDateEvent(startDate, endDate) + '</div>' +
		  ' <div class="event-timeslot">' + event.timeSlot + '</div>' +
		  '</div>');

		  

		  $('#timeslot-'+event.timeSlot).prop('checked', true);
		  $('#timeslot-'+event.timeSlot).attr('data-shedule-id',event.scheduleID );
		//   if (event.status != 'open'){
		// 	$('#timeslot-'+event.timeSlot).prop('disabled',true);
		//   }
		  $('#timeslot-'+event.timeSlot).attr('data-status',event.status );
		  $('#timeslot-'+event.timeSlot).addClass(event.status);
		  schedule_id.push(event.scheduleID);
		  $event.data( 'event', event );
		  $event.click( plugin.settings.onEventSelect );
  
		  // simplify further customization
		  plugin.settings.onEventCreate( $event );
  
		  container.append($event);
		})
		
	  },
	  addEvent: function(newEvent) {
		var plugin = this;
		// add the new event to events list
		plugin.settings.events = [...plugin.settings.events, newEvent]
		this.buildCalendar(this.currentDate, $(this.element).find('.calendar'));
	  },

	  removeAllEvents: function(){
		
		var plugin = this;
		// add the new event to events list
		plugin.settings.events = []
		this.buildCalendar(this.currentDate, $(this.element).find('.calendar'));

	  },
	 
	  getDateEvents: function (d) {
		var plugin = this;
		return plugin.settings.events.filter(function (event) {
		  return plugin.isDayBetween(new Date(d), new Date(event.startDate), new Date(event.endDate));
		});
	  },
	  isDayBetween: function (d, dStart, dEnd) {
		dStart.setHours(0,0,0);
		dEnd.setHours(23,59,59,999);
		d.setHours(12,0,0);
  
		return dStart <= d && d <= dEnd;
	  },
	  formatDateEvent: function (dateStart, dateEnd) {
		var formatted = '';
		formatted += this.settings.days[dateStart.getDay()] + ' - ' + dateStart.getDate() + ' ' + this.settings.months[dateStart.getMonth()].substring(0, 3);
  
		if (dateEnd.getDate() !== dateStart.getDate()) {
		  formatted += ' to ' + dateEnd.getDate() + ' ' + this.settings.months[dateEnd.getMonth()].substring(0, 3)
		}
		return formatted;
	  }
	});
  
	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function (options) {
	  return this.each(function () {
		if (!$.data(this, "plugin_" + pluginName)) {
		  $.data(this, "plugin_" + pluginName, new Plugin(this, options));
		}
	  });
	};
  
  })(jQuery, window, document);

// TOASTR
/*
 * Note that this is toastr v2.1.3, the "latest" version in url has no more maintenance,
 * please go to https://cdnjs.com/libraries/toastr.js and pick a certain version you want to use,
 * make sure you copy the url from the website since the url may change between versions.
 * */
!function(e){e(["jquery"],function(e){return function(){function t(e,t,n){return g({type:O.error,iconClass:m().iconClasses.error,message:e,optionsOverride:n,title:t})}function n(t,n){return t||(t=m()),v=e("#"+t.containerId),v.length?v:(n&&(v=d(t)),v)}function o(e,t,n){return g({type:O.info,iconClass:m().iconClasses.info,message:e,optionsOverride:n,title:t})}function s(e){C=e}function i(e,t,n){return g({type:O.success,iconClass:m().iconClasses.success,message:e,optionsOverride:n,title:t})}function a(e,t,n){return g({type:O.warning,iconClass:m().iconClasses.warning,message:e,optionsOverride:n,title:t})}function r(e,t){var o=m();v||n(o),u(e,o,t)||l(o)}function c(t){var o=m();return v||n(o),t&&0===e(":focus",t).length?void h(t):void(v.children().length&&v.remove())}function l(t){for(var n=v.children(),o=n.length-1;o>=0;o--)u(e(n[o]),t)}function u(t,n,o){var s=!(!o||!o.force)&&o.force;return!(!t||!s&&0!==e(":focus",t).length)&&(t[n.hideMethod]({duration:n.hideDuration,easing:n.hideEasing,complete:function(){h(t)}}),!0)}function d(t){return v=e("<div/>").attr("id",t.containerId).addClass(t.positionClass),v.appendTo(e(t.target)),v}function p(){return{tapToDismiss:!0,toastClass:"toast",containerId:"toast-container",debug:!1,showMethod:"fadeIn",showDuration:300,showEasing:"swing",onShown:void 0,hideMethod:"fadeOut",hideDuration:1e3,hideEasing:"swing",onHidden:void 0,closeMethod:!1,closeDuration:!1,closeEasing:!1,closeOnHover:!0,extendedTimeOut:1e3,iconClasses:{error:"toast-error",info:"toast-info",success:"toast-success",warning:"toast-warning"},iconClass:"toast-info",positionClass:"toast-top-right",timeOut:5e3,titleClass:"toast-title",messageClass:"toast-message",escapeHtml:!1,target:"body",closeHtml:'<button type="button">&times;</button>',closeClass:"toast-close-button",newestOnTop:!0,preventDuplicates:!1,progressBar:!1,progressClass:"toast-progress",rtl:!1}}function f(e){C&&C(e)}function g(t){function o(e){return null==e&&(e=""),e.replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/'/g,"&#39;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function s(){c(),u(),d(),p(),g(),C(),l(),i()}function i(){var e="";switch(t.iconClass){case"toast-success":case"toast-info":e="polite";break;default:e="assertive"}I.attr("aria-live",e)}function a(){E.closeOnHover&&I.hover(H,D),!E.onclick&&E.tapToDismiss&&I.click(b),E.closeButton&&j&&j.click(function(e){e.stopPropagation?e.stopPropagation():void 0!==e.cancelBubble&&e.cancelBubble!==!0&&(e.cancelBubble=!0),E.onCloseClick&&E.onCloseClick(e),b(!0)}),E.onclick&&I.click(function(e){E.onclick(e),b()})}function r(){I.hide(),I[E.showMethod]({duration:E.showDuration,easing:E.showEasing,complete:E.onShown}),E.timeOut>0&&(k=setTimeout(b,E.timeOut),F.maxHideTime=parseFloat(E.timeOut),F.hideEta=(new Date).getTime()+F.maxHideTime,E.progressBar&&(F.intervalId=setInterval(x,10)))}function c(){t.iconClass&&I.addClass(E.toastClass).addClass(y)}function l(){E.newestOnTop?v.prepend(I):v.append(I)}function u(){if(t.title){var e=t.title;E.escapeHtml&&(e=o(t.title)),M.append(e).addClass(E.titleClass),I.append(M)}}function d(){if(t.message){var e=t.message;E.escapeHtml&&(e=o(t.message)),B.append(e).addClass(E.messageClass),I.append(B)}}function p(){E.closeButton&&(j.addClass(E.closeClass).attr("role","button"),I.prepend(j))}function g(){E.progressBar&&(q.addClass(E.progressClass),I.prepend(q))}function C(){E.rtl&&I.addClass("rtl")}function O(e,t){if(e.preventDuplicates){if(t.message===w)return!0;w=t.message}return!1}function b(t){var n=t&&E.closeMethod!==!1?E.closeMethod:E.hideMethod,o=t&&E.closeDuration!==!1?E.closeDuration:E.hideDuration,s=t&&E.closeEasing!==!1?E.closeEasing:E.hideEasing;if(!e(":focus",I).length||t)return clearTimeout(F.intervalId),I[n]({duration:o,easing:s,complete:function(){h(I),clearTimeout(k),E.onHidden&&"hidden"!==P.state&&E.onHidden(),P.state="hidden",P.endTime=new Date,f(P)}})}function D(){(E.timeOut>0||E.extendedTimeOut>0)&&(k=setTimeout(b,E.extendedTimeOut),F.maxHideTime=parseFloat(E.extendedTimeOut),F.hideEta=(new Date).getTime()+F.maxHideTime)}function H(){clearTimeout(k),F.hideEta=0,I.stop(!0,!0)[E.showMethod]({duration:E.showDuration,easing:E.showEasing})}function x(){var e=(F.hideEta-(new Date).getTime())/F.maxHideTime*100;q.width(e+"%")}var E=m(),y=t.iconClass||E.iconClass;if("undefined"!=typeof t.optionsOverride&&(E=e.extend(E,t.optionsOverride),y=t.optionsOverride.iconClass||y),!O(E,t)){T++,v=n(E,!0);var k=null,I=e("<div/>"),M=e("<div/>"),B=e("<div/>"),q=e("<div/>"),j=e(E.closeHtml),F={intervalId:null,hideEta:null,maxHideTime:null},P={toastId:T,state:"visible",startTime:new Date,options:E,map:t};return s(),r(),a(),f(P),E.debug&&console&&console.log(P),I}}function m(){return e.extend({},p(),b.options)}function h(e){v||(v=n()),e.is(":visible")||(e.remove(),e=null,0===v.children().length&&(v.remove(),w=void 0))}var v,C,w,T=0,O={error:"error",info:"info",success:"success",warning:"warning"},b={clear:r,remove:c,error:t,getContainer:n,info:o,options:{},subscribe:s,success:i,version:"2.1.3",warning:a};return b}()})}("function"==typeof define&&define.amd?define:function(e,t){"undefined"!=typeof module&&module.exports?module.exports=t(require("jquery")):window.toastr=t(window.jQuery)});
//# sourceMappingURL=toastr.js.map

toastr.options = {
	"closeButton": false,
	"debug": false,
	"newestOnTop": false,
	"progressBar": true,
	"positionClass": "toast-bottom-right",
	"preventDuplicates": false,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "5000",
	"extendedTimeOut": "1000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "fadeIn",
	"hideMethod": "fadeOut"
  }