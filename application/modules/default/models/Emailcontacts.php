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

class Default_Model_Emailcontacts extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_emailcontacts';
    protected $_primary = 'id';
	
       
    public function getgroupEmails($sort, $by, $pageNo, $perPage,$searchQuery)
	{
                        $where = "e.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;		
		if(!sapp_Global::_isactivemodule(RESOURCEREQUISITION))
			$where .= " AND eg.group_code <> 'REQ_HR' AND eg.group_code <> 'REQ_MGMT' ";
		if(!sapp_Global::_isactivemodule(BGCHECKS))
			$where .= " AND eg.group_code <> 'BG_CHECKS_HR' AND eg.group_code <> 'BG_CHECKS_MNGMNT' ";
	$emailContactsdata = $this->select()
                                    ->setIntegrityCheck(false)	
                                    ->from(array('e'=>'main_emailcontacts'))
                                    ->joinInner(array('eg'=>'main_emailgroups'), "eg.id = e.group_id and eg.isactive = 1", array('group_name'=>'eg.group_name'))
                                    ->joinInner(array('bu'=>'main_businessunits'), "bu.id = e.business_unit_id and bu.isactive = 1", array('unitname'=>"if(bu.id = 0,'',bu.unitname)"))
                                    ->where($where)
                                    ->order("$by $sort") 
                                    ->limitPage($pageNo, $perPage);
		
	return $emailContactsdata;       		
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
		$objName = 'emailcontacts';

	    $tableFields = array('action'=>'Action','unitname'=>'Business Unit','group_name' => 'Group','groupEmail' => 'Group Email');
		
		$tablecontent = $this->getgroupEmails($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
	public function getdataforview($id = 0)
        {
            $data = array();
            if($id != 0)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $query = "SELECT `e`.id,e.groupEmail,eg.group_name,eg.group_code,b.unitname 
                          FROM `main_emailcontacts` AS `e` 
                          left join main_emailgroups eg on eg.id = e.group_id and eg.isactive = 1 
                          left join main_businessunits b on b.id = e.business_unit_id and b.isactive = 1 
                          WHERE (e.isactive = 1 and e.id =".$id.")";
                $result = $db->query($query);
                $data = $result->fetch();
            }
            return $data;
        }
	public function getgroupEmailRecord($id=0)
	{  
            $emailContactsArr="";$where = "isactive = 1";
            		
            if($id != 0)
            {   $where = "e.isactive = 1 and e.id =".$id;
                $emailContactsdata = $this->select()
										 ->setIntegrityCheck(false)	
                                        ->from(array('e'=>'main_emailcontacts'))
										->joinLeft(array('eg'=>'main_emailgroups'),'eg.id = e.group_id',array('group_code'=>'eg.group_code'))
                                        ->where($where);
                   
                $emailContactsArr = $this->fetchAll($emailContactsdata)->toArray(); 
            }
            return $emailContactsArr;       		
	}
	
    public function SaveorUpdateEmailcontactsData($data, $where)
    {
	    if($where != '')
                    {
                            $this->update($data, $where);
		return 'update';
                       }
                        else
                       {
		$this->insert($data);
		$id=$this->getAdapter()->lastInsertId($this->_name);
		return $id;
                        }
		
	
	}
    public function getContactsForConstants()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select ec.groupEmail,eg.group_code,ec.business_unit_id 
                  from main_emailcontacts ec 
                  inner join main_emailgroups eg on eg.id = ec.group_id and eg.isactive = 1
                  where ec.isactive = 1";
        $result = $db->query($query);
        $rows = $result->fetchAll();
        if(count($rows)>0)
            return $rows;
        else 
            return array();
    }
    public function getemailcnt($bunit_id,$group_id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select count(*) cnt from main_emailcontacts 
                  where group_id = ".$group_id." and business_unit_id = ".$bunit_id." and isactive = 1";
        $result = $db->query($query);
        $row = $result->fetch();
        $cnt = $row['cnt'];
        return $cnt;
    }
    public function getgroupoptions($bunit)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select id,group_name,group_code 
                  from main_emailgroups 
                  where id not in (select distinct group_id 
                  from main_emailcontacts 
                  where isactive = 1 and business_unit_id = ".$bunit.")";
        $result = $db->query($query);
        $options = $result->fetchAll();
        return $options;
    }
}