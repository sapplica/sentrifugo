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

class Default_Form_ethniccode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('action', BASE_URL.'ethniccode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'ethniccode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$ethniccode = new Zend_Form_Element_Text('ethniccode');
        $ethniccode->setAttrib('maxLength', 20);
        
        
        $ethniccode->setRequired(true);
        $ethniccode->addValidator('NotEmpty', false, array('messages' => 'Please enter ethnic short code.'));  
        $ethniccode->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid ethnic short code.'
								 )
							 )
						 )
					 )); 
		$ethniccode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_ethniccode',
                                                        'field'=>'ethniccode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $ethniccode->getValidator('Db_NoRecordExists')->setMessage('ethnic short code already exists.'); 	
		
		$ethnicname = new Zend_Form_Element_Text('ethnicname');
        $ethnicname->setAttrib('maxLength', 20);
        
        
        $ethnicname->setRequired(true);
        $ethnicname->addValidator('NotEmpty', false, array('messages' => 'Please enter ethnic code.'));  
        $ethnicname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid ethnic code.'
								 )
							 )
						 )
					 )); 
		$ethnicname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_ethniccode',
                                                        'field'=>'ethnicname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $ethnicname->getValidator('Db_NoRecordExists')->setMessage('ethnic code already exists.'); 	
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		

        $submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'ethniccode/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'ethniccode\');'";;
		 


		 $this->addElements(array($id,$ethniccode,$ethnicname,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}