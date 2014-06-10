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

$(document).ready(function(){

	// To scroll to top of the page, for the last page of the grid results
	$('#maincontentdiv').on('click', '[id^=pagination_] a span', function(){

		page_data = $('#pagenotext').val().split(' ');
		if(page_data[1] == page_data[3]){
			location.hash = "mainDiv";
		}
	});
	
	// To close date picker when a select box in the form is clicked.
    $('select:not(.not_appli)').select2()
    .on("opening", function() {
        $( ".hasDatepicker" ).datepicker("hide");
    });				
	
});

var downloadPdf = function(url, formId){
   $.blockUI({ width:'50px',message: $("#spinner").html() });
   $('#generatereport').val('pdf');
	//$(this.form).submit();
   data = $(formId).serialize();
     $.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function(response) {
				response = JSON.parse(response);
				download_url = base_url + '/reports/downloadreport/file_name/' + response.file_name;
			    var $preparingFileModal = $("#preparing-file-modal");

		        //$preparingFileModal.dialog({ modal: true });

		        $.fileDownload(download_url, {
		            successCallback: function(url) {
		                //$preparingFileModal.dialog('close');
						$.unblockUI();
		            },
		            failCallback: function(responseHtml, url) {

		                /*$preparingFileModal.dialog('close');
		                $("#error-modal").dialog({ modal: true });*/
		            	$.unblockUI();
		                jAlert('Download of the report failed');
		            }
		        });
		        return false; //this is critical to stop the click event which will trigger a normal file download!
			}
	});	
}
function display_child_reports()
{ 
    if($('#sub_reports').hasClass("config-up"))		
    {
        $( '#sub_reports').removeClass("config-up");
        $( '#sub_reports').addClass("config-down");
        $('#sub_reports').show();
        $('#sub_reports').slideDown();	
    }
    else
    {
        $( '#sub_reports').removeClass("config-down");
        $( '#sub_reports').addClass("config-up");
        $('#sub_reports').hide();
        $('#sub_reports').slideUp();	
    }

    var overlay	= '<div id="reportgridoverlay" class="overlayreport"></div>';
    //$('.reports-ctrl').prepend(overlay); 
}
function timepicker_onclose(id)
{
    $.blockUI({ width:'50px',message: $("#spinner").html() });
                        
    
    var val= $('#'+id).val();
    var sel_date = $( "#interview_date" ).val();
    /*if(sel_date =='')
    {*/
        if(val != null && val != '')
        {
            $.post(base_url+"/index/gettimeformat",{sel_time:val},function(data){
                $('#'+id).val(data.timeformat);
                $.unblockUI();
            },'json');
        }
        else 
        {
            $.unblockUI();
        }
    /*}
    else 
    {
        if(val != null && val != '')
        {           
            $.post(base_url+"/index/chkcurrenttime",{sel_time:val,sel_date:sel_date},function(data){
                if(data.greater == 'yes')
                {
                    $('#'+id).val('');
                    $('#'+id).parent().append('<span id="errors-interview_time" class="errors">Interview time must be less than current time.</span>' );
                }
                else 
                {
                    $('#'+id).val(data.timeformat);
                }
                
            },'json');
        }
        $.unblockUI();
    }*/
    //above was commented by k.rama krishna
}

function disp_requisition(val,disp_id)
{    
    $('#'+disp_id).val('');
    if(val != '')
    {
        $.post(base_url+"/employee/getemprequi",{cand_id:val},function(data){
            $('#'+disp_id).val(data.requi_code);
        },'json');
    }
}
function getEmailOfUser(obj,email_id)
{
    var cand_id = $(obj).val();
    var email_obj = $('#'+email_id);
    $('#errors-emailaddress').remove();
    if(cand_id !='')
    {
        $.post(base_url+"/usermanagement/getemailofuser",{cand_id:cand_id},function(data){
            email_obj.val(data.email);
        },'json');
    }
    else
    {
        email_obj.val('');
    }
}
//loginUserId
	function saveDetails(url,dialogMsg,toggleDivId,jsFunction){	
	
		var actionurl = url.split( '/' );
		$("#formid").attr('action',base_url+"/"+url);       
		$("#formid").attr('method','post');
		$('#formid').ajaxForm({
		    beforeSend: function(a,f,o) {
				/*if(actionurl[2] == 'editforgotpassword')
				{ 	
				//$('.wrapperdivright').block({message: $("#spinner").html(),timeout:500 });
						$(".login-button").before("<div id='loader-1'></div>");
						$("#loader-1").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
						//$.blockUI({ width:'50px',message: $("#defaultspinner").html() });
				}*/
            },			
			dataType:'json',
			success: function(response, status, xhr) { 
			     $("#formid").find('.errors').remove();
				 $("#formid").find('.borderclass').removeClass('borderclass');
				 $("#formid").find('span[class^="errors_"]').remove();
				 /*if($("#loader-1").is(":visible"))
	                $("#loader-1").remove();
				if(actionurl[2] == 'editforgotpassword')
					$.unblockUI;*/	
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
									  $("#"+id).parent().parent().append(getErrorHtml(v, id,''));
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
	
					if(response['result'] ==  'saved' ||  response['result'] ==  'fbsaved') {
						if(toggleDivId.length > 2)
							$("#"+toggleDivId).toggle("slow");								
						
						changepassworddefaultvalues();
						if(dialogMsg.length > 2) 
							eval(dialogMsg); 
						else
							
							eval(jsFunction);
							
						if(response['controller'] == 'pendingleaves' )	
						  window.location.href = base_url+'/pendingleaves';
						  
						if(response['nomessage'] != 'yes')
                            successmessage(response['message']);
                       
						if(response['page'] == 'changepassword')
							successmessagechange(response['message']);
					} 
					
			}
			});
	}

	function getErrorHtml(formErrors , id, flag )
	{		
		var o = '<span class="errors'+flag+'" id="errors-'+id+'">';
			 o += formErrors;
			 o += '</span>';
		return o;
	}
	function changepassworddefaultvalues()
	{
		$('#password').val('');
		$('#newpassword').val('');
		$('#passwordagain').val('');
		

	}

	function successmessage(message){
		$("#error_message").css('display','block');
		$("#error_message").html("<div class='ml-alert-1-success'><div class='style-1-icon success'></div>"+message+"</div>"); 
		setTimeout(function(){
			$('#error_message').fadeOut('slow');
		},3000);
	}
	
	function successmessage_changestatus(message,flag,controllername)
	{	
	    
		/*	Purpose:	To get the success messages in dash board grids,pass the controller name & change the error message span id.
			Modified Date:	30/10/2013.
			Modified By:	Yamini.
		*/
		var eleId = 'error_message_'+controllername;
		$("#error_message").css('display','block');
		//alert("Ele Exists > "+$("#"+eleId).length);
		if($("#"+eleId).length == 0) 
		{
			//Element with id doesn't exist...
			$("#error_message").attr("id","error_message_"+controllername);
			$("#error_message_"+controllername).css('display','block');
		}
		else
		{
			$("#error_message_"+controllername).css('display','block');
		}
		
		/*$("#error_message").html('<div id="messageData" class="ml-alert-1-'+flag+'"><div style="display:block;"><span class="style-1-icon '+flag+'"></span>'+message+'</div></div>'); */
		
		$("#error_message_"+controllername).html('<div id="messageData" class="ml-alert-1-'+flag+'"><div style="display:block;"><span class="style-1-icon '+flag+'"></span>'+message+'</div></div>'); 
        
		setTimeout(function(){
			//$('#error_message').fadeOut('slow');
			$('#error_message_'+controllername).fadeOut('slow');
		},3000);
		
		//$("#error_message_"+controllername).remove();
	}

            
	function redirecttocontroller(controllername)
	{
	 //setTimeout(function(){
      window.location.href = base_url+'/'+controllername;	
	  //},3000);
	}
	
	function changeeditscreen(controllername,id)
	{
	  window.location.href = base_url+'/'+controllername+'/edit/id/'+id;
	}
	/*	For redirecting to edit screen from view screen in mydetails	*/
	function redirecttoEditscreen(controllerName,actionName)
	{
		//alert("Redirect > "+controllerName+" >> "+actionName);
	  window.location.href = base_url+'/'+controllerName+'/'+actionName;
	}
	function changeviewscreen(controllername,id)
	{
	  //window.location.href = base_url+'/'+controllername+'/edit/id/'+id;
	  window.location.href = base_url+'/'+controllername+'/view/id/'+id;
	}
	
	function changeempeditscreen(controllername,id)
	{
		/*var indexarr = [ "empskills", "empleaves","empholidays","educationdetails","dependencydetails","trainingandcertificationdetails" ];
		
		if(jQuery.inArray(controllername, indexarr) == -1)
		{*/
		   window.location.href = base_url+'/'+controllername+'/edit/userid/'+id;
		   
		/*}   
		else   
		{
		  window.location.href = base_url+'/'+controllername+'/index/userid/'+id;
		}*/  
	}
	
	function changeempviewscreen(controllername,id)
	{
	  /*var indexarr = [ "empskills", "empleaves","empholidays" ,"educationdetails","dependencydetails","trainingandcertificationdetails"];
		
		if(jQuery.inArray(controllername, indexarr) == -1)
		{*/
	      window.location.href = base_url+'/'+controllername+'/view/userid/'+id;
		/*}
        else
        {
		 window.location.href = base_url+'/'+controllername+'/index/userid/'+id;
        } */  		
	}
	
	function changemyempviewscreen(controllername,actionname,id)
	{
	 window.location.href = base_url+'/'+controllername+'/'+actionname+'/userid/'+id;
	
	}
	
	function changepopupeditscreen(controllername,id,unitid)
	{
	  window.parent.$('#'+controllername+'Container').dialog('close');
	  //var url = base_url+'/'+controllername+'/editpopup/id/'+id+'/unitId/'+unitid+'/popup/1';
	 // var url = '/hrms/holidaydates/editpopup/id/6/unitId/1/popup/1';
	  var url = base_url+'/holidaydates/editpopup/id/6/unitId/1/popup/1';
	 	
           setTimeout(function(){
		    displaydeptform(url,'');
		},2000);	
	}
/*	Configurations Array it has both Employee configuration names & Site configuration names*/
var configurationsArr = new Array('employmentstatus','eeoccategory','jobtitles','payfrequency','remunerationbasis','positions','bankaccounttype','competencylevel','educationlevelcode','attendancestatuscode','workeligibilitydoctypes','employeeleavetypes','ethniccode','timezone','weekdays','monthslist','gender','maritalstatus','prefix','racecode','nationalitycontextcode','nationality','accountclasstype','licensetype','numberformats','identitycodes','emailcontacts','countries','states','cities','geographygroup','veteranstatus','militaryservice','currency','currencyconverter','language');
function changestatus(controllername,objid,flag)
{	
	var flagAr = flag.split("@#$"); 
	var i;
	var msgdta = ' ';
	for(i=0;i<flagAr.length;i++)
	{
		msgdta += flagAr[i]+' ';
	}	
	//mdgdta = msgdta.trim();
	mdgdta = $.trim(msgdta);
    if(controllername == 'bgscreeningtype') 
        var messageAlert = 'Are you sure you want to delete the selected screening type?';
    else  if(controllername == 'businessunits') 
		 var messageAlert = 'Are you sure you want to delete the selected business unit?';
	else  if(controllername == 'agencylist') 
		var messageAlert = 'You are trying to delete the selected agency. The background check processes assigned to this agency will become invalid. Please confirm.';
	else if(controllername == 'pendingleaves')	
	    var messageAlert = 'Are you sure you want to cancel the selected leave request?';
        else if(controllername == 'roles')	
	    var messageAlert = 'Are you sure you want to delete the selected role name?';
	else
	{
		//if(configurationsArr.indexOf(controllername) != -1)
                if($.inArray(controllername,configurationsArr) != -1)
		{
			var messageAlert = 'If the selected '+mdgdta+' is used in the system, it will be unset. Are you sure you want to delete this '+mdgdta+'?';
		}
		else
		{
			var messageAlert = 'Are you sure you want to delete the selected '+mdgdta+'? ';
		}
	}
     
	 jConfirm(messageAlert, "Delete "+msgdta, function(r) {

        if(r==true)
        {               
            if(controllername == 'candidatedetails')
            {
                $.post(base_url+"/candidatedetails/chkcandidate",{id:objid},function(data){
                    if(data.status == 'no')
                    {
                       // jAlert('Selected candidate cannot be deleted.');
					    successmessage_changestatus('Candidate cannot be deleted.','error',controllername);
                    }
                    else 
                    {
                        $.ajax({
                            url: base_url+"/"+controllername+"/delete",   
                            type : 'POST',
                            data: 'objid='+objid,
                            dataType: 'json',
                                    success : function(response)
                                    {	
                                        successmessage_changestatus(response['message'],response['msgtype'],controllername);
                                        if(response['flagtype']=='process')
												location.reload();
										else
												getAjaxgridData(controllername);		    	        	
                                    }
                                });
                    }
                },'json');
            }
            else
            {
                   $.ajax({
                   url: base_url+"/"+controllername+"/delete",   
                   type : 'POST',
                   data: 'objid='+objid,
				   beforeSend: function () {
							$.blockUI({ width:'50px',message: $("#spinner").html() });
							},
                   dataType: 'json',
                           success : function(response)
                           {	
                              successmessage_changestatus(response['message'],response['msgtype'],controllername);
                               if(response['flagtype']=='process')
								{
									if(response['redirect']=='no')
									return false;
									else
									location.reload();
								}
                               else
									getAjaxgridData(controllername);		    	        	
                           }
                       });
            }
           }
           else 
           {

           }
        });
		 
}
	function changeEmployeestatus(controllername,objid,flag,userId)
	{
		var flagAr = flag.split("@#$"); 
		var i;
		var msgdta = ' ';
		for(i=0;i<flagAr.length;i++)
		{
			msgdta += flagAr[i]+' ';
		}	
		mdgdta = $.trim(msgdta);
	   var messageAlert = 'Are you sure you want to delete this '+mdgdta+'? ';
		jConfirm(messageAlert, "Delete "+mdgdta, function(r) 
		{

		 if(r==true)
			{
				$.ajax({
					url: base_url+"/"+controllername+"/delete",   
					type : 'POST',
					data: 'objid='+objid,
					dataType: 'json',
					success : function(response)
						{	//alert("Response > "+response['msgtype']+ " response > "+response);
							successmessage_changestatus(response['message'],response['msgtype']);
							if(response['flagtype']=='process')
								location.reload();
							else
								getEmployeeAjaxgridData(controllername,userId);		    	        	
					}
				});
			}
			else {		 }
		});
	}
	
	function getEmployeeAjaxgridData(objname,userId)
	{	
		var sort = $("#sortval_"+objname).val();
		var by = $("#byval_"+objname).val();
		var userId = '';var context="";
		var url = document.URL.split('/');  	//Taking userId from Current URL 
		//if(url.indexOf("mydetails") != -1)
                if($.inArray("mydetails",url) != -1)
		{
			userId = loginUserId;	// if url has mydetails take login Id as user id...
			context = 'mydetails';
		}
		else
		{
			//if(url.indexOf("myemployees") != -1)	
                        if($.inArray("myemployees",url) != -1)	
                            context = 'myteam';
			userId = url[url.length-1];
		}
		//alert("User id > "+userId+" type > "+typeof userId+ " >> objname"+objname);
		//alert("login user id"+loginUserId);
		if(userId == "")	userId = loginUserId;
		
        //var perpage = $(".records_input_"+objname).val();
		var perpage = $("#perpage_"+objname).val();
        
		var page = $(".gotopage_input_"+objname).val(); 
		var searchData = $("#"+objname+"_searchdata").val(); 
		searchData = decodeURIComponent(searchData);
		var formGridId = $("#formGridId").val(); 
		var unitId = '';mname='';mnuid='';
		if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
		formGridId = ''; 
		else
		{
			unitId = formGridId.split("/"); 
			mname = unitId[0]; mnuid = unitId[1];
			
		}
		if(page == '' || page == 'undefined' || typeof(page) === 'undefined')
		page = $(".currentpage").val();
		
		var dataparam = 'per_page='+ perpage+'&page='+page+'&call=ajaxcall&objname='+objname+'&'+mname+'='+mnuid+'&userid='+userId+"&context="+context+'&sort='+sort+'&by='+by;
		if(searchData!='' && searchData!='undefined')
			dataparam = dataparam+'&searchData='+searchData;
		$('#searchdata').remove();	
		$('#footer').append("<input type='hidden' value='"+searchData+"' id='searchdata' />");
		$('#footer').append('<input type="hidden" value="'+objname+'" id="objectName" />');	
		$.ajax({
			url: base_url+"/"+objname+"/index/format/html",   
			type : 'POST',
			data : dataparam,
			success : function(response){
				//$('#maincontentdiv').html('');
				//$('#maincontentdiv').html('<div id="gridblock"></div>');
				//$('#gridblock').html(response);	
				$('#grid_'+objname).html(response);
			}
		});
		
	}
	function getAjaxgridData(objname,dashboardcall)
	{		
                //var perpage = $(".records_input_"+objname).val();
				var perpage = $("#perpage_"+objname).val();
               // var perpage = $('#perpage_'+objname).val();
		var page = $(".gotopage_input_"+objname).val(); 
		var sort = $("#sortval_"+objname).val();
		var by = $("#byval_"+objname).val();
		var searchData = $("#"+objname+"_searchdata").val();
		searchData = decodeURIComponent(searchData);
		var formGridId = $("#formGridId").val(); 
		var unitId = '';mname='';mnuid='';
		if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
		formGridId = ''; 
		else
		{
			unitId = formGridId.split("/"); 
			mname = unitId[0]; mnuid = unitId[1];
			
		}
		if(page == '' || page == 'undefined' || typeof(page) === 'undefined')
		page = $(".currentpage").val();
		
		var dataparam = 'per_page='+ perpage+'&page='+page+'&call=ajaxcall&objname='+objname+'&'+mname+'='+mnuid+'&dashboardcall='+dashboardcall+'&sort='+sort+'&by='+by;
		if(searchData!='' && searchData!='undefined')
			dataparam = dataparam+'&searchData='+searchData;
		$('#'+objname+'_searchdata').remove();	
		$('#footer').append("<input type='hidden' value='"+searchData+"' id='"+objname+"_searchdata' />");
		$('#footer').append('<input type="hidden" value="'+objname+'" id="objectName" />');	
		$.ajax({
			url: base_url+"/"+objname+"/index/format/html",   
			type : 'POST',
			data : dataparam,
			success : function(response){
				//$('#maincontentdiv').html('');
				//$('#maincontentdiv').html('<div id="gridblock"></div>');
				//$('#gridblock').html(response);	
				$('#grid_'+objname).html(response);
			}
		});
		
	}
	
	function moreMenus(con)
	{	
		if(con)
		{
			$('#moreMenusDiv').show();
			$('#moreLinkDiv').hide();
		}
		else{
			$('#moreMenusDiv').hide();
			$('#moreLinkDiv').show();
		}
	}

function redirecttolink(link)
{    
 $.blockUI({ width:'50px',message: $("#spinner").html() }); 
 window.location = base_url+'/'+link;

}

function closetab(ele,pagename,pagelink)
{ 
var newURL = window.location.protocol + "//" + window.location.host;

     jQuery.ajax({
          //url: newURL+'/Sentrifugo/default/index/clearsessionarray',
		  url: base_url+'/index/clearsessionarray',
          data: 'name='+pagename+'&link='+pagelink,
          type: "POST",
          dataType: "json",
		  beforeSend: function () {
		    $("#recentviewtext").before("<div id='loader-recent'></div>");
            $("#loader-recent").html("<img src="+base_url+"/public/media/images/loader_21X21.gif>");
          },
          success: function(response){
		    //alert(response['result']+'eps'+response['is_empty'])
               if(response['result'] == 'success'){
	           $(ele).parent('li').remove();
			   $("#loader-recent").remove();
	       }
		   //alert($('.recentviewd').find('ul li').html());
               if(response['is_empty'] == 'yes')
                {
                    $('#recentviewtext').html('');
                }
            if(typeof($('.recentviewd').find('ul li').html()) == 'undefined')
                   $('#recentviewtext').html('');
            }
        },'json');
}
function removeOptions(ele){
	$(ele+" option").remove();	
}
function destroyandcreateCombobox(ele){
						//$(ele).combobox("setValue","");
						jQuery(ele).trigger("liszt:updated");
					}
					
