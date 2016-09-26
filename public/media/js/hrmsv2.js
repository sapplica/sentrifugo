function upgradesystem(weburl,flag,codeversion,dbversion)
{
	var param = '';
	if(flag !='' && codeversion !='' && dbversion !='')
	{
		 if(flag=='code')
			 param = 'service=preparedownloadablelink&update=code&codeversion='+codeversion+'&dbversion='+dbversion;
		 else if(flag == 'db')
			 param = 'service=preparedownloadablelink&update=db&codeversion='+codeversion+'&dbversion='+dbversion;
		 else
			 param = 'service=prepareupgradeversion&update=both&clientversion='+codeversion+'&upgradeversion='+dbversion;
		 $.ajax({
             	url: weburl,				
				type : 'POST',
				crossDomain :true,
				data : param,
				dataType: 'json',
				beforeSend: function () {
					if(flag=='both')
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					else
						$.blockUI({ width:'50px',message: $("#upgradespinner").html() });
				},
				success : function(response){	
					$.unblockUI();
						if(response.status == 1 && $.trim(response.message) == 'Success' && response.result !='')
						{
							$('#successpan').html('<span>Plese click <a href='+response.result+' target="_blank">here </a>to download</span>');
						}else
						{
							$('#successpan').html('<span class="errors">Some error occured. Please try again.</span>');
						}	
				}
			});
	}
}	
	
	function getcurrentversion(weburl,clientversion)
	{
		if(clientversion !='')
			param = 'service=getcurrentversion&clientversion='+clientversion;
		else
			param = 'service=getcurrentversion';
		$.ajax({
         	url: weburl,	
         	crossDomain :true,
			type : 'POST',	
			data : param,
			dataType: 'json',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$("#errors-versionumber").remove();
					if(response.status == 1 && $.trim(response.message) == 'Success' && response.totalversion !='')
					{
						
								$('#versionnumber').html(response.totalversion);
								$('#s2id_versionnumber .select2-choice span').html('Select version to upgrade');
					}else
					{
						$('#versionnumber').parent().append('<span class="errors" id="errors-versionumber">No updates are available now.</span>');
					}	
			}
		});
	}
	
	function upgradetotalsystem(weburl,flag,codeversion)
	{
		if(flag == 'demo')
		{
			$('#demo_success_msg').show();
			$('#demo_success_msg').append('Application upgraded successfully.');
			setTimeout(function(){
				$('#demo_success_msg').fadeOut('slow');
			},3000);
			window.location = base_url+'/dashboard/upgradeapplication';
		}else
		{	
			if($("#versionnumber").val())
			{
				var dbversion = $("#versionnumber").val(); 
				upgradesystem(weburl,flag,codeversion,dbversion);
			}else
			{
				jAlert('Please select version to upgrade.');
			}
		}
	}
	
	function comapareversions(weburl,codeversion,dbversion)
	{
		
		$.ajax({
         	url: weburl,
         	crossDomain :true,
			type : 'POST',	
			data : 'service=compareversion&codeversion='+codeversion+'&dbversion='+dbversion,
			dataType: 'json',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#upgradespinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$("#errors-versionumber").remove();
				if(response.status == 1 && $.trim(response.message) == 'Success' && response.result !='')
				{
					$('#successpan').html('<span>Plese click <a href='+response.result+' target="_blank">here </a>to download and upgrade the system.</span>');
				}else
				{
					$('#successpan').html('<div class="show-text">'+response.result+'</div>');
				}		
			}
		});
		
	}		
	
	function hideshowgroups(ele)
	{
		var configparam = $('input[name=group_flag]:checked').val();
		alert(configparam);
		
	}
	
	
	
	function fnSaveMappedEmployees_old()
	{
		var errorcount = 0;
	    var divlength = $("[class^='users_right_list_div users_right_list user_div_']").length;
	    var finalids='';
	    
	    if(divlength == 0)
    	{
	    	$(".no_right_data_found span").html('Please add employees to map.');
	    	errorcount++;
    	}
	    
	    if(errorcount == 0)
	    	{
	    	$.blockUI({ width:'50px',message: $("#spinner").html() });
		    	jQuery("[class^='users_right_list_div users_right_list user_div_']").each(function() {
		    		var employeeIds = $(this).attr('subject');
		    		finalids+=employeeIds+',';
		    	  
		    	});
		    	finalids= finalids.replace(/,\s*$/, "");
		    	
		    		if(finalids)
		    		{
		    			
		    			$('#empids').val(finalids);
		    			 $("#formid").submit();
		    			
		    		}
	    	}else
    		{
	    		$.unblockUI();
    		}
	}
	
	/*
	function saveempgroupdetails(url)
	{
            
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
							  if($("#"+id).length > 0){
									  $("#"+id).parent().parent().append(getErrorHtml(v, id,''));
								  
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
						//if(varaible.substring(x+1) == "registration")
							//eval(jsFunction);
					}



	
					if(response['result'] ==  'saved') 
					{
					} 
					
			}
			});
	}*/
	
	
	function validatedocumentname(ele,flag)
	{
		var elementid = $(ele).attr('id');
		var reqValue = $(ele).val();
		var re = /^[a-zA-Z0-9\- ]+$/;
		$('#errors-document_name_'+elementid).remove();
		if(reqValue == '')
		{
			if(flag == 1)
				$(ele).parent().append("<span class='errors' id='errors-document_name_"+elementid+"'>Please enter document name.</span>");
		}		
		else if(!re.test(reqValue))
		{
			$(ele).parent().append("<span class='errors' id='errors-document_name_"+elementid+"'>Please enter valid document name.</span>");
		}
		else
		{
			$('#errors-document_name_'+elementid).remove();
		}
	}

	function validatedocumentonsubmit()
	{
		var parentdivlength = $('div[id^=parent]').length;
	    var re = /^[a-zA-Z0-9\- ]+$/;
	    var errorcount = 0;
	    /*var genderid = $('#genderid').val();
	    var maritalstatusid = $('#maritalstatusid').val();
	    var nationalityid = $('#nationalityid').val();
	    var dob = $('#dob').val();*/
	    $('#errors-genderid').remove();
	    $('#errors-maritalstatusid').remove();
	    $('#errors-nationalityid').remove();
	    $('#errors-dob').remove();
	  	
		if(parentdivlength > 0)
	    {                    
	        $('.identitydocclass').each(function(i){                            
	            //var ele= $(this).find('.cls_service_request_name');   
	            var ele= $(this);                         
	            var elementid = $(ele).attr('id');
	            var reqValue = $(ele).val();
	            $('#errors-'+elementid).remove();
	            $('#errors-document_name_'+elementid).remove();
	            if($(ele).val() == '')
	            {
	                if(ele.hasClass('hasDatepicker'))
	                {    
	                	$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter expiry date.</span>");
	                	errorcount++;
	                }	
	                else
	                {
	                	if($(ele).parent().parent().find("label").hasClass('required'))
	                	{	
	                		$(ele).parent().append("<span class='errors' id='errors-document_name_"+elementid+"'>Please enter document name.</span>");
	                		errorcount++;
	                	}	
	                }	
	                
	            }
	            else if(!re.test(reqValue))
	            {
	            	if(!ele.hasClass('hasDatepicker'))
	                {
		                $(ele).parent().append("<span class='errors' id='errors-document_name_"+elementid+"'>Please enter valid document name.</span>");
		                errorcount++;
	                }
	            }
	            else
	            {
	            	$('#errors-'+elementid).remove();
	                $('#errors-document_name_'+elementid).remove();
	            }
	        });
	    }
	   /* if(genderid == '')
	    {
	        $('#genderid').parent().append("<span class='errors' id='errors-genderid'>Please select gender.</span>");
	        errorcount++;
	    }
	    if(maritalstatusid == '')
	    {
	        $('#maritalstatusid').parent().append("<span class='errors' id='errors-maritalstatusid'>Please select marital status.</span>");
	        errorcount++;
	    }
	    if(nationalityid == '')
	    {
	        $('#nationalityid').parent().append("<span class='errors' id='errors-nationalityid'>Please select nationality.</span>");
	        errorcount++;
	    }
	    if(dob == '')
	    {
	        $('#dob').parent().append("<span class='errors' id='errors-dob'>Please select date of birth.</span>");
	        errorcount++;
	    }*/
	    if(errorcount == 0)
	    {
	        $.blockUI({ width:'50px',message: $("#spinner").html() });
	        document.getElementById("formid").submit();
	    }
	}
	
	
	function changesalarytext(ele)
	{
		var salarytypeval = $("#salarytype").val()
		if(salarytypeval)
		{
				if(salarytypeval == 1)
					$('#salarytext').html('Per Annum');
				else
					$('#salarytext').html('Per Hour');
		}else
		{
			$('#salarytext').html('');
		}		
		
	}
	
	
	/**
	 * Populting Request reciever, CC reciever and service desk department based on business unit and department selection
	 * Request reciever and CC reciever are populated for all groups except (User and Management). 
	 * Service desk departments are populated which are not there in main_sd_configuration table based on business unit and department selection.
	 * This is done to avoid duplicate entries of service desk departments.
	 * @param ele 
	 */

	function displayDept(ele)
	{
		var elementid = '';
		var id = '';
		var dataparam = ''; 
		var bunitid = $("#businessunit_id").val();
		var deptid = $("#department_id").val();
		
		// Removing error divs.
		$('#errors-performance_app_flag-0').remove();
		
		elementid = $(ele).attr('id');
		if(elementid == 'businessunit_id')
		{
			
			
				 if(ele.selectedIndex == 1){
					
					 id = ele[ele.selectedIndex].value;
					
					 $("#performance_app_flag-1").prop("checked", false);
					 $("#performance_app_flag-1").prop('disabled', true);
					 $("#performance_app_flag-1 label").hide();
				     $("#performance_app_flag-0").prop("checked", true);
				     chkduplicateimplementation(2, ele.selectedIndex);
					 
					}
				 else if(ele.selectedIndex != -1){
					 id = ele[ele.selectedIndex].value;
					}
				dataparam = 'elementid='+elementid+'&bunitid='+id;
				
				// Making implementation default to business unit wise
				if(ele.selectedIndex != 1){
					$("#performance_app_flag-1").prop('disabled', false);
					$('input[name="performance_app_flag"][value="1"]').prop('checked', true);
				}
				
		}else if(elementid == 'department_id')
		{
			
				if(ele.selectedIndex != -1){
				 id = ele[ele.selectedIndex].value;
				}else{
					id = '';
				}
				dataparam = 'bunitid='+bunitid+'&deptid='+id;
		}
		else
		{
			dataparam = 'bunitid='+bunitid+'&deptid='+id;
		}
		
		if(dataparam!='')
		{
			$.ajax({
	                url: base_url+"/appraisalconfig/getdepartments/format/html",				
					type : 'POST',	
					data : dataparam,
					dataType: 'html',
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					success : function(response){	
						$.unblockUI();
						var obj = $.parseJSON(response);
						if(obj)
						{
							if(obj['implement'] != '' && obj['implement'] != 'null' && elementid == 'businessunit_id')
							{ 
	                        	$('input[name="performance_app_flag"][value="' + obj['implement'] + '"]').prop('checked', true);
	                        	if(obj['implement'] == 1)
	                        		{
	                        		
		                        		$('#department_id').html('');
		                        		$('#s2id_department_id').find('span').html('Select Department');
	                        		}
	                        	else if(obj['implement'] == 0)
	                        		{
	                        			displayDepartments("department_id");
	                        		}
	                        }
	                        
	                        if($('input[name=performance_app_flag]:checked').val() == 1)
	                    	{
	                        	$('#department_id').html('');
	                    		$('#s2id_department_id').find('span').html('Select Department');
	                    		$("#s2id_department_id").parent().parent().addClass('hiddenclass');
	                    	}
	                        
	                        if($('input[name=performance_app_flag]:checked').val() == 0)
	                    	{
	                    		$("#s2id_department_id").parent().parent().removeClass('hiddenclass');
	                    	}
						}	
					}
				});
		}
	}

	/**
	 * This function validates the service desk implementation to be based on business unit or department wise.
	 * This function is used to display departments based on business unit selection based on front end flag.
	 * @param ele
	 */

	function checkimplementfun(ele)
	{
            var value = $(ele).val();
            $('#errors-performance_app_flag-0').remove();
           if(value == 0 || value == 1)
            {
                if(value == 0)
                {
                    var bunitid = $('#businessunit_id').val();
                    if(bunitid == '')
                    {
                        $('#performance_app_flag-0').parent().parent().append("<span class='errors' id='errors-performance_app_flag-0'>Please select a business unit.</span>");
                        $('#performance_app_flag-0').removeAttr('checked');
                        $("#performance_app_flag-1").prop("checked", true);
                    }
                    else if(bunitid == 0)
                    {
                    	 $("#performance_app_flag-0").prop("checked", true);
                    	 chkduplicateimplementation(2);
                    }
                    else
                    {
                    	 chkduplicateimplementation(2);
                    }
                }
                else if(value == 1)
                {
                    chkduplicateimplementation(1);
                }
            }
	}

	/**
	 * This function is used to populate departments based on business unit seletion.
	 * @param eleId
	 */

	function displayDepartments(eleId)
	{
	  var id;
	  var params = '';
		
		  id= $("#businessunit_id").val();		
		  params = 'business_id='+id+'&con='+'appraisal_config';
		
		if(id !='')
		{
			$.ajax({
	                url: base_url+"/index/getdepartments/format/html",   				
					type : 'POST',	
					data : params,
					dataType: 'html',
					beforeSend: function () {
					$("#"+eleId).before("<div id='loader'></div>");
					$("#loader").html("<img src=" + domain_data + "public/media/images/loaderwhite_21X21.gif>");
					},
					success : function(response){
						   if($.trim(response) == 'nodepartments')
							 {
						    	$("#loader").remove();
					        	$('#s2id_'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'></span>");
								$("#errors-"+eleId).show();
								$("#errors-"+eleId).html("Departments are not added for this business unit.");
								$("#"+eleId).find('option').remove();
								$('#s2id_'+eleId).find('span').html('Select Department');
		                        								 
							 }
					         if(response != '' && response != 'null' && $.trim(response) != 'nodepartments')
							  {
							    if($("#errors-"+eleId).is(':visible'))
			                    $("#errors-"+eleId).hide();
								$('#s2id_'+eleId).find('span').html('Select Department');
	                            $("#loader").remove();
                                $("#"+eleId).find('option').remove();
								$("#"+eleId).html(response);
							  }
							  	
							}
				});
		}
		

	}

    function chkduplicateimplementation(flag, business_unit_index)
    {
		var bunitid = $("#businessunit_id").val();
		var bunittext = $("#businessunit_id option:selected").text();
		var deptid = $("#department_id").val();
		var performance_app_flag = $('input[name=performance_app_flag]:checked').val();
		if(deptid != '')
		{
			dataparam = 'bunitid='+bunitid+'&deptid='+id;
		}
		else
		{
			dataparam = 'bunitid='+bunitid;
		}
		if(bunitid !='')
        {
            $.ajax({				
                url: base_url+"/appraisalconfig/getbunitimplementation/format/json",    				
                type : 'POST',	
                data : dataparam,
                dataType: 'json',                        
                success : function(response)
                {	
                	 if(response['count'] !='' && response['count'] > 0)
                    {
                		// Show alert when user edit configuration which was used in pending appraisal
                    		if (business_unit_index == 1) {
                        		// When user selected 'No Business Unit'
                    			if ($("#id").length != 0 && $("#id").val().length > 0) 
                    			{
                    				jAlert('Applicability cannot be changed as requests are in pending state for "' + bunittext + '"');
                    			}
                        	} else {
                        		jAlert('Applicability cannot be changed as requests are in pending state for "'+bunittext+'" business unit');
                        	}
                    	
                    	  if(performance_app_flag == 1)
                        {
                            $('#performance_app_flag-1').removeAttr('checked');
                            $("#performance_app_flag-0").prop("checked", true);
                        }
                        else
                        {
                            $('#performance_app_flag-0').removeAttr('checked');
                            $("#performance_app_flag-1").prop("checked", true);
                        }
                        return false;
                    }	
                        else if(response['painitdata']!= '' &&   response['result'] !='')
                	{
                		if(response['painitdata'] > 0)
                		{
                			if (business_unit_index == 1) {
                        		// When user selected 'No Business Unit'
                				jAlert('Applicability cannot be changed as appraisal process already initialized for "' + bunittext + '"');
                        	} else {
                        		jAlert('Applicability cannot be changed as appraisal process already initialized for "' + bunittext + '" business unit');
                        	}                			
	                    	if(performance_app_flag == 1)
	                        {
	                    	    $('#performance_app_flag-1').removeAttr('checked');
	                            $("#performance_app_flag-0").prop("checked", true);
	                        }
	                        else
	                        {
	                            $('#performance_app_flag-0').removeAttr('checked');
	                            $("#performance_app_flag-1").prop("checked", true);
	                        }
	                    	return false;
                		}
                		else if(response['result'] != performance_app_flag && response['painitdata'] == 0)
                		{
                			jAlert('You are trying to change the applicability. All the previous details will be inactivated.');
                		}
                			
                	}
					
                    if(flag == 1)
                    {
                    	// Hide Departments select box
                		$("#s2id_department_id").parent().parent().addClass('hiddenclass');
                    }
                    else if(flag == 2)
                    {
                        $('#department_id').html('');
                        $('#s2id_department_id').find('span').html('Select Department');
                        $('#department_id').parent().parent().find('label').removeClass('required');
                        $("#s2id_department_id").parent().parent().removeClass('hiddenclass');	
                        displayDepartments("department_id");
                        $('#department_id').parent().parent().find('label').addClass('required');
                    }					
                }
            });
        }			
    }

	
	function getDepartments(id)
	{
	  var id;
	  var params = '';
		  params = 'business_id='+id;
		 elementid = $("businessunit_id").attr('id');
		if(id !='')
		{
			$.ajax({
	                url: base_url+"/feedforwardinit/getdept/format/html",   				
					type : 'POST',	
					data : params,
					dataType: 'html',
					beforeSend: function () {
						$("#"+elementid).before("<div id='loader'></div>");
						$("#loader").html("<img src=" + domain_data + "public/media/images/loaderwhite_21X21.gif>");
						},
					
					success : function(response){
						    if(response != '' && response != 'null' && $.trim(response) != 'nodepartments')
							  {
					        	  $("#loader").remove();
								  $("#department_id").html(response);
							  }
							  	
							}
				});
		}
		

	} 
	
	function performance_flag(eleId)
	{
	  var id;
	  var params = '';
		  id= $("#businessunit_id").val();	
		  params = 'business_id='+id;
		  elementid = $(eleId).attr('id');
		if(id !='')
		{
			$.ajax({
	                url: base_url+"/feedforwardinit/performanceflag/format/html",   				
					type : 'POST',	
					data : params,
					dataType: 'html',
					beforeSend: function () {
						$("#"+elementid).before("<div id='loader'></div>");
						$("#loader").html("<img src=" + domain_data + "public/media/images/loaderwhite_21X21.gif>");
						},
					success : function(response){
						
						    if(response != '' && response != 'null' && response == 1)
							  {
					        	  $("#loader").remove();
					        	  $("#department_id").hide();
								 
			                        	$('#department_id').html('');
			                    		$('#s2id_department_id').find('span').html('Select Department');
			                    		$("#s2id_department_id").parent().parent().addClass('hiddenclass');
			                    	
							  }
						    else if(response != '' && response != 'null' && response == 0)
						    	{ 
						    	 $("#loader").remove();
						    	$('#department_id').html('');
	                    		$('#s2id_department_id').find('span').html('Select Department');
	                    		$("#s2id_department_id").parent().parent().removeClass('hiddenclass');
						    	   getDepartments(id);
						    	  		                    	
						    	}
							  	
							}
				});
		}
		

	}
	
	function getappraisalmode(eleId)
	{
		  var id;
		  var params = '';
		  var bunitid = $("#businessunit_id").val();
		  var deptid = $("#department_id").val();
			dataparam = 'bunitid='+bunitid+'&deptid='+deptid;
			  elementid = $(eleId).attr('id');
			if(bunitid !='')
			{
				$.ajax({
		                url: base_url+"/feedforwardinit/getappraisalmode/format/html",   				
						type : 'POST',	
						data : dataparam,
						dataType: 'html',
						
						success : function(response){
							alert(response);
							    if(response != '' && response != 'null' )
								  {
						        	  $("#loader").remove();
									$("#appraisal_mode").find('span').html(response);
								  }
							    else if(response != '' && response != 'null' && response == 0)
							    	{ 
							    	  		                    	
							    	}
								  	
								}
					});
			}
	}
	
	
function displayorgtabs(tabflag)
{
	if(tabflag == 1)
		{
			$("#existing-orghead").addClass("act");
			$("#new-orghead").removeClass("act");
			$("#currentorgdropdown").show();
			$("#neworgheaddiv").hide();
			if($("#currentorghead").val() !='')
				{
					$("#existingorgdiv").show();
				}
			$("#selectedtab").val(tabflag);
		}else
		{
			$("#existing-orghead").removeClass("act");
			$("#new-orghead").addClass("act");
			$("#currentorgdropdown").hide();
			$("#existingorgdiv").hide();
			$("#neworgheaddiv").show();
			$("#selectedtab").val(tabflag);
			
			// Change cancel button name to redirect user to specific page employees grid or previously visited page
			$("#btn_cancel").attr('name', 'add_new_org_head');
		}	
}
function validatequestionname(ele)
{
    var elementid = $(ele).attr('id');
    var reqValue = $(ele).val();
    var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
    $('#errors-'+elementid).remove();
    if(reqValue == '')
    {
            $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter question.</span>");
    }		
    else if(!re.test(reqValue))
    {
            $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid question.</span>");
    }
    else
    {
            $('#errors-'+elementid).remove();
    }
}
function displayorgfields(userid)
{
	if(userid)
	{	
		$.ajax({
	     	url: base_url+"/employee/getindividualempdetails/format/html",
	     	type : 'POST',	
			data : 'userid='+userid,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$("#existingorgdiv").show();
			    $("#existingorgdiv").html(response);
			    
			}
		});
	}else
	{
		$("#existingorgdiv").hide();
	}	
}

