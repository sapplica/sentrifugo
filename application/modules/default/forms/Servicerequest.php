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

class Default_Form_Servicerequest extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');	
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'servicerequest');
        
        $request_for_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('request_for',null);

        $id = new Zend_Form_Element_Hidden('id');	
        $service_desk_id = new Zend_Form_Element_Hidden('service_desk_id');        
        
        $request_for = new Zend_Form_Element_Select('request_for');
        $request_for->setLabel("Request For");
        $request_for->setRegisterInArrayValidator(false);
        $request_for->setRequired(true);
        $request_for->addValidator('NotEmpty', false, array('messages' => 'Please select request for.'));
        $request_for->setAttrib('onchange', 'displayassets(this)');
        $request_for->addMultiOptions(array(
        		'1' => 'Service',
        		'2' => 'Asset',
        
        ));
    
        $asset_id= new Zend_Form_Element_select('asset_id');
        $asset_id->setLabel("Asset Name");
        $asset_id->addMultiOptions(array('' => 'Select Asset Name' ));
        if($request_for_val == 2) {
	        $asset_id->setRequired(true);
	        $asset_id->addValidator('NotEmpty', false, array('messages' => 'Please select asset name.'));
	        $asset_id->setRegisterInArrayValidator(false);
	       
        }
        		
       
         
        $service_desk_conf_id = new Zend_Form_Element_Select('service_desk_conf_id');        
        $service_desk_conf_id->setLabel("Category");		
        $service_desk_conf_id->setRequired(true);
        $service_desk_conf_id->addValidator('NotEmpty', false, array('messages' => 'Please select category.'));
         $service_desk_conf_id->addMultiOptions(array('' => 'Select category'));
        $service_desk_conf_id->setRegisterInArrayValidator(false);  
        
       
		
       	$service_request_id = new Zend_Form_Element_Select('service_request_id');        
        $service_request_id->setLabel("Request Type");		
        $service_request_id->setRequired(true);
        $service_request_id->addValidator('NotEmpty', false, array('messages' => 'Please select request type.'));
        $service_request_id->addMultiOptions(array('' => 'Select request'));
        $service_request_id->setRegisterInArrayValidator(false);  
        
        $priority = new Zend_Form_Element_Select('priority');        
        $priority->setLabel("Priority");		
        $priority->setRequired(true);
        $priority->addValidator('NotEmpty', false, array('messages' => 'Please select priority.'));
        $priority->addMultiOptions(array('' => 'Select priority','1' => 'Low','2' => 'Medium','3' => 'High'));
        $priority->setRegisterInArrayValidator(false);  

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
        $description->setRequired(true);
        $description ->setAttrib('maxlength', '200');
        $description->addValidator('NotEmpty', false, array('messages' => 'Please enter description.'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $submit->setLabel('Save');

        $this->addElements(array($id,$request_for,$asset_id,$service_desk_conf_id,$service_request_id,$priority,$description,$submit,$service_desk_id));
        $this->setElementDecorators(array('ViewHelper')); 
    }//end of init
}