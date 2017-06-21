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

class Default_Form_Disciplinaryincident extends Zend_Form
{
        public function init()
        {
                $this->setMethod('post');
                $this->setAttrib('id', 'formid');
                $this->setAttrib('name', 'disciplinaryincident');
                $this->setAttrib('action',BASE_URL.'disciplinaryincident/add');

                $id = new Zend_Form_Element_Hidden('id');
				$id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
				
                $incident_raised_by = new Zend_Form_Element_Text("incident_raised_by");        
                $incident_raised_by->setLabel("Incident Raised By");
                $incident_raised_by->setAttrib('name', '');
                $incident_raised_by->setAttrib('id', 'incident_raised_by');
                $incident_raised_by->setAttrib('onblur', 'clearautocompletename(this)');
                // $incident_raised_by->setRequired(true);
                // $incident_raised_by->addValidator('NotEmpty', false, array('messages' => 'Please Enter the Incident Raised By.'));

                $employee_name = new Zend_Form_Element_Select("employee_name");        
                $employee_name->setLabel("Employee Name");
                $employee_name->setAttrib('name', 'employee_name');
                $employee_name->setAttrib('id', 'employee_name');
                $employee_name->setAttrib('onblur', 'clearautocompletename(this)');
                $employee_name->setRegisterInArrayValidator(false);
                $employee_name->setRequired(true);
                $employee_name->addMultiOption('','Select An Employee');
                $employee_name->addValidator('NotEmpty', false, array('messages' => 'Please Select the Employee Name.'));

                $employee_id = new Zend_Form_Element_Text("emp_id");        
                $employee_id->setLabel("Employee Id");
                $employee_id->setAttrib('name', 'emp_id');
                $employee_id->setAttrib('id', 'emp_id');
                $employee_id->setAttrib('readonly', 'true');
                $employee_id->setRequired(true);
                $employee_id->setAttrib('onfocus', 'this.blur()');
                $employee_id->addValidator('NotEmpty', false, array('messages' => 'Employee Id can\'t be empty.'));        

                $businessunit_name = new Zend_Form_Element_Select("employee_bu_id"); 
                $businessunit_name->setAttrib('onchange', 'displayEmployeeDepartments(this,"employee_dept_id","")');
                $businessunit_name->setLabel("Business Unit");
                $businessunit_name->setAttrib('name', 'employee_bu_id');
                $businessunit_name->setAttrib('id', 'employee_bu_id');
                $businessunit_name->setRequired(true);
                $businessunit_name->addValidator('NotEmpty', false, array('messages' => 'Please Select Business Unit.'));
                $bunitModel = new Default_Model_Businessunits();
                $bunitdata = $bunitModel->fetchAll('isactive=1','unitname');
                $businessunit_name->addMultiOption('','Select Business Unit');
                $businessunit_name->addMultiOption('0','No Business Unit');
                foreach ($bunitdata->toArray() as $data){
                        $businessunit_name->addMultiOption($data['id'],$data['unitname']);
                }

                $department_name = new Zend_Form_Element_Select("employee_dept_id");        
                $department_name->setLabel("Department");
                $department_name->setAttrib('name', 'employee_dept_id');
                $department_name->setAttrib('id', 'employee_dept_id');
                $department_name->setRegisterInArrayValidator(false);
                // $department_name->setRequired(true);
                $department_name->addMultiOption('','Select Department');
                // $department_name->addValidator('NotEmpty', false, array('messages' => 'Please Select Department.'));

                $job_title = new Zend_Form_Element_Text("job_title");        
                $job_title->setLabel("Job Title");
                $job_title->setAttrib('name', 'job_title');
                $job_title->setAttrib('id', 'job_title');
                $job_title->setAttrib('readonly', 'true');
                $job_title->setAttrib('onfocus', 'this.blur()');   

                $reporting_manager = new Zend_Form_Element_Text("reporting_manager");        
                $reporting_manager->setLabel("Reporting Manager");
                $reporting_manager->setAttrib('name', 'reporting_manager');
                $reporting_manager->setAttrib('id', 'reporting_manager');
                $reporting_manager->setRequired(true);
                $reporting_manager->setAttrib('readonly', 'true');
                $reporting_manager->setAttrib('onfocus', 'this.blur()');
                $reporting_manager->addValidator('NotEmpty', false, array('messages' => 'Reporting Manager can\'t be empty.'));

                $date_of_occurrence = new ZendX_JQuery_Form_Element_DatePicker('date_of_occurrence');
                $date_of_occurrence->setLabel("Date of Occurrence");
                $date_of_occurrence->setOptions(array('class' => 'brdr_none'));
                $date_of_occurrence->setRequired(true);
                $date_of_occurrence->setAttrib('onfocus', 'this.blur()');
                $date_of_occurrence->addValidator('NotEmpty', false, array('messages' => 'Please Select Date of Occurrence.'));

                $expiry_date = new ZendX_JQuery_Form_Element_DatePicker('violation_expiry');
                $expiry_date->setLabel("Expiry Date");
                $expiry_date->setOptions(array('class' => 'brdr_none'));
                $expiry_date->setRequired(true);
                $expiry_date->setAttrib('onfocus', 'this.blur()');
                $expiry_date->setRequired(true);
                $expiry_date->addValidator('NotEmpty', false, array('messages' => 'Please Select Expiry Date.'));	        

                $type_of_violation = new Zend_Form_Element_Select("violation_id");        
                $type_of_violation->setLabel("Type of Violation");
                $type_of_violation->setAttrib('name', 'violation_id');
                $type_of_violation->setAttrib('id', 'violation_id');
                $type_of_violation->setRequired(true);
                $type_of_violation->addValidator('NotEmpty', false, array('messages' => 'Please Select Type of Violation.'));

                $violation_description = new Zend_Form_Element_Textarea("employer_statement");
                $violation_description->setLabel("Violation Description");
                $violation_description->setAttrib('name', 'employer_statement');
                $violation_description->setAttrib('id', 'employer_statement');
                $violation_description->setRequired(true);
                $violation_description->setAttrib('rows', 10);
                $violation_description->setAttrib('cols', 50);
                $violation_description->addValidator('NotEmpty', false, array('messages' => 'Please Enter Violation Description.'));                

                $employee_appeal = new Zend_Form_Element_Radio("employee_appeal");
                $employee_appeal->setLabel("Employee Appeal");
                $employee_appeal->setAttrib('name', 'employee_appeal');
                $employee_appeal->setAttrib('id', 'employee_appeal');
                $employee_appeal->addMultiOptions(array(
                        '1' => 'Yes',
                        '2' => 'No',
                ));
                $employee_appeal->setSeparator('');
                $employee_appeal->setValue(1); 							   
                $employee_appeal->setRegisterInArrayValidator(false);
                if(defined('sentrifugo_gilbert')) {
                	$employee_appeal->setRequired(true);
		            $employee_appeal->addValidator('NotEmpty', false, array('messages' => 'Please select the Employee Appeal.'));
                }else{
	                if($id_val!='') {
		                $employee_appeal->setRequired(true);
		                $employee_appeal->addValidator('NotEmpty', false, array('messages' => 'Please select the Employee Appeal.'));
	                }
                }    

                $employee_statement = new Zend_Form_Element_Textarea("employee_statement");
                $employee_statement->setLabel("Employee Statement");
                $employee_statement->setAttrib('name', 'employee_statement');
                $employee_statement->setAttrib('id', 'employee_statement');
                
                $employee_statement->setAttrib('rows', 10);
                $employee_statement->setAttrib('cols', 50);
                if(defined('sentrifugo_gilbert')) {
                	$employee_statement->setRequired(true);
	                $employee_statement->addValidator('NotEmpty', false, array('messages' => 'Please Enter Employee Statement.'));
                }else {
	                if($id_val!='') {
	                	$employee_statement->setRequired(true);
	                	$employee_statement->addValidator('NotEmpty', false, array('messages' => 'Please Enter Employee Statement.'));
	                }
                }                                


                $verdict = new Zend_Form_Element_Radio("cao_verdict");
                if(defined('sentrifugo_gilbert'))
                {
                        $verdict->setLabel("CSO/CAO Verdict");	
                }
                else
                {
                        $verdict->setLabel("Verdict");	
                }
                $verdict->setAttrib('name', 'cao_verdict');
                $verdict->setAttrib('id', 'cao_verdict');
                $verdict->addMultiOptions(array(
                        '1' => 'Guilty',
                        '2' => 'Not Guilty',
                ));
                $verdict->setSeparator('');
                $verdict->setValue(1); 									   
                $verdict->setRegisterInArrayValidator(false);
                if(defined('sentrifugo_gilbert')) {
                	$verdict->setRequired(true);
		            $verdict->addValidator('NotEmpty', false, array('messages' => 'Please select the Verdict.'));
                }else {
	                if($id_val!='') {
		                $verdict->setRequired(true);
		                $verdict->addValidator('NotEmpty', false, array('messages' => 'Please select the Verdict.'));
	                }
                }

                $corrective_action = new Zend_Form_Element_Select("corrective_action");
                $corrective_action->setLabel("Corrective Action");
                $corrective_action->setAttrib('name', 'corrective_action');
                $corrective_action->setAttrib('id', 'corrective_action');
                $corrective_action->addMultiOption('','Select Corrective Action');
                $corrective_action->addMultiOption('Suspension With Pay','Suspension With Pay');
                $corrective_action->addMultiOption('Suspension W/O Pay','Suspension W/O Pay');
                $corrective_action->addMultiOption('Termination','Termination');
                $corrective_action->addMultiOption('Other','Other');
                if(defined('sentrifugo_gilbert')) {
                	$corrective_action->setRequired(true);
		            $corrective_action->addValidator('NotEmpty', false, array('messages' => 'Please Select Corrective Action.'));
                }else{
	                if($id_val!='') {
		                $corrective_action->setRequired(true);
		                $corrective_action->addValidator('NotEmpty', false, array('messages' => 'Please Select Corrective Action.'));
	                }
                }                          
                $corrective_action_other = new Zend_Form_Element_Text("corrective_action_text");
                $corrective_action_other->setLabel("Other");
                $corrective_action_other->setAttrib('onblur', 'validatecorrectiveaction(this)');
                $corrective_action_other->setAttrib('onkeyup', 'validatecorrectiveaction(this)');
                $corrective_action_other->setAttrib('name', 'corrective_action_text');
                $corrective_action_other->setAttrib('id', 'corrective_action_text');

                $submit = new Zend_Form_Element_Submit('submit');
                $submit->setAttrib('id', 'submitbutton');
                if($id_val!='')
                	$submit->setLabel('Update');
                else	
                	$submit->setLabel('Save');

                if(defined('sentrifugo_gilbert'))
                {
                        $this->addElements(array($id,$incident_raised_by,$employee_name,$employee_id,$businessunit_name, $department_name,$job_title,$reporting_manager,$date_of_occurrence,$type_of_violation,$violation_description,$employee_appeal,$employee_statement,$verdict,$corrective_action,$corrective_action_other,$submit));

                        $this->setElementDecorators(array('ViewHelper')); 
                        $this->setElementDecorators(array(
                        'UiWidgetElement',
                        ),array('date_of_occurrence')); 
                }
                else
                {
                        $this->addElements(array($id,$incident_raised_by,$employee_name,$employee_id,$businessunit_name, $department_name,$job_title,$reporting_manager,$date_of_occurrence,$type_of_violation,$violation_description,$employee_appeal,$employee_statement,$verdict,$corrective_action,$corrective_action_other,$expiry_date,$submit));

                        $this->setElementDecorators(array('ViewHelper')); 
                        $this->setElementDecorators(array('UiWidgetElement',),array('date_of_occurrence','violation_expiry')); 		
                }
        }
}