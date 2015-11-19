<?php

/* * ******************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Description of PerformanceHelper
 *
 * @author mainak paul
 */
class sapp_PerformanceHelper 
{
   
    public static function display_success_message($namespace,$type)
    {
?>
    <script type="text/javascript">
        $(document).ready(function(){
<?php 
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        if ($flashMessenger->setNamespace($namespace)->hasMessages())
        {
            foreach ($flashMessenger->getMessages() as $msg){
                if($type == 'success')
                {
                ?>
                    successmessage('<?php echo trim($msg); ?>');                    
            <?php 
                }
                else
                {
?>
        error_message('<?php echo trim($msg);?>');
<?php        
                }
        } ?>
        <?php } ?>  
        });
    </script>
<?php
    }
    public static function check_per_implmentation($businessunit_id,$department_id)
    {
        $output = array();
        if($businessunit_id != '')
        {
            $model = new Default_Model_Appraisalinit();
            $output = $model->check_per_implmentation($businessunit_id, $department_id);
        }
        return $output;
    }
    
    
    public static function questions_privileges($questionarray,$appraisalid,$checkArr,$initializationdata)
    {
?>
        <ul class="tabs_all_button">
            <li class="active divider" id="alldiv" onclick="showhideqsdiv(1)" >All</li>
            <li id="selecteddiv" onclick="showhideqsdiv(2)">Selected</li>
        </ul> 
     
     <?php if(isset($initializationdata['poppermission']) && $initializationdata['poppermission']=='yes') { ?>
	     <div class="addnewqs" onclick="addnewqspopup(1,'<?php echo $appraisalid; ?>');">
	     		 + Add New Question
	     </div>
	     
     <?php 
     			sapp_PerformanceHelper::question_div(1);
     		}
     ?>
     <div id="hiddenquestiondiv" style="display:none;" class="qstnscrldiv">
						<div class="total-form-controller_" id="childqsdiv">		
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; margin-top: 0px;" id="questiontable" class="requisition-table employee_appraisal-table">
								<tr>
								   <th class="question_field">Questions</th>
								   <th class="field_width">
				                  		<div class="comments_div_fiel">Manager Comments</div>
								   	   <div class="comments_div_fiel">Manager Ratings</div>
								   	   <div class="comments_div_fiel">Employee Comments</div>
								   	    <div class="comments_div_fiel">Employee Ratings</div>
								   </th>
								 </tr>
							</table>
						</div>
	</div>
     
     <div id="questiondiv" class="qstnscrldiv">
    	
			<div class="total-form-controller_" id="parentqsdiv">		
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear:both;margin-top: 0px;" class="requisition-table employee_appraisal-table">
				 <tr>
				   <th class="check_field"><input type="checkbox" class ="selectallcls" name="selectall" value="selectall" id="selectall">Check All</th>
				   <th class="question_field">Questions</th>
				   <th class="field_width">
                  	<div class="comments_div_fiel"> <input type="checkbox" class ="mgrcmntcls" name="mgrcmnt" value="" id="mgrcmnt">Manager Comments</div>
				   	<div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls" name="mgrrating" value="" id="mgrrating">Manager Ratings	</div>
				   	<div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls" name="empcmnt" value="" id="empcmnt">Employee Comments</div>
				   	<div class="comments_div_fiel"><input type="checkbox" class ="empratingcls" name="empratings" value="" id="empratings" >Employee Ratings</div>
				   </th>
				 </tr>
		 <?php foreach($questionarray as $key => $question)
		 		{
		 			$check = '';
		 			$mgrcheck = '';
		 			$mgrrate = '';
		 			$empratcheck = '';
		 			$empcmntcheck = '';
		 			if(!empty($checkArr))
		 			{
		 				
			 			if(array_key_exists($question['id'], $checkArr))
			 			{
			 			   $check = 'checked';
			 			   $checkid = $checkArr[$question['id']];
			 			   for($i=0;$i<sizeof($checkid);$i++)
			 			   {
			 			   		if($checkid['MC'] == 1)
			 			   			 $mgrcheck = 'checked';
			 			   		if($checkid['MR'] == 1)
			 			   			 $mgrrate = 'checked';		 
			 			   		if($checkid['ER'] == 1)
			 			   			 $empratcheck = 'checked';
			 			   		if($checkid['EC'] == 1)
			 			   			 $empcmntcheck = 'checked';		 		 
			 			   }
			 			   
			 			} 
		 			}else
		 			{
 							 $check = 'checked';	
	 			   			 $mgrcheck = 'checked';
	 			   			 $mgrrate = 'checked';		 
	 			   			 $empratcheck = 'checked';
	 			   			 $empcmntcheck = 'checked';
		 			}  
		 ?>
				 <tr id="questiontr_<?php echo $question['id'];?>">
				   <td class="check_field"><input type="checkbox" class ="checkallcls" ques_id ="<?php echo $question['id'];?>" id="check_<?php echo $question['id'];?>" name="check[]" value="<?php echo $question['id'];?>" <?php echo $check;?> onclick="checkchildtd(this)"></td>
				   <td class="question_field" id="queshtml_<?php echo $question['id'];?>">
				   <div>
				   			<span class="appri_ques"><?php echo $question['question']; ?></span>
				   			<span class="appri_desc"><?php echo $question['description']; ?></span>
				   </div>
				   </td>
				   <td class="field_width">
                    <div class="comments_div_fiel"><input type="checkbox" class ="mgrcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrcmnt_<?php echo $question['id'];?>" name="mgrcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $mgrcheck;?> onclick="checkparenttd(this)">Manager Comments</div>
				   	    <div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrrating_<?php echo $question['id'];?>" name="mgrrating[<?php echo $question['id'];?>]" value="1" <?php echo $mgrrate;?> onclick="checkparenttd(this)" >Manager Ratings</div>	
				   	   <div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> onclick="checkparenttd(this)">Employee Comments</div>
				   	   <div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php echo $empratcheck;?> onclick="checkparenttd(this)">Employee Ratings</div>
				  </td>
				  	<?php sapp_PerformanceHelper::check_selected_Qs($check,$mgrcheck,$mgrrate,$empratcheck,$empcmntcheck,$question['id']); ?>
				 </tr>
		<?php
		 		} 
		?>		 
				 
				</table>
				<input type="hidden" id="appraisalid" name="appraisalid" value="<?php echo $appraisalid;?>">
				<input type="hidden" id="initializeflag" name="initializeflag" value="">
				<input type="hidden" id="initializestep" name="initializestep" value="<?php echo $initializationdata['initialize_status'];?>">
				<input type="hidden" id="questioncount" name="questioncount" value="<?php echo sizeof($questionarray);?>">
			</div>
			</div>
		
				
			<?php if($initializationdata['group_settings'] != 2){?>
			<div class="new-form-ui-submit" id="qssubmitdiv">	
					<?php if($initializationdata['initialize_status'] == 1){?>
						<button name="submitbutton" id="submitbuttons" type="button" onclick="saveInitilize(3)">Update Initialization</button>
					<?php } else {?>
					<button name="submitbutton" id="submitbuttons" type="button" onclick="saveInitilize(1)">Save & Initialize</button>
					<button name="submitbutton" id="submitbuttons" type="button" onclick="saveInitilize(2)">Save & Initialize Later</button>
					<button name="submitbutton" id="submitbuttons" type="button" onclick="changesettings('0','<?php echo $appraisalid?>')">Discard</button>
					<?php } ?>
					
			</div>	
			<?php }?> 
		
       
		
		<script type="text/javascript">
				$(document).ready(function()
				{
					$('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
					$('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
					
					$('#selectall').click(function(event) {  
						$('.checkallcls').prop('checked',$(this).prop('checked'));
						if(this.checked) { 
				            $('.checkallcls').each(function() { 
				            	var id = $(this).attr('ques_id'); 
				            	appendcheckboxtext(id);                
				            });
				        }else
				        {
                                            //$('.mgrcmntcls').prop('checked',false);
                                            //$('.empcmntcls').prop('checked',false);
                                            //$('.mgrratingcls').prop('checked',false);
                                            //$('.empratingcls').prop('checked',false);
                                            $("tr[id^=hiddentr_]").remove();
                                            appendheighttodiv(1);
				        }          
				    });

				    $('#mgrcmnt').click(function(event) {  //on click 
						if(this.checked) { 
							$('.checkallcls').each(function() { 
								var id = $(this).attr('ques_id'); 
								if(this.checked)
								{
									$("#mgrcmnt_"+id).prop('checked', true);
								}
								appendcheckboxtext(id);                
							});
						}else
						{
							$('.mgrcmntcls').prop('checked',$(this).prop('checked'));
							$("tr[id^=hiddentr_]").remove();
							appendheighttodiv(1);
						}
				    });

				    $('#mgrrating').click(function(event) {  //on click 
						if(this.checked) { 
							$('.checkallcls').each(function() { 
								var id = $(this).attr('ques_id'); 
								if(this.checked)
								{
									$("#mgrrating_"+id).prop('checked', true);
								}									
								appendcheckboxtext(id);                
							});
						}else
						{
							$('.mgrratingcls').prop('checked',$(this).prop('checked'));
							$("tr[id^=hiddentr_]").remove();
							appendheighttodiv(1);
						}
				    });

				    $('#empcmnt').click(function(event) {  //on click 		    	
						if(this.checked) { 
							$('.checkallcls').each(function() { 
								var id = $(this).attr('ques_id'); 
								if(this.checked)
								{
									$("#empcmnt_"+id).prop('checked', true);
								}									
								appendcheckboxtext(id);                
							});
						}else
						{
							$('.empcmntcls').prop('checked',$(this).prop('checked'));
							$("tr[id^=hiddentr_]").remove();
							appendheighttodiv(1);
						}
				    });
					
					$('#empratings').click(function(event) {  //on click
				    	if(this.checked) { 
							$('.checkallcls').each(function() { 
								var id = $(this).attr('ques_id'); 
								if(this.checked)
								{
									$("#empratings_"+id).prop('checked', true);
								}									
								appendcheckboxtext(id);                
							});
						}else
						{
							$('.empratingcls').prop('checked',$(this).prop('checked'));
							$("tr[id^=hiddentr_]").remove();
							appendheighttodiv(1);
						} 
				     
				    });
					
					$('.checkallcls').click(function(event) {  //on click
				    	if(!$(this).prop('checked'))
				    	{
				    		/*$('.empratingcls').prop('checked',$(this).prop('checked'));
				    		$('.empratingcls').prop('checked',$(this).prop('checked'));
				    		$('.empratingcls').prop('checked',$(this).prop('checked'));*/
				    	} 
				     
				    });

				    <?php if(empty($checkArr)) { ?>
					    $('#selectall').prop('checked',true);
			    		$('#mgrcmnt').prop('checked',true);
			    		$('#mgrrating').prop('checked',true);
			    		$('#empcmnt').prop('checked',true);
			    		$('#empratings').prop('checked',true);
				    <?php }?>

				    // In 'Step3 - Configure Appraisal Parameters' screen check column headers when all options were selected on page load
				    checkparentclass();
				    if($("#info_message").html()=='')
				    	$("#info_message").html('Configure Questions For All Employees');
				});

		</script>
<?php 					
    }
    
    	public static function employee_group_questions_privileges($groupEmployeeCountArr,$appraisalid,$initializationdata,$dispflag)
	    {
		
	    ?>
	    	<?php if($dispflag!='view' && !empty($groupEmployeeCountArr)) { ?>
				<div style="clear: both;position: relative;top: 17px;width: 92%;" class="ml-alert-1-info" id="msg_div">
					<div class="style-1-icon info"></div>
					Once appraisal process is initialized and employees start filling their appraisals, questions cannot be added or edited.
				</div>
			<?php } ?>	
			<div class="width_98">
<?php 	    	if(!empty($groupEmployeeCountArr))
	    	{ ?>
	    	
<?php 	    	
	    		foreach($groupEmployeeCountArr as $key =>  $val)
	    		{
?>	    						    		
	    			<div class="groupeddiv" id="groupdiv_<?php echo $val['id'];?>">
	    				<div class="groupnamediv" id="groupname_<?php echo $val['id']; ?>"><?php echo $val['group_name'];?></div>
	    				<div class="empcountdiv">Employees <span class="count"><?php echo $val['empcount'];?></span></div>
	    				<div class="qscountdiv">Questions <span class="count"><?php echo $val['qscount'];?></span></div>
	    				
	    				<div id="hoverdiv_<?php echo $val['id'];?>" class="grphoverclass">
	    					<ul>
	    					<?php if($initializationdata['initialize_status'] == 1) { ?>
		    					<li onclick='viewempgroup("<?php echo $val['group_name'];?>","<?php echo $val['group_id'];?>","<?php echo $appraisalid;?>","<?php echo $val['empcount'];?>");'>View</li>
		    					<?php if($dispflag == 'edit') { ?>
		    						<li onclick='editgroupemp("<?php echo $val['group_name'];?>","<?php echo $val['group_id'];?>","<?php echo $appraisalid;?>","<?php echo $val['empcount'];?>");'>Edit</li>
		    					<?php } //else if($dispflag == 'display') {?>
		    						<!--  <li onclick='showgroupemp("<?php //echo $val['group_name'];?>","<?php //echo $val['group_id'];?>","<?php //echo $appraisalid;?>","<?php //echo $val['empcount'];?>");'>Edit</li>-->
		    					<?php //}?>	
		    				<?php } else {?>
		    					<li onclick='viewempgroup("<?php echo $val['group_name'];?>","<?php echo $val['group_id'];?>","<?php echo $appraisalid;?>","<?php echo $val['empcount'];?>");'>View</li>
		    					<?php if($dispflag == 'edit') {?>
		    					<li onclick='editgroupemp("<?php echo $val['group_name'];?>","<?php echo $val['group_id'];?>","<?php echo $appraisalid;?>","<?php echo $val['empcount'];?>");'>Edit</li>
		    					<li onclick='deletegroupemp("<?php echo $val['group_id'];?>","<?php echo $appraisalid;?>","<?php echo $val['id'];?>");'>Delete</li>
		    				<?php }}?>
		    				</ul>
	    				</div>
	    			</div>
	    			
<?php 
	    		}
	    	}
?>	    		
<div class="clear" id="clear_div"></div>
				<?php if(isset($initializationdata['empcount']) && $initializationdata['empcount'] > 0) {?>	
					<?php if($dispflag!='view') { ?>
						<div class="newgroup_msg managerresponsediv_msg">
							Groups are not configured yet.
						</div>
	    				
	    				<div class="new-form-ui-submit" style="margin-left: 0px;">
	    					<div class="create_new_group" onclick="creategroupemp('<?php echo $appraisalid;?>')" style="margin-left: 0px; height: 17px;">Create New Group</div>
	    					<button name="submitbutton" id="submitbuttons" class="discard_button" type="button" onclick="changesettings('0','<?php echo $appraisalid?>')">Discard</button>
	    				</div>	
	    			<?php } ?>	
	    		<?php } ?>	
	    		
	    		<div class="invfrnds_confirm create_group_div per_steps"  style="display:none; margin-left: 0px;" >
				</div>
				<div class="clear"></div>
				</div>
	    		
	    	
	    	
	    		<div class="new-form-ui-submit" id="initialization_div">
	    		<?php if($initializationdata['initialize_status'] == 1){?>
	    			<?php if($dispflag!='view' && $dispflag!='display') { ?>
	    				<button name="submitbutton" id="submitbuttons" type="button" onclick="saveGroupInitilize(1,'<?php echo $appraisalid?>')">Update Initialization</button>
	    			<?php } ?>	
	    			
	    	  	<?php }else {?>	
		    		<?php if($dispflag == 'edit') { ?>
		    			<?php if($initializationdata['empcount'] == 0 && !empty($groupEmployeeCountArr)){?>
							 <button name="submitbutton" id="submitbuttons" class="init_class" type="button" onclick="saveGroupInitilize(1,'<?php echo $appraisalid?>')">Initialize</button>
							<button name="submitbutton" id="submitbuttons" class="init_class_later" type="button" onclick="saveGroupInitilize(2,'<?php echo $appraisalid?>')">Initialize Later</button>
							<button name="submitbutton" id="submitbuttons" class="discard_button" type="button" onclick="changesettings('0','<?php echo $appraisalid?>')">Discard</button> 
						<?php }?>	
						
					
					<?php }else{ ?>	
					
				<?php }}?>	
				</div>
				<div class="clear"></div>	
				
				<script type="text/javascript" language="javascript">
					$(document).ready(function()
					{
						/*if($(".groupeddiv").length == 0)
						{
							$(".discard_button").remove();
						}*/
						if($(".groupeddiv").length > 0)
						{
							$(".newgroup_msg").remove();
						}
						if($(".newgroup_msg").is(':visible')) $("#msg_div").remove();
						$("#info_message").html('Customized Employee Groups');
					});
				</script>
<?php			
	    	
	    }
	    
	    public static function check_selected_Qs($check,$mgrcheck,$mgrrate,$empratcheck,$empcmntcheck,$id)
	    {
	    	if($check == 'checked' || $mgrcheck == 'checked' || $mgrrate == 'checked' || $empratcheck == 'checked' || $empcmntcheck == 'checked')
	    	{
?>	    
				<script>
						var id = '<?php echo $id;?>';
						appendcheckboxtext(id);
				</script>		
	    		
<?php    	
	   		}
	   }
	   
		public static function ff_check_selected_Qs($check,$empratcheck,$empcmntcheck,$id)
	    {
	    	if($check == 'checked' || $empratcheck == 'checked' || $empcmntcheck == 'checked')
	    	{
?>	    
				<script>
						var id = '<?php echo $id;?>';
						ff_appendcheckboxtext(id);
				</script>		
	    		
<?php    	
	   		}
	   }
	   
	   
    public static function questions_privileges_view($questionarray,$appraisalid,$checkArr,$initializationdata)
    {
?>     
        
            <ul class="tabs_all_button"><li id="selecteddiv" class="active" style="cursor: default;">Questions</li></ul> 
            <div id="questiondiv" class="qstnscrldiv">
            <div class="total-form-controller_">		
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; margin-top: 0px; width: 97%;" class="requisition-table employee_appraisal-table" >
                    <tr>
                        <th class="question_field">Questions</th>
                        <th class="field_width">
                            <div class="comments_div_fiel">Manager Comments</div>
                            <div class="comments_div_fiel">Manager Ratings</div>
                            <div class="comments_div_fiel">Employee Comments</div>
                            <div class="comments_div_fiel">Employee Ratings</div>
                        </th>
                    </tr>
<?php 
            foreach($questionarray as $key => $question)
            {
                $check = '';  $mgrcheck = '';  $mgrrate = '';  $empratcheck = '';  $empcmntcheck = '';
                if(!empty($checkArr))
                {
                    if(array_key_exists($question['id'], $checkArr))
                    {
                        $check = 'checked';
                        $checkid = $checkArr[$question['id']];
                        for($i=0;$i<sizeof($checkid);$i++)
                        {
                            if($checkid['MC'] == 1)
                                $mgrcheck = 'checked';
                            if($checkid['MR'] == 1)
                                $mgrrate = 'checked';		 
                            if($checkid['ER'] == 1)
                                $empratcheck = 'checked';
                            if($checkid['EC'] == 1)
                                $empcmntcheck = 'checked';		 		 
                       }			 			   
                    } 
                }  
?>
                    <tr id="questiontr_<?php echo $question['id'];?>">				   
                        <td class="question_field" id="queshtml_<?php echo $question['id'];?>">
                        <div>
				   			<span class="appri_ques"><?php echo $question['question']; ?></span>
				   			<span class="appri_desc"><?php echo $question['description']; ?></span>
				   		</div>
                        </td>
                        <td class="field_width">
                            <div class="comments_div_fiel"><input type="checkbox" class ="mgrcmntcls" ques_id ="<?php echo $question['id'];?>" id="mgrcmnt_<?php echo $question['id'];?>" name="mgrcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $mgrcheck;?> disabled >Manager Comments</div>
                            <div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls" ques_id ="<?php echo $question['id'];?>" id="mgrrating_<?php echo $question['id'];?>" name="mgrrating[<?php echo $question['id'];?>]" value="1" <?php echo $mgrrate;?> disabled >Manager Ratings</div>	
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> disabled >Employee Comments</div>
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php echo $empratcheck;?> disabled >Employee Ratings</div>
                        </td>				  	
                    </tr>
<?php
            } 
?>	    				 
                </table>
                <input type="hidden" id="appraisalid" name="appraisalid" value="<?php echo $appraisalid;?>">
                <input type="hidden" id="initializeflag" name="initializeflag" value="">
                <input type="hidden" id="initializestep" name="initializestep" value="<?php echo $initializationdata['initialize_status'];?>">
            </div>
        </div>
        <div class="new-form-ui-submit" id="qssubmitdiv">
               </div>	
        <div class="clear"></div>
   		
    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
            //$('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});	
            if($("#info_message").html()=='')
		    	$("#info_message").html('Configure Questions For All Employees');									
        });
    </script>
<?php 					
    }        
			   public static function question_div($moduleflag) 
			   {
		?>
			
			<div id="qspopupdiv" style="display: none; ">	
				
					 <div class="total-form-controller" >	
					 <div id="successdiv" style="display:none;">
						<div class='ml-alert-1-success'>
						<div class='style-1-icon success'></div>
							Question added succesfully
					  </div>
					 </div>	
					 <div id="error_message" style="display:none;"></div>	
					  <div id="contentdiv"> 
					 		<div class="new-form-ui ">				  
					  			<label class="required">Parameter</label>
								<div class="division">
									<select id="category_id" name="category_id" >
										<option value="">Select Parameter</option>
									</select>
								</div>
							</div>
							
							<div class="new-form-ui ">
				            	<label class="required">Question <img class="tooltip" title="Special characters allowed are - ? ' . , / # @ $ & * ( ) !" src="<?php echo MEDIA_PATH;?>images/help.png"></label>
				            	<div class="division">
									<input type="text" onkeyup="validatequestionname(this)" onblur="validatequestionname(this)" name="question_id" id="question_id" value="" maxlength="100">                            
								</div>
				        	</div>
				        	
				        	<div class="new-form-ui textareaheight">
				            <label class="">Description </label>
					            <div class="division">
									<textarea name="description" id="description"></textarea>
							    </div>
				        	</div>
				        	<input type="hidden" id="moduleflag" name="moduleflag" value="<?php echo $moduleflag;?>">
				        	
				        	<div class="new-form-ui-submit"  >
						
							<input type="button" value="Save" id="submitqs" name="submitqs" onclick="savequestions()"/>																	
							<button name="Cancels" id="Cancels" type="button" onclick="closeqspopup()">Cancel</button>
							
							</div>
					 </div>  
					 </div>
				 </div>
<?php
   			}
    public static function ff_questions_privileges($questionarray,$checkArr)
    {
?><div class="invfrnds_confirm" style="width: 100% !important; margin-left: 0px;">
<div class="users_list" style="width: 100%;">
      <div class="cofig_title"  style="margin-left: 0px; padding-top: 0px;">Configure Questions</div>
        <ul class="tabs_all_button white_tabs"  style="margin-left: 0px;">
            <li class="active divider" id="alldiv" onclick="showhideqsdiv(1)" >All</li>
            <li id="selecteddiv" onclick="showhideqsdiv(2)">Selected</li>
        </ul> 
     
	     <div class="addnewqs" onclick="ffaddnewqspopup();">
	     		 + Add New Question
	     </div>
	    
     <?php 
     		sapp_PerformanceHelper::ff_question_div();
     ?>
     <div id="hiddenquestiondiv" style="display:none;" class="qstnscrldiv">
		<div class="total-form-controller" id="childqsdiv">		
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; width: 100%; margin-top: 0px;" id="questiontable" class="requisition-table employee_appraisal-table">
				<tr>
				   <th class="question_field">Questions</th>
				   <th class="field_width">
				   	   <div class="comments_div_fiel">Comments</div>
				   	    <div class="comments_div_fiel">Ratings</div>
				   </th>
				 </tr>
			</table>
		</div>
	</div>
     
     <div id="questiondiv" class="qstnscrldiv">
    	
			<div class="total-form-controller_" id="parentqsdiv">		
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; width: 100%;margin-top: 0px;" class="requisition-table employee_appraisal-table">
				 <tr>
				   <th class="check_field" width="10%"><input type="checkbox" class ="selectallcls" name="selectall" value="selectall" id="selectall">Check All</th>
				   <th class="question_field" width="33%">Questions</th>
				   <th class="field_width" width="57%">
				   	<div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls" name="empcmnt" value="" id="empcmnt">Comments</div>
				   	<div class="comments_div_fiel"><input type="checkbox" class ="empratingcls" name="empratings" value="" id="empratings" checked disabled>Ratings</div>
				   </th>
				 </tr>
			<?php foreach($questionarray as $key => $question)
				{
					$check = '';
					$mgrcheck = '';
					$mgrrate = '';
					$empratcheck = '';
					$empcmntcheck = '';
					if(!empty($checkArr))
					{
						if(array_key_exists($question['id'], $checkArr))
						{
						   $check = 'checked';
						   $checkid = $checkArr[$question['id']];
						   for($i=0;$i<sizeof($checkid);$i++)
						   {
								if($checkid['ER'] == 1)
									 $empratcheck = 'checked';
								if($checkid['EC'] == 1)
									 $empcmntcheck = 'checked';		 		 
						   }
						} 
					}
					else
					{
						 $check = 'checked';
						 $empratcheck = 'checked';
						 $empcmntcheck = 'checked';
						
					}  
				?>
				<tr id="questiontr_<?php echo $question['id'];?>">
					<td class="check_field"><input type="checkbox" class ="checkallcls" ques_id ="<?php echo $question['id'];?>" id="check_<?php echo $question['id'];?>" name="check[]" value="<?php echo $question['id'];?>" <?php echo $check;?> onclick="checkchildtd(this)"></td>
					<td class="question_field" id="queshtml_<?php echo $question['id'];?>">
					<div>
						<span class="appri_ques"><?php echo $question['question']; ?></span>
						<span class="appri_desc"><?php echo $question['description']; ?></span>
					</div>
					</td>
					<td class="field_width">
						<div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> onclick="checkparenttd(this)">Comments</div>
						<div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php //echo $empratcheck;?> checked disabled>Ratings</div>
					</td>
					<?php sapp_PerformanceHelper::ff_check_selected_Qs($check,$empratcheck,$empcmntcheck,$question['id']); ?>
				</tr>
				<?php
				} 
		?>		 
				 
				</table>
			</div>
			</div>
			</div> </div> 
			<div class="new-form-ui-submit" style="margin-top: 40px;">
				<input type="hidden" name="initialize_status" id="initialize_status" value="">
				<button name="submitbutton" id="submitbuttons" type="button" onclick="saveInitilize(1)">Save & Initialize</button>
				<button name="submitbutton" id="submitbuttons" type="button" onclick="saveInitilize(2)">Save & Initialize Later</button>
				<button onclick="window.location.href='<?php echo BASE_URL;?>feedforwardinit';" type="button" id="Cancel" name="Cancel">Cancel</button>
			</div>
				
		<script type="text/javascript">
				$(document).ready(function()
				{
					$('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
					$('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
					
					$('#selectall').click(function(event) {  
						$('.checkallcls').prop('checked',$(this).prop('checked'));
						if(this.checked) { 
				            $('.checkallcls').each(function() { 
				            	var id = $(this).attr('ques_id'); 
				            	appendcheckboxtext(id);                
				            });
				        }else
				        {
                        	$("tr[id^=hiddentr_]").remove();
							  appendheighttodiv(1);
				        }          
				    });

				    $('#empcmnt').click(function(event) {  //on click 
					    	if(this.checked) { 
					            $('.checkallcls').each(function() { 
					            	var id = $(this).attr('ques_id'); 
									if(this.checked)
									{
										$("#empcmnt_"+id).prop('checked', true);
									}									
					            	appendcheckboxtext(id);                
					            });
					        }else
					        {
								$('.empcmntcls').prop('checked',$(this).prop('checked'));
					        	$("tr[id^=hiddentr_]").remove();
								appendheighttodiv(1);
					        }
				    });
				    
				    <?php if(empty($checkArr)) { ?>
					    $('#selectall').prop('checked',true);
			    		$('#empcmnt').prop('checked',true);
			    		$('#empratings').prop('checked',true);
			    	<?php }?>
				});

		</script>
<?php 					
    }
	public static function ff_questions_privileges_view($questionarray,$checkArr,$saveFlag='')
    {
?>   <div style="margin-left: 0px;" class="cofig_title">Questions</div>   
<ul class="tabs_all_button" style="margin-left: 0px;"><li id="selecteddiv" class="active" style="cursor: default; background: none repeat scroll 0% 0% #fff;">Questions</li></ul>
        <div id="questiondiv" class="qstnscrldiv per_steps"  style="overflow: hidden; padding-bottom: 0px; margin-bottom: 20px;">
            
            	
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; width: 100%; margin-top: 0px; margin-bottom: 0px;" class="requisition-table employee_appraisal-table">
                    <tr>
                        <th class="question_field">Question</th>
                        <th class="field_width">
                            <div class="comments_div_fiel">Comments</div>
                            <div class="comments_div_fiel">Ratings</div>
                        </th>
                    </tr>
<?php 
            foreach($questionarray as $key => $question)
            {
                $check = '';  $mgrcheck = '';  $mgrrate = '';  $empratcheck = '';  $empcmntcheck = '';
                if(!empty($checkArr))
                {
                    if(array_key_exists($question['id'], $checkArr))
                    {
                        $check = 'checked';
                        $checkid = $checkArr[$question['id']];
                        for($i=0;$i<sizeof($checkid);$i++)
                        {
                            if($checkid['ER'] == 1)
                                $empratcheck = 'checked';
                            if($checkid['EC'] == 1)
                                $empcmntcheck = 'checked';		 		 
                       }			 			   
                    } 
                }  
?>
                    <tr id="questiontr_<?php echo $question['id'];?>">				   
                        <td class="question_field" id="queshtml_<?php echo $question['id'];?>">
                        <div>
				   			<span class="appri_ques"><?php echo $question['question']; ?></span>
				   			<span class="appri_desc"><?php echo $question['description']; ?></span>
				   		</div>
                        </td>
                        <td class="field_width">
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> disabled >Comments</div>
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php echo $empratcheck;?> disabled >Ratings</div>
                        </td>				  	
                    </tr>
<?php
            } 
?>	    				 
                </table>
            
        </div>
        <?php if($saveFlag=='yes'){?>
        	<div class="new-form-ui-submit">
				<button name="submitbutton" id="submitbuttons" type="button" onclick="closeFF()">Update</button>
				<button onclick="window.location.href='<?php echo BASE_URL;?>feedforwardinit';" type="button" id="Cancel" name="Cancel">Cancel</button>
			</div>
		<?php }?>
        <div class="clear"></div>
    </div>		
    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
            $('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});										
        });
    </script>
<?php 					
    } 
    
				public static function ff_question_div() 
			   {
?>
<script type="text/javascript" src="<?php echo MEDIA_PATH;?>js/pa.js"></script>
			<div id="ffqspopupdiv" style="display: none;">	
				  
		</div>		 		    
<?php
   			}
   			
   			   public static function saveCronMail($optionArr) 
			   {
			   		$options['subject'] = APPLICATION_NAME.':'.$optionArr['subject'];
                    $options['header'] = $optionArr['header'];
                    $options['toEmail'] = $optionArr['toname'];  
                    $options['toName'] = $optionArr['toemail'];
                    $options['bcc'] = $optionArr['bcc'];
                    $options['message'] = $optionArr['message'];
                    $options['cron'] = $optionArr['cron'];
	                sapp_Global::_sendEmail($options);    		
			   	
			   }
   			
				
			   public static function displayappdetails($appraisalid) 
			   {

			   		//$appperiod = '';
					$budeptArr =  sapp_Global::getbudeptname($appraisalid);
					$appstring = '';
			 		if(isset($budeptArr['appdata']))
			 		{
				  		$appraisalInfo = $budeptArr['appdata'];
				  		$appstring = self::getAppraisalText($appraisalInfo);	
			 		}
?> 		

		 		<div id="deptinfo" class="deptinfo">
					<span class="head_txt">Business Unit : </span><span><?php echo $budeptArr['buname'].''.($budeptArr['deptname']!=''?'</span> <span class="head_txt">Department : </span><span> '.$budeptArr['deptname']:''); ?></span>
				</div>
				<div id="appraisalinfo" class="appraisalinfo">
					<?php echo $appstring;?>
				</div>
<?php 
			   }
			   
			   public static function getAppraisalText($appraisalInfo = array()) {
			   		$appperiod = '';
					$appmode = $appraisalInfo['appraisal_mode'].' Appraisal';
			 		
					if($appraisalInfo['appraisal_mode'] != 'Yearly')
						$appperiod = ' ('.utf8_encode(substr($appraisalInfo['appraisal_mode'],0,1)).$appraisalInfo['appraisal_period'].')';
				
					return $appmode.$appperiod.(isset($appraisalInfo['to_year'])?(', '.$appraisalInfo['to_year']):'');
			   }
		   
public static function manager_questions_privileges($questionarray,$appraisalid,$checkArr,$initializationdata)
{
?>
	<div class="cofig_title" style="margin-left: 0px;">Configure Appraisal Parameters for All Employees</div>
	<ul class="tabs_all_button"  style="margin-left: 0px;">
		<li class="active divider" id="alldiv" onclick="showhideqsdiv(1)" >All</li>
		<li id="selecteddiv" onclick="showhideqsdiv(2)">Selected</li>
	</ul>      
	<?php if(isset($initializationdata['poppermission']) && $initializationdata['poppermission']=='yes') { ?>
	<div class="addnewqs" onclick="addmanagerqspopup(1,'<?php echo $appraisalid; ?>');">
		 + Add New Question
	</div>   
	<?php 
		sapp_PerformanceHelper::manager_question_div(1);
	}
	?>
	<div id="hiddenquestiondiv" style="display:none;" class="qstnscrldiv">
		<div class="total-form-controller" id="childqsdiv">		
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; margin-top: 0px; width: 100%;" id="questiontable" class="requisition-table employee_appraisal-table">
				<tr>
					<th class="question_field">Questions</th>
					<th class="field_width">
						<div class="comments_div_fiel">Manager Comments</div>
						<div class="comments_div_fiel">Manager Ratings</div>
						<div class="comments_div_fiel">Employee Comments</div>
						<div class="comments_div_fiel">Employee Ratings</div>
					</th>
				</tr>
			</table>
		</div>
	</div>
     
	<div id="questiondiv" class="qstnscrldiv">
		<div class="total-form-controller_" id="parentqsdiv">		
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; margin-top: 0px; width: 100%;" class="requisition-table employee_appraisal-table">
				<tr>
					<th class="check_field"><input type="checkbox" class ="selectallcls" name="selectall" value="selectall" id="selectall">Check All</th>
					<th class="question_field">Questions</th>
					<th class="field_width">
						<div class="comments_div_fiel"> <input type="checkbox" class ="mgrcmntcls" name="mgrcmnt" value="" id="mgrcmnt">Manager Comments</div>
						<div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls" name="mgrrating" value="" id="mgrrating">Manager Ratings</div>					
						<div class="comments_div_fiel" style="width:45%;"> <input type="checkbox" class ="empcmntcls" name="empcmnt" value="" id="empcmnt">Employee Comments</div>
						<div class="comments_div_fiel"><input type="checkbox" class ="empratingcls" name="empratings" value="" id="empratings" >Employee Ratings</div>
					</th>
				</tr>
				<?php foreach($questionarray as $key => $question)
				{
					$check = '';
		 			$mgrcheck = '';
		 			$mgrrate = '';					
					$empratcheck = '';
					$empcmntcheck = '';
					if(!empty($checkArr))
					{
						if(array_key_exists($question['id'], $checkArr))
						{
							$check = 'checked';
							$checkid = $checkArr[$question['id']];
							for($i=0;$i<sizeof($checkid);$i++)
							{
			 			   		if($checkid['MC'] == 1)
			 			   			 $mgrcheck = 'checked';
			 			   		if($checkid['MR'] == 1)
			 			   			 $mgrrate = 'checked';								
								if($checkid['ER'] == 1)
								$empratcheck = 'checked';
								if($checkid['EC'] == 1)
								$empcmntcheck = 'checked';		 		 
							}
						} 
					}
					else
					{
						$check = 'checked';	
						$mgrcheck = 'checked';
						$mgrrate = 'checked';		 
						$empratcheck = 'checked';
						$empcmntcheck = 'checked';
					}
					?>
					<tr id="questiontr_<?php echo $question['id'];?>">
						<td class="check_field"><input type="checkbox" class ="checkallcls" ques_id ="<?php echo $question['id'];?>" id="check_<?php echo $question['id'];?>" name="check[]" value="<?php echo $question['id'];?>" <?php echo $check;?> onclick="checkmgrchildtd(this)"></td>
						<td class="question_field" id="queshtml_<?php echo $question['id'];?>">
							<div>
								<span class="appri_ques"><?php echo $question['question']; ?></span>
								<span class="appri_desc"><?php echo $question['description']; ?></span>
							</div>
						</td>
						<td class="field_width">
							<div class="comments_div_fiel"><input type="checkbox" class ="mgrcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrcmnt_<?php echo $question['id'];?>" name="mgrcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $mgrcheck;?> onclick="checkparenttd(this)">Manager Comments</div>
							<div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrrating_<?php echo $question['id'];?>" name="mgrrating[<?php echo $question['id'];?>]" value="1" <?php echo $mgrrate;?> onclick="checkparenttd(this)" >Manager Ratings</div>							
							<div class="comments_div_fiel" style="width:45%;"> <input type="checkbox" class ="empcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> onclick="checkmgrparenttd(this)">Employee Comments</div>
							<div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php echo $empratcheck;?> onclick="checkmgrparenttd(this)">Employee Ratings</div>
						</td>
						<?php sapp_PerformanceHelper::check_manager_selected_Qs($check,$empratcheck,$empcmntcheck,$question['id']); ?>
					</tr>
				<?php
				} 
				?>		 
			</table>
			<input type="hidden" id="appraisalid" name="appraisalid" value="<?php echo $appraisalid;?>">
			<input type="hidden" id="initializeflag" name="initializeflag" value="">
			<input type="hidden" id="initializestep" name="initializestep" value="<?php echo $initializationdata['initialize_status'];?>">
			<input type="hidden" id="questioncount" name="questioncount" value="<?php echo sizeof($questionarray);?>">
		</div>
	</div>		
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
			$('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
			
			$('#selectall').click(function(event) {  
				$('.checkallcls').prop('checked',$(this).prop('checked'));
				if(this.checked) { 
					$('.checkallcls').each(function() { 
						var id = $(this).attr('ques_id'); 
						appendmgrcheckboxtext(id);                
					});
				}else
				{
					//$('.mgrcmntcls').prop('checked',false);
					//$('.empcmntcls').prop('checked',false);
					//$('.mgrratingcls').prop('checked',false);
					//$('.empratingcls').prop('checked',false);
					$("tr[id^=hiddentr_]").remove();
					appendheighttodiv(2);
				}          
			});

			$('#mgrcmnt').click(function(event) {  //on click 
				
					if(this.checked) { 
						$('.checkallcls').each(function() { 
							var id = $(this).attr('ques_id'); 
							if(this.checked)
							{
								$("#mgrcmnt_"+id).prop('checked', true);
							}							
							appendcheckboxtext(id);                
						});
					}else
					{
						$('.mgrcmntcls').prop('checked',$(this).prop('checked'));					
						$("tr[id^=hiddentr_]").remove();
						appendheighttodiv(1);
					}
			});

			$('#mgrrating').click(function(event) {  //on click 
				
					if(this.checked) { 
						$('.checkallcls').each(function() { 
							var id = $(this).attr('ques_id'); 
							if(this.checked)
							{
								$("#mgrrating_"+id).prop('checked', true);
							}							
							appendcheckboxtext(id);                
						});
					}else
					{
						$('.mgrratingcls').prop('checked',$(this).prop('checked'));
						$("tr[id^=hiddentr_]").remove();
						appendheighttodiv(1);
					}
			});			
			
			$('#empcmnt').click(function(event) {  //on click 
				
					if(this.checked) { 
						$('.checkallcls').each(function() { 
							var id = $(this).attr('ques_id'); 
							if(this.checked)
							{
								$("#empcmnt_"+id).prop('checked', true);
							}								
							appendmgrcheckboxtext(id);                
						});
					}else
					{
						$('.empcmntcls').prop('checked',$(this).prop('checked'));
						$("tr[id^=hiddentr_]").remove();
						appendheighttodiv(2);
					}
			});
			$('#empratings').click(function(event) {  //on click
				
					if(this.checked) { 
						$('.checkallcls').each(function() { 
							var id = $(this).attr('ques_id'); 
							if(this.checked)
							{
								$("#empratings_"+id).prop('checked', true);
							}									
							appendmgrcheckboxtext(id); 
						});
					}else
					{
						$('.empratingcls').prop('checked',$(this).prop('checked'));					
						$("tr[id^=hiddentr_]").remove();
						appendheighttodiv(2);
					} 
			 
			});
			
			$('.checkallcls').click(function(event) {  //on click
				if(!$(this).prop('checked'))
				{
					// $('.empratingcls').prop('checked',$(this).prop('checked'));
					// $('.empratingcls').prop('checked',$(this).prop('checked'));
					// $('.empratingcls').prop('checked',$(this).prop('checked'));
				} 
			 
			});
			<?php if(empty($checkArr)) { ?>
				$('#selectall').prop('checked',true);
				$('#mgrcmnt').prop('checked',true);
				$('#mgrrating').prop('checked',true);
				$('#empcmnt').prop('checked',true);
				$('#empratings').prop('checked',true);
			<?php }?>

			checkparentclass();
		});

	</script>
<?php 					
    }
    
