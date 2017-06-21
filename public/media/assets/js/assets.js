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
function refreshgrid(objname, dashboardcall,projectId,otherAction,start_date,end_date,emp_id) {
	var Url = "";
	var context = "";
	var formGridId = $("#formGridId").val();
	var unitId = '';
	var module_name = '/assets';
	mname = '';
	mnuid = '';
	$('#columnId').remove();
	if (formGridId == '' || formGridId == 'undefined' || typeof(formGridId) === 'undefined')
			formGridId = '';
	else 
		{
			unitId = formGridId.split("/");
			mname = unitId[0];
			mnuid = unitId[1];
		} 
	//var url = document.URL.split('/');
	var dataparam = 'objname=' + objname + '&refresh=refresh&call=ajaxcall' + '&' + mname + '=' + mnuid + "&context=" + context + "&dashboardcall=" + dashboardcall;

	/* if(projectId != '')
		dataparam = dataparam+ "&projectId=" + projectId;
	
	if(otherAction != '' && otherAction == "employeereports")
		dataparam = dataparam +'&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && otherAction == "projectreports")
		dataparam = dataparam + '&emp_id=' + emp_id + '&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && (otherAction == "viewexpensereports" || otherAction == "expensereports"))
		dataparam = dataparam +'&start_date=' + start_date + '&end_date=' + end_date; */
	
	Url = base_url + module_name + "/" + objname + "/"+otherAction+"/format/html";
	$("#" + objname + "_searchdata").val(''); 
	
	$.ajax({
		
		url: Url,
		type: 'POST',
		data: dataparam,
		success: function(response) {
			//alert(response);
			
			$('#grid_' + objname).html(response);
			/* if(otherAction == "expensereports"){
				expenseReportsSuccess();
			} */
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

function getsearchdata(objname, conText, colname, event, etype,projectId,otherAction,start_date,end_date,emp_id) 
	{ 
	//alert(otherAction);
	// alert(objname,conText,colname,event,etype);
	var module_name = '/assets';
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
	

	$.ajax({
		url: Url,
		type: 'POST',
		data: dataparam,
		success: function(response) {
			$('#grid_' + objname).html(response); 
		}
	});

}


function changestatus(controllername, objid, flag,allocated_id) {

	var deleteflag = $("#viewval").val();
	var flagAr = flag.split("@#$");
	var i;
	var msgdta = ' ';
	var module_name = '/assets';

	for (i = 0; i < flagAr.length; i++) 
		{
			msgdta += flagAr[i] + ' ';
		}
	mdgdta = $.trim(msgdta);
	var messageAlert = 'Are you sure you want to delete this ' + mdgdta + '? ';
	if(mdgdta == 'asset' || mdgdta =='asset category')
	{
		if(allocated_id != 0 && allocated_id != '' && mdgdta == 'asset' ){
			//window.confirm('This Asset is in use')
			jAlert('This '+mdgdta+' is in use','Asset');
			return false;
		}
		
	jConfirm(messageAlert, "Delete " + msgdta, function(r) {
		
		if (r == true) {
			
			$.ajax({
				url: base_url + module_name + "/" + controllername + "/delete",	
				type: 'POST',
				data: 'objid='+objid+'&deleteflag='+deleteflag,
				dataType: 'json',

				success: function(response) 
				{	
					successmessage_changestatus(response['message'], response['msgtype'], controllername);
					
					if($.trim(response['flagtype']) == 'sd_request')
						window.location = base_url + module_name + "/" + controllername;
					else{
						if(deleteflag==1)
						{
							redirecttocontroller(controllername);

						}
						else
						{
							getAjaxgridData(controllername,'','','index');
						}
					}
				}
			});

		} 
		else {

		}
	});
	}

}

function getAjaxgridData(objname, dashboardcall,projectId,otherAction,start_date,end_date,emp_id) {
	var perpage = $("#perpage_" + objname).val();
	var page = $(".gotopage_input_" + objname).val();
	var sort = $("#sortval_" + objname).val();
	var by = $("#byval_" + objname).val();
	var searchData = $("#" + objname + "_searchdata").val();
	searchData = decodeURIComponent(searchData);
	var formGridId = $("#formGridId").val();	
	var unitId = '';
	var module_name = '/assets'; 
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
	
	/* if(projectId != '')
	dataparam = dataparam + '&projectId=' + projectId;
	
	if(otherAction != '' && otherAction == "employeereports")
		dataparam = dataparam + '&start_date=' + start_date + '&end_date=' + end_date;
	
	if(otherAction != '' && otherAction == "projectreports")
		dataparam = dataparam + '&emp_id=' + emp_id + '&start_date=' + start_date + '&end_date=' + end_date; */
	
	
	if (searchData != '' && searchData != 'undefined')
		dataparam = dataparam + '&searchData=' + searchData;

	$('#' + objname + '_searchdata').remove();
	$('#footer').append("<input type='hidden' value='" + searchData + "' id='" + objname + "_searchdata' />");
	$('#footer').append('<input type="hidden" value="' + objname + '" id="objectName" />');
	/* if(otherAction == "projectsreports" || otherAction == "employeereports")
	{
		url =  url +"/format/html";
	} */
	
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
	var myarr = url.split("/");
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
	var module_name = '/assets';
	  $.blockUI({ width:'50px',message: $("#spinner").html() });	
	  window.location.href = base_url + module_name +'/'+controllername+'/edit/id/'+id;
}
function deletereceipt(receiptId)
{
	var recipt_ids=[];
	if(receiptId==0)
	{
		$('input[type=checkbox]').each(function () {
			if(this.checked==true)
			{
				 recipt_ids.push(this.value);
			}
		});
	}else
	{
		 recipt_ids.push(receiptId);
	}

	Url = base_url + "/expenses/receipts/deletereceipt/format/html";
	$.ajax({
		url: Url,
		type: 'POST',
		data: '&receipt_ids=' + recipt_ids,
		success: function(response) {
			$('#receiptslist').html(response);
			$('.dropdown-button').dropdown();
		}
	});
}
function downloadimage(image_name)
{  
	var image_names=[];
	if(image_name=='')
	{	
		$('input[type=checkbox]').each(function () {
			if(this.checked==true)
			{
				
				image_names.push($(this).attr('image'));
			}
		});
	}else
	{   
		image_names.push(image_name);
	}
	if(image_names=='')
	{
		jAlert('Select Atleast One Image To Download');
	}else
	{  
		$.fileDownload((base_url + "/assets/assets/downloadimage/format/html"), {
//preparingMessageHtml: "We are preparing your report, please wait...",
failMessageHtml: "There was a problem generating your image, please try again.",
httpMethod: "POST",
data: 'image_names=' + image_names
});	

}
	/*//Url = base_url + "/expenses/receipts/downloadreceipt/format/html";
	  Url = base_url + "/assets/assets/downloadimage";
	$.ajax({
		url: Url,
		type: 'POST',
		data: '&image_names=' + image_names,
		success: function(response) {
			//return false;
			alert('test');
			
		}
	});*/
	
}
function geReceipts(param)
{
	Url = base_url + "/expenses/receipts/displayreceipts/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&param=' + param,
			success: function(response) {
				$('#receiptslist').html(response);
				$('.dropdown-button').dropdown();
			}
		});
}

function displaydeptform(url,menuname)
{
	if(url.indexOf("assetcategories") > -1 ){
	    	if(url.indexOf("assetuserlog")>-1){
			var user_id = document.getElementById("allocated_to").value;
			var urlArr = url.split('/'); 
			url=url+user_id;
			}else{
		var cat_id = document.getElementById("category").value;
		var urlArr = url.split('/');  
		url = url+cat_id;
		
		}
	    }else if(url.indexOf("assets") > -1)
		{
			var vendor_id = document.getElementById("vendor").value;
			var urlArr = url.split('/');  
			url = url+vendor_id;
		}
	if(menuname == 'Assets'){
	   var tmUrl = base_url+'/assets';
	   	}
	else{
	   var tmUrl = base_url+'/default';
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

	//window.parent.$("#"+con).find('option').remove();
	//window.parent.$("#"+con).parent().find('.select2-container').find('.select2-search-choice').remove();
	window.parent.$("#"+con).html(defOption+addpopupdata);
	if($('#'+controllername+'Container', window.parent.document).html() !='null')
	{
		window.parent.$('#'+controllername+'Container').dialog('close');
		//window.parent.$('#errors-'+con).remove();
	}
}
function getSubCategories()
{
	
	var cnval=$('#category').val();
	if(cnval=="")
	{
		$('#sub_category').find('option').remove();
		$('#s2id_sub_category').find('a.select2-choice').find('span').html('Select Sub Category');
	}
	else{
		$.get(base_url+'/assets/assets/getsubcategories/cnval/'+cnval,function(data)
		{ 
          
		$('#sub_category').find('option').remove();
		$('#s2id_sub_category').find('a.select2-choice').find('span').html('Select Sub Category'); 
		$('#sub_category').append(data.options);
		$('#sub_category').trigger("liszt:updated");
		},'json');
		}

}
function getName()
{
	var allocateval=$('#allocated_to').val();
	if(allocateval>0 ){
		$('#assetlog').show();
	}else {
		$('#assetlog').hide();
	}
	
}
function getIsWorkingStatus()
{
	if (document.getElementById('is_working-Yes').checked) {
		//if($('#errors-is_working').checked ==true)
 		 	$('#errors-is_working').remove();
		  $('.test').show();
		 
		}
	if (document.getElementById('is_working-No').checked) {
		//if($('#errors-is_working').checked ==true)
 		  $('#errors-is_working').remove();
		  $('.test').hide();
		  var allocateval=$('#allocated_to').val('');
		
		}

}
function getWarrantyStatus()
{
	if (document.getElementById('warenty_status-Yes').checked) {
		//if($('#errors-warenty_status').checked ==true)
 		 	$('#errors-warenty_status').remove();
		  $('.warentytest').show();
		}
	if (document.getElementById('warenty_status-No').checked) {
		//if($('#errors-warenty_status').checked ==true)
 		 	$('#errors-warenty_status').remove();
		  $('.warentytest').hide();
		  var warenty_end_date = $('#warenty_end_date').val('');
		  
		}
}
function checkvalidity()
{
	var subcat = $('#subcatfield').val();
	//alert(subcat);
		if(subcat){
		$('.subcatename').css('display','none');
		
	}
	}
function displayempnames(bunitid){
	
	//var bunitid = $("#location").val();
if(bunitid==""){
	$('#allocated_to').find('option').remove();
	 $('#s2id_allocated_to').find('a.select2-choice').find('span').html('Select Allocate To'); 
	 $('#assetlog').hide();

}else{
	$('#allocated_to').find('option').remove();
	 $('#s2id_allocated_to').find('a.select2-choice').find('span').html('Select Allocate To'); 
	 $('#assetlog').hide();
	$.ajax({
        url: base_url+"/assets/assets/getemployeesdata/format/html",				
		type : 'POST',	
		data: 'bunitid=' + bunitid,
		success: function(response) {
			$('#allocated_to').html(response);
			}
		
	});
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
function redirecttocontroller(controllername)
{
	var module_name = '/assets';
	$.blockUI({ width:'50px',message: $("#spinner").html() });
  window.location.href = base_url+ module_name +'/'+controllername;	
}
