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

class Default_Form_Usermanagement extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'usermanagement');
		
        $company_id = new Zend_Form_Element_Hidden("company_id");
        $id = new Zend_Form_Element_Hidden("id");
        $id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
		$employeeId = new Zend_Form_Element_Select("employeeId");
        $employeeId->setRegisterInArrayValidator(false);        
        $employeeId->setLabel("User Type");        
        $employeeId->setAttrib("class", "formDataElement");
        if($id_val == '')
        {
            $employeeId->setRequired(true);        
            $employeeId->addValidator('NotEmpty', false, array('messages' => 'Please configure identity codes.'));
        }
		/*$userfullname = new Zend_Form_Element_Text("userfullname");
        $userfullname->setLabel("Full Name");	
        $userfullname->setAttrib("class", "formDataElement");
        $userfullname->setRequired("true");
        $userfullname->addValidator('NotEmpty', false, array('messages' => 'Please enter full name.'));
        $userfullname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid full name.'
                           )
        	));*/
        	
       $firstname = new Zend_Form_Element_Text("firstname");
        $firstname->setLabel("First Name");	
        $firstname->setAttrib("class", "formDataElement");
        $firstname->setRequired("true");
        $firstname->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));
        $firstname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid first name.'
                           )
        	));

       $lastname = new Zend_Form_Element_Text("lastname");
        $lastname->setLabel("Last Name");	
        $lastname->setAttrib("class", "formDataElement");
        $lastname->setRequired("true");
        $lastname->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));
        $lastname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid last name.'
                           )
        	)); 	
                
	$entrycomments = new Zend_Form_Element_Textarea("entrycomments");
        $entrycomments->setLabel("Comments")
                      ->setAttrib("COLS", "40")
                      ->setAttrib("ROWS", "4");        
        $entrycomments->setAttrib("class", "formDataElement");
					
	$selecteddate = new Zend_Form_Element_Text("selecteddate");     
        $selecteddate->setLabel("Date of Selection");			
        $selecteddate->setAttrib('readonly', 'readonly');
		$selecteddate->setAttrib('onfocus', 'this.blur()');

	$emailaddress = new Zend_Form_Element_Text("emailaddress");                        
        $emailaddress->setRequired("true");
        $emailaddress->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
        
        
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
                                                        'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" ',                                                                        					        						
        						)));
        $emailaddress->getValidator('Db_NoRecordExists')->setMessage('Email already exists.');
        
        
        
	$act_inact = new Zend_Form_Element_Hidden("act_inact");       
        
	$empreasonlocked = new Zend_Form_Element_Textarea("empreasonlocked");
        $empreasonlocked->setLabel("Reason Locked")
                        ->setAttrib("COLS", "40")
                        ->setAttrib("ROWS", "4");		
        $empreasonlocked->setAttrib("class", "formDataElement");

		$emplockeddate = new Zend_Form_Element_Text("emplockeddate");
        $emplockeddate->setLabel("Date Locked");
        $emplockeddate->setAttrib('readonly', 'readonly');
		$emplockeddate->setAttrib('onfocus', 'this.blur()');
        
        $temp_lock = Zend_Controller_Front::getInstance()->getRequest()->getParam('emptemplock',null);
        $temp_lock_date = Zend_Controller_Front::getInstance()->getRequest()->getParam('emplockeddate',null);
       

	$emprole = new Zend_Form_Element_Select("emprole");        
        $emprole->setRegisterInArrayValidator(false);
        $emprole->setRequired("true");
        $emprole->setLabel("Assign Role");	
        $emprole->setAttrib("class", "formDataElement");
        $emprole->addValidator('NotEmpty', false, array('messages' => 'Please select role.'));

	$submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Save");  
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib("class", "formSubmitButton");
        $url = "'usermanagement/saveupdate/format/json'";
        $dialogMsg = "''";
        $toggleDivId = "''";
        $jsFunction = "'redirecttocontroller(\'usermanagement\');'";;	  
        
        $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
                        ));

        $this->addElements(array($id,$submit,$company_id,$employeeId,$firstname,$lastname,
                                $entrycomments,$selecteddate,$emailaddress,$act_inact,
                                $empreasonlocked,$emplockeddate,$emprole,));
        $this->setElementDecorators(array('ViewHelper')); 
    }//end of init function.
}//end of class