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
 * Breadcrumbs View Helper
 *
 * A View Helper that creates the menu
 *
 *
 */
class Zend_View_Helper_Employeedetails extends Zend_View_Helper_Abstract {


	public  function employeedetails($emparr,$conText,$userId)
	{
		
		
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		
		$loggedinuser = $data['id'];
		$group_id = $data['group_id'];
		$loggedinUserRole=$data['emprole'];
		$empdata = '';
		$employeetabsStr = '';
        $empdata  ='<div class="ml-alert-1-success" id="empdetailsmsgdiv" style="display:none;">';
		$empdata .='<div class="style-1-icon success" style="display:block;"></div>';
		$empdata .='<div id="successtext"></div>';
		$empdata .='</div>';
		$empdata .= '<div class="all-bg-ctrl">';
		$employeeModal = new Default_Model_Employee();
		$employessunderEmpId = $employeeModal->getEmployeesUnderRM($userId);
		if(!empty($employessunderEmpId))
		$empdata .= '<input type="hidden" value="true" id="hasteam" name="hasteam" />';
		else
		$empdata .= '<input type="hidden" value="false" id="hasteam" name="hasteam" />';
		if($conText == 'edit' || $conText == 'view')
		{
		 
			//If the user has BG status as "Yet to start" then we should enable the link....
			$usersModel = new Default_Model_Users();
			$bgstatusArr = $usersModel->getBGstatus($userId);
			if(!empty($bgstatusArr)&& isset($bgstatusArr) && $bgstatusArr[0]['group_id'] != MANAGEMENT_GROUP)
			{
				if($bgstatusArr[0]['isactive'] == 1)
				$empdata .= '<div id="hrbgchecklink" style="display:none;" class="action-to-page"><a href="'.BASE_URL.'/empscreening/checkscreeningstatus/empid/'.$userId.'">Send for Background Check</a></div>';
			}
		
		}
		/* if($conText != 'mydetails')
		{
			$empdata .= '<div class="back-to-page"><input type="button" value="Back" name="Back" onclick="gobacktocontroller(\''.$conText.'\');"></div>';
		}
		 */
		$empdata .= '<div class="emp-screen-view">';
		$empdata .= '<div class="display-img-div" id="displayimg" >';
		$empdata .= '<div class="employee-pic-emp">';
	   if($loggedinUserRole!=''&& $loggedinUserRole == '4')
	   {
		    $empdata .=	'<div class="chg-img_profile">';
            if($emparr['profileimg']!=''){
			$empdata .=	'<img id="blah" class="imgbrdr"  src="'.DOMAIN.("public/uploads/profile/").$emparr['profileimg'].'" onerror="this.src=\''.DOMAIN.'public/media/images/default-profile-pic.jpg\'"/>';
			 }
			else{
			$empdata .=	'<img id="blah" class="imgbrdr"  src="'.DOMAIN.'public/media/images/employee-deafult-pic.jpg" />';
			}
			$empdata.='</div>';
			?>
		<!-- Start Div for updating photoupload-->
   	    <!--End Update div -->	
		 <input type="hidden" id="uploadimagepathedit" name="uploadimagepath" value="<?php echo $emparr['profileimg'];?>"/>
		<input type="hidden" name="profile_image" value=""/>
	    <!-- End Photo Upload -->
	  <!-- <div class="uploaderror_profile" style="display:none;"></div>-->
			  
      <?php
		}	
     else
		{
				if($emparr['profileimg']!='')
				{
				$empdata .=	'<img id="userImage" src="'.DOMAIN.("public/uploads/profile/").$emparr['profileimg'].'" onerror="this.src=\''.DOMAIN.'public/media/images/default-profile-pic.jpg\'"/>';
				}
				else
				 {
				$empdata .=	'<img id="userImage" src="'.DOMAIN.'public/media/images/employee-deafult-pic.jpg" />';
				 }
		}

		/**
		** Active/inactve buttons 18-03-2015
		** should not be available in my details page
		** should not be available for org head inHR > Emp page
		** should be available for all employee, Manager, HR, Sys Adm employees, for Super Admin, Management and HR
		**/
		if($conText != 'mydetails' && $emparr['is_orghead'] != 1)
		{
				if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $group_id == MANAGEMENT_GROUP)//for activate inactivate user
				{
					$sel_act = $sel_dact = "";
					if($emparr['isactive'] < 2 && $emparr['emptemplock'] == 0)
					{
						if($emparr['isactive'] == 1)
						{
							$sel_act = "selected";
						}
						else if($emparr['isactive'] == 0)
						{
							$sel_dact = "selected";
						}
						/** disable the buttons for organization head **/
						
							if($loggedinUserRole!=''&& $loggedinUserRole == '4')
							{
										  
											$empdata .= '<div class="left_div" >
														<span class="uploadbut uploadbutsel" id="upload_custom_div_profile" style="display:block;"> Edit Profile Photo</span>
														<div id="loaderimgprofileedit" style="display:none;"><img src="'.DOMAIN.'public/media/images/loaderwhite_21X21.gif" style="width:21px; height: 21px; float: none; "/></div>
														</div> 
													<div id="profile_edit" style="display:none; margin: 0 auto; width: 80px;">
							<div class="mrgetop10 fltleft">
							<input type="button" class="submit_bg" value="Update" onclick="empprofileImageSave('.$emparr['id'].');" /></div>
									</div>
									 <div class="uploaderror_profile" style="display:none;"></div>
									<p class="field switch"><label class="cb-enable  '.$sel_act.'"><span>Active</span></label><label class="cb-disable '.$sel_dact.'"><span>Inactive</span></label> </p>';
							}else
							{
								$empdata .= '<p class="field switch"><label class="cb-enable  '.$sel_act.'"><span>Active</span></label><label class="cb-disable '.$sel_dact.'"><span>Inactive</span></label> </p>';
							}	
										
						if($sel_act == "selected")
						{
							$empdata .= "
										<script type='text/javascript' language='javascript'>
											$('.cb-disable').click(function(){              
												makeActiveInactive('inactive','".$emparr['id']."');
											});
										</script> ";
						}
						else if($sel_dact == "selected")
						{
							$empdata .= "
										<script type='text/javascript' language='javascript'>
											$('.cb-enable').click(function(){                
												makeActiveInactive('active','".$emparr['id']."');
											});
										</script> ";
						}
						
					}
					else if($emparr['isactive'] < 2 && $emparr['emptemplock'] == 1)
					{
						$sel_dact = "selected";$sel_act = "";
						$empdata .= '<p class="field switch"><label class="cb-enable  '.$sel_act.'"><span>Active</span></label><label class="cb-disable '.$sel_dact.'"><span>Inactive</span></label> </p>';
						$empdata .= "
									<script type='text/javascript' language='javascript'>
										$('.cb-enable').click(function(){                
											makeActiveInactive('active','".$emparr['id']."');
										});
										
									</script>   
									";
					}
					else
					{
						$sel_dact = "selected";$sel_act = "";
						$empdata .= '<p class="field switch"><label class="cb-enable  '.$sel_act.'"><span>Active</span></label><label class="cb-disable '.$sel_dact.'"><span>Inactive</span></label> </p>';
						$empdata .= "
									<script type='text/javascript' language='javascript'>
										$('.cb-enable,.cb-disable').click(function(){                
											makeActiveInactive('other','".$emparr['isactive']."');
										});
										
									</script>   
									";
					}
				}
		}
		
