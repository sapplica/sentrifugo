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

class Default_Form_Announcements extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'announcements');
		$this->setAttrib('name', 'announcements');

        $id = new Zend_Form_Element_Hidden('id');
		
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
       /*  if($loginuserGroup == HR_GROUP)
        	$businessunit_id = new Zend_Form_Element_Select("businessunit_id");
        else */
        $businessunit_id = new Zend_Form_Element_Multiselect("businessunit_id");
        $businessunit_id->setLabel("Business Units");
        $businessunit_id->setRegisterInArrayValidator(false);
        $businessunit_id->setRequired(true);
        $businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
        
        $department_id = new Zend_Form_Element_Multiselect("department_id");
        $department_id->setLabel("Departments");  
        $department_id->setRegisterInArrayValidator(false);      
        $department_id->setRequired(true);
        $department_id->addValidator('NotEmpty', false, array('messages' => 'Please select department.'));
        
		$title = new Zend_Form_Element_Text("title");
		$title->setLabel("Title");
		$title->setAttrib('maxLength', 100);
		$title->addFilter(new Zend_Filter_StringTrim());
		$title->setRequired(true);
        $title->addValidator('NotEmpty', false, array('messages' => 'Please enter title.'));
		$title->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid title.'
                           )
        	));
        $title->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_announcements',
	                                                     'field'=>'title',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	                                                      ) ) );
		// Validation for duplicate name is not required	                                                      
		$title->getValidator('Db_NoRecordExists')->setMessage('Title name already exists.');	

		$description = new Zend_Form_Element_Textarea('post_description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description->setRequired(true);
        $description->addValidator('NotEmpty', false, array('messages' => 'Please enter description.'));

		$this->addElements(array($id,$businessunit_id,$department_id,$title,$description));
    	$this->setElementDecorators(array('ViewHelper')); 
	}
}