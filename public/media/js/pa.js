/***
 * RAMAKRISHNA
***/

/**
 * This function is used to construct accordion in performance appraisal step - 2.
 * @param {integer} init_id = id of appraisal.
 * @returns {html} HTML of accordion
 */
function construct_accordion(init_id,call)
{
    if(init_id != '')
    {
        $('#id_acc_container').html('');
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $.post(base_url+"/appraisalinit/constructacc/format/html",{init_id:init_id,call:call},function(data){
            $('#id_acc_container').html(data);
            $.unblockUI();
            
        },'html');
    }
}
/**
 * This function is used to construct accordion in performance appraisal step - 2.
 * @param {integer} init_id = id of appraisal.
 * @returns {html} HTML of accordion
 */
function construct_rep_accordion(init_id)
{
    if(init_id != '')
    {
        $('#id_acc_container').html('');
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $.post(base_url+"/appraisalinit/constructreportacc/format/html",{init_id:init_id},function(data){
            $('#id_acc_container').html(data);
            $.unblockUI();
        },'html');
    }
}
/**
 * This function is used to view the manager group in appraisalmanager/showgroups.
 * @param {integer} group_id  = id of the group
 * @returns {html} HTML of group.
 */
function view_manager_group(group_id,group_name)
{
    if(group_id != '')
    {
        var appraisal_id = $('#idhid_appraisal_id').val();
        var manager_id = $('#idhid_manager_id').val();
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        
        $('#id_create_group').html('');
        var params = {appraisal_id:appraisal_id,manager_id:manager_id,group_id:group_id,group_name:group_name};
        $.post(base_url+"/appraisalmanager/viewgroup/format/html",params,function(data){
        	$('#id_create_group').show();
            $('#id_create_group').html(data);
            $.unblockUI();   
        },'html');
    }    
}
/**
 * This function is used to create new group for manager in appraisalmanager/showgroups.
 * @param {integer} appraisal_id = id of appraisal
 * @param {integer} manager_id   = id of manager
 * @param {string} flag          = action type like add,edit
 * @param {integer} group_id     = id of group
 * @returns {html} HTML to create new group.
 */
function create_manager_group(appraisal_id,manager_id,flag,group_id)
{
    if(appraisal_id != '' && manager_id != '')
    {        
        $('#id_create_group').html('');
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        var params = {appraisal_id:appraisal_id,manager_id:manager_id,flag:flag,group_id:group_id};
        $.post(base_url+"/appraisalmanager/createnewgroup/format/html",params,function(data){
        	 $('#id_create_group').show();
            $('#id_create_group').html(data);
            $.unblockUI();   
        },'html');
    }
}
/**
 * This function is used to edit group created by manager.
 * @param {integer} group_id = id of group
 * @returns {HTML} HTML of group
 */
function edit_manager_group(group_id)
{
    if(group_id != '')
    {
        var appraisal_id = $('#idhid_appraisal_id').val();
        var manager_id = $('#idhid_manager_id').val();
        create_manager_group(appraisal_id,manager_id,'edit',group_id);
    }
}
/**
 * This function is used to show groups which are created by manager.
 * @param {integer} appraisal_id = id of appraisal
 * @returns {HTML}  HTML of groups.
 */
function show_groups(appraisal_id)
{
    if(appraisal_id != '')
    {
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $('#iddiv_showgroups').html('');
        $.post(base_url+"/appraisalmanager/showgroups/format/html",{appraisal_id:appraisal_id},function(data){
            $('#iddiv_showgroups').html(data);
            $.unblockUI();   
        },'html');
    }
}

/**
 * This function is used to show groups in view format which are created by manager.
 * @param {integer} appraisal_id = id of appraisal
 * @returns {HTML}  HTML of groups.
 */
function show_view_groups(appraisal_id)
{
    if(appraisal_id != '')
    {
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $('#iddiv_showgroups').html('');
        $.post(base_url+"/appraisalmanager/showviewgroups/format/html",{appraisal_id:appraisal_id},function(data){
            $('#iddiv_showgroups').html(data);
            $.unblockUI();   
        },'html');
    }
}
/**
 * This function is used to delete group which created by manager in initialisation.
 * @param {integer} appraisal_id = id of appraisal
 * @param {integer} manager_id   = id of manager
 * @param {integer} group_id     = id of group
 * @returns {json} Success/failure messages in form of json.
 */
function delete_manager_group(appraisal_id,manager_id,group_id)
{
	var groupname = $("#groupname_"+group_id).html();
    jConfirm("Are you sure you want to delete "+groupname+" group?", "Delete group", function(r) 
    {
        if(r==true)
        {
            if(appraisal_id != '' && manager_id != '' && group_id != '')
            {
                $.blockUI({ width:'50px',message: $("#spinner").html() });
                $.post(base_url+"/appraisalmanager/deletemanagergroup",{appraisal_id:appraisal_id,manager_id:manager_id,group_id:group_id},function(data){
                    $.unblockUI();
                    $("#groupdiv_"+group_id).remove();
                    if(data['empcount'] > 0)
					{
                    	if($("#iddiv_create").length == 0)
                    		$("#iddiv_submit_init").before("<div class='create_new_group' style='margin-left: 0;' onclick='create_manager_group(\""+data['appraisalid']+"\",\""+data['managerid']+"\",\"add\",\"\")' id='iddiv_create'>Create New Group</div>");
					}
                    if($(".groupeddiv").length ==0)
                	{
                    	$("#iddiv_showgroups").append('<div class="newgroup_msg managerresponsediv_msg">Groups are not configured yet</div>');
                	}
                    $("#group_cnt").val(data['empcount']);
                    
                    //show_groups(appraisal_id);
                    $('#id_create_group').html('');
                    $('#id_create_group').hide();
                    successmessage_changestatus(data.msg,data.status,'appraisalmanager');                                 
                },'json');
            }
        }
    });
}
/**
 * This function is used to submit manager initialisation after completing his task.
 * @param {string} appraisal_id = id of appraisal in encrypted format.
 * @param {string} manager_id   = id of manager in encrypted format.
 * @returns {json} Success/failure messages in form of json.
 */
function submit_manager_initilisation(appraisal_id,manager_id)
{
	var group_cnt = $("#group_cnt").val();
	var msg = '';
	if(group_cnt > 0)
		msg = " Groups are not configured yet. Are you sure you want to submit the appraisal?";
	else
		msg = "Are you sure, you want to submit your status?";
    jConfirm(msg, "Submit Initialization", function(r) 
    {
        if(r==true)
        {
            if(appraisal_id != '' && manager_id != '')
            {
                $.blockUI({ width:'50px',message: $("#spinner").html() });
                $('#id_create_group').html('');
                $('#id_create_group').hide();
                $.post(base_url+"/appraisalmanager/submitmanager",{appraisal_id:appraisal_id,manager_id:manager_id},function(data){
                    $.unblockUI();
                    window.location.href= base_url+"/appraisalmanager";
                    /*$('#iddiv_create').remove();
                    $('#iddiv_submit_init').remove();
                    successmessage_changestatus(data.msg,data.status,'appraisalmanager');*/
                    
                },'json');
            }
        }
    });
}
function discard_step2(init_id,management_appraisal)
{
    $.blockUI({ width:'50px',message: $("#spinner").html() });
    $.post(base_url+"/appraisalinit/discardsteptwo",{init_id:init_id,management_appraisal:management_appraisal},function(data){        
        $.unblockUI();
        successmessage_changestatus(data.message,data.status,'appraisalinit');
        setTimeout(function(){
	    location.reload(true);
        },3000);
    },'json');
}
function addlinemanager(init_id,context,line1_id,manager_levels,editid)
{
    $('#idline_content').html('');
    //$('#idline_content').slideUp('slow','swing');
    //$('div[id^="idemps_"]').slideUp('slow','swing');
    //$('.acc_edit_div').slideUp('slow','swing');
    $('.acc_edit_div').html('');
    $('div[id^="idemps_"]').html('');
    $.post(base_url+"/appraisalinit/addlinemanager/format/html",{init_id:init_id,context:context,line1_id:line1_id,levels:manager_levels},function(data){
        if(context=='edit')
        {
        	$("#div_"+editid).show();
        	$("#div_"+editid).html(data);
        }else
        {
        	$('#idline_content').html(data);
			$('#idline_content').show();
        }	
    },'html');
}
function display_managers(init_id,context,line1_id)
{
    var levels = $('#idsel_levels').val();
    $('#iddiv_managers').html('');

    $.post(base_url+"/appraisalinit/displaymanagers/format/html",{type:'line',levels:levels,init_id:init_id,context:context,line1_id:line1_id},function(data){
        $('#iddiv_managers').html(data);
    },'html');
}
function conf_btn_click(id,app_id,call)
{    				 
    if(id == 'id_set_line')
    {
        $('#idhid_choose_option').val('line');
        $('#options_div').hide();
        $('.total-form-controller').hide();
        $('#iddiv_mcontent').html('');
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $.post(base_url+"/appraisalinit/displayline/format/html",{init_id:app_id,call:call},function(data){
            $('#iddiv_mcontent').html(data);
            $.unblockUI();
        },'html');
    }
    else if(id == 'id_set_report')
    {
        $('#idhid_choose_option').val('report');
        $('#options_div').hide();
        $('#iddiv_mcontent').html('');
        $.blockUI({ width:'50px',message: $("#spinner").html() });
        $.post(base_url+"/appraisalinit/displayreport/format/html",{init_id:app_id,call:call},function(data){
            $('#iddiv_mcontent').html(data);
            // Remove 'Discard' button when user selected option 'Choose by Organization Hierarchy'
            $("#idbtn_discard").remove();
            $.unblockUI();
        },'html');
    }        
}

