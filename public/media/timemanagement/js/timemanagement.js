function getErrorHtml(formErrors, id, flag) {
	var o = '<span class="errors' + flag + '" id="errors-' + id + '">';
	o += formErrors;
	o += '</span>';
	return o;
}

function getPrevMonthData(startYrMon,selYrMon){
   var url = base_url + module_name + "/index/index";
   window.location.href = url+'?startYrMon='+startYrMon+'&selYrMon='+selYrMon+'&flag=pre';
}
function getNextMonthData(startYrMon,selYrMon){
   var url = base_url + module_name + "/index/index";
   window.location.href = url+'?startYrMon='+startYrMon+'&selYrMon='+selYrMon+'&flag=next';
}
function weeklyView() {
	
	var selYrMon = $("#calSelYrMonth").val();
    var url = base_url + module_name + "/index/week";
	//window.location.href = url+'?selYrMon='+selYrMon+'&week=1';
	window.location.href = base_url+'/weekview/'+selYrMon+'/1';
	
}
function viewTimeEntry(year_month) {
	  //console.log(" year_month "+year_month);	
	 // var url = base_url + module_name + "/index/week?flag=time&selYrMon="+year_month;
	 // window.location.href = url;
	  window.location.href = base_url+"/timeentry/"+year_month+"///time";
}
function  viewEnterTime(week,calWeek) {
	 var selYrMon = $("#calSelYrMonth").val();
	// var url = base_url + module_name + "/index/week?flag=time&selYrMon="+selYrMon+'&week='+week+'&calWeek='+calWeek;
	 var url = base_url +"/timeentry/"+selYrMon+'/'+week+'/'+calWeek+'/time';
	
	 window.location.href = url;
	
}
function monthlyView(){
	
	  var selYrMon = $("#calSelYrMonth").val();
	//  var startYrMon = "";
	//  var startYrMonArray = selYrMon.split('-');
	//  startYrMon = startYrMonArray[0]+"-"+(parseInt(startYrMonArray[1])-3);
	 // startYrMon = "2015-04";
	 // selYrMon = "2015-07";
	  var url = base_url + module_name + "/index/index";
	//  window.location.href = url+'?startYrMon='+startYrMon+'&selYrMon='+selYrMon+'&flag=mon';
	//  window.location.href = url+'?selYrMon='+selYrMon+'&flag=mon';
	  window.location.href = base_url+'/monthview/'+selYrMon+'/mon';
	
}
function getWeekData(flag,weekNo,calWeek,day) {
	if(day == undefined )
		day = '';
	
   var selYrMon = $("#calSelYrMonth").val();
   var url = base_url + module_name + "/index/week";
	//window.location.href = url+'?selYrMon='+selYrMon+'&week='+weekNo+'&calWeek='+calWeek+'&flag='+flag+'&day='+day;
   if(flag != 'time')
	   window.location.href = base_url+'/weekview/'+selYrMon+'/'+weekNo+'/'+calWeek+'/'+flag+'/'+day;
   else    
	    window.location.href = base_url+'/timeentry/'+selYrMon+'/'+weekNo+'/'+calWeek+'/'+flag+'/'+day;
}
function editWeekData(flag,day) {
	
	var selYrMon = $("#calSelYrMonth").val();
    var url = base_url + module_name + "/index/week";
	//window.location.href = url+'?selYrMon='+selYrMon+'&flag='+flag+'&day='+day;
    window.location.href = base_url+'/timeentryday/'+selYrMon+'/'+flag+'/'+day;
	
}
function submitDayTimesheet(day) {
	
	var selYrMon = $("#calSelYrMonth").val();
//	var startYrMon = $("#startYrMon").val();
    var url = base_url + module_name + "/index/submit/format/json";
    var yeMonArr = selYrMon.split("-");
    
    var messageAlert = 'Are you sure you want to submit timesheet for day : ' + day +'-'+yeMonArr[1]+'-'+yeMonArr[0]+ '? ';
    jConfirm(messageAlert, "Submit Timesheet", function(r) {

		if (r == true) {
		
		    $.ajax({
				url: url,
				data: 'selYrMon='+selYrMon+'&day='+day+'&call=ajaxcall',
				type: 'POST',		
				success: function(response) {			 
					  if(response.status == 'success'){
						  var flag = response.status; 					
						// $("#error_message").html('<div id="messageData" >'+ response.message + '</div>');
						$("#error_message").show(); 
            $('#error_message').empty();	    
						$("#error_message").html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>' + response.message + '</div></div>');
						 setTimeout(function() {
								$('#error_message').fadeIn('slow').fadeOut(function() {
								  location.href = base_url+ "/monthview/"+selYrMon+"/mon";
								});
							}, 3000);
						//location.href = base_url + module_name + "/index/index?startYrMon="+startYrMon+"&selYrMon="+selYrMon+"&flag=mon";
						// location.href = base_url + module_name + "/index/index?selYrMon="+selYrMon+"&flag=mon";
						
					  }
				}
		    });
		}
    });
		
}
function submitWeekTimesheet(week, calWeek, submitText) {
	
	var selYrMon = $("#calSelYrMonth").val();
//	var unfilledDays = $("#unfilledDays").val();
/*	if(unfilledDays > 0) {
		
		var message = "Please fill time sheet for unfilled days.";
		
		 //$("#error_message").html('<div id="messageData" >Please fill time sheet for unfilled days.</div>');
		if(!$('#error_message').is(':visible'))
		{
			console.log("not visible");
			$('#error_message').css('display', 'block');
			 setTimeout(function() {
					$('#error_message').fadeOut('slow');
				}, 3000);
		} else {
		$("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>' + message + '</div></div>');
		 setTimeout(function() {
				$('#error_message').fadeOut('slow');
			}, 3000);
		}
		 
	} else {
	*/	
		var url = base_url + module_name + "/index/submit/format/json";
		var messageAlert = 'Are you sure you want to '+submitText.toLowerCase()+' timesheet for the filled days of week : ' + week + '? ';
    jConfirm(messageAlert, submitText+" timesheet", function(r) {

      		if (r == true) {
          		$.ajax({
          			url: url,
          			data: 'selYrMon='+selYrMon+'&week='+week+'&call=ajaxcall&calWeek='+calWeek,
          			type: 'POST',		
          			success: function(response) {			 
          				  if(response.status == 'success'){
          					  var flag = response.status; 				
          					// $("#error_message").html('<div id="messageData" >'+ response.message + '</div>');
          					 $("#error_message").show(); 
                     $('#error_message').empty();	    
          					 $("#error_message").html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>' + response.message + '</div></div>'); 
          					 setTimeout(function() {
          							$('#error_message').fadeIn('slow').fadeOut(function() {
          					       location.href = base_url +"/weekview/"+selYrMon+"/"+week+"/"+calWeek+"//";		
          							});
          						}, 3000);
          					//location.href = base_url + module_name + "/index/week?week="+week+"&calWeek="+calWeek+"&selYrMon="+selYrMon;
          					
          				  }
          			}
          	    });		
	       }
  });
 // }	
	
}
function saveAndSubmitTimesheet(weekNo,calWeek,weekStart,weekEnd) {

    var eraseVisible = false;
    $('span[class^="erase_icon"]').each(function(){   

      var id = $(this).attr('id');
      var isEraseVisible = $('#'+id).is(':visible');
      if(isEraseVisible)
          eraseVisible = true;
  
    });
    if(eraseVisible) {

  	  var selYrMon = $("#calSelYrMonth").val();
      var messageAlert = 'Are you sure you want to save and submit the timesheet for week : ' + weekNo + '? ';
      jConfirm(messageAlert, "Save and Submit Timesheet", function(r) {
  
        		if (r == true) {
                var selYrMon = $("#calSelYrMonth").val();
              	var url = base_url + module_name + "/index/save/format/json";	
              	var taskHrs = new Array();  
              	var sun_note = jQuery("textarea#sun_note_text").val();
              	var mon_note = jQuery("textarea#mon_note_text").val();
              	var tue_note = jQuery("textarea#tue_note_text").val();
              	var wed_note = jQuery("textarea#wed_note_text").val();
              	var thu_note = jQuery("textarea#thu_note_text").val();
              	var fri_note = jQuery("textarea#fri_note_text").val();
              	var sat_note = jQuery("textarea#sat_note_text").val();
              	var week_note = jQuery("textarea#week_note_text").val();
              	
              	sun_note = (sun_note != undefined)?sun_note:'';
              	mon_note = (mon_note != undefined)?mon_note:'';
              	tue_note = (tue_note != undefined)?tue_note:'';
              	wed_note = (wed_note != undefined)?wed_note:'';
              	thu_note = (thu_note != undefined)?thu_note:'';
              	fri_note = (fri_note != undefined)?fri_note:'';
              	sat_note = (sat_note != undefined)?sat_note:'';
              	week_note = (week_note != undefined)?week_note:'';
  
              	var sunHrs = parseInt($('#sun_tot_hrs').text().trim());
              	var monHrs = parseInt($('#mon_tot_hrs').text().trim());
              	var tueHrs = parseInt($('#tue_tot_hrs').text().trim());
              	var wedHrs = parseInt($('#wed_tot_hrs').text().trim());
              	var thuHrs = parseInt($('#thu_tot_hrs').text().trim());
              	var friHrs = parseInt($('#fri_tot_hrs').text().trim());
              	var satHrs = parseInt($('#sat_tot_hrs').text().trim());
  	
              	if(sunHrs >= 24 || monHrs >= 24 || tueHrs >= 24 || wedHrs >= 24 || thuHrs >= 24
              			|| friHrs >= 24 || satHrs >= 24) {
              		
              		 $("#error_message").show(); 
                   $('#error_message').empty();	    
              		 $("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>Time per day cannot be more than 24 Hours</div></div>');
              		 setTimeout(function() {
              				$('#error_message').fadeOut('slow');
              			}, 10000);
  		 
              	} else {
              	
              		$('#weekview').find('tr.proj_task_col').each(function(){ 
              		     
              		      var id = $(this).attr('id');
              		      var res = id.split('_');
              		      var projId = res[3];
              		      var projTaskId = res[4];
              		     
              		      var sunHrs = $('#input_sun_'+res[4]).val().trim();
              		      var monHrs = $('#input_mon_'+res[4]).val().trim();
              		      var tueHrs = $('#input_tue_'+res[4]).val().trim();
              		      var wedHrs = $('#input_wed_'+res[4]).val().trim();
              		      var thuHrs = $('#input_thu_'+res[4]).val().trim();
              		      var friHrs = $('#input_fri_'+res[4]).val().trim();
              		      var satHrs = $('#input_sat_'+res[4]).val().trim();              		            
              	              
              		      if(sunHrs != '' || monHrs != '' || tueHrs != '' || wedHrs != '' || thuHrs != '' || friHrs != '' || satHrs != '') {           
              	         
              		    	  taskHrs.push(projTaskId+"#"+projId+"#"+sunHrs+"#"+monHrs+"#"+tueHrs+"#"+wedHrs+"#"+thuHrs+"#"+friHrs+"#"+satHrs);
              		      } 
              			  
              		});
            		if(taskHrs.length > 0) {
            			$.ajax({
            				url: url,
            				data: 'data='+taskHrs+'&selYrMon='+selYrMon+'&calWeek='+calWeek+'&week='+weekNo
            						+'&call=ajaxcall'+'&weekStart='+weekStart+'&weekEnd='+weekEnd
            						+'&sun_note='+sun_note+'&mon_note='+mon_note+'&tue_note='+tue_note
            						+'&wed_note='+wed_note+'&thu_note='+thu_note+'&fri_note='+fri_note+'&sat_note='+sat_note+'&week_note='+week_note,
            				type: 'POST',		
            				success: function(response) {			 
            					  if(response.status == 'success'){            					  	
            						  var flag = response.status; 
            						   
            						   	$.ajax({
                        			url: base_url + module_name + "/index/submit/format/json",
                        			data: 'selYrMon='+selYrMon+'&week='+weekNo+'&call=ajaxcall&calWeek='+calWeek,
                        			type: 'POST',		
                        			success: function(response) {			 
                        				  if(response.status == 'success'){
                        					  var flag = response.status; 				
                        					 
                        					 //$('#error_message').css('display', 'block');
                                   $("#error_message").show(); 
                                   $('#error_message').empty();	         
                        					 $("#error_message").html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>Saved and Submitted successfully for week'+ weekNo +'.'+' </div></div>'); 
                        					 setTimeout(function() {
                        							$('#error_message').fadeIn('slow').fadeOut(function() {
                        				         location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";			
                        							});
                        						}, 3000);
                                  //location.href = base_url + module_name + "/index/week?selYrMon="+selYrMon+"&week="+weekNo+"&calWeek="+calWeek+"&flag=time";
                                 
                        				  }
                        			}
                        	    });	          						
            					  }
								  else
								  {
									  $("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>Date of joining should be greater than current date</div></div>');
										setTimeout(function() {
											$('#error_message').fadeIn('slow').fadeOut(function() {
											location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";		
												});
										}, 3000);
								  }
            				  }
							  
            		    });
            		}
            	}	    		
  	     }
    });
  } else {
  
     		var message = "Enter time to save and submit timesheet for this week.";     		
    		if(!$('#error_message').is(':visible'))
    		{ 
    			$('#error_message').css('display', 'block');
    			$("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>' + message + '</div></div>');
       		 		setTimeout(function() {
       		 			$('#error_message').fadeOut('slow');
       			}, 10000);
    			
    		
    		} 
  }
}
function getMonthData(startYrMon,selYrMon) {
   //	console.log("month "+month+" year "+ year);
   
   var url = base_url + module_name + "/index/index";
  // console.log("url = "+url + " domain "+domain_data);
   var empid = $("#empid").val();
   window.location.href = url+'?startYrMon='+startYrMon+'&empid='+empid+'&selYrMon='+selYrMon+'&flag=mon';
/*	 $.ajax({
				url: url,
				type: 'POST',
				dataType:'json',
				data: 'call=ajaxcall&month=' + month + '&year='+ year + '&empid='+empid ,
				success: function(response) {
				//console.log(" response "+response);
				//	$('#grid_' + objname).html(response);
					//$('#gridblock').html(response);
				}
		});
*/          
}
function eraseWeek(weekNo,calWeek) {
	var selYrMon = $("#calSelYrMonth").val();
	var url = base_url + module_name + "/index/eraseweek/format/json";	
	
	var messageAlert = 'Are you sure you want to erase timesheet for this week '+weekNo+'? ';
	jConfirm(messageAlert, "Erase timesheet for week " + weekNo, function(r) {

			if (r == true) {			
				$.ajax({
					url: url,
					data: 'selYrMon='+selYrMon+'&calWeek='+calWeek+'&week='+weekNo+'&call=ajaxcall',
					type: 'POST',		
					success: function(response) {			 
						  if(response.status == 'success'){			  	
							  var flag = response.status; 				  
						//	 $("#error_message").html('<div id="messageData" >'+ response.message + '</div>');
						  $("#error_message").show(); 
              $('#error_message').empty();	    
							 $("#error_message").html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>' + response.message + '</div></div>');
							 setTimeout(function() {
									$('#error_message').fadeIn('slow').fadeOut(function() {
						        	location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";			
										});
								}, 3000);
							//location.href = base_url + module_name + "/index/week?selYrMon="+selYrMon+"&week="+weekNo+"&calWeek="+calWeek+"&flag=time";
						
						  }else
						  {
								 $("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>Date of joining should be greater than current date</div></div>');
								    setTimeout(function() {
          								$('#error_message').fadeIn('slow').fadeOut(function() {
          						        location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";		
          									});
          							}, 3000);
								
						  }
					}
			    });
			}
    });
	
}
function saveTimesheet(weekNo,calWeek,weekStart,weekEnd) {
	
	var selYrMon = $("#calSelYrMonth").val();
	var url = base_url + module_name + "/index/save/format/json";	
	var taskHrs = new Array();  
	var sun_note = jQuery("textarea#sun_note_text").val();
	var mon_note = jQuery("textarea#mon_note_text").val();
	var tue_note = jQuery("textarea#tue_note_text").val();
	var wed_note = jQuery("textarea#wed_note_text").val();
	var thu_note = jQuery("textarea#thu_note_text").val();
	var fri_note = jQuery("textarea#fri_note_text").val();
	var sat_note = jQuery("textarea#sat_note_text").val();
	var week_note = jQuery("textarea#week_note_text").val();
	
	sun_note = (sun_note != undefined)?sun_note:'';
	mon_note = (mon_note != undefined)?mon_note:'';
	tue_note = (tue_note != undefined)?tue_note:'';
	wed_note = (wed_note != undefined)?wed_note:'';
	thu_note = (thu_note != undefined)?thu_note:'';
	fri_note = (fri_note != undefined)?fri_note:'';
	sat_note = (sat_note != undefined)?sat_note:'';
	week_note = (week_note != undefined)?week_note:'';
	
//	console.log(" sun_note "+sun_note);
//	console.log(" mon_note "+mon_note);
//	console.log(" tue_note "+tue_note);
//	console.log(" wed_note "+wed_note);
//	console.log(" thu_note "+thu_note);
//	console.log(" fri_note "+fri_note);
//	console.log(" sat_note "+sat_note);
//	console.log(" week_note "+week_note);
	var sunHrs = parseInt($('#sun_tot_hrs').text().trim());
	var monHrs = parseInt($('#mon_tot_hrs').text().trim());
	var tueHrs = parseInt($('#tue_tot_hrs').text().trim());
	var wedHrs = parseInt($('#wed_tot_hrs').text().trim());
	var thuHrs = parseInt($('#thu_tot_hrs').text().trim());
	var friHrs = parseInt($('#fri_tot_hrs').text().trim());
	var satHrs = parseInt($('#sat_tot_hrs').text().trim());
	
//	var sunTimeArray = sun_tot_hrs.split(":");
//	var monTimeArray = mon_tot_hrs.split(":");
//	var tueTimeArray = tue_tot_hrs.split(":");
//	var wedTimeArray = wed_tot_hrs.split(":");
//	var thuTimeArray = thu_tot_hrs.split(":");
//	var friTimeArray = fri_tot_hrs.split(":");
//	var satTimeArray = sat_tot_hrs.split(":");
//	
//	var sunHrs = sunTimeArray[0];
//	var monHrs = monTimeArray[0];
//	var tueHrs = tueTimeArray[0];
//	var wedHrs = wedTimeArray[0];
//	var thuHrs = thuTimeArray[0];
//	var friHrs = friTimeArray[0];
//	var satHrs = satTimeArray[0];
	
//	console.log(" sun "+sunHrs);
//	console.log(" mon "+monHrs);
//	console.log(" tue "+tueHrs);
//	console.log(" wed "+wedHrs);
//	console.log(" thu "+thuHrs);
//	console.log(" fri "+friHrs);
	
	  var eraseVisible = false;
    $('span[class^="erase_icon"]').each(function(){   

      var id = $(this).attr('id');
      var isEraseVisible = $('#'+id).is(':visible');
      if(isEraseVisible)
          eraseVisible = true;
  
    });
    if(eraseVisible) {
    
          	if(sunHrs >= 24 || monHrs >= 24 || tueHrs >= 24 || wedHrs >= 24 || thuHrs >= 24
          			|| friHrs >= 24 || satHrs >= 24) {
          		
          		 $("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>Time per day cannot be more than 24 Hours</div></div>');
          		 setTimeout(function() {
          				$('#error_message').fadeOut('slow');
          			}, 10000);
          		 
          	} else {
          	
          		$('#weekview').find('tr.proj_task_col').each(function(){ 
          		     
          		      var id = $(this).attr('id');
          		      var res = id.split('_');
          		      var projId = res[3];
          		      var projTaskId = res[4];
          		     
          		      var sunHrs = $('#input_sun_'+res[4]).val().trim();
          		      var monHrs = $('#input_mon_'+res[4]).val().trim();
          		      var tueHrs = $('#input_tue_'+res[4]).val().trim();
          		      var wedHrs = $('#input_wed_'+res[4]).val().trim();
          		      var thuHrs = $('#input_thu_'+res[4]).val().trim();
          		      var friHrs = $('#input_fri_'+res[4]).val().trim();
          		      var satHrs = $('#input_sat_'+res[4]).val().trim();
          		            
          	              
          		      if(sunHrs != '' || monHrs != '' || tueHrs != '' || wedHrs != '' || thuHrs != '' || friHrs != '' || satHrs != '') {           
          	         
          		    	  taskHrs.push(projTaskId+"#"+projId+"#"+sunHrs+"#"+monHrs+"#"+tueHrs+"#"+wedHrs+"#"+thuHrs+"#"+friHrs+"#"+satHrs);
          		      } 
          			  
          		});
          		if(taskHrs.length > 0) {
          			$.ajax({
          				url: url,
          				data: 'data='+taskHrs+'&selYrMon='+selYrMon+'&calWeek='+calWeek+'&week='+weekNo
          						+'&call=ajaxcall'+'&weekStart='+weekStart+'&weekEnd='+weekEnd
          						+'&sun_note='+sun_note+'&mon_note='+mon_note+'&tue_note='+tue_note
          						+'&wed_note='+wed_note+'&thu_note='+thu_note+'&fri_note='+fri_note+'&sat_note='+sat_note+'&week_note='+week_note,
          				type: 'POST',		
          				success: function(response) {			 
          					  if(response.status == 'success'){
          					  	
          						  var flag = response.status; 
          						  
          					//	 $("#error_message").html('<div id="messageData" >'+ response.message + '</div>');
          					//	if(!$('#error_message').is(':visible'))
          			    	//	{
          							//$('#error_message').css('display', 'block'); 
                        $("#error_message").show(); 
                        $('#error_message').empty();	         			    		
          							$("#error_message").html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>' + response.message + '</div></div>');
          							setTimeout(function() {
          								$('#error_message').fadeIn('slow').fadeOut(function() {
          						        location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";		
          									});
          							}, 3000);
          			    	//	}
          						//location.href = base_url + module_name + "/index/week?selYrMon="+selYrMon+"&week="+weekNo+"&calWeek="+calWeek+"&flag=time";
          						
          					  }else
							  {
								  $("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>Date of joining should be greater than current date</div></div>');
								    setTimeout(function() {
          								$('#error_message').fadeIn('slow').fadeOut(function() {
          						        location.href = base_url + "/timeentry/"+selYrMon+"/"+weekNo+"/"+calWeek+"/time";		
          									});
          							}, 3000);
							  }
          				}
          		    });
          		}
          	}
	} else {
  
       // $("#error_message").html('');
     		var message = "Enter time to save timesheet for this week.";
    		if(!$('#error_message').is(':visible'))
    		{
    			//console.log("not visible");
    		//	$('#error_message').css('display', 'block');
    		  $("#error_message").show(); 
          $('#error_message').empty();	    
    			$("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>' + message + '</div></div>');
    			 setTimeout(function() {
    					$('#error_message').fadeOut('slow');
    				}, 10000);
    		}
//    		else {
//    		$("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>' + message + '</div></div>');
//    		 setTimeout(function() {
//    				$('#error_message').fadeOut('slow');
//    			}, 5000);
//    		}
  }
	
}
function refreshgrid(objname, dashboardcall,projectId,otherAction,start_date,end_date,emp_id) {
	var Url = "";
	var context = "";
	var formGridId = $("#formGridId").val();
	var unitId = '';
	var module_name = '/timemanagement';
	mname = '';
	mnuid = '';
	$('#columnId').remove();
	if (formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
		formGridId = '';
	else {
		unitId = formGridId.split("/");
		mname = unitId[0];
		mnuid = unitId[1];
	} 

  //Clearing the params for pdf and excel generation
  $('#sort_order').val(''); 
  $('#sort_by').val(''); 
  $('#page_no').val(''); 
  $('#per_page').val(''); 
	//var url = document.URL.split('/');
	var dataparam = 'objname=' + objname + '&refresh=refresh&call=ajaxcall' + '&' + mname + '=' + mnuid + "&context=" + context + "&dashboardcall=" + dashboardcall;

	if(projectId != '')
		dataparam = dataparam+ "&projectId=" + projectId;
	
	if(otherAction != '' && otherAction == "employeereports")
		dataparam = dataparam +'&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && otherAction == "projectreports")
		dataparam = dataparam + '&emp_id=' + emp_id + '&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && (otherAction == "viewexpensereports" || otherAction == "expensereports"))
		dataparam = dataparam +'&start_date=' + start_date + '&end_date=' + end_date;
	
	Url = base_url + module_name + "/" + objname + "/"+otherAction+"/format/html";
	$("#" + objname + "_searchdata").val(''); 
	$.ajax({
		url: Url,
		type: 'POST',
		data: dataparam,
		success: function(response) {
			$('#grid_' + objname).html(response);
			if(otherAction == "expensereports"){
				expenseReportsSuccess();
			}
			//$('#gridblock').html(response);
		}
	});
}

function opensearch(objname,projectId,otherAction,start_date,end_date,emp_id) {
	var dashboardcall = $("#dashboardcall").val();
	if ($(".searchtxtbox_" + objname).is(":visible")) {
		$('.ui-datepicker-trigger').hide();
		$(".searchtxtbox_" + objname).hide();
		$("#search_tr_" + objname).hide();
		refreshgrid(objname, dashboardcall,projectId,otherAction,start_date,end_date,emp_id);
	} else {
		$('.ui-datepicker-trigger').show();
		$(".searchtxtbox_" + objname).show();
		$("#search_tr_" + objname).show();
	}
}

function getsearchdata(objname, conText, colname, event, etype,projectId,otherAction,start_date,end_date,emp_id) { //alert(otherAction);
	// alert(objname,conText,colname,event,etype);
	var module_name = '/timemanagement';
	if (etype == 'text') {
		var code = event.keyCode || event.which;
		if (code != 13) {
			return;
		}
	}
	var dashboardcall = $("#dashboardcall").val();
	var Url = "";
	var perpage = $("#perpage_" + objname).val();
	if (perpage == 'undefined' || typeof(perpage) === 'undefined') {
		if (dashboardcall == 'Yes')
			perpage = '10';
		else
			perpage = '20';
	}
	var page = $(".gotopage_input_" + objname).val();
	var formGridId = $("#formGridId").val();
	var unitId = '';
	var mname = '';
	var mnuid = '';
	var columnid = '';
	if (formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
		formGridId = '';
	else {
		unitId = formGridId.split("/");
		mname = unitId[0];
		mnuid = unitId[1];
	}
	var searchData = '{';
	$('.searchtxtbox_' + objname).each(function() {
		if (this.value != '') {
			searchData += '"' + this.id + '":"' + encodeURIComponent(this.value) + '",';
			if (columnid == '')
				columnid = colname;
		}
	});
	searchData = searchData.substr(0, (searchData.length - 1));
	if (searchData != '' && searchData != 'undefined')
		searchData += '}';
	if (page == '' || page == 'undefined' || typeof(page) === 'undefined')
		page = $(".currentpage").val();
	page = 1;
	var dataparam = 'per_page=' + perpage + '&page=' + page + '&call=ajaxcall&objname=' + objname + '&' + mname + '=' + mnuid + '&context=' + conText + '&dashboardcall=' + dashboardcall;
	if (searchData != '' && searchData != '{}')
		dataparam = dataparam + '&searchData=' + searchData;
	if (objname == 'servicerequests') {
		var v_val = $('#service_grid_status').val();
		if (typeof(v_val) == 'undefined')
			v_val = '';
		dataparam = dataparam + '&t=' + $('#service_grid').val() + '&v=' + v_val;
	}
	$('#' + objname + '_searchdata').remove();
	$('#objectName').remove();
	$('#footer').append("<input type='hidden' value='" + searchData + "' id='" + objname + "_searchdata' />");
	$('#footer').append('<input type="hidden" value="' + objname + '" id="objectName" />');
	if ($("#columnId").length)
		$('#columnId').val(columnid);
	else $('#footer').append('<input type="hidden" value="' + columnid + '" id="columnId" />');
	
	if (otherAction == '' || otherAction == 'undefined' || typeof(otherAction) === 'undefined')
		otherAction = 'index';
	Url = base_url + module_name + "/" + objname + "/"+otherAction+"/format/html";
	
	if(projectId != '')
		dataparam = dataparam + '&projectId=' + projectId ;
	
	if(otherAction != '' && otherAction == "employeereports")
		dataparam = dataparam + '&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && otherAction == "projectreports")
		dataparam = dataparam + '&emp_id=' + emp_id + '&start_date=' + start_date + '&end_date=' + end_date;
	$('#search_data_pdf').val(searchData); 

	$.ajax({
		url: Url,
		type: 'POST',
		data: dataparam,
		success: function(response) {
			$('#grid_' + objname).html(response); 
		}
	});

}

function changestatus(controllername, objid, flag) {
	var deleteflag = $("#viewval").val();
	var flagAr = flag.split("@#$");
	var i;
	var msgdta = ' ';
	var module_name = '/timemanagement';
	for (i = 0; i < flagAr.length; i++) {
		msgdta += flagAr[i] + ' ';
	}

	mdgdta = $.trim(msgdta);

	var messageAlert = 'Are you sure you want to delete the selected ' + mdgdta + '? ';
	jConfirm(messageAlert, "Delete " + msgdta, function(r) {

		if (r == true) {
			$.ajax({
				url: base_url + module_name + "/" + controllername + "/delete",
				type: 'POST',
				data: 'objid=' + objid+'&deleteflag='+deleteflag,
				beforeSend: function() {
					// $.blockUI({ width:'50px',message:
					// $("#spinner").html() });
				},
				dataType: 'json',
				success: function(response) { 
					successmessage_changestatus(response['message'], response['msgtype'], controllername);
					if ($.trim(response['flagtype']) == 'sd_request')
						window.location = base_url + module_name + "/" + controllername;
					else{
						if(deleteflag==1)
							redirecttocontroller(controllername);
						else
							getAjaxgridData(controllername,'','','index');
					}
						
				}
			});
		} else {

		}
	});

}
function redirecttocontroller(controllername)
{
	var module_name = '/timemanagement';
	$.blockUI({ width:'50px',message: $("#spinner").html() });
  window.location.href =base_url + module_name + "/" + controllername;
}
function getAjaxgridData(objname, dashboardcall,projectId,otherAction,start_date,end_date,emp_id) {
	var perpage = $("#perpage_" + objname).val();
	var page = $(".gotopage_input_" + objname).val();
	var sort = $("#sortval_" + objname).val();
	var by = $("#byval_" + objname).val();
  //assigning values to hidden variables for pdf download
  $('#sort_order').val(sort); 
  $('#sort_by').val(by); 
  $('#page_no').val(page); 
  $('#per_page').val(perpage); 
	var searchData = $("#" + objname + "_searchdata").val();
	searchData = decodeURIComponent(searchData);
	var formGridId = $("#formGridId").val();
	var unitId = '';
	var module_name = '/timemanagement'; 
	if(otherAction == '' || otherAction == 'undefined' || typeof(otherAction) === 'undefined'){
		otherAction = 'index';
	} 
	var url =  base_url + module_name + "/" + objname + "/"+otherAction;
	mname = '';
	mnuid = '';
	if (formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
		formGridId = '';
	else {
		unitId = formGridId.split("/");
		mname = unitId[0];
		mnuid = unitId[1];

	}
	if (page == '' || page == 'undefined' || typeof(page) === 'undefined')
		page = $(".currentpage").val();

	var dataparam = 'per_page=' + perpage + '&page=' + page + '&call=ajaxcall&objname=' + objname + '&' + mname + '=' + mnuid + '&dashboardcall=' + dashboardcall + '&sort=' + sort + '&by=' + by;
	
	if(projectId != '')
	dataparam = dataparam + '&projectId=' + projectId;
	
	if(otherAction != '' && otherAction == "employeereports")
		dataparam = dataparam + '&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && otherAction == "projectreports")
		dataparam = dataparam + '&emp_id=' + emp_id + '&start_date=' + start_date + '&end_date=' + end_date;
	
	
	if (searchData != '' && searchData != 'undefined')
		dataparam = dataparam + '&searchData=' + searchData;

	$('#' + objname + '_searchdata').remove();
	$('#footer').append("<input type='hidden' value='" + searchData + "' id='" + objname + "_searchdata' />");
	$('#footer').append('<input type="hidden" value="' + objname + '" id="objectName" />');
	if(otherAction == "projectsreports" || otherAction == "employeereports")
	{
		url =  url +"/format/html";
	}
	
	$.ajax({
		url: url,
		type: 'POST',
		data: dataparam,
		success: function(response) {
			$('#grid_' + objname).html(response);

		}
	});

}

function successmessage_changestatus(message, flag, controllername) {
	var eleId = 'error_message_' + controllername;
	$("#error_message").css('display', 'block');
	if ($("#" + eleId).length == 0) {
		$("#error_message").attr("id", "error_message_" + controllername);
		$("#error_message_" + controllername).css('display', 'block');
	} else {
		$("#error_message_" + controllername).css('display', 'block');
	}
	$("#error_message_" + controllername).html('<div id="messageData" class="ml-alert-1-' + flag + '"><div style="display:block;"><span class="style-1-icon ' + flag + '"></span>' + message + '</div></div>');
	setTimeout(function() {
		$('#error_message_' + controllername).fadeOut('slow');
	}, 3000);
}

function paginationndsorting(url,projectId,otherAction,start_date,end_date,emp_id) {
  // alert(url);
	var myarr = url.split("/");

  //code to get the params for pdf generation
  //start
  var sort_order = '';
  var sort_by = '';
  var page_no = '';
  var per_page = '';
  for (var i = 0; i < myarr.length; i++) 
  {
    if(myarr[i] == 'sort')
    {
      sort_order = myarr[i+1];
    }    
    if(myarr[i] == 'by')
    {
      sort_by = myarr[i+1];
    }    
    if(myarr[i] == 'page')
    {
      page_no = myarr[i+1];
    }    
    if(myarr[i] == 'per_page')
    {
      per_page = myarr[i+1];
    }
  }
  $('#sort_order').val(sort_order); 
  $('#sort_by').val(sort_by); 
  $('#page_no').val(page_no); 
  $('#per_page').val(per_page); 
  //end

	if (url.indexOf('/call/ajaxcall') == -1)
		url = url + '/call/ajaxcall';
	var dashboardcall = $("#dashboardcall").val();
	if (url.indexOf("objname") != -1) {
		divid = url.match(/objname\/(.*?)\//i)[1];
	}

	if (url.indexOf("sort") != -1) {
		var strSortParam = url.substring(url.lastIndexOf('sort') + 5);

		var sortOrder = strSortParam.substring(0, strSortParam.lastIndexOf('by') - 1);

		var sortBy = strSortParam.substring(strSortParam.lastIndexOf('by') + 3);

		$('#sort_param').val(sortBy + "/" + sortOrder); 
	}
	var searchData = $("#" + divid + "_searchdata").val();
	var perfTimes = $("#gridblock *").serialize();
	searchData = decodeURIComponent(searchData);
	
	$.post(url, {
		searchData: searchData,
		dashboardcall: dashboardcall,
		projectId:projectId,
		otherAction:otherAction,
		start_date:start_date,
		end_date:end_date,
		emp_id:emp_id
	}, function(response) {
		$('#grid_' + divid).html(response);
	}, 'html');
}

function selectrow(objid,tr)
{
	var row = $(tr);      
	if(!row.hasClass('newclass')){
			row.addClass('newclass')       
				.siblings()                
				.removeClass('newclass');  
	}
}

function changeeditscreen(controllername,id)
{
	var module_name = '/timemanagement';
	  $.blockUI({ width:'50px',message: $("#spinner").html() });	
	  window.location.href = base_url + module_name +'/'+controllername+'/edit/id/'+id;
}

function getStates()
{
  var cnval=$('#country_id').val();
  $.get(base_url+'/timemanagement/index/getstates/cnval/'+cnval,function(data){
      $('#state_id').find('option').remove();
      $('#state_id_text').val('');
      $('#state_id').append(data.options);
       //$('#state_id').trigger("liszt:updated");
  },'json');
  
}

function changeprojecteditscreen(controllername,id)
{
	$.blockUI({ width:'50px',message: $("#spinner").html() });
	if(controllername == 'projects')
	window.location.href = base_url+'/timemanagement/'+controllername+'/edit/id/'+id;
}

function gobacktoprojects(){
   window.location.href = base_url+'/timemanagement/projects';
}

function displaydeptform(url,menuname)
{
	var urlArr = url.split('/');   
	if(menuname == 'Currency'){
	   var tmUrl = base_url;
	}
	else{
	   var tmUrl = base_url+'/timemanagement';
	}
	var baseurlArr = tmUrl.split('/');
	var request_hostname = window.location.hostname;
	var flag = 'yes';
	var urlsplitArr = url.split("/");
	var controllername = urlArr[baseurlArr.length]; 
	if(flag == 'yes'){
		$("body").append('<div id="blockingdiv" class="ui-widget-overlay ui-front"></div>');
		
		var capitalizedtitle = '';
		if(menuname !='')
		{
		  capitalizedtitle = menuname;
		}			
		$(".closeAttachPopup").remove();
								//alert(controllername);
		window.parent.$('#'+controllername+'Container').dialog({open:function(){
					$('#'+controllername+'Container').css('display','block');
		            $('#'+controllername+'Cont').attr('src', url);
					$(document).bind('keydown',function(e) {
						
						if (e.keyCode === 8) {
							return false;
						};
					});														  
													},
							close: function() {
							 $('#blockingdiv').remove();
							 $('#'+controllername+'Cont').attr('src', '');
							 $(document).unbind('keydown');
							},
							title: capitalizedtitle,
							height:'auto',
							draggable:false,
							width: 780
							});
	}
}

function closeiframeAddPopup(addpopupdata,controllername,con,textstr)
{    
	var option = '';
	if(textstr != '')
	{           
                var defOption = "<option value=''>Select "+textstr+"</option>";		
		window.parent.$('#s2id_'+con+' .select2-choice span').html('Select '+textstr);
            
	}else{
		
            if(con != 'bg_checktype')
            {
                var defOption = "<option value=''> </option>";
                window.parent.$('#s2id_'+con+' .select2-choice span').html('');
            }
	}
	if(con == 'country' || con == 'country_1' || con == 'state' || con == 'state_1')
	{
		removeselectoptions(con);
	}
	window.parent.$("#"+con).find('option').remove();
	window.parent.$("#"+con).parent().find('.select2-container').find('.select2-search-choice').remove();
	window.parent.$("#"+con).html(defOption+addpopupdata);
	if($('#'+controllername+'Container', window.parent.document).html() !='null')
	{
		window.parent.$('#'+controllername+'Container').dialog('close');
		window.parent.$('#errors-'+con).remove();
	}
}

function from_to_date_validation(from_date_id,to_date_id,obj,message)
{
   var obj_id = $(obj).prop('id');	
   var from_val = $('#'+from_date_id).val();
   var to_val = $('#'+to_date_id).val();
   $("#errors-"+obj_id).remove();
 
    if(from_val != '' && to_val != '')
    {
        $.post(base_url+"/index/fromdatetodate",{from_val:from_val,to_val:to_val},function(data){
                if(data.result == 'no')
                {
                   $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>"+message+"</span>");
                    $('#'+obj_id).val('');
                }
        },'json');
        
    }
    else
    {
            $('#'+obj_id).trigger('blur');
    }
}

function only_number(evt)
{
    var key=evt.keyCode ? evt.keyCode : evt.charCode;
    key=parseFloat(key);
    //console.log(key);
    if((key>=48 && key<=57)  || key==8 || key==127 || key==0 || key==37 || key==39 || key==9 || key==46)
        return true;
    else 
    {
        if(evt.preventDefault)    
            evt.preventDefault();
        else
            evt.returnValue=false;
    }
}

/*add tasks pop up*/
function addtasks(projectId)
{
    if($('#fieldchange').val() == 'changed'){
	   /*var messageAlert = 'Click on Update to save the data before adding a new task. ';
	   jConfirm(messageAlert, "Add Task", function(r) {
			if (r == true) {
				openTaskPopup(projectId);
			} else {
			}
	    });*/
		
		jAlert('Click on Update to save the data before adding a new task.');
		return false;
		
	}else{
	   openTaskPopup(projectId);
	}
}

function  openTaskPopup(projectId){
    var myPos = [ $(window).width() / 5, 150 ];
    $("#idaddtasks").dialog({
        //draggable:false, 
        //resizable: true,
        title:'Add Tasks',
		position: myPos,
        modal: true, 
        buttons : [
        {text:"Add",click : function() {	
             var type=$('.rad_tasks:checked').val();
             var ret='';
             ret= sub_addtasks(type,projectId);   
             if(ret)
             {
			 
                 $('#project_add').show();
                 $(this).dialog("destroy");
             }
        }},
        {"class":'cancel_dialog',text:'Cancel', click: function() {                
                $(this).dialog("destroy");
                }
        }
      ],
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
          $('#idradtasktype_new').prop('checked',true);
          addtasks_content('new','idtaskcontent');
      }
    });
}

/*add tasks popup content*/
function addtasks_content(type,content_id)
{
	$.ajax({
		url: base_url + "/timemanagement/projects/addtasksproject/format/html",
		data: 'type=' + type,
		dataType: 'html',
		success: function(response) {
			  $('#'+content_id).html(response);
		}
	});
}

/*radio button click in add tasks popup*/
function radiotask_onclick(obj)
{
    var type=obj.value;
    addtasks_content(type,'idtaskcontent');
}
/*saving tasks from add task popup*/
function sub_addtasks(type,projectId)
{
    var tbl=$('#idaddtaskstbl');
    if(type=='new')
    {
	    var task_name=$('#idtxttask').val();
        if(task_name!='')
        {            
		    $.ajax({
				url: base_url + "/timemanagement/defaulttasks/checkduptask/format/json",
				data: 'taskname=' + task_name,
				type: 'POST',
				dataType: 'json',
				success: function(response) { 
					if(response.result == 'exists'){ 
						 $('#idtxttask').parent().parent().find('.task_errors').remove();
						 $('#idtxttask').parent().parent().append("<span class='task_errors new_task_error'>This task already exists.</span>"); 
						 return false;
					}
					else if(response.result == 'notexists'){
						var def_check=$('#addtodefault').prop( "checked" ) ; 
						$.ajax({
							url: base_url + "/timemanagement/projects/addtasks/format/html",
							data: 'type=' + type+'&task_name=' + task_name+'&def_check=' + def_check+'&projectId=' + projectId,
							type: 'POST',
							dataType: 'html',
							success: function(response) {
								  $('#first_task_add').hide();
								  $('#task_submit').show();
								  $('#tasksearch_div').show();					  
								  $('#task_content_div').show();
							  
								  $('#task_content_div').html(response);
								  $('#idaddtasks').dialog("destroy");
								  $('#task_added').show();
								  $('#task_add_msg').empty();
								  $('#task_add_msg').append("<span class='style-1-icon success'></span>Task(s) added successfully.");
								  setTimeout(function(){
										$('#task_added').fadeOut('slow');
									},3000);
							}
						});
					}
				}
			});  
        }
        else 
        { 
		   $('#idtxttask').parent().parent().find('.task_errors').remove();
		   $('#idtxttask').parent().parent().append("<span class='task_errors new_task_error'>Please enter task name.</span>"); 
		   return false;
        }    
    }
    else if(type=='default')
    {
        var chk_cnt=$('.clschk_default_tasks:checked').length;
        if(chk_cnt>0)
        {
		    jsonObj = [];
            $('.clschk_default_tasks:checked').each(function(){
                var task_id=$(this).val();
                var task_name=$('#idtd_deftask'+task_id).html();
              
			    jsonObj.push(task_id);
            });
			
			jsonString = JSON.stringify(jsonObj);
				
			$.ajax({
				url: base_url + "/timemanagement/projects/addtasks/format/html",
				data: 'type=' + type+'&taskids=' + jsonString+'&projectId=' + projectId,
				type: 'POST',
				dataType: 'html',
				success: function(response) {
				        $('#first_task_add').hide();
					    $('#task_submit').show();
					    $('#tasksearch_div').show();					  
					    $('#task_content_div').show();
						
						$('#task_content_div').html(response);
						$('#idaddtasks').dialog("destroy");
						$('#task_added').show();
						$('#task_add_msg').empty();
						$('#task_add_msg').append("<span class='style-1-icon success'></span>Task(s) added successfully.");
						setTimeout(function(){
							$('#task_added').fadeOut('slow');
						},3000);
				}
			});
        }
        else 
        {
            $('#iddeferror').find('.task_errors').remove();
            $('#iddeferror').append("<span class='task_errors'>Please select any one task.</span>"); 
            return false;
        }
            
    }
	 else if(type=='most')
    {
        var chk_cnt=$('.clschk_most_tasks:checked').length;
        if(chk_cnt>0)
        {
		    jsonObj = [];
            $('.clschk_most_tasks:checked').each(function(){
                var task_id=$(this).val();
                var task_name=$('#idtd_mosttask'+task_id).html();
              
			    jsonObj.push(task_id);
            });
			
			jsonString = JSON.stringify(jsonObj); 
				
			$.ajax({
				url: base_url + "/timemanagement/projects/addtasks/format/html",
				data: 'type=' + type+'&taskids=' + jsonString+'&projectId=' + projectId,
				type: 'POST',
				dataType: 'html',
				success: function(response) {
				        $('#first_task_add').hide();
					    $('#task_submit').show();
					    $('#tasksearch_div').show();					  
					    $('#task_content_div').show();
						
						$('#task_content_div').html(response);
						$('#idaddtasks').dialog("destroy");
						$('#task_added').show();
						$('#task_add_msg').empty();
						$('#task_add_msg').append("<span class='style-1-icon success'></span>Task(s) added successfully.");
						setTimeout(function(){
							$('#task_added').fadeOut('slow');
						},3000);
				}
			});
        }
        else 
        {
            $('#iddeferror').find('.task_errors').remove();
            $('#iddeferror').append("<span class='task_errors'>Please select any one task.</span>"); 
            return false;
        }
            
    }
}

function p_code(evt)
{
    $('#idtxttask').parent().parent().find('.task_errors').remove();
    var key=evt.keyCode ? evt.keyCode : evt.charCode;
    key=parseFloat(key);
    //alert(key);
    if((key>=65 && key<=90) || (key>=97 && key<=122) || (key>=48 && key<=57) || key==95 || 
            key==32 || key==8 || key==127 || key==0 || key==37 || key==39 || key==9 || key==46 || key==36)
        return true;
     else 
    {
        if(evt.preventDefault)    
            evt.preventDefault();
        else
            evt.returnValue=false;
   
    }
}

/*add resources pop up*/
function addresource(projectId)
{ 
    if($('#fieldchange').val() == 'changed'){
	  /* var messageAlert = 'Click on Update to save the data before adding a new resource.';
	   jConfirm(messageAlert, "Add Resource", function(r) {
			if (r == true) {
				openResourcePopup(projectId);
			} else {
			}
	    });*/
		jAlert('Click on Update to save the data before adding a new resource.');
		return false;
		
	}else{
	   openResourcePopup(projectId);
	}
}
function openResourcePopup(projectId){
    var myPos = [ $(window).width() / 5, 150 ];
		$("#idaddresources").dialog({
			//draggable:false, 
			//resizable: true,
			title:'Add Resources',
			position: myPos,
			modal: true, 
			buttons : [
			{text:"Add to Project",click : function() {	//alert($('#existetd_mem_str').val());
				 var type=$('.rad_resource:checked').val();
				 var projectres=$('#existetd_mem_str').val();
				 var ret='';
				 ret= saveResourceToProject(type,projectId,projectres);   
				 if(ret)
				 {
					 $(this).dialog("destroy");
				 }
			}},
			{"class":'cancel_dialog',text:'Cancel', click: function() {                
					$(this).dialog("destroy");
					}
			}
		  ],
		  close:function()
		  {          
			  $(this).dialog("destroy");
		  },
		  open:function()
		  { 
			  $('.ui-widget-overlay').addClass('ui-front-overwrite');
			  $('.ui-dialog').removeClass('ui-dialog-buttons');
			  $('.ui-dialog').removeClass('ui-front');
			  $('.ui-dialog').addClass('ui-btn-overwrite');
			  $('#idradresourcetype_manager').prop('checked',true);
			  addresources_content('manager','idresourcecontent',projectId);
		  }
		});
}

/*radio button click in add resource popup*/
function radioresource_onclick(obj,projectId)
{
    var type=obj.value;
    addresources_content(type,'idresourcecontent',projectId);
}

/*add resource popup content*/
function addresources_content(type,content_id,projectId)
{
    var addedempstr = mngr = projLead= data = '';
    if(type == 'manager'){
	   addedempstr = $('#added_mngr_emp_str').val(); 
	   data = 'type=' + type + '&projectId=' + projectId+'&addedempstr='+addedempstr;
	}
	if(type == 'emp'){
	   mngr = $('#added_mngr_emp_str').val();
	   projLead = $('#added_lead_emp_str').val();
	   addedempstr = $('#added_emp_str').val();
	  // if($.trim(mngr) != ''){
	      data = 'type=' + type + '&projectId=' + projectId+'&addedempstr='+addedempstr+'&mngrstr='+mngr;
	   /*}else{
		   $('.errors').find('.res_errors').remove();
		   $('.errors').append("<span class='res_errors'>Please first select manager.</span>"); 
		   return false;
	   }*/
	   data = 'type=' + type + '&projectId=' + projectId+'&addedempstr='+addedempstr+'&mngrstr='+mngr+'&leadstr='+projLead;
	}//alert(addedempstr);
	$.ajax({
		url: base_url + "/timemanagement/projectresources/addresourcesproject/format/html",
		data: data,
		dataType: 'html',
		success: function(response) {
			  $('#'+content_id).html(response);
		}
	});
}

/*saving tasks from add resource popup*/
function saveResourceToProject(type,projectId,projRes){
	if(projRes!='')
	{            
		$.ajax({
			url: base_url + "/timemanagement/projectresources/addresources/format/html",
			data: 'type=' + type + '&projectId=' + projectId+'&projRes='+projRes,
			type: 'POST',
			dataType: 'html',
			success: function(response) { 
			      $('#first_res_add').hide();
				  $('.role_drop_down').show();
				  $('.search_emp_by_name_div').show();
				  $('#resource_content_div').show();
				  $('#res_submit').show();
				  
				  $('#resource_content_div').html(response);
				  $('#idaddresources').dialog("destroy");
				  
				  $('#task_added').show();
				  $('#task_added').html("<span class='style-1-icon success'></span>Resource(s) added successfully.");
				  $('#task_added').show();
				  setTimeout(function(){
					 $('#task_added').fadeOut('slow');
				  },3000);
			}
	    });
	}
	else 
	{ 
	   $('.errors').find('.res_errors').remove();
	   if(type == 'emp'){
	      type = 'employee';
	   }
	   $('.errors').append("<span class='res_errors' ><div class='ml-alert-1-error' id='messageData' style='margin-top: 0px; margin-bottom: 0px;'><span class='style-1-icon error'></span>Please select "+type+".</div></span>"); 
	   return false;
	}    
}

function fnAddRemoveProjectUser(addremove,userId,projectId,userName,imgName,userCmpId,userDesign)
{
	/* if(userId != '' && projectId != '') 
	{ */
	if(userId != '')
	{  
         var selectedRadioType = 'Resources';   
         var spantmRole = ''; 		 
		 if($('#idradresourcetype_manager').length > 0 && $('#idradresourcetype_manager').prop("checked")){
			 selectedRadioType = 'Managers';
		 }else if($('#idradresourcetype_emp').length > 0 && $('#idradresourcetype_emp').prop("checked")){
			 selectedRadioType = 'Employees';
		 }
		 
		 if($('#tm_role'+userId).length > 0 && $('#tm_role'+userId).val() == 'Manager'){
			 spantmRole = '<span class="role_disp manager_role">M</span>';
		 }
		//Removed added or removed User Div. If addremove is 0->Delete 1->Add
		if(addremove == 1)
		{
			//To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)
			
			if ($(".users_left_list_div.users_left_list").length == 1) 
			{
				if($(".no_left_data_found").length < 1)
				{
					var no_user_data_div = '<div class="users_left_list_div no_left_data_found" style="display:none;"><span class="values">'+selectedRadioType+' are not available.</span> </div>'
					
					$(".users_left_list_div:first").before(no_user_data_div);	
				}
			}
			else
			{
				$(".no_left_data_found").remove();
			}
			//End
			
			//Remove Current Div
			$(".user_div_"+userId).remove();
			//End
			
			//To check whether no user exists div exists(If exists then display block div of no user exists)				
			if ($(".no_left_data_found").length > 0) 
			{
				$(".no_left_data_found").show();
			}
			
			 
			var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(0,\''+userId+'\',\''+projectId+'\',\''+addslashes(userName)+'\',\''+imgName+'\',\''+addslashes(userCmpId)+'\',\''+addslashes(userDesign)+'\');" style="cursor:pointer;" class="search_right users_right_list_div users_right_list user_div_'+userId+'" alt="Remove" title="Remove" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+domain_data+'/public/media/images/default-profile-pic.jpg\'" src="'+domain_data+'/public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span>'+spantmRole+'<span class="empuid member_id">'+userCmpId+'</span><span class="emprole member_jname">'+userDesign+'</span></div>';
			
			if ($(".users_right_list_div").length > 0) 
			{
				$(".users_right_list_div:first").before(newDivToAppend);
			}
			
			$(".no_right_data_found").hide();			
			
			$(".no_added_user_found").hide();
			
		}
		else if(addremove == 0)
		{		
			//alert($(".users_right_list_div.users_right_list").length);
			//To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)
			
			if ($(".search_right").length == 1) 
			{
				if($(".no_right_data_found").length < 1)
				{
					var no_user_data_div = '<div class="users_right_list_div no_right_data_found" style="display:none;"><span class="values">'+selectedRadioType+' are not available.</span> </div>'
					
					$(".users_right_list_div:first").before(no_user_data_div);
				}
			}
			else
			{
				$(".no_right_data_found").remove();
			}
			//End
			
			//Remove Current Div
			$(".user_div_"+userId).remove();
			//End
			//alert($(".no_right_data_found").length);
			//To check whether no user exists div exists(If exists then display block div of no user exists)				
			if ($(".no_right_data_found").length > 0) 
			{
				$(".no_right_data_found").show();
				
				if($('.no_added_user_found').length > 0){
				   $(".no_added_user_found").show();
				}
			}
			//End				
		
			$(".no_search_results").hide();
			
			if($('#tm_role'+userId).length > 0 && $('#tm_role'+userId).val() == 'Manager'){
			    spantmRole = '<span class="role_disp manager_role">M</span>';
		    }
			
			var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(1,\''+userId+'\',\''+projectId+'\',\''+addslashes(userName)+'\',\''+imgName+'\',\''+addslashes(userCmpId)+'\',\''+addslashes(userDesign)+'\');" style="cursor:pointer;" class="users_left_list_div users_left_list user_div_'+userId+'" alt="Add" title="Add" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+domain_data+'/public/media/images/default-profile-pic.jpg\'" src="'+domain_data+'/public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span>'+spantmRole+'<span class="empuid member_id">'+userCmpId+'</span><span class="emprole member_jname">'+userDesign+'</span></div>';
			
			if ($(".users_left_list_div").length > 0) 
			{
				$(".users_left_list_div:first").before(newDivToAppend);
			}
			
			$(".no_left_data_found").hide();
		}
	
	
	
	
		var ids_data = $("#existetd_mem_str" ).val();
		if(ids_data != '')
		{
			var ids_arr = ids_data.split(',');
			
			var isExist = $.inArray(userId, ids_arr); 
			if(isExist == -1)
			{
				ids_data = ids_data+','+userId;
				$("#existetd_mem_str").val(ids_data);
			}
			else
			{
				ids_arr = $.grep(ids_arr, function(value) {
					return value != userId;
				}); 
				
				 var ids_arr_to_string = ids_arr.join(",");				 
				 $("#existetd_mem_str").val(ids_arr_to_string);
			}
		}
		else
		{
			ids_data = userId;
			$("#existetd_mem_str").val(ids_data);
		}
		
		//alert($("#existetd_mem_str").val());
		$("#search_emp_by_name").val('');
		$('div.users_left_list').show();
		$('#idclear').hide();

		$('#actionButtonsDiv').show();
	}
}

function addslashes (str) {
	  return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}

/*view resource task popup*/
function viewTasks(projectId,projectResourceId){
     var myPos = [ $(window).width() / 5, 150 ];
    $("#idviewTasks").dialog({
        title:'View Employee Task',
		position: myPos,
        modal: true, 
      
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
          viewEmpTask(projectId,projectResourceId,'idempviewtaskcontent');
      }
    });
}
/*view tasks for a resource in project popup content*/
function viewEmpTask(projectId,projectResourceId,content_id)
{
	$.ajax({
		url: base_url + "/timemanagement/projectresources/viewemptasks/format/html",
		data: 'projectId=' + projectId + '&projectResourceId=' + projectResourceId,
		dataType: 'html',
		success: function(response) {
			  $('#'+content_id).html(response);
		}
	});
}

function check_quoted_hrs(selobj)
{
    var act_quot=$('#project_estimated_hrs').val();
    act_quot=act_quot.split(':');
    if(act_quot[1]=='' || act_quot[1]==null)
        act_quot[1]=0;
    act_quot=(parseFloat(act_quot[0])*60)+parseFloat(act_quot[1]);
   
    if(act_quot!='')
    {
        var tot=0;
        $('.cls_quoted_hrs_est').each(function(){
            var val=$(this).val();
            if(val!='')
            {
                val=val.split(':');
                if(val[1]=='' || val[1]==null)
                    val[1]=0;
                if(parseFloat(val[1])>60)
                {
                    selobj.value='';    
                    selobj.className ='borderclass';
                    $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>Invalid time.</div></div>");
                    $('#iderr_tasks').show();
                    setTimeout(function(){
                         $('#iderr_tasks').fadeOut('slow');
                     },3000);
                    return true; 
                }
                val=(parseFloat(val[0])*60)+parseFloat(val[1]);
                tot+=parseFloat(val);
            }
        });
        if(tot>act_quot)
        {
            selobj.value='';
           $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>Total estimation time is greather than or equal to total task's estimation time.</div></div>");
           $('#iderr_tasks').show();
           setTimeout(function(){
                $('#iderr_tasks').fadeOut('slow');
            },3000);
            
        }    
    }
}

function time_frmat(obj)
{
    var val=obj.value;
    var format = /^([0-9]*\:?[0-9]{1,2})$/;
    if(val !='')
    {
        if (!format.test(val))      
        {
		     obj.value='';
			 $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>Incorrect time format.</div></div>");
             $('#iderr_tasks').show();
             setTimeout(function(){
                     $('#iderr_tasks').fadeOut('slow');
                 },3000);
        }
        else 
            $('#iderr_tasks').html("");
        
    }
}

function dotnumber(evt)
{
    var key=evt.keyCode ? evt.keyCode : evt.charCode;//alert(key)//e.keyCode ? e.keyCode : e.charCode
    key=parseFloat(key);
    //console.log(key);
    if((key>=48 && key<=57) || key==46 || key==8 || key==127 || key==0 || key==37 || key==39 || key==9)
        return true;
    else 
    {
        if(evt.preventDefault)    
            evt.preventDefault();
        else
            evt.returnValue=false;
    }
}

/*View task resources popup*/
function viewResources(projectId,projectTaskId){
 //alert(projectTaskId);
      var myPos = [ $(window).width() / 5, 150 ];
    $("#idviewResources").dialog({
        title:'View Task Resources',
		position: myPos,
        modal: true, 
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
          viewTaskResources(projectId,projectTaskId,'idviewResourcescontent');
      }
    });
}
/*view resources for task popup content*/
function viewTaskResources(projectId,projectTaskId,content_id)
{
	$.ajax({
		url: base_url + "/timemanagement/projecttasks/viewtasksresources/format/html",
		data: 'projectId=' + projectId + '&projectTaskId=' + projectTaskId,
		dataType: 'html',
		success: function(response) {
			  $('#'+content_id).html(response);
		}
	});
}

/*delete task from project function*/
function delProjTask(tableRowId,projectId,taskProjectId,taskCount,taskId){
    var messageAlert = 'Are you sure you want to delete the selected task? ';
	jConfirm(messageAlert, "Delete Task", function(r) {

		if (r == true) {
			$.ajax({
				url: base_url + "/timemanagement/projecttasks/deletetask/format/json",
				data: 'projectId=' + projectId + '&projectTaskId=' + taskProjectId + '&taskId=' + taskId,
				dataType: 'json',
				success: function(response) {
					  //$('#'+content_id).html(response);
					  if(response.status == 'success'){ 
					      if(taskCount == 1){ 
							  $('#first_task_add').show();
							  $('#task_submit').hide();
							  $('#tasksearch_div').hide();					  
							  $('#task_content_div').hide();
						  }
					  
						  $('#'+tableRowId).remove();
						  $('#task_added').show();
					      $('#task_add_msg').empty();
					      $('#task_add_msg').append("<span class='style-1-icon success'></span>"+response.message);
					      setTimeout(function(){
							   $('#task_added').fadeOut('slow');
						  },3000);
					
					  }else{
						   $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>"+response.message+"</div></div>");
						   $('#iderr_tasks').show();
						  setTimeout(function(){
							 $('#iderr_tasks').fadeOut('slow');
						  },3000);
					  }
					   
				}
	        });
		} else {

		}
	});
}

function delProjResource(tableRowId,resourceProjectId,empId,projectId,existedEmpId,resCount){
    var messageAlert = 'Are you sure you want to delete the selected resource? ';
	jConfirm(messageAlert, "Delete Resource", function(r) {

		if (r == true) {
			$.ajax({
				url: base_url + "/timemanagement/projectresources/deleteprojectresource/format/json",
				data: 'projectId=' + projectId +'&resourceProjectId=' + resourceProjectId+ '&empId=' + empId,
				dataType: 'json',
				success: function(response) {
					  //$('#'+content_id).html(response);
					  if(response.status == 'success'){
						  $('#'+tableRowId).remove();
						  
						  var ids_data = $("#"+existedEmpId).val();
						  if(ids_data != '')
						  {
								var ids_arr = ids_data.split(',');
								
								var isExist = $.inArray(empId, ids_arr); 
								if(isExist > -1)
								{
									ids_arr = $.grep(ids_arr, function(value) {
										 return value != empId;
									}); 
									
									var ids_arr_to_string = ids_arr.join(",");				 
									$("#"+existedEmpId).val(ids_arr_to_string);
								}
						  }
		
		                  if(resCount == 1){
								$('#first_res_add').show();
						        $('.role_drop_down').hide();
						        $('.search_emp_by_name_div').hide();
								$('#resource_content_div').hide();
						        $('#res_submit').hide();
						  }
						  $('#task_added').html("<span class='style-1-icon success'></span>"+response.message);
						  $('#task_added').show();
						  setTimeout(function(){
							 $('#task_added').fadeOut('slow');
						  },3000);
					  }else{
						   $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>"+response.message+"</div></div>");
						  $('#iderr_tasks').show();
						  setTimeout(function(){
							 $('#iderr_tasks').fadeOut('slow');
						  },3000);
					  }
					   
				}
	        });
		} else {

		}
	});
}
//assign task to employee
function assignTasks(projectId,employeeId)
{
	 var myPos = [ $(window).width() / 5, 150 ];
    $("#idassignTasks").dialog({
        title:'Assign Task To Resource',
		position: myPos,
        modal: true, 
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
		   $('#idradtasktype_unassigned').prop('checked',true);
          assignTasktoResource('unassigned',projectId,employeeId);
      }
    });
}	

function assignTasktoResource(type,projectId,employeeId)
{
	$.ajax({
		url: base_url + "/timemanagement/projectresources/assigntasktoresources/format/html",
		data: 'projectId=' + projectId + '&employeeId=' + employeeId+'&type='+type,
		dataType: 'html',
		success: function(response) {
			  $('#idassignTaskscontent').html(response);
			   $('#idradtasktype_'+type).prop('checked',true);
			   
			   $('#table_'+type).tablesorter({
				   scrollHeight: 500,
				   widgets: ['zebra','scroller']
				});

			  $('.scroller_tbl').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
		}
	});
}
function showSelectedTask(obj,projectId,employeeId)
{
	var type=obj.value;
	$('#idhidtype').val(type);
	assignTasktoResource(type,projectId,employeeId);
}

//task assignment
function taskAssign(projectId,employeeId)
{
	jsonObj = [];
	jsonid = [];
	$('.unasgnd_cls:checked').each(function(){
		var task_id=$(this).val();
		var project_task_id=$(this).attr('project_task_id');
		jsonObj.push(task_id);
		jsonid.push(project_task_id);
	});
	var selected_tasks = $(".unasgnd_cls:checked").length;
	if(selected_tasks==0)
	{
		jAlert("Please select atleast one checkbox to assigne task .",'Notification');
		return false;
	}
	jsonString = JSON.stringify(jsonObj);
	jsonArray = JSON.stringify(jsonid);
	$.ajax({
		url: base_url + "/timemanagement/projectresources/taskassign/format/json",
		data: 'projectId=' + projectId+'&taskids=' + jsonString+'&employeeId=' + employeeId+'&projecttaskids=' + jsonArray,
		type: 'POST',
		dataType: 'json',
		success: function(response) {
		       $('#idassignTasks').dialog("destroy");
			   $('#task_added').show();
			   $('#task_added').html("<span class='style-1-icon success'></span>"+response.status);
			   $('#task_added').show();
			   setTimeout(function(){
				 $('#task_added').fadeOut('slow');
			   },3000);
		}
	});
	
}
//task delete
function resourceTaskDelete(projectId,employeeId)
{
	jsonObj = [];
	jsonid = [];
	$('.asgnd_cls:checkbox:not(:checked)').each(function(){
		var task_id=$(this).val();
		var project_task_id=$(this).attr('project_task_primary_id');
		jsonObj.push(task_id);
		jsonid.push(project_task_id);
	});
	if(jsonObj=='')
	{
		jAlert("Please uncheck atleast one checkbox to delete task .",'Notification');
		return false;
	}
	jsonString = JSON.stringify(jsonObj);
	jsonArray = JSON.stringify(jsonid);
	$.ajax({
		url: base_url + "/timemanagement/projectresources/resourcetaskdelete/format/json",
		data: 'projectId=' + projectId+'&taskids=' + jsonString+'&employeeId=' + employeeId+'&projecttaskids=' + jsonArray ,
		type: 'POST',
		dataType: 'json',
		success: function(response) {
			  $('#idassignTasks').dialog("destroy");
              $('#task_added').show();
			  $('#task_added').html("<span class='style-1-icon success'></span>"+response.status);
			  $('#task_added').show();
			  setTimeout(function(){
				 $('#task_added').fadeOut('slow');
			  },3000);
		}
	});
}
//task assign/delete at a time
function resourcetaskAssignDelete(projectId,employeeId)
{
	jsonObj = [];
	jsonObject = [];
	$('.asgn_unasgnd_cls:checked').each(function(){
		var task_id=$(this).val();
		jsonObj.push(task_id);
	});
	$('.asgn_unasgnd_cls:checkbox:not(:checked)').each(function(){
		var task_id=$(this).val();
		jsonObject.push(task_id);
	});
	jsonString = JSON.stringify(jsonObj);
	jsonArray = JSON.stringify(jsonObject);
	$.ajax({
		url: base_url + "/timemanagement/projectresources/resourcetaskassigndelete/format/json",
		data: 'projectId=' + projectId+'&chekedtaskids=' + jsonString+'&employeeId=' + employeeId+'&uncheckedtaskids=' + jsonArray ,
		type: 'POST',
		dataType: 'json',
		success: function(response) {
			  $('#idassignTasks').dialog("destroy");
			  $('#task_added').show();
			  $('#task_added').html("<span class='style-1-icon success'></span>"+response.status);
			  $('#task_added').show();
			  setTimeout(function(){
				 $('#task_added').fadeOut('slow');
			  },3000);
		}
	});
}




// assign reources to task
function assignResources(projectId,taskId,projectTaskId){
 var myPos = [ $(window).width() / 5, 150 ];
    $("#idassignResources").dialog({
        title:'Assign Resources To Task',
		position: myPos,
        modal: true, 
		buttons : [
			{text:"+ Assign Resources",click : function() {	//alert($('#existetd_mem_str').val());
				 var oldRes = $('#old_existed_str').val();
				 var newRes = $('#existetd_mem_str').val();
				 var ret='';
				 ret= saveResourceToTask(oldRes,newRes,projectId,taskId,projectTaskId);   
				 if(ret)
				 {
					 $(this).dialog("destroy");
				 }
			}},
			{"class":'cancel_dialog',text:'Cancel', click: function() {                
					$(this).dialog("destroy");
					}
			}
		  ],
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
       		  
		  $.ajax({
				url: base_url + "/timemanagement/projecttasks/assignresourcestotask/format/html",
				data: 'projectId=' + projectId + '&taskId=' + taskId+'&projectTaskId='+projectTaskId,
				dataType: 'html',
				success: function(response) {
					  $('#idassignResourcesContent').html(response);
				}
			});
	
      }
    });
}

/*saving selected resources to project task*/
function saveResourceToTask(oldRes,newRes,projectId,taskId,projectTaskId){
	if(newRes!=oldRes)
	{            
		$.ajax({
			url: base_url + "/timemanagement/projecttasks/saveresources/format/json",
			data: 'oldRes=' + oldRes + '&newRes=' + newRes+'&projectId='+projectId+'&taskId='+taskId+'&projectTaskId='+projectTaskId,
			type: 'POST',
			dataType: 'json',
			success: function(response) { 
				  $('#idassignResources').dialog("destroy");
				  $('#task_added').show();
				  $('#task_add_msg').empty();
				  $('#task_add_msg').append("<span class='style-1-icon success'></span>Resource(s) assigned successfully.");
				  setTimeout(function(){
					$('#task_added').hide();
				  },3000);
			}
	    });
	}
	else 
	{ 
	   $('.errors').find('.res_errors').remove();
	   $('.errors').append("<span class='res_errors'>Please select resources.</span>"); 
	   return false;
	}    
}


function editTaskName(obj,taskId,projectId,projTaskId){
    var taskName = $('#idtxt_taskname'+projTaskId).val();
    if($.trim(taskName) != ''){
		$.ajax({
				url: base_url + "/timemanagement/projecttasks/edittaskname/format/json",
				data: 'projectId='+projectId+'&taskId='+taskId+'&taskName='+taskName,
				type: 'POST',
				dataType: 'json',
				success: function(response) { 
					if(response.status == 'success'){					
					 $('#taskname_edit'+projTaskId).hide();
                     $('#dis_span_'+projTaskId).show();
					 $('#dis_span_'+projTaskId).text(taskName);
					 $('#idtxt_taskname'+projTaskId).val(taskName);
                     $('#idspan_taskname'+projTaskId).show();
		
						$('#task_added').show();
						$('#task_add_msg').empty();
						$('#task_add_msg').append("<span class='style-1-icon success'></span>Task name updated successfully.");
						setTimeout(function(){
						   $('#task_added').hide();
						},3000);
					}else if(response.status == 'error'){
						 $('#iderr_tasks').html("<div class='ml-alert-1-error' id='messageData'><div style='display:block;'><span class='style-1-icon error'></span>"+response.message+"</div></div>");
						 $('#iderr_tasks').show();
						setTimeout(function(){
							$('#iderr_tasks').fadeOut('slow');
						},3000);
					}
				}
		});
	}else{
	    $('#iderr_tasks').html("Please enter task name.");
		$('#iderr_tasks').show();
		setTimeout(function(){
			$('#iderr_tasks').fadeOut('slow');
		},3000);
	}
}

function showVideoTutorial(){
	$("#video_tut_dialog").dialog({
                    draggable:false, 
                    resizable: false,
                    width:'auto',
                    title:'Video Tutorial',
                    modal: true
		});
}

/*
 * Java Script Functions for Employee Timesheets 
 * ==============================================
 */

function search_employee(event,manager_id,id,clicked_status,act,type,hidweek)
{
	$('#idclear_view_task').hide();
	
	if($.trim($('#empstring').val()).length>0)
	{
		$('#idclear_view_task').show();
	}
	else
	{
		$('#idclear_view_task').hide();
		var search = encodeURIComponent($('#'+id).val());
		var click=$('#idhidmenuitem').val();
	    var type= $('#timesheetView').val();
	    var hidweek=$('#'+hidweek).val();
	    var selmn=$('#idhidselmn').val();
	    var sort=$('#idhidac_sort').val();
	    var by=$('#idhidac_by').val();
	    if(type=='month')
	    {
	    	//display_monthly(selmn,'<?php echo $data->id;?>',search,click,act,type,hidweek);
	    	display_monthly(selmn,manager_id,search,click,act,type,hidweek);
	    }
	    else if(type=='week')
	    {
	        var weekstart=$('#idhidac_startday').val();
	        var weekend=$('#idhidac_endday').val();
	        show_accordion(manager_id,weekstart,weekend,search,click,act,type,hidweek);
	    }
	}
	if (event.keyCode == 13) {
		var search = encodeURIComponent($('#'+id).val());
		var click=$('#idhidmenuitem').val();
	    var type= $('#timesheetView').val();
	    var hidweek=$('#'+hidweek).val();
	    var selmn=$('#idhidselmn').val();
	    var sort=$('#idhidac_sort').val();
	    var by=$('#idhidac_by').val();
	    if(type=='month')
	    {
	    	//display_monthly(selmn,'<?php echo $data->id;?>',search,click,act,type,hidweek);
	    	display_monthly(selmn,manager_id,search,click,act,type,hidweek);
	    }
	    else if(type=='week')
	    {
	        var weekstart=$('#idhidac_startday').val();
	        var weekend=$('#idhidac_endday').val();
	        show_accordion(manager_id,weekstart,weekend,search,click,act,type,hidweek);
	    }
	}
}

function display_monthly(selmn,manager_id,search,clicked_status,active,type,hidweek){
	$.ajax({
        type:"get",		
        url:base_url+"/timemanagement/emptimesheets/getmonthlyspan/selmn/"+selmn,
        dataType:'json',
        success: function(data)
        {  
			$('#emp_ts_month_year').html(data.displayYearMonth);
        	show_accordion(manager_id,data.startday,data.endday,search,clicked_status,'',type,''); 
        }
    });
}

function display_weeks_monthly(selmn,manager_id,search,click,hidweek)
{
    search = $('#empstring').val();
    search = encodeURIComponent(search);
	
	if(hidweek!='')
	{
		 $.ajax({
        type:"get",	
        data:"hidweek="+hidweek+"&selmn="+selmn,	
        url:base_url+"/timemanagement/emptimesheets/getweekstartenddates/format/json",
        dataType:'json',
        success: function(response)
        {
			 $('#weeknamedisplay').html('Week-'+hidweek);
			 $('#weekdatesdisplay').html(response.saved);
        }
		});
	}
	
    $.ajax({
        type:"get",	
        data:"hidweek="+hidweek+"&selmn="+selmn+"&manager_id="+manager_id+"&search="+search+"&clicked_status="+click,	
        url:base_url+"/timemanagement/emptimesheets/displayweeks/format/html",
        dataType:'html',
        success: function(response)
        {
        	$('#idweeks_display').html(response);
        }
    });
}

function show_accordion(manager_id,startday,endday,search,clicked_status,active,type,hidweek)
{
    var emp_list_flag=$('#sel_emp_list').val();
    search = encodeURIComponent(search);
    $('#pageno').val('0');
    var page = parseInt($('#pageno').val());
    page = page+1;
    $.ajax({
        type:"post",	
        data:"emp_list_flag="+emp_list_flag+"&hidweek="+hidweek+"&type="+type+"&active="+active+"&clicked_status="+clicked_status+"&manager_id="+manager_id+"&startday="+startday+"&endday="+endday+"&search="+search+"&page="+page,	
        url:base_url+"/timemanagement/emptimesheets/accordion/format/html",
        dataType:'html',
        success: function(response)
        {
        	 $('#idacc_content').html(response);
        }
    });
}

function viewEmployeeTimesheet(selmn,user_id,manager_id,type,hidweek,emp_list_flag,project_ids){
	var form = document.createElement("form");
    var selYrMon = document.createElement("input"); 
    var ele_manager_id = document.createElement("input");  
    var ele_user_id = document.createElement("input"); 
    var ele_hidweek = document.createElement("input");  
    var ele_type = document.createElement("input");  
    var ele_emp_list_flag = document.createElement("input");
    var ele_project_ids = document.createElement("input");
    
    form.method = "POST";
    form.action = base_url+"/timemanagement/emptimesheets/employeetimesheet";   

    selYrMon.value=selmn;
    selYrMon.name="selYrMon";
    form.appendChild(selYrMon);  

    ele_manager_id.value=manager_id;
    ele_manager_id.name="manager_id";
    form.appendChild(ele_manager_id);
    
    ele_user_id.value=user_id;
    ele_user_id.name="user_id";
    form.appendChild(ele_user_id);
    
    ele_hidweek.value=hidweek;
    ele_hidweek.name="hidweek";
    form.appendChild(ele_hidweek);
    
    ele_type.value=type;
    ele_type.name="type";
    form.appendChild(ele_type);
    
    ele_emp_list_flag.value=emp_list_flag;
    ele_emp_list_flag.name="emplistflag";
    form.appendChild(ele_emp_list_flag);
    
    ele_project_ids.value=project_ids;
    ele_project_ids.name="project_ids";
    form.appendChild(ele_project_ids);

    document.body.appendChild(form);

    form.submit();
	/*var data = "selYrMon="+selmn+"&user_id="+user_id+"&manager_id="+manager_id+"&hidweek="+hidweek+"&type="+type;
	locationUrl = base_url+"/timemanagement/emptimesheets/employeetimesheet?"+data;
	window.location.href = locationUrl*/
}
function viewApproveAlert()
{
	 var myPos = [ $(window).width() / 5, 150 ];
    $("#idviewapprovedsheets").dialog({
        title:'Approved Timesheets',
		position: myPos,
        modal: true, 
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	      $('.ui-widget-overlay').addClass('ui-front-overwrite');
		  $('.ui-dialog').removeClass('ui-dialog-buttons');
		  $('.ui-dialog').removeClass('ui-front');
		  $('.ui-dialog').addClass('ui-btn-overwrite');
       		  
		  $.ajax({
				url: base_url + "/timemanagement/index/getapprovedtimesheet/format/html",
				dataType: 'html',
				success: function(response) {
				
					  $('#idapprovedaheetcontent').html(response);
				}
			});
	
      }
    });
}
function closeApproveAlert()
{
	$.ajax({
		url: base_url + "/timemanagement/index/closeapprovealert/format/json",
		type: 'POST',
		dataType: 'json',
		success: function(response) { 
			 //alert('rest');
			  setTimeout(function(){
					$('#close_div').hide();
				  },100);
		}
	});
}

function enableEmpTimesheet(selmn,emp_id,type,hideweek,manager_id,flag,emp_list_flag){
	$.ajax({
        data:"selmn="+selmn+"&hideweek="+hideweek+"&type="+type+"&emp_id="+emp_id+"&emplistflag="+emp_list_flag,	
        url:base_url+"/timemanagement/emptimesheets/enabletimesheet/format/json",
        dataType:'json',
        type: 'POST',
        success:function(data){
        	$("#grid_msg").html("<span class='style-1-icon success'></span>Enabled Successfully");
        	$("#grid_msg").show();
        	 setTimeout(function(){
     			$('#grid_msg').fadeOut('slow');
     		},3000);
        	
        	if(flag=="grid"){
        		var status= $('#idhidmenuitem').val();
        	    var search=$('#empstring').val();
        	    //var selmn=$('#idhidselmn').val();
        	    var type=$('#timesheetView').val();
        	    //var hidweek=$('#idhidweek_ac').val();
        	    if(type=='month')
        	    {
        	    	display_monthly(selmn,manager_id,search,status,'',type,hideweek);
        	    }
        	    else
        	    {        
        	    	display_weeks_monthly(selmn,manager_id,search,status,hideweek);
        	    }
        	}else{
        		if(type=='week')
    		    {        
    		    	emp_display_weeks_monthly(selmn,manager_id,'');
    		    }else{
    		    	$('#idweeks_display').html('');
    		    	displayEmpTimesheetMonthly(selmn,emp_list_flag);
    		    }
        	}
        	 
        }
    });
}

function approveEmpTimesheet(selmn,emp_id,type,hideweek,manager_id,flag,emp_list_flag){
	$.ajax({
        data:"selmn="+selmn+"&hideweek="+hideweek+"&type="+type+"&emp_id="+emp_id+"&emplistflag="+emp_list_flag,	
        url:base_url+"/timemanagement/emptimesheets/approvetimesheet/format/json",
        dataType:'json',
        type: 'POST',
        success:function(data){
		
        	$("#grid_msg").html("<span class='style-1-icon success'></span>Approved Successfully.");
        	$("#grid_msg").show();
        	 setTimeout(function(){
     			$('#grid_msg').fadeOut('slow');
     		},3000);
        	 
    		if(flag=="grid"){
    			var status= $('#idhidmenuitem').val();
        	    var search=$('#empstring').val();
        	   //var selYrmn=$('#idhidselmn').val();
        	    var type=$('#timesheetView').val();
        	   //var hidweek=$('#idhidweek_ac').val();
        	    if(type=='month')
        	    {
        	    	display_monthly(selmn,manager_id,search,status,'',type,hideweek);
        	    }
        	    else
        	    {        
        	    	display_weeks_monthly(selmn,manager_id,search,status,hideweek);
        	    }
        		
        	}else{
        		if(type=='week')
    		    {        
    		    	emp_display_weeks_monthly(selmn,manager_id,'');
    		    }else{
    		    	$('#idweeks_display').html('');
    		    	displayEmpTimesheetMonthly(selmn,emp_list_flag);
    		    }
        	}
        	 
        }
    });
}

function rejectEmpTimesheet(selmn,emp_id,type,hideweek,manager_id,flag,emp_list_flag){
	 $("#idreject_note").dialog({
         draggable:false, 
         resizable: false,
         width:300,
         title:'Reason/Note',
         modal: true, 
         buttons : [
             {text:"Ok",click : function() {	
                  $('.note_errors').remove();   
                  var txtval=encodeURIComponent($.trim($('#idtxtrejectnote').val()));  
                  
                  if(txtval=='')
                  {                                                 
	                 $('#idtxtrejectnote').parent().append("<span class='note_errors  task_errors' style='font-size:12px;color:red;'>Please enter comment.</span>"); 
	                 setTimeout(function(){ $('.note_errors').fadeOut('slow');},3000);
                  }
                  else
                  {
                	  $.ajax({
                	        data:"selmn="+selmn+"&hideweek="+hideweek+"&type="+type+"&emp_id="+emp_id+"&rejnote="+txtval+"&emplistflag="+emp_list_flag,	
                	        url:base_url+"/timemanagement/emptimesheets/rejecttimesheet/format/json",
                	        dataType:'json',
                	        type: 'POST',
                	        success:function(data){
                	        	$('#idtxtrejectnote').val("");
                	        	$("#grid_msg").html("<span class='style-1-icon success'></span>Rejected Successfully.");
                	        	$("#grid_msg").show();
                	        	 setTimeout(function(){
                	     			$('#grid_msg').fadeOut('slow');
                	     		},3000);
                	        	 
            	        		if(flag=="grid"){
            	        			var status= $('#idhidmenuitem').val();
            	            	    var search=$('#empstring').val();
            	            	    //var selmn=$('#idhidselmn').val();
            	            	    var type=$('#timesheetView').val();
            	            	    //var hidweek=$('#idhidweek_ac').val();
            	            	    if(type=='month')
            	            	    {
            	            	    	display_monthly(selmn,manager_id,search,status,'',type,hideweek);
            	            	    }
            	            	    else
            	            	    {        
            	            	    	display_weeks_monthly(selmn,manager_id,search,status,hideweek);
            	            	    }
            	            	}else{
            	            		if(type=='week')
            	        		    {        
            	        		    	emp_display_weeks_monthly(selmn,manager_id,'');
            	        		    }else{
            	        		    	$('#idweeks_display').html('');
            	        		    	displayEmpTimesheetMonthly(selmn,emp_list_flag);
            	        		    }
            	            	}
            	            	 
                	        }
                	    });
                     $(this).dialog("destroy");
                  }
             }},
             {"class":'cancel_dialog',text:'Cancel', click:function() {
                 $(this).dialog("destroy");
             }}
         ],
         open:function(){
              $('.note_errors').remove();  
              $('#idtxtrejectnote').val('');
         },
         close:function(){
             $(this).dialog("destroy");
         }
     });
}

function emp_display_weeks_monthly(selmn,manager_id,hidweek,emp_list_flag)
{

	if(hidweek!='')
	{
		 $.ajax({
        type:"get",	
        data:"hidweek="+hidweek+"&selmn="+selmn,	
        url:base_url+"/timemanagement/emptimesheets/getweekstartenddates/format/json",
        dataType:'json',
        success: function(response)
        {
			 $('#weeknamedisplay').html('Week-'+hidweek);
			 $('#weekdatesdisplay').html(response.saved);
        }
		});
	}
    $.ajax({
        type:"POST",	
        data:"hidweek="+hidweek+"&selmn="+selmn+"&manager_id="+manager_id+"&emplistflag="+emp_list_flag,	
        url:base_url+"/timemanagement/emptimesheets/empdisplayweeks/format/html",
        dataType:'html',
        success: function(response)
        {
        	$('#idweeks_display').html(response);
        }
    });
}

function displayEmpTimesheetMonthly(selmn,emp_list_flag){
	//alert("Here");
	var user_id=$('#user_id').val();
	var manager_id=$('#manager_id').val();
	var project_ids = $('#project_ids').val();
	$.ajax({
        type:"post",	
        data: "selYrMon="+selmn+"&manager_id="+manager_id+"&user_id="+user_id+"&emplistflag="+emp_list_flag+"&project_ids="+project_ids,	
        url:base_url+"/timemanagement/emptimesheets/emptimesheetmonthly/format/html",
        dataType:'html',
        success: function(response)
        {
			$('#emp_timesheet_view').html(response);
        }
    });
}

function displayEmpTimesheetWeekly(selmn,hideweek,emp_list_flag){
	var user_id=$('#user_id').val();
	var manager_id=$('#manager_id').val();
	var project_ids = $('#project_ids').val();
	$.ajax({
        type:"post",	
        data: "selYrMon="+selmn+"&manager_id="+manager_id+"&user_id="+user_id+"&hideweek="+hideweek+"&emplistflag="+emp_list_flag+"&project_ids="+project_ids,	
        url:base_url+"/timemanagement/emptimesheets/emptimesheetweekly/format/html",
        dataType:'html',
        success: function(response)
        {
        	$('#emp_timesheet_view').html(response);
        }
    });
}

function approveEmpDayTimesheet(selmn,emp_id,day,flag,emp_list_flag){
	$.ajax({
		data:"selmn="+selmn+"&day="+day+"&emp_id="+emp_id+"&emplistflag="+emp_list_flag,	
        url:base_url+"/timemanagement/emptimesheets/approvedaytimesheet/format/json",
        dataType:'json',
        type: 'POST',
        success:function(data){
        	$("#grid_msg").html("<span class='style-1-icon success'></span>Approved Successfully.");
        	$("#grid_msg").show();
        	 setTimeout(function(){
     			$('#grid_msg').fadeOut('slow');
     		},3000);
      		if(flag == "month"){
      			displayEmpTimesheetMonthly(selmn,emp_list_flag);
      		}else{
      			var manager_id=$('#manager_id').val();
       			var hidweek=$('#hidweek').val();
       			emp_display_weeks_monthly(selmn,manager_id,hidweek,emp_list_flag);
      		}
        	
        }
    });
}

function rejectEmpDayTimesheet(selmn,emp_id,day,flag,emp_list_flag){
	
	$("#idreject_note").dialog({
        draggable:false, 
        resizable: false,
        width:300,
        title:'Reason/Note',
        modal: true, 
        buttons : [
            {text:"Ok",click : function() {	
                 $('.note_errors').remove();   
                 var txtval=encodeURIComponent($.trim($('#idtxtrejectnote').val()));  
                 
                 if(txtval=='')
                 {                                                 
	                 $('#idtxtrejectnote').parent().append("<span class='note_errors  task_errors' style='font-size:12px;color:red;'>Please enter comment.</span>"); 
	                 setTimeout(function(){ $('.note_errors').fadeOut('slow');},3000);
                 }
                 else
                 {
               	  $.ajax({
               	        data:"selmn="+selmn+"&day="+day+"&emp_id="+emp_id+"&rejnote="+txtval+"&emplistflag="+emp_list_flag,	
               	        url:base_url+"/timemanagement/emptimesheets/rejectdaytimesheet/format/json",
               	        dataType:'json',
               	        type: 'POST',
               	        success:function(data){
               	        	$('#idtxtrejectnote').val("");
               	        	$("#grid_msg").html("<span class='style-1-icon success'></span>Rejected Successfully.");
            	        	$("#grid_msg").show();
            	        	 setTimeout(function(){
            	     			$('#grid_msg').fadeOut('slow');
            	     		},3000);
            	        	if(flag == "month"){
            	        		 displayEmpTimesheetMonthly(selmn,emp_list_flag);
            	       		}else{
            	       			var manager_id=$('#manager_id').val();
            	       			var hidweek=$('#hidweek').val();
            	       			emp_display_weeks_monthly(selmn,manager_id,hidweek,emp_list_flag);
            	       		}
            	        	
               	        }
               	    });
                    $(this).dialog("destroy");
                 }
            }},
            {"class":'cancel_dialog',text:'Cancel', click:function() {
                $(this).dialog("destroy");
            }}
        ],
        open:function(){
             $('.note_errors').remove();  
             $('#idtxtrejectnote').val('');
        },
        close:function(){
            $(this).dialog("destroy");
        }
    });
	
}

function openNotes(str){
	jAlert(str,'Notes');
}

function downloadAttachment(id){
	   $.blockUI({ width:'50px',message: $("#spinner").html() });
	     $.ajax({
				type: "POST",
				url: base_url + '/timemanagement/expenses/getfilename/id/' + id,
				//data: data,
				success: function(response) {
	    	 		response = JSON.parse(response);
					download_url = base_url + '/timemanagement/expenses/download/expense_file/' + response.file_name;
				    var $preparingFileModal = $("#preparing-file-modal");
			        $.fileDownload(download_url, {
			            successCallback: function(responseHtml,download_url) {
							$.unblockUI();
			            },
			            failCallback: function(responseHtml, download_url) {
			            	$.unblockUI();
			                jAlert('Download of the expense failed');
			            }
			        });
			        return false; 
	     		}
	   });
}

function submitExpense(id){
	   $.blockUI({ width:'50px',message: $("#spinner").html() });
	     $.ajax({
				type: "POST",
				url: base_url + '/timemanagement/expenses/submitexpense/id/' + id,
				//data: data,
				success: function(response) {
	    	 		response = JSON.parse(response);
	    	 		if(response.status == 'success'){
	    	 			$.unblockUI();
	    	 			jAlert(response.msg);
	    	 			$('.refresh-grid').trigger('click');
	    	 		}else{
	    	 			$.unblockUI();
		                jAlert('Expense submission failed.');
	    	 		}
			        return false; 
	     		}
	   });	
}


function getEmployeeWeekDataByDay(weekNo,day,user_id,manager_id,selmn,emp_list_flag){
	var project_ids = $('#project_ids').val();
	if(day == undefined )
		day = '';	
	$.ajax({
        type:"post",	
        data: "selYrMon="+selmn+"&manager_id="+manager_id+"&user_id="+user_id+"&hideweek="+weekNo+"&emplistflag="+emp_list_flag+"&day="+day+"&project_ids="+project_ids,	
        url:base_url+"/timemanagement/emptimesheets/emptimesheetweekly/format/html",
        dataType:'html',
        success: function(response)
        {
        	$('#emp_timesheet_view').html(response);
        }
    });
}
/*
 * END for Java Script Functions for Employee Timesheets 
 * =====================================================
 */

function addProjecttoClient(flag,client_id){
	locationUrl = base_url+"/timemanagement/projects/edit/cid/"+client_id;
	window.location.href = locationUrl;
}

function weekEdit(yearMonth,day) {
    var url = base_url + module_name + "/index/week";
	//window.location.href = url+'?selYrMon='+yearMonth+'&flag=time&day='+day;
	window.location.href = base_url+'/timeentryday/'+yearMonth+'/'+'time'+'/'+day;
}

function loadProjects(ele){
	var clientID = $(ele).val();
	
	$.ajax({
        type:"post",	
        data: "clientID="+clientID,	
        url:base_url+"/timemanagement/expenses/getprojectbyclientid/format/json",
        dataType:'json',
        success: function(response)
        {
			$('#project_id').empty();
			$.each($.parseJSON(response), function(key,value){
				$('#project_id').append($('<option>').text(value.project_name).attr('value', value.id));
			});
        }
    });	
}

function expenseReportsSuccess(){
	//Remove View Action
	$('.sprite.view').remove(); 
	$('.expensestatus').each(function(){
		switch($(this).html()) {
	    case 'saved':
	    	$(this).addClass('blue');
	        break;
	    case 'submitted':
	    	$(this).addClass('oragne');
	        break;
	    case 'approved':
	    	$(this).addClass('green');
	        break;
	    case 'rejected':
	    	$(this).addClass('red');
	        break;                
		}	
	});
}

function updateExpenseStatusPopup(id,status){
	 $('<div></div>').dialog({
	        modal: true,
	        title: "Confirmation",
	        open: function () {
	            var markup = 'Reject Note <input type="text" id="reject_notes"/>';
	            $(this).html(markup);
	        },
	        buttons: {
	            Ok: function () {
	        		//console.log($('#reject_note').val());
	        		//return false;
	        		updateExpenseStatus(id,status,$('#reject_notes').val());
	        		$(this).dialog("close");
	        		$(this).dialog('destroy').remove();
	            },
	            Cancel: function () {
	                $(this).dialog("close");
	                $(this).dialog('destroy').remove();
	            }
	        }
	    });
}

function updateExpenseStatus(id,status,notes){
	if (typeof notes === 'undefined') { notes = ''; }
	$.blockUI({ width:'50px',message: $("#spinner").html() });
	$.ajax({
		type: "POST",
		url: base_url + '/timemanagement/expenses/updateexpensestatus/',
		data: 'id=' + id+'&status='+status+'&notes='+notes,
		success: function(response) {
	 		response = JSON.parse(response);
	 		if(response.status == 'success'){
	 			$.unblockUI();
	 			jAlert(response.msg);
	 			$('.refresh-grid').trigger('click');
	 		}else{
	 			$.unblockUI();
                jAlert('Expense submission failed.');
	 		}
	        return false; 
 		}
});	
}

/*leaver request function from hrms*/

function emptytodate(ele)
{
  var dayselected =  $('#leaveday :selected').val();
  var fromdateval = $('#from_date').val();
  var todateval = $('#to_date').val();
  var selector = $(ele).prop('id');
  var date1 = new Date(fromdateval);
  //var date2 = new Date(todateval);
 
	if(date1 != '')
	{
		var fromdate = date1.getFullYear();
	}
	/* if(date2 != '')
	{
		var todate = date2.getFullYear();
	} */
 
  var date = new Date();
  var y = date.getFullYear();
 
	if(fromdate <= y )
	{
	  if(dayselected == 1)
	    {  
	      validateselecteddate(ele);  
		}
	  else if(dayselected == 2)
	    {
		  if(fromdateval !='') 
		    $("#appliedleavesdaycount").val(0.5);
		  else
	        $("#appliedleavesdaycount").val('');	  
	    }
	}
	else
	{
	
		if(fromdate > y)
		{
			$("#"+selector).val('');
			 $('#errors-from_date').remove();
			 $('#from_date').parent().append("<span class='errors' id='errors-from_date'>Leave cannot be applied for future year.</span>");
		}
		/* if(todate > y)
		{
			$("#"+selector).val('');
			$('#errors-to_date').remove();
			$('#to_date').parent().append("<span class='errors' id='errors-to_date'>Leave cannot be applied for future year.</span>");
		} */
		
	}
	
}

function validateselecteddate(ele)
{
  if($("#todateerrorspan").is(":visible"))
     $("#todateerrorspan").hide();
  var leavetypeselectedval =  $('#leavetypeid :selected').val();
  var leavetypeselectedstr = leavetypeselectedval.split('!@#'); 
  var leavetypeid = leavetypeselectedstr[0];
  var leavetypelimit = leavetypeselectedstr[1];
  var leavetypetext = leavetypeselectedstr[2];
  var dayselected =  $('#leaveday :selected').val();
  var fromdateval = $('#from_date').val();
  var todateval = $('#to_date').val();
  var weekend_startday = $('#wkstrtday').val();
  var weekend_endday = $('#wkendday').val();
  var ishalfday = $('#ishalfday').val();
  var context = 1;
  var selectorid = '';
  var selector = $(ele).prop('id');
  if(selector == 'from_date')
     selectorid = 1;
  else	 
     selectorid = 2;
	
	var fromdateArr = fromdateval.split("-");
	var fromDate = Date.parse(new Date(fromdateArr[2], fromdateArr[0], fromdateArr[1]));
	
	var todateArr = todateval.split("-");
	var toDate = Date.parse(new Date(todateArr[2], todateArr[0], todateArr[1]));
	
	var fromdateformat = fromdateArr[2]+'-'+fromdateArr[0]+'-'+fromdateArr[1];
	var todateformat = todateArr[2]+'-'+todateArr[0]+'-'+todateArr[1];
/* 	var date1 = $('#from_date').datepicker('getDate');
	var date2 = $('#to_date').datepicker('getDate'); */
	//var date1 = new Date(fromdateval);
	var date2 = new Date(todateval);
	
	/* if(date1 != '')
	{
		var fromdate = date1.getFullYear();
	} */
	if(date2 != '')
	{
	  var todate = date2.getFullYear();
	}
	 
	  var date = new Date();
	  var y = date.getFullYear();
	
    if(fromdateval != '' && todateval != '' && leavetypeselectedval !='' && todate <= y )	
	  {
		$(ele).parent().append("<span class='errors' id='errors-"+selector+"'></span>"); 
		$.ajax({
					url: base_url+"/index/calculatebusinessdays/format/json",   
					type : 'POST',	
					data : 'fromDate='+fromdateval+'&toDate='+todateval+'&dayselected='+dayselected+'&leavetypelimit='+leavetypelimit+'&leavetypetext='+leavetypetext+'&ishalfday='+ishalfday+'&context='+context+'&selectorid='+selectorid+'&leavetypeid='+leavetypeid,
					dataType: 'json',
					beforeSend: function ()
					{
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					success : function(response){
						     if(response['result'] == 'success' && response['result'] !='' && response['days'] !='') 
							{
							  $("#appliedleavesdaycount").val(response['days']);
							  $("#errors-"+selector).remove();
							  	if(response['availableleaves'] !='' && response['days'] !='')
								{
							  		if(response['days'] > 1)
							  		{
							  			$(".select2-results-dept-0 li:has('div'):has('span')").remove();
							  			
							  		}
							  		if(response['days'] > response['availableleaves'])
									 jAlert("Applied leaves exceed the available leaves count. Additional leaves will be considered as Loss of Pay.");
								}
							}
							if(response['result'] == 'error' && response['result'] !='' && response['message'] !='')
							{
							    $("#errors-"+selector).show();
								$("#errors-"+selector).html(response['message']);
								$("#"+selector).val('');
								$("#appliedleavesdaycount").val('');
							}
							if(response['result'] != 'error' && response['days'] == 0) {
								$("#errors-"+selector).show();
								$("#errors-"+selector).html('You cannot apply leave on Weekend/Holidays.');
								$("#"+selector).val('');
								$("#appliedleavesdaycount").val('');
							}
					}
				});
	  } else {
		 if(selector=='from_date') {
			  if($("#to_date").val()!='') {
				$("#"+selector).val('');
			  }	
		  }else{
			  if($("#from_date").val()!='') {
					$("#"+selector).val('');
			  }		
		  }
		  $("#appliedleavesdaycount").val('');
		  if(leavetypeselectedval == '') {
			  jAlert("Please select leave type.");
		  }
		  	/* if(fromdate > y)
			{	
		  		$("#"+selector).val('');
			  	$('#errors-from_date').remove();
			  	$('#from_date').parent().append("<span class='errors' id='errors-from_date'>Leave cannot be applied for future year.</span>");
			} */
			if(todate > y)
			{
				$("#"+selector).val('');
				 $('#errors-to_date').remove();
				 $('#to_date').parent().append("<span class='errors' id='errors-to_date'>Leave cannot be applied for future year.</span>");
			}
	  }
}
function hidetodatecalender()
{
	
    if($("#fromdateerrorspan").is(":visible"))
      $("#fromdateerrorspan").hide();
	if($("#errors-from_date").is(":visible"))
      $("#errors-from_date").hide();
	if($("#errors-to_date").is(":visible"))
	  $("#errors-to_date").hide();
	if($("#todateerrorspan").is(":visible"))
      $("#todateerrorspan").hide();

    var dayselected =  $('#leaveday :selected').val();
    var todate = $('#to_date').val();
    var fromdate = $('#from_date').val();
    if(dayselected == 1)
	{
	    $("#todatediv").show();
		$('#to_date').val(fromdate);
		$('#from_date').val(fromdate);
		$("#appliedleavesdaycount").val('1');
	}
	else if(dayselected == 2)
	{
			$.ajax({
					url: base_url+"/leaverequest/gethalfdaydetails/format/json",   
					type : 'POST',	
					dataType: 'json',
					success : function(response){
							if(response['result'] == 1)
							{
							    $("#todatediv").hide();
								$('#to_date').val('');
								$('#from_date').val(fromdate);
								$("#appliedleavesdaycount").val('0.5');
							}
							else if(response['result'] == 2)
							{
							   $('#s2id_leaveday .select2-choice span').html('Full Day');
							   $('#leaveday').val(1);
							   jAlert("Half day leave cannot be applied.");
							}
							else if($.trim(response['result']) == 'error')
							{
							   $('#s2id_leaveday .select2-choice span').html('Full Day');
							   $('#leaveday').val(1);
							   jAlert("Half day leave cannot be applied.");
							}
					}
				});
	}			

}

function saveDetails(url,dialogMsg,toggleDivId,jsFunction){	
	var actionurl = url.split( '/' );
	$("#formid").attr('action',base_url+"/"+url);       
	$("#formid").attr('method','post');
	$('#formid').ajaxForm({
	    beforeSend: function(a,f,o) {
        },			
		dataType:'json',
		
		success: function(response, status, xhr) { 
		     $("#formid").find('.errors').remove();
			 $("#formid").find('.borderclass').removeClass('borderclass');
			 $("#formid").find('span[class^="errors_"]').remove();
			 
			 elementid = '';
			 var i =0;
			 $.each(response, function(id) {
				 if(i == 0){
					 firstelementid = id;
					 i++;
				 } 
				 if(response['result'] !=  'saved' ){
				  $.each(this, function(k, v) {	
					  if(elementid != id){ 
						  elementid = id;
						  var formName =  $('#formid').attr('name');
						  if($("#"+id).length > 0){
							  if(formName == 'Changepassword')
								  $("#"+id).parent().parent().append(getErrorHtml(v, id, '_'+id));
							  else{
								  $("#"+id).parent().append(getErrorHtml(v, id,''));
							  }
						}else{
							  $("[name="+id+"]").parent().parent().append(getErrorHtml(v, id,''));
						}
					  }
				  });
				 }
				});
				
				if($(".errors").length > 0)
				{
					var varaible = window.location.pathname;
					var x = varaible.lastIndexOf('/');
					if(varaible.substring(x+1) == "registration")
						eval(jsFunction);
				}

				if(response['result'] ==  'saved' ||  response['result'] ==  'fbsaved' || response['result'] == 'exception') {
					if(toggleDivId.length > 2)
						$("#"+toggleDivId).toggle("slow");								
			
					if(dialogMsg.length > 2) 
						eval(dialogMsg); 
					else
						
						eval(jsFunction);
						
					if(response['controller'] == 'pendingleaves' )	{ // leave request in timesheet
					   //window.location.href = base_url+'/pendingleaves';
					  location.reload();
					} 
					
				} 
				
		}
		});
}
function getEmpDuration(empId,start_date,end_date,project_id,params)
{
	 var myPos = [ $(window).width() / 5, 150 ];
    $("#idviewEmpProj").dialog({
        title:'View Employee Project Duration',
		position: myPos,
        modal: true, 
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
			$('.ui-widget-overlay').addClass('ui-front-overwrite');
			$('.ui-dialog').removeClass('ui-dialog-buttons');
			$('.ui-dialog').removeClass('ui-front');
			$('.ui-dialog').addClass('ui-btn-overwrite');	$.ajax({
				type:"post",	
				data: "empId="+empId+"&start_date="+start_date+"&end_date="+end_date+"&projectId="+project_id+"&params="+params,	
				url:base_url+"/timemanagement/reports/getempduration/format/html",
				dataType:'html',
				success: function(response)
				{
					$('#idviewEmpProjcontent').html(response);
				}
			});
      }
    });
}
function getProjectTaskDuration(empId,start_date,end_date,project_id,params)
{
	var myPos = [ $(window).width() / 5, 150 ];
    $("#idviewProjTask").dialog({
        title:'View Project Task Duration',
		position: myPos,
        modal: true, 
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
			$('.ui-widget-overlay').addClass('ui-front-overwrite');
			$('.ui-dialog').removeClass('ui-dialog-buttons');
			$('.ui-dialog').removeClass('ui-front');
			$('.ui-dialog').addClass('ui-btn-overwrite');	
			$.ajax({
				type:"post",	
				data: "empId="+empId+"&start_date="+start_date+"&end_date="+end_date+"&projectId="+project_id+"&params="+params,	
				url:base_url+"/timemanagement/reports/getprojecttaskduration/format/html",
				dataType:'html',
				success: function(response)
				{
					$('#idviewProjTaskcontent').html(response);
				}
			});
      }
    });
}
function closetab(ele,pagename,pagelink)
{ 
     var newURL = window.location.protocol + "//" + window.location.host;

     jQuery.ajax({
		  url: base_url+'/index/clearsessionarray',
          data: 'name='+pagename+'&link='+pagelink,
          type: "POST",
          dataType: "json",
		  beforeSend: function () {
		    $("#recentviewtext").before("<div id='loader-recent'></div>");
            $("#loader-recent").html("<img src="+domain_data+"public/media/images/loader_21X21.gif>");
          },
          success: function(response){
               if(response['result'] == 'success'){
	           $(ele).parent('li').remove();
			   $("#loader-recent").remove();
	       }
               if(response['is_empty'] == 'yes')
                {
                    $('#recentviewtext').html('');
                }
            if(typeof($('.recentviewd').find('ul li').html()) == 'undefined')
                   $('#recentviewtext').html('');
            }
        },'json');
}

function loadPage(page){
	var form_change = $("#form_change").val();
  console.log(" from change "+form_change);    
	if(form_change==1){
		
		$("#dialog_global").dialog({
			draggable:false, 
			resizable: false,
		    width:252,
			title:'Confirmation',
		    modal: true, 
		    buttons : [
		        {text:"Ok",click : function() {		        	
		        	 $(this).dialog("close");
		        	 window.location.href = base_url+"/"+page;
		        }},
		         {"class":'cancel_dialog',text:'Cancel', click:function() {
		        	$(this).dialog("close");
		         }
		        }
		    ]
		});

	}else{
		window.location.href = base_url+"/"+page;
	}
	
}
function redirecttolink(link,module)
{    
 $.blockUI({ width:'50px',message: $("#spinner").html() });
 if(module!='')
	 window.location = base_url+'/'+module+'/'+link;
 else
	 window.location = base_url+'/'+link;

}