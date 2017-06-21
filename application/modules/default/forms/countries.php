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

class Default_Form_countries extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'countries/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'countries');


        $id = new Zend_Form_Element_Hidden('id');
		
		$country = new Zend_Form_Element_Select('country');
        $country->setAttrib('class', 'selectoption');
        $country->setAttrib('onchange', 'displayCountryCode(this)');
        $country->setRegisterInArrayValidator(false);
        $country->addMultiOption('','Select Country');
			$countrymodel = new Default_Model_Countries();
			$id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
			$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
			
		    if($id_val == '' || $actionName == 'view')
			{
			   $countrymodeldata = $countrymodel->getTotalCountriesList('addcountry');
				foreach ($countrymodeldata as $countryres){
					$country->addMultiOption($countryres['id'],utf8_encode($countryres['country_name']));
				}
				$country->addMultiOption('other','Other');
			}	
        $country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$country->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_countries',
	                                                     'field'=>'country_id_org',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		  $country->getValidator('Db_NoRecordExists')->setMessage('Country already exists.');
		      
			
		$countrycode = new Zend_Form_Element_Text('countrycode');
        $countrycode->setAttrib('maxLength', 20);
		$countrycode->setAttrib('readonly',true);
		$countrycode->setAttrib('onfocus', 'this.blur()');
        
        $countrycode->setRequired(true);
        $countrycode->addValidator('NotEmpty', false, array('messages' => 'Please enter country code.'));  
		$countrycode->addValidator("regex",true,array(
									
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								   
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid country code.'
								   )
					));
       	
		$citizenship = new Zend_Form_Element_Text('citizenship');
        $citizenship->setAttrib('maxLength', 20);
        
		$citizenship->addValidator("regex",true,array(
                          
                          
						   'pattern' =>'/^[a-zA-Z\s]*$/i',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid citizenship.'
                           )
        	));

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');


		 $this->addElements(array($id,$country,$countrycode,$citizenship,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}