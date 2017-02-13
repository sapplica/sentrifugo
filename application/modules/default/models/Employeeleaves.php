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

class Default_Model_Employeeleaves extends Zend_Db_Table_Abstract
{
	protected $_name = 'main_employeeleaves';
    protected $_primary = 'id';
	
	public function getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$id)
	{
		$where = " e.user_id = ".$id." AND e.isactive = 1 ";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$empskillsData = $this->select()
    					   ->setIntegrityCheck(false)	 
						   ->from(array('e' => 'main_employeeleaves'),array('id'=>'e.id','emp_leave_limit'=>'e.emp_leave_limit','used_leaves'=>'e.used_leaves','remainingleaves'=>new Zend_Db_Expr('e.emp_leave_limit - e.used_leaves'),'e.alloted_year'))
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
	
		return $empskillsData;       		
	}
	
	public function getsingleEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*'))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getPreviousYearEmployeeleaveData($id)
	{
		$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('el'=>'main_employeeleaves'),array('el.*','remainingleaves'=>new Zend_Db_Expr('el.emp_leave_limit - el.used_leaves')))
						->where('el.user_id='.$id.' AND el.isactive = 1 AND el.alloted_year = year(now())-1');
		
		return $this->fetchAll($select)->toArray();
	}
	
	public function getsingleEmpleavesrow($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateEmpLeaves($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
			return $id;
		}
		
	}
	
	public function SaveorUpdateEmployeeLeaves($user_id,$emp_leave_limit,$isleavetrasnfer,$loginUserId)
	{
		$date= gmdate("Y-m-d H:i:s");
	   
	    $db = Zend_Db_Table::getDefaultAdapter();
		$rows = $db->query("INSERT INTO `main_employeeleaves` (user_id,emp_leave_limit,used_leaves,alloted_year,createdby,modifiedby,createddate,modifieddate,isactive,isleavetrasnferset) VALUES (".$user_id.",".$emp_leave_limit.",'0',year(now()),".$loginUserId.",".$loginUserId.",'".$date."','".$date."',1,".$isleavetrasnfer.") ON DUPLICATE KEY UPDATE emp_leave_limit='".$emp_leave_limit."',modifiedby=".$loginUserId.",modifieddate='".$date."',isactive = 1,isleavetrasnferset=".$isleavetrasnfer." ");				
		$id=$this->getAdapter()->lastInsertId('main_employeeleaves');
		return $id;
		
	
	}
	
	public function saveallotedleaves_normalquery($postedArr,$totLeaves,$userid,$loginUserId)
	{
	    $date= gmdate("Y-m-d H:i:s");
		$db = Zend_Db_Table::getDefaultAdapter();		
	 	$rows = $db->query("INSERT INTO main_allottedleaveslog (userid,assignedleaves,totalleaves,year,
				createdby,modifiedby,createddate,modifieddate) VALUES (".$userid.",".$postedArr['leave_limit'].",".$totLeaves.",".$postedArr['alloted_year'].",
				".$loginUserId.",".$loginUserId.",'".$date."','".$date."');");		
		
		$id = $this->getAdapter()->lastInsertId('main_allottedleaveslog');
		return $id;
	}
	
    public function saveallotedleaves($postedArr,$totLeaves,$userid,$loginUserId)
	{
	    $allotedLeavesDetailsmodel = new Default_Model_Allottedleaveslog();
	    $date= gmdate("Y-m-d H:i:s");
	    $data = array(
			'userid' => $userid,
			'assignedleaves' => !empty($postedArr['leave_limit'])?$postedArr['leave_limit']:$totLeaves,
	        'totalleaves' => $totLeaves,
	        'year' => !empty($postedArr['alloted_year'])?$postedArr['alloted_year']:date("Y"),
	        'createdby' => $loginUserId,
	        'modifiedby' => $loginUserId,
	        'createddate' => $date,
			'modifieddate' => $date
		);
		$id = $allotedLeavesDetailsmodel->SaveorUpdateAllotedLeavesDetails($data,'');	
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
				if($key == 'remainingleaves')
					$searchQuery .= "  e.emp_leave_limit - e.used_leaves   like '%".$val."%' AND ";
				else
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");					
		}
		/** search from grid - END **/
		$objName = 'empleaves';
		$tableFields = array('action'=>'Action','emp_leave_limit'=>'Allotted Leave Limit','used_leaves'=>'Used Leave','remainingleaves'=>'Leave Balance','alloted_year'=>'Allotted Year');
		
		$tablecontent = $this->getEmpLeavesData($sort, $by, $pageNo, $perPage,$searchQuery,$exParam1);  
				
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
						'menuName'=>'Leave',
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