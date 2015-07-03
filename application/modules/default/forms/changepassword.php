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

class Default_Form_changepassword extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAction(BASE_URL.'dashboard/editpassword');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'editpassword');


        $id = new Zend_Form_Element_Hidden('id');
		
		$oldPassword = new Zend_Form_Element_Password('password');
    	$oldPassword->setAttrib('size',20); 
    	$oldPassword->addValidator('NotEmpty', false, array('messages' => 'Please enter current password.'));
    	                   
                          
    	$oldPassword->setAttrib('maxLength', 15);
    	$oldPassword->setRequired(true);
		$oldPassword->addFilters(array('StringTrim'));
    	
    	 
        
    	$newPassword = new Zend_Form_Element_Password('newpassword');
    	$newPassword->setAttrib('size',20);  
        $newPassword->setAttrib('maxLength',15);  		
       	$newPassword->setRequired(true);
       	$newPassword->addValidator('NotEmpty', false, array('messages' => 'Please enter new password.'));  
    	$newPassword->addValidator('stringLength', true, array('min' => 6, 'max' => 15,
    	'messages' => array(Zend_Validate_StringLength::TOO_LONG => 'The password cannot be more than 15 characters.',
                        Zend_Validate_StringLength::TOO_SHORT => 'New password should be atleast 6 characters long.') ));
                         
    	$newPassword->addFilters(array('StringTrim'));
    	
        
    	$newPasswordAgain = new Zend_Form_Element_Password('passwordagain');
    	$newPasswordAgain->setAttrib('size',20);
        $newPasswordAgain->setAttrib('maxLength',15); 		
        $newPasswordAgain->setRequired(true);
    	$newPasswordAgain->addValidator('NotEmpty', false, array('messages' => 'Please&nbsp;enter&nbsp;confirm&nbsp;password.'));
    	$newPasswordAgain->addValidator('stringLength', true, array('min' => 6, 'max' => 15,
    	'messages' => array(Zend_Validate_StringLength::TOO_LONG => 'The password cannot be more than 15 characters.',
                        Zend_Validate_StringLength::TOO_SHORT => 'Confirm password should be atleast 6 characters long.') ));
                        
        
         $newPasswordAgain->addFilters(array('StringTrim'));						   

         $submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'dashboard/editpassword/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";
		 

		 $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		));

		 $this->addElements(array($id,$oldPassword,$newPassword,$newPasswordAgain,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}