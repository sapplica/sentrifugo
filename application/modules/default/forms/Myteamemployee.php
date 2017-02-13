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

class Default_Form_Myteamemployee extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'myemployees/add');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'myteamemployee');
		$controller_name = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$id = new Zend_Form_Element_Hidden('id');
		$id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
		$userid = new Zend_Form_Element_Hidden('user_id');

                //for emp code
                $employeeId = new Zend_Form_Element_Text("employeeId");
                $employeeId->setRequired("true");
                $employeeId->setLabel("Employee Code");        
                $employeeId->setAttrib("class", "formDataElement");
                $employeeId->setAttrib("readonly", "readonly");
				$employeeId->setAttrib('onfocus', 'this.blur()');
				$employeeId->addValidator('NotEmpty', false, array('messages' => 'Identity codes are not configured yet.'));

                //for emp id
                $employeeNumId = new Zend_Form_Element_Text("employeeNumId");
                $employeeNumId->setRequired("true");
                $employeeNumId->setLabel("Employee Id");
                $employeeNumId->setAttrib('maxLength', 5);       
                $employeeNumId->setAttrib("class", "formDataElement");
                $employeeNumId->addValidator('NotEmpty', false, array('messages' => 'Please enter the Employee Id.'));
                $employeeNumId->addValidator("regex",true,array(                          
                           'pattern'=>'/^[0-9]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           ))); 
		
				/*$employeeId = new Zend_Form_Element_Text("employeeId");
                $employeeId->setRequired("true");
                $employeeId->setLabel("Employee ID");        
                $employeeId->setAttrib("class", "formDataElement");
                $employeeId->setAttrib("readonly", "readonly");
				$employeeId->setAttrib('onfocus', 'this.blur()');
                
				$employeeId->addValidator('NotEmpty', false, array('messages' => 'Identity codes are not configured yet.'));
				$employeeId->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                                array('table' => 'main_users',
                                                                'field' => 'employeeId',
                                                                'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id',0).'" ',
                                                                )));
                $employeeId->getValidator('Db_NoRecordExists')->setMessage('Employee ID already exists. Please try again.');*/
                
		$prefix_id = new Zend_Form_Element_Select('prefix_id');
		$prefix_id->addMultiOption('','Select Prefix');
		$prefix_id->setLabel("Prefix");
		$prefix_id->setRegisterInArrayValidator(false);
		$prefix_id->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_prefix',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$prefix_id->getValidator('Db_RecordExists')->setMessage('Selected prefix is deleted.');
		
				$first_name = new Zend_Form_Element_Text("firstname");
                $first_name->setLabel("First Name");
               	$first_name->setRequired(true);
               	$first_name->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));
                $first_name->setAttrib("class", "formDataElement");
                $first_name->setAttrib('maxlength', 50);
                $first_name->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
									   'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
                
                $last_name = new Zend_Form_Element_Text("lastname");
                $last_name->setLabel("Last Name");
                $last_name->setRequired(true);	
                $last_name->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));
                $last_name->setAttrib("class", "formDataElement");
                $last_name->setAttrib('maxlength', 50);
                $last_name->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.]+ ?)+$/',
                                   'messages'=>array(
									   'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
		
				$modeofentry = new Zend_Form_Element_Select("modeofentry");                
                $modeofentry->setLabel("Mode of Employment")
                            ->addMultiOptions(array(
													'Direct' => 'Direct',
                                                    ));	
                $modeofentry->setAttrib("class", "formDataElement");
                if($id_val == ''){
					$modeofentry->setRequired(true);
                	$modeofentry->addValidator('NotEmpty', false, array('messages' => 'Please select mode of employment.'));
                }
                
				$emprole = new Zend_Form_Element_Select("emprole");        
                $emprole->setRegisterInArrayValidator(false);
                $emprole->setRequired(true);
				$emprole->setLabel("Role");
                $emprole->setAttrib("class", "formDataElement");
                $emprole->addValidator('NotEmpty', false, array('messages' => 'Please select role.'));
                $emprole->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_roles',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
				$emprole->getValidator('Db_RecordExists')->setMessage('Selected role is deleted.');
                
                $emailaddress = new Zend_Form_Element_Text("emailaddress");
				$emailaddress->setRequired(true);
                $emailaddress->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
                $emailaddress->addValidator("regex",true,array(
						    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid email.'
                           )
        	    ));
        		$emailaddress->setLabel("Email");
                $emailaddress->setAttrib("class", "formDataElement");              
                $emailaddress->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                                array('table' => 'main_users',
                                                                'field' => 'emailaddress',
                                                                'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id',0).'" ',                                                                        					        						
                                                                )));
                $emailaddress->getValidator('Db_NoRecordExists')->setMessage('Email already exists.');
                
		$businessunit = new Zend_Form_Element_Select('businessunit_id');
		$businessunit->setRegisterInArrayValidator(false);
		$businessunit->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_businessunits',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$businessunit->getValidator('Db_RecordExists')->setMessage('Selected business unit is deleted.');
		
		$department = new Zend_Form_Element_Select('department_id');
		$department->setRegisterInArrayValidator(false);
		$department->setRequired(true);
		$department->addValidator('NotEmpty', false, array('messages' => 'Please select department.'));
		$department->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_departments',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$department->getValidator('Db_RecordExists')->setMessage('Selected department is deleted.');	
		
		$reportingmanager = new Zend_Form_Element_Select('reporting_manager');
		$reportingmanager->setRegisterInArrayValidator(false);	
		$reportingmanager->setRequired(true);
        $reportingmanager->addValidator('NotEmpty', false, array('messages' => 'Please select reporting manager.'));
        $reportingmanager->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_users',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$reportingmanager->getValidator('Db_RecordExists')->setMessage('Selected reporting manager is inactivated.');
        
        $jobtitle = new Zend_Form_Element_Select('jobtitle_id');
		$jobtitle->setLabel("Job Title");
        $jobtitle->addMultiOption('','Select Job Title');
		$jobtitle->setAttrib('onchange', 'displayPositions(this,"position_id","")');
		$jobtitle->setRegisterInArrayValidator(false);
		$jobtitle->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_jobtitles',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$jobtitle->getValidator('Db_RecordExists')->setMessage('Selected job title is deleted.');	                
		
		$position = new Zend_Form_Element_Select('position_id');
        $position->setLabel("Position");
		$position->addMultiOption('','Select Position');
		$position->setRegisterInArrayValidator(false);
		$position->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_positions',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$position->getValidator('Db_RecordExists')->setMessage('Selected position is deleted.');
        			
		$empstatus = new Zend_Form_Element_Select('emp_status_id');
		$empstatus->setAttrib('onchange', 'displayempstatusmessage()');
		$empstatus->setRegisterInArrayValidator(false);	
		$empstatus->setRequired(true);
        $empstatus->addValidator('NotEmpty', false, array('messages' => 'Please select employment status.'));
        $empstatus->addValidator(new Zend_Validate_Db_RecordExists(
                                                                array('table' => 'main_employmentstatus',
                                                                'field' => 'workcodename',
                                                                'exclude'=>'isactive = 1',
                                                                )));
     	$empstatus->getValidator('Db_RecordExists')->setMessage('Selected employment status is deleted.');
        
        $date_of_joining = new ZendX_JQuery_Form_Element_DatePicker('date_of_joining');
		$date_of_joining->setLabel("Date of Joining");
		$date_of_joining->setOptions(array('class' => 'brdr_none'));
		$date_of_joining->setAttrib('onchange', 'validatejoiningdate(this)');
		$date_of_joining->setRequired(true);
		$date_of_joining->setAttrib('readonly', 'true');
		$date_of_joining->setAttrib('onfocus', 'this.blur()');
        $date_of_joining->addValidator('NotEmpty', false, array('messages' => 'Please select date of joining.'));	

        $date_of_leaving = new ZendX_JQuery_Form_Element_DatePicker('date_of_leaving');
		$date_of_leaving->setOptions(array('class' => 'brdr_none'));
          $date_of_leaving->setAttrib('onchange', 'validateleavingdate(this)'); 				
		$date_of_leaving->setAttrib('readonly', 'true');
		$date_of_leaving->setAttrib('onfocus', 'this.blur()');
		
		$yearsofexp = new Zend_Form_Element_Text('years_exp');
		$yearsofexp->setAttrib('maxLength', 2);
		$yearsofexp->addFilter(new Zend_Filter_StringTrim());
		$yearsofexp->addValidator("regex",true,array(
						  'pattern'=>'/^[0-9]\d{0,1}(\.\d*)?$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           )
        	));
		
		$extension_number = new Zend_Form_Element_Text('extension_number');
		$extension_number->setAttrib('maxLength', 4);
		$extension_number->setLabel("Extension");
		$extension_number->addFilter(new Zend_Filter_StringTrim());
		$extension_number->addValidator("regex",true,array(                          
                           'pattern'=>'/^[0-9]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           )
        	));
        	
	    $office_number = new Zend_Form_Element_Text('office_number');
        $office_number->setAttrib('maxLength', 10);
		$office_number->setLabel("Work Telephone Number");
        $office_number->addFilter(new Zend_Filter_StringTrim());
		$office_number->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9\+\-\)\(]+$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        	));

		$office_faxnumber = new Zend_Form_Element_Text('office_faxnumber');
        $office_faxnumber->setAttrib('maxLength', 15);
		$office_faxnumber->setLabel("Fax");
        $office_faxnumber->addFilter(new Zend_Filter_StringTrim());
		$office_faxnumber->addValidator("regex",true,array(
                           'pattern'=>'/^[0-9\+\-\)\(]+$/', 
                           'messages'=>array(
							  'regexNotMatch'=>'Please enter valid fax number.'
                           )
        	)); 	
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');	
		                
		$this->addElements(array($id,$userid,$reportingmanager,$empstatus,$businessunit,$department,$jobtitle,$position,$prefix_id,$extension_number,$office_number,$office_faxnumber,$yearsofexp,$date_of_joining,$date_of_leaving,$submit,$employeeId,$modeofentry,$emailaddress,$emprole,$first_name,$last_name,$employeeNumId));
                $this->setElementDecorators(array('ViewHelper')); 
                $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('date_of_joining','date_of_leaving'));  		
	}
}