function group_emp_ready_fn()
{
    $('#idcancelbtn').click(function(){
        //$('#idline_content').html('');
        $('#idline_content').slideUp('slow','swing');
        $('.acc_edit_div').slideUp('slow','swing');
    });
    $('#idbtn_submit').click(function(){
        
        var ids_arr = new Array();
	var ids_data = $("#existetd_mem_str" ).val();
	if(ids_data != '')
	{
            ids_arr = ids_data.split(',');
	}	
		    
        /*to display error message when employees are not selected*/
        if(ids_arr.length<1)
        {
            $("#no_members_error").html('Please add atleast one employee.');
            $('#no_members_error').show();
            setTimeout(function(){
                $('#no_members_error').fadeOut('slow');
            },3000);
        }
        else
        {            
            $('#idfrm_conf_mng').submit();
        }
    });
    $('#idclear').hide();
    $('#idclear_right').hide();
		
    if($.trim($('#search_emp_by_name').val()).length>0)
    {
        $('#idclear').show();
    }
    else
    {
        $('#idclear').hide();
    }
    if($.trim($('#search_emp_by_name_right').val()).length>0)
    {
        $('#idclear_right').show();
    }
    else
    {
        $('#idclear_right').hide();
    }
    
    $('.users_list_left').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
    $('.users_list_right').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
    
    $('#search_emp_by_name').bind('keyup', function() {

        var txt = $.trim($('#search_emp_by_name').val());
        $('div.users_left_list').hide();
        $('div.users_left_list').each(function(){
           if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
               $(this).show();
           }
        });

        if($('div.users_left_list:visible').length < 1)
        {
            $('div.no_search_results').show();
        }
        else
        {
            $('div.no_search_results').hide();
        } 

        if(txt.length>0)
        {
            $('#idclear').show();
            $(".no_left_data_found").hide();
        }
        else
        { 
            $('#idclear').hide();		
        }
    });
    $('#search_emp_by_name_right').bind('keyup', function() {
		var txt = $.trim($('#search_emp_by_name_right').val());
		$('div.users_right_list').hide();
			$('div.users_right_list').each(function(){
			   if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
				   $(this).show();
			   }
			});
				
		if($('div.users_right_list:visible').length < 1)
		{
				$(".no_right_data_found").hide();
				$('div.no_search_results_right').show();
		}
		else
		{
				$('div.no_search_results_right').hide();
		} 
		
		if(txt.length>0)
			{
				$('#idclear_right').show();
				$(".no_right_data_found").hide();
		}
		else
		{ 
				$('#idclear_right').hide();		
		}
    });

    $('#search').bind('click', function() {

        var txt = $.trim($('#search_emp_by_name').val());
        $('div.users_left_list').hide();
        $('div.users_left_list').each(function(){
           if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
               $(this).show();
           }
        });
		
	if($('div.users_left_list:visible').length < 1)
	{
            $('div.no_search_results').show();
	}
	else
	{
            $('div.no_search_results').hide();
	} 
	
	if(txt.length>0)
        {
            $('#idclear').show();
            $(".no_left_data_found").hide();
	}
        else
        { 
            $('#idclear').hide();
	}
    });
    
    $('#search_right').bind('click', function() {

	var txt = $.trim($('#search_emp_by_name_right').val());
	$('div.users_right_list').hide();
        $('div.users_right_list').each(function(){
           if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
               $(this).show();
           }
        });
			
	if($('div.users_right_list:visible').length < 1)
	{
            $('div.no_search_results_right').show();
	}
	else
	{
            $('div.no_search_results_right').hide();
	} 
	
	if(txt.length>0){
		$('#idclear_right').show();
		$(".no_right_data_found").hide();
	}else{ 
		$('#idclear_right').hide();
	}
    });
}
function clearSearchData()
{	
    $('#search_emp_by_name').val('');
    $('#idclear').hide();
	
    var txt = $.trim($('#search_emp_by_name').val());
    $('div.users_left_list').hide();
    $('div.users_left_list').each(function(){
        if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1)
        {
            $(this).show();
        }
    });
	
    $('div.no_search_results').hide();			
}
function clearSearchData_right()
{	
	$('#search_emp_by_name_right').val('');
    $('#idclear_right').hide();
	
    var txt = $.trim($('#search_emp_by_name_right').val());
    $('div.users_right_list').hide();
    $('div.users_right_list').each(function(){
        if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1)
        {
            $(this).show();
        }
    });
    if(txt == '')
    	{
    	$('.no_search_results_right').hide();
    	}
    
    			
}

