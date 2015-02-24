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

class Default_Form_systempreference extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'systempreference');


        $id = new Zend_Form_Element_Hidden('id');
		$dateformatid = new Zend_Form_Element_Select('dateformatid');
        $dateformatid->setAttrib('class', 'selectoption');
        $dateformatid->setRegisterInArrayValidator(false);
        $dateformatid->setRequired(true);
		$dateformatid->addValidator('NotEmpty', false, array('messages' => 'Please select date format.'));
		
		$timeformatid = new Zend_Form_Element_Select('timeformatid');
        $timeformatid->setAttrib('class', 'selectoption');
        $timeformatid->setRegisterInArrayValidator(false);
        $timeformatid->setRequired(true);
        $timeformatid->addValidator('NotEmpty', false, array('messages' => 'Please select time format.')); 

        $timezoneid = new Zend_Form_Element_Select('timezoneid');
        $timezoneid->setAttrib('class', 'selectoption');
        $timezoneid->setRegisterInArrayValidator(false);
        $timezoneid->setRequired(true);
        $timezoneid->addValidator('NotEmpty', false, array('messages' => 'Please select time zone preference.'));	
		$timezoneid->addMultiOption('','Select Time zone');
		$timezoneModal = new Default_Model_Timezone();
	    	$timezoneData = $timezoneModal->fetchAll('isactive=1','timezone')->toArray();;
			foreach ($timezoneData as $data){
		$timezoneid->addMultiOption($data['id'],$data['timezone'].' ['.$data['timezone_abbr'].']');
	    	}

        $currencyid = new Zend_Form_Element_Select('currencyid');
        $currencyid->setAttrib('class', 'selectoption');
        $currencyid->setRegisterInArrayValidator(false);
        $currencyid->setRequired(true);
		$currencyid->addValidator('NotEmpty', false, array('messages' => 'Please select currency.'));	

        $passwordid = new Zend_Form_Element_Select('passwordid');
        $passwordid->setAttrib('class', 'selectoption');
		$passwordid->setAttrib('onchange', 'displayPasswordDesc(this)');
        $passwordid->setRegisterInArrayValidator(false);
        $passwordid->setRequired(true);
		$passwordid->addValidator('NotEmpty', false, array('messages' => 'Please select default password.'));  		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($timezoneid,$id,$dateformatid,$timeformatid,$currencyid,$passwordid,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}