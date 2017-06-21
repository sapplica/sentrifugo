<?php
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

/**
 * Performancesteps View Helper
 *
 * A View Helper that helps in performance appraisal initialisation.
 *
 *
 */
class Zend_View_Helper_Performancesteps extends Zend_View_Helper_Abstract 
{
    public function performancesteps($perfArray)
    {
		$request = Zend_Controller_Front::getInstance();
        $controllerName = $request->getRequest()->getControllerName();
        $actionName = $request->getRequest()->getActionName();
        $step1_link = "";
        $step2_link = '';
        $step3_link = '';
        if($perfArray['appraisalid'] != '')
        {
        	if($perfArray['context'] == 'edit')
        	{            
	            $step1_link = BASE_URL."appraisalinit/edit/id/".$perfArray['appraisalid'];
	            $step2_link = BASE_URL."appraisalinit/confmanagers/i/".sapp_Global::_encrypt($perfArray['appraisalid']);
	            if($perfArray['step2status'] == 'Completed')
	            	$step3_link = BASE_URL."appraisalinit/assigngroups/i/".sapp_Global::_encrypt($perfArray['appraisalid']);
        	}else
        	{
        		$step1_link = BASE_URL."appraisalinit/view/id/".$perfArray['appraisalid'];
            	$step2_link = BASE_URL."appraisalinit/viewconfmanagers/i/".sapp_Global::_encrypt($perfArray['appraisalid']);
            	if($perfArray['step2status'] == 'Completed')
            		$step3_link = BASE_URL."appraisalinit/viewassigngroups/i/".sapp_Global::_encrypt($perfArray['appraisalid']);
        	}   
        }
        
        
       
        
?>
<script type="text/javascript" src="<?php echo MEDIA_PATH;?>js/pa.js"></script>
<div class="per_steps">
        <ul class="toggle_ul">
            <li id="initstep_1" class="step_1 incomplete"  ><div class="step_round">Step<span class="num_txt">1</span></div>
            	<div class="left_tab_content">
                	<div class="completed_icon"></div>
                	<h3 class="tab_title">Initialization</h3>
                    <span class="tab_txt">Initialize appraisal for a department or a business unit and enable to managers or employees</span>
                    <?php if($perfArray['step1status'] == 'Completed') { ?>
                    	<div class="status_txt complete_status"><?php echo $perfArray['step1status'];?></div>
                    <?php } else { ?>
                    	<div class="status_txt in_progress"><?php echo $perfArray['step1status'];?></div>	
                    <?php } ?>
                </div>
            
            </li>

            <li id="initstep_2" class="step_2 incomplete"><div class="step_round">Step<span class="num_txt">2</span></div>
          		 <div class="left_tab_content">
                 	<div class="completed_icon"></div>
                	<h3 class="tab_title">Configure Line Managers</h3>
                    <span class="tab_txt">Configure line managers by using the existing organization hierarchy or by assigning line managers to employees manually </span>
                    <?php if($perfArray['step2status'] == 'Completed') { ?>
                    	<div class="status_txt complete_status"><?php echo $perfArray['step2status'];?></div>
                    <?php } else { ?>
                    	<div class="status_txt in_progress"><?php echo $perfArray['step2status'];?></div>	
                    <?php } ?>	
                </div>
            </li>
            <li id="initstep_3" class="step_3 incomplete"><div class="step_round">Step<span class="num_txt">3</span></div>
           		<div class="left_tab_content">
                	<div class="completed_icon"></div>
                	<h3 class="tab_title">Configure Appraisal Parameters</h3>
                    <span class="tab_txt">Set the appraisal parameters for all the employees or for an employee group for a department or a business unit</span>
                    <?php if($perfArray['step3status'] == 'Completed') { ?>
                    	<div class="status_txt complete_status"><?php echo $perfArray['step3status'];?></div>
                    <?php } else { ?>
                    	<div class="status_txt in_progress"><?php echo $perfArray['step3status'];?></div>	
                    <?php } ?>
                </div>
            </li>

        </ul>
        <input type="hidden" id="ratingsflag" value="<?php echo ($perfArray['ratingsflag'] == 'false')?2:1;?>" >
		<?php if(isset($perfArray['appraisalid']) && $perfArray['appraisalid']!='') { 
				if($actionName!='view' && $actionName!='edit')
				 {
		?>
	        <div class="appdetails">
	        			<?php sapp_PerformanceHelper::displayappdetails($perfArray['appraisalid']);?>
	        </div>
        <?php }} ?>
        <?php if(isset($perfArray['ratingsflag']) && $perfArray['ratingsflag'] == 'false'){?>
        		<div class='ml-alert-1-error' style="clear: both;position: relative;top: 17px;width: 92%;">
						<div class='style-1-icon error'></div>
							Ratings not added for the appraisal. <a target ="_blank" href="<?php echo BASE_URL.'appraisalratings/add'?>" style="color:#A31414;text-decoration: none;font-weight: bold;">Click here</a> to configure ratings.
				</div>
        <?php }?>
        
    <script type="text/javascript" >
        $(document).ready(function(){
        	<?php 
            if($step1_link != '')
            {
            ?>
	            $('#initstep_1').click(function(){
	            	$.blockUI({ width:'50px',message: $("#spinner").html() });
	                window.location = '<?php echo $step1_link;?>';
	            });
            <?php 
    		}
            ?>
            <?php 
            if($step2_link != '')
            {
            ?>
                $('#initstep_2').click(function(){
                	$.blockUI({ width:'50px',message: $("#spinner").html() });
                    window.location = '<?php echo $step2_link;?>';
                });
            <?php 
            }
            if($step3_link != '')
            {
            ?>
                $('#initstep_3').click(function(){
                	$.blockUI({ width:'50px',message: $("#spinner").html() });
                    window.location = '<?php echo $step3_link;?>';
                });
            <?php 
            }
            ?>

			<?php 
					if($perfArray['step1status'] == 'Completed')
					{
			?>
						$("#initstep_1").removeClass('incomplete').addClass('complete');
			<?php 
    				}
			?>

			<?php 
					if($perfArray['step2status'] == 'Completed')
					{
			?>
						$("#initstep_2").removeClass('incomplete').addClass('complete');
			<?php 
    				}
			?>

			<?php 
					if($perfArray['step3status'] == 'Completed')
					{
			?>
						$("#initstep_3").removeClass('incomplete').addClass('complete');
			<?php 
    				}
			?>
			var currentpage = '<?php echo $perfArray['currentpage'];?>';
			$("#"+currentpage).addClass('active');


			<?php if($perfArray['currentpage']!='initstep_1'){?>
				<?php if($perfArray['context'] == 'edit'){?>
					  $(".breadcrumbs").append('<span class="arrows">&rsaquo;</span> <span>Edit</span>');	
				<?php } else { ?>
					  $(".breadcrumbs").append('<span class="arrows">&rsaquo;</span> <span>View</span>');
			   <?php } ?>		  	
            <?php }?>

            <?php  if($perfArray['appraisalid'] == ''){?>
			            // $('#initstep_1').click(function(){
							// jAlert("Configure appraisal details.");
			            // });
			            
			            $('#initstep_2').click(function(){
			            	jAlert("Complete step 1 to configure step 2.");
			            });

						$('#initstep_3').click(function(){
							jAlert("Complete step 2 to configure step 3.");
			            });
            		
            <?php }?>
        });
    </script>
<?php 
        
    }
}//end of class
?>