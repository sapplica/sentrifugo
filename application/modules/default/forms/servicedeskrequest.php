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

class Default_Form_servicedeskrequest extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'servicedeskrequests');


        $id = new Zend_Form_Element_Hidden('id');
		
		$servicedeskdepartment = new Zend_Form_Element_Select('service_desk_id');
		$servicedeskdepartment->setLabel("Category");
        $servicedeskdepartment->setAttrib('class', 'selectoption');
        $servicedeskdepartment->addMultiOption('','Select category');
        $servicedeskdepartment->setRegisterInArrayValidator(false);
        $servicedeskdepartment->setRequired(true);
		$servicedeskdepartment->addValidator('NotEmpty', false, array('messages' => 'Please select category.'));
		
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		if($postid !='')
		{
			$servicedeskrequest = new Zend_Form_Element_Text("service_request_name");
			$servicedeskrequest->setLabel("Request Type");
			$servicedeskrequest->setAttrib('maxLength', 30);
			$servicedeskrequest->addFilter(new Zend_Filter_StringTrim());
			$servicedeskrequest->setRequired(true);
	        $servicedeskrequest->addValidator('NotEmpty', false, array('messages' => 'Please enter request type.'));
			$servicedeskrequest->addValidator("regex",true,array(                           
	                           'pattern'=>'/^[a-zA-Z0-9\- ]+$/',
	                           'messages'=>array(
	                               'regexNotMatch'=>'Please enter valid request type.'
	                           )
	        	));
	        $servicedeskrequest->addValidator(new Zend_Validate_Db_NoRecordExists(
		                                            array(  'table'=>'main_sd_reqtypes',
		                                                     'field'=>'service_request_name',
		                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND service_desk_id="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('service_desk_id').'" AND isactive=1',    
		
		                                                      ) ) );
			$servicedeskrequest->getValidator('Db_NoRecordExists')->setMessage('Request type already exists for the category.');	
	   	
			$description = new Zend_Form_Element_Textarea('description');
			$description->setLabel("Description");
	        $description->setAttrib('rows', 10);
	        $description->setAttrib('cols', 50);
			$description ->setAttrib('maxlength', '200');
			
			$submit = new Zend_Form_Element_Submit('submitbutton');
			$submit->setAttrib('id', 'submitbutton');
			$submit->setLabel('Update');
		}

		
		$submitadd = new Zend_Form_Element_Button('submitbutton');
		$submitadd->setAttrib('id', 'submitbuttons');
		$submitadd->setAttrib('onclick', 'validaterequestsonsubmit(this)');
		$submitadd->setLabel('Save');
		
		if($postid !='')
			 $this->addElements(array($id,$servicedeskdepartment,$servicedeskrequest,$description,$submit));
	    else		 
		 	$this->addElements(array($id,$servicedeskdepartment,$submitadd));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}