		$empdata .= '</div>';
		$empdata .=	'<div id="loaderimg" style="display:none; clear:both; margin:0 auto; text-align: center; width:100%;"><img src="'.DOMAIN.("public/media/images/loaderwhite_21X21.gif").'" style="width:21px; height: 21px; float: none; "/>';
		$empdata .= '</div>';
		$empdata .=	'</div>';

		$empdata .= '<div id="personalDetailsDiv">';

		/**
		** 18-03-2015
		** Change organization head should not be available in my details page
		**/
		if($conText != 'mydetails' && $emparr['is_orghead'] == '1') 
		{
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $group_id == MANAGEMENT_GROUP)//To see Change Line Manager link
			{
				$headicon = '<span class="org-head-div"><img title="Organization head" src="'.DOMAIN.("public/media/images/org-head.png").'" class="org-head-icon" /><a class="change_orgn_head" href="'.BASE_URL.("employee/changeorghead/orgid/".$emparr['user_id']).'">Change Organization Head</a></span>';
				//$changeorghead = '<a href="'.BASE_URL.("/employee/changeorghead/orgid/".$emparr['user_id']).'">Change Organization head</a>';
			}
			else 
			{
				$headicon = '';
			}
		}	
		else 
		{
			$headicon = '';
			//$changeorghead = '';
		}	
		if(isset($emparr['active_prefix']) && isset($emparr['prefix']) && $emparr['active_prefix'] == 1 && $emparr['prefix'] !='')
		$empdata .=	'<p><b>Employee Name <i>:</i></b><span class="emp-name-span bold-text">'.$emparr['prefix'].'.&nbsp;'.$emparr['userfullname'].'</span>'.$headicon.'</p> ';
		else
		$empdata .=	'<p><b>Employee Name <i>:</i></b><span class="emp-name-span bold-text">'.$emparr['userfullname'].'</span>'.$headicon.'</p>';
		$empdata .=	'<p><b>Employee Id <i>:</i></b><span class="emp-name-span" id ="spanempid">'.$emparr['employeeId'].'</span></p>';
		$empdata .=	'<p><b>Email Id <i>:</i></b><span><a href="javascript:void(0)">'.$emparr['emailaddress'].'</a></span></p>';
		if($emparr['contactnumber'] !='')
		{
			$empdata .=	'<p><b>Contact Number <i>:</i></b>';
			$empdata .= '<span id="contactnospan" >'.$emparr['contactnumber'].'</span>';
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $loggedinuser == $userId || $group_id == MANAGER_GROUP)
			{
				$empdata .= '<span class="number-edit"><input type="button" value="Update" id="editcontactnumber" name="Edit Number" onclick="opencontactnumberpopup(\''.$emparr['id'].'\',\'edit\',\''.$emparr['contactnumber'].'\');">';
				$empdata .= '</span>';
			}
			$empdata .= '</p>';
		}
		else
		{
			$empdata .=	'<p><b>Contact Number <i>:</i></b>';
			$empdata .= '<span id="contactnospan" ></span>';
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $loggedinuser == $userId || $group_id == MANAGER_GROUP)
			{
				$empdata .= '<span class="number-add"><input type="button" value="Add" id="addcontactnumber" name="Add Number" onclick="opencontactnumberpopup(\''.$emparr['id'].'\',\'add\',\'\');">';
				$empdata .= '</span>';
			}

			$empdata .= '</p>';
		}
		$empdata .= '</div>';
		$empdata .= '</div>';
		$empdata .= '<div id="employeeContainer"  style="display: none; overflow: auto;">
						<div class="heading">
							<a href="javascript:void(0)">
								<img src="'. DOMAIN.'public/media/images/close.png" name="" align="right" border="0" hspace="3" vspace="5" class="closeAttachPopup" style="margin: -24px 8px 0 0;"> 
							</a>
						</div>
						<iframe id="employeeCont" name="employeeCont" class="business_units_iframe" frameborder="0"></iframe>
					</div>';
		$empdata .= '</div>';

		echo $empdata;
		$employeetabsStr = $this->employeetabs($conText,$userId);
		echo $employeetabsStr;
	}
	public  function employeetabs($conText,$userId)
	{
		$tabHeightClass="";
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		

		if(defined('EMPTABCONFIGS')) 
		 $empOrganizationTabs = explode(",",EMPTABCONFIGS);

		$loggedinuser = $data['id'];		$group_id = $data['group_id'];

		if ($conText == "mydetails")
		{
			$tabHeightClass="mydetails-height";
		}
		else if ($conText == "edit" || $conText == "view")
		{
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $group_id == MANAGEMENT_GROUP)
			{
				$tabHeightClass="hr-employee-height";
			}
			else if($group_id == MANAGER_GROUP ||$group_id == EMPLOYEE_GROUP||$group_id == SYSTEMADMIN_GROUP)
			{
				$tabHeightClass="mydetails-height";
			}
		}

		$tabsHtml = '<div class="poc-ui-data-control" id="'.$tabHeightClass.'">
		<div class="left-block-ui-data">
		<div class="agency-ui">
		<ul>';
		if($conText == "edit")
		{
			
			//View all tabs with all privileges....	onclick - changeempeditscreen...
			
			$tabsHtml .= '<li  id="empdetails" onclick="changeeditscreen(\'employee\','.$userId .');">
				'.TAB_EMP_OFFICIAL.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("employeedocs", $empOrganizationTabs))
			$tabsHtml .= '<li id = "employeedocs" onclick="changemyempviewscreen(\'employeedocs\',\'index\','.$userId .');">'.TAB_EMP_DOCUMENTS.'<span class="beta_menu"></span></li>';
			
			if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_leaves" onclick="changeempeditscreen(\'empleaves\','.$userId .');">'.TAB_EMP_LEAVES.'</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_holidays", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_holidays" onclick="changeempeditscreen(\'empholidays\','.$userId .');">'.TAB_EMP_HOLIDAYS.'</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_salary" onclick="changeempeditscreen(\'empsalarydetails\','.$userId .');">'.TAB_EMP_SALARY.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempeditscreen(\'emppersonaldetails\','.$userId .');">'.TAB_EMP_PERSONAL.'</li>';

			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempeditscreen(\'empcommunicationdetails\','. $userId .');">'.TAB_EMP_CONTACT.'</li>
				';

			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_skills" onclick="changeempeditscreen(\'empskills\','.$userId .');">'.TAB_EMP_SKILLS.'</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempeditscreen(\'empjobhistory\','.$userId .');">'.TAB_EMP_JOB_HISTORY.'</li>';

			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .= '<li id= "experience_details" onclick="changeempeditscreen(\'experiencedetails\','.$userId .');">'.TAB_EMP_EXPERIENCE.'</li>';

			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "education_details" onclick="changeempeditscreen(\'educationdetails\','.$userId .');">'.TAB_EMP_EDUCATION.'</li>';

			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempeditscreen(\'trainingandcertificationdetails\','.$userId .');">'.TAB_EMP_TRAINING_CERTIFY.'</li>';

			if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
			$tabsHtml .= '<li id = "medical_claims" onclick="changeempeditscreen(\'medicalclaims\','. $userId .');">'.TAB_EMP_MEDICAL_CLAIMS.'</li>';

			if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "disabilitydetails" onclick="changeempeditscreen(\'disabilitydetails\','.$userId .');">'.TAB_EMP_DISABILITY.'</li>';

			if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "dependency_details" onclick="changeempeditscreen(\'dependencydetails\','.$userId .');">'.TAB_EMP_DEPENDENCY.'</li>';

			if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
			$tabsHtml .= '<li id="visadetails"onclick="changeempeditscreen(\'visaandimmigrationdetails\','.$userId .');">'.TAB_EMP_VISA_EMIGRATION.'</li>';

			if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
			$tabsHtml .= '<li id= "creditcarddetails" onclick="changeempeditscreen(\'creditcarddetails\','.$userId.');">'.TAB_EMP_CORPORATE_CARD.'</li>';

			if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
			$tabsHtml	.= '<li id="workeligibilitydetails" onclick="changeempeditscreen(\'workeligibilitydetails\','. $userId .');">'.TAB_EMP_WORK_ELIGIBILITY.'</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional" onclick="changeempeditscreen(\'empadditionaldetails\','.$userId .');">'.TAB_EMP_ADDITIONAL.'</li>';
			
			//if(!empty($empOrganizationTabs) && in_array("emp_performanceappraisal", $empOrganizationTabs))
			//$tabsHtml .= '<li id = "emp_performanceappraisal" onclick="changeempeditscreen(\'empperformanceappraisal\','.$userId .');">'.TAB_EMP_PERFORMANCE_APPRAISAL.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("emp_payslips", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_payslips" onclick="changeempeditscreen(\'emppayslips\','.$userId .');">'.TAB_EMP_PAY_SLIPS.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("emp_benifits", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_benifits" onclick="changeempeditscreen(\'empbenefits\','.$userId .');">'.TAB_EMP_BENEFITS.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("emp_renumeration", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_renumeration" onclick="changeempeditscreen(\'empremunerationdetails\','.$userId .');">'.TAB_EMP_REMUNERATION.'</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_security", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_security" onclick="changeempeditscreen(\'empsecuritycredentials\','.$userId .');">'.TAB_EMP_SECURITY_CREDENTIALS.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("assetdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id= "assetdetails" onclick="changeempeditscreen(\'assetdetails\','.$userId.');">'.TAB_EMP_ASSETS.'</li>';
		}
		else if($conText == "view")
		{
		
			if($group_id == HR_GROUP ||$group_id == MANAGEMENT_GROUP || $loggedinuser == SUPERADMIN)
			{
				
				$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'employee\','.$userId .');">
				'.TAB_EMP_OFFICIAL.'</li>';
				
				if(!empty($empOrganizationTabs) && in_array("employeedocs", $empOrganizationTabs))
				$tabsHtml .= '<li id = "employeedocs" onclick="changemyempviewscreen(\'employeedocs\',\'view\','.$userId .');">'.TAB_EMP_DOCUMENTS.'<span class="beta_menu"></span></li>';

				if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_leaves" onclick="changeempviewscreen(\'empleaves\','.$userId .');">'.TAB_EMP_LEAVES.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_holidays", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_holidays" onclick="changeempviewscreen(\'empholidays\','.$userId .');">'.TAB_EMP_HOLIDAYS.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_salary" onclick="changeempviewscreen(\'empsalarydetails\','.$userId .');">'.TAB_EMP_SALARY.'</li>';
								
				if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempviewscreen(\'emppersonaldetails\','.$userId .');">'.TAB_EMP_PERSONAL.'</li>';

				if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempviewscreen(\'empcommunicationdetails\','. $userId .');">'.TAB_EMP_CONTACT.'</li>
				';

				if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_skills" onclick="changeempviewscreen(\'empskills\','.$userId .');">'.TAB_EMP_SKILLS.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempviewscreen(\'empjobhistory\','.$userId .');">'.TAB_EMP_JOB_HISTORY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
				$tabsHtml .= '<li id= "experience_details" onclick="changeempviewscreen(\'experiencedetails\','.$userId .');">'.TAB_EMP_EXPERIENCE.'</li>';

				if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "education_details" onclick="changeempviewscreen(\'educationdetails\','.$userId .');">'.TAB_EMP_EDUCATION.'</li>';

				if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempviewscreen(\'trainingandcertificationdetails\','.$userId .');">'.TAB_EMP_TRAINING_CERTIFY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
				$tabsHtml .= '<li id = "medical_claims" onclick="changeempviewscreen(\'medicalclaims\','. $userId .');">'.TAB_EMP_MEDICAL_CLAIMS.'</li>';

				if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "disabilitydetails" onclick="changeempviewscreen(\'disabilitydetails\','.$userId .');">'.TAB_EMP_DISABILITY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "dependency_details" onclick="changeempviewscreen(\'dependencydetails\','.$userId .');">'.TAB_EMP_DEPENDENCY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
				$tabsHtml .= '<li id="visadetails" onclick="changeempviewscreen(\'visaandimmigrationdetails\','.$userId .');">'.TAB_EMP_VISA_EMIGRATION.'</li>';

				if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
				$tabsHtml .= '<li id= "creditcarddetails" onclick="changeempviewscreen(\'creditcarddetails\','.$userId.');">'.TAB_EMP_CORPORATE_CARD.'</li>';

				if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
				$tabsHtml	.= '<li id="workeligibilitydetails" onclick="changeempviewscreen(\'workeligibilitydetails\','. $userId .');">'.TAB_EMP_WORK_ELIGIBILITY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_additional" onclick="changeempviewscreen(\'empadditionaldetails\','.$userId .');">'.TAB_EMP_ADDITIONAL.'</li>';
				
				//if(!empty($empOrganizationTabs) && in_array("emp_performanceappraisal", $empOrganizationTabs))
				//$tabsHtml .= '<li id = "emp_performanceappraisal" onclick="changeempviewscreen(\'empperformanceappraisal\','.$userId .');">'.TAB_EMP_PERFORMANCE_APPRAISAL.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_payslips", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_payslips" onclick="changeempviewscreen(\'emppayslips\','.$userId .');">'.TAB_EMP_PAY_SLIPS.'</li>';
				
				if(!empty($empOrganizationTabs) && in_array("emp_benifits", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_benifits" onclick="changeempviewscreen(\'empbenefits\','.$userId .');">'.TAB_EMP_BENEFITS.'</li>';
				
				if(!empty($empOrganizationTabs) && in_array("emp_renumeration", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_renumeration" onclick="changeempviewscreen(\'empremunerationdetails\','.$userId .');">'.TAB_EMP_REMUNERATION.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_security", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_security" onclick="changeempviewscreen(\'empsecuritycredentials\','.$userId .');">'.TAB_EMP_SECURITY_CREDENTIALS.'</li>';
				
				if(!empty($empOrganizationTabs) && in_array("assetdetails", $empOrganizationTabs))
					$tabsHtml .= '<li id= "assetdetails" onclick="changeempviewscreen(\'assetdetails\','.$userId.');">'.TAB_EMP_ASSETS.'</li>';
				
			}
			else if($group_id == MANAGER_GROUP ||$group_id == EMPLOYEE_GROUP||$group_id == SYSTEMADMIN_GROUP)
			{
				//View only 7 tabs with view privilege....	General Tabs...
				
				$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'employee\','.$userId .');">
				'.TAB_EMP_OFFICIAL.'</li>';
					
				if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempviewscreen(\'emppersonaldetails\','.$userId .');">'.TAB_EMP_PERSONAL.'</li>';

				if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempviewscreen(\'empcommunicationdetails\','. $userId .');">'.TAB_EMP_CONTACT.'</li>
				';

				if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_skills" onclick="changeempviewscreen(\'empskills\','.$userId .');">'.TAB_EMP_SKILLS.'</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempviewscreen(\'empjobhistory\','.$userId .');">'.TAB_EMP_JOB_HISTORY.'</li>';

				if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
				$tabsHtml .= '<li id= "experience_details" onclick="changeempviewscreen(\'experiencedetails\','.$userId .');">'.TAB_EMP_EXPERIENCE.'</li>';

				if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "education_details" onclick="changeempviewscreen(\'educationdetails\','.$userId .');">'.TAB_EMP_EDUCATION.'</li>';
				

				if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempviewscreen(\'trainingandcertificationdetails\','.$userId .');">'.TAB_EMP_TRAINING_CERTIFY.'</li>';

			}
				
				
		}
		else if($conText == "mydetails")
		{
			
			$tabsHtml .= '<li id="empdetails"><a href="'.BASE_URL.'mydetails">'.TAB_EMP_OFFICIAL.'</a></li>';
			
			if(!empty($empOrganizationTabs) && in_array("employeedocs", $empOrganizationTabs))
			$tabsHtml .= '<li id = "employeedocs"><a href="'.BASE_URL.'mydetails/documents">'.TAB_EMP_DOCUMENTS.'</a><span class="beta_menu"></span></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
			$tabsHtml .='<li id = "emp_leaves"><a href="'.BASE_URL.'mydetails/leaves">'.TAB_EMP_LEAVES.'</a></li>';
			
			if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_salary"><a href="'.BASE_URL .'mydetails/salarydetailsview">'.TAB_EMP_SALARY.'</a></li>';
			
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "emppersonaldetails"><a href="'.BASE_URL.'mydetails/personaldetailsview">'.TAB_EMP_PERSONAL.'</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "empcommunicationdetails" ><a href="'.BASE_URL.'mydetails/communicationdetailsview">'.TAB_EMP_CONTACT.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .='<li id = "emp_skills"><a href="'.BASE_URL.'mydetails/skills">'.TAB_EMP_SKILLS.'</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory"><a href="'.BASE_URL.'mydetails/jobhistory">'.TAB_EMP_JOB_HISTORY.'</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .='<li id= "experience_details"><a href="'.BASE_URL.'mydetails/experience">'.TAB_EMP_EXPERIENCE.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "education_details"><a href="'.BASE_URL.'mydetails/education">'.TAB_EMP_EDUCATION.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "trainingandcertification_details"><a href="'.BASE_URL.'mydetails/certification">'.TAB_EMP_TRAINING_CERTIFY.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
			$tabsHtml .='<li id = "medical_claims"><a href="'.BASE_URL.'mydetails/medicalclaims">'.TAB_EMP_MEDICAL_CLAIMS.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "disabilitydetails"><a href="'.BASE_URL.'mydetails/disabilitydetailsview">'.TAB_EMP_DISABILITY.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "dependency_details"><a href="'.BASE_URL.'mydetails/dependency">'.TAB_EMP_DEPENDENCY.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
			$tabsHtml .='<li id="visadetails"><a href="'.BASE_URL.'mydetails/visadetailsview">'.TAB_EMP_VISA_EMIGRATION.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
			$tabsHtml .='<li id= "creditcarddetails"><a href="'.BASE_URL.'mydetails/creditcarddetailsview">'.TAB_EMP_CORPORATE_CARD.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
			$tabsHtml .='<li id="workeligibilitydetails"><a href="'.BASE_URL.'mydetails/workeligibilitydetailsview">'.TAB_EMP_WORK_ELIGIBILITY.'</a></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional"><a href="'.BASE_URL.'mydetails/additionaldetailsedit">'.TAB_EMP_ADDITIONAL.'</a></li>';
			
			if(!empty($empOrganizationTabs) && in_array("assetdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "assetdetails"><a href="'.BASE_URL.'mydetails/assetdetailsview">'.TAB_EMP_ASSETS.'</a></li>';
					
			
		}
		else if($conText == "myemployees")
		{
			$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'myemployees\','.$userId .');">'.TAB_EMP_OFFICIAL.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("employeedocs", $empOrganizationTabs))
			$tabsHtml .= '<li id = "employeedocs" onclick="changemyempviewscreen(\'myemployees\',\'docview\','.$userId .');">'.TAB_EMP_DOCUMENTS.'<span class="beta_menu"></span></li>';
				
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emppersonaldetails" onclick="changemyempviewscreen(\'myemployees\',\'perview\','.$userId .');">'.TAB_EMP_PERSONAL.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changemyempviewscreen(\'myemployees\',\'comview\','.$userId .');">'.TAB_EMP_CONTACT.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_skills" onclick="changemyempviewscreen(\'myemployees\',\'skillsview\','.$userId .');">'.TAB_EMP_SKILLS.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory" onclick="changemyempviewscreen(\'myemployees\',\'jobhistoryview\','.$userId .');">'.TAB_EMP_JOB_HISTORY.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "experience_details" onclick="changemyempviewscreen(\'myemployees\',\'expview\','.$userId .');">'.TAB_EMP_EXPERIENCE.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "education_details" onclick="changemyempviewscreen(\'myemployees\',\'eduview\','.$userId .');">'.TAB_EMP_EDUCATION.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changemyempviewscreen(\'myemployees\',\'trainingview\','.$userId .');">'.TAB_EMP_TRAINING_CERTIFY.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional" onclick="changemyempviewscreen(\'myemployees\',\'additionaldetailsview\','.$userId .');">'.TAB_EMP_ADDITIONAL.'</li>';
		}
		else if($conText == "myemployeesedit")
		{
			$tabsHtml .= '<li id="empdetails" onclick="changeeditscreen(\'myemployees\','.$userId .');">'.TAB_EMP_OFFICIAL.'</li>';
			
			if(!empty($empOrganizationTabs) && in_array("employeedocs", $empOrganizationTabs))
			$tabsHtml .= '<li id = "employeedocs" onclick="changemyempviewscreen(\'myemployees\',\'docedit\','.$userId .');">'.TAB_EMP_DOCUMENTS.'<span class="beta_menu"></span></li>';
				
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emppersonaldetails" onclick="changemyempviewscreen(\'myemployees\',\'peredit\','.$userId .');">'.TAB_EMP_PERSONAL.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changemyempviewscreen(\'myemployees\',\'comedit\','.$userId .');">'.TAB_EMP_CONTACT.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_skills" onclick="changemyempviewscreen(\'myemployees\',\'skillsedit\','.$userId .');">'.TAB_EMP_SKILLS.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory" onclick="changemyempviewscreen(\'myemployees\',\'jobhistoryedit\','.$userId .');">'.TAB_EMP_JOB_HISTORY.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "experience_details" onclick="changemyempviewscreen(\'myemployees\',\'expedit\','.$userId .');">'.TAB_EMP_EXPERIENCE.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "education_details" onclick="changemyempviewscreen(\'myemployees\',\'eduedit\','.$userId .');">'.TAB_EMP_EDUCATION.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changemyempviewscreen(\'myemployees\',\'trainingedit\','.$userId .');">'.TAB_EMP_TRAINING_CERTIFY.'</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional" onclick="changemyempviewscreen(\'myemployees\',\'additionaldetailsedit\','.$userId .');">'.TAB_EMP_ADDITIONAL.'</li>';
		}
	$tabsHtml .= '</ul></div></div>';
		echo $tabsHtml;
	}


}
?>


