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

class Default_Form_Organisationheadchange extends Zend_Form
{
	public function init()
	{
	    $this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setAttrib('name','organisationinfo');
		$this->setAttrib('action',BASE_URL.'organisationinfo/addorghead');		

        $id = new Zend_Form_Element_Hidden('id');	
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		
        $firstname_orghead = new Zend_Form_Element_Text('firstname_orghead');
        $firstname_orghead->setAttrib('maxLength', 50);
        $firstname_orghead->addFilter(new Zend_Filter_StringTrim());
		$firstname_orghead->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               
							   'regexNotMatch'=>'Please enter valid first name.'
                           )
        	));
        $lastname_orghead = new Zend_Form_Element_Text('lastname_orghead');
        $lastname_orghead->setAttrib('maxLength', 50);
        $lastname_orghead->addFilter(new Zend_Filter_StringTrim());
		$lastname_orghead->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               
							   'regexNotMatch'=>'Please enter valid last name.'
                           )
        	));
			
		$designation = new Zend_Form_Element_Text('designation');
        $designation->setAttrib('maxLength', 50);
        $designation->addFilter(new Zend_Filter_StringTrim());
        
        
		$designation->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(

							   'regexNotMatch'=>'Please enter valid designation.'
                           )
        	));
		
		$employeeId = new Zend_Form_Element_Text("employeeId");
        $employeeId->setRequired("true");
        $employeeId->setLabel("Employee Code");        
        $employeeId->setAttrib("class", "formDataElement");
        $employeeId->setAttrib("readonly", "readonly");
		$employeeId->setAttrib('onfocus', 'this.blur()');
		$employeeId->addValidator('NotEmpty', false, array('messages' => 'Identity codes are not configured yet.'));

        //for emp id
        $employeeNumId = new Zend_Form_Element_Text("employeeNumId");
        //$employeeNumId->setRequired("true");
        $employeeNumId->setLabel("Employee Id");
        $employeeNumId->setAttrib('maxLength', 5);       
        $employeeNumId->setAttrib("class", "formDataElement");
        //$employeeNumId->addValidator('NotEmpty', false, array('messages' => 'Please enter the Employee Id.'));
        $employeeNumId->addValidator("regex",true,array(                          
                   'pattern'=>'/^[0-9]+$/',
                   'messages'=>array(
                       'regexNotMatch'=>'Please enter only numbers.'
                   )));   
		
		/*$employeeId = new Zend_Form_Element_Text("employeeId");
		$employeeId->setLabel("Employee ID");        
		$employeeId->setAttrib("class", "formDataElement");
		$employeeId->setAttrib("readonly", "readonly");
		$employeeId->setAttrib('onfocus', 'this.blur()');		*/
		
		
		$prefix_id = new Zend_Form_Element_Select('prefix_id');
		$prefix_id->setLabel("Prefix");
		$prefix_id->setRegisterInArrayValidator(false);	
		
		$emprole = new Zend_Form_Element_Select("emprole");        
		$emprole->setRegisterInArrayValidator(false);
		$emprole->setLabel("Role");
		$emprole->setAttrib("class", "formDataElement");
		
		$emailaddress = new Zend_Form_Element_Text("emailaddress");
		
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
															'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id',0).'" and isactive!=0'
															)));
		$emailaddress->getValidator('Db_NoRecordExists')->setMessage('Email already exists.');
		
		$jobtitle = new Zend_Form_Element_Select('jobtitle_id');
		$jobtitle->setLabel("Job Title");
		$jobtitle->addMultiOption('','Select Job Title');
		$jobtitle->setAttrib('onchange', 'displayPositions(this,"position_id","")');
		$jobtitle->setRegisterInArrayValidator(false);	
		
		$position = new Zend_Form_Element_Select('position_id');
		$position->setLabel("Position");
		$position->addMultiOption('','Select Position');
		$position->setRegisterInArrayValidator(false);	
		
		$date_of_joining = new ZendX_JQuery_Form_Element_DatePicker('date_of_joining_head');
        $date_of_joining->setLabel("Date of Joining");
		$date_of_joining->setOptions(array('class' => 'brdr_none'));	
		$date_of_joining->setAttrib('readonly', 'true');
		$date_of_joining->setAttrib('onfocus', 'this.blur()');
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		 $this->addElements(array($id,$description,$lastname_orghead,$firstname_orghead,$designation,$employeeId,$prefix_id,$emprole,$emailaddress,$jobtitle,$position,$date_of_joining,$submit,$employeeNumId));//$email,$secondaryemail,
		 
		 $this->setElementDecorators(array('ViewHelper')); 
		 $this->setElementDecorators(array('File'),array('org_image'));
		 $this->setElementDecorators(array('UiWidgetElement',),array('org_startdate','date_of_joining_head'));
		 
	}
}