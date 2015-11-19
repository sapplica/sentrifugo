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

class Default_StructureController extends Zend_Controller_Action
{

    private $options;
	public function preDispatch()
	{
		 
		
	}
	
    public function init()
    {
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
    }
	
	public function indexAction()
	{	
		$structureModel = new Default_Model_Structure();
		$orgData = $structureModel->getOrgData();
		$unitData = $structureModel->getUnitData();
		$deptData = $structureModel->getDeptData();
		$nobu = 'no';
		foreach($deptData as $rec)
		{			
			if($rec['unitid'] == '0')
			$nobu = 'exists';
			
		}
		$this->view->orgData = $orgData;
		$this->view->unitData = $unitData;
		$this->view->deptData = $deptData;
		$this->view->nobu = $nobu;
		$this->view->msg = 'This is organization structure';
	}
}
?>