function paginationndsorting(url){
//console.log(url);
//alert(url);
			var myarr = url.split("/");
			//if($.inArray('/call/ajaxcall',url) == -1)
			if(url.indexOf('/call/ajaxcall') == -1)                        
				url = url+'/call/ajaxcall';
			
			//var searchData = $("#searchdata").val();
			var dashboardcall = $("#dashboardcall").val();
			/*if(typeof searchData !== 'undefined' && searchData != '')
			{ 
				url = url.replace(/\/\/searchData(.*)%7D/,"");
				url = url.replace(/\/searchData(.*)%7D/,"");
				if(url.indexOf('/searchData/'+searchData) == -1)
                                //if($.inArray('/searchData/'+searchData,url) == -1)
				{
					if(url.indexOf('unitId') == -1)
                                        //if($.inArray('unitId',url) == -1)
					{						
						url = url+'/searchData/'+searchData;
					}
					else
					{
						url = url.replace(/\/+$/,''); //rtrim url by '/'
						if(url.indexOf('unitId/pA==/') != -1 || url.indexOf('unitId/pQ==/') != -1)
                                                //if($.inArray('unitId/pA==/',url) != -1 || $.inArray('unitId/pQ==/',url) != -1)
						{							
							url = url+'searchData/'+searchData;						
						}
						else if(url.indexOf('unitId/pA==') != -1 || url.indexOf('unitId/pQ==') != -1)
                                                //else if($.inArray('unitId/pA==',url) != -1 || $.inArray('unitId/pQ==',url) != -1)
						{
							url = url+'/searchData/'+searchData;
							
						}
						else if(url.indexOf('unitId') != -1)
                                                //else if($.inArray('unitId',url) != -1)
						{						
							if(url.indexOf('context') != -1)
                                                        //if($.inArray('context',url) != -1)
								url = url+'//searchData/'+searchData;
							else
								url = url+'/searchData/'+searchData;
							
							//alert("here in else if"+url);
						}
						else url = url+'searchData/'+searchData;
					}
				}
			}*/
			
			//var divid = myarr[11]; 
			/*if(url.indexOf('http') != -1 || url.indexOf('https') != -1 || url.indexOf('/format/html') != -1)	
                        //if($.inArray('http',url) != -1 || $.inArray('https',url) != -1 || $.inArray('/format/html',url) != -1)	
			{		
				if(url.indexOf(domain_data) != -1)
                                //if($.inArray(domain_data,url) != -1)
				{
					
					if(domain_data.indexOf('sapplica') != -1)
                                        //if($.inArray('sapplica',domain_data) != -1)
					var divid = myarr[11]; 
					else var divid = myarr[12];
				}
				else var divid = myarr[11]; 
			}			
			else 
			{
				if(url.indexOf(domain_data) != -1)
                                //if($.inArray(domain_data,url) != -1)
				divid = myarr[10]; 
				else var divid = myarr[9];
			}*/
			if(url.indexOf("objname") != -1)                        
			{
				divid = url.match(/objname\/(.*?)\//i)[1];
			}	
			
			if(url.indexOf("sort") != -1) 
                        //if($.inArray("sort",url) != -1) 
			{
				var strSortParam = url.substring(url.lastIndexOf('sort')+5);
				
				var sortOrder = strSortParam.substring(0,strSortParam.lastIndexOf('by')-1);
				
				var sortBy = strSortParam.substring(strSortParam.lastIndexOf('by')+3);
				$('#sort_param').val(sortBy+"/"+sortOrder);
			}		
			var searchData = $("#"+divid+"_searchdata").val();
			var perfTimes = $("#gridblock *").serialize();
			searchData = decodeURIComponent(searchData);
			$.post(url,{searchData:searchData,dashboardcall:dashboardcall} , function(response) {		
				$('#grid_'+divid).html(response);
		},'html');
}	

function refreshgrid(objname,dashboardcall)
{	//alert("Obj Name > "+objname);
	var employeeTabs = new Array('dependencydetails','creditcarddetails','visaandimmigrationdetails','workeligibilitydetails','disabilitydetails','empcommunicationdetails','empskills','empleaves','empholidays','medicalclaims','educationdetails','experiencedetails','trainingandcertificationdetails','emppersonaldetails','empperformanceappraisal','emppayslips','empbenefits','emprenumerationdetails','emprequisitiondetails','empadditionaldetails','empsecuritycredentials');	
	var Url ="";var context ="";
	var formGridId = $("#formGridId").val(); 
	var unitId = '';mname='';mnuid='';$('#columnId').remove();
	if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
	formGridId = ''; 
	else
	{
		unitId = formGridId.split("/"); 
		mname = unitId[0]; mnuid = unitId[1];
	}
	//For context.... to apply privileges to internal grids...
	var url = document.URL.split('/');  	//Taking userId from Current URL 
	//if(url.indexOf("mydetails") != -1)
	if($.inArray("mydetails",url) != -1)
	{
		context = 'mydetails';
	}
	//else if(url.indexOf("myemployees") != -1)
	else if($.inArray("myemployees",url) != -1)
	{
		context = 'myteam';
	}
	var dataparam = 'objname='+objname+'&refresh=refresh&call=ajaxcall'+'&'+mname+'='+mnuid+"&context="+context+"&dashboardcall="+dashboardcall;
	
	/*if(employeeTabs.indexOf(objname) != -1)
		Url = base_url+"/"+objname+"/edit/format/html";
	else
		Url = base_url+"/"+objname+"/index/format/html";
		*/
		
	Url = base_url+"/"+objname+"/index/format/html";
	//alert("data Param > "+dataparam+">> URL >> "+Url);
	
	$("#"+objname+"_searchdata").val('');
	
	$.ajax({
		url: Url,   
		type : 'POST',
		data : dataparam,
		success : function(response){
			$('#grid_'+objname).html(response);
		}
	});
}	
function refreshgrid_23092013(objname)
{	//alert("Obj Name > "+objname);
	var formGridId = $("#formGridId").val(); 
	var unitId = '';mname='';mnuid='';
	if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
	formGridId = ''; 
	else
	{
		unitId = formGridId.split("/"); 
		mname = unitId[0]; mnuid = unitId[1];
	}
	var dataparam = 'objname='+objname+'&refresh=refresh&call=ajaxcall'+'&'+mname+'='+mnuid;
	//alert("data Param > "+dataparam);
	$.ajax({
		url: base_url+"/"+objname+"/index/format/html",   
		type : 'POST',
		data : dataparam,
		success : function(response){
			$('#grid_'+objname).html(response);
		}
	});
}

function opensearch(objname)
{
	var dashboardcall = $("#dashboardcall").val();
	if($(".searchtxtbox_"+objname).is(":visible"))
	{
            $('.ui-datepicker-trigger').hide();
            $(".searchtxtbox_"+objname).hide();	
            $("#search_tr_"+objname).hide();	
            refreshgrid(objname,dashboardcall);		
	}
	else 
        {           
            $('.ui-datepicker-trigger').show();
            $(".searchtxtbox_"+objname).show();					
            $("#search_tr_"+objname).show();	
        }
}	
/**
 * This function is used in requisition screen.
 * @param {Object} obj Object of no.of positions
 * @returns {String}  Error message
 */
function check_zerovalue(obj)
{
    var val = parseFloat(obj.value);    
    if(val == 0)
    {
        $('#errors-'+obj.id).remove();
        $('#'+obj.id).parent().append("<span class='errors' id='errors-"+obj.id+"'>No.of positions cannot be zero.</span>");        
    }
}
function addslashes(string) {
    return string.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
        replace(/"/g, '\\"');
}
function getsearchdata(objname,conText,colname,event,etype)
{	//alert("obj name "+objname);
    
    if(etype == 'text')
    {
        var code = event.keyCode || event.which;
        if(code != 13) 
        { 
            return ;
        }
    }
    var dashboardcall = $("#dashboardcall").val();
	var employeeTabs = new Array('dependencydetails','creditcarddetails','visaandimmigrationdetails','workeligibilitydetails','disabilitydetails','empcommunicationdetails','empskills','empleaves','empholidays','medicalclaims','educationdetails','experiencedetails','trainingandcertificationdetails','emppersonaldetails','empperformanceappraisal','emppayslips','empbenefits','emprenumerationdetails','emprequisitiondetails','empadditionaldetails','empsecuritycredentials');	
	var Url ="";
	//var perpage = $(".records_input_"+objname).val(); 
	var perpage = $("#perpage_"+objname).val(); 
	if(perpage == 'undefined' || typeof(perpage) === 'undefined')
	{
		if(dashboardcall == 'Yes')
		perpage = '10';
		else
		perpage = '20';
	}
		
	var page = $(".gotopage_input_"+objname).val();
	var formGridId = $("#formGridId").val(); 
	var unitId = '';var mname='';var mnuid='';var columnid = '';
	if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
	formGridId = ''; 
	else
	{
		unitId = formGridId.split("/"); 
		mname = unitId[0]; mnuid = unitId[1];
	}
		
	var searchData = '{';	
	$('.searchtxtbox_'+objname).each(function() {
		if(this.value != '')
		{
		  searchData += '"'+this.id+'":"'+encodeURIComponent(this.value)+'",';		  
		  if(columnid == '')
		  columnid = colname;
	    } 		
	});
	searchData = searchData.substr(0,(searchData.length - 1));
	if(searchData !='' && searchData !='undefined')
	searchData += '}';
      
		
	if(page == '' || page == 'undefined' || typeof(page) === 'undefined')
	page = $(".currentpage").val();
	
	page = 1; // for to go 1st page with serach results--20 march 2014
	
	var dataparam = 'per_page='+ perpage+'&page='+page+'&call=ajaxcall&objname='+objname+'&'+mname+'='+mnuid+'&context='+conText+'&dashboardcall='+dashboardcall;
	
	if(searchData != '' && searchData != '{}')
            dataparam = dataparam+'&searchData='+searchData;	
           
	$('#'+objname+'_searchdata').remove();
	$('#objectName').remove();
	$('#footer').append("<input type='hidden' value='"+searchData+"' id='"+objname+"_searchdata' />");							
	$('#footer').append('<input type="hidden" value="'+objname+'" id="objectName" />');							
	if ($("#columnId").length)
	$('#columnId').val(columnid);
	else $('#footer').append('<input type="hidden" value="'+columnid+'" id="columnId" />');	
	
	/*if(employeeTabs.indexOf(objname) != -1)
		Url = base_url+"/"+objname+"/edit/format/html";
	else
		Url = base_url+"/"+objname+"/index/format/html";	*/	
	//alert("dataParam >> "+dataparam+ " >> URL >> "+Url);
	Url = base_url+"/"+objname+"/index/format/html";
	
	//console.log(Url);
	
	$.ajax({
		url: Url,   
		type : 'POST',
		data : dataparam,
		success : function(response){					
                        $('#grid_'+objname).html(response);                                      
		}
	});
       
}	
function getsearchdata_23092013(objname)
{
	//var perpage = $(".records_input_"+objname).val(); 
	var perpage = $("#perpage_"+objname).val(); 
	var page = $(".gotopage_input_"+objname).val();
	var formGridId = $("#formGridId").val(); 
	var unitId = '';mname='';mnuid='';
	if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
	formGridId = ''; 
	else
	{
		unitId = formGridId.split("/"); 
		mname = unitId[0]; mnuid = unitId[1];
	}
	searchData = '{';	
	$('.searchtxtbox_'+objname).each(function() {
		if(this.value != '')
		{
		  searchData += '"'+this.id+'":"'+this.value+'",';
	    } 		
	});
	searchData = searchData.substr(0,(searchData.length - 1));
	searchData += '}';
	//searchData = searchData.substring(0, searchData.length - 1);		
	if(page == '' || page == 'undefined' || typeof(page) === 'undefined')
	page = $(".currentpage").val();
	
	var dataparam = 'per_page='+ perpage+'&page='+page+'&call=ajaxcall&objname='+objname+'&'+mname+'='+mnuid;
	if(searchData != '' && searchData != '{}')
		dataparam = dataparam+'&searchData='+searchData;	
	$('#searchdata').remove();
	$('#footer').append("<input type='hidden' value='"+searchData+"' id='searchdata' />");							
	$('#footer').append('<input type="hidden" value="'+objname+'" id="objectName" />');							
	
	$.ajax({
		url: base_url+"/"+objname+"/index/format/html",   
		type : 'POST',
		data : dataparam,
		success : function(response){
			//$('#maincontentdiv').html('');
			//$('#maincontentdiv').html('<div id="gridblock"></div>');				
			//$('#gridblock').html(response);					
			$('#grid_'+objname).html(response);
		}
	});
}
/**
 * This function is ajax function used in group and roles report on click on role count.
 * @param {Integer} group_id = id of group
 * @param {String} sort_name = field name to be sort
 * @param {String} sort_type = sort type like asc,desc
 * @param {String} dialog_id = id of dialog box
 * @returns {HTML} HTML of roles
 */
function getrolepopup(group_id,sort_name,sort_type,dialog_id)
{
    var myPos = [ $(window).width() / 5, 150 ];
    $('#'+dialog_id).dialog({
        resizable: false,        
        modal: true,
        title: 'Roles',
        position :myPos,
        open:function(){
            $.blockUI({ width:'50px',message: $("#spinner").html() });
            $('.ui-widget-overlay').addClass('ui-front-overwrite');
            $('.ui-dialog').removeClass('ui-dialog-buttons');
            $('.ui-dialog').removeClass('ui-front');
            $('.ui-dialog').addClass('ui-btn-overwrite');
            $.post(base_url+"/reports/getrolepopup/format/html",{group_id:group_id,sort_name:sort_name,sort_type:sort_type},function(data){
                $('#'+dialog_id).html(data);
                $.unblockUI();
            },'html');
            
        },
        close:function(){
            $('#'+dialog_id).html('');
            $('#'+dialog_id).dialog('destroy');
        }
    });
}
/**
 * This function is used in groups,roles and employees report to get popup content.
 * @param {String} group_id   = id of group
 * @param {String} role_id    = id of role 
 * @param {Integer} page_no   = page number
 * @param {String} sort_name  = name of the field to be sort.
 * @param {String} sort_type  = sorting type
 * @param {String} dialog_id  = id of dialog box.
 * @param {String} per_page  = number of records per page.
 * @returns {HTML} HTML content of employees.
 */
function emprolesgroup_popup(group_id,role_id,page_no,sort_name,sort_type,dialog_id,per_page)
{
    var myPos = [ $(window).width() / 5, 150 ];
    
    $('#'+dialog_id).dialog({
        resizable: false,
        
        modal: true,
        title: 'Employees/Users',
        position :myPos,
        open:function(){
            $('.ui-widget-overlay').addClass('ui-front-overwrite');
            $('.ui-dialog').removeClass('ui-dialog-buttons');
            $('.ui-dialog').removeClass('ui-front');
            $('.ui-dialog').addClass('ui-btn-overwrite');
            $.blockUI({ width:'50px',message: $("#spinner").html() });
            $.post(base_url+"/reports/emprolesgrouppopup/format/html",{per_page:per_page,group_id:group_id,role_id:role_id,page_no:page_no,sort_name:sort_name,sort_type:sort_type},function(data){
                $('#'+dialog_id).html(data);
                $.unblockUI();
            },'html');
            
        },
        close:function(){
            $('#'+dialog_id).dialog('destroy');
            $('#'+dialog_id).html('');
        }
    });
}
function selectrow(objid,tr)
{
	var row = $(tr);      
	if(!row.hasClass('newclass')){
			row.addClass('newclass')       //add class to clicked row
				.siblings()                //get the other rows
				.removeClass('newclass');  //remove their classes	
	}
}	
function viewrecord(objname)
{
	var hrefData = $('#'+objname+' .newclass').children().children().children().attr('name');
	//if(hrefData != '' && typeof(hrefData) !== 'undefined')
	//{
		//var myarr = hrefData.split("/");
		//var id = myarr[2];
		var id = hrefData;
		if(typeof(id) !== 'undefined')
		{	
			$.ajax({
				url: base_url+"/"+objname+"/view/format/html",
				//url: "/view/format/html",
				type : 'POST',
				data : 'id='+id+'&call=ajaxcall',
				dataType: 'html',
				success : function(response){
					$("#quickview").html(response);
					$("#quickview").dialog({
						resizable: false,
						height:'auto',
						modal: true,
						title: 'View Data',
						width: 600
						
					});			
				}
			}); 
		}
		else return false;
}

/**
 * This function gives all data of requisition ID through ajax call.
 * @param {Object} obj  =  object of requisition ID select box.
 * @returns {Json}  Json array of all values.  
 */
function getApprReqData(obj)
{
    var rval = obj.value;
    $('#emp_type,#additional_info,#priority,#exp_range,#orderdate,#businessunit,#department,#position,#report_manager,#jobtitle,#no_of_positions,#jobdescription,#required_skills,#required_qualifications').html('');
    if(rval != '')
    {
        $.post(base_url+"/default/requisition/getapprreqdata",{req_id:rval},function(data){        
            $('#orderdate').html(data.onboard_date);
            $('#businessunit').html(data.unitname);
            $('#department').html(data.deptname);
            $('#position').html(data.positionname);
            $('#report_manager').html(data.emp_name);
            $('#jobtitle').html(data.jobtitlename);
            $('#no_of_positions').html(data.req_no_positions);
            $('#jobdescription').html(data.jobdescription);
            $('#required_skills').html(data.req_skills);
            $('#required_qualifications').html(data.req_qualification);
            $('#exp_range').html(data.req_exp_years);
            $('#emp_type').html(data.emp_type);
            $('#priority').html(data.req_priority);
            $('#additional_info').html(data.additional_info);
        },'json');
    }
}
/**
 * This function is used for ajax call to get departments on onchange of
 * business units drop down in requisition screen.
 * @param {Object} obj  = object of business unit.
 * @param {String} dept_id = id of department drop down.
 * @param {String} position_id = id of position drop down.
 * @returns {String} Department options.
 */
function getdepts_req(obj,dept_id,position_id)
{
     var val = $(obj).val();
    $('#email_cnt').val('');
    $.post(base_url+"/default/requisition/getdepartments",{bunitid:val},function(data){
        $('#'+dept_id).find('option').remove();
       // $('#'+position_id).find('option').remove();
        $('#'+dept_id).html(data.options);
        $('#s2id_'+dept_id).find('a.select2-choice').find('span').html('Select Department');
        var opt_len = $('#'+dept_id).find('option').length;
        if(opt_len == 1)
        {
            $("#errors-"+dept_id).remove();
           // $('#'+dept_id).parent().append("<span class='errors' id='errors-"+dept_id+"'>No departments configured for this business unit.</span>");
		    $('#'+dept_id).parent().append("<span class='errors' id='errors-"+dept_id+"'>Departments are not configured for this business unit.</span>");
        }
        getemail_cnt(val)
       // $('#s2id_'+position_id).find('a.select2-choice').find('span').html('Select Position');
    },'json');
}
/**
 * This function is used in requisition for getting email count of business unit.
 * @param {Integer} bunit_id
 * @returns {Integer} count of email
 */
function getemail_cnt(bunit_id)
{
    $('#email_cnt').val('');
    if(bunit_id != '')
    {
        $.post(base_url+"/default/requisition/getemailcount",{bunitid:bunit_id},function(data){
            $('#email_cnt').val(data.count);
        },'json');
    }
}
/**
 * This function is used for ajax call to get positions on onchange event of 
 * department drop down in requisition screen.
 * 
 * @param {Object} obj = object of department.
 * @param {String} bunit_id  = id of business unit.
 * @param {String} position_id = id of position dropdown.
 * @param {String} job_id  = id of job title dropdown.
 * @returns {String} Position options.
 */
function getpositions_req(dept_id,bunit_id,position_id,job_id)
{
    var dept_val = $('#'+dept_id).val();
    var bunit_val = $('#'+bunit_id).val();
    var job_val = $('#'+job_id).val();
    if(job_val != '')
    {
        $.post(base_url+"/default/requisition/getpositions",{bunitid:bunit_val,dept_id:dept_val,job_id:job_val},function(data){
            $('#'+position_id).find('option').remove();
            $('#'+position_id).html(data.options);
            $('#s2id_'+position_id).find('a.select2-choice').find('span').html('Select Position');
            var opt_len = $('#'+position_id).find('option').length;
            if(opt_len == 1)
            {
                $("#errors-"+position_id).remove();
              //  $('#'+position_id).parent().append("<span class='errors' id='errors-"+position_id+"'>No positions configured for this job title.</span>");
			  $('#'+position_id).after("<span class='errors' id='errors-"+position_id+"'>Positions are not configured yet.</span>");
            }
        },'json');
    }
    else 
    {
        $('#'+position_id).find('option').remove();
        $('#s2id_'+position_id).find('a.select2-choice').find('span').html('Select Position');
        $('#'+position_id).html("<option value=''>Select Position</option>");
    }
}
function bunit_emailcontacts(bunit_id)
{
    var bunit = $('#'+bunit_id).val();
    if(bunit != '')
    {
        $("#errors-group_id").remove();
        $.post(base_url + "/emailcontacts/getgroupoptions",{bunit:bunit},function(data){
            $('#group_id').find('option').remove();
            $('#group_id').html(data.options);
            $('#s2id_group_id').find('a.select2-choice').find('span').html('Select Group');
            var opt_len = $('#group_id').find('option').length;
            if(opt_len == 1)
            {
                $("#errors-group_id").remove();                   
                $('#group_id').parent().append("<span class='errors' id='errors-group_id'>No more groups are available for this business unit.</span>");
            }
        },'json');
    }
}
/**
 * This function is used to get states on change of countries in add candidates details screen.
 * @param {String} country_id = id of country dropdown.
 * @param {String} state_id = id of state dropdown.
 * @param {String} city_id = id of city dropdown.
 * @returns {Json} String state options in json format.
 */
function getStates_cand(country_id,state_id,city_id)
{
    var country_val = $('#'+country_id).val();
    $.post(base_url+"/default/states/getstatescand",{country_id:country_val},function(data){
            $('#'+state_id).find('option').remove();
            $('#'+city_id).find('option').remove();
            $('#'+state_id).html(data.options);
            $('#s2id_'+state_id).find('a.select2-choice').find('span').html('Select State');
            $('#s2id_'+city_id).find('a.select2-choice').find('span').html('Select City');
        },'json');
    
}
/**
 * This function is used to get cities on change of states in add candidates details screen. 
 * @param {String} state_id = id of state dropdown.
 * @param {String} city_id = id of city dropdown.
 * @returns {Json} String city options in json format.
 */
function getcities_cand(state_id,city_id)
{
    var state_val = $('#'+state_id).val();
    $.post(base_url+"/default/cities/getcitiescand",{state_id:state_val},function(data){           
            $('#'+city_id).find('option').remove();
            $('#'+city_id).html(data.options);           
            $('#s2id_'+city_id).find('a.select2-choice').find('span').html('Select City');
        },'json');
    
}
/**
 * This function is used in roles/edit.phtml,this is for selecting menus
 *  in accordion
 * @param {int} menu_id   = id of menu item.
 * 
 */
function check_child_roles(menu_id,con)
{
    var chk_obj = $('#idcheckbox'+menu_id);
    var chk_status = chk_obj.prop('checked');
    var parent_id = chk_obj.attr('parent_id');
    var rad_btns_yes = new Array('cls_radio_add_yes','cls_radio_edit_yes','cls_radio_delete_yes','cls_radio_view_yes','cls_radio_upatt_yes','cls_radio_viewatt_yes');
    var rad_btns_no = new Array('cls_radio_add_no','cls_radio_edit_no','cls_radio_delete_no','cls_radio_view_no','cls_radio_upatt_no','cls_radio_viewatt_no');
    
    var complete_width = $('.poc-ui-data-control').width();
    $('.left-block-ui-data').css("width", "230");
    $('.right-block-data').css("width", (complete_width-(263)));	

    var classList =chk_obj.attr('class').split(/\s+/);
    if(chk_status)
    {      
        //start of checking parent items.
        $.each( classList, function(index, item){
            if (item !== 'cls_checkboxes') {
               
               var carr = item.split('_');
               
               $('#idcheckbox'+carr[1]).prop('checked',true);
               $('#idcls_checkboxes_'+carr[1]).show('slow','swing');
               $('.cls_radiomenu_yes_'+carr[1]).prop('checked',true); 
            }
            
        });
        //end of checking parent items
        $('.childclass_'+menu_id).prop('checked',true);
        if(con == '')
        {
            var menu_chk_len = $('.cls_radiomenu_yes_'+menu_id).length;//for checking no.of checkboxes for menu items like reports
            if(menu_chk_len > 0)
                $('.cls_radiobuttons_div'+menu_id).show('slow','swing');
        }
        $('.cls_radiomenu_yes_'+menu_id).prop('checked',true);   
        for(i = 0;i < rad_btns_yes.length;i++)
        {
            $('.'+rad_btns_yes[i]+menu_id).prop('checked',true);   
        }
    }
    else 
    {
        $('.childclass_'+menu_id).prop('checked',false);
        if(con == '')
            $('.cls_radiobuttons_div'+menu_id).hide('slow','swing');
        $('.cls_radiomenu_yes_'+menu_id).prop('checked',false);
        $('.cls_radiomenu_no_'+menu_id).prop('checked',false);
        for(i = 0;i < rad_btns_yes.length;i++)
        {
            $('.'+rad_btns_yes[i]+menu_id).prop('checked',false);   
            $('.'+rad_btns_no[i]+menu_id).prop('checked',false);   
        }
        //start of unchecking of parent checkboxes
        var chk_cnt = $('.childclass_'+parent_id+':checked').length;
        
        if(chk_cnt == 0)
        {
            $('#idcheckbox'+parent_id).prop('checked',false);
            $('.cls_radiobuttons_div'+parent_id).hide('slow','swing');
            $('.cls_radiomenu_yes_'+parent_id).prop('checked',false);
            $('.cls_radiomenu_no_'+parent_id).prop('checked',false);
            for(i = 0;i < rad_btns_yes.length;i++)
            {
                $('.'+rad_btns_yes[i]+parent_id).prop('checked',false);   
                $('.'+rad_btns_no[i]+parent_id).prop('checked',false);   
            }
            $.each( classList, function(index, item){
                if (item !== 'cls_checkboxes' && item !== 'childclass_'+parent_id) {
                    var carr = item.split('_');
                    $('#idcheckbox'+carr[1]).prop('checked',false);
                    $('.cls_radiobuttons_div'+carr[1]).hide('slow','swing');
                    $('.cls_radiomenu_yes_'+carr[1]).prop('checked',false);
                    $('.cls_radiomenu_no_'+carr[1]).prop('checked',false);
                    for(i = 0;i < rad_btns_yes.length;i++)
                    {
                        $('.'+rad_btns_yes[i]+carr[1]).prop('checked',false);   
                        $('.'+rad_btns_no[i]+carr[1]).prop('checked',false);   
                    }
                }

            });
        }
        //end of unchecking of parent checkboxes
    }
    
    var checked_checkboxes = parseInt($('.cls_checkboxes:checked').length);
    if(checked_checkboxes >0)
    {
        $('#prev_cnt').val('1');
    }
    else 
    {
        $('#prev_cnt').val('');
    }
}

/**
 * This function is used in roles/edit.phtml,this is for selecting permissions in  menus
 *  in accordion
 * @param {string} class_name   = class name of radion button.
 * @param {object} obj          = object of check box. 
 * @returns {undefined}
 */
function checkradio_child_roles(class_name,obj)
{
    var checked_status = obj.checked;    
    var id = obj.id;
    $('.'+class_name).prop('checked',checked_status);
    var parent_cls = "cls_radiomenu_yes_";
    var parent_id = $('#'+id).attr("data-parent");
    
    var clen = $("."+parent_cls+parent_id+":checked").length;
    if(clen == 0)
    {                
        $('#idcheckbox'+parent_id).trigger('click');
        $('#idcheckbox'+parent_id).prop('checked',false);
        
    }
    //console.log(checked_status);
    
}
function checkradio_child_roles_original(class_name)
{
    $('.'+class_name).prop('checked',true);
}

function displayCountryCode(ele)
{
    var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	   if(id == 'other')
		{
		  $('#othercountrydiv').show();
		  $("#countrycode").val("");
		  $("#citizenship").val("");
		  $('#countrycode').removeAttr('onfocus');
		  $("#countrycode").attr("readonly", false);
		}
	   else{
			$.ajax({
				url: base_url+"/countries/getcountrycode",   
				type : 'POST',
				dataType: 'json',
				data : 'coutryid='+id,
				success : function(response){
					if(response[0]['country_code'] !='')
					{
					 $("#countrycode").attr("readonly", true);
					 $('#countrycode').val(response[0]['country_code']);
					  $('#othercountrydiv').hide();
					  $("#citizenship").val("");
					 //$("#countrycode").attr("disabled","disabled");
					}else
					{
					 $("#countrycode").attr("readonly", true);
					 $('#countrycode').val('default');
					  $('#othercountrydiv').hide();
					  $("#citizenship").val("");
					// $("#countrycode").attr("disabled","disabled");
					}
					
				}
			});
	   }

}
function displaydeptform(url,menuname)
{
	$.ajax({
		type:"post",		
		url:base_url+'/index/checkisactivestatus',
		dataType:'json',
		success: function(response)
		{
			if(response['result'] != 'false')
			{
				
	
				var urlArr = url.split('/');   
				var baseurlArr = base_url.split('/');
				
				
				var request_hostname = window.location.hostname;
				//$('#deptCont').attr('src', url);
				var job_title = '';var country_id = ''; var country = '';var state_id = ''; var state = '';
				var flag = 'yes';
				var urlsplitArr = url.split("/");
				
				var controllername = urlArr[baseurlArr.length];
				//alert(" Url > "+url+" > controller name > "+controllername);	
				if(menuname == 'Employment Status'){
					if($('#screenflag').length > 0){
						screenflag = $('#screenflag').val(); 
						
						if(url.indexOf('addpopup') != -1){
							url =url+'/screenflag/'+screenflag;		
						}		
					}
				}
				
				if(menuname == 'Position'){
					jobtitle_id = $('#jobtitle_id').val();
					if(jobtitle_id!=null && jobtitle_id.length>0){
						job_title = jobtitle_id;
					}else{
						jobtitle = $('#jobtitle').val();
						if(jobtitle!=null && jobtitle.length>0){
							job_title = jobtitle;
						}
					}

					if(url.indexOf('addpopup') != -1){
						url =url+'/jobtitleid/'+job_title;		
					}		
				}
				if(menuname == 'State')
				{
					country_id = $('#country').val();
					if(country_id == '' || typeof country_id == 'undefined') 
					{
						country_id = $('#country_1').val();
					}
					if(country_id == '' || typeof country_id == 'undefined') 
					{
						country_id = $('#countryid').val();
					}
					if(country_id == '' || typeof country_id == 'undefined') 
					{
						country_id = $('#perm_country').val();
					}
					if(country_id!=null && country_id.length>0)
					{
						country = country_id;
					}
					if(url.indexOf('addpopup') != -1 || url.indexOf('addnewstate') != -1)
					{
						url =url+'/selectcountryid/'+country;		
					}		
				}
				if(menuname == 'City')
				{
					country_id = $('#country').val();
					state_id = $('#state').val();
					if(country_id == '' || typeof country_id == 'undefined')
						country_id = $('#country_1').val();
					if(country_id == '' || typeof country_id == 'undefined')
						country_id = $('#countryid').val();
					if(country_id == '' || typeof country_id == 'undefined') 
						country_id = $('#perm_country').val();
					
					if(state_id == '' || typeof state_id == 'undefined')
						state_id = $('#state_1').val();
					if(state_id == '' || typeof state_id == 'undefined')
						state_id = $('#perm_state').val();
					
					if(country_id != null && country_id.length > 0 && typeof country_id != 'undefined')
					{
						country = country_id;
					}
					if(state_id != null && state_id.length > 0 && typeof state_id != 'undefined')
					{
						state = parseInt(state_id);
					}

					if(url.indexOf('addpopup') != -1 || url.indexOf('addnewcity') != -1)
					{
						url =url+'/selectcountryid/'+country+'/selectstateid/'+state;		
					}		
				}
				if(controllername =='interviewrounds')
				{
					//var act_name = urlsplitArr[3];
					var act_name = '';
					if(url.indexOf("interviewrounds") != -1)                        
					{
						act_name = url.match(/interviewrounds\/(.*?)\//i)[1];
					}			
					/*if(url.indexOf('http') != -1 || url.indexOf('https') != -1)
							//if($.inArray('http',url) != -1 || $.inArray('https',url) != -1)
					var act_name = urlsplitArr[4];
					else
					var act_name = urlsplitArr[3];	*/
					
					var deptid = '';
					deptid = $('#deptidHidden').val();
					if(url.indexOf('deptid') == -1)                                       
					{						
						if(deptid!= '' && typeof(deptid) !== 'undefined')
						url = url+'/deptid/'+deptid;
					}
					var int_status = $('#hiddeninterview_status').val();
					var round_status = $('#previousstatus').val();	
					var round_count = $('#interviewroundcount').val();
					if(act_name == 'editpopup')
					{
						if(int_status == 'Completed')
						{				
							jAlert('As the interview process is completed, you cannot edit the record.');
							flag = 'no';
						}
						else
						{
							flag = 'yes';
						}
					}
					else if(act_name == 'addpopup')
					{
						if(int_status == 'Completed' || (round_status != 'Schedule for next round' && round_status !=''))//&& round_status != 'Qualified' && round_status != 'Selected'
						{
							if(int_status == 'Completed')
							jAlert('The interview is completed for the candidate. So you cannot assign a new round to the candidate.');
							else
								jAlert('The candidate is not scheduled to the next round. So you cannot assign a new round to the candidate.');
							flag = 'no';
						}
						else
						{
										if(round_status == '' && round_count != 0)
										{ 
											flag = 'no';
											jAlert('The candidate is not scheduled to the next round. So you cannot assign a new round to the candidate.');
										}
										else
										{
											flag = 'yes';
										}
						}
					}
					else
					{
						flag = 'yes';		
					}
				}	
					if(flag == 'yes'){
						$("body").append('<div id="blockingdiv" class="ui-widget-overlay ui-front"></div>');
						
						var capitalizedtitle = '';
						if(menuname !='')
						{
						  capitalizedtitle = menuname;
						}else
						{
						capitalizedtitle = controllername.substr(0, 1).toUpperCase() + controllername.substr(1);
									if(controllername == 'apprreqcandidates')
									{
										capitalizedtitle = "Candidate Details";
									}else if(controllername == 'processes')
									{
										capitalizedtitle = "Background check process";
									}
						}			
						//$('#'+controllername+'Container').dialog();
						/*window.parent.$('#'+controllername+'Container').css('display','block');
						window.parent.$('#'+controllername+'Cont').attr('src', url);*/    
						$(".closeAttachPopup").remove();
						window.parent.$('#'+controllername+'Container').dialog({
																	open:function(){
																																$('#'+controllername+'Container').css('display','block');
						$('#'+controllername+'Cont').attr('src', url);
																	  //$('#'+controllername+'Cont').html(''); 
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
											width: 780
											});
						// To save uploaded comments file data in the database
						$('#processesCont').contents().find('input[name="bg-check-details-id"]').val(urlsplitArr[5]);
					}
			}
		}
	});
	
}
function displaydeptform_frame(url,menuname)
{
    //$('#deptCont').attr('src', url);
	var job_title = '';var country_id = ''; var country = '';
	var flag = 'yes';
	var urlsplitArr = url.split("/");
	//var controllername = urlsplitArr[2];
	if(url.indexOf('http') != -1 || url.indexOf('https') != -1)
        //if($.inArray('http',url) != -1 || $.inArray('https',url) != -1)
	var controllername = urlsplitArr[3];
	else
	var controllername = urlsplitArr[2];
	//alert(" Url > "+url+" > controller name > "+controllername);	
	if(menuname == 'Position'){
		jobtitle_id = $('#jobtitle_id').val();
		if(jobtitle_id!=null && jobtitle_id.length>0){
			job_title = jobtitle_id;
		}else{
			jobtitle = $('#jobtitle').val();
			if(jobtitle!=null && jobtitle.length>0){
				job_title = jobtitle;
			}
		}

		if(url.indexOf('addpopup') != -1){
			url =url+'/jobtitleid/'+job_title;		
		}		
	}
	if(menuname == 'State'){
		country_id = $('#country').val();
		if(country_id!=null && country_id.length>0){
			country = country_id;
		}

		if(url.indexOf('addpopup') != -1){
			url =url+'/selectcountryid/'+country;		
		}		
	}
		
		if(flag == 'yes'){
			$("body").append('<div id="blockingdiv" class="ui-widget-overlay ui-front"></div>');
			
			var capitalizedtitle = '';
			if(menuname !='')
			{
			  capitalizedtitle = menuname;
			}else
			{
			capitalizedtitle = controllername.substr(0, 1).toUpperCase() + controllername.substr(1);
                        if(controllername == 'apprreqcandidates')
                        {
                            capitalizedtitle = "Candidate Details";
                        }else if(controllername == 'processes')
                        {
                            capitalizedtitle = "Background check process";
                        }
			}			
			//$('#'+controllername+'Container').dialog();
			/*window.parent.$('#'+controllername+'Container').css('display','block');
			window.parent.$('#'+controllername+'Cont').attr('src', url);*/    
			$(".closeAttachPopup").remove();
			window.parent.$('#'+controllername+'Container').dialog({
														open:function(){
                                                                                                                    $('#'+controllername+'Container').css('display','block');
			window.parent.$('#'+controllername+'Cont').attr('src', url);
														  //$('#'+controllername+'Cont').html(''); 
                                                          $(document).bind('keydown',function(e) {
															
															if (e.keyCode === 8) {
																return false;
															};
														});														  
														},
								close: function() {
								 $('#blockingdiv').remove();
                                 $('#'+controllername+'Cont').attr('src', '');
								 $(document).unbind('keydown',function(e) {});
								},
								title: capitalizedtitle,
								height:'auto',
								width: 780
								});
			// To save uploaded comments file data in the database
			$('#processesCont').contents().find('input[name="bg-check-details-id"]').val(urlsplitArr[5]);
		}
	
}

	  
function closeframe(id)
{
	//$('#DepartmentContainer').css('display','none');
	window.parent.$('#DepartmentContainer').dialog("close");
	//parent.document.getElementById('DepartmentContainer').dialog("destroy");
	//$('li.item-a').parent().css('background-color', 'red');
}

function refreshgridfromIframe(objname,dashboardcall)
{	

	var Url ="";var context ="";
	var formGridId = window.parent.$("#formGridId").val();
	var unitId = '';mname='';mnuid='';$('#columnId').remove();
	if(formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
	{
		formGridId = ''; 
		var curUrl = window.parent.$(location).attr('href');
		mname = 'unitId';
		lastChar = curUrl.substr(curUrl.length - 1);
		if(lastChar != '/')
		curUrl = curUrl+'/';
		
		if(curUrl.indexOf("id") != -1)                        
		{
			mnuid = curUrl.match(/id\/(.*?)\//i)[1];
		}
	}
	else
	{
		unitId = formGridId.split("/"); 
		mname = unitId[0]; mnuid = unitId[1];
	}
	if(window.parent.$('#grid_'+objname).length == 0)
	{	
		//div doesn't exist	
		window.parent.$('.total-form-controller').after('<div id="grid_'+objname+'" class="all-grid-control"></div>');
	}	
	
	window.parent.$('#createdept').remove();
	
	
	var dataparam = 'objname='+objname+'&refresh=refresh&call=ajaxcall'+'&'+mname+'='+mnuid+"&context="+context+"&dashboardcall="+dashboardcall;
	
		
	Url = base_url+"/"+objname+"/index/format/html";
	
	window.parent.$("#"+objname+"_searchdata").val('');
	
	$.ajax({
		url: Url,   
		type : 'POST',
		data : dataparam,
		success : function(response){
			window.parent.$('#grid_'+objname).html(response);
		}
	});
}	

function closeiframepopup(controllername,con)
{	
	
	//alert(controllername+" >> "+con);
	//parent.window.location.reload();
	
	if($('#'+controllername+'Container', window.parent.document).html() !='null' && con == 'cancel')
	{
	   window.parent.$('#'+controllername+'Container').dialog('close');
	}
	else if(con == 'refreshgrid')
	{
		window.parent.refreshgridfromIframe(controllername,'No');
		window.parent.$('#'+controllername+'Container').dialog('close');
	}
	else
	{
	  parent.window.location.reload();
	}
  
    /*	window.parent.$('#'+controllername+'Container').dialog('close');
		if(con != 'cancel')
			parent.window.location.reload();
	*/
}

function removeselectoptions(con)
{
	if(con == 'country' || con == 'country_1' || con == 'perm_country')
	{
		window.parent.$('#state').find('option').remove(); 	   
		window.parent.$('#s2id_state .select2-choice span').html('Select state');
		window.parent.$('#state_1').find('option').remove(); 	   
		window.parent.$('#s2id_state_1 .select2-choice span').html('Select state');
		window.parent.$('#perm_state').find('option').remove(); 	   
		window.parent.$('#s2id_perm_state .select2-choice span').html('Select state');
	}	
	window.parent.$('#city').find('option').remove(); 	   
	window.parent.$('#s2id_city .select2-choice span').html('Select city');
	window.parent.$('#city_1').find('option').remove(); 	   
	window.parent.$('#s2id_city_1 .select2-choice span').html('Select city');
	window.parent.$('#perm_city').find('option').remove(); 	   
	window.parent.$('#s2id_perm_city .select2-choice span').html('Select city');
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
function closeiframeAddPopup_identity(addpopupdata,controllername,con,prev_cntrl)
{  		
    if(prev_cntrl == 'usermanagement')
    {
	window.parent.$("#"+con).find('option').remove();	
	window.parent.$("#"+con).parent().find('.select2-container').find('.select2-search-choice').remove();
	window.parent.$("#"+con).html(addpopupdata);       
        window.parent.$('#s2id_'+con).find('a.select2-choice').find('span').html(window.parent.$("#"+con+" option:first").text());
    } 
    else if(prev_cntrl == 'organisationinfo')
    {
        window.parent.$("#"+con).val(addpopupdata);
    }
    else if(prev_cntrl == 'employee')
    {
        window.parent.$("#"+con).val(addpopupdata);        
        window.parent.$("#spanempid").html(addpopupdata);
    }
    if($('#'+controllername+'Container', window.parent.document).html() !='null')
    {
        window.parent.$('#'+controllername+'Container').dialog('close');
        window.parent.$('#errors-'+con).remove();
    }
}
function closeiframeAddPopup_frame(addpopupdata,controllername,con,textstr,prev_iframename)
{
    if($('#'+controllername+'Container', window.parent.document).html() !='null')
    {
        window.parent.$('#'+controllername+'Container').dialog('close');
    }
    if(prev_iframename == 'empskillsCont')
     {
         mobj = window.top.empskillsCont;
     }   
    //console.log(window.parent.$('#'+prev_iframename).html());
    //console.log($('#'+prev_iframename).get(0).contentWindow.data);
    //console.log(addpopupdata,controllername,con,textstr);
    //window.$('#'+prev_iframename).$("#"+con).html('')
    //console.log(window.parent.find('#'+prev_iframename).find("#"+con).html());
    //return false;
	var option = '';
	if(textstr != '')
	{
            var defOption = "<option value=''>Select "+textstr+"</option>";
		//var defOption = $('<option></option>').attr("value", "").text("Select "+textstr);
		mobj.$('#s2id_'+con+' .select2-choice span').html('Select '+textstr);
	}else{
		//var defOption = $('<option></option>').attr("value", "").text(" ");
                var defOption = "<option value=''> </option>";
		mobj.$('#s2id_'+con+' .select2-choice span').html('');
	}
	
	mobj.$("#"+con).find('option').remove();
	
	mobj.$("#"+con).parent().find('.select2-container').find('.select2-search-choice').remove();
	mobj.$("#"+con).html(defOption+addpopupdata);
       // console.log(window.top.$('#'+prev_iframename).$("#"+con).html());
	
}

function closeiframepopup_03102013(controllername,con)
{	
	window.parent.$('#'+controllername+'Container').dialog('close');
    if(con != 'cancel')
   parent.window.location.reload();

}

function displayStateCode_old(ele)
{
    var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
   
	if(id == 'other')
		{
		  $('#otherstatediv').show();
		  //$("#state").parent().find('.select2-container').find('.select2-search-choice').remove();
		  //$('#state').find('option').remove();
		  $("#state").append("<option value='other' label='Other'>Other</option>");
		}
	   else{
			if($("#otherstatediv").is(':visible'))
			  $('#otherstatediv').hide();
			if($('.select2-choices').find('li div').html() == 'Other')
              $('#s2id_state').find('ul li:first').remove();			
			  
			  $('#state option[value="other"]').remove(); 
			  /*$('#state option').each(function(){
			   if (this.value == 'other') {
					//$('#state option[value="other"]').remove();
					$('#state').find('option').remove();
		            $("#state").prepend("<option value='other' label='Other'>Other</option>");
				}else
				{
				   //$("#state").append("<option value='other' label='Other'>Other</option>");
				  return false;
				}
			});*/
			  //alert($('#state').find('option[value="other"]'));
			  //alert( $('#state').find('option').val('other'));
			  //alert($('#state option:contains("Other")'));
			/*if($('#state option[value="other"]')) 
              {	
                alert("true");			  
			    $('#state option[value="other"]').remove(); 
			  }
			else
              {
                alert("false");			  
                $("#state").prepend("<option value='other' label='Other'>Other</option>");			
			  }*/
			//if($("#otherstatecodediv").is(':visible'))  
			 // $('#otherstatecodediv').hide();
	   }

}

function displayStateCode(ele)
{

	/*if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}*/
	
    id = $("#state").val()+',';
    var idarray = id.split(',');
	var idarray = idarray[idarray.length-2];
	if(idarray == 'other')
		{
		   $('#otherstatediv').show();
		}
	else
		{ 
			   $('#otherstatediv').hide();
		}	
	/*if(idarray == 'other')
		{
		 console.log($('#s2id_city').find('ul li[class="select2-search-choice"]').length);
		 alert($('#s2id_city').find('ul li[class="select2-search-choice"]').length);
		   if($('#s2id_city').find('ul li[class="select2-search-choice"]').length > 1)
		    {
			   //$('.select2-choices').find('li:last').prev('li').remove();
			   //jAlert('Sorry, Primary contact cannot be deleted.');
			   	
			    //if($('.select2-choices').find('li div').html() == 'Other')
				 //{
				   console.log(idarray);
				   //$("#city").append("<option title='' value='other'>Other</option>");
				   $('.select2-choices').find('li:last').prev('li').remove();
				   //$('.select2-drop-multi').find('li:last').removeClass('select2-selected');
				   $('.select2-drop-multi').find('.select2-results').find('li:last').removeClass('select2-selected');
		
				  //} 
		       
			}else
			{
			    $('#othercitydiv').show();
			}
		}
	   else{
	  
			if($("#othercitydiv").is(':visible'))
			  $('#othercitydiv').hide();
		
	   }*/

}

function displayCityCode_old(ele)
{
    var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}

	if(id == 'other')
		{
		  $('#othercitydiv').show();
		  $("#city").append("<option value='other' label='Other'>Other</option>");
		  //$("#city").chosen({ limit : 1 });
		}
	   else{
	  
			if($("#othercitydiv").is(':visible'))
			  $('#othercitydiv').hide();
			  
			 if($('.select2-choices').find('li div').html() == 'Other')
              $('#s2id_city').find('ul li:first').remove();			
			  
			  $('#city option[value="other"]').remove(); 
	   }

}
function displayCityCode(ele)
{

	/*if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}*/
	
    id = $("#city").val()+',';
    var idarray = id.split(',');
	var idarray = idarray[idarray.length-2];
	if(idarray == 'other')
		{
		   $('#othercitydiv').show();
		}
	else
		{ 
			   $('#othercitydiv').hide();
		}	
	/*if(idarray == 'other')
		{
		 console.log($('#s2id_city').find('ul li[class="select2-search-choice"]').length);
		 alert($('#s2id_city').find('ul li[class="select2-search-choice"]').length);
		   if($('#s2id_city').find('ul li[class="select2-search-choice"]').length > 1)
		    {
			   //$('.select2-choices').find('li:last').prev('li').remove();
			   //jAlert('Sorry, Primary contact cannot be deleted.');
			   	
			    //if($('.select2-choices').find('li div').html() == 'Other')
				 //{
				   console.log(idarray);
				   //$("#city").append("<option title='' value='other'>Other</option>");
				   $('.select2-choices').find('li:last').prev('li').remove();
				   //$('.select2-drop-multi').find('li:last').removeClass('select2-selected');
				   $('.select2-drop-multi').find('.select2-results').find('li:last').removeClass('select2-selected');
		
				  //} 
		       
			}else
			{
			    $('#othercitydiv').show();
			}
		}
	   else{
	  
			if($("#othercitydiv").is(':visible'))
			  $('#othercitydiv').hide();
		
	   }*/

}
function displayParticularState(ele,con,eleId,countryid){
	var id;
	//alert("Here ele "+ele+" >> eleId >> "+eleId+ " >> cOUNTRY iD >> "+countryid);
	if(countryid !='')
	{
	  id = countryid;
	}else
    {	
		if(ele.selectedIndex != -1){
		 id = ele[ele.selectedIndex].value;
		}else{
			id = '';
		}
	}
	if(id !='')
	{
		$.ajax({
				//url: base_url+"/states/getstates/format/html", 
                url: base_url+"/index/getstates/format/html",				
				type : 'POST',	
				data : 'country_id='+id+'&con='+con,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){	//alert($.trim(response));
				        if($.trim(response) == 'nostates')
						{
						  $("#loader").remove();
						  //$("#errors-"+eleId).show();
						  $("#errors-"+eleId).remove();
						  //$("#errors-"+eleId).html("States not created yet.");
						  if(con == 'otheroption')
						  {
						    $('#s2id_'+eleId).find('ul li:not(:last)').remove(); 
							$("#"+eleId).html("<option value='other'>Other</option>");
                            $('#'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'>All states have been configured already.</span>"); 							
							//$("#errors-"+eleId).html("All states have been configured already.");
						  }
						  else 
						  {
							//$("#errors-"+eleId).html("States are not configured yet.");
                                                        
                                                        if($('#'+eleId).parent().find("span.add-coloum").length > 0)
                                                            $('#'+eleId).parent().find("span.add-coloum").prepend("<span class='errors' id='errors-"+eleId+"'>States are not configured yet.</span>");
                                                        else
                                                            $('#'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'>States are not configured yet.</span>");
							$("#"+eleId).find('option').remove();
							$("#s2id_"+eleId).find("span").html("Select State");
							$("#"+eleId).prepend("<option value='' label='select state'>Select State</option>");
						  }
                        }
                        if(response != '' && response != 'null' && $.trim(response) != 'nostates')
						{ 	
							$('#s2id_'+eleId+' .select2-choice span').html('Select state');
							$("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();											
							$("#loader").remove();
							if($("#errors-"+eleId).is(':visible'))
		                     $("#errors-"+eleId).hide();
							$("#"+eleId).html(response);	 
							if($("#otherstatediv").is(':visible'))
							 $('#otherstatediv').hide();
							if(countryid!='')
							{
							  $('#s2id_'+eleId).find('span').html($("#perm_state option:selected").text());
							  $("#"+eleId).val($("#perm_state option:selected").val());
							} 
							 
							 /* Clear city drop down based on state Ids */
							 if(eleId == 'perm_state')
							   {
									$('#perm_city').find('option').remove();
									$('#s2id_perm_city .select2-choice span').html('Select city');
							   } 
							   else if(eleId == 'current_state')
							   {
								   $('#current_city').find('option').remove();
								   $('#s2id_current_city .select2-choice span').html('Select city');
							   }
							   else if(eleId == 'state')
							   {
									$('#city').find('option').remove(); 	   
									$('#s2id_city .select2-choice span').html('Select city');
									$('#s2id_city ul li:not(:last)').remove();	
							   }
							   else if(eleId == 'issuingauth_state')
							   {
									$('#issuingauth_city').find('option').remove(); 	   
									$('#s2id_issuingauth_city .select2-choice span').html('Select city');
							   }
							   else if(eleId.indexOf('1') != -1){
									$('#city_1').find('option').remove();
									$('#s2id_city_1 .select2-choice span').html('Select city');
							   }else if(eleId.indexOf('2') != -1){
									$('#city_2').find('option').remove();
									$('#s2id_city_2 .select2-choice span').html('Select city');
							   }else if(eleId.indexOf('3') != -1){
									$('#s2id_city_3 .select2-choice span').html('Select city');
									$('#city_3').find('option').remove();
							   } 
                        } 						 
				}
			});
	}
	else
	{
	   //alert(eleId);
	   if(eleId == 'perm_state')
	   {
	       $('#'+eleId).find('option').remove();
		   $('#s2id_'+eleId+' .select2-choice span').html('Select state');
		   $('#perm_city').find('option').remove();
		   $('#s2id_perm_city .select2-choice span').html('Select city');
	   } 
       else if(eleId == 'current_state')
       {
	       $('#'+eleId).find('option').remove();
		   $('#s2id_'+eleId+' .select2-choice span').html('Select state');
		   $('#current_city').find('option').remove();
		   $('#s2id_current_city .select2-choice span').html('Select city');
       }
	   else if(eleId == 'state')
	   {
	        $('#'+eleId).find('option').remove();
		    $('#s2id_'+eleId+' .select2-choice span').html('Select state');
		    $('#city').find('option').remove(); 	   
		    $('#s2id_city .select2-choice span').html('Select city');
			$('#s2id_state ul li:not(:last)').remove();
			$('#s2id_city ul li:not(:last)').remove();	
	   }
	   else if(eleId == 'issuingauth_state')
	   {
	        $('#'+eleId).find('option').remove();
		    $('#s2id_'+eleId+' .select2-choice span').html('Select state');
		    $('#issuingauth_city').find('option').remove(); 	   
		    $('#s2id_issuingauth_city .select2-choice span').html('Select city');
	   }
	   else if(eleId.indexOf('1') != -1){
			$('#city_1').find('option').remove();
			$('#s2id_city_1 .select2-choice span').html('Select city');
	   }else if(eleId.indexOf('2') != -1){
			$('#city_2').find('option').remove();
			$('#s2id_city_2 .select2-choice span').html('Select city');
	   }else if(eleId.indexOf('3') != -1){
			$('#s2id_city_3 .select2-choice span').html('Select city');
			$('#city_3').find('option').remove();
	   }
	}	
}
//function displayParticularState_normal(ele,con,eleId,countryid)
function displayParticularState_normal(ele,con,eleId,city_id)
{
    var id;		
    id = ele.value;
    $('#s2id_'+eleId+' .select2-choice span').html('Select state');
    $('#s2id_'+city_id+' .select2-choice span').html('Select city');                        
    $('#'+eleId).find('option').remove();
    $('#'+city_id).find('option').remove();            
    $("#"+eleId).html("<option value='' label='Select State'>Select State</option>");
    $("#"+city_id).html("<option value='' label='Select State'>Select City</option>");
    if(id !='')
    {
        $.ajax({				
                url: base_url+"/index/getstatesnormal/format/html",				
                type : 'POST',	
                data : 'country_id='+id+'&con='+con,
                dataType: 'html',
                beforeSend: function () {
                    $("#"+eleId).before("<div id='loader'></div>");
                    $("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
                },
                success : function(response){	//alert($.trim(response));
                    if($.trim(response) == 'nostates')
                    {
                        $("#loader").remove();
                        $("#errors-"+eleId).show();
                        //$("#errors-"+eleId).html("States not created yet.");
                        $("#errors-"+eleId).html("States are not configured yet.");
                        $("#"+eleId).find('option').remove();
                        $("#s2id_"+eleId).find("span").html("Select State");
                        $("#"+eleId).prepend("<option value='' label='select state'>Select State</option>");
                    }
                    if(response != '' && response != 'null' && $.trim(response) != 'nostates')
                    { 	
                        $('#s2id_'+eleId+' .select2-choice span').html('Select state');
                        $("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();											
                        $("#loader").remove();
                        if($("#errors-"+eleId).is(':visible'))
                            $("#errors-"+eleId).hide();
                        $("#"+eleId).html(response);	 
                        if($("#otherstatediv").is(':visible'))
                        $('#otherstatediv').hide();
							
                    } 						 
                }
            });
	}		
}
function displayParticularCandidates(ele,flag)
{
	var id;
	
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}

	if(id !='')
	{
		$.ajax({
				//url: base_url+"/cities/getcities/format/html",
                url: base_url+"/scheduleinterviews/getcandidates",				
				type : 'POST',	
				data : 'req_id='+id,
				dataType: 'json',
				beforeSend: function () {
					if(flag == 'cand') { }
					else{
						$("#candidate_name").before("<div id='loader'></div>");
						$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
					}
				},
				success : function(response){							
						if(flag == 'cand')
						{
							$('#job_title').val('');	
							$('#job_title').val(response.jobtitle);		
						}
				        else
						{
							if($.trim(response.candidates) == 'nocandidates')
							{
							  $("#loader").remove();
							  $("#errors-candidate_name").show();
							  //$("#errors-candidate_name").html("Candidates not created yet.");
							  $("#errors-candidate_name").html("Candidates are not added yet.");
							  $("#candidate_name").find('option').remove();
							  $("#s2id_candidate_name").find("span").html("Select candidate");
							  $("#candidate_name").html("<option value='' label='select Candidate'>Select candidate</option>");
							}						
							else if(response.candidates != '' && response.candidates != 'null' && $.trim(response.candidates) != 'nocandidates')
							{
								$("#candidate_name").parent().find('.select2-container').find('.select2-search-choice').remove();					
								$("#loader").remove();
								if($("#errors-candidate_name").is(':visible'))
								 $("#errors-candidate_name").hide();
								$("#candidate_name").html("<option value='' label='select Candidate'>Select candidate</option>"+response.candidates);	 						
							}
							$('#job_title').val('');	
							$('#job_title').val(response.jobtitle);							
							if($.trim(response.managers) == 'nomanagers')
							{
							  $("#loader").remove();
							  $("#errors-interviewer_id").show();
							  $("#errors-interviewer_id").html("No Interviewers.");
							  $("#interviewer_id").find('option').remove();
							  $("#s2id_interviewer_id").find("span").html("Select Interviewer");
							  $("#interviewer_id").html("<option value='' label='select Candidate'>Select Interviewer</option>");
							}						
							else if(response.managers != '' && response.managers != 'null' && $.trim(response.managers) != 'nomanagers')
							{
								$("#interviewer_id").parent().find('.select2-container').find('.select2-search-choice').remove();					
								$("#loader").remove();
								if($("#errors-interviewer_id").is(':visible'))
								 $("#errors-interviewer_id").hide();
								$("#interviewer_id").html("<option value='' label='select Candidate'>Select Interviewer</option>"+response.managers);	 						
							}
						}
				}
			});
	}
	else
	{
	  
	   $("#candidate_name").parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#candidate_name').find('option').remove();
	   $("#candidate_name").html("<option value='' label='Select Candidate'>Select Candidate</option>");
	   $('#job_title').val('');	
	   $("#interviewer_id").parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#interviewer_id').find('option').remove();
	   $("#interviewer_id").html("<option value='' label='select Candidate'>Select Interviewer</option>");
           $('#s2id_interviewer_id').find('a.select2-choice').find('span').html('Select Interviewer');
           $('#s2id_candidate_name').find('a.select2-choice').find('span').html('Select Candidate');
	}
	
}
function displayParticularCity(ele,con,eleId,stateid)
{
  var id;
  if(stateid != '')
  {
    id = stateid;
  }else
  {
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
  }	
	
	if(id !='')
	{
		$.ajax({
				//url: base_url+"/cities/getcities/format/html",
                url: base_url+"/index/getcities/format/html",				
				type : 'POST',	
				data : 'state_id='+id+'&con='+con,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				        if($.trim(response) == 'nocities')
						{
						  $("#loader").remove();
						  $("#errors-"+eleId).show();
						  $("#errors-"+eleId).remove();
						  //$("#errors-"+eleId).html("Cities not created yet.");
						  if(con == 'otheroption')
						  {	
			                $('#s2id_'+eleId).find('ul li:not(:last)').remove();						  
							$("#"+eleId).html("<option value='other'>Other</option>");	
							//$("#s2id_"+eleId).append("<option value='other'>Other</option>");
							$('#'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'>All cities have been configured already.</span>");
							//$("#errors-"+eleId).html("All cities have been configured already.");
						  }
						  else 
						  { 
							 // $('#'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'>Cities are not configured yet.</span>");							 					  
                              if($('#'+eleId).parent().find("span.add-coloum").length > 0)
                                  $('#'+eleId).parent().find("span.add-coloum").prepend("<span class='errors' id='errors-"+eleId+"'>Cities are not configured yet.</span>");
                              else
                                  $('#'+eleId).parent().append("<span class='errors' id='errors-"+eleId+"'>Cities are not configured yet.</span>");
                              
							  $("#"+eleId).find('option').remove();
							  $("#s2id_"+eleId).find("span").html("Select City");
							  $("#"+eleId).prepend("<option value='' label='select city'>Select City</option>");
						  }
                        }
						 if(response != '' && response != 'null' && $.trim(response) != 'nocities')
						{
							if(stateid =='') 
							 $('#s2id_'+eleId+' .select2-choice span').html('Select city');
							$("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();					
							$("#loader").remove();
							if($("#errors-"+eleId).is(':visible'))
		                     $("#errors-"+eleId).hide();
							$("#"+eleId).html(response);	 
							if($("#otherstatediv").is(':visible'))
							 $('#otherstatediv').hide();
							if(stateid!='')
							{
							 $('#s2id_'+eleId).find('span').html($("#perm_city option:selected").text());
							 $("#"+eleId).val($("#perm_city option:selected").val());
							} 
                        }						 
				}
			});
	}
	else
	{
	  
	   $("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#'+eleId).find('option').remove();
	   $("#"+eleId).prepend("<option value='' label='Select city'>Select city</option>");
           $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select city');
	}

}
function displayParticularCity_normal(ele,con,eleId,stateid)
{
  var id;
  if(stateid != '')
  {
    id = stateid;
  }else
  {
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
  }	
	
	if(id !='')
	{
		$.ajax({
				//url: base_url+"/cities/getcities/format/html",
                url: base_url+"/index/getcitiesnormal/format/html",				
				type : 'POST',	
				data : 'state_id='+id+'&con='+con,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				        if($.trim(response) == 'nocities')
						{
						  $("#loader").remove();
						  $("#errors-"+eleId).show();
						  //$("#errors-"+eleId).html("Cities not created yet.");
						  $("#errors-"+eleId).html("Cities are not configured yet.");
						  $("#"+eleId).find('option').remove();
						  $("#s2id_"+eleId).find("span").html("Select City");
	                      $("#"+eleId).prepend("<option value='' label='select city'>Select City</option>");
                        }
						 if(response != '' && response != 'null' && $.trim(response) != 'nocities')
						{
							if(stateid =='') 
							 $('#s2id_'+eleId+' .select2-choice span').html('Select city');
							$("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();					
							$("#loader").remove();
							if($("#errors-"+eleId).is(':visible'))
		                     $("#errors-"+eleId).hide();
							$("#"+eleId).html(response);	 
							if($("#otherstatediv").is(':visible'))
							 $('#otherstatediv').hide();
							if(stateid!='')
							{
							 $('#s2id_'+eleId).find('span').html($("#perm_city option:selected").text());
							 $("#"+eleId).val($("#perm_city option:selected").val());
							} 
                        }						 
				}
			});
	}
	else
	{
	  
	   $("#"+eleId).parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#'+eleId).find('option').remove();
	   $("#"+eleId).prepend("<option value='' label='Select city'>Select city</option>");
           $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select city');
	}

}

/*function displayState(ele){
	
	var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	$.ajax({
	        url: base_url+"/index/getstates/format/html",   
	        type : 'POST',	
	        data : 'country_id='+id,
	        dataType: 'html',
			beforeSend: function () {
		    $("#state").before("<div id='loader'></div>");
            $("#loader").html("<img src="+base_url+"/public/media/images/loader_21X21.gif>");
            },
	        success : function(response){
			        $("#state").parent().find('.select2-container').find('.select2-search-choice').remove();					
			        $("#loader").remove();
	        		$("#state").html(response);	 
	        }
	    });
}

function displayDepartments(ele)
{
  var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	if(id !='')
	{
		$.ajax({
				url: base_url+"/departments/getdepartments/format/html",   
				type : 'POST',	
				data : 'buss_id='+id,
				dataType: 'html',
				beforeSend: function () {
				$("#departmentid").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
						$('#s2id_departmentid .select2-choice span').html('Select Department');
						$("#departmentid").parent().find('.select2-container').find('.select2-search-choice').remove();					
						$("#loader").remove();
						$("#departmentid").html(response);	 
					}
			});
	}
	else
	{
	  
	   $("#departmentid").parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#departmentid').find('option').remove();
	   $("#departmentid").prepend("<option value='' label='Select city'>Select Department</option>");
	}


}*/

function displayTargetCurrency(ele)
{
  var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	var idstr = id.split('!@#');
	if(idstr !='')
	{
		$.ajax({
				//url: base_url+"/currency/gettargetcurrency/format/html",   
				url: base_url+"/index/gettargetcurrency/format/html",   
				type : 'POST',	
				data : 'basecurr_id='+idstr[0],
				dataType: 'html',
				beforeSend: function () {
				$("#targetcurrency").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
						$('#s2id_targetcurrency .select2-choice span').html('Select target currency');
						$("#targetcurrency").parent().find('.select2-container').find('.select2-search-choice').remove();					
						$("#loader").remove();
						$("#targetcurrency").html(response);	 
					}
			});
	}
	else
	{
	  
	   $("#targetcurrency").parent().find('.select2-container').find('.select2-search-choice').remove();
	   $('#targetcurrency').find('option').remove();
	   $("#targetcurrency").prepend("<option value='' label='Select city'>Select Department</option>");
	}


}


function displayPasswordDesc(ele)
{
  var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
  if($('[id^=password_]').html()!='')
    $('[id^=password_]').hide();
	
	$("#password_"+id).show();

}


function displayFormElements()
{
	if($("#secondpoc").is(":visible"))
	{
		$("#thirdpoc").show();
		$("#thirdpocid").val('shown');
		$('#add-more-form').css('display','none');
		$('#less-form').css('display','block');
	}
	else 
	{
		$("#secondpoc").show();
		$("#secondpocid").val('shown');
		$('#less-form').css('display','block');
	}
}

function hideFormElements()
{
	if($("#thirdpoc").is(":visible"))
	{
		$("#thirdpoc").hide();
		$("#thirdpocid").val('');
		$('#add-more-form').css('display','block');
		//$('.less-form').css('display','none');
	}
	else 
	{
		$("#secondpoc").hide();
		$("#secondpocid").val('');
		$('#less-form').css('display','none');
	}
}

function contactElements(eleId)
{
	if(eleId == 'first')
	{		
		$("#firstpoc").show();
		$("#secondpoc").hide();
		$("#thirdpoc").hide();	
		$('[id^="pocli"]').removeClass('active');	
		$('#poclifirst').addClass('active');	
		$('.right-block-data').addClass('right-over-border');		
	}
	else if(eleId == 'second')
	{
		$("#firstpoc").hide();
		$("#secondpoc").show();
		$("#thirdpoc").hide();
		$('[id^="pocli"]').removeClass('active');
		$('#poclisecond').addClass('active');
		$('.right-block-data').removeClass('right-over-border');		
	}
	else if(eleId == 'third')
	{
		$("#firstpoc").hide();
		$("#secondpoc").hide();
		$("#thirdpoc").show();	
		$('[id^="pocli"]').removeClass('active');
		$('#poclithird').addClass('active');
		$('.right-block-data').removeClass('right-over-border');			
	}
}

function contact1Elements()
{
	$("#firstpoc").show();
	$("#secondpoc").hide();
	$("#thirdpoc").hide();	
}

function contact2Elements()
{
	$("#firstpoc").hide();
	$("#secondpoc").show();
	$("#thirdpoc").hide();		
}

function contact3Elements()
{
	$("#firstpoc").hide();
	$("#secondpoc").hide();
	$("#thirdpoc").show();	
}

function deletepoc(agencyid,pocid,contacttype)
{
	if(contacttype == '1')
	{
		jAlert('Sorry, Primary contact cannot be deleted. Please change the contact type and try again.','Message');
		return false;
	}
	else
	{
		$.ajax({
			url: base_url+"/agencylist/deletepoc",   
			type : 'POST',
			data: 'agencyid='+agencyid+'&pocid='+pocid,
			dataType: 'json',
			beforeSend : function () 
							{								
								$.blockUI({ width:'50px',message: $("#spinner").html() });
							},
			success : function(response)
			{
				successmessage_changestatus(response['message'],response['msgtype']);
				window.location.href = base_url+"/agencylist/edit/id/"+agencyid;
			}
		});
	}
}

function getemployeeData(ele)
{
	var id;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	if(id !='' && id!='0')
	{
		$.ajax({
				url: base_url+"/empscreening/getemployeedata/format/html",   
				type : 'POST',	
				data : 'empid='+id,
				dataType: 'html',
				beforeSend: function () {
				$('#company1').css('display','none');$('#company2').css('display','none');$('#company3').css('display','none');
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){						
					$('#personaldatadiv').css('display','block');
					$('#personaldatadiv').html(response);
					var complete_empwidth = $('.emp-screen-view').width();
					$('#displayimg').css("width", "150");
					$('#personalDetailsDiv').css("width", (complete_empwidth-(152)));
				}
			});
	}
	else
		return false;
}

function displayAgencyList()
{
	var val = [];
	$('input:checkbox[id^="checktype"]:checked').each(function(i){
		val[i] = $(this).val();
	});		
	if(val == '')
	{
		$('.agency-selecters').css('display','none');		
		$('.popup-agency-selecters').css('display','none');	
	}
	else
	{
		$.ajax({
		url: base_url+"/empscreening/getagencylist/format/html",   
		type : 'POST',	
		data : 'checktypeid='+val,
		dataType: 'html',
		beforeSend: function () {
		$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
		//$('#checkagency').val('checked');
		},
		success : function(response){		
				if(response !='')
				{
					$('.agency-selecters').css('display','block');		
					$('.popup-agency-selecters').css('display','block');
					 $('#chooseagency').css('display','block');
					 $('#chooseagency').html(response);
					
					var complete_width = $('#chooseagency').width();
					$('#agencyDataDiv').css("width", "230");
					$('#pocdataDiv').css("width", (complete_width-(273)));	
					//$('.right-block-data').addClass('right-over-border');
				}
				else
				{
					$('.agency-selecters').html('');		
					$('.popup-agency-selecters').css('display','none');		
					$('#chooseagency').html('');				
					$('#agencyDataDiv').html('');
					$('#pocdataDiv').html('');				
				}
			}
		});
	}
	
}

function getPOCData(id,divnum)
{			
	id = id.value;
	$.ajax({
		url: base_url+"/empscreening/getpocdata/format/html",   
		type : 'POST',	
		data : 'agencyid='+id,
		dataType: 'html',
		beforeSend: function () {
		//$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
		$('#c1').css('display','none');$('#c3').css('display','none');$('#c2').css('display','none');
		$('#agencyids').val(id);
		},
		success : function(response){
			if(divnum == 0)
			$('.right-block-data').addClass('right-over-border');
			else
			$('.right-block-data').removeClass('right-over-border');
			$('[id^="agenycnameId"]').removeClass('active');
			$('#agenycnameId'+id).addClass('active');
			$('#pocdataDiv').css('display','block');
			$('#pocdataDiv').html(response);
			$("select:not(.not_appli)").select2({
				formatResult: format_select,
				escapeMarkup: function(m) { return m; }
			});
		}
	});
}


/*function validateselecteddate(ele)
{
  if($("#todateerrorspan").is(":visible"))
     $("#todateerrorspan").hide();
  var satholiday =  $('#is_sat_holiday').val();
  var dayselected =  $('#leaveday :selected').val();
  var fromdateval = $('#from_date').val();
  var todateval = $('#to_date').val();
	  
	var fromdateArr = fromdateval.split("-");
	var fromDate = Date.parse(new Date(fromdateArr[2], fromdateArr[0], fromdateArr[1]));
	
	var todateArr = todateval.split("-");
	var toDate = Date.parse(new Date(todateArr[2], todateArr[0], todateArr[1]));
	
	// Logic to calculate number of days based on saturdatholiday 
	var weekday=new Array(7);
	weekday[0]="Monday";
	weekday[1]="Tuesday";
	weekday[2]="Wednesday";
	weekday[3]="Thursday";
	weekday[4]="Friday";
	weekday[5]="Saturday";
	weekday[6]="Sunday";
	
	var from_date = $("#from_date").datepicker('getDate');
    var to_date   = $("#to_date").datepicker('getDate');
	var constantday = '';
	var days = '';
	if(satholiday == 'yes')
	  constantday = 5;
	else  
	  constantday = 6;
	days = calcBusinessDays(from_date,to_date,constantday);
	
   	var oneDay  = 24*60*60*1000;
	var diff_in_days = Math.abs(((to_date.getTime() - from_date.getTime())/oneDay) + 1);
	var fromdayOfWeek = weekday[from_date.getUTCDay()];
	var todayOfWeek = weekday[to_date.getUTCDay()];
		

	if(dayselected == 1)
	{
	 if(satholiday == 'yes')
	    {
			if(fromdayOfWeek != 'Sunday' && fromdayOfWeek != 'Saturday' && todayOfWeek != 'Sunday' && todayOfWeek != 'Saturday')
			{
				if(toDate >= fromDate)
					{
					 $("#appliedleavesdaycount").val(days);
					}
				else
					{
					 $("#todateerrorspan").show();
					 $("#todateerrorspan").html("From Date should be less than To Date");
					}
			}
			else
			{
				 
					$("#todateerrorspan").show();
					$("#todateerrorspan").html("From date or To date cannot be Saturday or Sunday");
				   
			}
        }
      else if(satholiday == 'no')	
        { 
			if(fromdayOfWeek != 'Sunday' && todayOfWeek != 'Sunday' )
			{
				if(toDate >= fromDate)
					{
					 $("#appliedleavesdaycount").val(days);
					}
				else
					{
					 $("#todateerrorspan").show();
					 $("#todateerrorspan").html("From Date should be less than To Date");
					}
			}
			else
			{
				 
					$("#todateerrorspan").show();
					$("#todateerrorspan").html("From date or To date cannot be Sunday");
				   
			}
        }	  
    }else
	{
	  if(satholiday == 'yes')
	    {
		  if((fromdayOfWeek != 'Saturday') && (fromdayOfWeek != 'Sunday'))
		    { 
				if(toDate == fromDate)
				   {
					$("#appliedleavesdaycount").val("0.5 Days");
				   }
				else
				   {
					$("#todateerrorspan").show();
					$("#todateerrorspan").html("From Date and To Date should be same for Half day");
				   }
			}
		  else
            {
				 $("#todateerrorspan").show();
				$("#todateerrorspan").html("Saturday or Sunday cannot be selected");
			   
            } 			
        }
       else if(satholiday == 'no')	
          {
		    if(fromdayOfWeek != 'Sunday')
		    { 
				if(toDate == fromDate)
				   {
					$("#appliedleavesdaycount").val("0.5 Days");
				   }
				else
				   {
					$("#todateerrorspan").show();
					$("#todateerrorspan").html("From Date and To Date should be same for Half day");
				   }
		    }
			else
            {
			 
			    $("#todateerrorspan").show();
				$("#todateerrorspan").html("Sunday cannot be selected");
			   
            }      			
          } 		  
	}
	
	
}*/
/**
 * This function is used in employee details.
 * @param {Object} ele Object of leaving date
 * @returns {String} Error message
 */
function validatejoiningdate(ele)
{
   if($("#errors-date_of_leaving").is(":visible"))
   {
    $("#errors-date_of_leaving").hide();
   }
   var datejoinval = $('#date_of_joining').val();
   var dateleaveval = $('#date_of_leaving').val();
    
    $.post(base_url+"/index/fromdatetodate",{from_val:datejoinval,to_val:dateleaveval},function(data){
        if(data.result == 'no')
        {
            $("#errors-date_of_leaving").show();
            $("#errors-date_of_leaving").html("Date of leaving should be greater date of joining."); 
            $('#date_of_leaving').val('');
        }
    },'json');	

}
/**
 * This function is used to validate from data and to date.
 * @param {String} from_date_id = id of from date text box
 * @param {String} to_date_id   = id of to date text box
 * @param {Object} obj          = object of text box which is selected. 
 * @param {String} message      = error message to be displayed.
 * @returns {String} Alert message.
 */
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
                        /*if(obj_id=="to_date")
                        {
                                $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
                        }
                        else if(obj_id == "from_date")
                        {
                                $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>From date should be less than to date.</span>");
                        }
                        else
                        {	if(obj_id.search("to_date") != -1)
                                        $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
                                else
                                        $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>From date should be less than to date.</span>");
                        }*/
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
/**
 * This function is used to validate from data and to date.
 * @param {String} from_date_id = id of from date text box
 * @param {String} to_date_id   = id of to date text box
 * @param {Object} obj          = object of text box which is selected. 
 * @param {String} message      = error message to be displayed.
 * @returns {String} Alert message.
 */
function from_to_date_validation_org(from_date_id,to_date_id,obj,message)
{
   var obj_id = $(obj).prop('id');	 
   
   var from_val = $('#'+from_date_id).val();   
   var to_val = $('#'+to_date_id).val();
   $("#errors-"+obj_id).remove();
   
   if(to_date_id == 'date_of_joining_head')
   {
		var from_val = window.parent.$('#'+from_date_id).val();		
   }  
    if(from_val != '' && to_val != '')
    {
    	 $("#org_startdate").before("<div id='loader'></div>");
    	 $("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
        $.post(base_url+"/index/fromdatetodateorg",{from_val:from_val,to_val:to_val},function(data){
                if(data.result == 'no')
                {
                	$("#loader").remove();
                        /*if(obj_id=="to_date")
                        {
                                $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
                        }
                        else if(obj_id == "from_date")
                        {
                                $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>From date should be less than to date.</span>");
                        }
                        else
                        {	if(obj_id.search("to_date") != -1)
                                        $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
                                else
                                        $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>From date should be less than to date.</span>");
                        }*/
					$('#errors-org_startdate').remove();
                    $(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>"+message+"</span>");
                    $('#'+obj_id).val('');
                }else{
                	$("#loader").remove();
                	if(from_date_id == 'org_startdate'){
                	   validateorgstartdate(obj,'organisationinfo');
                	}
                }
        },'json');
        
    }
    else
    {
            $('#'+obj_id).trigger('blur');
    }
}
function chk_future_date(obj,msg)
{
    var obj_id = $(obj).prop('id');	
    $("#errors-"+obj_id).remove();
    var from_val = $("#"+obj_id).val();
	var to_val = '';
	  //Future date should be greater than current date...
	if(from_val != '')
    {
		$.post(base_url+"/index/fromdatetodate",{from_val:from_val,to_val:to_val,con:'future'},function(data){
			//alert("validation res > "+data.result);
			if(data.result == 'no')
			{
                            $(obj).val('');
				$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>"+msg+"</span>");
			}
		},'json');
        
    }
	else
	{
		$('#'+obj_id).trigger('blur');
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
    if(dayselected == 1)
	{
	    $("#todatediv").show();
		$('#to_date').val('');
		$('#from_date').val('');
		$("#appliedleavesdaycount").val('');
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
								$('#from_date').val('');
								$("#appliedleavesdaycount").val('');
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

function emptytodate(ele)
{

  /*if($('#to_date').val() != '')
  {
    $('#to_date').val('');
  }
  if($('#appliedleavesdaycount').val() != '')
  {
    $('#appliedleavesdaycount').val('');
  }
  if($("#fromdateerrorspan").is(":visible"))
  {
     $("#fromdateerrorspan").hide();
  }*/
    
  var dayselected =  $('#leaveday :selected').val();
  var fromdateval = $('#from_date').val();
  if(dayselected == 1)
    {  
      validateselecteddate(ele)  
	}
  else if(dayselected == 2)
    {
	  if(fromdateval !='') 
	    $("#appliedleavesdaycount").val(0.5);
	  else
        $("#appliedleavesdaycount").val('');	  
    }	

}

function validateselecteddate(ele)
{

  if($("#todateerrorspan").is(":visible"))
     $("#todateerrorspan").hide();
  //var leavetypeselectedstr =  $('#leavetypeid :selected').val().split('!@#');
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
	
	/* Logic to calculate number of days based on saturdatholiday */
	/*var weekday=new Array(7);
	weekday[0]="Monday";
	weekday[1]="Tuesday";
	weekday[2]="Wednesday";
	weekday[3]="Thursday";
	weekday[4]="Friday";
	weekday[5]="Saturday";
	weekday[6]="Sunday";
	
	var from_date = $("#from_date").datepicker('getDate');
    var to_date   = $("#to_date").datepicker('getDate');
	var constantday = '';
	var days = '';
	if(weekend_startday != weekend_endday)
	  constantday = 5;
	else  
	  constantday = 6;*/
	  
    if(fromdateval != '' && todateval != '')	
	  {
	    //days = calcBusinessDays(from_date,to_date,constantday,weekend_startday,weekend_endday);
		//days = calculateBusinessDays(fromdateArr[2]+'-'+fromdateArr[0]+'-'+fromdateArr[1],todateArr[2]+'-'+todateArr[0]+'-'+todateArr[1]);
		$(ele).parent().append("<span class='errors' id='errors-"+selector+"'></span>"); 
		$.ajax({
					url: base_url+"/index/calculatebusinessdays/format/json",   
					type : 'POST',	
					data : 'fromDate='+fromdateval+'&toDate='+todateval+'&dayselected='+dayselected+'&leavetypelimit='+leavetypelimit+'&leavetypetext='+leavetypetext+'&ishalfday='+ishalfday+'&context='+context+'&selectorid='+selectorid,
					dataType: 'json',
					beforeSend: function ()
					{
						$.blockUI({ width:'50px',message: $("#spinner").html() });
					},
					success : function(response){
					        //alert(response);
					        if(response['result'] == 'success' && response['result'] !='' && response['days'] !='') 
							{
							  $("#appliedleavesdaycount").val(response['days']);
							  //$('#errors-to_date').hide();
							  $("#errors-"+selector).remove();
							  	if(response['availableleaves'] !='' && response['days'] !='')
								{
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
							    /*$("#todateerrorspan").show();
								$("#todateerrorspan").html(response['message']);
								$('#to_date').val('');
								$('#errors-to_date').hide();*/
							}
					      	/*days = response;
							if(dayselected == 1)
								{
									if(toDate >= fromDate)
										{
										  if(leavetypelimit >= days)
											{
											 $("#appliedleavesdaycount").val(days);
											 $('#errors-to_date').hide();
											} 
										  else
											{
											 $("#todateerrorspan").show();
											 $("#todateerrorspan").html(leavetypetext+" leave type permits maximum of "+leavetypelimit+" leaves.");
											 $('#to_date').val('');
											 $('#errors-to_date').hide();
											} 				
										}
									else
										{
										 $("#todateerrorspan").show();
										 $("#todateerrorspan").html("From Date should be less than To Date");
										 $('#to_date').val('');
										 $('#errors-to_date').hide();
										}
								}else
								{
									if(toDate == fromDate)
										{
											if(ishalfday == 1)
											{
											 $("#appliedleavesdaycount").val("0.5 Days");
											 $('#errors-to_date').hide();
											}else
											{
											 $("#todateerrorspan").show();
											 $("#todateerrorspan").html("Half day leave cannot be applied");
											 $('#to_date').val(''); 
											 $('#errors-to_date').hide();
											}  				
										}
									else
										{
											$("#todateerrorspan").show();
											$("#todateerrorspan").html("From Date and To Date should be same for Half day");
											$('#to_date').val('');
											$('#errors-to_date').hide();
										}
										  
								}*/
					}
				});
	  }
	/*else
	  {
	    		
	    $("#fromdateerrorspan").show();
		$("#fromdateerrorspan").html("Please Select From date.");
		$("#fromdateerrorspan").html("Please select date.");
		return false;
	  }*/
	  
   	/*var oneDay  = 24*60*60*1000;
	var diff_in_days = Math.abs(((to_date.getTime() - from_date.getTime())/oneDay) + 1);
	var fromdayOfWeek = weekday[from_date.getUTCDay()];
	var todayOfWeek = weekday[to_date.getUTCDay()];*/
    
}

/* Function to calculate working WEEKDAYS excluding the WEEKEND Dates */
function calcBusinessDays(dDate1, dDate2,constantday,weekend_startday,weekend_endday) { // input given as Date objects
        var iWeeks, iDateDiff, iAdjust = 0;
        if (dDate2 < dDate1) return -1; // error code if dates transposed
        var iWeekday1 = dDate1.getDay(); // day of week
        var iWeekday2 = dDate2.getDay();
		//alert("DAY1 >>"+iWeekday1+">> dAY2 >>"+iWeekday2+">> STARTDAY >>"+weekend_startday+">>END DAY >>"+weekend_endday+">> CONST >>"+constantday);
        //iWeekday1 = (iWeekday1 == weekend_startday) ? 7 : iWeekday1; // change Sunday from 0 to 7
        //iWeekday2 = (iWeekday2 == weekend_endday) ? 7 : iWeekday2;
        if ((iWeekday1 > constantday) && (iWeekday2 > constantday)) iAdjust = 1; // adjustment if both days on weekend
        iWeekday1 = (iWeekday1 > constantday) ? constantday : iWeekday1; // only count weekdays
        iWeekday2 = (iWeekday2 > constantday) ? constantday : iWeekday2;
        //alert(iWeekday1+">>"+iWeekday2);   
        // calculate differnece in weeks (1000mS * 60sec * 60min * 24hrs * 7 days = 604800000)
        iWeeks = Math.floor((dDate2.getTime() - dDate1.getTime()) / 604800000)
         //alert(iWeekday1+">>"+iWeekday2+">>"+iWeeks); 
        if (iWeekday1 <= iWeekday2) {
          iDateDiff = (iWeeks * constantday) + (iWeekday2 - iWeekday1)
        } else {
          iDateDiff = ((iWeeks + 1) * constantday) - (iWeekday1 - iWeekday2)
        }
        // alert(iDateDiff);
        iDateDiff -= iAdjust // take into account both days on weekend
        // alert(iDateDiff);
        return (iDateDiff + 1); // add 1 because dates are inclusive
    }
	
function calculateBusinessDays(from_date,to_date)
{
  $.ajax({
					url: base_url+"/index/calculatebusinessdays/format/json",   
					type : 'POST',	
					data : 'fromDate='+from_date+'&toDate='+to_date,
					dataType: 'json',
					success : function(response){
							//alert(response);
							return response;
					}
				});
}	

function saveExplanation()
{
	var count = $('#inprocesscount').val();
	var params = ''; var id= ''; var text = '';
	var specId = $('#spec_id').val();
	var flag = $('#flag').val();
	var errorFlag = 'true';
	for(i=0;i<count;i++)
	{
		id = $('#id_'+i).val();
		text = $('#text_'+i).val();
		if(text == '' || typeof(text) === 'undefined')
		{
			$('#error_'+i).html('Please enter explanation.');
			errorFlag = 'false';
		}
		if(i!=0) params += '&';
		params += 'id'+i+'='+id+'&text'+i+'='+text;
	}	
	
	params += '&count='+count+'&specimenId='+specId+'&empFlag='+flag;
	if(params != '' && errorFlag != 'false')
	{
		$.ajax({
					url: base_url+"/empscreening/forcedfullupdate/format/json",   
					type : 'POST',	
					data : params,
					dataType: 'json',
					beforeSend: function () {
						$.blockUI({ width:'50px',message: $("#spinner").html() });
						},
					success : function(response){
							if(response['result'] == 'saved')
							{	
								$.unblockUI();
								$('#error_message_force').html('<div class="ml-alert-1-success"><div class="style-1-icon success"></div>Screening Details updated successfully.</div>');
								$('#error_message_force').css('display','block');															
							    setTimeout(function(){
									window.parent.$('#inprocessContainer').dialog('close');		
									window.parent.location.href = base_url+'/empscreening/edit/id/'+specId+'-'+flag;
								},2000);								
							}
					}
				});
		
	}else {
		return false;
	}
}
var hideshowcomments = '2';
function savecommentData()
{
	var comment = $('#commentData').val();
	var detail_id = $('#commentrecord').val();
	var agency_id = $('#agencyrecord').val();
	var hr_id = $('#createduserRecord').val();
	//var letters = /^[A-Za-z]+$/;  
    //var letters =  /^[a-zA-Z][a-zA-Z0-9\-\s]+$/i;
	
	if(comment != '' && comment != 'undefined')
	{		
		$('#cmnterrmsg').html('');
		var data;
		data = 'comment='+comment+'&detailid='+detail_id+'&agency_id='+agency_id+'&hr_id='+hr_id;
		
			$.ajax({
				url: base_url+"/processes/savecomments/format/json",   
				type : 'POST',	
				data : data,
				dataType: 'json',
				success : function(response){
						if(response['result'] == 'saved')
						{				
							$('#respp').css('display','block');
							$('#resppdiv').css('display','block');
							/*$('#commentopendiv').css('display','none');
							$('#commenthidediv').css('display','none');*/
							$('#respp').html('Your comment is Posted successfully.');
							$('#commentData').val('');
							$('#commentData').focus();
							setTimeout(function(){
								$('#respp').css('display','none');
								$('#resppdiv').css('display','none');
								$('#nocomments').css('display','none');
								if(response['commentcount'] == 'morethan2')
								{
									if($("#commentopendiv").is(":visible"))
										$('#commentopendiv').css('display','block');
									else if($("#commenthidediv").is(":visible"))
										$('#commenthidediv').css('display','block');
									else
									{
										$('#postcommentdiv').append('<div id="commentopendiv"  class="commentsdiv"><span style="cursor:pointer;" onclick="displaycommentsarea();">View all comments</span></div>');
										$('#postcommentdiv').append('<div id="commenthidediv" style="display:none" class="commentsdiv"><span style="cursor:pointer;" onclick="hidecommentsarea();">Hide comments</span></div>');
										hideshowcomments == '100'
									}
								}								
								displaycomments(detail_id,'limit');
							},1000);
						}else if(response['result'] == 'error')	
						{
							$('#commentData').focus();
							$('#cmnterrmsg').html('Please enter comment.');
							return false;
						}
				}
			});
		//}else{
		//	return false;
		//}
	}
	else
	{
		$('#commentData').focus();
		$('#cmnterrmsg').html('Please enter comment.');
		return false;
	}
}

function displaycomments(detailId,con)
{	
	if(con == 'all')
	$('#commentopendiv').css('display','none');
	/*else
	$('#commentopendiv').css('display','block');*/
	if(detailId != '' && detailId != 'undefined')
	{
		$.ajax({
			url: base_url+"/processes/displaycomments/format/html",   
			type : 'POST',	
			data : 'detailid='+detailId+'&dispFlag='+con+'&limcount='+hideshowcomments,
			dataType: 'html',
			success : function(response){
				$('#commentsArea').css('display','block');
				$('#commentsArea').html(response);
				if(hideshowcomments == '100')
				{
					$('#commenthidediv').css('display','block');
					$('#commentopendiv').css('display','none');
				}
				else
				{
					$('#commenthidediv').css('display','none');
					$('#commentopendiv').css('display','block');
				}
			}
		});
	}
	else
	{
		return false;
	}
}

function displayStatusdata(controllername)
{

var id;	
 id = $("#statusid").val();

	if(id)
	{
	  if(controllername == 'empscreening')
		window.location.href = base_url+'/'+controllername+'/con/'+id;
	  else
		window.location.href = base_url+'/'+controllername+'/'+id;
	 }
}

function displaycommentsarea()
{
	//$('#commentsArea').css('display','block');
	hideshowcomments = '100';
	detailId = $('#commentrecord').val();
	$('#commentopendiv').css('display','none');
	$('#commenthidediv').css('display','block');
	$('#whitecomment').css('display','none');
	displaycomments(detailId,'all');
}

function hidecommentsarea()
{	
	hideshowcomments = '2';
	$('#commenthidediv').css('display','none');
	$('#commentsArea').css('display','none');
	$('#commentopendiv').css('display','block');
	$('#whitecomment').css('display','block');
}

function displayEmployeeDepartments(ele,eleId,con)
{

  var id;
  var params = '';
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	if(con == 'leavemanagement')
	{
	
	  params += 'business_id='+id+'&con='+con;
	}else
	{
	
	  params += 'business_id='+id;
	}
	//alert("DepartMents > "+params);
	if(id !='')
	{
		$.ajax({
				//url: base_url+"/employee/getdepartments/format/html",
                url: base_url+"/index/getdepartments/format/html",   				
				type : 'POST',	
				data : params,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){	
				        if($.trim(response) == 'nodepartments')
						 {
						    $("#loader").remove();
							//$("#errors-department_id").show();
							$("#errors-"+eleId).show();
						    //$("#errors-department_id").empty();
						    //$("#errors-"+eleId).html("No departments created for the business unit.");
							$("#errors-"+eleId).html("Departments are not added for this business unit.");
							$("#"+eleId).find('option').remove();
							$('#s2id_'+eleId).find('span').html('Select Department');
	                        //$("#"+eleId).prepend("<option value='' label='select department'>Select Department</option>");
										 
						 }
				         if(response != '' && response != 'null' && $.trim(response) != 'nodepartments')
						  {
						    if($("#errors-"+eleId).is(':visible'))
		                     $("#errors-"+eleId).hide();
							$('#s2id_'+eleId).find('span').html('Select Department');
                            $("#loader").remove();
							$("#"+eleId).html(response);
						  }
						  	
						}
			});
	}
	

}
/**
 * This function is used to get reporting managers in employee screen based on role id and department id.
 * @param {String} ele       = department id
 * @param {String} eleId     = reporting manager id
 * @param {String} role_id   = role id
 * @param {String} empId     = id (primary key used in edit screen)
 * @returns {HTML}  All reporting managers in HTML format.
 */
function displayReportingmanagers_emp(ele,eleId,role_id,empId)
{    
    var id = '';  var params = '';var Url='';var employeeId = '';var empRole='';
    $('#errors-'+eleId).remove();
    
    id = $('#'+ele).val();
    params="id="+id;
    
    //alert(empId);
    if(empId != "")
    {
        employeeId = $("#"+empId).val();
        //alert(" employeeId > "+employeeId);
        if(employeeId)
        {
            params="id="+id+"&empId="+employeeId;
        }
    }
					
    empRole = $("#"+role_id).val();
    if(empRole != "")
    {
        var roleArr = empRole.split('_');   
                //alert("roel > "+empRole+"  >> grp > "+empGroup);
        if(roleArr.length > 0)
        {	
            if(roleArr[1] != management_group && $("#"+ele).val()== "")
            {	
                Url="";					
            }
            else
            {
                Url= base_url+"/employee/getempreportingmanagers/format/html";
            }
            params=params+"&empgroup="+roleArr[1];
        }

    }
    else
        Url= base_url+"/employee/getempreportingmanagers/format/html";
				
	//alert("params > "+params+" > con > "+con+" > Url > "+Url);
        //console.log("params > "+params+" > Url > "+Url);
        
    $("#"+eleId).find('option').remove();
    $("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
    $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
    //if(id !='' && Url != "" && empRole != "")
    if(Url != "" && empRole != "")
    {
        $.ajax({
            url:Url,   				
            type : 'POST',	
            data : params,
            dataType: 'html',
            beforeSend: function () {
                $("#"+eleId).before("<div id='loader'></div>");
                $("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
            },
            success : function(response){							
                if($.trim(response) == 'nomanagers')
                {
                    $("#loader").remove();							
                    $("#errors-"+eleId).show();
                    //$("#errors-"+eleId).html("No managers are created for the selected department.");
                    //$("#errors-"+eleId).html("Managers are not added for the selected department.");
                    $("#"+eleId).parent().append('<span id="errors-'+eleId+'" class="errors">Managers are not added for the selected department.</span>');
                    $("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
                    $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');

                }
                else if(response != '' && response != 'null' && $.trim(response) != 'nomanagers')
                {								
                    if($("#errors-"+eleId).is(':visible'))
                        $("#errors-"+eleId).hide();
                                                                    //$('#s2id_'+eleId).find('span').html('Select a Reporting manager');
                    $("#loader").remove();
                    $("#"+eleId).html(response);
                    /*	Append superAdmin option to response if the selected emp role is  'management' */
                    /*if(empRole != "")
                    {
                        if(roleArr.length >0)
                        {
                            if(roleArr[1]== management_group)	//Management person
                            {
                                //$("#reporting_manager").append("<option value = '"+superAdmin_id+"' title=''>Super Admin</option>");
                                $('#s2id_reporting_manager').find('a.select2-choice').find('span').html('Super Admin');
                            }
                        }
                    }*/																			
                }
                else
                {
                    $("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");							 
                    $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
                }						  	
            }
        });
    }
    else 
    {
        $("#"+eleId).find('option').remove();
        $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
        $("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
    }
}
function displayEmpReportingmanagers(ele,eleId,con,empId)
{
	var id;  var params = '';var Url='';var employeeId = '';var empRole='';
        $('#errors-'+eleId).remove();
	if(ele.selectedIndex != -1)
	{
		id = ele[ele.selectedIndex].value;	//drop down selected value....
		params="id="+id;
		
	}
	else
	{
		id = '';
	}	
	//alert(empId);
	if(empId != "")
	{
		employeeId = $("#"+empId).val();
		//alert(" employeeId > "+employeeId);
		if(employeeId)
		{
			params="id="+id+"&empId="+employeeId;
		}
	}
	
	
	if(con == "req")
	{
		Url= base_url+"/requisition/getempreportingmanagers/format/html";
	}
	else
	{
		/*	Purpose:	If the emprole is management set the reporting manager value to superadmin 
						else get the reporting manager based on department.
			Modified Date:	11/6/2013.
			Modified By:	yamni,Ramakrishna.
		*/
		empRole = $("#emprole").val();
		if(empRole != "")
		{
			var roleArr = empRole.split('_');
			var empGroup = $("#emp_group").val();
			
			//alert("roel > "+empRole+"  >> grp > "+empGroup);
			if(roleArr.length > 0)
			{	
				if(roleArr[1]== management_group && $("#department_id").val()== "")
				{	
					Url="";					
				}
				else
				{
					Url= base_url+"/employee/getempreportingmanagers/format/html";
				}
				params=params+"&empgroup="+roleArr[1];
			}
		
		}
		else
			Url= base_url+"/employee/getempreportingmanagers/format/html";
		
	}
	
	//alert("params > "+params+" > con > "+con+" > Url > "+Url);
	if(id !='' && Url != "")
	{
		$.ajax({
				url:Url,   				
				type : 'POST',	
				data : params,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response)
					{							
				       if($.trim(response) == 'nomanagers')
						{
							$("#loader").remove();
							if(con == "req")
							{									
								//$("#"+eleId).parent().append('<span id="errors-reporting_id" class="errors">No managers are created for the selected department.</span>');								
								$("#"+eleId).parent().append('<span id="errors-reporting_id" class="errors">Managers are not added for the selected department.</span>');
								
								$("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
								$('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
								$('#'+eleId).trigger('change');
							}
							else
							{
								$("#errors-"+eleId).show();
								//$("#errors-"+eleId).html("No managers are created for the selected department.");
								//$("#errors-"+eleId).html("Managers are not added for the selected department.");
                                                                $("#"+eleId).parent().append('<span id="errors-'+eleId+'" class="errors">Managers are not added for the selected department.</span>');
                                                                $("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
                                                                $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
							}							 
						}
				        else if(response != '' && response != 'null' && $.trim(response) != 'nomanagers')
						  {
								if(con == "req")
								{
									if($("#errors-"+eleId).is(':visible'))
										$("#errors-"+eleId).hide();
									
									$('#'+eleId).trigger('change');
									$('#s2id_'+eleId).find('span').html('Select a Reporting manager');
									$("#loader").remove();
									$("#"+eleId).html(response);
								}
								else
								{
									if($("#errors-"+eleId).is(':visible'))
									   $("#errors-"+eleId).hide();
									//$('#s2id_'+eleId).find('span').html('Select a Reporting manager');
									$("#loader").remove();
									$("#"+eleId).html(response);
									/*	Append superAdmin option to response if the selected emp role is  'management' */
									if(empRole != "")
									{
										if(roleArr.length >0)
										{
											if(roleArr[1]== management_group)	//Management person
											{
												//$("#reporting_manager").append("<option value = '"+superAdmin_id+"' title=''>Super Admin</option>");
												$('#s2id_reporting_manager').find('a.select2-choice').find('span').html('Super Admin');
											}
										}
									}
											
								}
						 }
						else
						{
							$("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
							 if(con == "req")
							{
								$('#'+eleId).trigger('change');
							}
						}
						  	
					}

				});
	}
	else 
	{
		
		if(con == "req")
		{                
			$("#"+eleId).html("<option value='' label='select a Reporting manager'>Select a Reporting manager</option>");
			$('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select a Reporting manager');
			$('#'+eleId).trigger('change');
		}
	}
}
function displayPositions(ele,eleId,con)
{

  var id;
  var params = '';
	if(con == 'orghead')
	{
		id = ele;
	}
	else
	{
		if(ele.selectedIndex != -1){
		 id = ele[ele.selectedIndex].value;
		}else{
			id = '';
		}
	}
	
	if(con != '')
	{
	
	  params += 'jobtitle_id='+id+'&con='+con;
	}else
	{
	
	  params += 'jobtitle_id='+id;
	}
	if(id !='')
	{
		$.ajax({
				//url: base_url+"/employee/getpositions/format/html", 
                url: base_url+"/index/getpositions/format/html",    				
				type : 'POST',	
				data : params,
				dataType: 'html',
				beforeSend: function () {
				$("#"+eleId).before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				        if($.trim(response) == 'nopositions')
						 {
						    $("#loader").remove();
						    $("#errors-"+eleId).show();					
						    $("#errors-"+eleId).html("Positions are not configured for this job title.");
							$("#"+eleId).find('option').remove();
	                        $("#"+eleId).prepend("<option value='' label='select position'>Select Position</option>");
							$('#s2id_'+eleId).find('span').html('Select Position');		 
						 }
				         if(response != '' && response != 'null' && $.trim(response) != 'nopositions')
						  {
						    if($("#errors-"+eleId).is(':visible'))
		                     $("#errors-"+eleId).hide();
							$('#s2id_'+eleId).find('span').html('Select Position');
                            $("#loader").remove();
							$("#"+eleId).html(response);
						  }
						  	
						}
			});
	}
        else
        {
            $('#'+eleId).find('option').remove();       
            $('#'+eleId).html("<option value='' label='select position'>Select Position</option>");
            $('#s2id_'+eleId).find('a.select2-choice').find('span').html('Select Position');
        }
	

}


function populateCurrentAddress(ele)
{
 
  if ($('#address_flag').is(":checked"))
	{
	  var street_address = $("#perm_streetaddress").val();
	  var country = $("#perm_country").val();
	  var countrytext = $("#perm_country option:selected").text();
	  var state = $("#perm_state").val();
	  var statetext = $("#perm_state option:selected").text();
	  var city = $("#perm_city").val();
	  var citytext = $("#perm_city option:selected").text();
	  var pincode = $("#perm_pincode").val();
	  var errorflag = 1;
	    if(street_address == '')
		{
		  $("#errors-address_flag").show();
		  //$("#errors-address_flag").html("Please enter permanent street address.");
		  $("#errors-address_flag").html("Please enter permanent address fields.");
		  $('#address_flag').removeAttr('checked');
		  return false;
		}
		if(country == '')
		{
		  $("#errors-address_flag").show();
		  //$("#errors-address_flag").html("Please enter country.");
		  $("#errors-address_flag").html("Please enter permanent address fields.");
		  $('#address_flag').removeAttr('checked');
		  return false;
		}
		if(state == '')
		{
		  $("#errors-address_flag").show();
		  //$("#errors-address_flag").html("Please select state.");
		  $("#errors-address_flag").html("Please enter permanent address fields.");
		  $('#address_flag').removeAttr('checked');
		  return false;
		}
		if(city == '')
		{
		  $("#errors-address_flag").show();
		  //$("#errors-address_flag").html("Please select permanent city.");
		  $("#errors-address_flag").html("Please enter permanent address fields.");
		  $('#address_flag').removeAttr('checked');
		  return false;
		}
		if(pincode == '')
		{
		  $("#errors-address_flag").show();
		  //$("#errors-address_flag").html("Please enter permanent postal code.");
		  $("#errors-address_flag").html("Please enter permanent address fields.");
		  $('#address_flag').removeAttr('checked');
		  return false;
		}
		if(street_address !='' && country !='' && state !='' && city !='' && pincode !='')
		{
		  
		  $.blockUI({ width:'50px',message: $("#spinner").html() });
		  if($("#errors-address_flag").is(':visible'))
		     $("#errors-address_flag").hide();
		  $("#current_streetaddress").val(street_address);
		  $("#current_country").val(country);
		  $('#s2id_current_country').find('span').html(countrytext);
		  displayParticularState('',"","current_state",country);
		   setTimeout(function(){
			  displayParticularCity('',"","current_city",state);
			},0100);
		 
		  $("#current_pincode").val(pincode); 
		  
		}
	}else
	{
	  if($("#errors-address_flag").is(':visible'))
		$("#errors-address_flag").hide();
		
	  $("#current_streetaddress").val('');
	  $("#current_country").val('');
	  $('#s2id_current_country').find('span').html('Select Country');
	  
	  
	   $('#current_state').find('option').remove();
	   $('#s2id_current_state').find('span').html('Select State');
	   $("#current_state").val('');
	   //$("#current_state").prepend("<option value='' label='Select State'>Select State</option>");
	   
	   
	   $('#current_city').find('option').remove();
	   $('#s2id_current_city').find('span').html('Select City');
	   $('#current_city').val('');
	   //$("#current_city").prepend("<option value='' label='Select State'>Select City</option>");
	  $("#current_pincode").val(''); 
	}
}
/**
	*	Added By:	Sapplica
	*	Date of Modification :	11/13/2013
	*	Purpose:	TO show the remaining form fields in medical claims form based on injury type
	*	Modified By:	Yamini
**/
function showformFields(id,dateFormatConst)
{	
	var haserrorMsg="";
	if(id != "")
	{
		var injuryType = $("#"+id).val();
		
		/*haserrorMsg = $("#haserrorMsg").val();
		
		alert("haserrorMsg > "+haserrorMsg);
		if(haserrorMsg == 'No')
		{	
			if($(".errors").length > 0)	$(".errors").remove();
			$("#formid")[0].reset();
		}*/
		if(injuryType == 1)
		{	
			//Paternity
			$("#injuryName,#injurySeverity,#OtherdisabilityType,#disabilityType").css("display","none");
			$("#empleave_from,#empleave_to,#empleaveDays,#hospName,#hospAddr,#roomNum,#nameofGP,#treatment,#cost").css("display","block");
			$('#injuredDate label').html('Paternity Date <img class="tooltip" title="'+dateFormatConst+'" src="'+base_url+'/public/media/images/help.png"> ');
			
			$('#injuryName label').html('Disability <img class="tooltip" title="Accepts spaces, hyphens, numbers and characters" src="'+base_url+'/public/media/images/help.png">');
			$("#medicalinsurerName label").text('Medical Insurer');
			$("#approvedLeaveslbl").show();
			$("#medicalclaimslbl").html('Hospital & Medical Claim Details');
			/*	Un bind  other injury type onblur validations to the element based on type 	*/
				$('#disability_type').unbind('blur');
				$('#injury_name').unbind('blur');
				$('#injury_severity').unbind('blur');
				fieldBlurvalidations(injuryType);
				
			//Show form with relavent form fields...
			$("#hasInjury").css("display","block");
			$("#formSubmit").css("display","block");
			
		}
		else if(injuryType == 2)
		{
			// Maternity
			//if($(".errors").length > 0)	$(".errors").remove();
			$("#injuryName,#injurySeverity,#OtherdisabilityType,#disabilityType").css("display","none");
			$("#empleave_from,#empleave_to,#empleaveDays,#hospName,#hospAddr,#roomNum,#nameofGP,#treatment,#cost").css("display","block");	
			$('#injuredDate label').html('Maternity Date <img class="tooltip" title="'+dateFormatConst+'" src="'+base_url+'/public/media/images/help.png"> ');
			$('#injuryName label').html('Disability <img class="tooltip" title="Accepts spaces, hyphens, numbers and characters" src="'+base_url+'/public/media/images/help.png">');
			$("#medicalinsurerName label").text('Medical Insurer');
			$("#approvedLeaveslbl").show();
			$("#medicalclaimslbl").html('Hospital & Medical Claim Details');
			/*	Un bind  other injury type onblur validations to the element based on type 	*/
				$('#disability_type').unbind('blur');
				$('#injury_name').unbind('blur');
				$('#injury_severity').unbind('blur');
				fieldBlurvalidations(injuryType);
			
			//Show form with relavent form fields...
			$("#hasInjury").css("display","block");
			$("#formSubmit").css("display","block");
		}
		else if(injuryType == 4)
		{	
			//if($(".errors").length > 0)	$(".errors").remove();
			//Injury
			$("#injuryName,#injurySeverity,#empleave_from,#empleave_to,#empleaveDays,#hospName,#hospAddr,#roomNum,#nameofGP,#treatment,#cost,#injury_name").css("display","block");
			$("#OtherdisabilityType,#disabilityType").css("display","none");
			$('#injuryName label').html('Injury <img class="tooltip" title="Accepts spaces,hyphens, numbers and characters" src="'+base_url+'/public/media/images/help.png">');
			$('#injuredDate label').html('Date of Injury <img class="tooltip" title="'+dateFormatConst+'" src="'+base_url+'/public/media/images/help.png"> ');
			$("#medicalinsurerName label").text('Medical Insurer');
			$("#approvedLeaveslbl").show();
			$("#medicalclaimslbl").html('Hospital & Medical Claim Details');
			
			/*	Bind onblur validations to the element based on type 	*/
				$('#injury_name').bind('blur');
				$('#injury_severity').bind('blur');
				$('#empleave_from_date').bind('blur');
				$('#empleave_to_date').bind('blur');
				$('#hospital_name').bind('blur');
				$('#hospital_addr').bind('blur');
				$('#room_num').bind('blur');
				$('#gp_name').bind('blur');
				$('#treatment_details').bind('blur');
				$('#total_cost').bind('blur');
			/*	Un bind  other injury type onblur validations to the element based on type 	*/
				$('#disability_type').unbind('blur');
				fieldBlurvalidations(injuryType);
			
			//Show form with relavent form fields...
			$("#hasInjury").css("display","block");
			$("#formSubmit").css("display","block");
		}
		else
		{	
			//Disablity
			//if($(".errors").length > 0)	$(".errors").remove();
			
			$("#injurySeverity,#empleave_from,#empleave_to,#empleaveDays,#hospName,#hospAddr,#roomNum,#nameofGP,#treatment,#cost").css('display','none');
			$("#disabilityType,#injuryName").css("display","block");
			$('#injuryName label').html('Disability <img class="tooltip" title="Accepts spaces, hyphens, numbers and characters" src="'+base_url+'/public/media/images/help.png">');
			$('#injuredDate label').html('Disability Applied Date <img class="tooltip" title="'+dateFormatConst+'" src="'+base_url+'/public/media/images/help.png"> ');
			//$("#medicalinsurerName label").text('Disability Insurer');
			$("#approvedLeaveslbl").hide();
			$("#medicalclaimslbl").html('Medical Claim Details');
			
			/*	Un bind  other injury type onblur validations to the element based on type 	*/
				$('#injury_name').unbind('blur');
				$('#injury_severity').unbind('blur');
				$('#empleave_from_date').unbind('blur');
				$('#empleave_to_date').unbind('blur');
				$('#hospital_name').unbind('blur');
				$('#hospital_addr').unbind('blur');
				$('#room_num').unbind('blur');
				$('#gp_name').unbind('blur');
				$('#treatment_details').unbind('blur');
				$('#total_cost').unbind('blur');
			/* Bind disability onblur validations */
				$('#injury_name').bind('blur');
				$('#disability_type').bind('blur');
				fieldBlurvalidations(injuryType);
				
			//Show form with relavent form fields...
			$("#hasInjury").css("display","block");
			$("#formSubmit").css("display","block");
		}
	}
}
/*	Medical claims Dates  validations
	* Injury,Paternity,Maternity dates should be less than or equal to leave applied by employee(start date).
*/
function medicalclaimDates_validation(from_date_id,to_date_id,obj,datefield_id,conText)
{
//alert(from_date_id+'<>'+to_date_id+'<>'+datefield_id+'<>'+conText);
   var txt="";var new_to_val =""; var userId='';
   var obj_id = $(obj).prop('id');	
   var from_val = $('#'+from_date_id).val();
   var to_val = $('#'+to_date_id).val();
   if(to_date_id == 'leavebyemp_from_date')
   {
      $('#leavebyemp_to_date').val('');
	  $('#leavebyemp_days').val('');
	  $('#empleave_from_date').val('');
	  $('#empleave_to_date').val('');
	  $('#empleave_days').val('');
   }
   if(datefield_id != "")
	{
		new_to_val = $('#'+datefield_id).val();
	}
   //alert(" from_date_id > "+from_date_id+" >to_date_id >  "+to_date_id+" > datefield_id > "+datefield_id);
   var medicalclaimType = $("#type").val();
   if(medicalclaimType != "")
   {	
		switch(medicalclaimType)
		{
			case '1':	txt = "paternity";	break;
			case '2':	txt = "maternity";	break;
			case '3':	txt = "disability";	break;
			case '4':	txt = "injured";	break;
		}
   }
    $("#errors-"+obj_id).remove();
	
	if(from_val != '' && to_val != '')
    {
		$.post(base_url+"/index/medicalclaimdates",{from_val:from_val,to_val:to_val,new_to_val:new_to_val,con:conText,claimtype:txt},function(data)
		{	
			if(data.result == 'no')
			{
				if(conText == 1)
				{
					//Employee applied leave start date should be greater than or equal to injured/maternity/paternity/disability date...
					if(txt != 'maternity' && txt != 'paternity'){
						$("#errors-"+from_date_id).remove();
						$("#errors-"+to_date_id).remove();
						$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Leave start date should be greater than or equal to "+txt+" date.</span>");
					}else{
						$("#errors-"+from_date_id).remove();
						$("#errors-"+to_date_id).remove();
						$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Leave start date should be less than or equal to "+txt+" date.</span>");
					}
				}
				else if(conText == 3)
				{
					//Approved leave start date should be greater than or equal to employee leave start date...
					$("#errors-"+from_date_id).remove();
					$("#errors-"+to_date_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Approved leave start date should be in between employee applied leave limit.</span>");
				}
				else if(conText == 4)
				{
					//Approved leave end date should be in between employee applied leave limit...
					$("#errors-"+from_date_id).remove();
					$("#errors-"+to_date_id).remove();
					$("#errors-"+datefield_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Approved leave end date should be in between employee applied leave limit.</span>");
				}
				else if(conText == 5)
				{	
					// date of joining  should be greater than injury/paternity/maternity/disability date & employee leave applied end date....
					$("#errors-"+from_date_id).remove();
					$("#errors-"+to_date_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Date of joining should be greater than or equal to  "+txt +"  date and employee leave start date.</span>");
				}
				else if(conText == 2)
				{	
					//To & from dates checking ... To date should be greater than from date....
					$("#errors-"+from_date_id).remove();
					$("#errors-"+to_date_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should greater than from date.</span>");
				}
				$('#'+obj_id).val('');
			}
			else
			{
				if(conText == 3)
				{
				  $("#empleave_to_date").val('');
				  $("#empleave_days").val('');
				}

 			//No error in ajax call . Here calculate number of days...
				if(conText == 2 || conText == 4)
				{	
					//TO get employee holiday group we need userId..
					if(document.URL != "")
					{
						var url = document.URL;
						var myarr = url.split("/");
						
							if(url.indexOf("unitId") != -1)                        
							{
								userId = url.match(/unitId\/(.*?)\//i)[1];
							}	
							
					}
					
					calcDays(from_date_id,to_date_id,obj,0,userId);
				}
			}
		},'json');
        
    }
	else
	{
		
	}
	
}
function fieldBlurvalidations(injury_typeVal)
{
	if(injury_typeVal == 3)
	{
		/*	For diability type bind onblur validations to fields....	*/
		$('#injury_name').on("blur",function()
		{
			if($('#errors-injury_name').length)	$("#errors-injury_name").remove();
			if($.trim($("#injury_name").val()) == '')
			{
				if($('#errors-injury_name').length)	$("#errors-injury_name").remove();
				$(this).parent().append("<span class='errors' id='errors-injury_name'>Please enter disability.</span>");
			}
			else
			{	
				$("#errors-injury_name").remove();
			}
		});  
		$('#s2id_disability_type').on("blur",function()
		{	
			if($.trim($("#disability_type").val()) == '')
			{
				if($('#errors-disability_type').length)	$("#errors-disability_type").remove();
				$(this).parent().append("<span class='errors' id='errors-disability_type'>Please select disability type.</span>");
			}
			else if($.trim($("#disability_type").val()) != '' && $.trim($("#disability_type").val()) == 'other impairments')
			{	
				$("#errors-disability_type").remove();
				$('#other_disability_type').on("blur",function()
				{	
					if($("#other_disability_type").val() == "")
					{
						if($('#errors-other_disability_type').length)	$("#errors-other_disability_type").remove();
						$("#other_disability_type").parent().append("<span class='errors' id='errors-other_disability_type'>Please enter any other disability type.</span>");
					}
					else
					{
						$('#errors-other_disability_type').remove();
					}
				});
			}
			else
			{
				$("#errors-disability_type").remove();
			}
			
		}); 
			
	}
	else if(injury_typeVal == 4)
	{
		$('#injury_name').on("blur",function()
		{
			if($('#errors-injury_name').length)	$("#errors-injury_name").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-injury_name'>Please enter injury .</span>");
			}
		});  
		$('#injury_severity').on("blur",function()
		{
			if($('#errors-injury_severity').length)	$("#errors-injury_severity").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-injury_severity'>Please select injury severity.</span>");
			}
		});  
		$('#empleave_from_date').on("blur",function()
		{
			if($('#errors-empleave_from_date').length)	$("#errors-empleave_from_date").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-empleave_from_date'>Please select date.</span>");
			}
			else
			{
				//Check if the approved leave from date is greater than or equal to employee leave applied start date.
				
				medicalclaimDates_validation('leavebyemp_from_date','empleave_from_date',this,'leavebyemp_to_date',3);
			}
		});  
		$('#empleave_to_date').on("blur",function()
		{
			if($('#errors-empleave_to_date').length)	$("#errors-empleave_to_date").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-empleave_to_date'>Please select date.</span>");
			}
			else
			{
				//Check if the approved leave to date is greater than or equal to employee leave applied end date
				medicalclaimDates_validation('empleave_from_date','empleave_to_date',this,'leavebyemp_to_date',4);
			}
		});  
		$('#hospital_name').on("blur",function()
		{
			if($('#errors-hospital_name').length)	$("#errors-hospital_name").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-hospital_name'>Please enter hospital name.</span>");
			}
		}); 
		$('#hospital_addr').on("blur",function()
		{
			if($('#errors-hospital_addr').length)	$("#errors-hospital_addr").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-hospital_addr'>Please enter hospital address.</span>");
			}
		}); 
		$('#room_num').on("blur",function()
		{
			if($('#errors-room_num').length)	$("#errors-room_num").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-room_num'>Please enter /ward number.</span>");
			}
		}); 
		$('#gp_name').on("blur",function()
		{
			if($('#errors-gp_name').length)	$("#errors-gp_name").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-gp_name'>Please enter concerned physician name.</span>");
			}
		}); 
		$('#treatment_details').on("blur",function()
		{
			if($('#errors-treatment_details').length)	$("#errors-treatment_details").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-treatment_details'>Please enter treatment details.</span>");
			}
		}); 
		$('#total_cost').on("blur",function()
		{
			if($('#errors-total_cost').length)	$("#errors-total_cost").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-total_cost'>Please enter total cost.</span>");
			}
		}); 
	}
	else if(injury_typeVal == 1 || injury_typeVal == 2)
	{	
		//Paternity (or) Maternity....
		
		$('#empleave_from_date').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-empleave_from_date').length)	$("#errors-empleave_from_date").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-empleave_from_date'>Please select date.</span>");
			}
			else
			{
				//Check if the approved leave_from_date is greater than or equal to employee leave applied start date. 
				medicalclaimDates_validation('leavebyemp_from_date','empleave_from_date',this,'leavebyemp_to_date',3);
			}
		});  
		$('#empleave_to_date').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-empleave_to_date').length)	$("#errors-empleave_to_date").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-empleave_to_date'>Please select date.</span>");
			}
			else
			{
				//Check if the approved leave to date is greater than or equal to employee leave applied end date & date of injury/maternity/paternity/disability.and should be less than employee leave applied end date...
				//medicalclaimDates_validation('leavebyemp_to_date','empleave_to_date',this,'leavebyemp_to_date',4);
				medicalclaimDates_validation('empleave_from_date','empleave_to_date',this,'leavebyemp_to_date',4);
			}
		});  
		$('#hospital_name').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-hospital_name').length)	$("#errors-hospital_name").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-hospital_name'>Please enter hospital name.</span>");
			}
		}); 
		$('#hospital_addr').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-hospital_addr').length)	$("#errors-hospital_addr").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-hospital_addr'>Please enter hospital address.</span>");
			}
		}); 
		$('#room_num').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-room_num').length)	$("#errors-room_num").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-room_num'>Please enter room number.</span>");
			}
		}); 
		$('#gp_name').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-gp_name').length)	$("#errors-gp_name").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-gp_name'>Please enter concerned physician name.</span>");
			}
		}); 
		$('#treatment_details').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-treatment_details').length)	$("#errors-treatment_details").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-treatment_details'>Please enter treatment details.</span>");
			}
		}); 
		$('#total_cost').on("blur",function()
		{
			//alert($.trim($(this).val()));
			if($('#errors-total_cost').length)	$("#errors-total_cost").remove();
			if($.trim($(this).val()) == '')
			{
				$(this).parent().append("<span class='errors' id='errors-total_cost'>Please enter total cost.</span>");
			}
		});
	}
}
 function showdisabilityField(id)
 {
	if(id != "")
	{
		var disabilityType = $("#"+id).val();
		//alert("disabilityType >> "+disabilityType);
		if(disabilityType == "other impairments")
		{
			$("#OtherdisabilityType").css("display","block");
		}
		else
		{
			$("#OtherdisabilityType").css("display","none");
		}
	}
 }
 
 function confirmadd()
 {
	var status = $('#bgstatusvalue').val();	
	if(status == 'Complete')	
	{		
		jConfirm("You are trying to re-open the background check process for the employee/candidate. Please confirm", "Confirmation", function(r) {
		if(r==true)
		 {
				alert(status);return true;
		 }
		 else {
				return false;
		 }
		 });
	}
	else {
		return true;
	}
 }
 
 function opencontactnumberpopup(id,flag)
 {
  $("#number_value").val('');
  $("#errors-contactnumber").html('');
  var result = '';
  var title = '';
  var number = $('#contactnospan').html();
  
   $("#number_value").bind("blur keyup",function(){
	  validatecontactnumber($("#number_value").val());
	});	
	
  if(flag == 'edit')
  {
    title = "Update Contact Number";
	$("#number_value").val(number);
  }	
  else
  {
    title = "Add Contact Number"; 
  }	

   $("#dialog-confirm").dialog({
       		draggable:false, 
			resizable: false,
		    width:252,
			title: title,
		    modal: true, 
		    /*buttons : {
		        "Ok" : function() {
                    result = validatenumber(id,flag);
					if(result != 'false')
                    { 
					    //$("#errors-contactnumber").html('Invalid phone number');
                        $(this).dialog("close");						
		        	}					
		        },
		        "Cancel" : function() {
				    $("#number_value").val('');
					$("#errors-contactnumber").html('');
		        	$(this).dialog("close");
		        }
		      }*/
			  buttons: [{
                                id:"btn-accept",
                                text: "Ok",
                                click: function() {
                                        result = validatenumber(id,flag);
										if(result != 'false')
										{ 
											//$("#errors-contactnumber").html('Invalid phone number');
											$(this).dialog("close");						
										}
                                }
                        },{
                                id:"dialogcncl",
                                text: "Cancel",
								Class: "ui-button-cancel",
                                click: function() {
                                        $("#number_value").val('');
										$("#errors-contactnumber").html('');
										$(this).dialog("close");
                                }
                        }]
		    });
			//$("#dialogcncl").addClass('ui-button-cancel');
 }
 
 function validatecontactnumber(val)
 {
   $("#errors-contactnumber").html('');
   var contactnumber;
   contactnumber = val.replace(/[^0-9]/g, '');
   var html = '';
    if(val == '')
	{
	  $("#errors-contactnumber").html('Please enter contact number.');

	}
	else if(contactnumber.length != 10) { 
	   $("#errors-contactnumber").html('Please enter valid phone number.');

	}
    else if(contactnumber == '0000000000') { 
	   $("#errors-contactnumber").html('Please enter valid phone number.');

	}	
 }
 
 function validatenumber(id,flag)
 {
  
  var contactnumber = $("#number_value").val();
     contactnumber = contactnumber.replace(/[^0-9]/g, '');
   var html = '';
    if($("#number_value").val() == '')
	{
	  $("#errors-contactnumber").html('Please enter contact number.');
	  msg = "false";
	}
	else if(contactnumber.length != 10) { 
	   $("#errors-contactnumber").html('Please enter valid phone number.');
	   msg = "false"; 
	} 
	else if(contactnumber == '0000000000') { 
	   $("#errors-contactnumber").html('Please enter valid phone number.');
       msg = "false";
	}
	else {
	  $.ajax({
				url: base_url+"/index/updatecontactnumber",   
				type : 'POST',	
				data : 'id='+id+'&contactnumber='+contactnumber,
				dataType: 'json',
				beforeSend: function () {
				$("#number_value").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				             $("#loader").remove();	
							 $("#dialog-confirm").dialog("close");
							 if(flag == 'edit')
							 {
							    $("#contactnospan").html(contactnumber);
							 } 
							 else
                             { 	
                               html ='<span class="number-edit"><input type="button" value="Update" onclick="opencontactnumberpopup('+id+',\'edit\');"></span>';							 
							   $("#contactnospan").html(contactnumber);
							   $(".number-add").html(html);
							 }  
							 $('#empdetailsmsgdiv').show();
							 $('#successtext').html(response['message']);
							 setTimeout(function(){
							  $('#empdetailsmsgdiv').fadeOut('slow');
							  //$('.ml-alert-1-success').fadeOut('slow');
							},3000);
                         }
						
			    });
	     msg = "true";
	} 
   return msg;
 }

