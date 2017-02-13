/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

//$(document).ready(function(){

	$(function() {
				/***
				the json config obj.
				name: the class given to the element where you want the tooltip to appear
				bgcolor: the background color of the tooltip
				color: the color of the tooltip text
				text: the text inside the tooltip
				time: if automatic tour, then this is the time in ms for this step
				position: the position of the tip. Possible values are
					TL	top left
					TR  top right
					BL  bottom left
					BR  bottom right
					LT  left top
					LB  left bottom
					RT  right top
					RB  right bottom
					T   top
					R   right
					B   bottom
					L   left
				***/

				//console.log($('.tour_siteconfiguration').visible());



				 var configArr = [];
				if($('.tour_aboutHrms').visible() && $('.tour_aboutHrms').attr('class'))
				{
					var obj = {
						"name" 		: "tour_aboutHrms",
						"bgcolor"	: "black",
						"color"		: "white",
						"position"	: "T",
						"text"		: application_name + " is an Open Source Human Resource Management Software, ideally providing Employee Management, Appraisal, Talent Acquisition, Leave Management, Background Check, Announcements, Analytics and Logs. It enables the administrators to configure the standards used in the organization such as currency codes, date formats, ethnic codes, etc. This application also meets the employee's everyday needs like Leave Management, Service Requests etc. It tracks existing employee data which includes personal history, skills, capabilities and accomplishments." ,
						"time" 		: 5000
						};
					configArr.push(obj);
				}
				if($('.tour_dashboard').attr('class'))
				{
					var obj = {
						"name" 		: "tour_dashboard",
						"bgcolor"	: "black",
						"color"		: "white",
						"position"	: "TL",
						"text"		: "Dashboard allows you to create Shortcuts and Widgets. You can have all the menus that are used regularly at one place. The easy to access Shortcuts and Widgets present in the dashboard will take you directly to your desired page hence avoiding tiresome menu redirections. You can also display your company announcements and employee's birthdays on the dashboard." ,
						"time" 		: 5000
						};
					configArr.push(obj);
				}
				if($('.tour_employeeselfservice').attr('class'))
				{
					var obj = {
						"name":"tour_employeeselfservice",
						"bgcolor":"black",
						"color":"white",
						"text":"Self-Service enables you to raise and handle leave requests.  You can access your personal information, documents and leave details. You can also check your team details.",
						"position":"L",
						"time":5000
					};
					configArr.push(obj);
				}
				if($('.tour_service').attr('class'))
				{
					var obj = {
						"name":"tour_service",
						"bgcolor":"black",
						"color":"white",
						"text":"Service Request delivers an easy to use IT portal with access to key services and information which are required by the employees. Configure the service request workflow without coding or scripting. You can raise and handle service requests.",
						"position":"TL",
						"time":5000
					};
				    configArr.push(obj);
				}
				if($('.tour_humanresource').attr('class'))
				{
					var obj = {"name":"tour_humanresource",
						"bgcolor":"black",
						"color":"white",
						"text":"HR (Human Resource) deals with the user, leave and holiday management configuration. It stores the employee data which includes personal history, skills, documents, education, visa & immigration etc.",
						"position":"L",
						"time":5000
					};
					configArr.push(obj);
				}
				if($('.tour_performanceappraisal').attr('class'))
				{
					var obj = {
						"name":"tour_performanceappraisal",
						"bgcolor":"black",
						"color":"white",
						"text":"Appraisals let you configure appraisal settings like parameters, skills, ratings and questions. You can provide a self-appraisal and appraise your employees. Users can also give a feedback about their manager using the Feed Forward feature.",
						"position":"TL",
						"time":5000
					};
					configArr.push(obj);
				}
				if($('.tour_requisition').attr('class'))
				{
					var obj = {"name":"tour_requisition",
						"bgcolor":"black",
						"color":"white",
						"text":"Recruitments helps you to monitor and maintain processes like initializing a requisition, managing CVs, scheduling interviews, shortlisting and selecting a candidate.",
						"position":"TL",
						"time":5000
					};
					configArr.push(obj);
				}
				if($('.tour_backgroundchecks').attr('class'))
				{
					var obj = {
						"name":"tour_backgroundchecks",
						"bgcolor":"black",
						"color":"white",
						"text":"Background Check module enables the pre and post-employment screening process. You can configure the screening types and manage the agencies you wish to work with.",
						"position":"TL",
						"time":5000
					};
					configArr.push(obj);					
				}
				if($('.tour_organization').attr('class'))
				{
					var obj = {
						"name":"tour_organization",
						"bgcolor":"black",
						"color":"white",
						"text":"Manage your organization's details, announcements, business units, departments and organization hierarchy here.",
						"position":"TL",
						"time":5000
					};
				    configArr.push(obj);
				}
				if($('.tour_reports').attr('class'))
				{
				    var obj = {
						"name":"tour_reports",
						"bgcolor":"black",
						"color":"white",
						"text":"Analytics uses descriptive techniques to represent your organization's data and allows you to generate custom reports and then export them to Excel or PDF.",
						"position":"R",
						"time":5000
					};
				    configArr.push(obj);
				}
				if($('.tour_siteconfiguration').attr('class'))
				{
					var obj = {
						"name":"tour_siteconfiguration",
						"bgcolor":"black",
						"color":"white",
						"text":"Site Configuration allows you to configure the standards used in your organization such as locations, currency codes, date formats, ethnic codes, etc.",
						"position":"R",
						"time":5000
					};
				    configArr.push(obj);
				}
				if($('.tour_managemodules').attr('class'))
				{
					var obj = {
						"name" 		: "tour_managemodules",
						"bgcolor"	: "black",
						"color"		: "white",
						"position"	: "R",
						"text"		: "Manage your " + application_name + " system by choosing the modules required for your organization. You can enable or disable the modules at any given time." ,
						"time" 		: 5000
						};
					configArr.push(obj);
				}
				 if($('.tour_expenses').attr('class'))
					{
						var obj = {
							"name":"tour_expenses",
							"bgcolor":"black",
							"color":"white",
							"text":"Expenses helps you to get more visibility and manage employee expenses efficiently. You can track employee expenses, store receipts, manage multiple expenses at once, have provision for various payment modes and do so much more!",
							"position":"R",
							"time":5000
						};
						configArr.push(obj);
					}
			     if($('.tour_assets').attr('class'))
					{
						var obj = {
							"name":"tour_assets",
							"bgcolor":"black",
							"color":"white",
							"text":"You can store and have comprehensive asset information at your fingertips. Keep a track of your organization’s assets and assign them to employees.",
							"position":"R",
							"time":5000
						};
						configArr.push(obj);
					}
			     if($('.tour_disciplinary').attr('class'))
					{
						var obj = {
							"name":"tour_disciplinary",
							"bgcolor":"black",
							"color":"white",
							"text":"Disciplinary module allows you to streamline your disciplinary management process. You can create violation types suitable for your organization, raise and manage disciplinary incidents against employees (defaulters).",
							"position":"R",
							"time":5000
						};
						configArr.push(obj);
					}
				if($('.tour_timemanagement').attr('class'))
				{
					var obj = {
						"name" 		: "tour_timemanagement",
						"bgcolor"	: "black",
						"color"		: "white",
						"position"	: "R",
						"text"		: "Time consists of timesheet management, tracking employee's activities & time offs. Generate accurate time reports on the basis of employees, projects and time period. Configure your organization’s projects and clients.",
						"time" 		: 5000
						};
					configArr.push(obj);
				}
				
			     if($('.tour_logs').attr('class'))
				{
					var obj = {
						"name":"tour_logs",
						"bgcolor":"black",
						"color":"white",
						"text":"Logs allow you to check the amount of activity happening on the application along with the daily users' login record.",
						"position":"L",
						"time":5000
					};
					configArr.push(obj);
				}
			    
				var config = configArr,
				//define if steps should change automatically
				autoplay	= false,
				//timeout for the step
				showtime,
				//current step of the tour
				step		= 0,
				//total number of steps
				total_steps	= config.length;
				
				/***
				*** check if the user has logged in for the first time and enable the script
				***/
				take_tour_flag = $('#take-tour-flag').val();
				if(take_tour_flag == 0){
					showOverlay();
					showControls();					
				}
				/***
				*** end of the first time user login check
				***/	
				/*
				we can restart or stop the tour,
				and also navigate through the steps
				*/
				
				$('#activatetour').click(function(){startTour();});
				$('#canceltour').click(function(){endTour();});
				$('#endtour').click(function(){endTour();});
				$('#restarttour').click(function(){restartTour();});
				$('#nextstep').click(function(){nextStep(); });
				$('#prevstep').click(function(){prevStep()});

				function startTour(){
					$('#activatetour').hide();
					$('#endtour,#restarttour').show();
					if(!autoplay && total_steps > 1)
						$('#nextstep').show();
					//showOverlay();
					$('#separator-tour').show();
					nextStep();
				}
				
				function nextStep(){
					if(!autoplay){
						if(step > 0)
							$('#prevstep').show();
						else
							$('#prevstep').hide();
						if(step == total_steps-1)
							$('#nextstep').hide();
						else
							$('#nextstep').show();	
					}	
					if(step >= total_steps){
						//if last step then end tour
						endTour();
						return false;
					}
					++step;
					showTooltip();
				}
				
				function prevStep(){
					if(!autoplay){
						if(step > 2)
							$('#prevstep').show();
						else
							$('#prevstep').hide();
						if(step == total_steps)
							$('#nextstep').show();
					}		
					if(step <= 1)
						return false;
					--step;
					showTooltip();
				}
				
				function endTour(){
					step = 0;
					if(autoplay) clearTimeout(showtime);
					removeTooltip();
					hideControls();
					hideOverlay();
				}
				
				function restartTour(){
					step = 0;
					$('#activatetour').hide();
					if(autoplay) clearTimeout(showtime);
					nextStep();
				}
				
				function showTooltip(){
					//remove current tooltip
					removeTooltip();
					
					var step_config	= config[step-1];
					var $elem = $('.' + step_config.name);
					$elem.css('','');

					if(autoplay)
						showtime	= setTimeout(nextStep,step_config.time);
					
					var bgcolor 		= step_config.bgcolor;
					var color	 		= step_config.color;
					
					var $tooltip		= $('<div>',{
						'id'			: 'tour_tooltip',
						'class' 	: 'tourtooltip',
						'html'		: '<p>'+step_config.text+'</p><span class="tourtooltip_arrow"></span>'
					}).css({
						'display'			: 'none',
						'background-color'	: bgcolor,
						'color'				: color
					});
					
					//position the tooltip correctly:
					
					//the css properties the tooltip should have
					var properties		= {};
					
					var tip_position 	= step_config.position;
					
					//append the tooltip but hide it
					$('BODY').prepend($tooltip);
					//console.log($elem);
					//get some info of the element
					var e_w	= ($elem)?$elem.outerWidth():200;
					var e_h	= ($elem)?$elem.outerHeight():200;
					var e_l	= ($elem)?$elem.offset().left:200;
					var e_t = ($elem)?($elem.offset().top-5):100;
					
					
					switch(tip_position){
						case 'TL'	:
							if(!$('.'+step_config.name).visible(true))
							{
								var temp = $('.simply-scroll-clip');
							
								temp.interval = setInterval(function() {
									if (temp[0]['scrollLeft'] >= 0) {										
										temp[0]['scrollLeft'] -= 6;
									}									
								},5);
								//if($elem.offset().left < 0)
									e_l	= $elem.offset().left + temp[0]['scrollLeft'];

							}
							properties = {
								'left'	: e_l + 'px',
								'top'	: e_t + e_h + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_TL');
							break;
						case 'TR'	:
							properties = {
								'left'	: e_l + e_w - $tooltip.width() + 'px',
								'top'	: e_t + e_h + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_TR');
							break;
						case 'BL'	:
							properties = {
								'left'	: e_l + 'px',
								'top'	: e_t - $tooltip.height() + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_BL');
							break;
						case 'BR'	:
							properties = {
								'left'	: e_l + e_w - $tooltip.width() + 'px',
								'top'	: e_t - $tooltip.height() + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_BR');
							break;
						case 'LT'	:
							properties = {
								'left'	: e_l + e_w + 'px',
								'top'	: e_t + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_LT');
							break;
						case 'LB'	:
							properties = {
								'left'	: e_l + e_w + 'px',
								'top'	: e_t + e_h - $tooltip.height() + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_LB');
							break;
						case 'RT'	:
							properties = {
								'left'	: e_l - $tooltip.width() + 'px',
								'top'	: e_t + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_RT');
							break;
						case 'RB'	:
							properties = {
								'left'	: e_l - $tooltip.width() + 'px',
								'top'	: e_t + e_h - $tooltip.height() + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_RB');
							break;
						case 'T'	:
							properties = {
								'left'	: e_l + e_w/2 - $tooltip.width()/2 + 'px',
								'top'	: e_t + e_h + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_T');
							break;
						case 'R'	:
							if(!$('.'+step_config.name).visible(true))
							{								
								var temp = $('.simply-scroll-clip');									
								temp.interval = setInterval(function() {			
									if (temp[0]['scrollLeft'] < temp[0]['scrollLeftMax']) {
										temp[0]['scrollLeft'] += 5;
									}								
								},5);
								//console.log($elem.offset().left+" >> "+temp[0]['scrollLeftMax']);								
								//if($elem.offset().left > 1000)
								e_l	= $elem.offset().left-temp[0]['scrollLeftMax'];								
							}
							properties = {
								'left'	: e_l - $tooltip.width() + 'px',
								'top'	: e_t + e_h/2 - $tooltip.height()/2 + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_R');
							break;
						case 'B'	:
							properties = {
								'left'	: e_l + e_w/2 - $tooltip.width()/2 + 'px',
								'top'	: e_t - $tooltip.height() + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_B');
							break;
						case 'L'	:
							if(!$('.'+step_config.name).visible(true))
							{						
								var temp = $('.simply-scroll-clip');	
								temp.interval = setInterval(function() {
									if (temp[0]['scrollRight'] > temp[0]['scrollRightMax']) {
										temp[0]['scrollRight'] -= 5;
									}
								},5);								
							}
							properties = {//e_l + e_w + 'px'
								'left'	: e_l + e_w + 'px',//e_w + 13 + 'px', 
								'top'	: e_t + e_h/2 - $tooltip.height()/2 + 'px'
							};
							$tooltip.find('span.tourtooltip_arrow').addClass('tourtooltip_arrow_L');
							break;
					}
					
					
					/*
					if the element is not in the viewport
					we scroll to it before displaying the tooltip
					 */
					var w_t	= $(window).scrollTop();
					var w_b = $(window).scrollTop() + $(window).height();
					//get the boundaries of the element + tooltip
					var b_t = parseFloat(properties.top,10);
					
					if(e_t < b_t)
						b_t = e_t;
					
					var b_b = parseFloat(properties.top,10) + $tooltip.height();
					if((e_t + e_h) > b_b)
						b_b = e_t + e_h;
						
					
					if((b_t < w_t || b_t > w_b) || (b_b < w_t || b_b > w_b)){
						$('html, body').stop()
						.animate({scrollTop: b_t}, 500, 'easeInOutExpo', function(){
							//need to reset the timeout because of the animation delay
							if(autoplay){
								clearTimeout(showtime);
								showtime = setTimeout(nextStep,step_config.time);
							}
							//show the new tooltip
							$tooltip.css(properties).show();
						});
					}
					else
					{
					//show the new tooltip
						$tooltip.css(properties).show();
					}
					
					var w_tL	= $(window).scrollLeft();
					var w_bL = $(window).scrollLeft() + $(window).width();
					var b_tL = parseFloat(properties.left,10);
					if(e_l < b_tL)
						b_tL = e_l;	
					var b_bL = parseFloat(properties.left,10) + $tooltip.width();
					if((e_l + e_w) > b_bL)
						b_b = e_l + e_w;
					
					if((b_tL < w_tL || b_tL > w_bL) || (b_bL < w_tL || b_b > w_bL))
					{
						$('html, body').stop()						
						.animate({scrollLeft: b_tL}, 500, 'easeInOutExpo', function(){
							//need to reset the timeout because of the animation delay
							if(autoplay){
								clearTimeout(showtime);
								showtime = setTimeout(nextStep,step_config.time);
							}
							//show the new tooltip
							$tooltip.css(properties).show();
						});
					}else
					{						
						//show the new tooltip
						$tooltip.css(properties).show();
					}
					
				}
				
				function removeTooltip(){
					$('#tour_tooltip').remove();
				}
				
				function showControls(){
					$('#tourcontrols').css('display','block');
					$idsArr = ['activatetour','restarttour','separator-tour','endtour','canceltour','nextstep','prevstep'];
					$.each($idsArr, function( index, value ) {
						if(value == 'nextstep' || value == 'prevstep') $('#'+value).hide();
						else $('#'+value).show();
					});
					$('#tourcontrols').animate({'right':'30px'},500);
				}
				
				function hideControls(){
					$('#tourcontrols').css('display','none');
				}
				
				function showOverlay(){
					var $overlay	= '<div id="tour_overlay" class="overlay"></div>';
					$('BODY').prepend($overlay);
				}
				
				function hideOverlay(){
					$('#tour_overlay').remove();
				}

				/***
				*** check if the user has logged in for the first time and enable the script
				***/
				if(take_tour_flag == 0){
					$('#endtour,#canceltour').click(function(){
						 jQuery.ajax({
								type: "POST",
								url: base_url+'/index/sessiontour',
								datatype: 'json',
								success: function(responce) {					
										
								},
							});
					});
				}
				
				$('#tourLink').click(function(){ 
					setdisplaymenu('tour');
					showOverlay();
					showControls();					
				});
									
			});