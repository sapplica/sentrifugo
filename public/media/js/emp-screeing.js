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
	
	// To upload feedback file by AJAX
	if($('#upload-file').length>0){
		var btnUpload = $('#upload-file');
		new AjaxUpload(btnUpload, {

			action :  base_url+'/empscreening/uploadfeedback',
			name   : 'feedback-file',  //we can rename it 
			dataType: 'json',
			onSubmit: function(file,ext){
							$('#errors-feedback-file').hide();
							$("#loaderimg").show();
						},
			onComplete: function(file, response){
			    var result = JSON.parse(response);
		      	if(result.result == 'success'){
		      		$('#uploaded-file-name-span').html(result.file_name);
		      		$('#feedback-file-name').val(result.file_name);
				}else{
		      		$('#uploaded-file-name-span').html('');
		      		$('#feedback-file-name').val('');
					
					if(result.msg){
						$('#uploaded-file-name-span').after('<span id="errors-feedback-file" class="errors">' + result.msg + '</span>');
					}else{
						$('#uploaded-file-name-span').after('<span id="errors-feedback-file" class="errors">Please choose different file</span>');
					}
					
					setTimeout(function(){
							$('.uploaderror').fadeOut('slow');
						}
						,3000
					);
				}
		      	$('#delete-feedback').hide();
				$("#loaderimg").hide();
			}
		},'json');		
	}
	
	// To delete feedback
	$('#delete-feedback').click(function(){
		jConfirm("Are you sure you want to delete the attached feedback file?", "Delete File", function(r) {
			if(r == true){
				$("#loaderimg").show();
				$.post(
					base_url + '/empscreening/deletefeedback',
					{
						rec_id: $('#delete-feedback').attr('data'),
						feedback_file: $('#uploaded-file-name-span a').html()
					}
				).done(function(data){
					data = JSON.parse(data);
					if(data.action=='update'){
						$('#uploaded-file-name-span').html('').after('<span id="errors-feedback-file" class="errors">Feedback file is deleted successfully</span>');
						$('#feedback-file-name').val('');
						$('#delete-feedback').hide();
						$("#loaderimg").hide();
					}
				});
			}
		});
	});
	
	// To save feedback file in DB
	$('#save-feedback').click(function(){
		if($('#feedback-file-name').val().length>0){
			var data;
			var detail_id = $('#commentrecord').val();
			data = 'detailid=' + detail_id + '&feedback_file=' + $('#feedback-file-name').val();
			$.ajax({
				url: base_url+"/processes/savefeedback/format/json",   
				type : 'POST',	
				data : data,
				dataType: 'json',
				success : function(response){
					if(response['result'] == 'saved'){
						$('#resppdiv').show();
						$('#respp').html('Your feedback is saved successfully.').show();
						feedback_file = $('#feedback-file-name').val();
						$('#uploaded-file-name-span').html('<a href="' + $('#baseurl').val() + '/empscreening/download?feedback_file=' + feedback_file + '">' + feedback_file + '</a>');
						$('#delete-feedback').show();					
						setTimeout(function(){
							$('#resppdiv').css('display','none');
						},1000);					
					}
				}
			});			
		}else{
			$('.errors').remove();
			$('#uploaded-file-name-span').html('').after('<span id="errors-feedback-file" class="errors">Please select file to upload.</span>');
			return false;
		}

	});
	
});