/* Function to calculate working WEEKDAYS excluding the WEEKEND Dates */
function calcDays(from_date_id, to_date_id,obj,conText,userId) 
{ 	
	var obj_id = $(obj).prop('id');
	var from_val = $('#'+from_date_id).val();
	var to_val ='';	var Url="";
   if(conText == 1)	//Calculating age...
   {	
	Url=base_url+"/index/calculatedays/format/json";
   }
   else
   {	//Calculating No of days b/w from date & to date...
		to_val = $('#'+to_date_id).val();
		Url=base_url+"/index/calculatebusinessdays/format/json";		
	}
 
   $("#errors-"+obj_id).remove();
   if(from_val != '' && (to_val != '' || conText == 1))
    {
		$.post(base_url+"/index/fromdatetodate",{from_val:from_val,to_val:to_val,con:conText},function(data){
			if(data.result == 'no')
			{
				if(obj_id=="to_date")
				{
					$("#errors-"+obj_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
				}
				else if(obj_id == "from_date")
				{
					$("#errors-"+obj_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>From date should be less than to date.</span>");
				}
				else if(obj_id == "dependent_dob")
				{
					$("#errors-"+obj_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>Date of birth should be less than current date</span>");
				}
				else
				{	
					$("#errors-"+obj_id).remove();
					$(obj).parent().append("<span class='errors' id='errors-"+obj_id+"'>To date should be greater than from date.</span>");
				}
				$('#'+obj_id).val('');
			}
			else if(data.result == 'yes')
			{	
				//No errors....
				// Make ajax call to calculate days....
				
				$.ajax({
					url: Url,   
					type : 'POST',	
					dataType: 'json',
					data : 'fromDate='+from_val+"&toDate="+to_val+"&conText="+conText+"&userId="+userId,
					beforeSend: function () 
					{
						if(conText == 1)	$("#"+from_date_id).before("<div id='loader'></div>");
						else				$("#"+to_date_id).before("<div id='loader'></div>");
						
						$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
					},
					success : function(response)
					{
						$("#loader").remove();	
						if(from_date_id == "leavebyemp_from_date")
							$("#leavebyemp_days").val(response);
						else if(from_date_id == "empleave_from_date")
							$("#empleave_days").val(response);
						else
							$('#dependent_age').val(response);
					}
							
				 });
				
				
			}
		},'json');
        
    }
	//$(obj).trigger('blur');	//Triggering blur function...
  }



function displayempstatusmessage()
{
 var empstatusval = $("#emp_status_id").val();
 var empstatustext = $("#emp_status_id option:selected").text();
 
	 if(empstatusval == 8 || empstatusval == 9 || empstatusval == 10)
	 {
	 
	   //$("#empstatusmessage").html("If you select "+empstatustext+" as employment status then the employee will not be able to log in to the system");
	  $("#empstatusmessage").html("You are trying to change the employment status of this employee to "+empstatustext+". The employee will not be able to log into the system.");
	    $("#empstatus-alert").dialog({
       		draggable:false, 
			resizable: false,
		    width:252,
			title: "Alert",
		    modal: true, 
		    buttons : {
		        "Ok" : function() {
				$(this).dialog("close");								
		        }
		      }
		    });
	    
		//jAlert("You are trying to change the employment status of this employee to "+empstatustext+". The employee will not be able to log into the system.", "Alert");
	 
	 }

}

function gobacktocontroller(context)
{ 
if(context == 'view' || context == 'edit')
  window.location.href = base_url+'/employee';
else if(context == 'myemployees')
 window.location.href = base_url+'/myemployees';
 else 
 window.location.href = base_url+'/mydetails/edit';
 
 
}

function showleavealert(leavetransfercount,prevyear)
{
 //alert(leavetransfercount);
  if($("#emp_leave_limit").val() !='')
    {
	 if(leavetransfercount !='' && prevyear !='')
	 {
	   $("#empleavesmessage").html(leavetransfercount+" leaves will be transfered to the employee from the year "+prevyear);
	    $("#empleaves-alert").dialog({
       		draggable:false, 
			resizable: false,
		    width:252,
			title: "Transfer of Leaves",
		    modal: true, 
		    buttons : {
		        "Ok" : function() {
				document.getElementById("formid").submit();
				$(this).dialog("close");								
		        },
				"Cancel" : function() {
		        	$(this).dialog("close");
		        }
		      }
		    });
	 
	 
	 }
   }else
   {
    document.getElementById("formid").submit(); 
   }   
	

}
function makeActiveInactive_1(status,emp_id)
{
    if(status == 'other')
	{
	   var empstatus = '';
	   if(emp_id == 2)
	     empstatus = 'Resigned';
	   else if(emp_id == 3)
         empstatus = 'Left';
       else if(emp_id == 4)
         empstatus = 'Suspended';		 
	   jAlert('You cannot activate / inactivate an employee when the employement status is '+empstatus+'', 'Alert');
	   
	}
	else
	{

			if(status == 'inactive')
				var mstatus = 'inactivate this employee';
			else
				mstatus = 'activate this employee';
			jConfirm("Are you sure you want to "+mstatus+'?', 'Confirmation', function(r) {
				if(r==true)
				{  
					$.post(base_url+"/default/employee/makeactiveinactive",{emp_id:emp_id,status:status},function(data){
						if(data.result == 'yes')
						{
							//$('.ml-alert-1-success').css('display','block');
							$('#empdetailsmsgdiv').show();
							if(status == 'active')
							{
								var parent = $('.cb-enable').parents('.switch');
								$('.cb-disable',parent).removeClass('selected');
								$('.cb-enable').addClass('selected');
								/*$('.cb-enable').removeClass('selected');
								$('.cb-disable').addClass('selected');*/
								$('#successtext').html("Employee activated successfully.");
								$('.cb-enable').unbind('click');
								$(".cb-disable").bind("click", (function () {
									makeActiveInactive("inactive",emp_id);
								}));
							}
							else 
							{
								var parent = $('.cb-disable').parents('.switch');
								$('.cb-enable',parent).removeClass('selected');
								$('.cb-disable').addClass('selected');
								/*$('.cb-disable').removeClass('selected');
								$('.cb-enable').addClass('selected');*/
								$('#successtext').html("Employee inactivated successfully.");
								$('.cb-disable').unbind('click');
								$(".cb-enable").bind("click", (function () {
									makeActiveInactive("active",emp_id);
								}));
							}
							/*setTimeout(function(){
									$('.ml-alert-1-success').fadeOut('slow');
							},3000);*/
							
							setTimeout(function(){
							  $('#empdetailsmsgdiv').fadeOut('slow');
							  //$('.ml-alert-1-success').fadeOut('slow');
							},3000);
						}
					},'json');
				}
			});
	}
}

function makeActiveInactive(status,emp_id)
{
    if(status == 'other')
	{
	   var empstatus = '';
	   if(emp_id == 2)
	     empstatus = 'Resigned';
	   else if(emp_id == 3)
         empstatus = 'Left';
       else if(emp_id == 4)
         empstatus = 'Suspended';		 
	   jAlert('You cannot activate / inactivate an employee when the employement status is '+empstatus+'', 'Alert');
	   
	}
	else
	{

			if(status == 'inactive')
				var mstatus = 'inactivate this employee';
			else
				mstatus = 'activate this employee';
			jConfirm("Are you sure you want to "+mstatus+'?', 'Confirmation', function(r) {
				if(r==true)
				{
				
					var hasteam = $('#hasteam').val();
					if(hasteam == 'true' && status == 'inactive')
					{
						var url	=	domain_data+"employee/makeactiveinactive/emp_id/"+emp_id+"/status/"+status+"/hasteam/"+hasteam;
						var menuname = 'Re-assign reporting manager';
						displaydeptform(url,menuname);
					}
					else
					{
						$.post(base_url+"/default/employee/makeactiveinactive",{emp_id:emp_id,status:status,hasteam:hasteam},function(data){
							if(data.result == 'yes')
							{
								//$('.ml-alert-1-success').css('display','block');
								$('#empdetailsmsgdiv').show();
								if(status == 'active')
								{
									var parent = $('.cb-enable').parents('.switch');
									$('.cb-disable',parent).removeClass('selected');
									$('.cb-enable').addClass('selected');
									/*$('.cb-enable').removeClass('selected');
									$('.cb-disable').addClass('selected');*/
									$('#successtext').html("Employee activated successfully.");
									$('.cb-enable').unbind('click');
									$(".cb-disable").bind("click", (function () {
										makeActiveInactive("inactive",emp_id);
									}));
								}
								else 
								{
									var parent = $('.cb-disable').parents('.switch');
									$('.cb-enable',parent).removeClass('selected');
									$('.cb-disable').addClass('selected');
									/*$('.cb-disable').removeClass('selected');
									$('.cb-enable').addClass('selected');*/
									$('#successtext').html("Employee inactivated successfully.");
									$('.cb-disable').unbind('click');
									$(".cb-enable").bind("click", (function () {
										makeActiveInactive("active",emp_id);
									}));
								}
								/*setTimeout(function(){
										$('.ml-alert-1-success').fadeOut('slow');
								},3000);*/
								
								setTimeout(function(){
								  $('#empdetailsmsgdiv').fadeOut('slow');
								  //$('.ml-alert-1-success').fadeOut('slow');
								},3000);
							}
						},'json');
					}
				}
			});
	}
}

function profileImageSave(){
	$("#profile_add").hide();
	var profile_photo = $('#uploadimagepath').val();
	
	var user_id = $('#userid').val();
	
	 $.ajax({
		url: base_url+'/dashboard/update',  
		async:false,
		data:'user_id='+user_id+'&profile_photo='+profile_photo,			
		type : 'POST',
		dataType: 'json',
		beforeSend: function () {
		   //$("#loadersuccess").show();
		   //$('.wrapperdivright').block({message: $("#spinner").html() });
		   // $(".profile-tabs-right-1 ul").after("<div id='bigloader'></div>");
          //  $("#bigloader").html("<img src="+base_url+"/public/media/images/loader2.gif>");
			$("#loaderimg").show();
        },
		success : function(response){
		    if(response == 'update'){
			        //$("#bigloader").remove(); 
					$("#loaderimg").hide();
					$("#profimg").html('<img  id="prof_image" src='+base_url+'/public/uploads/profile/'+profile_photo+' width="28" height="28" border="0">');
					successmessage('Your profile image updated.');
					/*$('.settingserror').show();
					$('.settingserror').css('color','green');
					$('.settingserror').html("Your Profile Image updated");*/
					 
					 //$("#loadersuccess").hide();
					/* setTimeout(function(){
						$('.settingserror').fadeOut('slow');
						//$('.wrapperdivright').block({message: $("#spinner").html() });
					    //window.location.href = base_url+'/dashboard/viewprofile';
					 },3000);*/
            }		
		}
	},'json'); 
}

function validatecost()
{

  if($("#errors-amountvalidation").is(":visible"))
    $("#errors-amountvalidation").hide();
	
  var totalcost = $("#total_cost").val();
  var amountclaimed = $("#amount_claimed").val();
   var type = $("#type").val();
  if(totalcost == '' && amountclaimed !='' && (type != "" && type != 3))
	  {
        $('#amount_claimed').after("<span class='errors' id='errors-amountvalidation'>Please enter total cost first.</span>");
	   	return false;
	  }
  else if((parseInt(amountclaimed) > parseInt(totalcost)) && (type != "" && type != 3))
     {
	    $('#amount_claimed').after("<span class='errors' id='errors-amountvalidation'>Amount claimed cannot be more than total cost.</span>");
        return false;
     }	 

}


function displayotherdocumentdiv(ele)
{

    if ($('#othercheck').is(":checked"))
	{
	   $('#otherdocument').val('');
	   $('#otherdocumentdiv').show();
	}else
	{
	  $('#otherdocument').val('');
	  $('#otherdocumentdiv').hide(); 
	}
}


function createorremoveshortcut(menuid,shortcutflag)
 {
    var actionvar = '';
	var html = '';
	
	if(shortcutflag == 1)
	  actionvar = 'make shortcut';
	else if(shortcutflag == 2)
      actionvar = 'remove shortcut';
    else if(shortcutflag == 3)
      actionvar = 'make shortcut';
	  
	if($("#errors-pageshortcut").is(":visible"))  
      $("#errors-pageshortcut").hide();  
	  
    //jConfirm("Are you sure you want to "+actionvar+'?', 'Confirmation', function(r) {
		//if(r==true)
		//{
			$.ajax({
				url: base_url+"/index/createorremoveshortcut",   
				type : 'POST',	
				data : 'menuid='+menuid+'&shortcutflag='+shortcutflag,
				dataType: 'json',
				beforeSend: function () {
				$("#pageshortcut").before("<div id='loader-shortcut'></div>");
				$("#loader-shortcut").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				           //alert(response['result']);
				            $("#loader-shortcut").remove(); 
				            if(response['result'] !='' )
							{
							    if(response['result'] !='error' && response['result'] !='inactive')
                                { 	
                                     if($.trim(response['result']) == 'limit')
                                     {
									      jConfirm("You have already added 16 shortcut icons.Please visit settings page to manage your shortcut icons", "Confirmation",function(r)
											{
												if(r==true)
												{
													window.location = base_url+"/viewsettings/2";
												}else{
													return false;
												}
											});
                                     }
                                     else
                                     {									 
										 if(shortcutflag == 1 || shortcutflag == 3)
										 {
											html ="<div id='pageshortcut' class ='activeshortcut' onclick='createorremoveshortcut("+menuid+",2)'>Remove Shortcut";
											html +="</div>";
											/*$("#pageshortcut").removeClass('inactiveshortcut').addClass('activeshortcut');
											$("#pageshortcut").html('Remove Shortcut');
											$("#pageshortcut").unbind('click');
											$("#pageshortcut").bind("click", (function () {
												createorremoveshortcut(menuid,2);
											}));*/
										 }
										 else if(shortcutflag == 2)
										 {
											html ="<div id='pageshortcut' class ='inactiveshortcut' onclick='createorremoveshortcut("+menuid+",1)'>Make Shortcut";
											html +="</div>"; 
											/*$("#pageshortcut").removeClass('activeshortcut').addClass('inactiveshortcut');
											$("#pageshortcut").html('Make Shortcut');
											$("#pageshortcut").unbind('click');
											$("#pageshortcut").bind("click", (function () {
												createorremoveshortcut(menuid,1);
											}));*/
										 }
										 $("#pageshortcutdiv").html(html);
										 location.reload();
									 }
								} 
							
								if(response['result'] =='error')
								{
								   $('#pageshortcut').before("<span class='shortcuterrors' id='errors-pageshortcut'>You cannot "+actionvar+".</span>");
								}
								
								if(response['result'] =='inactive')
								{
								   $('#pageshortcut').before("<span class='shortcuterrors' id='errors-pageshortcut'>You cannot "+actionvar+".</span>");
								}
							}
				             
                         }
						
			    });
		//}
    //});		
	
 }
 
 
 function validateorgstartdate(ele,con,bunitid)
 {
 var startdate = $(ele).val();
 var errormsg;
  if(con == '')
   con = 'organisationinfo';
 if($(".errors").is(":visible"))
	$(".errors-"+ele.id).remove();
 if(startdate !='')
    { 
        $.ajax({
				url: base_url+"/organisationinfo/validateorgstartdate",   
				type : 'POST',	
				data : 'startdate='+startdate+'&con='+con+'&bunitid='+bunitid,
				dataType: 'json',
				beforeSend: function () {
				if(con == 'organisationinfo')
				  $("#org_startdate").before("<div id='loader'></div>");
				else 
				  $("#start_date").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				           $("#loader").remove(); 
				           if(response['result'] =='error' && response['result'] !='')
						    {
							  if(con == 'organisationinfo')
							  {
								$("#org_startdate").parent().append('<span id="errors-org_startdate" class="errors">Organization start date must be less than Business unit and Department start date.</span>' );
								$("#org_startdate").val('');
							  }
							  else if(con == 'businessunit')
                              {
							    if(bunitid !='')
							     errormsg = "Business unit start date must be less than Department start date and greater than Organization start date.";
								else
                                 errormsg = "Business unit start date must be greater than Organization start date.";								
							    $("#start_date").parent().append('<span id="errors-start_date" class="errors">'+errormsg+'</span>' );
								$("#start_date").val('');
                              }
                              else if(con == 'departments')
                              {
							    if(bunitid > 0)
							     errormsg = "Department start date must be greater than Business unit and Organization start dates.";
								else
                                 errormsg = "Department start date must be greater than Organization start date.";
							    $("#start_date").parent().append('<span id="errors-start_date" class="errors">'+errormsg+'</span>' );
								$("#start_date").val('');
                              } 							  
						    }else if(response['result'] =='success'){
						    	if(con == 'organisationinfo')
								{
						    		$("#errors-org_startdate").remove();
								}else if(con == 'departments' || con == 'businessunit'){
									$("#errors-start_date").remove();
								}     
						    }
				             
                         }
						
			    });
	}			
 }
 
 function validateorgandunitstartdate(ele,con)
 {
 var startdate = $('#start_date').val();
 var bunitid;
 var errormsg;
	if(ele.selectedIndex > 0){
	 bunitid = ele[ele.selectedIndex].value;
	}else{
		bunitid = '';
	}
//alert(bunitid);

 if($(".errors").is(":visible"))
	$(".errors").remove();
 if(startdate !='' && bunitid !='')
    { 
        $.ajax({
				url: base_url+"/organisationinfo/validateorgstartdate",   
				type : 'POST',	
				data : 'startdate='+startdate+'&con='+con+'&bunitid='+bunitid,
				dataType: 'json',
				beforeSend: function () {
				$("#start_date").before("<div id='loader'></div>");
				$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response){
				           $("#loader").remove(); 
				           if(response['result'] =='error' && response['result'] !='')
						    {
							  
                              if(con == 'deptunit')
                              {
							  	errormsg = "Department start date must be greater than Business unit and Organization start dates.";
							    $("#start_date").parent().append('<span id="errors-start_date" class="errors">'+errormsg+'</span>' );
								$("#start_date").val('');
                              } 							  
						    }
				             
                         }
						
			    });
	}			
 }

function modifylist(actionName,userid,level,parent)
{	
	var buttons1;
	if(actionName == 'add')
	{		
		var level = parseInt(level) + 1;
		$.ajax({
				url: base_url+"/heirarchy/addlist/format/html",				
				type : 'POST',
				data : 'userid='+userid+'&level='+level+'&parent='+parent+'&actionName='+actionName,
				dataType: 'html',
				success : function(response){					
					if(response == 'nodata'){
						response = '<div class="newframe-div hierarchypopup"><div class="ml-alert-1-info"><div class="style-1-icon info"></div>No employees to add to the hierarchy.</div></div>';	
						buttons1 = buttons1 = [{
                                id:"dialogclose",
                                text: "Close",								
                                click: function() {
                                       $("#addlist").dialog('close');
                                }
                        }];
					}
					else
					{
						buttons1 = [{
                                id:"dialogadd",
                                text: "Add",								
                                click: function() {
                                        savehierarchylevel();
                                }
                        },{
                                id:"dialogcncl",
                                text: "Cancel",
								Class: "ui-button-cancel",
                                click: function() {
                                        $("#addlist").dialog('close');
                                }
                        }];
					}
					
					$("#addlist").html(response);
					$("#addlist").dialog({
						resizable: false,
						height:400,
						modal: true,
						title: 'Add - Hierarchy level '+level,
						//width: 780,
						closeOnEscape: false,
						/*buttons : {
							"Add" : function() {
								savehierarchylevel();
							},
							"Cancel" : function() {
								id : "asas";
								$("#addlist").dialog('close');
							}
						}*/
						buttons: buttons1
					});						
					$("#addlist").css('display','block');
					$('#editdialogbox').slimscroll({	});
				}
			});		
	}
	else if(actionName == 'remove')
	{
		jConfirm("You are trying to remove the person from the hierarchy. Please confirm", "Confirmation",function(r)
		{
			if(r==true)
			{
				var message = '';
				$.ajax({
						url: base_url+"/heirarchy/deletelist/format/json",				
						type : 'POST',
						data : 'userid='+userid+'&level='+level+'&parent='+parent,
						dataType: 'json',
						success : function(response){
							if(response['result'] == 'updated')
							{								
								/*message = "Hierarchy is updated successfully.";
								$("#error_message").html('<div id="messageData" class="ml-alert-1-success"><div style="display:block;"><span class="style-1-icon success"></span>'+message+'</div></div>');*/
								window.location = base_url+"/heirarchy/edit";
							}else
							{								
								message = "Hierarchy is not updated successfully.";
								$("#error_message").html('<div id="messageData" class="ml-alert-1-error"><div style="display:block;"><span class="style-1-icon error"></span>'+message+'</div></div>');
								
								setTimeout(function(){
									//$('#error_message').fadeOut('slow');
									$('#error_message').fadeOut('slow');
								},3000);
							}							
						}
					});
			}else{
				return false;
			}
		});
	}
	else if(actionName == 'edit')
	{
		buttons2 = '';
		$.ajax({
				url: base_url+"/heirarchy/editlist/format/html",				
				type : 'POST',
				data : 'olduserid='+userid+'&level='+level+'&parent='+parent+'&actionName='+actionName,
				dataType: 'html',
				success : function(response){					
					if(response == 'nodata'){
						response = '<div class="newframe-div hierarchypopup"><div class="ml-alert-1-info"><div class="style-1-icon info"></div>No employees to update the hierarchy.</div></div>';	
						buttons2 = [{
									id:"dialogclose",
									text: "Close",								
									click: function() {
										   $("#addlist").dialog('close');
									}
							}];
					}else{
						buttons2 = [{
									id:"dialogadd",
									text: "Update",
									click: function() {
											savehierarchylevel();
									}
							},{
									id:"dialogcncl",
									text: "Cancel",
									Class: "ui-button-cancel",
									click: function() {
											$("#addlist").dialog('close');
									}
							}]
					}
					
					
					$("#addlist").html(response);
					$("#addlist").dialog({
						resizable: false,
						height:150,
						modal: true,
						title: 'Edit - Hierarchy level '+level,
						//width:780,
						closeOnEscape: false,
						/*buttons : {
							"Update" : function() {
								savehierarchylevel();
							},
							"Cancel" : function() {
								$("#addlist").dialog('close');
							}
						}*/
						buttons: buttons2
					});							
					$("#addlist").css('display','block');
				}
		});
	}
}

function savehierarchylevel()
{
	var level = $('#levelnumberval').val();	
	var newuserid = $('#levelselect').val();
	var actionName = $('#actiontype').val();
	
	var options = $('#levelselect > option:selected');
	if(options.length == 0 && actionName == 'add'){
		$('#levelselect-error').html('Please select employee.');
		return false;
	}
	else if(newuserid == '' && actionName == 'edit')
	{
		$('#levelselect-error').html('Please select employee.');
		return false;
	} 
	else 
	{
		if(actionName == 'add')
		{
			var parent = $('#parentuserid').val();	
			$.ajax({
				url: base_url+"/heirarchy/saveadddata/format/json",				
				type : 'POST',
				data : 'newuserid='+newuserid+'&level='+level+'&parent='+parent+'&actionName='+actionName,
				dataType: 'json',
				success : function(response){					
					if(response['result'] == 'saved')
					{
						/*$('#resppdiv').css('display','block');
						$('#resppdiv').html("Level data is added successfully");
						setTimeout(function(){				
						$('#resppdiv').css('display','none');
						},1000);*/
						window.location = base_url+"/heirarchy/edit";
					}
					else
					{
						$('#levelselect-error').html('Please select employee.');
						return false;
					}
					
				}
			});	
		}
		if(actionName == 'edit')
		{
			var olduserparent = $('#parentid').val();
			var olduserid = $('#olduserid').val();
			
			$.ajax({
				url: base_url+"/heirarchy/saveeditdata/format/json",				
				type : 'POST',
				data : 'newuserid='+newuserid+'&level='+level+'&actionName='+actionName+'&olduserid='+olduserid+'&olduserparent='+olduserparent, //'&parent='+parent+
				dataType: 'json',
				success : function(response){					
					if(response['result'] == 'updated')
					{
						/*$('#resppdiv').css('display','block');
						$('#resppdiv').html("Level data is "+response['result']+" successfully");
						setTimeout(function(){				
						$('#resppdiv').css('display','none');
						},1000);*/
						window.location = base_url+"/heirarchy/edit";
					}else if(response['result'] == 'failed')
					{
						/*$('#resppdiv').css('display','block');
						$('#resppdiv').html("Sorry!, You cannot edit the level");
						setTimeout(function(){				
						$('#resppdiv').css('display','none');
						},1000); */
						window.location = base_url+"/heirarchy/edit";
					}
					else
					{
						$('#levelselect-error').html('Please select employee.');
						return false;
					}
				}
			});	
		}
	}
}


function displayHolidayDates(ele)
{
    var id;
	var Url;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	var userid = $("#userid").val();
	Url = base_url+"/empholidays/index/format/html";
	if(id)
	{
		$.ajax({
			url: Url,   
			type : 'POST',
			data : 'groupid='+id+'&call=ajaxcall'+'&userid='+userid,
			success : function(response){
                            $('#grid_empholidays').show();			
							$('#grid_empholidays').html(response);                                      
			}
		});
	}else
	{
	    $('#grid_empholidays').hide();	
	}

}



function changereportsscreen(controllername)
{
  if(controllername)
    window.location.href = base_url+'/reports/'+controllername;
}

function downloadLeavesPdf(url, data){
   $.blockUI({ width:'50px',message: $("#spinner").html() });
   //data = $(formId).serialize();
     $.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function(response) {
				response = JSON.parse(response);
				download_url = base_url + '/reports/downloadreport/file_name/' + response.file_name;
			    var $preparingFileModal = $("#preparing-file-modal");

		        //$preparingFileModal.dialog({ modal: true });

		        $.fileDownload(download_url, {
		            successCallback: function(url) {
		                //$preparingFileModal.dialog('close');
						$.unblockUI();
		            },
		            failCallback: function(responseHtml, url) {

		                /*$preparingFileModal.dialog('close');
		                $("#error-modal").dialog({ modal: true });*/
		            	$.unblockUI();
		                jAlert('Download of the report failed');
		            }
		        });
		        return false; //this is critical to stop the click event which will trigger a normal file download!
			},
	});	
}

function downloadBUsPdf(url, formId)
{	
	$.blockUI({ width:'50px',message: $("#spinner").html() });
   
   //data = $(formId).serialize();
    var data = $('#id_param_string').val();
	if(data == '')
	var data = $(formId).serialize();
     $.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function(response) {
				response = JSON.parse(response);
				download_url = base_url + '/reports/downloadreport/file_name/' + response.file_name;
			    var $preparingFileModal = $("#preparing-file-modal");

		        //$preparingFileModal.dialog({ modal: true });

		        $.fileDownload(download_url, {
		            successCallback: function(url) {
		                //$preparingFileModal.dialog('close');
						$.unblockUI();
		            },
		            failCallback: function(responseHtml, url) {

		                /*$preparingFileModal.dialog('close');
		                $("#error-modal").dialog({ modal: true });*/
		            	$.unblockUI();
		                jAlert('Download of the report failed');
		            }
		        });
		        return false; //this is critical to stop the click event which will trigger a normal file download!
			},
	});	
}