	public static function manager_questions_privileges_view($questionarray,$appraisalid,$checkArr,$initializationdata)
    {
?>     
        
            <ul class="tabs_all_button"  style="margin-left: 0px;"><li id="selecteddiv" class="active" style="cursor: default;">Configured Questions</li></ul>
            <div id="questiondiv" class="qstnscrldiv"> 
            <div class="total-form-controller">		
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="clear: both; margin-top: 0px; width: 100%;" class="requisition-table employee_appraisal-table">
                    <!---<tr>
                        <th class="question_field">Question</th>
                        <th class="field_width">
							<div class="comments_div_fiel">Manager Comments</div>
							<div class="comments_div_fiel">Manager Ratings</div>
							<div class="comments_div_fiel">Employee Comments</div>
							<div class="comments_div_fiel">Employee Ratings</div>							
                        </th>
                    </tr>-->
<?php 
            foreach($questionarray as $key => $question)
            {
                $check = '';  $mgrcheck = '';  $mgrrate = '';  $empratcheck = '';  $empcmntcheck = '';
                if(!empty($checkArr))
                {
                    if(array_key_exists($question['id'], $checkArr))
                    {
                        $check = 'checked';
                        $checkid = $checkArr[$question['id']];
                        for($i=0;$i<sizeof($checkid);$i++)
                        {
                            if($checkid['MC'] == 1)
                                $mgrcheck = 'checked';
                            if($checkid['MR'] == 1)
                                $mgrrate = 'checked';		 
                            if($checkid['ER'] == 1)
                                $empratcheck = 'checked';
                            if($checkid['EC'] == 1)
                                $empcmntcheck = 'checked';		 		 
                       }			 			   
                    } 
                }  
?>
                    <tr id="questiontr_<?php echo $question['id'];?>">				   
                        <td class="question_field" id="queshtml_<?php echo $question['id'];?>">
                        <div>
				   			<span class="appri_ques"><?php echo $question['question']; ?></span>
				   			<span class="appri_desc"><?php echo $question['description']; ?></span>
				   		</div>
                        </td>
                        <td class="field_width">
							<div class="comments_div_fiel"><input type="checkbox" class ="mgrcmntcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrcmnt_<?php echo $question['id'];?>" name="mgrcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $mgrcheck;?> onclick="checkparenttd(this)" disabled>Manager Comments</div>
							<div class="comments_div_fiel"><input type="checkbox" class ="mgrratingcls qprivileges_<?php echo $question['id'];?>" ques_id ="<?php echo $question['id'];?>" id="mgrrating_<?php echo $question['id'];?>" name="mgrrating[<?php echo $question['id'];?>]" value="1" <?php echo $mgrrate;?> onclick="checkparenttd(this)" disabled>Manager Ratings</div>						
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empcmntcls" ques_id ="<?php echo $question['id'];?>" id="empcmnt_<?php echo $question['id'];?>" name="empcmnt[<?php echo $question['id'];?>]" value="1"  <?php echo $empcmntcheck;?> disabled >Employee Comments</div>
                            <div class="comments_div_fiel"> <input type="checkbox" class ="empratingcls" ques_id ="<?php echo $question['id'];?>" id="empratings_<?php echo $question['id'];?>" name="empratings[<?php echo $question['id'];?>]" value="1"  <?php echo $empratcheck;?> disabled >Employee Ratings</div>
                        </td>				  	
                    </tr>
<?php
            } 
?>	    				 
                </table>
                <input type="hidden" id="appraisalid" name="appraisalid" value="<?php echo $appraisalid;?>">
                <input type="hidden" id="initializeflag" name="initializeflag" value="">
                <input type="hidden" id="initializestep" name="initializestep" value="<?php echo $initializationdata['initialize_status'];?>">
            </div>
        </div>
        <div class="new-form-ui-submit" id="qssubmitdiv">
            <button onclick="window.location.href='<?php echo BASE_URL;?>appraisalinit';" type="button" id="Cancel" name="Cancel">Cancel</button>
        </div>	
        <div class="clear"></div>
    </div>		
    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#questiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});
            $('#hiddenquestiondiv').alternateScroll({ 'horizontal-bar-class': 'styled-h-bar'});										
        });
    </script>
