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

class Default_Form_employmentstatus extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'employmentstatus/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'employmentstatus');


        $id = new Zend_Form_Element_Hidden('id');
		
		$workcode = new Zend_Form_Element_Text('workcode');
        $workcode->setAttrib('maxLength', 20);
        
        $workcode->setRequired(true);
        $workcode->addValidator('NotEmpty', false, array('messages' => 'Please enter work short code.')); 
        $workcode->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid work short code.'
								   )
					)); 		
      
		$workcodename = new Zend_Form_Element_Select('workcodename');
        $workcodename->setAttrib('class', 'selectoption');
        $workcodename->setRegisterInArrayValidator(false);
        $workcodename->setRequired(true);
		$workcodename->addValidator('NotEmpty', false, array('messages' => 'Please select work code.'));
		

		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$workcode,$workcodename,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}