function downloadHolidaysPdf(url, data){
   $.blockUI({ width:'50px',message: $("#spinner").html() });
   //data = $(divId).find('select, textarea, input').serialize();
     $.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function(response) {
				response = JSON.parse(response);
				download_url = base_url + '/reports/downloadreport/file_name/' + response.file_name;
			    var $preparingFileModal = $("#preparing-file-modal");

		        //$preparingFileModal.dialog({ modal: true });

		        $.fileDownload(download_url, {
		            successCallback: function(url) {
		                //$preparingFileModal.dialog('close');
						$.unblockUI();
		            },
		            failCallback: function(responseHtml, url) {

		                /*$preparingFileModal.dialog('close');
		                $("#error-modal").dialog({ modal: true });*/
		            	$.unblockUI();
		                jAlert('Download of the report failed');
		            }
		        });
		        return false; //this is critical to stop the click event which will trigger a normal file download!
			},
	});	
}


function downloadLeaveManagementPdf(url, data){
   $.blockUI({ width:'50px',message: $("#spinner").html() });
   //data = $(divId).find('select, textarea, input').serialize();
     $.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function(response) {
				response = JSON.parse(response);
				download_url = base_url + '/reports/downloadreport/file_name/' + response.file_name;
			    var $preparingFileModal = $("#preparing-file-modal");

		        //$preparingFileModal.dialog({ modal: true });

		        $.fileDownload(download_url, {
		            successCallback: function(url) {
		                //$preparingFileModal.dialog('close');
						$.unblockUI();
		            },
		            failCallback: function(responseHtml, url) {

		                /*$preparingFileModal.dialog('close');
		                $("#error-modal").dialog({ modal: true });*/
		            	$.unblockUI();
		                jAlert('Download of the report failed');
		            }
		        });
		        return false; //this is critical to stop the click event which will trigger a normal file download!
			},
	});	
}

