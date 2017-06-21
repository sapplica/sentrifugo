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

class Default_Form_Appraisalratings extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
	  $this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'appraisalratings');
    $id = new Zend_Form_Element_Hidden('id');
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		
		$rating_value = new Zend_Form_Element_Text("Rating Value");

		$rating_value->setLabel("Rating Value");
		$rating_value->setAttrib('maxLength', 30);
		$rating_value->addFilter(new Zend_Filter_StringTrim());
		$rating_value->setRequired(true);
        $rating_value->addValidator('NotEmpty', false, array('messages' => 'Please enter rating value.'));
		$rating_value->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9\- ]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid rating value.'
                           )
        	));
        	
        $rating_text = new Zend_Form_Element_Text("Rating Text");
		$rating_text->setLabel("Rating Text");
		$rating_text->setAttrib('maxLength', 30);
		$rating_text->addFilter(new Zend_Filter_StringTrim());
		$rating_text->setRequired(true);
        $rating_text->addValidator('NotEmpty', false, array('messages' => 'Please enter rating text.'));
		$rating_text->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9.\- ?\',\/#@$&*()!]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid rating text.'
                           )
        	));	
		
		
	
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$rating_value,$rating_text,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
		
	}
}