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

class Default_Model_Employeeoncalls extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeeoncalls';
    protected $_primary = 'id';

	public function getEmpOncallsData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();

		$empskillsData = $this->select()
    					   ->setIntegrityCheck(false)
						   ->from(array('e' => 'main_employeeoncalls'),array('id'=>'e.id','emp_oncall_limit'=>'e.emp_oncall_limit','used_oncalls'=>'e.used_oncalls','remainingoncalls'=>new Zend_Db_Expr('e.emp_oncall_limit - e.used_oncalls'),'e.alloted_year'))
						   ->where($where)
    					   ->order("$by $sort")
    					   ->limitPage($pageNo, $perPage);

		return $empskillsData;
	}

	public function getsingleEmployeeoncallData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeoncalls'),array('el.*'))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())');

		return $this->fetchAll($select)->toArray();
	}

	public function getPreviousYearEmployeeoncallData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeoncalls'),array('el.*','remainingoncalls'=>new Zend_Db_Expr('el.emp_oncall_limit - el.used_oncalls')))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())-1');

		return $this->fetchAll($select)->toArray();
	}

	public function getsingleEmponcallsrow($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}

	public function SaveorUpdateEmpOncalls($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeoncalls');
			return $id;
		}

	}

	public function SaveorUpdateEmployeeOncalls($user_id,$emp_oncall_limit,$isoncalltrasnfer,$loginUserId)
	{
		$date= gmdate("Y-m-d H:i:s");

	    $db = Zend_Db_Table::getDefaultAdapter();
		$rows = $db->query("INSERT INTO `main_employeeoncalls` (user_id,emp_oncall_limit,used_oncalls,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isoncalltrasnferset) VALUES (".$user_id.",".$emp_oncall_limit.",'0',year(now()),".$loginUserId.",".$loginUserId.",'".$date."','".$date."',1,".$isoncalltrasnfer.") ON DUPLICATE KEY UPDATE emp_oncall_limit='".$emp_oncall_limit."',modifiedby=".$loginUserId.",modifieddate='".$date."',isactive = 1,isoncalltrasnferset=".$isoncalltrasnfer." ");
		$id=$this->getAdapter()->lastInsertId('main_employeeoncalls');
		return $id;


	}

	public function saveallotedoncalls_normalquery($postedArr,$totOncalls,$userid,$loginUserId)
	{
	    $date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();
	 	$rows = $db->query("INSERT INTO main_allottedoncallslog (userid,assignedoncalls,totaloncalls,year,
				createdby,modifiedby,createddate,modifieddate) VALUES (".$userid.",".$postedArr['oncall_limit'].",".$totOncalls.",".$postedArr['alloted_year'].",
				".$loginUserId.",".$loginUserId.",'".$date."','".$date."');");

		$id = $this->getAdapter()->lastInsertId('main_allottedoncallslog');
		return $id;
	}

    public function saveallotedoncalls($postedArr,$totOncalls,$userid,$loginUserId)
	{
	    $allotedOncallsDetailsmodel = new Default_Model_Allottedoncallslog();
	    $date= gmdate("Y-m-d H:i:s");
	    $data = array(
			'userid' => $userid,
			'assignedoncalls' => !empty($postedArr['oncall_limit'])?$postedArr['oncall_limit']:$totOncalls,
	        'totaloncalls' => $totOncalls,
	        'year' => !empty($postedArr['alloted_year'])?$postedArr['alloted_year']:date("Y"),
	        'createdby' => $loginUserId,
	        'modifiedby' => $loginUserId,
	        'createddate' => $date,
			'modifieddate' => $date
		);
		$id = $allotedOncallsDetailsmodel->SaveorUpdateAllotedOncallsDetails($data,'');
		return $id;
	}

	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{
        $searchQuery = '';$tablecontent = '';$level_opt = array();
        $searchArray = array();$data = array();$id='';
        $dataTmp = array();
		if($searchData != '' && $searchData!='undefined')
		{
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'remainingoncalls')
					$searchQuery .= "  e.emp_oncall_limit - e.used_oncalls   like '%".$val."%' AND ";
				else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
		/** search from grid - END **/
		$objName = 'emponcalls';
		$tableFields = array('action'=>'Action','emp_oncall_limit'=>'Allotted Oncall Limit','used_oncalls'=>'Used Oncalls','remainingoncalls'=>'Oncall Balance','alloted_year'=>'Allotted Year');

		$tablecontent = $this->getEmpOncallsData($sort, $by, $pageNo, $perPage,$searchQuery,$exParam1);

		$dataTmp = array('userid'=>$exParam1,
						'sort' => $sort,
						'by' => $by,
						'pageNo' => $pageNo,
						'perPage' => $perPage,
						'tablecontent' => $tablecontent,
						'objectname' => $objName,
						'extra' => array(),
						'tableheader' => $tableFields,
						'jsGridFnName' => 'getEmployeeAjaxgridData',
						'jsFillFnName' => '',
						'searchArray' => $searchArray,
						'add'=>'add',
						'menuName'=>'Oncalls',
						'formgrid'=>'true',
						'unitId'=>$exParam1,
						'dashboardcall'=>$dashboardcall,
						'call'=>$call,
						'context'=>$exParam2
				);
		return $dataTmp;
	}
}
?>