function getdeptData(id)
{	
	url = base_url+"/businessunits/getdeptnames/format/html";
	var myPos = [ $(window).width() / 5, 150 ];
	$.ajax({		
		type: "POST",
		url: url,
		data: 'bunitid='+id,
		dataType: 'html',
		success: function(response) 
		{
			$("#deptinfo").css('display','block');
			$("#deptinfo").html(response);
			$("#deptinfo").dialog({
				open : function(){					
					$('.ui-widget-overlay').addClass('ui-front-overwrite');
					$('.ui-dialog').removeClass('ui-dialog-buttons');
					$('.ui-dialog').removeClass('ui-front');
					$('.ui-dialog').addClass('ui-btn-overwrite');
				},
				title: 'Departments List',
				//height:600,
				//width: 600,
				position: myPos,
				modal: true, 
				buttons : {
					"Close" : function() {
					$(this).dialog("close");								
					}
				}
			});
		}
	});
			
}

function getempData(deptid)
{
	url = base_url+"/departments/getempnames/format/html";	
	var myPos = [ $(window).width() / 5, 150 ];
	$.ajax({		
		type: "POST",
		url: url,
		data: 'deptid='+deptid,
		dataType: 'html',
		success: function(response) 
		{
			$("#empinfo").css('display','block');
			$("#empinfo").html(response);
			$("#empinfo").dialog({
				open : function(){
					$('.ui-widget-overlay').addClass('ui-front-overwrite');
					$('.ui-dialog').removeClass('ui-dialog-buttons');
					$('.ui-dialog').removeClass('ui-front');
					$('.ui-dialog').addClass('ui-btn-overwrite');
				},
				title: 'Employees List',
				//height:600,
				//width: 600,
				position: myPos,
				modal: true, 
				buttons : {
					"Close" : function() {
					$(this).dialog("close");								
					}
				}
			});
		}
	});
	
}

