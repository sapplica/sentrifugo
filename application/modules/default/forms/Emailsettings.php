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

class Default_Form_Emailsettings extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'emailsettings');
		        
        $id = new Zend_Form_Element_Hidden("id");
        $id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
		
        $username = new Zend_Form_Element_Text("username");
        $username->setLabel("User name");	
        $username->setAttrib("class", "formDataElement");
       // $username->setRequired("true");
        $username->setAttrib('maxlength', '100');
        //$username->addValidator('NotEmpty', false, array('messages' => 'Please enter username.'));
        
        $tls = new Zend_Form_Element_Text("tls");
        $tls->setLabel("Secure Transport Layer");	
        $tls->setAttrib("class", "formDataElement");
       // $tls->setRequired("true");
        $tls->setAttrib('maxlength', '40');
      //  $tls->addValidator('NotEmpty', false, array('messages' => 'Please enter secure transport layer.'));
        
        $auth = new Zend_Form_Element_Select("auth");
        $auth->setLabel("Authentication Type");	
        $auth->setMultiOptions(array(							
							'true'=>'True' ,
							'false'=>'False'
							));
        $auth->setAttrib("class", "formDataElement");
		$auth->setAttrib("onChange","toggleAuth()");
        $auth->setRequired("true");
        $auth->setAttrib('maxlength', '50');
        $auth->addValidator('NotEmpty', false, array('messages' => 'Please enter authentication type.'));
        
        $port = new Zend_Form_Element_Text("port");
        $port->setLabel("Port");	
        $port->setAttrib("class", "formDataElement");
        $port->setRequired("true");
        $port->setAttrib('maxlength', '50');
        $port->addValidator('NotEmpty', false, array('messages' => 'Please enter port.'));
        
        $password = new Zend_Form_Element_Password("password");
        $password->setLabel("Password");	
        $password->setAttrib("class", "formDataElement");
         // $password->setRequired("true");
        $password->setAttrib('maxlength', '100');
       // $password->addValidator('NotEmpty', false, array('messages' => 'Please enter password.'));
        
        $server_name = new Zend_Form_Element_Text("server_name");
        $server_name->setLabel("SMTP Server");	
        $server_name->setAttrib("class", "formDataElement");
        $server_name->setRequired("true");
        $server_name->setAttrib('maxlength', '100');
        $server_name->addValidator('NotEmpty', false, array('messages' => 'Please enter SMTP Server.'));
                        	                        	       

	$submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Save");  
        $submit->setAttrib('id', 'submitbutton');
        $submit->setAttrib("class", "formSubmitButton");
       

        $this->addElements(array($id,$submit,$username,$tls,$auth,$port,$password,$server_name));
        $this->setElementDecorators(array('ViewHelper')); 
    }//end of init function.
}//end of class