/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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
function redirecttocontroller(controllername)
{
	$.blockUI({ width:'50px',message: $("#spinner").html() });
	window.location.href = base_url+'/exit/'+controllername;	
}
function getAjaxgridData(objname,dashboardcall)
{		
 		var perpage = $("#perpage_"+objname).val();
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
		var url ='';

		url = base_url+"/exit/"+objname+"/index/format/html" ;

		$.ajax({
			url: url,   
			type : 'POST',
			data : dataparam,
			success : function(response){
				$('#grid_'+objname).html(response);
			}
		});

}
 function successmessage_changestatus(message,flag,controllername)
	{	
		var eleId = 'error_message_'+controllername;
		$("#error_message").css('display','block');
		if($("#"+eleId).length == 0) 
		{
			$("#error_message").attr("id","error_message_"+controllername);
			$("#error_message_"+controllername).css('display','block');
		}
		else
		{
			$("#error_message_"+controllername).css('display','block');
		}
		$("#error_message_"+controllername).html('<div id="messageData" class="ml-alert-1-'+flag+'"><div style="display:block;"><span class="style-1-icon '+flag+'"></span>'+message+'</div></div>'); 
       	setTimeout(function(){
			$('#error_message_'+controllername).fadeOut('slow');
		},3000);
	}


function changestatus(controllername,objid,flag)
{
	
	var deleteflag = $("#viewval").val();
	var flagAr = flag.split("@#$"); 
	var i;
	var msgdta = ' ';
	for(i=0;i<flagAr.length;i++)
	{
		msgdta += flagAr[i]+' ';
	}	
	
	mdgdta = $.trim(msgdta);
	var messageAlert = 'Are you sure you want to delete the selected '+mdgdta+'? ';
	
	 jConfirm(messageAlert, "Delete "+msgdta, function(r) {

        if(r==true)
        {               
			if(controllername == 'exittypes' || controllername == 'configureexitqs' || controllername == 'exitprocsettings')
			{
                $.ajax({
                    url: base_url+"/exit/"+controllername+"/delete",   
                    type : 'POST',
                    data: 'objid='+objid+'&deleteflag='+deleteflag,
                    beforeSend: function () {
                        $.blockUI({ width:'50px',message: $("#spinner").html() });
                    },
                    dataType: 'json',
                    success : function(response)
                    {	
                        successmessage_changestatus(response['message'],response['msgtype'],controllername);

							if(deleteflag==1)
							{
								redirecttocontroller(controllername);
							}
							else
							{
								getAjaxgridData(controllername);	
							}							
						
                    }
                });
			}
         
        }
           else 
           {

           }
        });
		 
}	
function changeeditscreen(controllername,id)
{

	  $.blockUI({ width:'50px',message: $("#spinner").html() });	
	  window.location.href = base_url+'/exit/'+controllername+'/edit/id/'+id;
}  

