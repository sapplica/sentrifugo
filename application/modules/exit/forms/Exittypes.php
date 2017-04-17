<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Exit_Form_Exittypes extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','epexittypes');
		$this->setAttrib('name','epexittypes');

		$exitType = new Zend_Form_Element_Text('exit_type');
		$exitType->setAttrib('id','exit_type');
		$exitType->setAttrib('name','exit_type');
		$exitType->setAttrib('maxlength','30');
		$exitType->addFilter(new Zend_Filter_StringTrim());
		$exitType->setRequired(true);
		$exitType->addValidator('NotEmpty',false,array("messages"=>'Please enter exit type.'));
		$exitType->addValidator('regex',true,array(
			'pattern'=>'/^[a-zA-Z0-9.\- ?\',\/#@$&*()!+]+$/', 
			'messages'=>array('regexNotMatch'=>'Please enter valid exit type.')
		));
		$exitType->addValidator(new Zend_Validate_Db_NoRecordExists(
			array(
				'table' => 'main_exit_types',
				'field' => 'exit_type',
				'exclude' => 'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive = 1'
			)	
		));
		$exitType->getValidator('Db_NoRecordExists')->setMessage('Exit type already exists.');

		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		
		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Save');

		$this->addElements(array($exitType,$submitBtn,$description));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>