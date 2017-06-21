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
	var module_name = '/expenses';
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
	//alert(Url);
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

function getsearchdata(objname, conText, colname, event, etype,projectId,otherAction,start_date,end_date,emp_id) { //alert(otherAction);
	// alert(objname,conText,colname,event,etype);
	var module_name = '/expenses';
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

function changestatus(controllername, objid, flag) {
	var flagAr = flag.split("@#$");
	var i;
	var msgdta = ' ';
	var module_name = '/expenses';
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
				data: 'objid=' + objid,
				beforeSend: function() {
					// $.blockUI({ width:'50px',message:
					// $("#spinner").html() });
				},
				dataType: 'json',
				success: function(response) { 
					successmessage_changestatus(response['message'], response['msgtype'], controllername);
					if ($.trim(response['flagtype']) == 'sd_request')
						window.location = base_url + module_name + "/" + controllername;
					else
						getAjaxgridData(controllername,'','','index');
				}
			});
		} else {

		}
	});

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
	var module_name = '/expenses'; 
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
	var module_name = '/expenses';
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
function downloadreceipt(receipt_name)
{
	var recipt_names=[];
	if(receipt_name=='')
	{	
		$('input[type=checkbox]').each(function () {
			if(this.checked==true)
			{
				 recipt_names.push($(this).attr('receiptname'));
			}
		});
	}else
	{
		recipt_names.push(receipt_name);
	}

	Url = base_url + "/expenses/receipts/downloadreceipt/format/html";
	$.ajax({
		url: Url,
		type: 'POST',
		data: '&recipt_names=' + recipt_names,
		success: function(response) {
			//return false;
			alert('test');
			
		}
	});
	
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