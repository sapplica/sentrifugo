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

class Default_Form_Appraisalinit extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'appraisalinit');

        $id = new Zend_Form_Element_Hidden('id');
        
        $auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity())
        {
            $loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
        }
        
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP  && $loginuserGroup != HR_GROUP)
        {
            
            $businessunit_id = new Zend_Form_Element_Hidden('businessunit_id');
            $department_id = new Zend_Form_Element_Hidden('department_id');

            $businessunit_name = new Zend_Form_Element_Text('businessunit_name');
            $businessunit_name->setLabel("Business Unit");
            $businessunit_name->setAttrib('readonly', 'true');
            $businessunit_name->setAttrib('onfocus', 'this.blur()');
            $businessunit_name->setOptions(array('class' => 'brdr_none'));

            $department_name = new Zend_Form_Element_Text('department_name');
            $department_name->setLabel("Department");
            $department_name->setAttrib('readonly', 'true');
            $department_name->setAttrib('onfocus', 'this.blur()');
            $department_name->setOptions(array('class' => 'brdr_none'));
			
        }
        else
        {
            $businessunit_id = new Zend_Form_Element_Select('businessunit_id');
            $businessunit_id->setLabel("Business Unit");                        
            $businessunit_id->setRegisterInArrayValidator(false);
            $businessunit_id->setRequired(true);
            $businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit'));
            
            $department_id = new Zend_Form_Element_Select('department_id');
            $department_id->setLabel("Department");                        
            $department_id->setRegisterInArrayValidator(false);
			$department_id->addMultiOptions(array('' => 'Select Department'));
            
            $performance_app_flag = Zend_Controller_Front::getInstance()->getRequest()->getParam('performance_app_flag',null);
            if($performance_app_flag != '' && $performance_app_flag == 0)
            {
                $department_id->setRequired(true);
                $department_id->addValidator('NotEmpty', false, array('messages' => 'Please select department'));
            }
            
        }
        // $appraisal_mode = new Zend_Form_Element_Text('appraisal_mode');
        // $appraisal_mode->setLabel("Appraisal Mode");
        // $appraisal_mode->setAttrib('readonly', 'readonly');
        // $appraisal_mode->setAttrib('onfocus', 'this.blur()');
        // $appraisal_mode->setOptions(array('class' => 'brdr_none'));
		
		
		$appraisal_mode = new Zend_Form_Element_Select('appraisal_mode');
		$appraisal_mode->setLabel("Appraisal Mode");
        $appraisal_mode->setAttrib('class', 'selectoption');
        $appraisal_mode->addMultiOptions(array(''   => 'Select appraisal mode',
        										'Quarterly'  => 'Quarterly', 
        										'Half-yearly'	 => 'Half-yearly',
        										'Yearly'	 =>	'Yearly'   ));
		$appraisal_mode->setRegisterInArrayValidator(false);
		$appraisal_mode->setRequired(true);
		$appraisal_mode->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal mode'));		
						
        $status = new Zend_Form_Element_Select('status');
        $status->setLabel("Appraisal Status");
        $status->setAttrib('class', 'selectoption');
        $status->setMultiOptions(array('1'=>'Open'));
        $status->setRegisterInArrayValidator(false);
        $status->setRequired(true);
        $status->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal status.'));
                
        $from_year = new Zend_Form_Element_Select('from_year');        
        $from_year->setAttrib('class', 'selectoption');        
        $from_year->setRegisterInArrayValidator(false);
        $from_year->setRequired(true);
        $from_year->setLabel("From Year");
        $from_year->addMultiOption("","From Year");
        $from_year->addValidator('NotEmpty', false, array('messages' => 'Please select from year'));
		$current_date = date('Y-m-d');
		$previous_year = date('Y',strtotime("$current_date -1 year"));
        for($i = $previous_year;$i<=($previous_year+5);$i++ )
        {
            $from_year->addMultiOption($i,$i);
        }
        
        $to_year = new Zend_Form_Element_Select('to_year');
        $to_year->setAttrib('class', 'selectoption');        
        $to_year->setRegisterInArrayValidator(false);
        $to_year->setRequired(true);
        $to_year->setLabel("To Year");
        $to_year->addMultiOption("","To Year");
        $to_year->addValidator('NotEmpty', false, array('messages' => 'Please select to year'));
        
        /* Limit 'To Year' field years 
         * upto following year of 'From Year'
         * upto 5 years from current last year i.e.$previous_year
         */
        $post_from_year = Zend_Controller_Front::getInstance()->getRequest()->getParam('from_year', null);
        if(!empty($post_from_year)) {
	        for($i = $post_from_year; ($i <= ($post_from_year+1) && $i <= ($previous_year+5)); $i++ ) {
	        	$to_year->addMultiOption($i,$i);
	        }
        }
        
        $appraisal_period = new Zend_Form_Element_Text('appraisal_period');
        $appraisal_period->setLabel("Period");
        $appraisal_period->setAttrib('readonly', 'readonly');
        $appraisal_period->setAttrib('onfocus', 'this.blur()');
        $appraisal_period->setOptions(array('class' => 'brdr_none'));		
        // $appraisal_period->setRequired(true);	
        // $appraisal_period->addValidator('NotEmpty', false, array('messages' => 'Please enter period'));
		
        $eligibility = new Zend_Form_Element_Multiselect('eligibility');
        $eligibility->setLabel("Eligibility");
        $eligibility->setAttrib('class', 'selectoption');
        $eligibility->setMultiOptions(array(''=>'Select Eligibility'));
        /*$eligibility->setRegisterInArrayValidator(false);
        $eligibility->setRequired(true);
        $eligibility->addValidator('NotEmpty', false, array('messages' => 'Please select eligiblity'));*/
        
        $eligibility_hidden = new Zend_Form_Element_Multiselect('eligibility_hidden');
        $eligibility_hidden->setLabel("Eligibility");
        $eligibility_hidden->setAttrib('class', 'selectoption');
        $eligibility_hidden->setRegisterInArrayValidator(false);
        
        $eligibilityflag = new Zend_Form_Element_Hidden('eligibilityflag');
        $eligibility_value = new Zend_Form_Element_Hidden('eligibility_value');
        
        $category_id = new Zend_Form_Element_Multiselect('category_id');
        $category_id->setLabel("Parameters");        
        $category_id->setMultiOptions(array(''=>'Select Parameters'));
        $category_id->setRegisterInArrayValidator(false);
        $category_id->setRequired(true);
        $category_id->addValidator('NotEmpty', false, array('messages' => 'Please select parameters'));
    	
        $enable = new Zend_Form_Element_Select('enable_step');
        $enable->setLabel("Enable To");  
        $enable->setAttrib('onchange', 'changeduedatetext(this.value)');         
        $enable->setMultiOptions(array('1'=>'Managers','2'=>'Employees'));
        $enable->setRegisterInArrayValidator(false);
        $enable->setRequired(true);
        $enable->addValidator('NotEmpty', false, array('messages' => 'Please select enable to'));
		
        $enable_to_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('enable_step',null);
        
        $mgr_due_date = new Zend_Form_Element_Text('managers_due_date');
        $mgr_due_date->setLabel("Managers Due Date");                        
        		
        $emp_due_date = new Zend_Form_Element_Text('employee_due_date');
        $emp_due_date->setLabel("Employees Due Date");                        
        		   
        if($enable_to_val == '' || $enable_to_val == 1)
        {
            $mgr_due_date->setRequired(true);
            $mgr_due_date->addValidator('NotEmpty', false, array('messages' => 'Please select managers due date'));
        }
        else
        {
            $emp_due_date->setRequired(true);
            $emp_due_date->addValidator('NotEmpty', false, array('messages' => 'Please select employees due date'));
        }
        $management_appraisal = new Zend_Form_Element_Checkbox('management_appraisal');
        $management_appraisal->setLabel("Consider management");  
        
        // $appraisal_ratings = new Zend_Form_Element_Text('appraisal_ratings');
        // $appraisal_ratings->setAttrib('readonly', 'readonly');
        // $appraisal_ratings->setAttrib('onfocus', 'this.blur()');
        // $appraisal_ratings->setLabel("Ratings");
		
		$appraisal_ratings = new Zend_Form_Element_Select('appraisal_ratings');
		$appraisal_ratings->setLabel("Appraisal Ratings");
        $appraisal_ratings->setAttrib('class', 'selectoption');
        $appraisal_ratings->addMultiOptions(array('' => 'Select ratings',
        										'1'=> '1-5',
        										'2'=> '1-10'));
		$appraisal_ratings->setRegisterInArrayValidator(false);
		$appraisal_ratings->setRequired(true);
		$appraisal_ratings->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal ratings'));		
		

		$app_period_hid = new Zend_Form_Element_Hidden('app_period_hid');
		
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save'); 		
        
        if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP && $loginuserGroup != HR_GROUP)
        {                    
            $this->addElements(array($appraisal_ratings,$management_appraisal,$id,$appraisal_period,$from_year,$to_year,$businessunit_id,$department_id,$businessunit_name,$department_name,
                                    $appraisal_mode,$category_id,$status,$eligibility,$eligibility_hidden,$eligibility_value,$eligibilityflag,$enable,$mgr_due_date,$emp_due_date,$app_period_hid,$submit));
        }
        else
        {   
            $this->addElements(array($appraisal_ratings,$management_appraisal,$id,$appraisal_period,$from_year,$to_year,$businessunit_id,$department_id,
                                    $appraisal_mode,$category_id,$status,$eligibility,$eligibility_hidden,$eligibility_value,$eligibilityflag,$enable,$mgr_due_date,$emp_due_date,$app_period_hid,$submit));
        }
        $this->setElementDecorators(array('ViewHelper'));
    }
}