function refreshgrid(objname,dashboardcall,catId)
{
	
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
	var url = document.URL.split('/');  
	
	var dataparam = 'objname='+objname+'&refresh=refresh&call=ajaxcall'+'&'+mname+'='+mnuid+"&context="+context+"&dashboardcall="+dashboardcall;
        
       
	if(catId != '')
		Url = base_url+"/"+objname+"/id/"+catId;
	else
		Url = base_url+"/exit/"+objname+"/index/format/html";

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
function opensearch(objname)
{
	var dashboardcall = $("#dashboardcall").val();
	if($(".searchtxtbox_"+objname).is(":visible"))
	{
            $('.ui-datepicker-trigger').hide();
            $(".searchtxtbox_"+objname).hide();	
            $("#search_tr_"+objname).hide();	
            refreshgrid(objname,dashboardcall,"");		
	}
	else 
        {           
            $('.ui-datepicker-trigger').show();
            $(".searchtxtbox_"+objname).show();					
            $("#search_tr_"+objname).show();	
        }
}	
function getsearchdata(objname,conText,colname,event,etype)
{
    if(etype == 'text')
    {
        var code = event.keyCode || event.which;
        if(code != 13) 
        { 
            return ;
        }
    }
    var dashboardcall = $("#dashboardcall").val();
	var Url ="";
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
	var unitId = '';var mname='';var mnuid='';var columnid = '';var flag='';
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
	page = 1; 
	
	var url = document.URL.split('/');  
	
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

	Url = base_url+"/exit/"+objname+"/index/format/html";
	
	$.ajax({
		url: Url,   
		type : 'POST',
		data : dataparam,
		success : function(response){	
			$('#grid_'+objname).html(response);                                      
		},
		
	});
       
}	

function createorremoveshortcut(menuid,shortcutflag)
{
    var actionvar = '';
    var html = '';
    if(shortcutflag == 1)
        
        actionvar = 'pin to shortcuts';
    else if(shortcutflag == 2)
        
        actionvar = 'unpin from shortcuts';
    else if(shortcutflag == 3)
        
        actionvar = 'pin to shortcuts';
  
    if($("#errors-pageshortcut").is(":visible"))  
    $("#errors-pageshortcut").hide();  
    $.ajax({
        url: base_url+"/index/createorremoveshortcut",   
        type : 'POST',	
        data : 'menuid='+menuid+'&shortcutflag='+shortcutflag,
        dataType: 'json',
        beforeSend: function () {
            $("#pageshortcut").before("<div id='loader-shortcut'></div>");
            $("#loader-shortcut").html("<img src=" + domain_data + "public/media/images/loaderwhite_21X21.gif>");
        },
        success : function(response){
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
                            }
                            else
                            {
                                return false;
                            }
                        });
                    }
                    else
                    {									 
                        if(shortcutflag == 1 || shortcutflag == 3)
                        {
                            
                            html ="<div id='pageshortcut' class ='sprite remove-shortcut-icon' onclick='createorremoveshortcut("+menuid+",2)'>Unpin from shortcuts";
                            html +="</div>";
                        }
                        else if(shortcutflag == 2)
                        {
                            
                            html ="<div id='pageshortcut' class ='sprite shortcut-icon' onclick='createorremoveshortcut("+menuid+",1)'>Pin to shortcuts";
                            html +="</div>"; 
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
 }
function redirecttolink(link,module)
{    
 $.blockUI({ width:'50px',message: $("#spinner").html() });
 if(module!='')
	 window.location = base_url+'/'+module+'/'+link;
 else
	 window.location = base_url+'/'+link;

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
function displayexitform(url,menuname)
{
	$.ajax({
		type:"post",		
		url:base_url+'/index/checkisactivestatus',
		dataType:'json',
		success: function(response)
		{  
			if(typeof (response['result']) == 'undefined' || response['result'] == 'false' || response['result'] == '')
			 {
				window.location.href = base_url+'/index';
			 }
			else if(response['result'] == 'true')
			{
				var urlArr = url.split('/'); 
				var exiturl =  base_url+'/exit';
				var baseurlArr = exiturl.split('/');
				var flag = 'yes';
				var urlsplitArr = url.split("/");
				var controllername = urlArr[baseurlArr.length];
					if(flag == 'yes'){
						$("body").append('<div id="blockingdiv" class="ui-widget-overlay ui-front"></div>');
						
						var capitalizedtitle = '';
						if(menuname !='')
						{
						  capitalizedtitle = menuname;
						}else
						{
						capitalizedtitle = controllername.substr(0, 1).toUpperCase() + controllername.substr(1);
									
						}
						if(capitalizedtitle=='Allexitproc')
							capitalizedtitle = 'Update Overall Status';
						$(".closeAttachPopup").remove();
							window.parent.$('#'+controllername+'Container').dialog({
																	open:function(){
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
						$('#processesCont').contents().find('input[name="bg-check-details-id"]').val(urlsplitArr[5]);
					}
			}else
			{
				window.location.href = base_url+'/index';
			}
		},
		global: false
	});
	
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

 function addAppQuestionDiv()
{
	var divcount = $("#multi_dept_div > div").length;
	var idcount = parseInt($('#idcount').val());
	if(divcount < 5)
	{	
		idcount = idcount + 1;
		var html = '';
		html+="<div id='parent_"+idcount+"' class='add_request'>";
		html+="<div class='new-form-ui clearb'>";
		html+="<label class='required'>Question <img class='tooltip' title='Special characters allowed are - ? &#39; . , / # @ $ & * ( ) !' src='" + domain_data + "public/media/images/help.png'></label>";
		html+="<div class='division'><input type='text' maxlength='100' value='' id='question_"+idcount+"' name='question[]' class='cls_service_request_name' onblur='validatequestionname(this)' onkeyup='validatequestionname(this)'></div>";
		html+="</div>";
		html+="<div class='new-form-ui clearb textareaheight'>";
		html+="<label>Description</label>";
		html+="<div class='division'><textarea maxlength='200' cols='50' rows='10' id='description_"+idcount+"' name='description[]'></textarea></div>";
		html+="</div>";
		html+="<div class='division'><span class='sprite remove-new remove-entry' title='Remove' onclick='removeDiv("+idcount+")'></span></div>";
		html+="</div>";
	
		$('#multi_dept_div').append(html);
		$('#idcount').val(idcount);
		$("[id^=description]").maxlength();
	}else
	{
		jAlert('You can add only 5 questions at a time.');
	}
	
}
function removeDiv(idcount)
{
	$('#parent_'+idcount).remove();
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


function displaydeptform(url,menuname)
{
	$.ajax({
		type:"post",		
		url:base_url+'/index/checkisactivestatus',
		dataType:'json',
		success: function(response)
		{  
			if(typeof (response['result']) == 'undefined' || response['result'] == 'false' || response['result'] == '')
			 {
				window.location.href = base_url+'/index';
			 }
			else if(response['result'] == 'true')
			{

				var urlArr = url.split('/');   
				var baseurlArr = base_url.split('/');
				var request_hostname = window.location.hostname;
				var job_title = '';var country_id = ''; var country = '';var state_id = ''; var state = '';
				var flag = 'yes';
				var urlsplitArr = url.split("/");
				var controllername = urlArr[baseurlArr.length];
				
				if(menuname=='Exit Type')
				{
					 var controllername = 'exittypes';
				}
				if(menuname=='Exit Questions')
				{
					 var controllername = 'configureexitqs';
					 url =url+'/isfrompopup/yes';		
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
									
						}			
						$(".closeAttachPopup").remove();
							window.parent.$('#'+controllername+'Container').dialog({
																	open:function(){
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
						$('#processesCont').contents().find('input[name="bg-check-details-id"]').val(urlsplitArr[5]);
					}
			}else
			{
				window.location.href = base_url+'/index';
			}
		},
		global: false
	});
	
}

function closeiframeAddPopup(addpopupdata,controllername,con,textstr,newId)
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
	
	/** to set the new category as selected value - start **/
	if(newId != '' && con == 'category_id')
	{
		window.parent.$('#'+con).select2('val',newId);
	}
	if(newId != '' && con == 'rccandidatename')
	{
		window.parent.$('#'+con).select2('val',newId);
	}
	 
	/** to set the new category as selected value - end **/

	if($('#'+controllername+'Container', window.parent.document).html() !='null')
	{
		setTimeout(function(){
		window.parent.$('#'+controllername+'Container').dialog('close');
		window.parent.$('#errors-'+con).remove();
		},2000);
	}
}
function closeiframepopup(controllername,con)
{	
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
}
function paginationndsorting(url){
			var myarr = url.split("/");
			if(url.indexOf('/call/ajaxcall') == -1)                        
				url = url+'/call/ajaxcall';
			var dashboardcall = $("#dashboardcall").val();
			if(url.indexOf("objname") != -1)                        
			{
				divid = url.match(/objname\/(.*?)\//i)[1];
			}	
			
			if(url.indexOf("sort") != -1) 
			{
				var strSortParam = url.substring(url.lastIndexOf('sort')+5);
				
				var sortOrder = strSortParam.substring(0,strSortParam.lastIndexOf('by')-1);
				
				var sortBy = strSortParam.substring(strSortParam.lastIndexOf('by')+3);
				$('#sort_param').val(sortBy+"/"+sortOrder);
			}
			
			var browserurl = document.URL.split('/');  
			var flag='';
			if($.inArray("pendingleaves",browserurl) != -1){
				flag = getlastarrayelement(browserurl);
				divid='pendingleaves';
			}
			
			var searchData = $("#"+divid+"_searchdata").val();
			var perfTimes = $("#gridblock *").serialize();
			searchData = decodeURIComponent(searchData);
			
			$.post(url,{searchData:searchData,dashboardcall:dashboardcall,flag:flag} , function(response) {		
				$('#grid_'+divid).html(response);
		},'html');
}

