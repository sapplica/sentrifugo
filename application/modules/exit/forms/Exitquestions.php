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

class Exit_Form_Exitquestions extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'exitquestions');


        $id = new Zend_Form_Element_Hidden('id');
		
		$exittypes = new Zend_Form_Element_Select('exit_type_id');
		$exittypes->setLabel("Exit Type");
        $exittypes->setAttrib('class', 'selectoption');
        $exittypes->addMultiOption('','Select exit type');
        $exittypes->setRegisterInArrayValidator(false);
        $exittypes->setRequired(true);
		$exittypes->addValidator('NotEmpty', false, array('messages' => 'Please select exit type.'));
		
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		if($postid !='')
		{
			$question = new Zend_Form_Element_Text("question");
			$question->setLabel('Question');
			$question->setAttrib('maxLength', 100);
			$question->addFilter(new Zend_Filter_StringTrim());
			$question->setRequired(true);
	        $question->addValidator('NotEmpty', false, array('messages' => 'Please enter question.'));
			$question->addValidator("regex",true,array(                           
	                           'pattern'=>'/^[a-zA-Z0-9.\- ?\',\/#@$&*()!]+$/',
	                           'messages'=>array(
	                               'regexNotMatch'=>'Please enter valid question.'
	                           )
	        	));
	       
			$description = new Zend_Form_Element_Textarea('description');
			$description->setLabel("Description");
	        $description->setAttrib('rows', 10);
	        $description->setAttrib('cols', 50);
			$description ->setAttrib('maxlength', '200');
		}

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save'); 
		
		$submitadd = new Zend_Form_Element_Button('submitbutton');
		$submitadd->setAttrib('id', 'submitbuttons');
		$submitadd->setAttrib('onclick', 'validatequestiononsubmit(this)');
		$submitadd->setLabel('Save');
		
		if($postid !='')
			 $this->addElements(array($id,$exittypes,$question,$description,$submit));
	    else		 
		 	$this->addElements(array($id,$exittypes,$submitadd));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}