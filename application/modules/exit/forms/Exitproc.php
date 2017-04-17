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
class Exit_Form_Exitproc extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','exitProcFrm');
		$this->setAttrib('name','exitProcFrm');
		//$this->setAttrib('action',BASE_URL.'exit/exitproc/save');

		/*$user_id = new Zend_Form_Element_Select('user_id');
		$user_id->setAttrib('id','user_id');
		$user_id->setAttrib('name','user_id');
		$user_id->setRequired(true);
		$user_id->addValidator('NotEmpty',false,array("messages"=>'Please select an employee'));*/
		
		$auth = Zend_Auth::getInstance();
		$user_id='';
		if($auth->hasIdentity())
		{
			$user_id = $auth->getStorage()->read()->id;
		}
		$exitType = new Zend_Form_Element_Select('exit_type');
		$exitType->setAttrib('id','exit_type');
		$exitType->setAttrib('name','exit_type');
		$exitType->setRequired(true);
		$exitType->addValidator('NotEmpty',false,array("messages"=>'Please enter exit type.'));
		$exitType->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                                array('table' => 'main_exit_process',
                                                                'field' => 'exit_type_id',
                                                                'exclude'=>'employee_id='.$user_id.' and overall_status!="Approved"  and overall_status!="Rejected"'
                                                                )));
		$exitType->getValidator('Db_NoRecordExists')->setMessage('Exit procedure has already been initialized with this type. ');														
					
		$comments = new Zend_Form_Element_Textarea('comments');
		$comments->setAttrib('id','comments');
		$comments->setAttrib('name','comments');
		$comments->setAttrib('onkeyup', 'removeValidation()');
		$comments->setRequired(true);
		$comments->addValidator('NotEmpty',false,array("messages"=>'Please enter comments.'));

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Initiate');

		$this->addElements(array($exitType,$comments,$submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>