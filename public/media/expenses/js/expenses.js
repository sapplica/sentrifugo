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
	var deleteflag = $("#viewval").val();
	var flagAr = flag.split("@#$");
	var i;
	var msgdta = ' ';
	var module_name = '/expenses';
	for (i = 0; i < flagAr.length; i++) {
		msgdta += flagAr[i] + ' ';
	}

	mdgdta = $.trim(msgdta);
	if(mdgdta=='paymentmode')
		mdgdta = 'payment mode';
		msgdta = 'Payment mode';
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
	var module_name = '/expenses';
	$.blockUI({ width:'50px',message: $("#spinner").html() });
  window.location.href =base_url + module_name + "/" + controllername;
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

function getProjects()
{
	
	  var emp_id=$('#to_id').val();
	  
	 if(emp_id=="")	
{
	    $('#project_id').find('option').remove();
$('#s2id_project_id').find('a.select2-choice').find('span').html('Select Project');
}
	  $.get(base_url+'/expenses/advances/getprojects/emp_id/'+emp_id,function(data){
      $('#project_id').find('option').remove();
      $('#project_id_text').val('');
	  $('#s2id_project_id').find('a.select2-choice').find('span').html('Select Project');
      $('#project_id').append(data.options);
      $('#project_id').trigger("liszt:updated");
  },'json');
  
  
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
	
	var mdgdta = 'receipt';
	var messageAlert = 'Are you sure you want to delete the selected ' + mdgdta + '? ';
	jConfirm(messageAlert, "Delete " + mdgdta, function(r) {
		if (r == true) {
			Url = base_url + "/expenses/receipts/deletereceipt/format/html";
			$.ajax({
				url: Url,
				type: 'POST',
				data: '&receipt_ids=' + recipt_ids,
				success: function(response) {
					$('#receiptslist').html(response);
					 var allreceiptcount = $('.allcnt').length;
					var unreceiptcount = $('.unreportedcnt').length;
					$('#allReceiptsCount').text('('+allreceiptcount+')');
					$('#unreportedReceiptCount').text('('+unreceiptcount+')');
					$('.dropdown-button').dropdown();
				}
			});
		}else
		{
			
		}
	});
}
function downloadreceipt(receipt_name)
{
	var recipt_names=[];
	if(receipt_name==''){	
		$('input[type=checkbox]').each(function () {
			if(this.checked==true){
				 recipt_names.push($(this).attr('receiptname'));
			}
		});
	}else{
		recipt_names.push(receipt_name);
	}
	//alert(recipt_names);
	if(recipt_names=='')
	{
		jAlert('Select Atleast One Receipt To Download');
	}else
	{
		$.fileDownload((base_url + "/expenses/receipts/downloadreceipt/format/html"), {
		//preparingMessageHtml: "We are preparing your report, please wait...",
		failMessageHtml: "There was a problem generating your report, please try again.",
		httpMethod: "POST",
		data: 'recipt_names=' + recipt_names
		});	
		
	}
	
}
function geReceipts(param)
{
	if(param == 'all')
	{
		$('.allreceipt').addClass('gbtn');
		$('.unreported').removeClass('gbtn');
		$('.unreported').addClass('wbtn');
		
	}else
	{
		$('.unreported').addClass('gbtn');
		$('.allreceipt').removeClass('gbtn');
		$('.allreceipt').addClass('wbtn');
	}
	
	
	$('.parameter').val(param);
	clearSearchData();
	//searchData();
	$('#searchstr').val('');
	$('#start_date').val($('#hid_start_date').val());
	$('#end_date').val($('#hid_end_date').val());
	$('#idclear_view_task').hide();
	Url = base_url + "/expenses/receipts/displayreceipts/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&param=' + param,
			success: function(response) {
				$('#receiptslist').html(response);
				$('.parameter').val(param);
				
				$('.dropdown-button').dropdown();
			}
		});
}
function addExpenseToReceipt(receipt_id,expense_id)
{
	var myPos = [ $(window).width() / 5, 150 ];
    $("#idAddExpense").dialog({
        title:'Add To Expense',
		position: myPos,
        modal: true, 
		buttons : [
			
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
				url: base_url + "/expenses/receipts/listexpenses/format/html",
				data: 'receipt_id=' + receipt_id+'&expense_id='+expense_id,
				dataType: 'html',
				success: function(response) {
					  $('#idAddExpenseContent').html(response);
				}
			});
	
      }
    });
}
//create expense form
function addnewexpense(urlstr,receiptId)
{
	var myPos = [ $(window).width() / 5, 150 ];
    $("#idAddNewExpense").dialog({
        title:'Expense',
		position: myPos,
        modal: true, 
		buttons : [
			
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
				url: urlstr,
				data: 'receipt_id=' + receiptId,
				dataType: 'html',
				success: function(response) {
					  $('#idAddNewExpenseContent').html(response);
				}
			});
	
      }
    });
}
function addReceipt(receipt_id,expense_id)
{
	//alert($('.parameter').val());
	 $.ajax({
				url: base_url + "/expenses/receipts/addreceipttoexpense/format/html",
				data: 'receipt_id=' + receipt_id+'&expense_id='+expense_id,
				//dataType: 'html',
				dataType: 'json',
			success: function(response) { 
				  $('#idAddExpense').dialog("destroy");
				  $('#receipt_added').show();
				  $('#receipt_add_msg').empty();
				  $('#receipt_add_msg').append("<span class='style-1-icon success'></span>Receipt Added To Expense Successfully.");
				  setTimeout(function(){
					$('#receipt_added').hide();
					window.location = window.location;
				  },3000);
				  //geReceipts($('.parameter').val());
				 
			}
			});
}
function removeFromExpense(receipt_id)
{
	var expense_id='';
	var mdgdta = 'expense';
	var messageAlert = 'Are you sure you want to remove this receipt from expense ? ';
	jConfirm(messageAlert, "Delete " + mdgdta, function(r) {
		if (r == true) {
			$.ajax({
					url: base_url + "/expenses/receipts/addreceipttoexpense/format/html",
					data: 'receipt_id=' + receipt_id+'&expense_id='+expense_id,
					//dataType: 'html',
					dataType: 'json',
				success: function(response) {
					 
					  $('#receipt_added').show();
					  $('#receipt_add_msg').empty();
					  $('#receipt_add_msg').append("<span class='style-1-icon success'></span> Expense Removed Successfully.");
					  setTimeout(function(){
						$('#receipt_added').hide();
						 window.location = window.location;
					  },3000);
					//  geReceipts($('.parameter').val());
					 
					 
				}
				});
		}else
		{
			
		}
	});
}
function search_employee(event,id)
{
	$('#idclear_view_task').hide();
	
	if($.trim($('#searchstr').val()).length>0)
	{
		$('#idclear_view_task').show();
	}
	else
	{
		var param = $('.parameter').val();
		$('#idclear_view_task').hide();
		var search = encodeURIComponent($('#'+id).val());
		Url = base_url + "/expenses/receipts/cleardata/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&param=' + param+'&searchstr='+search,
			success: function(response) {
				$('#receiptslists').html(response);	
				if(param=='all')
					$('#allReceiptsCount').text('('+$('#alldatacount').val()+')');
				else
					$('#unreportedReceiptCount').text('('+$('#alldatacount').val()+')');
				$('.dropdown-button').dropdown();
			}
		});
	}
	if (event.keyCode == 13) {
		var search = encodeURIComponent($('#'+id).val());
		var param = $('.parameter').val();
		Url = base_url + "/expenses/receipts/displayreceipts/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&param=' + param+'&searchstr='+search,
			success: function(response) {
				$('#receiptslist').html(response);	
				if(param=='all')
					$('#allReceiptsCount').text('('+$('#allsearchdatacount').val()+')');
				else
					$('#unreportedReceiptCount').text('('+$('#allsearchdatacount').val()+')');
				$('.dropdown-button').dropdown();
			}
		});
	}
}
function searchedData()
{
	var id = 'searchstr';
	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	var searchh = encodeURIComponent($('#'+id).val());
		var param = $('.parameter').val();
		Url = base_url + "/expenses/receipts/displayreceipts/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&param=' + param+'&searchstr='+searchh+'&start_date='+start_date+'&end_date='+end_date,
			success: function(response) {
				$('#receiptslist').html(response);	
				
					if(param=='all')
						$('#allReceiptsCount').text('('+$('#alldatacount').val()+')');
					else
						$('#unreportedReceiptCount').text('('+$('#alldatacount').val()+')');
					
				$('.dropdown-button').dropdown();
			}
		});
}


