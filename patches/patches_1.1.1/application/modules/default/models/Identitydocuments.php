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

class Default_Model_Identitydocuments extends Zend_Db_Table_Abstract
{	
    protected $_name = 'main_identitydocuments';
    protected $_primary = 'id';
    
	public function getIdentityDocumentsData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		$where = "i.isactive = 1";
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$identityDocumentsData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('i'=>'main_identitydocuments'),array('i.*','mandatory'=>'if(i.mandatory = 1,"Yes","No")','expiry'=>'if(i.expiry = 1,"Yes","No")'))
                           ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
        
		return $identityDocumentsData;       		
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
							$searchQuery .= " i.".$key." like '%".$val."%' AND ";
                           $searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
			
		$objName = 'identitydocuments';
		$bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');
		$tableFields = array('action'=>'Action','document_name' => 'Document Name','mandatory' => 'Mandatory','expiry' => 'Expiry','description' => 'Description');
		
		$tablecontent = $this->getIdentityDocumentsData($sort, $by, $pageNo, $perPage,$searchQuery);     
		
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
			'search_filters' => array(
                           'mandatory' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                            'expiry' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                        ),
		);
		return $dataTmp;
	}
	
	public function getIdentitydocumnetsrecord()
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitydocuments'),array('i.*'))
                            ->where('i.isactive=1');							
		$identityDocumnetsArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityDocumnetsArr; 
	
	}
	
	public function getIdentitydocumnetsrecordwithID($id)
	{	$identityCodesArr="";
		$db = Zend_Db_Table::getDefaultAdapter();		
	    $select = $this->select()
                            ->from(array('i'=>'main_identitydocuments'),array('i.*'))
                            ->where('i.isactive=1 AND i.id='.$id.'');							
		$identityDocumnetsArr = $this->fetchAll($select)->toArray(); 
		
		return  $identityDocumnetsArr; 
	
	}
      
	public function SaveorUpdateIdentitydocumentsData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_identitycodes');
			return $id;
		}		
	}
	
	public function getallcodes($code)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "select * from main_identitycodes";		
		$result = $db->query($query)->fetch();
		if($code == 'bgcheck')
	    return $result['backgroundagency_code'];
		else
		return $result;
	}
}