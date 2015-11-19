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

class Default_Model_Servicedeskrequest extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_sd_reqtypes';
    protected $_primary = 'id';
	
	public function getServiceDeskRequestData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "sdr.isactive = 1 and d.isactive=1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$servicedeskRequestData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('sdr'=>'main_sd_reqtypes'),array('sdr.*'))
                           ->joinInner(array('d'=>'main_sd_depts'), 'sdr.service_desk_id=d.id', array('service_desk_name' => 'd.service_desk_name'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		return $servicedeskRequestData;       		
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
						if ($key == 'service_desk_name') {
							$searchQuery .= " d.".$key." like '%".$val."%' AND ";
						} else {
							$searchQuery .= " sdr.".$key." like '%".$val."%' AND ";
						}
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'servicedeskrequest';
		
		$tableFields = array('action'=>'Action','service_desk_name'=>'Category','service_request_name' => 'Request Type','description' => 'Description');
		
		$tablecontent = $this->getServiceDeskRequestData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
		);
		return $dataTmp;
	}
	
	public function getServiceDeskRequestbyID($id)
	{
	    $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('sdr'=>'main_sd_reqtypes'),array('sdr.*'))
					    ->where('sdr.isactive = 1 AND sdr.id='.$id.' ');
		return $this->fetchAll($select)->toArray();
	
	}
	
	public function SaveorUpdateServiceDeskRequestData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_sd_reqtypes');
			return $id;
		}
		
	
	}
	
	public function checkduplicaterequestname($servicedeskid,$requestname)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
	    $qry = "select count(*) as count from main_sd_reqtypes sd where sd.service_request_name ='".$requestname."' AND sd.service_desk_id='".$servicedeskid."' AND sd.isactive=1 ";
		$res = $db->query($qry)->fetchAll();
		return $res;
	}
        
        /**
         * This function uses to get count of request types based on category id.
         * @param integer $category_id  = id of service desk category
         * @return integer
         */
        public function getReqCnt($category_id)
        {
            $cnt = 0;
            if($category_id != '')
            {
                $data = $this->fetchAll("isactive = 1 and service_desk_id = ".$category_id);
                $cnt = $data->count();
            }
            return $cnt;
        }
}