<?php 					
    }
    
		public static function check_manager_selected_Qs($check,$empratcheck,$empcmntcheck,$id)
	    {
	    	if($check == 'checked' || $empratcheck == 'checked' || $empcmntcheck == 'checked')
	    	{
?>	    
				<script>
						var id = '<?php echo $id;?>';
						appendmgrcheckboxtext(id);
				</script>		
	    		
<?php    	
	   		}
	   }
	   
		public static function manager_question_div($moduleflag) 
			   {
		?>
			
			<div id="qspopupdiv" style="display: none; ">	
				
					 <div class="total-form-controller" >	
					 <div id="successdiv" style="display:none;">
						<div class='ml-alert-1-success'>
						<div class='style-1-icon success'></div>
							Question added succesfully
					  </div>
					 </div>	
					 <div id="error_message" style="display:none;"></div>	
					  <div id="contentdiv"> 
					 		<div class="new-form-ui ">				  
					  			<label class="required">Parameter</label>
								<div class="division">
									<select id="category_id" name="category_id" >
										<option value="">Select Parameter</option>
									</select>
								</div>
							</div>
							
							<div class="new-form-ui ">
				            	<label class="required">Question</label>
				            	<div class="division">
									<input type="text" onkeyup="validatequestionname(this)" onblur="validatequestionname(this)" name="question_id" id="question_id" value="" maxlength="100">                            
								</div>
				        	</div>
				        	
				        	<div class="new-form-ui textareaheight">
				            <label class="">Description </label>
					            <div class="division">
									<textarea name="description" id="description"></textarea>
							    </div>
				        	</div>
				        	<input type="hidden" id="moduleflag" name="moduleflag" value="<?php echo $moduleflag;?>">
				        	
				        	<div class="new-form-ui-submit"  >
						
							<input type="button" value="Save" id="submitqs" name="submitqs" onclick="savemanagerquestions()"/>																	
							<button name="Cancel" id="Cancel" type="button" onclick="closeqspopup()">Cancel</button>
							
							</div>
					 </div>  
					 </div>
				 </div>
<?php
   			}
   			
	public static function display_ratings_div($ratingtype,$ratingsarr) 
			   {
			   	if($ratingtype == 1)
			   	{
			   	 	$ratingsstar = 5;
			   	 	$ratingclass = "rating_star_class rating_star_";
			   	 	$ratingtextclass = "rating_text_";
			   	} 
			   	else
			   	{
			   	 	$ratingsstar = 10;
			   	 	$ratingclass = "rating-star-class rating_star-";
			   	 	$ratingtextclass = "rating_text-";
			   	} 
			   	
			   	 ?>
			   	 <div id="ratingsdiv" class="ratings_div" style="display: none;">
	   <?php 	
			   	 for($i=0;$i<$ratingsstar;$i++)
			   	 {
		?>
					<div class="ratings_block">
						<span id="ratingstar_<?php echo $i+1;?>" class="<?php echo $ratingclass.($i+1);?>"><?php echo $i+1; ?></span>
						<span id="ratingtext_<?php echo $i+1;?>" class="<?php echo $ratingtextclass.($i+1);?>"><?php echo $ratingsarr[$i]['rating_text'];?> </span>
                        </div>
		<?php
			   	 }	
	   ?>
	   				
	   				</div>		   	 
   		<?php 	}
   		
	public static function update_QsParmas_Allemps($questions,$categoryids='')
		    {
		    		$auth = Zend_Auth::getInstance();
			     	if($auth->hasIdentity()){
								$loginUserId = $auth->getStorage()->read()->id;
					}
		    		$appraisalQsModel = new Default_Model_Appraisalquestions();
		    		$appraisalCategoryModel = new Default_Model_Appraisalcategory();
		    		
		    		if($questions!='')
		    		{
			    		$QsdataArr = array('isused'=>1, 
								  'modifiedby'=>$loginUserId,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						$Qswhere = " id IN($questions) ";
						$QsId = $appraisalQsModel->SaveorUpdateAppraisalQuestionData($QsdataArr, $Qswhere);
		    		}
					
					if($categoryids!='')
					{
						$CatdataArr = array('isused'=>1, 
								  'modifiedby'=>$loginUserId,
								  'modifieddate'=>gmdate("Y-m-d H:i:s")
						);
						$Catwhere = " id IN($categoryids) ";
						$CatId = $appraisalCategoryModel->SaveorUpdateAppraisalCategoryData($CatdataArr, $Catwhere);
					}
		    		
	?>	    
	<?php    	
	   }
	   
	   
		public static function skills_div($key, $emp_skills = NULL) 
			   {
		?>
			<script type="text/javascript">
			function appendskillsdata(inc_val,inc_text)
			{
				var ratingsmin = $("#ratingsmin").val();
				var ratingsmax = $("#ratingsmax").val();
				var selectedskillsval = $("#selectedskills").val();
				var key = '<?php echo $key;?>';
				var newselectedskillsval = '';
				if(selectedskillsval)
					newselectedskillsval = selectedskillsval+','+inc_val;
				else
					newselectedskillsval = inc_val;
	            var content = "<tr id='idtr_skill_"+inc_val+"' class='cls_skillrow'>";
	            content += "<td><select class='cls_sel_skills' name='sel_skill[]' id='idsel_skill_"+inc_val+"' ><option value="+inc_val+">"+inc_text+"</option></select><div class='hide_select'></div>";
	            content += "<input tabindex='-1' class='app_req_field_"+key+" cls_srating_"+key+"' type='hidden' id='id_emp_skills_"+inc_val+"' name='emp_skills[]' value='"+inc_val+"' /></td>";
	            content += "<td><input tabindex='-1' class='app_req_field_"+key+" cls_srating_"+key+"' type='hidden' id='skill_rating_"+inc_val+"' name='skill_rating[]' value='' /><div onclick='removeValidationMessage(this)' class='rateit' data-rateit-backingfld='#skill_rating_"+inc_val+"' data-rateit-step='1' data-rateit-resetable='false' data-rateit-min='"+ratingsmin+"' data-rateit-max='"+ratingsmax+"'></div></td>";            
	            content += "<td><span class='cls_close_skill' onclick = delete_skill('"+inc_val+"')>Close</span></td>";
	            content += "</tr>";
	            
	            $('#idskill_table').append(content);
	            $("#selectedskills").val(newselectedskillsval);
	            $('#idsel_skill_'+inc_val).select2();
	            $('div.rateit, span.rateit').rateit();
	            if($("#idskills_norows").is(":visible"))
	            	$('#idskills_norows').hide();
			}

			
			function saveskills(flag)
			{
				if(flag==1)
				{
					 $('.errors').remove(); 
					 if($.trim($('#skills_multi').val()) == '')
				      {
						// To place error messages after Add Link
				         $('#s2id_skills_multi').after("<span class='errors' id='errors-skills_multi'>Please select skill.</span>");
				      }
				      else 
				      {
				           $("#skills_multi option:selected").each(function () {
				        	   var $this = $(this);
				        	   if ($this.length) {
				        	    var selText = $this.text();
				        	    var selValue = $this.val();
				        	    appendskillsdata(selValue,selText);
				        	   }
				        	});
				           	$("#contentdiv").hide();
							$("#contentdiv_multi").hide();
				        	$("#successdiv_multi").show();
							$(".or_css").hide();
							$("#crt_skill").hide();
				        	$("#skills_multi").html('');
				        	setTimeout(function(){
				        		$('#skillsdiv').dialog('close');
				        	},3000);	
				
				      }
					
				}else
				{
					$('#errors-skills_multi').remove(); 
					$('#s2id_skills_multi').off("blur");
					var skillValue = $('#skill_name').val();
					var description = $('#description').val();
					$("#skills_multi").html('');
				  	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!+]+$/;
				  	$('#errors-skill_name').remove();
				  	if(skillValue == '')
				  	{
				  		$('#skill_name').parent().append("<span class='errors' id='errors-skill_name'>Please enter skill.</span>");
				  	}		
				  	else if(!re.test(skillValue))
				  	{
				  		$('#skill_name').parent().append("<span class='errors' id='errors-skill_name'>Please enter valid skill.</span>");
				  	}
				  	else
				  	{
						skillValue = encodeURIComponent(skillValue);
						description = encodeURIComponent(description);	
				  		$.ajax({
					     	url: base_url+"/appraisalskills/saveskillspopup/format/json",
					     	type : 'POST',	
							data : 'skillsval='+skillValue+'&description='+description,
							dataType: 'json',
							beforeSend: function () {
								$.blockUI({ width:'50px',message: $("#spinner").html() });
							},
							success : function(response){	
								$.unblockUI();
								if(response['msg'] == 'success')
								{
									
									appendskillsdata(response['id'],response['skills']);
									$("#contentdiv").hide();
									$("#contentdiv_multi").hide();
									$(".or_css").hide();
									$("#crt_skill").hide();
						        	$("#successdiv").show();
						        	setTimeout(function(){
						        		$('#skillsdiv').dialog('close');
						        	},3000);
									
								}else
								{
									$('#skill_name').parent().append("<span class='errors' id='errors-skill_name'>"+response['msg']+"</span>");
								}	
							}
						});
				  	}
					
				}	
				
			}

			
			function getskilldata()
			  {
				  apply_select2();
				  /*$('#s2id_skills_multi').on("blur", function(){
			    		validateskillname(this,1);
					});*/
				  $("#skill_name").val('');
				  $("#description").val('');
				  $("#skills_multi").select2('data', null)
				  $("#errors-skill_name").html('');
				  $(".errors").remove();
				  $("#errors-skills_multi").remove();
				  $("#successdiv").hide();
				  $("#successdiv_multi").hide();
				  $("#contentdiv").show();
				  $("#contentdiv_multi").show();
				  $(".or_css").show();
				  $("#crt_skill").show();
				  var skillsval = $.trim($('#selectedskills').val());
					  $.ajax({
				         	url: base_url+"/appraisalskills/getappraisalskills/format/json",
				         	type : 'POST',
				         	data : 'skillsval='+skillsval,	
							dataType: 'json',
							success : function(response){	
								if(response.result=='success')
								{
									$("#skills_multi").html(response.data);
								}else
								{
									$("#skills_multi").parent().append("<span class='errors' id='errors-select_multi'>"+response.data+"</span>");
								}	
							    
							}
						});
				 
				    var title = "Add Skills"; 
				    $("#skillsdiv").dialog({
				       		draggable:false, 
							resizable: false,
						    width:500,
							title: title,
						    modal: true 
						    });
			  }
			  	
				 function validateskillname(ele,flag)
				  {
				  	var elementid = $(ele).attr('id');
				  	var skillValue = $(ele).val();
				  	var re = /^[a-zA-Z0-9\- ?'.,\/#@$&*()!+]+$/;
				  	$('#errors-'+elementid).remove();
				  	if(flag==1)
				  	{
				  		 elementid = elementid.substring(5);
				  		$('#errors-'+elementid).remove();
					  	  if($.trim($('#'+elementid).val()) == '')
					      {
					
					         // To place error messages after Add Link
					         $('#'+elementid).after("<span class='errors' id='errors-"+elementid+"'>Please select skill.</span>");
					      }
					      else 
					      {
					          $('#errors-'+elementid).remove();   
					
					      }
				  	}else
				  	{  	  	
						  	if(skillValue == '')
						  	{
						  		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter skill.</span>");
						  	}		
						  	else if(!re.test(skillValue))
						  	{
						  		$(ele).parent().append("<span class='errors' id='errors-"+elementid+"'>Please enter valid skill.</span>");
						  	}
						  	else
						  	{
						  		$('#errors-'+elementid).remove();
						  	}
				  	}
				  }
				 </script>
			<div id="skillsdiv" style="display: none; ">	
					<div class="total-form-controller" >	
					 <div id="successdiv_multi" style="display:none;">
						<div class='ml-alert-1-success'>
						<div class='style-1-icon success'></div>
							Skills selected succesfully
					  </div>
					 </div>	
					 <div id="error_message_multi" style="display:none;"></div>	
					  <div id="contentdiv_multi"> 
					 		<div class="new-form-ui-multi  ">				  
					  			<label class="required">Skill</label>
								<div class="division">
									<select  id="skills_multi" name="skills_multi" multiple="multiple" class="skills">
									</select>
								</div>
							</div>
							<input type="hidden" name="selectedskills" id="selectedskills" value="<?php echo $emp_skills; ?>" />
				        	<div class="new-form-ui-submit" style="width: auto; clear: none; margin-top: 37px;">
						
							<input type="button" value="Select" id="submitqs" name="submitqs" onclick="saveskills(1)"/>																	
							<button name="Cancel" id="Cancel" type="button" onclick="$('#skillsdiv').dialog('close');">Cancel</button>
							
							</div>
					 </div>  
					 </div>
					<div class="or_css">(or)</div>
					<div class="label-title" id="crt_skill">Create Skill</div>
					 <div class="total-form-controller" style="clear: both;">	
					 <div id="successdiv" style="display:none;">
						<div class='ml-alert-1-success'>
						<div class='style-1-icon success'></div>
							Skill added succesfully
					  </div>
					 </div>	
					 <div id="error_message" style="display:none;"></div>	
					  <div id="contentdiv"> 
					 		<div class="new-form-ui ">				  
					  			<label class="required">Skill</label>
								<div class="division">
									<input type="text" maxlength="30" value="" id="skill_name" name="skill_name" onkeyup="validateskillname(this,2)" onblur="validateskillname(this,2)">
								</div>
							</div>
							
				        	<div class="new-form-ui textareaheight">
				            <label class="">Description </label>
					            <div class="division">
									<textarea name="description" id="description"></textarea>
							    </div>
				        	</div>
				        	
				        	<div class="new-form-ui-submit"   style="width: auto; clear: none; margin-top: 48px;">
						
							<input type="button" value="Save" id="submitqs" name="submitqs" onclick="saveskills(2)"/>																	
							<button name="Cancel" id="Cancel" type="button" onclick="$('#skillsdiv').dialog('close');">Cancel</button>
							
							</div>
					 </div>  
					 </div>
				 </div>
			
<?php
   			}
   			
   			public static function calculateheight($employeeArr) 
			   {
			   	$height = '180px';
			    if($employeeArr>0)
				{
					if($employeeArr > 2)
						$height = '350px';
				}
				return $height;
 
			   }
			   
			   
			public static function appraisal_status_dropdown($statusArr,$flag) 
			   {
			   	
			   	 ?>
			   	 	<div class="new-form-ui" id="statusdiv">
		            <label >Appraisal Status <img class="tooltip" title="Select business unit and check the status" src="<?php echo DOMAIN.'public/media/';?>images/help.png"></label>
					<button type="button" id="appraisalstatusclear" name="appraisalstatusclear" class="inputclear" style="display:none;"  onclick="clearappstatus('<?php echo $flag;?>')">Clear</button>
		            <div class="division">
							<select  id="appraisal_status" name="appraisal_status" onchange="displappstatus('<?php echo $flag;?>',this.value)">
		        				<option value="">Select Appraisal Status</option>
			        			<?php 
			        					foreach($statusArr as $key => $val)
			        					{
			        			?>
			        					<option value="<?php echo $key;?>"><?php echo $val;?></option>
			        			<?php 
			        					}
			        			?>
							</select>
					</div>
		        	</div>		   	 
   		<?php 	}
   		
	public static function manager_appraisal_status_dropdown($flag,$loginuserGroup = '') 
			   {					
			   	 ?>
			   	 	<div class="new-form-ui" id="statusdiv">
		            <label >Manager Appraisal Status 
					<?php if(isset($loginuserGroup) && $loginuserGroup != HR_GROUP){ ?>
					<img class="tooltip" title="Select business unit and check the status" src="<?php echo DOMAIN.'public/media/';?>images/help.png">
					<?php } ?>
					</label>
					<button type="button" id="appraisalstatusclear" name="appraisalstatusclear" class="inputclear" style="display:none;"  onclick="clearappstatus('<?php echo $flag;?>')">Clear</button>					
		            <div class="division">
							<select  id="appraisal_status" name="appraisal_status" onchange="displmanagerappstatus('<?php echo $flag;?>',this.value)">
		        						<option value="">Select Appraisal Status</option>
			        					<option value="2">Completed</option>
			        					<option value="3">Not Completed</option>
							</select>
					</div>
		        	</div>		   	 
   		<?php 	}
}			   
?>

