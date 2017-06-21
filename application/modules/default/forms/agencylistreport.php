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

class Default_Form_agencylistreport extends Zend_Form
{
	public function init()
	{
	    $this->setMethod('post');		
		$this->setAttrib('id', 'agencylistreport');
		$this->setAttrib('name','agencylistreport');
		$this->setAttrib('action',BASE_URL.'reports/agencylistreport');
		
		$agencyname = new Zend_Form_Element_Text('agencynamef');
		$agencyname->setLabel('Agency');
		$agencyname->setAttrib('onblur', 'clearagencyname(this)');
		
        $agencyname->setAttrib('maxLength', 50);
        
			
				
		$primaryphone = new Zend_Form_Element_Text('primaryphonef');
		$primaryphone->setLabel('Primary Phone');
		$primaryphone->setAttrib('onblur', 'blurelement(this)');
        $primaryphone->setAttrib('maxLength', 15);
        $primaryphone->addFilter(new Zend_Filter_StringTrim());
        $primaryphone->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Primary phone number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Primary phone number must contain at least %min% characters.'
											)))));
		$primaryphone->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));	
		 
						 
		$checktype = new Zend_Form_Element_Multiselect('bg_checktypef');
		$checktype->setLabel('Screening Type');
		
		$checktypeModal = new Default_Model_Bgscreeningtype();
	    	$typesData = $checktypeModal->fetchAll('isactive=1','type');
			foreach ($typesData->toArray() as $data){
		$checktype->addMultiOption($data['id'],$data['type']);
	    	}
		$checktype->setRegisterInArrayValidator(false);	
		$checktype->setAttrib('onchange', 'changeelement(this)');
						
		$website = new Zend_Form_Element_Text('website_urlf');
		$website->setLabel('Website Url');
		$website->setAttrib('maxLength', 50);
        $website->addFilter(new Zend_Filter_StringTrim());
      	   
      	$website->setAttrib('onblur', 'clearagencyname(this)');
		
		
		
		$this->addElements(array($agencyname,$primaryphone,$checktype,$website));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}
?>