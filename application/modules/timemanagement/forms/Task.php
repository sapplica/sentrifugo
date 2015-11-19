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

class Timemanagement_Form_Task extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timemanagement/defaulttasks/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'defaulttasks');


        $id = new Zend_Form_Element_Hidden('id');
		
		$task = new Zend_Form_Element_Text('task');
        $task->setAttrib('maxLength', 100);
        
        $task->setRequired(true);
        $task->addValidator('NotEmpty', false, array('messages' => 'Please enter default task.'));
		$task->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([a-zA-Z0-9& ]*)$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter a valid default task.'
								     )
					       ));	
        $task->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'tm_tasks',
                                                     'field'=>'task',
                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and is_active=1',    
                                                 ) )  
                                    );
        $task->getValidator('Db_NoRecordExists')->setMessage('Default task already exists.');	
        	
      
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$task,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}