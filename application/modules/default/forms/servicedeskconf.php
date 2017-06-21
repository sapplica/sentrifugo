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

class Default_Form_servicedeskconf extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		//$this->setAttrib('action',BASE_URL.'language/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'servicedeskrequests');


        $id = new Zend_Form_Element_Hidden('id');
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		$businessunit_id = new Zend_Form_Element_Select('businessunit_id');
		$businessunit_id->setLabel("Business Unit");
        $businessunit_id->setAttrib('class', 'selectoption');
		if($postid == '')
		{                    
			$businessunit_id->setAttrib('onchange', 'displayemployees(this)');
			$bunitModel = new Default_Model_Businessunits();
			$bunitdata = $bunitModel->fetchAll('isactive=1','unitname');
	    	$businessunit_id->addMultiOptions(array(''=>'Select Business unit',
                                            '0'=>'No Business Unit'));
                
	    	foreach ($bunitdata->toArray() as $data){
				$businessunit_id->addMultiOption($data['id'],$data['unitname']);
	    	}
		}else
		{
			$businessunit_id->addMultiOptions(array(''=>'Select Business unit'));
		}	
        $businessunit_id->setRegisterInArrayValidator(false);
        $businessunit_id->setRequired(true);
		$businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
		
		
		$department_id = new Zend_Form_Element_Select('department_id');
		$department_id->setLabel("Department");
        $department_id->setAttrib('class', 'selectoption');
        $department_id->addMultiOption('','Select Department');
        if($postid == '')
	        $department_id->setAttrib('onchange', 'displayemployees(this)');
		$department_id->setRegisterInArrayValidator(false);
		
		$service_desk_flag = new Zend_Form_Element_Radio('service_desk_flag');
		$service_desk_flag->setLabel("Applicability");
        $service_desk_flag->setAttrib('onclick', 'changeimplementation(this)');
        $service_desk_flag->addMultiOptions(array(
										        '1' => 'Business unit wise',
										        '0' => 'Department wise',
    									   ));
		$service_desk_flag->setSeparator('');
		$service_desk_flag->setValue(1);    									   
		$service_desk_flag->setRegisterInArrayValidator(false);
		$service_desk_flag->setRequired(true);
		$service_desk_flag->addValidator('NotEmpty', false, array('messages' => 'Please select applicability.'));
		
		$request_for = new Zend_Form_Element_Select('request_for');
		$request_for->setLabel("Request For");
		$request_for->setRegisterInArrayValidator(false);
		$request_for->setRequired(true);
		$request_for->addValidator('NotEmpty', false, array('messages' => 'Please select request for.'));
		$request_for->setAttrib('onchange', 'displayemployees(this)');
		$request_for->addMultiOptions(array(
				'1' => 'Service',
				'2' => 'Asset',
				
		));
		
		
		$service_desk_id = new Zend_Form_Element_Multiselect('service_desk_id');
		$service_desk_id->setLabel("Category");
        $service_desk_id->setAttrib('class', 'selectoption');
        //$service_desk_id->addMultiOption('','Select category');
		$service_desk_id->setRegisterInArrayValidator(false);
		$service_desk_id->setRequired(true);
		$service_desk_id->addValidator('NotEmpty', false, array('messages' => 'Please select category.'));
          
		
		$request_recievers = new Zend_Form_Element_Multiselect('request_recievers');
		$request_recievers->setLabel("Executors");
        $request_recievers->setAttrib('class', 'selectoption');
        $request_recievers->setRegisterInArrayValidator(false);
        $request_recievers->setRequired(true);
		$request_recievers->addValidator('NotEmpty', false, array('messages' => 'Please select executor.'));
		
		$approvingauthority = new Zend_Form_Element_Select('approvingauthority');
		$approvingauthority->setLabel("No. of Approvers");
        $approvingauthority->setAttrib('class', 'selectoption');
        $approvingauthority->setAttrib('onchange', 'displayapprovingauthority(this)');
        $approvingauthority->addMultiOptions(array(
        										'' => 'Select no. of approvers',	
										        '1' => '1',
										        '2' => '2',
        										'3' => '3',				
    									   ));
		$approvingauthority->setRegisterInArrayValidator(false);
		$approvingauthority->setRequired(true);
		$approvingauthority->addValidator('NotEmpty', false, array('messages' => 'Please select no. of approvers.'));
		
		$approver_1 = new Zend_Form_Element_Select('approver_1');
		$approver_1->setLabel("Approver 1");
        $approver_1->setAttrib('class', 'selectoption');
        $approver_1->addMultiOption('','Select Approver 1');
        $approver_1->setAttrib('onchange', 'displayapprovingauthority(this)');
		$approver_1->setRegisterInArrayValidator(false);
		
		$approver_2 = new Zend_Form_Element_Select('approver_2');
		$approver_2->setLabel("Approver 2");
        $approver_2->setAttrib('class', 'selectoption');
        $approver_2->addMultiOption('','Select Approver 2');
        $approver_2->setAttrib('onchange', 'displayapprovingauthority(this)');
		$approver_2->setRegisterInArrayValidator(false);
		
		$approver_3 = new Zend_Form_Element_Select('approver_3');
		$approver_3->setLabel("Approver 3");
        $approver_3->setAttrib('class', 'selectoption');
        $approver_3->addMultiOption('','Select Approver 3');
		$approver_3->setRegisterInArrayValidator(false);
		
		$cc_mail_recievers = new Zend_Form_Element_Multiselect('cc_mail_recievers');
		$cc_mail_recievers->setLabel("Request Viewers");
        $cc_mail_recievers->setAttrib('class', 'selectoption');
        $cc_mail_recievers->setRegisterInArrayValidator(false);
        
        $attachment = new Zend_Form_Element_Radio('attachment');
		$attachment->setLabel("Attachment");
        $attachment->addMultiOptions(array(
										        '1' => 'Yes',
										        '0' => 'No',
    									   ));
		$attachment->setSeparator('');    
		$attachment->setValue(0);									   
		$attachment->setRegisterInArrayValidator(false);
   	
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$businessunit_id,$department_id,$description,$service_desk_flag,$service_desk_id,$request_recievers,$approvingauthority,$approver_1,$approver_2,$approver_3,$cc_mail_recievers,$attachment,$request_for,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
		
	}
}