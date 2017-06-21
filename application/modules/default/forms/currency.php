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

class Default_Form_currency extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'currency/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'currency');


        $id = new Zend_Form_Element_Hidden('id');
		
		$currencyname = new Zend_Form_Element_Text('currencyname');
        $currencyname->setAttrib('maxLength', 20);
        
        $currencyname->addFilter(new Zend_Filter_StringTrim());
        $currencyname->setRequired(true);
        $currencyname->addValidator('NotEmpty', false, array('messages' => 'Please enter currency.'));  
        
		$currencyname->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 
        
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid currency.'
                           )
        	));	
		$currencyname->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_currency',
                                                        'field'=>'currencyname',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $currencyname->getValidator('Db_NoRecordExists')->setMessage('Currency already exists.');
		
		$currencycode = new Zend_Form_Element_Text('currencycode');
        $currencycode->setAttrib('maxLength', 20);
        
        $currencycode->addFilter(new Zend_Filter_StringTrim());
        $currencycode->setRequired(true);
        $currencycode->addValidator('NotEmpty', false, array('messages' => 'Please enter currency code.'));  
        
        $currencycode->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9]*$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid currency code.'
                           )
        	));				
		
		$currencycode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_currency',
                                                        'field'=>'currencycode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $currencycode->getValidator('Db_NoRecordExists')->setMessage('Currency code already exists.');
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		

        $submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'gender/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'gender\');'";;
		 		 	

		 $this->addElements(array($id,$currencyname,$currencycode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}