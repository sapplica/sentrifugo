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

/**
 * This gives employee report form.
 */
class Default_Form_Activeuserreport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_auser_report');

        $createddate = new Zend_Form_Element_Text("createddate");        
        $createddate->setLabel("Created Date");        
        $createddate->setAttrib('readonly', 'readonly');
        
        $logindatetime = new Zend_Form_Element_Text("logindatetime");        
        $logindatetime->setLabel("Last Login Date");
        $logindatetime->setAttrib('readonly', 'readonly');
		                     
        
        $emprole = new Zend_Form_Element_Select("emprole");
        $emprole->setRegisterInArrayValidator(false);
        $emprole->setLabel("Role");        
        $emprole->addMultiOptions(array(''=>'Select Role'));
        
        $isactive = new Zend_Form_Element_Select("isactive");
        $isactive->setLabel("Status");        
        $isactive->addMultiOptions(array(''=>'Select Status',1 => 'Active',0 => 'Inactive'));
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($submit,$createddate,$logindatetime,$emprole,$isactive));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}