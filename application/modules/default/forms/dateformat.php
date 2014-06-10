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

class Default_Form_dateformat extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',DOMAIN.'dateformat/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'dateformat');


        $id = new Zend_Form_Element_Hidden('id');
	$example = new Zend_Form_Element_Text('example');	
		$dateformat = new Zend_Form_Element_Text('dateformat');
        $dateformat->setAttrib('maxLength', 20);
        //$dateformat->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $dateformat->addFilter(new Zend_Filter_StringTrim());
        $dateformat->setRequired(true);
        $dateformat->addValidator('NotEmpty', false, array('messages' => 'Please enter date format.'));  
        /*$dateformat->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s\[\]\.\-#$@&_*()]*$/', 
                          // 'pattern'=>'/^[a-zA-Z][^(!~^?%`)]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please input Alphanumeric Value.'
                           )
        	));*/
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		//$description->setAttribs(array('style' => 'resize:none;overflow:auto;border:none;'));

        $submit = new Zend_Form_Element_Submit('submit');
		// $submit->setLabel('Upload File')
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'dateformat/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'dateformat\');'";;
		 

		 //$submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		//));

		 $this->addElements(array($id,$dateformat,$description,$submit,$example));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}