function search_advances(event,id)
{
	$('#idclear_view_task').hide();
	
	if($.trim($('#searchstr').val()).length>0)
	{
		$('#idclear_view_task').show();
	}
	else
	{
		//var param = $('.parameter').val();
		$('#idclear_view_task').hide();
		var search = encodeURIComponent($('#'+id).val());
		Url = base_url + "/expenses/advances/clearadvancesdata/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data:'&searchstr='+search,
			success: function(response) {
			$("#clearAdvancesData").html(response);
			} 
		});
		
		
		
	}
	if (event.keyCode == 13) {
		var search = encodeURIComponent($('#'+id).val());
	//	var param = $('.parameter').val();
		Url = base_url + "/expenses/advances/viewmoreadvances/format/html";
		$.ajax({
			url: Url,
			type: 'POST',
			data: '&searchstr='+search,
			success: function(response) {
				$(".viewmoreclass").html(response);
						
			}
		});
	}
}
function addTrip(url,menuname)
{
	if(url.indexOf("key") > -1 )
	{
		var receipts_id = $('.cls_receipt_ids').val();
		var urlArr = url.split('/');  
		url = url+'/cls_receipts_id/'+receipts_id;
	}
	var urlArr = url.split('/');   
	
	var expUrl = base_url+'/expenses';
	var baseurlArr = expUrl.split('/');
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
	window.parent.$("#"+con).find('option').remove();
	window.parent.$("#"+con).parent().find('.select2-container').find('.select2-search-choice').remove();
	window.parent.$("#"+con).html(defOption+addpopupdata);
	if($('#'+controllername+'Container', window.parent.document).html() !='null')
	{
		window.parent.$('#'+controllername+'Container').dialog('close');
		window.parent.$('#errors-'+con).remove();
	}
}
function closePopup(controllername,con)
{
	if($('#'+controllername+'Container', window.parent.document).html() !='null')
	{
		window.parent.$('#'+controllername+'Container').dialog('close');
		window.parent.$('#errors-'+con).remove();
		//geReceipts($('.parameter').val());
	}
}
function deleteUploadedReceipt(receipt_id,isFromBulk,key_val)
{
	if(isFromBulk=='yes')
	{
		var str = $('#receipts_ids_'+key_val).val();
		var cls_str = $('.cls_receipt_ids').val();
		
	}else
	{
		var str = $('.post_receipt_ids').val();
	}
	var receiptIds = "";
	var classReceiptIds = "";
	var strarray = str.split(',');
	for (var i = 0; i < strarray.length; i++) {
		
		if(strarray[i]!=receipt_id)
		{
		  receiptIds += (receiptIds=="" ? strarray[i] : "," + strarray[i]);
		}
	}
	if(isFromBulk=='yes')
	{
		$('.receipts_count_'+receipt_id).remove();
		$('#receipts_ids_'+key_val).val(receiptIds);
		
		var strarrayy = cls_str.split(',');
		for (var j = 0; j < strarrayy.length; j++) {
			
			if(strarrayy[j]!=receipt_id)
			{
			  classReceiptIds += (classReceiptIds=="" ? strarrayy[j] : "," + strarrayy[j]);
			}
		}
		
		$('.cls_receipt_ids').val(classReceiptIds);
		
		//$('.uploaadedReceipts_'+key_val).hide();
			var words = $('#receipts_ids_'+key_val).val();
			var wordsCount=0;
			if(words!='')
				wordsCount = words.split(",").length;
			$('.uploaadedReceipts_'+key_val).html(wordsCount);
			//$('#uploadbtn_'+key_val).after('<div><a id="image_count_'+key_val+'" onclick="uploadedfiles(\'file\','+key_val+');">'+wordsCount+'</a></div>');
		
		
		
	}else
	{
		$('.receipts_count_'+receipt_id).remove();
		$('.post_receipt_ids').val(receiptIds);
	}
	
}
function tripList(expenseId,tripId)
{
	var myPos = [ $(window).width() / 5, 150 ];
    $("#idTrips").dialog({
        title:'Add To Trip',
		position: myPos,
        modal: true, 
		buttons : [
			
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
				url: base_url + "/expenses/receipts/listtrips/format/html",
				data: '&expense_id='+expenseId+'&tripId='+tripId,
				dataType: 'html',
				success: function(response) {
					  $('#idTripsContent').html(response);
				}
			});
	
      }
    });
}
function addTriptoExpense(expense_id,tripId)
{
	$.ajax({
			url: base_url + "/expenses/receipts/addexpensetotrip/format/html",
			data: 'trip_id=' + tripId+'&expense_id='+expense_id,
			dataType: 'json',
			success: function(response) { 
				  $('#idTrips').dialog("destroy");
				  $('#receipt_added').show();
				  $('#receipt_add_msg').empty();
				  $('#receipt_add_msg').append("<span class='style-1-icon success'></span>Expense Added to Trip Successfully.");
				  setTimeout(function(){
					$('#receipt_added').hide();
				  },3000);
				  geReceipts($('.parameter').val());
			}
	});
}
function expenseStatus(status,expense_id,trip_id)
{
	var msg = status;
	if(status=='submitted')
		msg = 'submit';
	else if(status=='rejected')
		msg = 'reject';
	else if(status=='approved')
		msg = 'approve';
	var messageAlert = 'Are you sure you want to '+msg+' the expense  ? ';
	jConfirm(messageAlert, "Expense "+msg , function(r) {
		if (r == true) {
				$.ajax({
						url: base_url + "/expenses/expenses/expensestatus/format/html",
						data: 'status=' + status+'&expense_id='+expense_id+'&trip_id='+trip_id,
						dataType: 'json',
						success: function(response) { 
							
							$('#demo_success_msg').show();
							$('#demo_success_msg').append(' Expense '+status+' successfully.');
							setTimeout(function(){
								$('#demo_success_msg').fadeOut('slow');
								window.location =window.location;
								
								//window.location.href = base_url +'/expenses/expenses/view/id/'+expense_id;
							},3000);
							
							 
						}
				});
		}else
		{
			
		}
	});
}
function tripStatus(status,trip_id)
{
	var msg = status;
	var display_msg = '';
	if(status=='S')
	{
		msg = 'submit';
		display_msg = 'submitted';
	}
	else if(status=='R')
	{
		msg = 'reject';
		display_msg = 'rejected';
	}
	else if(status=='A')
	{
		msg = 'approve';
		display_msg = 'approved';
	}
	var messageAlert = 'Are you sure you want to '+msg+' the Trip  ? ';
	jConfirm(messageAlert, "Trip "+msg , function(r) {
		if (r == true) {
				$.ajax({
						url: base_url + "/expenses/trips/tripstatus/format/html",
						data: 'status=' + status+'&trip_id='+trip_id,
						dataType: 'json',
						success: function(response) { 
							$('#demo_success_msg').show();
							$('#demo_success_msg').append('Trip '+display_msg+' successfully.');
							setTimeout(function(){
								$('#demo_success_msg').fadeOut('slow');
								window.location = base_url +'/expenses/trips/view/id/'+trip_id;
							},3000);
							//window.location =window.location;
							 
						}
				});
		}else
		{
			
		}
	});

}


