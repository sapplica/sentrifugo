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

class Default_Form_empholidays extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empholidays');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
		
		$holiday_group_name = new Zend_Form_Element_Text('holiday_group_name');
        $holiday_group_name->setAttrib('readonly', 'true');	
		$holiday_group_name->setAttrib('onfocus', 'this.blur()');  
			
		$holiday_group = new Zend_Form_Element_Select('holiday_group');
        $holiday_group->setRegisterInArrayValidator(false);
		$holiday_group->addMultiOption('','Select Holiday Group');
		$holiday_group->setAttrib('onchange', 'displayHolidayDates(this)');
		$holiday_group->setRequired(true);
		$holiday_group->addValidator('NotEmpty', false, array('messages' => 'Please select holiday group.'));
			
						
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$holiday_group_name,$holiday_group,$submit));
        $this->setElementDecorators(array('ViewHelper'));
     		
	}
}