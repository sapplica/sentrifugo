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

class Timemanagement_Form_Configuration extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'configuration');


        $id = new Zend_Form_Element_Hidden('id');
		/*$ts_block_reminder_day = new Zend_Form_Element_Select('ts_block_reminder_day');
        $ts_block_reminder_day->setAttrib('class', 'selectoption');
        $ts_block_reminder_day->setRegisterInArrayValidator(false);
        $ts_block_reminder_day->setRequired(true);
		$ts_block_reminder_day->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet block reminder day.'));
		
		$ts_blocking_day = new Zend_Form_Element_Select('ts_blocking_day');
        $ts_blocking_day->setAttrib('class', 'selectoption');
        $ts_blocking_day->setRegisterInArrayValidator(false);
        $ts_blocking_day->setRequired(true);
        $ts_blocking_day->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet block day.')); 
        
        $ts_block_start_day = new Zend_Form_Element_Select('ts_block_start_day');
        $ts_block_start_day->setAttrib('class', 'selectoption');
        $ts_block_start_day->setRegisterInArrayValidator(false);
        $ts_block_start_day->setRequired(true);
        $ts_block_start_day->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet block start day.')); 
        
        $ts_block_end_day = new Zend_Form_Element_Select('ts_block_end_day');
        $ts_block_end_day->setAttrib('class', 'selectoption');
        $ts_block_end_day->setRegisterInArrayValidator(false);
        $ts_block_end_day->setRequired(true);
        $ts_block_end_day->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet block end day.')); */
        
        $ts_weekly_reminder_day = new Zend_Form_Element_Select('ts_weekly_reminder_day');
        $ts_weekly_reminder_day->setAttrib('class', 'selectoption');
        $ts_weekly_reminder_day->setRegisterInArrayValidator(false);
        $ts_weekly_reminder_day->setRequired(true);
        $ts_weekly_reminder_day->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet weekly submission reminder.')); 
        
        $ts_block_dates_range = new Zend_Form_Element_Select('ts_block_dates_range');
        $ts_block_dates_range->setAttrib('class', 'selectoption');
        $ts_block_dates_range->setRegisterInArrayValidator(false);
        $ts_block_dates_range->addMultiOption('','Select Block dates range');
		$ts_block_dates_range->addMultiOption('1-31','1st - End of month');
		$ts_block_dates_range->addMultiOption('26-25','26th previous month - 25th current month');
        $ts_block_dates_range->setRequired(true);
        $ts_block_dates_range->addValidator('NotEmpty', false, array('messages' => 'Please select timesheet blocking range.'));

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		// $this->addElements(array($id,$ts_block_reminder_day,$ts_blocking_day,$ts_block_start_day,$ts_block_end_day,$ts_weekly_reminder_day,$submit));
		 $this->addElements(array($id,$ts_weekly_reminder_day,$ts_block_dates_range,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}