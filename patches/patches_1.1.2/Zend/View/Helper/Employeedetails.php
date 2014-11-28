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
				$empdata .= '<div id="hrbgchecklink" style="display:none;" class="action-to-page"><a href="'.BASE_URL.'/empscreening/checkscreeningstatus/empid/'.$userId.'">Send for background checks</a></div>';
			}
		
		}
		if($conText != 'mydetails')
		{
			$empdata .= '<div class="back-to-page"><input type="button" value="Back" name="Back" onclick="gobacktocontroller(\''.$conText.'\');"></div>';
		}
		
		$empdata .= '<div class="emp-screen-view">';
		$empdata .= '<div class="display-img-div" id="displayimg" >';
		$empdata .= '<div class="employee-pic-emp">';
		if($emparr['profileimg']!=''){
			$empdata .=	'<img id="userImage" src="'.BASE_URL.("/public/uploads/profile/").$emparr['profileimg'].'" onerror="this.src=\''.BASE_URL.'/public/media/images/default-profile-pic.jpg\'"/>';
		}
		else{
			$empdata .=	'<img id="userImage" src="'.BASE_URL.'/public/media/images/employee-deafult-pic.jpg" />';
		}
		if($conText != 'mydetails')
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
					$empdata .= '<p class="field switch"><label class="cb-enable  '.$sel_act.'"><span>Active</span></label><label class="cb-disable '.$sel_dact.'"><span>Inactive</span></label> </p>';
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
		$empdata .=	'<div id="loaderimg" style="display:none; clear:both; margin:0 auto; text-align: center; width:100%;"><img src="'.BASE_URL.("/public/media/images/loaderwhite_21X21.gif").'" style="width:21px; height: 21px; float: none; "/>';
		$empdata .= '</div>';
		$empdata .=	'</div>';

		$empdata .= '<div id="personalDetailsDiv">';
		if($emparr['is_orghead'] == '1') $headicon = '<img src="'.BASE_URL.("/public/media/images/org-head.png").'" class="org-head-icon" />';
		else $headicon = '';
		if(isset($emparr['active_prefix']) && isset($emparr['prefix']) && $emparr['active_prefix'] == 1 && $emparr['prefix'] !='')
		$empdata .=	'<p><b>Employee Name <i>:</i></b><span class="emp-name-span bold-text">'.$emparr['prefix'].'.&nbsp;'.$emparr['userfullname'].'</span>'.$headicon.'</p>';
		else
		$empdata .=	'<p><b>Employee Name <i>:</i></b><span class="emp-name-span bold-text">'.$emparr['userfullname'].'</span>'.$headicon.'</p>';
		$empdata .=	'<p><b>Employee Id <i>:</i></b><span class="emp-name-span" id ="spanempid">'.$emparr['employeeId'].'</span></p>';
		$empdata .=	'<p><b>Email Id <i>:</i></b><span><a href="javascript:void(0)">'.$emparr['emailaddress'].'</a></span></p>';
		if($emparr['contactnumber'] !='')
		{
			$empdata .=	'<p><b>Contact Number <i>:</i></b>';
			$empdata .= '<span id="contactnospan" >'.$emparr['contactnumber'].'</span>';
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $loggedinuser == $userId )
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
			if($group_id == HR_GROUP || $loggedinuser == SUPERADMIN || $loggedinuser == $userId)
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
								<img src="'. BASE_URL.'/public/media/images/close.png" name="" align="right" border="0" hspace="3" vspace="5" class="closeAttachPopup" style="margin: -24px 8px 0 0;"> 
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

		$tabsHtml = '<div class="poc-ui-data-control" id="'.$tabHeightClass.'"><div class="left-block-ui-data"><div class="agency-ui"><ul>';
		if($conText == "edit")
		{
			
			//View all tabs with all privileges....	onclick - changeempeditscreen...
			
			$tabsHtml .= '<li  id="empdetails" onclick="changeeditscreen(\'employee\','.$userId .');">
				Employee Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempeditscreen(\'emppersonaldetails\','.$userId .');">Personal Details</li>';

			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempeditscreen(\'empcommunicationdetails\','. $userId .');">Communication Details</li>
				';

			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_skills" onclick="changeempeditscreen(\'empskills\','.$userId .');">Employee Skills</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempeditscreen(\'empjobhistory\','.$userId .');">Employee Job History</li>';

			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .= '<li id= "experience_details" onclick="changeempeditscreen(\'experiencedetails\','.$userId .');">Experience Details</li>';

			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "education_details" onclick="changeempeditscreen(\'educationdetails\','.$userId .');">Education  Details</li>';
			if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_leaves" onclick="changeempeditscreen(\'empleaves\','.$userId .');">Employee Leaves</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_holidays", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_holidays" onclick="changeempeditscreen(\'empholidays\','.$userId .');">Employee Holidays</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_salary" onclick="changeempeditscreen(\'empsalarydetails\','.$userId .');">Salary Details</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_performanceappraisal", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_performanceappraisal" onclick="changeempeditscreen(\'empperformanceappraisal\','.$userId .');">Performance Appraisal</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_payslips", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_payslips" onclick="changeempeditscreen(\'emppayslips\','.$userId .');">Pay slips</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_benifits", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_benifits" onclick="changeempeditscreen(\'empbenefits\','.$userId .');">Benefits</li>';

			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempeditscreen(\'trainingandcertificationdetails\','.$userId .');">Training & Certification  Details</li>';

			if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
			$tabsHtml .= '<li id = "medical_claims" onclick="changeempeditscreen(\'medicalclaims\','. $userId .');">Medical Claims</li>';

			if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "disabilitydetails" onclick="changeempeditscreen(\'disabilitydetails\','.$userId .');">Disability Details</li>';

			if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "dependency_details" onclick="changeempeditscreen(\'dependencydetails\','.$userId .');">Dependency Details</li>';

			if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
			$tabsHtml .= '<li id="visadetails"onclick="changeempeditscreen(\'visaandimmigrationdetails\','.$userId .');">Visa and Immigration Details</li>';

			if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
			$tabsHtml .= '<li id= "creditcarddetails" onclick="changeempeditscreen(\'creditcarddetails\','.$userId.');">Corporate Card Details</li>';

			if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
			$tabsHtml	.= '<li id="workeligibilitydetails" onclick="changeempeditscreen(\'workeligibilitydetails\','. $userId .');">Work Eligibility Details</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_reqdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_reqdetails" onclick="changeempeditscreen(\'emprequisitiondetails\','.$userId .');">Requisition Details</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_renumeration", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_renumeration" onclick="changeempeditscreen(\'empremunerationdetails\','.$userId .');">Remuneration Details</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_security", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_security" onclick="changeempeditscreen(\'empsecuritycredentials\','.$userId .');">Security Credentials</li>';

			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional" onclick="changeempeditscreen(\'empadditionaldetails\','.$userId .');">Additional Details</li>';
			
		}
		else if($conText == "view")
		{
			if($group_id == HR_GROUP ||$group_id == MANAGEMENT_GROUP || $loggedinuser == SUPERADMIN)
			{
				
				$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'employee\','.$userId .');">
				Employee Details</li>';
					
				if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempviewscreen(\'emppersonaldetails\','.$userId .');">Personal Details</li>';

				if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempviewscreen(\'empcommunicationdetails\','. $userId .');">Communication Details</li>
				';

				if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_skills" onclick="changeempviewscreen(\'empskills\','.$userId .');">Employee Skills</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempviewscreen(\'empjobhistory\','.$userId .');">Employee Job History</li>';

				if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
				$tabsHtml .= '<li id= "experience_details" onclick="changeempviewscreen(\'experiencedetails\','.$userId .');">Experience Details</li>';

				if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "education_details" onclick="changeempviewscreen(\'educationdetails\','.$userId .');">Education  Details</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_leaves" onclick="changeempviewscreen(\'empleaves\','.$userId .');">Employee Leaves</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_holidays", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_holidays" onclick="changeempviewscreen(\'empholidays\','.$userId .');">Employee Holidays</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_salary" onclick="changeempviewscreen(\'empsalarydetails\','.$userId .');">Salary Details</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_performanceappraisal", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_performanceappraisal" onclick="changeempviewscreen(\'empperformanceappraisal\','.$userId .');">Performance Appraisal</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_payslips", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_payslips" onclick="changeempviewscreen(\'emppayslips\','.$userId .');">Pay slips</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_benifits", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_benifits" onclick="changeempviewscreen(\'empbenefits\','.$userId .');">Benefits</li>';

				if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempviewscreen(\'trainingandcertificationdetails\','.$userId .');">Training & Certification  Details</li>';

				if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
				$tabsHtml .= '<li id = "medical_claims" onclick="changeempviewscreen(\'medicalclaims\','. $userId .');">Medical Claims</li>';

				if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "disabilitydetails" onclick="changeempviewscreen(\'disabilitydetails\','.$userId .');">Disability Details</li>';

				if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "dependency_details" onclick="changeempviewscreen(\'dependencydetails\','.$userId .');">Dependency Details</li>';

				if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
				$tabsHtml .= '<li id="visadetails" onclick="changeempviewscreen(\'visaandimmigrationdetails\','.$userId .');">Visa and Immigration Details</li>';

				if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
				$tabsHtml .= '<li id= "creditcarddetails" onclick="changeempviewscreen(\'creditcarddetails\','.$userId.');">Corporate Card Details</li>';

				if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
				$tabsHtml	.= '<li id="workeligibilitydetails" onclick="changeempviewscreen(\'workeligibilitydetails\','. $userId .');">Work Eligibility Details</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_reqdetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_reqdetails" onclick="changeempviewscreen(\'emprequisitiondetails\','.$userId .');">Requisition Details</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_renumeration", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_renumeration" onclick="changeempviewscreen(\'empremunerationdetails\','.$userId .');">Remuneration Details</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_security", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_security" onclick="changeempviewscreen(\'empsecuritycredentials\','.$userId .');">Security Credentials</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_additional" onclick="changeempviewscreen(\'empadditionaldetails\','.$userId .');">Additional Details</li>';
			}
			else if($group_id == MANAGER_GROUP ||$group_id == EMPLOYEE_GROUP||$group_id == SYSTEMADMIN_GROUP)
			{
				//View only 7 tabs with view privilege....	General Tabs...
				
				$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'employee\','.$userId .');">
				Employee Details</li>';
					
				if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emppersonaldetails" onclick="changeempviewscreen(\'emppersonaldetails\','.$userId .');">Personal Details</li>';

				if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
				$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changeempviewscreen(\'empcommunicationdetails\','. $userId .');">Communication Details</li>
				';

				if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_skills" onclick="changeempviewscreen(\'empskills\','.$userId .');">Employee Skills</li>';

				if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
				$tabsHtml .= '<li id = "emp_jobhistory" onclick="changeempviewscreen(\'empjobhistory\','.$userId .');">Employee Job History</li>';

				if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
				$tabsHtml .= '<li id= "experience_details" onclick="changeempviewscreen(\'experiencedetails\','.$userId .');">Experience Details</li>';

				if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "education_details" onclick="changeempviewscreen(\'educationdetails\','.$userId .');">Education  Details</li>';
				

				if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
				$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changeempviewscreen(\'trainingandcertificationdetails\','.$userId .');">Training & Certification  Details</li>';

			}
				
				
		}
		else if($conText == "mydetails")
		{
			
			$tabsHtml .= '<li id="empdetails"><a href="'.BASE_URL.'/mydetails">Employee Details</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "emppersonaldetails"><a href="'.BASE_URL.'/mydetails/personaldetailsview">Personal Details</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "empcommunicationdetails" ><a href="'.BASE_URL.'/mydetails/communicationdetailsview">Communication Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .='<li id = "emp_skills"><a href="'.BASE_URL.'/mydetails/skills">Employee Skills</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory"><a href="'.BASE_URL.'/mydetails/jobhistory">Employee Job History</a></li>';
				
			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .='<li id= "experience_details"><a href="'.BASE_URL.'/mydetails/experience">Experience Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "education_details"><a href="'.BASE_URL.'/mydetails/education">Education  Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_leaves", $empOrganizationTabs))
			$tabsHtml .='<li id = "emp_leaves"><a href="'.BASE_URL.'/mydetails/leaves">Employee Leaves</a></li>';
			

			if(!empty($empOrganizationTabs) && in_array("emp_salary", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_salary"><a href="'.BASE_URL .'/mydetails/salarydetailsview">Salary Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "trainingandcertification_details"><a href="'.BASE_URL.'/mydetails/certification">Training & Certification  Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("medical_claims", $empOrganizationTabs))
			$tabsHtml .='<li id = "medical_claims"><a href="'.BASE_URL.'/mydetails/medicalclaims">Medical Claims</a></li>';

			if(!empty($empOrganizationTabs) && in_array("disabilitydetails", $empOrganizationTabs))
			$tabsHtml .='<li id = "disabilitydetails"><a href="'.BASE_URL.'/mydetails/disabilitydetailsview">Disability Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("dependency_details", $empOrganizationTabs))
			$tabsHtml .='<li id = "dependency_details"><a href="'.BASE_URL.'/mydetails/dependency">Dependency Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("visadetails", $empOrganizationTabs))
			$tabsHtml .='<li id="visadetails"><a href="'.BASE_URL.'/mydetails/visadetailsview">Visa and Immigration Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("creditcarddetails", $empOrganizationTabs))
			$tabsHtml .='<li id= "creditcarddetails"><a href="'.BASE_URL.'/mydetails/creditcarddetailsview">Corporate Card Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("workeligibilitydetails", $empOrganizationTabs))
			$tabsHtml .='<li id="workeligibilitydetails"><a href="'.BASE_URL.'/mydetails/workeligibilitydetailsview">Work Eligibility Details</a></li>';

			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			
			$tabsHtml .= '<li id = "emp_additional"><a href="'.BASE_URL.'/mydetails/additionaldetailsedit">Additional Details</a></li>';

		}
		else if($conText == "myemployees")
		{
			
			$tabsHtml .= '<li id="empdetails" onclick="changeviewscreen(\'myemployees\','.$userId .');">Employee Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emppersonaldetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emppersonaldetails" onclick="changemyempviewscreen(\'myemployees\',\'perview\','.$userId .');">Personal Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("empcommunicationdetails", $empOrganizationTabs))
			$tabsHtml .= '<li id = "empcommunicationdetails" onclick="changemyempviewscreen(\'myemployees\',\'comview\','.$userId .');">Communication Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_skills", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_skills" onclick="changemyempviewscreen(\'myemployees\',\'skillsview\','.$userId .');">Employee Skills</li>';
				
			
				
			if(!empty($empOrganizationTabs) && in_array("emp_jobhistory", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_jobhistory" onclick="changemyempviewscreen(\'myemployees\',\'jobhistoryview\','.$userId .');">Employee Job History</li>';
				
			if(!empty($empOrganizationTabs) && in_array("experience_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "experience_details" onclick="changemyempviewscreen(\'myemployees\',\'expview\','.$userId .');">Experience  Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("education_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "education_details" onclick="changemyempviewscreen(\'myemployees\',\'eduview\','.$userId .');">Education  Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("trainingandcertification_details", $empOrganizationTabs))
			$tabsHtml .= '<li id = "trainingandcertification_details" onclick="changemyempviewscreen(\'myemployees\',\'trainingview\','.$userId .');">Training & Certification  Details</li>';
				
			if(!empty($empOrganizationTabs) && in_array("emp_additional", $empOrganizationTabs))
			$tabsHtml .= '<li id = "emp_additional" onclick="changemyempviewscreen(\'myemployees\',\'additionaldetailsview\','.$userId .');">Additional Details</li>';
		}
		$tabsHtml .= '</ul></div></div>';
		echo $tabsHtml;
	}

}
?>