function fnAddRemoveProjectUser(addremove,userId,userName,imgName,employee_id,jobtitle_name)
{	
	var jobtitle_length=jobtitle_name.length;
	var	 jobtitle;
	if(jobtitle_length<=17)
	      jobtitle=jobtitle_name;
	else
		 jobtitle=jobtitle_name.substring(0,15) + '...';
    if(userId != '')
    {
    	 var emp_count='';
        //Removed added or removed User Div. If addremove is 0->Delete 1->Add
        if(addremove == 1)
        {
            //To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)				
            if ($(".users_left_list_div.users_left_list").length == 1) 
            {
                if($(".no_left_data_found").length < 1)
                {		
                    var no_user_data_div = '<div class="users_left_list_div no_left_data_found" style="display:none;"><span class="values">Employees are not available.</span> </div>';
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

        	
     
            var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(0,\''+userId+'\',\''+addslashes(userName)+'\',\''+imgName+'\',\''+addslashes(employee_id)+'\',\''+addslashes(jobtitle_name)+'\');" style="cursor:pointer;" class="users_right_list_div users_right_list user_div_'+userId+'" subject ="'+userId+'" alt="Remove" title="Remove" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+ domain_data + 'public/media/images/default-profile-pic.jpg\'" src="'+ domain_data + 'public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span> <span class="member_id">'+employee_id+'</span> <span class="member_jname" title="'+jobtitle_name+'">'+jobtitle+'</span> </div>';
            if ($(".users_right_list_div").length > 0) 
            {	
				$("#search_emp_by_name_right").val('');				
                $(".users_right_list_div:first").before(newDivToAppend);
				$(".users_right_list_div").show();
				$(".no_search_results_right").hide();
            }
				
            $(".no_right_data_found").hide();	
            
            emp_count = parseInt($("#emp_count_val").html());
            emp_count = emp_count + 1;
            //for calculating users div height on right-side
            calculateUsersDivHeight(1);

        }
        else if(addremove == 0)
        {						
            //To check whether current div is last div (If it is last div then create new div of no user exists and make it as display:none)    			
            if ($(".users_right_list_div.users_right_list").length == 1) 
            {
                if($(".no_right_data_found").length < 1)
                {		
                    var no_user_data_div = '<div class="users_right_list_div no_right_data_found" style="display:none;"><span class="values">Add employees to group.</span> </div>'						
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
                $(".no_search_results_right").hide();
            }
            //End
            $(".no_search_results").hide();								
            var newDivToAppend = '<div onclick="javascript:fnAddRemoveProjectUser(1,\''+userId+'\',\''+addslashes(userName)+'\',\''+imgName+'\',\''+addslashes(employee_id)+'\',\''+addslashes(jobtitle_name)+'\');" style="cursor:pointer;" class="users_left_list_div users_left_list user_div_'+userId+'" subject ="'+userId+'" alt="Add" title="Add" name="'+addslashes(userName)+'"><span class="values"><div class="profile_img"><img width="28px" height="28px" onerror="this.src=\''+ domain_data + 'public/media/images/default-profile-pic.jpg\'" src="'+ domain_data + 'public/uploads/profile/'+imgName+'"></div> </span> <span class="member_name">'+userName+'</span><span class="member_id">'+employee_id+'</span> <span class="member_jname" title="'+jobtitle_name+'">'+jobtitle+'</span> </div>';
												
            if ($(".users_left_list_div").length > 0) 
            {				
                $(".users_left_list_div:first").before(newDivToAppend);
            }				
            $(".no_left_data_found").hide();

            emp_count = parseInt($("#emp_count_val").html());
            emp_count = emp_count - 1;
            
            //for calculating users div height on left-side
            calculateUsersDivHeight(0);
        }
        $("#emp_count_val").html(emp_count);						
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

function calculateUsersDivHeight(con)
{
	var heightStr = 180;
	var user_left_list = '';
	if(con == 1)
		user_left_list = $(".users_right_list").length;
	else if(con == 0)
		user_left_list = $(".users_left_list").length;
	   
	 	if(user_left_list > 2)
	    	heightStr = 350;
	 	
	 	if(con == 1)
	 		$(".users_list_right").height(heightStr);
	 	else 
	 		$(".users_list_left").height(heightStr);
}
/***
 * END
 */





/***
 * SANDEEP
***/

/***
 * END
 */




/***
 * MAINAK
***/

/**
 * Manager Question Functions
 */
function appendmgrcheckboxtext(id)
{
	
		$("#hiddentr_"+id).remove();
		
		var queshtml = $('#queshtml_'+id).html(); 
		var html = "<tr id='hiddentr_"+id+"'>";
		html+="<td class='question_field'>"+queshtml+"</td>";
		html+="<td class='field_width'>";
		if($("#mgrcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled checked type='checkbox'>Manager Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled type='checkbox'>Manager Comments</div>";		
                if($("#mgrrating_"+id).prop('checked'))
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled checked type='checkbox'>Manager Ratings</div>";	
                else
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled type='checkbox'>Manager Ratings</div>";		
	   	if($("#empcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Employee Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Employee Comments</div>";		
	   	if($("#empratings_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Employee Ratings</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Employee Ratings</div>";	
		html+="</td>";
		html+="</tr>";
		$("#questiontable").append(html);
		
		/*TO append height to hidden child div */
		appendheighttodiv(2);
}

function prependmgrcheckboxtext(id)
{
	
		$("#hiddentr_"+id).remove();
		
		var queshtml = $('#queshtml_'+id).html(); 
		var html = "<tr id='hiddentr_"+id+"'>";
		html+="<td class='question_field'>"+queshtml+"</td>";
		html+="<td class='field_width'>";
	   	if($("#empcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Employee Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Employee Comments</div>";		
	   	if($("#empratings_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Employee Ratings</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Employee Ratings</div>";	
		html+="</td>";
		html+="</tr>";
		if($(".selectallcls").prop('checked'))		
			$("#childqsdiv tr:first").after(html);
		
		/*TO append height to hidden child div */
		appendheighttodiv(2);
}

function checkmgrchildtd(ele)
{
	var id = $(ele).attr('ques_id');
	var allCheckBox = $("[class='checkallcls']");	
	var count_checked = allCheckBox.filter(":checked").length;
	var questioncount = $("#questioncount").val(); 
	
	/* Removing permission for childdiv if parent is unchecked
	 * Removing permission for parentclass 
	 * removing hidden div
	 * */
	if(!$(ele).prop('checked'))
	{
		$("#mgrcmnt_"+id).prop('checked',$(ele).prop('checked'));
		$("#mgrrating_"+id).prop('checked',$(ele).prop('checked'));
		$("#empcmnt_"+id).prop('checked',$(ele).prop('checked'));
		$("#empratings_"+id).prop('checked',$(ele).prop('checked'));
		$("#selectall").prop('checked',$(ele).prop('checked'));
		$("#hiddentr_"+id).remove();
	}else
	{
		if(count_checked == questioncount)
		{
			$("#selectall").prop('checked',$(ele).prop('checked'));
		}	
		appendmgrcheckboxtext(id);
	}
	appendheighttodiv(2);
	
}

function checkmgrparenttd(ele)
{
	var id = $(ele).attr('ques_id');
	appendmgrcheckboxtext(id);
	if($(ele).prop('checked'))
	{
		$("#check_"+id).prop('checked',$(ele).prop('checked'));
	}
	if(!$("#mgrcmnt_"+id).prop('checked') && !$("#mgrrating_"+id).prop('checked') && !$("#empcmnt_"+id).prop('checked')&& !$("#empratings_"+id).prop('checked'))
	{
		$("#check_"+id).prop('checked',$(ele).prop('checked'));
		$("#hiddentr_"+id).remove();
	}
	appendheighttodiv(2);
}

/*
 * End -  Manager Question Functions
 */

/*
 * Start - Hr Question Functions
 */
function appendheighttodiv(flag)
{
	var rowCount = $('#questiontable tr').length;
	// if(flag==1)
	// {	
		// if(rowCount > 7)
		// {
			// $("#questiontable").height(450);
		// }else
		// {
			// $("#questiontable").height(0);
		// }	
	// }else
	// {
		if(rowCount > 7)
		{
			$("#questiontable").height(450);
		}else
		{
			$("#questiontable").height(0);
		}	
		
	// }	
}
function appendcheckboxtext(id)
{
	
		$("#hiddentr_"+id).remove();
		
		var queshtml = $('#queshtml_'+id).html(); 
		var html = "<tr id='hiddentr_"+id+"'>";
		html+="<td class='question_field'>"+queshtml+"</td>";
		html+="<td class='field_width'>";
		if($("#mgrcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled checked type='checkbox'>Manager Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled type='checkbox'>Manager Comments</div>";		
                if($("#mgrrating_"+id).prop('checked'))
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled checked type='checkbox'>Manager Ratings</div>";	
                else
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled type='checkbox'>Manager Ratings</div>";	
	   	if($("#empcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Employee Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Employee Comments</div>";		
	   	if($("#empratings_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Employee Ratings</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Employee Ratings</div>";	
		html+="</td>";
		html+="</tr>";
		$("#questiontable").append(html);
		
		/*TO append height to hidden child div */
		appendheighttodiv(1);
			
}

function ff_appendcheckboxtext(id)
{	
	$("#hiddentr_"+id).remove();	
	var queshtml = $('#queshtml_'+id).html(); 
	var html = "<tr id='hiddentr_"+id+"'>";
	html+="<td class='question_field'>"+queshtml+"</td>";
	html+="<td class='field_width'>";
	if($("#empcmnt_"+id).prop('checked'))
		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Comments</div>";
	else
		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Comments</div>";		
	if($("#empratings_"+id).prop('checked'))
		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Ratings</div>";
	else
		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Ratings</div>";	
	html+="</td>";
	html+="</tr>";
	$("#questiontable").append(html);	
	/*TO append height to hidden child div */
	appendheighttodiv(1);			
}

function prependcheckboxtext(id)
{
	
		$("#hiddentr_"+id).remove();
		
		var queshtml = $('#queshtml_'+id).html(); 
		var html = "<tr id='hiddentr_"+id+"'>";
		html+="<td class='question_field'>"+queshtml+"</td>";
		html+="<td class='field_width'>";
		if($("#mgrcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled checked type='checkbox'>Manager Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled type='checkbox'>Manager Comments</div>";		
                if($("#mgrrating_"+id).prop('checked'))
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled checked type='checkbox'>Manager Ratings</div>";	
                else
                    html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled type='checkbox'>Manager Ratings</div>";	
	   	if($("#empcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Employee Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Employee Comments</div>";		
	   	if($("#empratings_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Employee Ratings</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Employee Ratings</div>";	
		html+="</td>";
		html+="</tr>";
		if($(".selectallcls").prop('checked'))
			$("#childqsdiv tr:first").after(html);
		
		/*TO append height to hidden child div */
		appendheighttodiv(1);
}

function ff_prependcheckboxtext(id)
{
	
		$("#hiddentr_"+id).remove();
		
		var queshtml = $('#queshtml_'+id).html(); 
		var html = "<tr id='hiddentr_"+id+"'>";
		html+="<td class='question_field'>"+queshtml+"</td>";
		html+="<td class='field_width'>";
		// if($("#mgrcmnt_"+id).prop('checked'))
	   		// html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled checked type='checkbox'>Manager Comments</div>";
	   	// else
	   		// html+="<div class='comments_div_fiel'> <input class='mgrcmntcls' disabled type='checkbox'>Manager Comments</div>";		
		// if($("#mgrrating_"+id).prop('checked'))
			// html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled checked type='checkbox'>Manager Ratings</div>";	
		// else
			// html+="<div class='comments_div_fiel'> <input class='mgrratingcls'  disabled type='checkbox'>Manager Ratings</div>";	
	   	if($("#empcmnt_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled checked type='checkbox'>Comments</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empcmntcls' disabled type='checkbox'>Comments</div>";		
	   	if($("#empratings_"+id).prop('checked'))
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled checked type='checkbox'>Ratings</div>";
	   	else
	   		html+="<div class='comments_div_fiel'> <input class='empratingcls' disabled type='checkbox'>Ratings</div>";	
		html+="</td>";
		html+="</tr>";
		if($(".selectallcls").prop('checked'))
			$("#childqsdiv tr:first").after(html);
		
		/*TO append height to hidden child div */
		appendheighttodiv(1);
}

function checkchildtd(ele)
{
	var id = $(ele).attr('ques_id');
	var allCheckBox = $("[class='checkallcls']");	
	var count_checked = allCheckBox.filter(":checked").length;
	var questioncount = $("#questioncount").val(); 
	
	/* Removing permission for childdiv if parent is unchecked
	 * Removing permission for parentclass 
	 * removing hidden div
	 * */
	if(!$(ele).prop('checked'))
	{
		$("#mgrcmnt_"+id).prop('checked',$(ele).prop('checked'));
		$("#mgrrating_"+id).prop('checked',$(ele).prop('checked'));
		$("#empcmnt_"+id).prop('checked',$(ele).prop('checked'));
		$("#empratings_"+id).prop('checked',$(ele).prop('checked'));
		$("#selectall").prop('checked',$(ele).prop('checked'));
		$("#hiddentr_"+id).remove();
	}else
	{
		if(count_checked == questioncount)
		{
			$("#selectall").prop('checked',$(ele).prop('checked'));
		}	
		appendcheckboxtext(id);
	}
	
	appendheighttodiv(1);
	
}

/**
 * In 'Step3 - Configure Appraisal Parameters' screen, check column headers when all options were selected on page load
 */
function checkparentclass()
{
	var allCheckBox = $("[class='checkallcls']");	
	var count_checked = allCheckBox.filter(":checked").length;
	
	var mgrcmntCheckBox = $("[class='mgrcmntcls']");	
	var count_mgrcntchecked = mgrcmntCheckBox.filter(":checked").length;
	
	var mgrratingCheckBox = $("[class='mgrratingcls']");	
	var count_mgrratingchecked = mgrratingCheckBox.filter(":checked").length;
	
	var empcmntCheckBox = $("[class='empcmntcls']");	
	var count_empcmntCheckBoxchecked = empcmntCheckBox.filter(":checked").length;
	
	var empratingCheckBox = $("[class='empratingcls']");	
	var count_empratingchecked = empratingCheckBox.filter(":checked").length;
	
	var questioncount = $("#questioncount").val();
	if(count_checked == questioncount)
	{
		$("#selectall").prop('checked',true);
	}
	if(count_mgrcntchecked == questioncount)
	{
		$("#mgrcmnt").prop('checked',true);
	}
	if(count_mgrratingchecked == questioncount)
	{
		$("#mgrrating").prop('checked',true);
	}
	if(count_empcmntCheckBoxchecked == questioncount)
	{
		$("#empcmnt").prop('checked',true);
	}
	if(count_empratingchecked == questioncount)
	{
		$("#empratings").prop('checked',true);
	}
}

function checkparenttd(ele)
{
	var id = $(ele).attr('ques_id');
	appendcheckboxtext(id);
	if($(ele).prop('checked'))
	{
		$("#check_"+id).prop('checked',$(ele).prop('checked'));
	}
	if(!$("#mgrcmnt_"+id).prop('checked') && !$("#mgrrating_"+id).prop('checked') && !$("#empcmnt_"+id).prop('checked')&& !$("#empratings_"+id).prop('checked'))
	{
		$("#check_"+id).prop('checked',$(ele).prop('checked'));
		$("#hiddentr_"+id).remove();
	}
	appendheighttodiv(1);
	/*if(!$("#mgrcmnt_"+id).prop('checked'))
	{
		$("#mgrcmnt").prop('checked',$(ele).prop('checked'));
	}
	if(!$("#mgrrating_"+id).prop('checked'))
	{
		$("#mgrrating").prop('checked',$(ele).prop('checked'));
	}
	if(!$("#empcmnt_"+id).prop('checked'))
	{
		$("#empcmnt").prop('checked',$(ele).prop('checked'));
	}
	if(!$("#empratings_"+id).prop('checked'))
	{
		$("#empratings").prop('checked',$(ele).prop('checked'));
	}*/
}

/**
 * 
 * End - Manger Question Functions
 */

function showhideqsdiv(flag)
{
	if(flag == 1)
	{
		$("#hiddenquestiondiv").hide();
		$("#questiondiv").show();
		$("#qssubmitdiv").show();
		$("#alldiv").addClass('active');
		$("#selecteddiv").removeClass('active');
		
	}else
	{
		$("#questiondiv").hide();
		$("#hiddenquestiondiv").show();
		$("#qssubmitdiv").hide();
		$("#selecteddiv").addClass('active');
		$("#alldiv").removeClass('active');
	}		
}

function emptooltip_helper(div_id,links)
{
    var usermDiv = $('#'+div_id);
    var usermtipContent = $(links);
    usermDiv.data('powertipjq', usermtipContent);
    usermDiv.powerTip({
            placement: 's',
            mouseOnToPopup: true
    });
}

function viewempgroup(groupname,groupid,appraisalid,empcount)
{
	if(groupid && appraisalid)
	{
		$.ajax({
         	url: base_url+"/appraisalinit/viewgroupedemployees/format/html",
         	type : 'POST',	
			data : 'groupid='+groupid+'&appraisalid='+appraisalid+'&groupname='+groupname+'&empcount='+empcount,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
	}
}

function editgroupemp(groupname,groupid,appraisalid,empcount)
{
	if(groupid && appraisalid)
	{
		$.ajax({
         	url: base_url+"/appraisalinit/displaygroupedemployees/format/html",
         	type : 'POST',	
			data : 'groupid='+groupid+'&appraisalid='+appraisalid+'&groupname='+groupname+'&empcount='+empcount,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
	}
}

function showgroupemp(groupname,groupid,appraisalid,empcount)
{
	if(groupid && appraisalid)
	{
		$.ajax({
         	url: base_url+"/appraisalinit/showgroupedemployees/format/html",
         	type : 'POST',	
			data : 'groupid='+groupid+'&appraisalid='+appraisalid+'&groupname='+groupname+'&empcount='+empcount,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
	}
}

function creategroupemp(appraisalid)
{
	if(appraisalid)
	{
		$.ajax({
         	url: base_url+"/appraisalinit/displaygroupedemployees/format/html",
         	type : 'POST',	
			data : 'appraisalid='+appraisalid,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
	}
}

function deletegroupemp(groupid,appraisalid,divid)
{
	var controllername = 'appraisalinit';
	var groupname = $("#groupname_"+divid).html();
	jConfirm("Are you sure you want to delete "+groupname+" group?", "Confirm ", function(r) {
		if(r==true)
        {
			if(groupid && appraisalid)
			{
				$.ajax({
		         	url: base_url+"/appraisalinit/deletegroupedemployees/format/json",
		         	type : 'POST',	
					data : 'groupid='+groupid+'&appraisalid='+appraisalid,
					dataType: 'json',
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					success : function(response){
						$.unblockUI();	
						
						if(response['result'] == 'success')
						{
							
							$("#groupdiv_"+divid).remove();
						
							if(response['empcount'] > 0)
							{
								$(".init_class").remove();
								$(".init_class_later").remove();
								
							if(!$(".create_new_group").is(":visible"))
								{
								
								$(".discard_button").remove();
								
								$("#initialization_div").prepend("<div class='create_new_group' style='margin-left: 0px;height: 17px;' onclick='creategroupemp("+appraisalid+")'>Create New Group</div><button name='submitbutton' id='submitbuttons' class='discard_button' type='button' onclick='changesettings('0',"+appraisalid+")'>Discard</button>");
								$( "#initialization_div" ).insertBefore( ".invfrnds_confirm" );
							
			    				
								}	
								if($(".groupeddiv").length == 0)
								{
									$("#clear_div").after('<div class="newgroup_msg managerresponsediv_msg">Groups are not configured yet.</div>');
								}
								
							}
							$(".invfrnds_confirm").hide();
						   $(".invfrnds_confirm").html('');
							successmessage_changestatus(response['msg'],response['result'],controllername);
						}else
						{
							successmessage_changestatus(response['msg'],response['result'],controllername);
						}	
							
					}
				});
			}
        }
	});
	
}


function fetchgroupdata()
{
	var groupid = $("#group_id").val();
	var appraisalid = $("#appraisalid").val();
	if(groupid && appraisalid)
		{
		$.ajax({
         	url: base_url+"/appraisalinit/getavailablemployes/format/html",
         	type : 'POST',	
			data : 'groupid='+groupid+'&appraisalid='+appraisalid,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
		}
}

/*function editgroupemp(groupid,appraisalid)
{
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
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
		}
	
}*/

function fetchallemployees(appraisalid)
{
	if(appraisalid)
		{
		$.ajax({
         	url: base_url+"/appraisalinit/getallgroupedemployees/format/html",
         	type : 'POST',	
			data : 'appraisalid='+appraisalid,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$(".invfrnds_confirm").show();
			    $(".invfrnds_confirm").html(response);
			    
			}
		});
		}
	
}

function changesettings(settingflag,appraisalid)
{
	
	var encryptappslid = $("#encryptappid").val();
	var AJAXURL = '';
	var msg = '';
	var DATATYPE = '';
	if(settingflag == 0)
	{	
		AJAXURL =  base_url+"/appraisalinit/changesettings/format/json";
		DATATYPE = 'json';
		msg = "The settings will be discarded. Are you sure?";
	}	
	else
	{	
		AJAXURL =  base_url+"/appraisalinit/displaysettings/format/html";
		DATATYPE = 'html';
		if(settingflag == 1)
			msg = "You choose to set the appraisal parameters to All employees. Please confirm.";
		else
			msg = "You choose to set the appraisal parameters to customized employee groups. Please confirm.";
	}	
	
	jConfirm(msg, "Confirm ", function(r) {
		if(r==true)
        {
			if(appraisalid)
			{
				
				$.ajax({
		         	url: AJAXURL,
		         	type : 'POST',	
					data : 'appraisalid='+appraisalid+'&settingflag='+settingflag,
					dataType: DATATYPE,
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					success : function(response){
						$.unblockUI();	
						if(settingflag == 0)
						{	
							if(response['result'] == 'success') {
								window.location.href= base_url+"/appraisalinit/assigngroups/i/"+encryptappslid;
							}
						}else
						{
							$("#options_div").html('');
							$("#ajaxcontntdiv").html(response);
						}	
					    
					}
				});
			}
        }
	});
	
}

function saveInitilize(flag)
{
	var allCheckBox = $("[class='checkallcls']");	
	var count_checked = allCheckBox.filter(":checked").length; 
	var ratingsflag = $("#ratingsflag").val();
	if(count_checked > 0)
	{
		var j = 0;
		var errorcount = 0;
		$("[class='checkallcls']").filter(":checked").each(function(){
			var id = $(this).val();
			var chk_count = $('.qprivileges_'+id+':checked').length;
			if(chk_count == 0)
			{
				j++;
				errorcount++;
			}            
		});
		if(j > 0)
		{
			jAlert("Please select atleast one privilege to proceed.");
		}	
		else 
		{
			if(flag == 1 || flag == 3)
			{
				msg= '<span class="alert_info_span"> You are trying to initialize the appraisal. </span>';
				msg+= '<span class="alert_info_span"> - Groups and questions cannot be edited once the appraisal is initiliazed.</span>';
				if(ratingsflag == 2)
					msg+= '<span class="alert_info_span"> - Appraisal ratings are not configured yet.</span>';	
				msg+= "<span class='alert_info_span'>Do you wish to continue?</span>";
			}	
			else
			{
				msg = "Appraisal configurations will be saved to be initialized later. Do you wish to continue?";
			}
			//for form submit
			jConfirm(msg, "Confirm ", function(r) {
				if(r==true)
				{
					$.blockUI({ width:'50px',message: $("#spinner").html() });
					$("#initializestep").val(flag);
					$("#formid").submit();
				}
			});
		}
	}
	else
	{
		jAlert("Please select atleast one question to proceed.");
	}
}

function saveGroupInitilize(initflag,appraisalid)
{
	var encryptaprsalid = $("#encryptappid").val();
	var ratingsflag = $("#ratingsflag").val();
	var msg = '';
	if(initflag==1)
	{	
		//msg= '<span class="alert_info_span"> - Line Manager(s) cannot be edited after initialization.</span>';
		if(ratingsflag == 2)
			msg+= '<span class="alert_info_span"> - Appraisal ratings not configured yet.</span>';	
		msg+= "<span class='alert_info_span'>Appraisal will be initialized. Do you wish to continue?</span>";
	}	
	else
		msg = "Appraisal configurations will be saved to be initialized later. Do you wish to continue?";
			
			
			jConfirm(msg, "Confirm ", function(r) {
		
		        if(r==true)
		        {
		        	$.ajax({
			         	url: base_url+"/appraisalinit/initializegroup/format/json",
			         	type : 'POST',	
						data : 'appraisalid='+appraisalid+'&initflag='+initflag,
						dataType: 'json',
						beforeSend: function() {
							$.blockUI({ width:'50px',message: $("#spinner").html() });
						},
						success : function(response){
							$.unblockUI();	
							if(response['result'] == 'success')
							{	
								if(initflag == 1)
									window.location.href= base_url+"/appraisalinit";
								else
									window.location.href= base_url+"/appraisalinit/assigngroups/i/"+encryptaprsalid;
							}	
							else
								jAlert(response['msg']);
						}
					});
		        }
			});
		
}

function validategroupname(ele)
{
	var elementid = $(ele).attr('id');
	var reqValue = $(ele).val();
	var re = /^[a-zA-Z0-9\- ]+$/;
	$('#errors-'+elementid).remove();
	if(reqValue == '')
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter group name.</span>");
	}		
	else if(!re.test(reqValue))
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid group name.</span>");
	}
	else
	{
		$('#errors-'+elementid).remove();
	}
}

function fnSaveMappedEmployees(groupid,appraisalid)
{
    var errorcount = 0;
    var divlength = $("[class^='users_right_list_div users_right_list user_div_']").length;
    var allCheckBox = $("[class='checkallcls']");	
    var count_checked = allCheckBox.filter(":checked").length; 
    var group_name = $("#group_name").val();
    var errorarray = [];
    var html = '';
	var re = /^[a-zA-Z0-9\- ]+$/;
	
    $("#errors-group_name").remove();
    
    if(count_checked == 0)
    {
        //jAlert("Please select atleast one question to proceed.");
        errorcount++;
        errorarray.push(1);
    }
    if(divlength == 0)
    {
    	$(".no_right_data_found span").html('Please add employees to group.');
        $(".no_right_data_found span").focus();
    	errorcount++;
    	errorarray.push(2);
    }
	
    if(group_name == '')
    {
        $("#group_name").parent().append("<span class='errors' id='errors-group_name'>Please enter group name.</span>");
        errorcount++;
        errorarray.push(3);
    }

	if(group_name != '' && !re.test(group_name))
	{
		$("#group_name").parent().append("<span class='errors' id='errors-group_name'>Please enter valid group name.</span>");
        errorcount++;
        errorarray.push(5);		
	}  
	
    if(count_checked > 0)
    {
        var j = 0;
        $("[class='checkallcls']").filter(":checked").each(function(){
            var id = $(this).val();
            var chk_count = $('.qprivileges_'+id+':checked').length;
            if(chk_count == 0)
            {
                j++;
                errorcount++;
            }            
        });
        if(j > 0)
        {
        	errorarray.push(4);
            //jAlert("Please select atleast one privilege to proceed.");
        }    
    }
	
    if(errorcount == 0)
    {
        $("#savehiddenFiled").trigger('click');
    }
    else
    {
        $.unblockUI();
        jQuery.each( errorarray, function( i, val ) {
        	
        	  if(val==1)
        	    html+="<span class='alert_info_span'> - Please select atleast one question to proceed.</span>";
        	  else if(val==2)
        		html+="<span class='alert_info_span'> - Please add employees to group.</span>";  
        	  else if(val==3)
          		html+="<span class='alert_info_span'> - Please enter group name.</span>";
        	  else if(val==4)
        		 html+="<span class='alert_info_span'> - Please select atleast one privilege to proceed.</span>"
				 else if(val==5)
        		 html+="<span class='alert_info_span'> - Please enter valid group name.</span>";  
        	});
        jAlert(html);
    }
}

function saveempgroupdetails(url)
{
	var controllername = 'appraisalinit';
	var appraisalid = $("#appraisalid").val();
    var group_settings = $("#group_settings").val();
	var encryptaprsalid = $("#encryptappid").val();
	$("#formid").attr('action',base_url+"/"+url);       
	$("#formid").attr('method','post');
	$('#formid').ajaxForm({
		data: {appraisalid:appraisalid,group_settings:group_settings },
	    beforeSend: function(a,f,o) {
	    	$.blockUI({ width:'50px',message: $("#spinner").html() });
        },			
		dataType:'json',
		success: function(response, status, xhr) { 
			
				if(response['result'] == 'success')
				{
                                    if(response['flag'] == 'appraisal')
					window.location.href= base_url+"/appraisalinit/assigngroups/i/"+encryptaprsalid;
                                    else 
                                    {
                                        $('#id_create_group').hide();
                                        $('#id_create_group').html('');
                                        show_groups(appraisalid);
                                        successmessage_changestatus(response['msg'],response['result'],'appraisalmanager');
                                    }
				}else
				{
					$.unblockUI();
					$("#group_name").parent().append("<span class='errors' id='errors-group_name'>"+response['msg']+"</span>");
					successmessage_changestatus(response['msg'],response['result'],controllername);
				}
				
		}
		});
	
}
/**
 * This function will help to close div which is used to created group.
 * @returns {null}
 */
function slideupqsdiv()
{
    $(".invfrnds_confirm").slideUp();;
    $(".invfrnds_confirm").html('');
}


/***
 * END
 */

function validatequestionname(ele)
{
	var elementid = $(ele).attr('id');
	var qsValue = $(ele).val();
	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
	$('#errors-'+elementid).remove();
	if(qsValue == '')
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter question.</span>");
	}		
	else if(!re.test(qsValue))
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid question.</span>");
	}
	else
	{
		$('#errors-'+elementid).remove();
	}
}

function closeqspopup()
{
	$("#qspopupdiv").dialog('close');
}

function ffcloseqspopup()
{
	$("#ffqspopupdiv").dialog('close');
}

function addmanagerqspopup(flag,appraisalid)
{
	apply_select2();
	$("#question_id").val('');
	$("#description").val('');
	$("#errors-question_id").html('');
	$("#errors-category_id").remove();
	$("#successdiv").hide();
	$("#contentdiv").show();

	/**
	* Validate 'Description' field with maximum number of characters allowed. Default value is 200 characters. 
	* Reference - /public/media/jquery/js/jquery.maxlength.js
	*/
	$('#description').maxlength();
	if(flag==1)
	{
		$.ajax({
			url: base_url+"/appraisalcategory/getappraisalcategory/format/json",
			type : 'POST',	
			data : 'appraisalid='+appraisalid,
			dataType: 'json',
			beforeSend: function () {
				$("#category_id").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
			},
			success : function(response){	
				$("#loader").remove();
				if(response.result=='success')
				{
					$("#category_id").html(response.data);
				}else
				{
					$("#category_id").parent().append("<span class='errors' id='errors-category_id'>Please enter valid question.</span>");
				}
			}
		});
	}  
	
	var title = "Add Question"; 
	$("#qspopupdiv").dialog({
		draggable:false, 
		resizable: false,
		width: 500,
		title: title,
		modal: true,
		close: function() { $("#qspopupdiv").dialog("destroy"); }
	});
}

function savemanagerquestions()
{
	var categoryval = $("#category_id").val();
	var questionval = $("#question_id").val();
	var description = $("#description").val();
	var moduleflag = $("#moduleflag").val();
	var errorcount = 0;
	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
	$("#errors-category_id").remove();
	$("#errors-question_id").remove();
	$('#error_message').html('');
		if(categoryval == '')
		{
			$("#category_id").parent().append("<span class='errors' id='errors-category_id'>Please select parameter.</span>");
			errorcount++;
		}
		if(questionval == '')
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter question.</span>");
			errorcount++;
		}
		else if(!re.test(questionval))
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter valid question.</span>");
			errorcount++;
		}
		questionval = encodeURIComponent(questionval);
		description = encodeURIComponent(description);
		if(errorcount==0)
		{
			$.ajax({
		     	url: base_url+"/appraisalquestions/savequestionpopup/format/json",
		     	type : 'POST',	
				data : 'categoryval='+categoryval+'&questionval='+questionval+'&moduleflag='+moduleflag+'&description='+description,
				dataType: 'json',
				beforeSend: function () {
					$.blockUI({ width:'50px',message: $("#spinner").html() });
				},
				success : function(response){	
					$.unblockUI();
					if(response['msg'] == 'success')
					{
						
						var id = response['id']; 
						var html = "<tr id='questiontr_"+id+"'>";
						if($(".selectallcls").prop('checked'))
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" checked onclick='checkchildtd(this)'></td>";
						else
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" onclick='checkchildtd(this)'></td>";
							html+="<td class='question_field' id='queshtml_"+id+"'>";
							html+="<div>";
							html+="<span class='appri_ques'>"+response['question']+"</span>";
							html+="<span class='appri_desc'>"+response['description']+"</span>";
							html+="</div>";
							html+="</td>";
							html+="<td class='field_width'>";
						if($("#mgrcmnt").prop('checked'))
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrcmntcls qprivileges_"+id+"' ques_id ="+id+" id='mgrcmnt_"+id+"' name='mgrcmnt["+id+"]'  value='1'  checked onclick='checkparenttd(this)'>Manager Comments</div>";
						else
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrcmntcls qprivileges_"+id+"' ques_id ="+id+" id='mgrcmnt_"+id+"' name='mgrcmnt["+id+"]'  value='1'  onclick='checkparenttd(this)'>Manager Comments</div>";
						if($("#mgrrating").prop('checked'))
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrratingcls qprivileges_"+id+"' ques_id ="+id+" id='mgrrating_"+id+"' name='mgrrating["+id+"]' value='1'  checked onclick='checkparenttd(this)' >Manager Ratings</div>";
						else
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrratingcls qprivileges_"+id+"' ques_id ="+id+" id='mgrrating_"+id+"' name='mgrrating["+id+"]' value='1' onclick='checkparenttd(this)' >Manager Ratings</div>";							
						if($("#empcmnt").prop('checked'))
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' checked onclick='checkparenttd(this)'>Employee Comments</div>";
						else
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' onclick='checkparenttd(this)'>Employee Comments</div>";
						if($("#empratings").prop('checked'))
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empratingcls qprivileges_"+id+"' ques_id ="+id+" id='empratings_"+id+"' name='empratings["+id+"]' value='1' checked onclick='checkparenttd(this)'>Employee Ratings</div>";
						else
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empratingcls qprivileges_"+id+"' ques_id ="+id+" id='empratings_"+id+"' name='empratings["+id+"]' value='1' onclick='checkparenttd(this)'>Employee Ratings</div>";
						    html=="</td>";
						    html=="</tr>";
						$("#contentdiv").hide();
						//$("#qspopupdiv").append("<div class='ml-alert-1-success'><div class='style-1-icon success'></div>Question added succesfully</div>");
						$("#successdiv").show();
						$("#parentqsdiv tr:first").after(html);
						prependmgrcheckboxtext(id);
						var qscount = +$("#questioncount").val() + 1;
						$("#questioncount").val(qscount);
						setTimeout(function(){
							$("#qspopupdiv").dialog("close");
						},3000);
						
					}else
					{
						$('#error_message').show();
						$('#error_message').html(response['msg']);
					}	
				}
			});
		
		}
		$('#category_id').val('').trigger("liszt:updated");
}

function addnewqspopup(flag,appraisalid)
{
		apply_select2();
	  $("#question_id").val('');
	  $("#description").val('');
	  $("#errors-question_id").html('');
	  $("#errors-category_id").remove();
	  $("#successdiv").hide();
	  $("#contentdiv").show();
	  if(flag==1)
		{
		  $.ajax({
	         	url: base_url+"/appraisalcategory/getappraisalcategory/format/json",
	         	type : 'POST',	
				data : 'appraisalid='+appraisalid,
				dataType: 'json',
				success : function(response){	
					if(response.result=='success')
					{
						$("#category_id").html(response.data);
					}else
					{
						$("#category_id").parent().append("<span class='errors' id='errors-category_id'>Please enter valid question.</span>");
					}	
				    
				}
			});
		}  
	 
	    var title = "Add Question"; 
	    $("#qspopupdiv").dialog({
	       		draggable:false, 
				resizable: false,
			    width:500,
				title: title,
			    modal: true,
				close: function() { $("#qspopupdiv").dialog("destroy");	}
		});
}

function savequestions()
{
	var categoryval = $("#category_id").val();
	var questionval = $("#question_id").val();
	var description = $("#description").val();
	var moduleflag = $("#moduleflag").val();
	var errorcount = 0;
	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
	
	$("#errors-category_id").remove();
	$("#errors-question_id").remove();
	$('#error_message').html('');
		if(categoryval == '')
		{
			$("#category_id").parent().append("<span class='errors' id='errors-category_id'>Please select parameter.</span>");
			errorcount++;
		}
		if(questionval == '')
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter question.</span>");
			errorcount++;
		}
		else if(!re.test(questionval))
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter valid question.</span>");
			errorcount++;
		}
		questionval = encodeURIComponent(questionval);
		description = encodeURIComponent(description);		
		if(errorcount==0)
		{
			$.ajax({
		     	url: base_url+"/appraisalquestions/savequestionpopup/format/json",
		     	type : 'POST',	
				data : 'categoryval='+categoryval+'&questionval='+questionval+'&moduleflag='+moduleflag+'&description='+description,
				dataType: 'json',
				beforeSend: function () {
					$.blockUI({ width:'50px',message: $("#spinner").html() });
				},
				success : function(response){	
					$.unblockUI();
					if(response['msg'] == 'success')
					{
						
						var id = response['id']; 
						var html = "<tr id='questiontr_"+id+"'>";
						if($(".selectallcls").prop('checked'))
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" checked onclick='checkchildtd(this)'></td>";
						else
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" onclick='checkchildtd(this)'></td>";
							html+="<td class='question_field' id='queshtml_"+id+"'>";
							html+="<div>";
							html+="<span class='appri_ques'>"+response['question']+"</span>";
							html+="<span class='appri_desc'>"+response['description']+"</span>";
							html+="</div>";
							html+="</td>";
							html+="<td class='field_width'>";
						if($("#mgrcmnt").prop('checked'))
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrcmntcls qprivileges_"+id+"' ques_id ="+id+" id='mgrcmnt_"+id+"' name='mgrcmnt["+id+"]'  value='1'  checked onclick='checkparenttd(this)'>Manager Comments</div>";
						else
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrcmntcls qprivileges_"+id+"' ques_id ="+id+" id='mgrcmnt_"+id+"' name='mgrcmnt["+id+"]'  value='1'  onclick='checkparenttd(this)'>Manager Comments</div>";
						if($("#mgrrating").prop('checked'))
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrratingcls qprivileges_"+id+"' ques_id ="+id+" id='mgrrating_"+id+"' name='mgrrating["+id+"]' value='1'  checked onclick='checkparenttd(this)' >Manager Ratings</div>";
						else
							html+="<div class='comments_div_fiel'><input type='checkbox' class ='mgrratingcls qprivileges_"+id+"' ques_id ="+id+" id='mgrrating_"+id+"' name='mgrrating["+id+"]' value='1' onclick='checkparenttd(this)' >Manager Ratings</div>";
						if($("#empcmnt").prop('checked'))
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' checked onclick='checkparenttd(this)'>Employee Comments</div>";
						else
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' onclick='checkparenttd(this)'>Employee Comments</div>";
						if($("#empratings").prop('checked'))
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empratingcls qprivileges_"+id+"' ques_id ="+id+" id='empratings_"+id+"' name='empratings["+id+"]' value='1' checked onclick='checkparenttd(this)'>Employee Ratings</div>";
						else
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empratingcls qprivileges_"+id+"' ques_id ="+id+" id='empratings_"+id+"' name='empratings["+id+"]' value='1' onclick='checkparenttd(this)'>Employee Ratings</div>";
						    html=="</td>";
						    html=="</tr>";
						$("#contentdiv").hide();
						//$("#qspopupdiv").append("<div class='ml-alert-1-success'><div class='style-1-icon success'></div>Question added succesfully</div>");
						$("#successdiv").show();
						$("#parentqsdiv tr:first").after(html);
						prependcheckboxtext(id);
						var qscount = +$("#questioncount").val() + 1;
						$("#questioncount").val(qscount);
						$('#s2id_category_id').find('a.select2-choice').find('span').html('Select Parameter');
						setTimeout(function(){
							$("#qspopupdiv").dialog("close");
						},3000);
						
					}else
					{
						$('#error_message').show();
						$('#error_message').html(response['msg']);
					}	
				}
			});
		
		}
		$('#category_id').val('').trigger("liszt:updated");
}
function ffaddnewqspopup()
{
	  $("#question_id").val('');
	  $("#description").val('');
	  $("#errors-question_id").html('');
	 
	  html = '<div class="total-form-controller"><div class="new-form-ui "><label class="required">Question <img class="tooltip" title="Special characters allowed are - ? &#39; . , / # @ $ & * ( ) !" src="'+ domain_data + 'public/media/images/help.png"></label>'+
		  		'<div class="division"><input type="text" onkeyup="validatequestionname(this)" onblur="validatequestionname(this)"'+
		  		'name="question_id" id="question_id" value="" maxlength="100"></div></div><div class="new-form-ui textareaheight">'+
		  		'<label class="">Description </label><div class="division"><textarea name="description" id="description"></textarea></div>'+
		  		'</div><div class="new-form-ui-submit"><input type="button" value="Save" id="submitqs" name="submitqs" onclick="ffsavequestions()"/>'+
		  		'<button name="Cancel" id="Cancel" type="button" onclick="ffcloseqspopup()">Cancel</button></div></div>';
	  $("#ffqspopupdiv").html(html);
	  
	  /**
	   * Validate 'Description' field with maximum number of characters allowed. Default value is 200 characters.
	   * Reference - /public/media/jquery/js/jquery.maxlength.js
	   * Max length validation can be added after the above statement only. - $("#ffqspopupdiv").html(html);
	   */
	  $('#description').maxlength();
	  
	    var title = "Add Question"; 
	    $("#ffqspopupdiv").dialog({
	       		draggable:false, 
				resizable: false,
			    width:500,
				title: title,
			    modal: true 
			    });
}
function ffsavequestions()
{
	var questionval = $("#question_id").val();
	var description = $("#description").val();
	var errorcount = 0;
	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!]+$/;
	
	$("#errors-question_id").remove();
	$('#error_message').html('');
		if(questionval == '')
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter question.</span>");
			errorcount++;
		}
		else if(!re.test(questionval))
		{
			$("#question_id").parent().append("<span class='errors' id='errors-question_id'>Please enter valid question.</span>");
			errorcount++;
		}
		
		if(errorcount==0)
		{
			$.ajax({
		     	url: base_url+"/feedforwardquestions/savepopup/format/json",
		     	type : 'POST',	
				data : 'question='+questionval+'&description='+description,
				dataType: 'json',
				beforeSend: function () {
					$.blockUI({ width:'50px',message: $("#spinner").html() });
				},
				success : function(response){	
					$.unblockUI();
					if(response['msg'] == 'success')
					{						
						var id = response['id']; 
						var html = "<tr id='questiontr_"+id+"'>";
						if($(".selectallcls").prop('checked'))
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" checked onclick='checkchildtd(this)'></td>";
						else
							html+="<td class='check_field'><input type='checkbox' class ='checkallcls' ques_id ="+id+" id='check_"+id+"' name='check[]' value="+id+" onclick='checkchildtd(this)'></td>";
							html+="<td class='question_field' id='queshtml_"+id+"'>";
							html+="<div>";
							html+="<span class='appri_ques'>"+response['question']+"</span>";
							html+="<span class='appri_desc'>"+response['description']+"</span>";
							html+="</div>";
							html+="</td>";
							html+="<td class='field_width'>";
						if($("#empcmnt").prop('checked'))
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' checked onclick='checkparenttd(this)'>Comments</div>";
						else
							html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empcmntcls qprivileges_"+id+"' ques_id ="+id+" id='empcmnt_"+id+"' name='empcmnt["+id+"]' value='1' onclick='checkparenttd(this)'>Comments</div>";
						html+="<div class='comments_div_fiel'> <input type='checkbox' class ='empratingcls qprivileges_"+id+"' ques_id ="+id+" id='empratings_"+id+"' name='empratings["+id+"]' value='1' disabled checked>Ratings</div>";
						    html=="</td>";
						    html=="</tr>";
						$("#ffqspopupdiv").html('');
						$("#ffqspopupdiv").append("<div class='total-form-controller'><div class='ml-alert-1-success'><div class='style-1-icon success'></div>Question added succesfully</div></div>");
						$(".total-form-controller_ tr:first").after(html);
						ff_prependcheckboxtext(id);
						setTimeout(function(){
							$("#ffqspopupdiv").dialog("close");
						},3000);
						
					}else
					{
						$('#error_message').show();
						$('#error_message').html(response['msg']);
					}	
				}
			});
		}	
}

function ff_emp_search_ready()
{
    $('#idclear_right').hide();
		
    if($.trim($('#search_emp_by_name_right').val()).length>0)
    	$('#idclear_right').show();
    else
    	$('#idclear_right').hide();
    
    $('#search_emp_by_name_right').bind('keyup', function() {

		var txt = $.trim($('#search_emp_by_name_right').val());
		$('li.ff_user_list').hide();
        $('li.ff_user_list').each(function(){
           if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
               $(this).show();
           }
        });
				
		if($('li.ff_user_list:visible').length < 1)
			$('div.no_search_results_right').show();
		else
			$('div.no_search_results_right').hide();
		
		if(txt.length>0)
			$('#idclear_right').show();
		else
			$('#idclear_right').hide();
    });

    $('#search_right').bind('click', function() 
    {
		var txt = $.trim($('#search_emp_by_name_right').val());
		
		$('li.ff_user_list').hide();
        $('li.ff_user_list').each(function(){
           if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1){
               $(this).show();
           }
        });
			
        if($('li.ff_user_list:visible').length < 1)
			$('div.no_search_results_right').show();
		else
			$('div.no_search_results_right').hide();
		
        if(txt.length>0)
			$('#idclear_right').show();
		else
			$('#idclear_right').hide();
    });
}
function ff_clearSearchData()
{	
    $('#search_emp_by_name_right').val('');
    $('#idclear_right').hide();
	
    var txt = $.trim($('#search_emp_by_name_right').val());
    $('li.ff_user_list').hide();
    $('li.ff_user_list').each(function(){
        if($(this).attr("name").toUpperCase().indexOf(txt.toUpperCase()) != -1)
        {
            $(this).show();
        }
    });
	
    $('div.no_search_results_right').hide();			
}

function checkratingstext()
{
	 $("#ratingsdiv").dialog({
    		draggable:false, 
			resizable: false,
		    width:250,
			title: "Ratings Definition",
		    modal: true 
		    });
}

function displayeligibilitydiv()
{
	if($("#eligibility_div").is(":visible"))
		{
			$("#eligibility_div").hide();
			$("#eligibility_hidden_div").show();
			$("#eligibilityflag").val(2);
			$("#selectallspan").html("Clear");
		}
	else
		{
			$("#eligibility_div").show();
			$("#eligibility_hidden_div").hide();
			$("#eligibilityflag").val(1);
			$("#eligibility").select2('val','All');
			$("#selectallspan").html("Select All");
		}
}

function clearEligibilityData()
{
			$("#eligibilityflag").val(1);
			$("#eligibility").select2('val','All');
			$("#clearspan").hide();
			$("#selectallspan").show();
}

function validateeligibility(eleid)
{
	var elementid = eleid.substring(5);
	 $('#errors-'+elementid).remove();
     
     if($.trim($('#'+elementid).val()) == '')
      {

         // To place error messages after Add Link
         $('#'+elementid).after("<span class='errors' id='errors-"+elementid+"'>Please select eligibility.</span>");
      }
      else 
      {
          $('#errors-'+elementid).remove();   

      }
     
}


function save_mng_response(key,flag)
{
    $('#idhid_btn_flag_'+key).val(flag);
    var rating = $('#consol_rating_'+key).val();
	var comments = $('#idconsol_comments_'+key).val();
    var j = 0 ;
    var errorarray = [];
    var html = '';
    $('.errors_'+key).remove();
    if(rating == 0 && flag != 'draft')
    {
		j++;
		$('#consol_rating_'+key).next().addClass('borderclass');
		$('#consol_rating_'+key).parent().append("<span class='errors errors_"+key+"' id='err-consol_rating_"+key+"'>Please select rating.</span>");
		errorarray.push(1);
	}
    if($.trim(comments) == '' && flag != 'draft')
    {
		j++;
		$('#idconsol_comments_'+key).addClass('borderclass');
		$('#idconsol_comments_'+key).parent().append("<span class='errors errors_"+key+"' id='err-idconsol_comments_"+key+"'>Please enter comments.</span>");
		errorarray.push(2);
		
    }
	if(j == 0)
    {
    	$.blockUI({ width:'50px',message: $("#spinner").html() });
        var data = $('#idfrm_manager_response_'+key).serialize();
        $.post(base_url+"/myteamappraisal/savelineresponse",data,function(data){
        	$.unblockUI();
            if(data.status == 'success')
                successmessage(data.msg);
            else 
                error_message(data.msg);

            setTimeout(function(){
                location.reload();
            },3000);
        },'json');
    }/*else
    {
    	jQuery.each( errorarray, function( i, val ) {
      	  if(val==1)
      	    html+="<span class='alert_info_span'>"+(i+1)+". Please select rating.</span>";
      	  else if(val==2)
      		html+="<span class='alert_info_span'>"+(i+1)+". Please enter comments.</span>";  
      	});
      jAlert(html);
    }*/	
}

function displaysearchedteam()
{
	
	$("#appraisalstatusclear").hide();
	$('#s2id_appraisal_status_select .select2-choice span').html('Select Appraisal Status');
	$("#appraisal_status_select").val('');			
	
	$("#businessunitclear").hide();
	$('#s2id_business_unit_select .select2-choice span').html('Select Business Unit');
	$("#business_unit_select").val('');	
	
	var searchstring =  $('#search_emp_by_name').val();
	
	$('#errors-search_emp_by_name').remove();
	/*if ($.trim(searchstring) == "")
	{ 
		$('#search_emp_by_name').parent().append("<span class='errors' id='errors-search_emp_by_name'>Please enter valid string.</span>");
		return false;
	}*/
	$.ajax({
     	url: base_url+"/myteamappraisal/getsearchedempcontent/format/html",
     	type : 'POST',	
		data : 'searchstring='+searchstring,
		dataType: 'html',
		beforeSend: function () {
			$.blockUI({ width:'50px',message: $("#spinner").html() });
		},
		success : function(response){	
			$.unblockUI();
			$("#empaccdiv").show();
		    $("#empaccdiv").html(response);
		   
		}
	});
	
}

function displaysearchedstatus(statusval)
{
	
		if(statusval)
			$("#appraisalstatusclear").show();
		else
		{
			$("#appraisalstatusclear").hide();
			$('#s2id_appraisal_status_select .select2-choice span').html('Select Appraisal Status');
			$("#appraisal_status_select").val('');
			$( location ).attr("href", base_url+"/myteamappraisal");
		}
		
		$('#search_emp_by_name').val('');
		$('#search_emp_by_name').html('');
		$("#businessunitclear").hide();
		$('#s2id_business_unit_select .select2-choice span').html('Select Business Unit');
		$("#business_unit_select").val('');		 
		$(".search_go").hide();	
		
    	$.ajax({
         	url: base_url+"/myteamappraisal/getsearchedstatus/format/html",
         	type : 'POST',	
			data : 'statusval='+statusval,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$("#empaccdiv").html('');
				$("#empaccdiv").show();
			    $("#empaccdiv").html(response);
			    
			}
		});
	
}

function clearsearchedteam()
{
	$('#errors-search_emp_by_name').html('');
	$('#search_emp_by_name').val('');
	$('#search_emp_by_name').html('');
	$(".search_go").hide();
	displaysearchedteam();
}

function cancel_accordian(id,key)
{
	$('.errors').remove();
	$(".borderclass").removeClass("borderclass");		
	var tmpId = key;
	$('#iddivcontent_'+tmpId).hide();
	$('#iddiv_collapse_'+tmpId).addClass('cls_expand');
	$('#iddiv_collapse_'+tmpId).removeClass('cls_collapse');
	$('#idmaindiv_'+tmpId).removeClass('expand_manager');
	$('#iddiv_collapse_'+tmpId).html('Expand');		
}

function displaysearchedbusinessunit(business_unit)
{
		if(business_unit)
			$("#businessunitclear").show();
		else
		{
			$("#businessunitclear").hide();
			$('#s2id_business_unit_select .select2-choice span').html('Select Business Unit');
			$("#business_unit_select").val('');
			$( location ).attr("href", base_url+"/myteamappraisal");
		}
		
		$('#search_emp_by_name').val('');
		$('#search_emp_by_name').html('');
		$("#appraisalstatusclear").hide();
		$('#s2id_appraisal_status_select .select2-choice span').html('Select Appraisal Status');
		$("#appraisal_status_select").val('');		
		$(".search_go").hide();	
		
    	$.ajax({
         	url: base_url+"/myteamappraisal/getsearchedstatus/format/html",
         	type : 'POST',	
			data : 'business_unit='+business_unit,
			dataType: 'html',
			beforeSend: function () {
				$.blockUI({ width:'50px',message: $("#spinner").html() });
			},
			success : function(response){	
				$.unblockUI();
				$("#empaccdiv").show();
			    $("#empaccdiv").html(response);
			    
			}
		});
	
}

function removeValidationMessage(ele) {
	if($(ele).hasClass('borderclass')) {
		if($(ele).val() || $(ele).prev().val()) {
			$(ele).removeClass('borderclass');
			var attr = $(ele).attr('alt');
			var eleid = $(ele).attr('id');
			$("#err-"+eleid).remove();
			if (typeof attr !== typeof undefined && attr !== false) {
				if($('#hidden_level').length)
					$(ele).next().next().next().remove();
				else
					$(ele).next().next().remove();
			}	
			else 
				$(ele).next().remove();
		
		}
	}
}



