function reportingManagerList(expenseId,managerId,expenseCreatedBy)
{
	var myPos = [ $(window).width() / 5, 150 ];
    $("#idManagers").dialog({
        title:'Forward expense to',
		position: myPos,
        modal: true, 
		buttons : [
			
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
				url: base_url + "/expenses/expenses/listreportingmangers/format/html",
				data: '&expense_id='+expenseId+'&managerId='+managerId+'&expenseCreatedBy='+expenseCreatedBy,
				dataType: 'html',
				success: function(response) {
					  $('#idManagersContent').html(response);
				}
			});
	
      }
    });
}
function expenseForwardTo(expense_id,userId)
{
	$.ajax({
			url: base_url + "/expenses/expenses/forwardexpenseto/format/html",
			data: 'managerId=' + userId+'&expense_id='+expense_id,
			dataType: 'json',
			success: function(response) {
				  $('#forward_li').hide();
				  $('#idManagers').dialog("destroy");
				  $('#expense_forward').show();
				  $('#expense_forward_msg').empty();
				  $('#expense_forward_msg').append("<span class='style-1-icon success'></span>Expense Forwarded Successfully.");
				  setTimeout(function(){
					$('#expense_forward').hide();
					window.location = base_url + '/expenses' + "/" + 'myemployeeexpenses';
				  },3000);
				  
				  //geReceipts($('.parameter').val());
			}
	});

}
function expreceiptdownload(expense_id)
{
	
	$.fileDownload((base_url + "/expenses/receipts/downloadexpensereceipt/format/html"), {
		//preparingMessageHtml: "We are preparing your report, please wait...",
		failMessageHtml: "There was a problem generating your report, please try again.",
		httpMethod: "POST",
		data: 'expense_id=' + expense_id
		});
		/*$.ajax({
						url: base_url + "/expenses/receipts/downloadexpensereceipt/format/html",
						data: 'expense_id=' + expense_id,
						dataType: 'json',
						success: function(response) { 
							
							 
						}
				});*/

}
function addmoreexpense(i)
{
		var i=$('#i').val();
		var str = "Receipts";
		
		var url = base_url+"/expenses/receipts/showreceiptspopup/key/"+i;
		var newContent = "<tr id='expense_row_"+i+"' class='expensecls removetr_"+i+"'>"+
								"<td>"+
									"<div class='new-form-ui'>"+
										"<input class='exp_name' type='text' name='expense_name_"+i+"' id='expense_name_"+i+"' onkeyup='validateTextInput(this,\"name\")' onblur='validateTextInput(this,\"name\")'>"+
									"</div>"+	
                                "</td>"+
								"<td>"+
									"<div class='new-form-ui'>"+
										"<input type='text' readonly class='datepicker datevalidation brdr_none' onchange='datevalidation(this,\"date\");' name='expense_date_"+i+"' id='expense_date_"+i+"'>"+
									"</div>"+
                                "</td>"+
                                "<td>"+
								 "<div class='new-form-ui sm_cat'>"+
                                    "<select name='category_"+i+"' id='category_"+i+"' onchange='slectboxchange(this,\"category\");' class='expense_category select2'>"+
                                        "<option value=''>Select Category</option>"+
                                    "</select>"+
									"</div>"+
                                "</td>"+
                               // "<td>"+
                                  //  "<select name='project_"+i+"' id='project_"+i+"'>"+
                                     //  "<option value=''></option>"+ 
                                     //   "<option value=''></option>"+
                                   // "</select>"+
                               // "</td>"+
							  "<td>"+ 
                                    "<div class='new-form-ui clearb sm_currency' style='width: 100%;'>"+
                                        
                                                   "<select name='paymentmode_"+i+"' id='paymentmode_"+i+"' onchange='slectboxchange(this,\"payment mode\");' class='paymentmode select2'>"+
                                                      "<option value=''>Mode</option>" +
                                                    "</select>"+
                                              
										
                                                    "<input type='text' name='payment_ref_"+i+"' id='payment_ref_"+i+"' class='paymentref'>"+
                                              
                                    "</div>"+
                                "</td>"+
								
								
								
                                "<td>"+
                                    "<div class='new-form-ui clearb sm_currency'>"+
                                       
                                                   "<select name='currency_"+i+"' id='currency_"+i+"' class='currencycls select2' onchange='getcurrencybluk("+i+");'>"+
                                                       "<option value=''>Currency</option>"+
                                                    "</select>"+
                                                
                                                    "<input type='text' onkeyup='validateTextAmount(this,\"amount\")' onblur='validateTextAmount(this,\"amount\")' name='amount_"+i+"' id='amount_"+i+"' class='amountcls'>"+
													
											"<input type='hidden' name='cal_amount_"+i+"' id='cal_amount_"+i+"' value=''>"+
											"<span id='currencynamediv_"+i+"' style='display:none'>"+ 
												"<span class='col s6 text-mute'>"+
												  "<span id='currencyname_"+i+"'></span>"+   
													  "<span class='cxcText_"+i+"' onclick='cxcEdit("+i+");'>"+
														 
															  "<span   class='cxcEdit_"+i+"' style='display:inline-block;'>0</span>"+
														   
															  "<input min='1'  onblur='cxcInputblur("+i+");' onfocus='cxcInputfocus("+i+");' class='cxcInput_"+i+"' style='display:none;' type='number' /> INR"+
													  "</span>"+
												"</span>"+
											  "</span>"+
													
                                               
                                    "</div>"+
                                "</td>"+
                                "<td>"+
								"<div class='new-form-ui'>"+
                                    "<input type='text' name='description_"+i+"' id='description_"+i+"'>"+
								"</div>"+	
                                "</td>"+
       
                               "<td class='text-center addbulk'>"+
                                    "<a class='actlink red dropdown-button' data-activates='uploadbtn_"+i+"'>+ Add</a>"+
                                    "<ul id='uploadbtn_"+i+"' class='dropdown-content' style='right: inherit !important; margin-top: 15px;'>"+
                                              "<li>"+ 
                                                   "<div class='file-field input-field'>"+
                                                        "<div class='btn'>"+
                                                            "<span data-activates='addbtn' class='fileupload_"+i+"' div_id='"+i+"'>From Computer</span>"+
                                                            "<input type='file'>"+
                                                        "</div>"+
                                                    "</div>"+
                                              "</li>"+
                                               "<li><a onclick='addexistingfile("+i+");' style='border-top: 1px dashed #1f8abc'>Existing Reciepts</a></li>"+
                                             
									"</ul>"+
									 "<div onclick='uploadedfiles(\"receipts\","+i+");' class='uploaadedReceipts_"+i+"'>"+ 
										"</div>"+
									
                                "</td>"+
                                "<td>"+
                                    "<a onclick='deletedRow("+i+");' class='actlink'><i class='fa fa-trash-o'></i></a>"+
                                "</td>"+
                            "</tr>"+
							"<input type='hidden' name='file_original_names_"+i+"' id='file_original_names_"+i+"' value=''>"+
				"<input type='hidden' name='file_new_names_"+i+"' id='file_new_names_"+i+"' value=''>"+
				"<input type='hidden' name='receipts_ids_"+i+"' id='receipts_ids_"+i+"' value=''>";

					$("#addexpenserow").append(newContent);
					datepickerCal();
					dipalycategories(i);
					displayprojects(i);
					displaycurrency(i);
					multiAjaxUpload('.fileupload_'+i,i);
					i++;
	$('#i').val(i);
	$('.dropdown-button').dropdown();
	
	$("select.expense_category").select2();
	$("select.paymentmode").select2();
	$("select.currencycls").select2();
}
function addexistingfile(val)
{
	addTrip(base_url+'/expenses/receipts/showreceiptspopup/key/'+val,'Receipts');
}
function deletedRow(row)
{
	messageAlert = 'Are you sure you want to delete the expense  ? ';
	jConfirm(messageAlert, "Expense "+msg , function(r) {
		if (r == true) {
				$(".removetr_"+row).remove();
				
		}else
		{
			
		}
	});
	
}
function dipalycategories(key)
{
	$.ajax({
			url: base_url + "/expenses/expenses/getcategories/format/html",
			dataType: 'json',
			success: function(response) {
				$('#category_'+key).html(response.options);
			}
	});
}
function displayprojects(key)
{
	$.ajax({
			url: base_url + "/expenses/expenses/getprojects/format/html",
			dataType: 'json',
			success: function(response) {
				$('#paymentmode_'+key).html(response.options);
			}
	});
}
function displaycurrency(key)
{
	$.ajax({
			url: base_url + "/expenses/expenses/getcurrency/format/html",
			dataType: 'json',
			success: function(response) {
				$('#currency_'+key).html(response.options);
				$("select.currencycls").select2();
			}
	});
}
function validateExpensesOnSubmit(ele)
{
    var parentdivlength = $('tr[id^=expense_row]').length;
	var row_count = $('[id ^= "expense_row_"]').size();
    var errorcount = 0;
	
	var application_currency_id = $('#currencyid').val();
	

	
	if(parentdivlength > 0)
    {
        $('.expensecls').each(function(i){  
		
		
			//category validation
            var ele= $(this).find('.expense_category');  
			var ele_val= $(this).find('.expense_category :selected').val(); 
            var elementid = $(ele).attr('id');
			$('#errors-'+elementid).remove();
            if(ele_val == '')
            {
                $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please select category.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+elementid).remove();
            }
			
			//date validation
			var dateEle= $(this).find('.datevalidation');                            
            var dateeleid = $(dateEle).attr('id');
			
			 $('#errors-'+dateeleid).remove();
			if($(dateEle).val() == '')
            {
                $(dateEle).parent().append("<span class='errors' id='errors-"+dateeleid+"'>Please enter date.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+dateeleid).remove();
            }
			
			//currency validation
			/*var curencyele= $(this).find('.currencycls');                            
            var currencyid = $(curencyele).attr('id');
			
			 $('#errors-'+currencyid).remove();
			 
			if($(curencyele).val() == '')
            {
                $(curencyele).parent().append("<span class='errors' id='errors-"+currencyid+"'>Please select currency.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+currencyid).remove();
            }*/
			
			
			//amount validation
			var amountele= $(this).find('.amountcls');                            
            var amontid = $(amountele).attr('id');
			
			 $('#errors-'+amontid).remove();
			 
			 var curencyele= $(this).find('.currencycls');                            
            var currencyid = $(curencyele).attr('id');
			
			 $('#errors-'+currencyid).remove();
			 
			 if(application_currency_id == '')
			 {
				$(curencyele).parent().append("<span class='errors' id='errors-"+currencyid+"'>Default currency is not configured yet.</span>");
                errorcount++;
			 }else
			 {
				 $('#errors-'+currencyid).remove();
			 }
			if($(amountele).val() == '')
            {
                $(amountele).parent().append("<span class='errors' id='errors-"+amontid+"'>Please enter amount.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+amontid).remove();
            }
			
			//name validation
			var expnameele= $(this).find('.exp_name');                            
            var expnameid = $(expnameele).attr('id');
			
			 $('#errors-'+expnameid).remove();
			if($(expnameele).val() == '')
            {
                $(expnameele).parent().append("<span class='errors' id='errors-"+expnameid+"'>Please enter name.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+expnameid).remove();
            }
			
			//paymentmode validation
			var paymentmodeele= $(this).find('.paymentmode');  
			var payment_val= $(this).find('.paymentmode :selected').val(); 			
            var paymentmodeid = $(paymentmodeele).attr('id');
			
			 $('#errors-'+paymentmodeid).remove();
			if(payment_val == '')
            {
                $(paymentmodeele).parent().append("<span class='errors' id='errors-"+paymentmodeid+"'>Please select payment mode.</span>");
                errorcount++;
            }
            else
            {
            	$('#errors-'+paymentmodeid).remove();
            }
			
			
			
			
           
        });
    }
 

   if(errorcount == 0)
	{
		$('#count').val(row_count);
		if(row_count>0)
		{
			document.getElementById("expenseId").submit();
		}
		else
		{
			$('#error_msg').show();
			$('#error_msg').text('Enter Expense Details.');
		}
			
	}
}
function datevalidation(ele,msg)
{
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
function deleteexpense(trip_id,expense_id)
{
	 var test_url = base_url + "/expenses/trips/deleteexpense/format/html";
	var messageAlert = 'Are you sure you want to delete the expense form the Trip  ? ';
	jConfirm(messageAlert, "Trip " , function(r) {
		if (r == true) {
			$.ajax({
				url: test_url,
				type: 'POST',
				data: '&expense_id=' + expense_id,
				success: function(response) {
					jAlert('expense deleted successfully from trip');
					window.location = window.location;

				}
			});
				
		}
		else
		{
			
		}
		
	});
}
function deleteadvance(id,emp_id)
{
	 var test_url = base_url + "/expenses/employeeadvances/delete/format/html";
	var messageAlert = 'Are you sure you want to delete the advance? ';
	jConfirm(messageAlert, "Advance " , function(r) {
		if (r == true) {
			$.ajax({
				url: test_url,
				dataType: 'json',
				data: '&id=' + id+'&emp_id='+emp_id,
				success: function(response) {
					//console.log(response['message']);
					jAlert(response['message']);
					window.location = window.location;
				}
			});
				
		}
		else
		{
			
		}
		
	});
}

function getCurrency()
{
	
    var currency_id=$("#expense_currency_id").val();
    var application_currencyid=$("#currencyid").val();
	if(currency_id=='' || currency_id==application_currencyid)
	{
		$("#currencynamediv").hide();
	}
	else if(application_currencyid!='')
	{
		
		$("#currencynamediv").show();
	}
	
	var currency='1'+$("#expense_currency_id option:selected").text()+'=';
	$("#currencyname").html(currency);
   /* $.ajax({
		url: base_url + "/expenses/expenses/getcurrencyname/format/json",
		data: 'currencyId=' + currency_id,
		dataType: 'json',
		success: function(data) {
			
	
			$("#currencyname").html(data.currency_name);
			//alert(data.currency_name);
			 
		}*/
//});

}
function getCurrencyAdvances()
{

    var currency_id=$("#currency_id").val();
    var application_currencyid=$("#currencyid").val();
	if(currency_id=='' || currency_id==application_currencyid)
	{
		$("#currencynamediv").hide();
	}
	else if(application_currencyid!='')
	{
		
		$("#currencynamediv").show();
	}
	
	var currency='1'+$("#currency_id option:selected").text()+'=';
	$("#currencyname").html(currency);
   /* $.ajax({
		url: base_url + "/expenses/expenses/getcurrencyname/format/json",
		data: 'currencyId=' + currency_id,
		dataType: 'json',
		success: function(data) {
			
	
			$("#currencyname").html(data.currency_name);
			//alert(data.currency_name);
			 
		}*/
//});

}

function getcurrencybluk(val)
{
	
    var currency_id=$("#currency_"+val).val();
    var application_currencyid=$("#currencyid").val();
	if(currency_id=='' || currency_id==application_currencyid)
	{
		$("#currencynamediv_"+val).hide();
	}
	else if(application_currencyid!='')
	{
		
		$("#currencynamediv_"+val).show();
	}
	
	var currency='1'+$("#currency_"+val+" option:selected").text()+'=';
	
	$("#currencyname_"+val).html(currency);

}
function  getAmount()
{
	$amount=$(".cxcEdit").text();
	$("#cal_amount").val($amount);
}




function multiAjaxUpload(ele,key)
{
	$(ele).uploadFile({
		url:base_url+"/expenses/expenses/uploadsave",
		fileName: "myfile",
	    allowedTypes:"jpg,png,jpeg,gif,doc,docx,pdf,xls,xlsx,zip",	
	    returnType:"json",
	    formData: {},
	    showFileCounter:false,
	    duplicateStrict:true,
	    showDelete:false,
	    maxFileSize:2*1024*1024, // Maximum allowed file size: 2MB
	   // maxFileCount:5,
	    showAbort: false,
	    showDone:false,
	    showCancel: false,
	    uploadButtonClass:'upload-attachment',
	    dragDrop:false,
		
	   // deletelStr:'x',
	    dynamicFormData: function () {
	        return {};
	    },
	    onSelect:function(files)
	    {
			// Clear previous error
	    	$("#fileupload_error").remove();
    		//$("span#errors-doc_attachment").html('');		    

    		$("#loaderimgprofile").show();
	    	var existing = $('#file_original_names_'+key).val();
			var existingArray = existing.split(',');    	
	    	var uploading_file_name = files[0].name; 
	    	if(navigator.userAgent.match(/msie/i) == 'MSIE')
	    		uploading_file_name = uploading_file_name.replace(/C:\\fakepath\\/i, '');
	    	var index = existing.indexOf(uploading_file_name);
	    	var comma = uploading_file_name.indexOf(',');

	    	for(var i = 0; i < files.length; i++)
			{    	
	    		var uploading_file_name = files[i].name;
	    		uploading_file_name = uploading_file_name.replace(/[^a-zA-Z0-9.]+/gi, '_');
	    		var index = existing.indexOf(uploading_file_name);
	        	var comma = uploading_file_name.indexOf(',');
		    	if (comma !== -1){
		    		$(ele).after('<div class="errors upload_error_x" id="fileupload_error">Comma( , ) not allowed in file names.</div>');
		    		$("#loaderimgprofile").hide();
		    		return false;
		    	}

		    	// Hide loader when uploaded file is not in allowed file types
		    	var ext = uploading_file_name.split('.').pop();
		    	var arr = new Array('jpg','png','jpeg','gif','doc','docx','pdf','xls','xlsx','zip');
		    	if($.inArray(ext,arr) == -1){
		    		$("#loaderimgprofile").hide();
		    	}

		    	// Hide loader when uploaded file size is more than 2 MB
		    	if (files[i].size > 2*1024*1024) {
		    		$("#loaderimgprofile").hide();
		    	}		    	
	    	}
		}, 
		onSuccess:function(files,data,xhr)
	    {
			$('.ajax-file-upload-error').not(':eq(0)').hide();
	    	$(".ajax-file-upload-progress").hide();
			$(".ajax-file-upload-filename").hide();
			
			var j = key;
	    	
	    	if(navigator.userAgent.match(/msie/i) == 'MSIE')
	    	{     
		    	// Remove unwanted text in file name
				var c = $('.ajax-file-upload-filename:first').html();
				var d = c.replace(/C:\\fakepath\\/i, '');
				d = d.replace(/<br>/i, '');
				$('.ajax-file-upload-filename:first').html(d);
				
				// Show title of uploaded attachment to users - for big named attachments
				$('.ajax-file-upload-filename:first').attr("title", d);
	    	} else {

				// Show title of uploaded attachment to users - for big named attachments
				$(".ajax-file-upload-filename")
				  .filter(function( index ) {
					  if ($(this).html() == files[0]) {
					    $(this).attr("title", files[0]);
					  }
				});
		    	
	    	}
	    	
	    	$('#file_original_names_'+j).val(($('#file_original_names_'+j).val())?($('#file_original_names_'+j).val()+','+data.filedata.original_name):data.filedata.original_name);
			
	    	$('#file_new_names_'+j).val(($('#file_new_names_'+j).val())?($('#file_new_names_'+j).val()+','+data.filedata.new_name):data.filedata.new_name);
	    	$("#loaderimgprofile").hide();
			
			
		},
		afterUploadAll:function(){
			words = $('#file_original_names_'+key).val();
			var wordsCount = words.split(",").length;
			if($('#image_count_'+key).length>0)
				$('#image_count_'+key).text('');
				
			$('#uploadbtn_'+key).after('<div><a onclick="uploadedfiles(\'file\','+key+');" id="image_count_'+key+'">'+wordsCount+'</a></div>');
		}
		
	});
}
$(document).ready(function(){
	if($('.fileupload_1').length>0){
		multiAjaxUpload('.fileupload_1',1);
	}
});
function uploadedfiles(text,key)
{
	if(text=='file')
	{
		var new_files = $('#file_new_names_'+key).val();
		var org_files = $('#file_original_names_'+key).val();
	}
		
	else
	{
		var files = $('#receipts_ids_'+key).val();
	}
		
	
	var myPos = [ $(window).width() / 5, 150 ];
    $("#iduploadedfiles").dialog({
        title:'Uploaded Files',
		position: myPos,
        modal: true, 
		buttons : [
			
		  ],
      close:function()
      {          
          $(this).dialog("destroy");
      },
      open:function()
      { 
	    
		if(text=='file')
		{
			 $('.ui-widget-overlay').addClass('ui-front-overwrite');
			  $('.ui-dialog').removeClass('ui-dialog-buttons');
			  $('.ui-dialog').removeClass('ui-front');
			  $('.ui-dialog').addClass('ui-btn-overwrite');
				 $.ajax({
				url: base_url + "/expenses/expenses/uploadedfiles/format/html",
				data: 'files=' + new_files+'&key_val='+key+'&org_files='+org_files,
				dataType: 'html',
				success: function(response) {
					  $('#iduploadedfilesContent').html(response);
				}
			});
		}
		else
		{
			Url = base_url + "/expenses/expenses/addreceiptimage/format/html";
			$.ajax({
				url: Url,
				type: 'POST',
				data: '&receipt_ids=' + files+'&key_val='+key+'&isFromBulk='+'yes',
				success: function(response) {
					 $('#iduploadedfilesContent').html(response);
				}
			});
		}				
		 
	
      }
    });
}
function deleteUploadedFile(key,key_val)
{
	var file_name = $('.deleteclass_'+key).attr('file_name');
	var org_name = $('.deleteclass_'+key).attr('org_name');
	$('.deleteclass_'+key).remove();
	
	
	var original_str = window.parent.$('#file_original_names_'+key_val).val();
	var new_str = window.parent.$('#file_new_names_'+key_val).val();
	
	$.post(base_url+"/expenses/expenses/uploaddelete",{op: "delete", doc_new_name: file_name},
	function()
	{
			var x_original_files = $('#file_original_names_'+key_val).val().split(',');
			var x_new_files = $('#file_new_names_'+key_val).val().split(',');

			var org_index = x_original_files.indexOf(org_name);
			if (org_index !== -1) {
				x_original_files.splice(org_index, 1);
				org_data = x_original_files.join(',');
				$('#file_original_names_'+key_val).val(org_data);
			}

			var new_index = x_new_files.indexOf(file_name);
			if (new_index !== -1) {
				x_new_files.splice(new_index, 1);
				new_data = x_new_files.join(',');
				$('#file_new_names_'+key_val).val(new_data);
			}
			$('#image_count_'+key_val).remove();
			var words = $('#file_original_names_'+key_val).val();
			var wordsCount=0;
			if(words!='')
				wordsCount = words.split(",").length;
			$('#uploadbtn_'+key_val).after('<div><a id="image_count_'+key_val+'" onclick="uploadedfiles(\'file\','+key_val+');">'+wordsCount+'</a></div>');
	});
}
function validateTextInput(ele,msg)
{
	var elementid = $(ele).attr('id');
	var value = $(ele).val();
	
	//var re = /^[a-zA-Z0-9\- ]+$/;
	
	// else if(!re.test(value))
	// {
		// $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid "+msg+".</span>");
	// }
	
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
function validateTextAmount(ele,msg)
{
	var elementid = $(ele).attr('id');
	var value = $(ele).val();
	var re = /^0*[1-9]\d*$/;
	
	// else if(!re.test(value))
	// {
		// $(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid "+msg+".</span>");
	// }
	
	$('#errors-'+elementid).remove();
	if(value == '')
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter "+msg+".</span>");
	}
	else if(!re.test(value))
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid "+msg+".</span>");
	}else
	{
		$('#errors-'+elementid).remove();
	}
}
function slectboxchange(ele,msg)
{
	var elementid = $(ele).attr('id');
	var value = $(ele).val();
	$('#errors-'+elementid).remove();
	if(value == '')
	{
		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter "+msg+".</span>");
	}
	else
	{
		$('#errors-s2id_'+elementid).remove();
		$('#s2id_'+elementid).val(value);
		
	}

}
function getReimbursable()
{
	if (document.getElementById('is_reimbursable-1').checked) {

 		 	 $('#errors-is_reimbursable').remove();
		 }
		 
	if (document.getElementById('is_reimbursable-0').checked) {
	
			$('#errors-is_reimbursable').remove();
		}
}
function cxcEdit(key)
{
	$(".cxcEdit_"+key).css('display',"none");
	$(".cxcInput_"+key).css('display',"inline-block");
	$(".cxcEdit_"+key).hide();
	$amount=$(".cxcEdit_"+key).text();
	$("#cal_amount_"+key).val($amount);
}
function cxcInputblur(key)
{
	$(".cxcInput_"+key).hide(); 
	$(".cxcEdit_"+key).html($(".cxcInput_"+key).val());
	$(".cxcEdit_"+key).show();
	$amount=$(".cxcEdit_"+key).text();
	$("#cal_amount_"+key).val($amount);
}
function cxcInputfocus(key)
{
	$(".cxcEdit_"+key).hide();
	$amount=$(".cxcEdit_"+key).text();
	$("#cal_amount_"+key).val($amount);
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