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


class Default_Form_Forgotpassword extends Zend_Form
{
    private $_timeout;
	
	public function __construct($options=null) {
		if (is_array($options)) {
			if (!empty($options['custom'])) {
				if (!empty($options['custom']['timeout'])) {
					$this->_timeout= $options['custom']['timeout'];
				}
				unset($options['custom']);
			}
		}	
		parent::__construct($options);
	}
	
    public function init ()
    {
	
        $this->setMethod('post');
		$this->setAction(BASE_URL.'index/editforgotpassword');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'forgotpassword');
        
		$id = new Zend_Form_Element_Hidden('id');
        $username = new Zend_Form_Element_Text('emailaddress');
        $username->setAttrib('class', 'email-status');
        
		
		
        $username->setLabel('Email Address:');
        $username->setRequired(true);
        $username->addFilter('StripTags');
        $username->addFilter('StringTrim');
        $username->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
        $username->addValidator('EmailAddress');		
			   
		$submit = new Zend_Form_Element_Submit('submit');
		
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('SEND');	   
		
        $url = "'default/index/editforgotpassword/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "''";
		 

		 $submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		));

		 $this->addElements(array($id,$username,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
    }
}


