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

class Default_Form_Roles extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'roles');

        $id = new Zend_Form_Element_Hidden('id');
		
        $rolename = new Zend_Form_Element_Text('rolename');
        $rolename->setAttrib('maxLength', 50);
        $rolename->setAttrib('title', 'Role name');        
        $rolename->addFilter(new Zend_Filter_StringTrim());
        $rolename->setRequired(true);
        $rolename->addValidator('NotEmpty', false, array('messages' => 'Please enter role name.'));  
        $rolename->addValidator("regex",true,array(
                           
                           'pattern'=>'/^[a-zA-Z0-9 ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid role name.'
                           )
        	));
        $rolename->addValidator(new Zend_Validate_Db_NoRecordExists(
        						array('table' => 'main_roles',
        						'field' => 'rolename',
                              'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive!=0',)));
        $rolename->getValidator('Db_NoRecordExists')->setMessage('Role name already exists.');
        $rolename->addValidators(array(array('StringLength',false,
                                array(
                                        'min' => 3,
                                        'max' => 50,
                                        'messages' => array(
                                            Zend_Validate_StringLength::TOO_LONG => 'Role name must contain at most %max% characters.',
                                            Zend_Validate_StringLength::TOO_SHORT =>'Role name must contain at least %min% characters.')))));
        
	$roletype = new Zend_Form_Element_Text('roletype');
        $roletype->setRequired(true);
        $roletype->setAttrib('maxLength', 25);
        $roletype->setAttrib('title', 'Role type');        
        $roletype->addFilter(new Zend_Filter_StringTrim());
        $roletype->addValidator('NotEmpty', false, array('messages' => 'Please enter role type.'));  
        $roletype->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z]+?$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphabets.'
                           )
        	));
        $roletype->addValidator(new Zend_Validate_Db_NoRecordExists(
        						array('table' => 'main_roles',
        						'field' => 'roletype',
                                 'exclude'=>'id != "'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive != 0',
        						)));
        $roletype->getValidator('Db_NoRecordExists')->setMessage('Role type already exists.');
        $roletype->addValidators(array(array('StringLength',false,
                                array(
                                        'min' => 3,
                                        'max' => 25,
                                        'messages' => array(
                                            Zend_Validate_StringLength::TOO_LONG => 'Role type must contain at most %max% characters.',
                                            Zend_Validate_StringLength::TOO_SHORT =>'Role type must contain at least %min% characters.')))));
        
        
        $roledescription = new Zend_Form_Element_Textarea('roledescription');
        $roledescription->setAttrib('rows', 10);
        $roledescription->setAttrib('cols', 50);
        $roledescription ->setAttrib('maxlength', '100');
        $roledescription->setAttrib('title', 'Role description');        
        

        $levelid = new Zend_Form_Element_Hidden('levelid');               
        $levelid->addFilter(new Zend_Filter_StringTrim());
        $levelid->setRequired(true);
        $levelid->addValidator('NotEmpty', false, array('messages' => 'Please select level.'));          
        
        $istimeActive = Zend_Controller_Front::getInstance()->getRequest()->getParam('istimeactive');
        $prev_cnt = new Zend_Form_Element_Hidden('prev_cnt');
        $prev_cnt->setRequired(true);
        if($istimeActive)
        	$prev_cnt->addValidator('NotEmpty', false, array('messages' => 'Please select privileges other than time management.'));
        else	
        	$prev_cnt->addValidator('NotEmpty', false, array('messages' => 'Please select privileges.'));  
        
        $submit = new Zend_Form_Element_Submit('submit');
        
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $url = "'roles/saveupdate/format/json'";
        $dialogMsg = "''";
        $toggleDivId = "''";
        $jsFunction = "'redirecttocontroller(\'roles\');'";;
		 
        $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
                        ));

        $this->addElements(array($id,$rolename,$roletype,$roledescription,$levelid,$submit,$prev_cnt));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}