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

class Default_Form_leavemanagementreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'leavemanagementreport');


        $id = new Zend_Form_Element_Hidden('id');
		
		        		
		$department = new Zend_Form_Element_Select('department_id');
		$department->setLabel('Department');
		$department->addMultiOption('','Select Department');
        $department->setAttrib('class', 'selectoption');
        $department->setRegisterInArrayValidator(false);
		
		$month = new Zend_Form_Element_Select('cal_startmonth');
		$month->setLabel('Start Month');
		$month->addMultiOption('','Select Calendar Start Month');
        $month->setAttrib('class', 'selectoption');
        $month->setRegisterInArrayValidator(false);
		
		$weekend_startday = new Zend_Form_Element_Select('weekend_startday');
		$weekend_startday->setLabel('Week-end 1');
		$weekend_startday->addMultiOption('','Select Weekend Start Day');
        $weekend_startday->setAttrib('class', 'selectoption');
        $weekend_startday->setRegisterInArrayValidator(false);
         
        $weekend_endday = new Zend_Form_Element_Select('weekend_endday');
		$weekend_endday->setLabel('Week-end 2');
		$weekend_endday->addMultiOption('','Select Weekend End Day');
        $weekend_endday->setAttrib('class', 'selectoption');
        $weekend_endday->setRegisterInArrayValidator(false);  		 
       
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$department,$month,$weekend_startday,$weekend_endday,$submit));
		$this->setElementDecorators(array('ViewHelper'));
          		 
	}
}