function validateratingsonsubmit()
{
    var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
    var errorcount = 0;
    var ratings = $("#appraisal_rating").val();
  	for(var i=0;i<ratings;i++)
  	{ 
  	      
	       var elementid = $('#rating_text_'+i).attr('id');
		    var reqValue = $('#rating_text_'+i).val();

			   if(reqValue == '')
            {    $('#errors-'+elementid).remove();
            	 $('#'+elementid).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter rating text.</span>");
                errorcount++;
            }
            else if(!re.test(reqValue))
            {  $('#errors-'+elementid).remove();
            	$('#'+elementid).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid rating text.</span>");
                errorcount++;
            }
            else
            {
                  for(var k=0;k<ratings;k++)
                {
                	var nextVal = $('#rating_text_'+(i+k+1)).val();
    			    var nextid = $('#rating_text_'+(i+k+1)).attr('id');
                   if(reqValue == nextVal )
                   {   $('#errors-'+nextid).remove(); 
                	   $('#'+nextid).parent().append("<span class='errors' id='errors-"+nextid+"'>This text already exists.</span>");
                       errorcount++;
                   }
                   
            }
  	}
    
  	 }
    if(errorcount == 0 )
    { 
          $.blockUI({ width:'50px',message: $("#spinner").html() });
        document.getElementById("formid").submit();
	 }
   

}


    function validateratingtext(ele)
    { 
    	 var errorcount = 0;
    	 var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
         var elementid = $(ele).attr('id');
         var reqValue = $(ele).val();
        $('#errors-'+elementid).remove();
        if($(ele).val() == '')
        { 
            $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter rating text.</span>");
            errorcount++;
        }
        else if(!re.test(reqValue))
        { 
            
            $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid rating text.</span>");
            errorcount++;
        }
        else
        {
        	$('#errors-'+elementid).remove();
        }

   
  }
	
    function validateorgheadjoiningdate()
    {
    	var joiningdate = $("#date_of_joining_head").val();
    	if(joiningdate!='' && typeof(joiningdate)!='undefined')
    	{
    		$("#date_of_joining_head").before("<div id='loader'></div>");
       	 	$("#loader").html("<img src='" + domain_data + "public/media/images/loaderwhite_21X21.gif'>");
       	 	$('#errors-date_of_joining_head').remove();	
    		$.post(base_url+"/index/validateorgheadjoiningdate",{joiningdate:joiningdate},function(data){
    			$("#loader").remove();
    	        if(data.result == 'no')
    	        {
    	            $("#date_of_joining_head").parent().append("<span class='errors' id='errors-date_of_joining_head'>Date of joining should be greater than organization started on.</span>");
    	            $('#date_of_joining_head').val('');
    	        }
    			},'json');
    	}
    }
    
    function validateTextInput(ele,msg)
    {
    	
    	var elementid = $(ele).attr('id');
    	var value = $(ele).val();
    	var re = /^[a-zA-Z0-9\- ]+$/;
    	$('#errors-'+elementid).remove();
    	if(value == '')
    	{
    		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter "+msg+".</span>");
    	}		
    	else if(!re.test(value))
    	{
    		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid "+msg+".</span>");
    	}
    	else
    	{
    		$('#errors-'+elementid).remove();
    	}
    }

    function validateTextArea(ele,msg) {
    	var elementid = $(ele).attr('id');
    	var value = $(ele).val();
    	$('#errors-'+elementid).remove();
    	if(value == '')
    	{
    		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter "+msg+".</span>");
    	}		
    	else
    	{
    		$('#errors-'+elementid).remove();
    	}
    }

    function validateNumberInput(ele,msg)
    {
    	
    	var elementid = $(ele).attr('id');
    	var value = $(ele).val();
    	var re = /^[0-9]+$/;
    	$('#errors-'+elementid).remove();
    	$(ele).removeClass('borderclass');
    	if(value == '')
    	{
    		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter leaves.</span>");
    		$(ele).addClass('borderclass');
    	}		
    	else if(!re.test(value))
    	{
    		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter only number.</span>");
    		$(ele).addClass('borderclass');
    	}
    	else
    	{
    		$('#errors-'+elementid).remove();
    		$(ele).removeClass('borderclass');
    	}
    }
	
    function updateleavedetails(leaveid,controllername)
    {
    	if(!leaveid)
    	{
    		return false;
    	}	
    		$.ajax({
    	     	url: base_url+"/leaverequest/updateleavedetails/format/html",
    	     	type : 'POST',	
    	     	data: {
    	            id: leaveid,
    	            status: $("#leaveactionid").val(),
    	            comments: $("#comments").val(),
    	        },
    			dataType: 'json',
    			success : function(response){	
    				$.unblockUI();
    				if(response['result'] == 'success'){
	    				$("#leave_success_div").show();
	    			    $("#leave_success_div").prepend(response['msg']);
	    			}
    				setTimeout(function(){
    				    closeiframepopup(controllername,''); 
    				},2000);
    			    
    			}
    		});
    	
    }
	
