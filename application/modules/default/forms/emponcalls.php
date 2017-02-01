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

class Default_Form_emponcalls extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'emponcalls');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
			
		
		$emp_oncall_limit = new Zend_Form_Element_Text('oncall_limit');
        $emp_oncall_limit->setAttrib('maxLength', 3);
        $emp_oncall_limit->addFilter(new Zend_Filter_StringTrim());
		$emp_oncall_limit->setRequired(true);
        $emp_oncall_limit->addValidator('NotEmpty', false, array('messages' => 'Please enter oncall limit for current year.'));
		
		$emp_oncall_limit->addValidator("regex",true,array(
                
						   'pattern'=>'/^(\-?[1-9]|\-?[1-9][0-9])$/',
                
                           'messages'=>array(
                               'regexNotMatch'=>'On call limit must be in the range of 0 to 100.'
                           )
        	));	
			
		$used_oncalls = new Zend_Form_Element_Text('used_oncalls');
        $used_oncalls->setAttrib('maxLength', 3);
		$used_oncalls->setAttrib('readonly', 'true');
        $used_oncalls->setAttrib('onfocus', 'this.blur()'); 		
        
		$alloted_year = new Zend_Form_Element_Text('alloted_year');
        $alloted_year->setAttrib('maxLength', 4);
		$alloted_year->setAttrib('readonly', 'true');
		$alloted_year->setAttrib('onfocus', 'this.blur()');
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$submitbutton = new Zend_Form_Element_Button('submitbutton');
		
		 $submitbutton->setAttrib('id', 'submitbuttons');
		$submitbutton->setLabel('Save');
		
		$this->addElements(array($id,$userid,$emp_oncall_limit,$used_oncalls,$alloted_year,$submit,$submitbutton));
        $this->setElementDecorators(array('ViewHelper'));
     		
	}
}