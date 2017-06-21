
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
 *
* @model Client Model
* @author sagarsoft
*
*/
class Assets_Model_AssetCategories extends Zend_Db_Table_Abstract
{
	protected $_name = 'assets_categories';
	protected $_primary = 'id';

	/**
	 * This will fetch all the asset category details based on the search paramerters passed with pagination.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $perPage
	 * @param number $pageNo
	 * @param JSON $searchData
	 * @param string $call
	 * @param string $dashboardcall
	 * @param string $a
	 * @param string $b
	 * @param string $c
	 * @param string $d
	 *
	 * @return array
	 */
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{
		//echo "here";exit;
		$searchQuery = '';
		$havingQuery = '';
		$searchArray = array();
		//$havingArray = array();
		$data = array();

		if($searchData != '' && $searchData!='undefined')
		{
			//if($key == 'SubCategoryCount') $key = 'COUNT(c2.parent)';
			
			$searchValues = json_decode($searchData);
			foreach($searchValues as $key => $val)
			{
				if($key == 'name'){
					$searchQuery .= " ".'c1.'.$key." like '%".$val."%' AND ";
				}else if($key == 'SubCategoryCount'){
					$havingQuery = " ".$key." = '".$val."'  ";
					} 
			
				$searchArray[$key] = $val;
			}
			$searchQuery = rtrim($searchQuery," AND");
		}
		$objName = 'assetcategories';
		$tableFields = array(
				'action'=>'Action',
				'name' => 'Category',
				'SubCategoryCount' => 'Sub Category(s)' ,
				);
		$tablecontent = $this->getAssetCategoriesData($sort, $by, $pageNo, $perPage,$searchQuery,$havingQuery);
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
				'menuName' => 'Asset Categories'
		);
		return $dataTmp;
	}

	/**
	 * This will fetch all the active asset category details.
	 *
	 * @param string $sort
	 * @param string $by
	 * @param number $pageNo
	 * @param number $perPage
	 * @param string $searchQuery
	 *
	 * @return array $assetcategoryData
	 */
	public function getAssetCategoriesData($sort, $by, $pageNo, $perPage,$searchQuery,$havingQuery)
	{
		$where = "";
		if($searchQuery)
			$where = " AND ".$searchQuery;
			$db = Zend_Db_Table::getDefaultAdapter();
		
      $assetcategoriesData =$this->select()
			 ->setIntegrityCheck(false)
			 ->from(array('c1'=>$this->_name),array(('c1.*'),"SubCategoryCount"=>"(SELECT COUNT(*) FROM `assets_categories` WHERE parent = c1.id AND is_active = 1)" ))
			 ->where('c1.is_active = 1 AND  c1.parent = 0'.$where)
			 ->group(array("c1.id"))
			 ->order("$by $sort")
		     ->limitPage($pageNo, $perPage);
			 if($havingQuery!='')
			 $assetcategoriesData->having("$havingQuery");
		return $assetcategoriesData;
	
	}

	/**
	 * This method will save or update the client details based on the client id.
	 *
	 * @param array $data
	 * @param string $where
	 */
	public function saveOrUpdateAssetCategoriesData($data, $where){
		
		if($where != ''){
		
					
			 $this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			
			$id=$this->getAdapter()->lastInsertId($this->_name);
			return $id;
		}
	}

	/**
	 * This method is used to fetch client details based on id.
	 *
	 * @param number $id
	 */
	public function getAssetCategoriesDetailsById($id)
	{

  $assetcategoriesData = $this->select()
					->setIntegrityCheck(false)
					->from(array('c1'=>$this->_name),array('c1.*'))
					->joinLeft(array('c2'=>$this->_name),"c1.parent = c2.id",array("parent_name"=>'c1.name'))
					->order('c1.id' )
					->where('c1.is_active = 1 AND c1.id='.$id.' ');
		

     return  $this->fetchAll($assetcategoriesData)->toArray();
		
	}
	
	
	public function getAssetSubCategoriesDetailsById($catid)
	{
		
	 	 $assetsubcategoriesData = $this->select()
		->setIntegrityCheck(false)
		->from(array('c1'=>$this->_name),array('c1.*'))	
		->where('c1.is_active = 1 AND c1.parent='.$catid.' ')
		->order('c1.id' );
		return $this->fetchAll($assetsubcategoriesData)->toArray();
	
	}

	/**
	 * This method returns all active clients to show in projects screen
	 *
	 * @return array
	 */
	public function getActiveAssetCategoriesData()
	{
		
		
	$select=$this->select()
		->setIntegrityCheck(false)
		->from(array('c1'=>$this->_name),array('c1.id','c1.name','c2.name'))
		->joinLeft(array('c2'=>$this->_name),"c1.parent = c2.id",array("parent_name"=>'c2.name'))
		->order('c1.id')
		->where('c1.is_active =1 ');
		
		return $this->fetchAll($select)->toArray();
	}
	//to get assest category  for settings.
	public function getActiveCategoriesData($bunitid,$deptid='')
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$cat_ids= array();
		$final_result = array();
		$where = 'sc.isactive=1 and sc.request_for=2';
		$resWhere ='ac.is_active=1 and ac.parent=0';		
				
		if($bunitid != '' && $bunitid !='null') {
			$where .= ' AND sc.businessunit_id = '.$bunitid.'';
			$catqry= " SELECT a.category FROM assets a WHERE a.isactive=1 AND a.location=$bunitid" ;
			$cat_ids = $db->query($catqry)->fetchAll();
		}	
		if($deptid !='' && $deptid !='null')
		{
			
			$where .= '  AND sc.department_id = '.$deptid.'';
		}	
		$qry = "select sc.service_desk_id from main_sd_configurations sc where ".$where." ";
		$res = $db->query($qry)->fetchAll();
		
		$categoryIds = '';
		$configured_cat_id_array = array();
		$available_asset_cat_id_array = array();
		if(!empty($res))
		{
			
			foreach ($res as $ids)
			{
				$config_cat_id_array[]=$ids['service_desk_id'];
			}
			$configured_cat_id_array = array_unique($config_cat_id_array);
		}
		
		if(!empty($cat_ids))
		{
			foreach ($cat_ids as $cat_id)
			{
				$unique_cat_id_array[] = $cat_id['category'];
			}
			$available_asset_cat_id_array = array_unique($unique_cat_id_array);
			
		}
		
		$result = array();
		if(!empty($configured_cat_id_array) && !empty($available_asset_cat_id_array)) {
			$result=array_diff($available_asset_cat_id_array,$configured_cat_id_array);
		}elseif(empty($configured_cat_id_array) && !empty($available_asset_cat_id_array)){
			$result=$available_asset_cat_id_array;
		}
				
		if(!empty($result)) {
			foreach ($result as $res_id)
			{
				$categoryIds.=$res_id.',';
			}
			$categoryIds = rtrim($categoryIds,',');
			$resWhere.=  ' AND ac.id IN ('.$categoryIds.')';
		}
		
		if($bunitid != '' && $bunitid !='null') {
			if(!empty($result)) {
	 			$resultqry = "select ac.id,ac.name from assets_categories ac where ".$resWhere." ";
			}	
		}
		/*else{
			$resultqry = "select ac.id,ac.name from assets_categories ac where ".$resWhere." ";
		}	*/
		
		if(!empty($resultqry)) {
			$final_result = $db->query($resultqry)->fetchAll();
		}
		return $final_result;
		
	}
	public function getAssetUserLogData()
	{
 	 $select = $this->select()
		->setIntegrityCheck(false)
		->from(array('ah'=>'assets_history'),array('ah.createddate','ac.name','u.userfullname'))
		->joinLeft(array('ac'=>'assets'),"ah.asset_id = ac.id",array())
		->joinLeft(array('u'=>'main_users'),"u.id = ah.user_id",array())
		->where('ah.isactive = 1 '); 
		
      return $this->fetchAll($select)->toArray();
		
	}
	public function isCategoryExistForasset($category_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT COUNT(*) as count FROM assets WHERE isactive =1 AND category=".$category_id;
		$result = $db->query($query)->fetch();
		return $result['count'];
	}
	public function isCategoryExistForassetSettings($category_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT COUNT(*) as count FROM main_sd_configurations WHERE isactive =1 AND request_for=2 AND service_desk_id=".$category_id;
		$result = $db->query($query)->fetch();
		return $result['count'];
		
	}
	public function getUserAssetData($userid)
	{
	  $select = $this->select()
            ->setIntegrityCheck(false)
		    ->from(array('ac'=>'assets'),array())
		    ->joinLeft(array('mu'=>'main_users')," mu.id=ac.allocated_to",array('mu.userfullname','ac.created','ac.name'))
		    ->where('mu.isactive = 1  AND ac.isactive = 1 AND  mu.id ='.$userid.'');
	
		return $this->fetchAll($select)->toArray();
	
	}
	public function getCategoryBYId($id)
	{
		$assetcategoriesData = $this->select()
					->setIntegrityCheck(false)
					->from(array('c1'=>$this->_name),array('c1.*'))
					->where('c1.is_active = 1 AND c1.id='.$id.' ');
		return $this->fetchAll($assetcategoriesData)->toArray();			
	}
	
	public function getActiveAssetCategory()
	{
		$assetcategoriesData = $this->select()
					->setIntegrityCheck(false)
					->from(array('c1'=>$this->_name),array('c1.id','c1.name'))
					->where('c1.is_active = 1 AND c1.parent=0 ');
		return $this->fetchAll($assetcategoriesData)->toArray();			
	}
	
	
	
}