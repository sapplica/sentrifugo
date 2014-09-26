
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
	
	function fnAddRemoveProjectUser(addremove,userId,userName,imgName)
	{
		
		if(userId != '')
		{
			//Removed added or removed User Div. If addremove is 0->Delete 1->Add
			if(addremove == 1)
			{
				//To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)
				
				if ($(".users_left_list_div.users_left_list").length == 1) 
				{
					if($(".no_left_data_found").length < 1)
					{
						var no_user_data_div = '<div class="users_left_list_div no_left_data_found" style="display:none;"><span class="values">Employees are not available.</span> </div>'
						
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
				
				
				var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(0,\''+userId+'\',\''+addslashes(userName)+'\',\''+imgName+'\');" style="cursor:pointer;" class="users_right_list_div users_right_list user_div_'+userId+'" subject ="'+userId+'" alt="Remove" title="Remove" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+base_url+'/public/media/images/default-profile-pic.jpg\'" src="'+base_url+'/public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span></div>';
				
				if ($(".users_right_list_div").length > 0) 
				{
					$(".users_right_list_div:first").before(newDivToAppend);
				}
				
				$(".no_right_data_found").hide();			
				
								
				
			}
			else if(addremove == 0)
			{		
				
				//To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)
				
				if ($(".users_right_list_div.users_right_list").length == 1) 
				{
					if($(".no_right_data_found").length < 1)
					{
						var no_user_data_div = '<div class="users_right_list_div no_right_data_found" style="display:none;"><span class="values">Employees are not available.</span> </div>'
						
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
				
				//To check whether no user exists div exists(If exists then display block div of no user exists)				
				if ($(".no_right_data_found").length > 0) 
				{
					$(".no_right_data_found").show();
				}
				//End				
			
				$(".no_search_results").hide();
				
				
				var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(1,\''+userId+'\',\''+addslashes(userName)+'\',\''+imgName+'\');" style="cursor:pointer;" class="users_left_list_div users_left_list user_div_'+userId+'" subject ="'+userId+'" alt="Add" title="Add" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+base_url+'/public/media/images/default-profile-pic.jpg\'" src="'+base_url+'/public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span></div>';
				
				
				
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
			
			
			$("#search_emp_by_name").val('');
			$('div.users_left_list').show();
			$('#idclear').hide();
														
			$('#actionButtonsDiv').show();
		}
		
		
	}
	
	
	function fetchgroupdata()
	{
		var groupid = $("#group_id").val();
		var appraisalid = $("#appraisalid").val();
		if(groupid && appraisalid)
			{
			$.ajax({
	         	url: base_url+"/appraisalinit/getgroupedemployees/format/html",
	         	type : 'POST',	
				data : 'groupid='+groupid+'&appraisalid='+appraisalid,
				dataType: 'html',
				beforeSend: function () {
					$.blockUI({ width:'50px',message: $("#spinner").html() });
				},
				success : function(response){	
					$.unblockUI();
				    $(".invfrnds_confirm").html(response);
				    
				}
			});
			}
	}
	
	
	function fnSaveMappedEmployees()
	{
		
		var errorcount = 0;
	    var groupid = $('#group_id').val();
	    var appraisalid = $("#appraisalid").val();
	    var divlength = $("[class^='users_right_list_div users_right_list user_div_']").length;
	    var finalids='';
	    
	    if(divlength == 0)
    	{
	    	$(".no_right_data_found span").html('Please add employees to map.');
	    	errorcount++;
    	}
	    
	    if(errorcount == 0)
	    	{
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
	    	}
	}
	
