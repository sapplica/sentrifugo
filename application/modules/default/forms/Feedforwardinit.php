<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_Form_Feedforwardinit extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'feedforwardinit');

        $id = new Zend_Form_Element_Hidden('id');
        $postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
        
        $appraisal_mode = new Zend_Form_Element_Select('appraisal_mode');
        $appraisal_mode->setLabel("Appraisal");
       	$appraisal_mode->setMultiOptions(array(''=>'Select Appraisal'));
        $appraisal_mode->setAttrib('class', 'selectoption');
        $appraisal_mode->setRequired(true);
        $appraisal_mode->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal.'));
        
		$status = new Zend_Form_Element_Select('status');
        $status->setLabel("Appraisal Status");
        $status->setAttrib('class', 'selectoption');
        $status->setMultiOptions(array('1'=>'Open'));//,'2' => 'Close'
        $status->setRegisterInArrayValidator(false);
        $status->setRequired(true);
        $status->addValidator('NotEmpty', false, array('messages' => 'Please select status.'));
       
        $employee_name_view = new Zend_Form_Element_Radio('employee_name_view');
		$employee_name_view->setLabel("Employee Details");
        $employee_name_view->addMultiOptions(array('1' => 'Show','0' => 'Hide',));
		$employee_name_view->setSeparator('');
		$employee_name_view->setValue(0);    									   
		$employee_name_view->setRegisterInArrayValidator(false);

		$enable_to = new Zend_Form_Element_MultiCheckbox('enable_to');
		$enable_to->setLabel("Enable To");
        $enable_to->addMultiOptions(array('0' => 'Appraisal Employees','1' => 'All Employees',));
		$enable_to->setSeparator('');
		$enable_to->setValue(0);
		$enable_to->setRequired(true);    									   
		$enable_to->setRegisterInArrayValidator(false);
		$enable_to->addValidator('NotEmpty', false, array('messages' => 'Please check enable to.'));
		
        $ff_due_date = new Zend_Form_Element_Text('ff_due_date');
        $ff_due_date->setLabel("Due Date");
        $ff_due_date->setOptions(array('class' => 'brdr_none')); 
        $ff_due_date->setRequired(true);
        $ff_due_date->addValidator('NotEmpty', false, array('messages' => 'Please select due date.'));
        
        $save = new Zend_Form_Element_Submit('submit');
        $save->setAttrib('id', 'submitbutton');
        $save->setLabel('Save & Initialize');

        $save_later = new Zend_Form_Element_Submit('submit');
        $save_later->setAttrib('id', 'submitbutton1');
        $save_later->setLabel('Save & Initialize Later');
        
        $this->addElements(array($id,$appraisal_mode,$status,$employee_name_view,$ff_due_date,$save,$save_later,$enable_to));
        $this->setElementDecorators(array('ViewHelper'));
    }
}