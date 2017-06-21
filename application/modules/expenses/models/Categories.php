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

class Expenses_Model_Categories extends Zend_Db_Table_Abstract
{
    protected $_name = 'expense_categories';
    protected $_primary = 'id';
	
	
	
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		$searchQuery = '';
		$searchArray = array();
		$data = array();

		  if($searchData != '' && $searchData!='undefined')
        {
            $searchValues = json_decode($searchData);
            if(count($searchValues) >0)
            {
                foreach($searchValues as $key => $val)
                {    
                    if($key == 'created_date')                    
                        $searchQuery .= " date(".$key.") = '".  sapp_Global::change_date($val,'database')."' AND ";					
                    else
                        $searchQuery .= " ".$key." like '%".$val."%' AND ";
                    $searchArray[$key] = $val;
                }
                $searchQuery = rtrim($searchQuery," AND");
            }
        }
			
		$objName = 'expensecategories';
		
		$tableFields = array(
					'action'=>'Action',
					'expense_category_name' => 'Expense Category Name',
					'userfullname' => 'Created By',
					'created_date' => 'Created Date',
		);
	
		$tablecontent = $this->getCategoriesData($sort, $by, $pageNo, $perPage,$searchQuery);
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
			'dashboardcall'=>$dashboardcall,
			'menuName' => 'Category',
			  'search_filters' => array(
                   
                            'created_date'=>array('type'=>'datepicker'),
                            
                        ),
			
			);
			return $dataTmp;
	}

	/**
	 * This will fetch all the categories details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 * 
	 * @return array $category_modes
	 */
	public function getCategoriesData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
		
	
		$where = "c.isactive = 1";

		if($searchQuery)
		$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		
		 $Category_modes = $this->select()
		->setIntegrityCheck(false)
		->from(array('c' => 'expense_categories'),array('c.*','created_date'=>'DATE_FORMAT(created_date,"'.DATEFORMAT_MYSQL.'")','expense_category_name'=>'c.expense_category_name'))
	    ->joinInner(array('u'=>'main_users'), "u.id = c.createdby and
	    u.isactive = 1",array('id'=>'c.id','userfullname'=>
	    'u.userfullname'))
		->where($where)
		->order("$by $sort")
		->limitPage($pageNo, $perPage);
		return $Category_modes;

	}
	
	
	
				/**
	 * This method will save or update the category details based on the  id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateCategoryData($data, $where){
		

		if($where != ''){
			$this->update($data, $where);
			return 'update';
			
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}
	public function getsingleCategorystatusData($id)
	{
		$row = $this->fetchRow("id = '".$id."' and isactive = 1");
		if (!$row) {
			
                    return array();
		}
                else
		return $row->toArray();
	}

	
	
	
	
	public function isCategoryExistForexpense($category_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT COUNT(*) as count FROM expenses WHERE isactive =1 AND category_id=".$category_id;
		$result = $db->query($query)->fetch();
		return $result['count'];
	}
	
	public function getExpensesCategoriesList()
	{
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('c'=>$this->_name),array('c.id','expense_category_name'))
		->where('c.isactive = 1 ')
		->order('c.expense_category_name');
		return $this->fetchAll($select)->toArray();
	}
}