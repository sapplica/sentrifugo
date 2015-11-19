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

class Default_Form_Multiplepolicydocs extends Zend_Form
{

	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','policydocs');
		$this->setAttrib('name','policydocs');

		$documentName = new Zend_Form_Element_Text('document_name');
		$documentName->setAttrib('id','document_name');
		$documentName->setAttrib('name','document_name[]');
		$documentName->setAttrib('maxlength','250');
		$documentName->addFilter(new Zend_Filter_StringTrim());
		$documentName->setRequired(true);
		$documentName->addValidator('NotEmpty',false,array("messages"=>'Please enter document'));
		$documentName->addValidator('regex',true,array(
			'pattern'=> '/^[a-zA-Z\-0-9\s]*$/',					
			'messages'=>array('regexNotMatch'=>'Please enter valid category')
		));


		$category_id = new Zend_Form_Element_Select('category_id');
		$category_id->setAttrib('id','category_id');
		$category_id->setAttrib('name','category_id');
		$category_id->setLabel('Category');	
		$category_id->setRequired(true);
		$category_id->addValidator('NotEmpty',false,array('messages'=>'Please select category'));
		$category_id->setRegisterInArrayValidator(false);			

		$documentDesc = new Zend_Form_Element_Textarea('description');
		$documentDesc->setAttrib('id','description');
		$documentDesc->setAttrib('name','description[]');
		$documentDesc->setAttrib('rows',10);
		$documentDesc->setAttrib('cols',50);
		$documentDesc->setAttrib('maxlength',250);

		$documentVersion = new Zend_Form_Element_Text('document_version');
		$documentVersion->setAttrib('id','document_version');
		$documentVersion->setAttrib('name','document_version[]');
		$documentVersion->setAttrib('maxlength','7');
		$documentVersion->addValidator('regex',true,array(
			'pattern'=>'/^[0-9]{1,7}(?:\.[0-9]{1,2})?$/',					
			'messages'=>array('regexNotMatch'=>'Please enter valid version')
		));
		$documentVersion->addFilter(new Zend_Filter_StringTrim());

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Add');
		//$submitBtn->setAttrib('onclick','validateUploadDoc()');

		$this->addElements(array($documentName, $category_id, $documentDesc, $documentVersion, $submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>