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

class Default_Form_performancereport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','formid');
		$this->setAttrib('name','performance');
	
		$startyear = date('Y', strtotime('-2 year'));
		$endyear = date('Y', strtotime('+2 year'));

		$employeename = new Zend_Form_Element_Text('employeename');
		$employeename->setLabel('Employee Name');
        // $employeename->setAttrib('onblur', 'clearautocompletename(this)');			
		
        $reporting_manager = new Zend_Form_Element_Text("reporting_manager");        
        $reporting_manager->setLabel("Line Manager");
        $reporting_manager->setAttrib('name', '');
        $reporting_manager->setAttrib('id', 'idreporting_manager');
		
		$fromyear = new Zend_Form_Element_Select('fromyear');
		$fromyear->setLabel('From Year');
		$fromyear->addMultiOption('','From Year');
		for($i=$startyear;$i<=$endyear;$i++)
		{
			$fromyear->addMultiOption($i,$i);
		}
		$fromyear->setAttrib('class','selectoption');
		$fromyear->setRegisterInArrayValidator(false);		

		
		$toyear = new Zend_Form_Element_Select('toyear');
		$toyear->setLabel('To Year');
		$toyear->addMultiOption('','To Year');
		$toyear->setAttrib('class','selectoption');
		$toyear->setRegisterInArrayValidator(false);
		
	
		/*for($i=$startyear;$i<=$endyear;$i++)
		{
			$toyear->addMultiOption($i,$i);
		}*/
		
       
        $department_id = new Zend_Form_Element_Multiselect("department_id");
        $department_id->setLabel("Department");        
        
        
        $businessunit_id = new Zend_Form_Element_Multiselect("businessunit_id");
        $businessunit_id->setLabel("Business Unit");      
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id','submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($employeename,$reporting_manager,$department_id,$businessunit_id,$fromyear,$toyear,$submit));
		$this->setElementDecorators(array('ViewHelper'));
		
	}
}