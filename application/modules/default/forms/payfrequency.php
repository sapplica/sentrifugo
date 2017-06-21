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

class Default_Form_payfrequency extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'payfrequency/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'payfrequency');


        $id = new Zend_Form_Element_Hidden('id');
		
		$freqtype = new Zend_Form_Element_Text('freqtype');
        $freqtype->setAttrib('maxLength', 20);
        $freqtype->setLabel("Pay Frequency");
        $freqtype->setRequired(true);
        $freqtype->addValidator('NotEmpty', false, array('messages' => 'Please enter pay frequency type.'));  
		$freqtype->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid pay frequency type.'
								   )
					));	
		$freqtype->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_payfrequency',
                                                        'field'=>'freqtype',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $freqtype->getValidator('Db_NoRecordExists')->setMessage('Pay frequency type already exists.');
        
        $freqshortcode = new Zend_Form_Element_Text('freqcode');
        $freqshortcode->setLabel("Short Code");
        $freqshortcode->setAttrib('maxLength', 20);
        $freqshortcode->setRequired(true);
        $freqshortcode->addValidator('NotEmpty', false, array('messages' => 'Please enter pay frequency short code.'));  
		$freqshortcode->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9]*$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid pay frequency short code.'
                           )
        	));
        $freqshortcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_payfrequency',
                                                        'field'=>'freqcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $freqshortcode->getValidator('Db_NoRecordExists')->setMessage('Pay frequency short code already exists.');
		
		
	
		$description = new Zend_Form_Element_Textarea('freqdescription');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$freqtype,$freqshortcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}