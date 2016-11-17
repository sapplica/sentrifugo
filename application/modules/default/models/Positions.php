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

class Default_Model_Positions extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_positions';
    protected $_primary = 'id';
	
	public function getPositionData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "p.isactive = 1 AND j.isactive=1";
		
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$positionData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('p'=>'main_positions'),array('p.*'))
						   

                           ->joinLeft(array('j'=>'main_jobtitles'), 'j.id=p.jobtitleid',array('j.jobtitlename'))						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		
		return $positionData;       		
	}
	public function getsinglePositionData($id)
	{
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$positionsData = $db->query("SELECT * FROM main_positions WHERE id = ".$id." AND isactive=1	");
		$res = $positionsData->fetchAll();
		if (isset($res) && !empty($res)) 
		{	
			return $res;
		}
		else
			return 'norows';
	}
	
	public function SaveorUpdatePositionData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_positions');
			return $id;
		}
		
	
	}
        
        public function getPositionOptions($bunit_id,$dept_id,$job_id)
        {
            
			return $this->fetchAll("isactive =1 and jobtitleid =".$job_id)->toArray();
        }
		
	public function getPositionList($jobtitle_id)
	{
            if(!empty($jobtitle_id))
            {
                $select = $this->select()
                            ->setIntegrityCheck(false)
                            ->from(array('p'=>'main_positions'),array('p.id','p.positionname'))
                            ->where('p.jobtitleid = '.$jobtitle_id.' AND p.isactive = 1')
                            ->order('p.positionname');
                
                return $this->fetchAll($select)->toArray();
            }
            else 
                return array();
	
	}
	
	public function getTotalPositionList()
	{
	   $select = $this->select()
					->setIntegrityCheck(false)
					->from(array('p'=>'main_positions'),array('p.id','p.positionname'))
					->where('p.isactive = 1')
					->order('p.positionname');
				
	   return $this->fetchAll($select)->toArray();
	
	}
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$exParam1='',$exParam2='',$exParam3='',$exParam4='')
	{		
        $searchQuery = '';$tablecontent = '';  $searchArray = array();$data = array();$id='';
        $dataTmp = array();
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
		/** search from grid - END **/
		$objName = 'positions';
		
		$tableFields = array('action'=>'Action','positionname' => 'Career Level','jobtitlename' => 'Career Track','description' => 'Description');
		$tablecontent = $this->getPositionData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'add' =>'add','call'=>$call,'dashboardcall'=>$dashboardcall
		);			
		return $dataTmp;
	}
	       
}