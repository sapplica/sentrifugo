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

require_once 'Zend/Db/Table/Abstract.php';
class Default_Model_States extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_states';
    protected $_primary = 'id';
	
	public function getStatesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "s.isactive = 1 AND c.is_active=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$statesData = $this->select()
    					   ->setIntegrityCheck(false)	
						   ->from(array('s'=>'main_states'),array('s.*'))
                           ->joinLeft(array('c'=>'tbl_countries'), 's.countryid=c.id',array('country_name'=>'c.country_name'))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $statesData;       		
	}
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{		
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				foreach($searchValues as $key => $val)
				{
					$searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		$objName = 'states';
		
		$tableFields = array('action'=>'Action','country_name' => 'Country','state' => 'State');
		
		$tablecontent = $this->getStatesData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
	    $dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'call'=>$call,
			'dashboardcall'=>$dashboardcall
		);	
		return $dataTmp;
	}
	
	public function getsingleStatesData($id,$tbl='')
	{
		if($tbl == 'main'){
			$row = $this->fetchRow("state_id_org = '".$id."' AND isactive = 1");
		}else {
			$row = $this->fetchRow("id = '".$id."' AND isactive = 1");
		}
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function getStatesDataByID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_states'),array('s.*'))
					    ->where('s.isactive = 1 AND s.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getDuplicateStateName($otherstatename,$countryid)
	{
	   $db = Zend_Db_Table::getDefaultAdapter();
		
		$rows = $db->query("select count(*) as count from tbl_states where lower(state_name) = lower('".$otherstatename."') and country_id = ".$countryid." ");
		
		return $rows->fetchAll();
	
	}
	
	public function SaveorUpdateStatesData($countryid, $statename,$stateid,$loginUserId)
	{
	    $date= gmdate("Y-m-d H:i:s");
	   
	    $db = Zend_Db_Table::getDefaultAdapter();
	 	$rows = $db->query("INSERT INTO `main_states` (countryid,state,statecode,state_id_org,createdby,modifiedby,createddate,modifieddate,isactive) VALUES (".$countryid.",'".$statename."','',".$stateid.",".$loginUserId.",".$loginUserId.",'".$date."','".$date."',1) ON DUPLICATE KEY UPDATE state='".$statename."',modifiedby=".$loginUserId.",modifieddate='".$date."',isactive=1 ");		
		
		$id=$this->getAdapter()->lastInsertId('main_states');
		return $id;
		
	
	}
	
	public function SaveMainStateData($countryid,$otherstatename)
	{
	    $date= gmdate("Y-m-d H:i:s");
	    $db = Zend_Db_Table::getDefaultAdapter();
	 	$rows = $db->query("INSERT INTO `tbl_states` (country_id,state_name,state_code,map_point_x,map_point_y,isactive,created,modified) VALUES (".$countryid.",'".$otherstatename."','',null,null,1,'".$date."','".$date."') ");		
		
		$id=$this->getAdapter()->lastInsertId('tbl_states');
		return $id;
	}
	
	public function deleteStateData($data,$where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_states');
			return $id;
		}
	
	}
	
	public function getTotalStatesList()
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'tbl_states'),array('s.id','s.state_name'))
						->order('s.state_name');
					
		return $this->fetchAll($select)->toArray();
	
	}
	
	
	public function getStatesList($countryid,$condition = '')
	{
		if($condition == 'addstate')
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'tbl_states'),array('s.*'))
						->joinLeft(array('m'=>'main_states'), "m.state_id_org = s.id and m.isactive = 1",array())
						->where('s.country_id='.$countryid.' AND s.isactive=1 AND m.state_id_org is null')
						->order('s.state_name');
		}
		else
		{
			$select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'tbl_states'),array('s.*'))
						->where('s.country_id='.$countryid.' AND s.isactive=1 ')
						->order('s.state_name');
		}
		
		return $this->fetchAll($select)->toArray();
    }

    public function getUniqueStatesList($country_id,$stateids)
	{
	    $where = '';
	    if($stateids !='')
		{
		  $where = "and s.id NOT IN(".$stateids.") ";
		}
	    $db = Zend_Db_Table::getDefaultAdapter();
        
        $query = "select s.* from tbl_states s where s.isactive = 1 and s.country_id=".$country_id." $where ORDER BY s.state_name";
        $result = $db->query($query)->fetchAll();
	    return $result;
		
	}	
	public function getStateName($stateid)
	{
	     $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'tbl_states'),array('statename'=>'s.state_name','s.id'))
						->where('s.id='.$stateid.' AND s.isactive=1 ');
						
		
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function getBasicStatesList($countryid)
	{
	    $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('s'=>'main_states'),array('s.*'))
                            ->where('s.countryid='.$countryid.' AND s.isactive=1 ')
                            ->order('s.state');
		
		return $this->fetchAll($select)->toArray();
    } 
	
	public function getStateNameData($state_id_org)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('s'=>'main_states'),array('s.id','s.state_id_org','s.state'))
						->where('s.state_id_org='.$state_id_org.' AND s.isactive=1 ');
						
		
		return $this->fetchAll($select)->toArray();
    } 
	
	public function getStateNamesByIds($stateIdArray)
	{
		$resultstring = implode(',', $stateIdArray);
		$stateArray = array();
		if($resultstring)
		{
			try
			{
				$qry = "select s.state_id_org, s.state from main_states s
                                        where s.state_id_org IN (".$resultstring.")";		 
				$db = Zend_Db_Table::getDefaultAdapter();
				$sqlRes = $db->query($qry);
				$stateRes = $sqlRes->fetchAll();

				if(!empty($stateRes))
				{
					foreach($stateRes as $state)
					{
						$stateArray[$state['state_id_org']]= $state['state'];
					}
				}
			}
			catch(Exception $e)
			{
				echo "Error Encountered - ".$e->getMessage();
			}
		}
		return $stateArray;
	}
}