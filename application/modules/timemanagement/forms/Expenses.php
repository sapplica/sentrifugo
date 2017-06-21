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

class Timemanagement_Form_Expenses extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timemanagement/expenses/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'expensecategory');


        $id = new Zend_Form_Element_Hidden('id');
        
		$client = new Zend_Form_Element_Select('client_id');
		$client->addMultiOption('','Select Client');
		$client->setRegisterInArrayValidator(false);	
		$client->setAttrib('onchange', 'loadProjects(this)');
        $client->setRequired(true);
        $client->addValidator('NotEmpty', false, array('messages' => 'Please select Client.'));
         
		$client->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'tm_clients',
                                        		'field' => 'id',
                                                'exclude'=>'is_active = 1',
										)));
		$client->getValidator('Db_RecordExists')->setMessage('Selected Client is inactivated.');

		$project = new Zend_Form_Element_Select('project_id');
		$project->addMultiOption('','Select Project');
		$project->setRegisterInArrayValidator(false);	
        $project->setRequired(true);
        $project->addValidator('NotEmpty', false, array('messages' => 'Please select Project.'));
         
		$project->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'tm_projects',
                                        		'field' => 'id',
                                                'exclude'=>'is_active = 1',
										)));
		$project->getValidator('Db_RecordExists')->setMessage('Selected Project is inactivated.');
		
		$category = new Zend_Form_Element_Select('expense_cat_id');
		$category->addMultiOption('','Select Category');
		$category->setRegisterInArrayValidator(false);	
        $category->setRequired(true);
        $category->addValidator('NotEmpty', false, array('messages' => 'Please select Category.'));
         
		$category->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'tm_expense_categories',
                                        		'field' => 'id',
                                                'exclude'=>'is_active = 1',
										)));
		$category->getValidator('Db_RecordExists')->setMessage('Selected Category is inactivated.');		
		
		$expenseDate = new ZendX_JQuery_Form_Element_DatePicker('expense_date');
		$expenseDate->setOptions(array('class' => 'brdr_none'));
        //$date_of_leaving->setAttrib('onchange', 'validatejoiningdate(this)'); 		
		$expenseDate->setAttrib('readonly', 'true');
		$expenseDate->setAttrib('onfocus', 'this.blur()');		
		
        $expenseAmount = new Zend_Form_Element_Text('expense_amount');
        $expenseAmount->setAttrib('maxLength', 8);
        $expenseAmount->setLabel("Unit Price");
		$expenseAmount->addValidator("regex",true,array(
									'pattern'=> '/^[1-9]\d{1,4}(\.\d{1,2})?$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter valid Amount.'
								     )
					       ));  

		$note = new Zend_Form_Element_Text('note');
        $note->setAttrib('maxLength', 200);
        $note->setLabel("Note");	

		$billable = new Zend_Form_Element_Radio('is_billable');
		$billable->setLabel("Type");
        $billable->addMultiOptions(array(
										   '1' => 'Yes',
										   '0' => 'No',
    									   ));
		$billable->setSeparator('');
		$billable->setValue('billable');    									   
		$billable->setRegisterInArrayValidator(false);
		$billable->setRequired(true);
		$billable->addValidator('NotEmpty', false, array('messages' => 'Please select Type.'));

/*
client_idbigint(20) unsigned NOT NULL
project_idbigint(20) unsigned NOT NULL
expense_cat_idint(10) unsigned NOT NULL
expense_datetimestamp NOT NULL
expense_amountdecimal(8,2) unsigned NOT NULL
notevarchar(200) NULL
is_billabletinyint(1) unsigned NOT NULL
receipt_filevarchar(200) NULL
expense_statusenum('saved','submitted','approved','rejected') NULL
status_update_datetimestamp NOT NULL
status_update_byint(11) NULL
reject_notevarchar(200) NULL
 */		
		       
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$client,$project,$category,$expenseDate,$expenseAmount,$note,$billable,$submit));
         $this->setElementDecorators(array('ViewHelper'));
         $this->setElementDecorators(array('UiWidgetElement',),array('expense_date'));         
	}
}