function getempholidaygroup(id,groupname)
{	
	url = base_url+"/holidaygroups/getempnames/format/html";
	var myPos = [ $(window).width() / 5, 150 ];
	$.ajax({		
		type: "POST",
		url: url,
		//data: 'groupid='+id+'&groupname='+groupname,
		data: 'groupid='+id,
		dataType: 'html',
		success: function(response) 
		{
			$("#empnamesinfo").css('display','block');
			$("#empnamesinfo").html(response);
			$("#empnamesinfo").dialog({
				open : function(){					
					$('.ui-widget-overlay').addClass('ui-front-overwrite');
					$('.ui-dialog').removeClass('ui-dialog-buttons');
					$('.ui-dialog').removeClass('ui-front');
					$('.ui-dialog').addClass('ui-btn-overwrite');
				},
				title: 'Employees Name',
				//height:600,
				//width: 600,
				position: myPos,
				modal: true, 
				buttons : {
					"Close" : function() {
					$(this).dialog("close");								
					}
				}
			});
		}
	});
			
}

function getholidaynames(id)
{	
	url = base_url+"/holidaygroups/getholidaynames/format/html";
	var myPos = [ $(window).width() / 5, 150 ];
	$.ajax({		
		type: "POST",
		url: url,
		//data: 'groupid='+id+'&groupname='+groupname,
		data: 'groupid='+id,
		dataType: 'html',
		success: function(response) 
		{
			$("#holidaynamesinfo").css('display','block');
			$("#holidaynamesinfo").html(response);
			$("#holidaynamesinfo").dialog({
				open : function(){					
					$('.ui-widget-overlay').addClass('ui-front-overwrite');
					$('.ui-dialog').removeClass('ui-dialog-buttons');
					$('.ui-dialog').removeClass('ui-front');
					$('.ui-dialog').addClass('ui-btn-overwrite');
				},
				title: 'Holiday Names',
				//height:600,
				//width: 600,
				position: myPos,
				modal: true, 
				buttons : {
					"Close" : function() {
					$(this).dialog("close");								
					}
				}
			});
		}
	});
			
}

