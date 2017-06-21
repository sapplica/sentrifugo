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
 * @Name   Expenses Controller
 *
 * @description
 *
 * This Expense controller contain actions related to Expense.
 *
 * 1. Display all Expense details.
 * 2. Save or Update Expense details.
 * 3. Delete Expense.
 * 4. View Expense details.
 *
 * @author sagarsoft
 * @version 1.0
 */
class Expenses_IndexController extends Zend_Controller_Action
{
	private $options;

	/**
	 * The default action - show the home page
	 */
	public function preDispatch()
	{
		/*$userModel = new Timemanagement_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		/* if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error'); */

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();

	}

	/**
	 * This method will display all the Expense details in grid format.
	 */
	/**
	 * This method will display all the Expense details in grid format.
	 */
	public function indexAction()
	{
		//echo "here";exit;
		$this->_redirect('expenses/expenses');
	}
	
	
}

