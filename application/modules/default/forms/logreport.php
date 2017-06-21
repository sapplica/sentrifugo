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

class Default_Form_logreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'reports/userlogreport');
		$this->setAttrib('id', 'logreport');
		$this->setAttrib('name', 'logreport');

		$emprole = new Zend_Form_Element_Select('emp_role');
		$emprole->setLabel('Employee Role');
		$emprole->setAttrib('onchange', 'changeelement(this)');
		$roleModel = new Default_Model_Roles();
		$roleList = $roleModel->getRolesList_USERLOG();
		$emprole->addMultiOption('','Select Employee Role');
		foreach ($roleList as $roleid=>$rolename){
			$emprole->addMultiOption($roleid,$rolename);
		}

		$group = new Zend_Form_Element_Select('group');
		$group->setLabel('Group');
		$group->setAttrib('onchange', 'changeelement(this)');
		$groupModel = new Default_Model_Groups();
		$groupList = $groupModel->getGroupList();
		$group->addMultiOption('','Select Group');
		foreach ($groupList as $groupid=>$groupname){
			$group->addMultiOption($groupid,$groupname);
		}

		$employeeId = new Zend_Form_Element_Text('employeeIdf');
		$employeeId->setAttrib('onblur', 'clearautocompleteuserlog(this)');
		$employeeId->addFilter(new Zend_Filter_StringTrim());
		$employeeId->setLabel("Employee ID");

		$username = new Zend_Form_Element_Text('username');
		$username->setAttrib('class', 'formelement');
		$username->setAttrib('onblur', 'clearautocompleteuserlog(this)');
		$username->addFilter(new Zend_Filter_StringTrim());
		$username->setLabel("User Name");

		$emailId = new Zend_Form_Element_Text('emailId');
		$emailId->setAttrib('onblur', 'clearautocompleteuserlog(this)');
		$emailId->addFilter(new Zend_Filter_StringTrim());
		$emailId->setLabel("Email");

		$logindate = new ZendX_JQuery_Form_Element_DatePicker('logindate');
		$logindate->setAttrib('onblur', 'blurelement(this)');
		$logindate->setLabel("Login Date");
		$logindate->setAttrib('readonly', 'true');
		$logindate->setAttrib('onfocus', 'this.blur()');
		$logindate->setOptions(array('class' => 'brdr_none'));

		$ipaddress = new Zend_Form_Element_Text('ipaddress');
		$ipaddress->setAttrib('onblur', 'clearautocompleteuserlog(this)');
		$ipaddress->addFilter(new Zend_Filter_StringTrim());
		$ipaddress->setLabel("Ip Address");

		
		$this->addElements(array($emprole,$group,$employeeId,$username,$emailId,$logindate,$ipaddress));
		$this->setElementDecorators(array('ViewHelper'));
		$this->setElementDecorators(array(
                    'UiWidgetElement',
		),array('logindate'));
	}
}