<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_GeneratereportsController extends Zend_Controller_Action
{

	private $options;
	private $userlog_model;
	public function preDispatch()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('empauto', 'json')->initContext();
		
	}

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
            $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
            $this->userlog_model = new Default_Model_Userloginlog();
	}
   
	public function agencyAction()
	{
		
	}
}