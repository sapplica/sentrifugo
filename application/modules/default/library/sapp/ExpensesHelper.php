<?php
class sapp_ExpensesHelper 
{
	//update trip status based on its expense status
	public static function tripstatus($trip_id)
	{
		if($trip_id>0)
		{
			$status = '';
			$tripsModel = new Expenses_Model_Trips();
			$expenseStatusArray = $tripsModel->getTripExpenses($trip_id);
			$statusArray = array();
			foreach($expenseStatusArray as $status)
			{
				$statusArray[] = $status['status'];
			}
			if(in_array('rejected',$statusArray))
			{
				$status = 'R';
			}else if(in_array('saved',$statusArray))
			{
				$status = 'NS';
			}else if(in_array('submitted',$statusArray))
			{
				$status = 'S';
			}else if(in_array('approved',$statusArray))
			{
				$status = 'A';
			}
			
			$auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
			}
			$data = array('status'=>$status,'modifiedby'=>$loginUserId,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$trip_id);
			$tripsModel->saveOrUpdateTripsData($data,$where);
		}
	}
   
}			   
?>