function checkissuingauthority(ele)
{
    var id;
	var Url;
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	Url = base_url+"/index/getissuingauthority/format/json";
	
	if(id)
	{
		$.ajax({
			url: Url,   
			type : 'POST',
			data : 'doctypeid='+id,
			dataType: 'json',
			success : function(response){
			    if(response['result'] !='')
				{		
			        if(response['result'] == 1)
					{
					    $("#statelabel").removeClass('required');
						$("#citylabel").removeClass('required');
						$("#issuingauthflag").val(1);
						$("#issuingauth_statediv").hide();
						$("#issuingauth_citydiv").hide();
					}
					else if(response['result'] == 2)
					{
					    $("#issuingauth_statediv").show();
						$("#issuingauth_citydiv").hide();
					    $("#statelabel").addClass('required');
						$("#citylabel").removeClass('required');
						$("#issuingauthflag").val(2);
					}else if(response['result'] == 3)
					{
					    $("#issuingauth_statediv").show();
						$("#issuingauth_citydiv").show();
					    $("#statelabel").addClass('required');
						$("#citylabel").addClass('required');
						$("#issuingauthflag").val(3);
					}
				}
                                                                 
			}
		});
	}

}

function displaydates(ele)
{
      var statusvalue = '';
	  if(ele.selectedIndex != -1){
		 statusvalue = ele[ele.selectedIndex].value;
		}else{
			statusvalue = '';
		}
		
	 if(statusvalue == 1)
       {
	      date_helper();
	   }else
	   {
	        $('#from_date,#to_date').datepicker("hide");
	        $('#from_date,#to_date').datepicker('destroy');
            $('#from_date,#to_date').val('');
			$('#errors-from_date').remove();
			$('#errors-to_date').remove();
	   }
	   
}

