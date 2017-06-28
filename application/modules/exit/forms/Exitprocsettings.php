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
class Exit_Form_Exitprocsettings extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','epSettingsFrm');
		$this->setAttrib('name','epSettingsFrm');

		/*$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
            $businessunit_id = $auth->getStorage()->read()->businessunit_id;
            $department_id = $auth->getStorage()->read()->department_id; 
            $loginuserRole = $auth->getStorage()->read()->emprole;
            $loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		
		if($loginuserRole != SUPERADMINROLE && $loginuserGroup != MANAGEMENT_GROUP)
        {
        */
			$businessunit_id = new Zend_Form_Element_Hidden('bunit_id');
            $department_id = new Zend_Form_Element_Hidden('dept_id');

            $businessunit_id = new Zend_Form_Element_Select('businessunit_id');
            $businessunit_id->setLabel("Business Unit");
			$businessunit_id->setRequired(true);
			$businessunit_id->setRegisterInArrayValidator(false);
			$businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
                       
            $department_id = new Zend_Form_Element_Select('department_id');
            $department_id->setLabel("Department");
            $department_id->setRequired(true);
			$department_id->setRegisterInArrayValidator(false);
			$department_id->addMultiOptions(array('' => 'Select Department'));   
			$department_id->addValidator('NotEmpty', false, array('messages' => 'Please select department.'));

			
        /*}
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
        }

		$l1_manager = new Zend_Form_Element_Checkbox('l1_manager');
		$l1_manager->setLabel("L1 Manager");
		$l1_manager->setAttrib('disabled','disabled'); */
       
		$l2_manager = new Zend_Form_Element_Select('l2_manager');
		$l2_manager->setLabel("L2 Manager");
        $l2_manager->addMultiOptions(array('' => 'Select L2 Manager'));
		$l2_manager->setRegisterInArrayValidator(false);
		$l2_manager->setRequired(true);
		$l2_manager->addValidator('NotEmpty', false, array('messages' => 'Please select l2 manager.'));		

		$hr_manager = new Zend_Form_Element_Select('hr_manager');
		$hr_manager->setLabel("HR Manager");
        $hr_manager->addMultiOptions(array('' => 'Select HR Manager'));
		$hr_manager->setRegisterInArrayValidator(false);
		$hr_manager->setRequired(true);
		$hr_manager->addValidator('NotEmpty', false, array('messages' => 'Please select hr manager.'));		

		$sys_admin = new Zend_Form_Element_Select('sys_admin');
		$sys_admin->setLabel("System Admin");
        $sys_admin->setAttrib('class', 'selectoption');
        $sys_admin->addMultiOptions(array('' => 'Select System Admin'));
		$sys_admin->setRegisterInArrayValidator(false);
		
		$general_admin = new Zend_Form_Element_Select('general_admin');
		$general_admin->setLabel("General Admin");
        $general_admin->setAttrib('class', 'selectoption');
        $general_admin->addMultiOptions(array('' => 'Select General Admin'));
		$general_admin->setRegisterInArrayValidator(false);
		

		$finance_manager = new Zend_Form_Element_Select('finance_manager');
		$finance_manager->setLabel("Finance Manager");
        $finance_manager->setAttrib('class', 'selectoption');
        $finance_manager->addMultiOptions(array('' => 'Select Finance Manager'));
		$finance_manager->setRegisterInArrayValidator(false);
		
		$notice_period = new Zend_Form_Element_Text('notice_period');
		$notice_period->setRequired(true);
		$notice_period->setAttrib('maxLength',3);
		$notice_period->addValidator('NotEmpty', false, array('messages' => 'Please enter notice period.'));	
		$notice_period->addFilter(new Zend_Filter_StringTrim());
		
		$notice_period->addValidator("regex",true,array(
                           'pattern'=>'/^([0-9]+?)+$/',
                           
                           'messages'=>array(
                                   'regexNotMatch'=>'Please enter only numbers.'
                           )
        ));
		$notice_period->addValidator("greaterThan",true,array(
                           'min'=>0,                                           
                           'messages'=>array(
                                   'notGreaterThan'=>'Notice period cannot be zero.'
                           )
        ));			
		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitbutton');
		$submitBtn->setLabel('Save');

		$this->addElements(array($businessunit_id,$department_id, $hr_manager,$l2_manager,  $sys_admin, $general_admin, $finance_manager,$notice_period,$submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>