function changereportingmanager(empid,status,ishead)
{
	var rmanager = $('#reporting_managerId').val();	
	Url = base_url+"/employee/changereportingmanager/format/json";
	if(rmanager == '')
	{		
		$('#errors-reporting_manager').html('Please select reporting manager.');
		return false;
	}else{
	$.blockUI({ width:'50px',message: $("#spinner").html() });
		$.ajax({
				url: Url,   
				type : 'POST',
				data : 'empid='+empid+'&newrmanager='+rmanager+'&status='+status+'&ishead='+ishead,
				dataType: 'json',
				success : function(response){
					$.unblockUI();
					$('#successmessagediv').css('display','block');
					if(response['result']  ==	'success')
					{		
						$('#successmessagediv').html("<div class='ml-alert-1-success'><div class='style-1-icon success'></div>Employee is successfully made inactive.</div>");
					}else{
						$('#successmessagediv').html("<div class='ml-alert-1-error'><div class='style-1-icon error'></div>Sorry, employee cannot be made inactive.</div>");
					}
					
					setTimeout(function(){				
						closeiframepopup('employee','');
					},1000);
				}
			});
	}
}

function validateCountry(tBox) { 
    var curVal = tBox.value; 
	if(curVal !='')
	{
		var re = /^[^ ][a-z0-9 ]*$/i;
		$('#errors-othercountry').remove();
		if(!re.test(curVal))
		{
			$('#othercountry').parent().append("<span class='errors' id='errors-othercountry'>Please enter valid country name.</span>");
		}
		else
		{
			$('#errors-othercountry').remove();
		}
    }else
    {
	    $('#errors-othercountry').remove();
    }  	
}

function validate_otherdocument(ele)
    {
	    var id= $(ele).prop('id');
        $('#errors-'+id).remove();
        if($.trim($('#'+id).val()) == '')
        {
            $('#'+id).parent().append("<span class='errors' id='errors-"+id+"'>Please enter other document.</span>");
        }
        else
        {
            $('#errors-'+id).remove();
        } 
    }

function getdetailsoforghead(ele)
{
	var id;	var params = '';
	if(ele.selectedIndex != -1){
	 id = ele[ele.selectedIndex].value;
	}else{
		id = '';
	}
	
	if(id !='')
	{
		$.ajax({				
                url: base_url+"/organisationinfo/getcompleteorgdata/format/json",    				
				type : 'POST',	
				data : 'userid='+id,
				dataType: 'json',
				beforeSend: function () {
					$("#orghead").before("<div id='loader'></div>");
					$("#loader").html("<img src="+base_url+"/public/media/images/loaderwhite_21X21.gif>");
				},
				success : function(response)
				{
					$("#loader").remove();
					
					var result = response['result'];
					var positionsArr = response['positionsdata'];
					var defOption = "<option value=''>Select Position</option>";		
					$('#s2id_position_id .select2-choice span').html('Select Position');
					$("#position_id").find('option').remove();
					$("#position_id").parent().find('.select2-container').find('.select2-search-choice').remove();
					$("#position_id").html(defOption+positionsArr);
					
					$('#rmdiv').css('display','block');
					$('#rmflag').val('1');					
					
					var oldRM  = $("#user_id").val();					
					if(oldRM == id)
					{
						$('#rmdiv').css('display','none');
						$('#rmflag').val('0');					
					}
						
					$('#jobtitle_id').val(result['jobtitle_id']);
					var jobtitle_idText = $("#jobtitle_id option[value='"+result['jobtitle_id']+"']").text()
					$('#s2id_jobtitle_id').find('a.select2-choice').find('span').html(jobtitle_idText);					
					$('#employeeId').val(result['employeeId']);
					
					$('#prefix_id').val(result['prefix_id']);
					var prefixText = $("#prefix_id option[value='"+result['prefix_id']+"']").text()
					$('#s2id_prefix_id').find('a.select2-choice').find('span').html(prefixText);
					
					$('#emprole').val(result['emprole']);
					var emproleText = $("#emprole option[value='"+result['emprole']+"']").text()
					$('#s2id_emprole').find('a.select2-choice').find('span').html(emproleText);
					
					$('#emailaddress').val(result['emailaddress']);
					$('#date_of_joining').val(result['date_of_joining']);
					$('#position_id').val(result['position_id']);
					var position_idText = $("#position_id option[value='"+result['position_id']+"']").text();
					$('#s2id_position_id').find('a.select2-choice').find('span').html(position_idText);
					
				}
			});
	}
	else
	{
		
	}
	
}
   






