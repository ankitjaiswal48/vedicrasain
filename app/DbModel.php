<?php
namespace App;
use Illuminate\Support\Facades\Auth;
use Eloquent;
use DB;
class DbModel extends Eloquent {
	

	/**
	 *Get all users
	 *
	 * @return Response
	 */
	public function get_all_users_internal_only($type=NULL,$addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		
		
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>1,'a.addedBy'=>$addedBy);
		}else{
			$data_arr= array('a.is_deleted'=>0,'a.userType'=>1,'a.addedBy'=>$addedBy);
		}
		 return  DB::table('users as a')->select('a.*','b.typeName','c.driver')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('user_additional as c', 'a.id', '=', 'c.parentId')->where($data_arr)->whereNotIn('a.id',$LogInId)->get();
		
			
	}
	
	public function get_all_users($where_arr=NULL){
	
		$userId = Auth::user()->id;
		
		$where_arr['a.is_deleted']=0;
              
              
		
		 $query = DB::table('users as a')
		 ->select('a.*','b.typeName')
		 ->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('user_additional as c', 'c.parentId', '=', 'a.id');
		 if(!empty($where_arr)){
			 
			 $query->where($where_arr);
		 }
		  $query->where('a.id','!=',$userId);
			return $query->get();
		
			
	}
	
	/**
	 *Get all Vendor
	 *
	 * @return Response
	 */
	public function get_all_vandors($type=NUll,$addedBy){
		$data_arr='';
		$LogInId[] = Auth::user()->id;
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>2,'a.addedBy'=>$addedBy);
		}else{
			$data_arr= array('a.is_deleted'=>0,'a.userType'=>2,'a.addedBy'=>$addedBy);
		}
		
		 return  DB::table('users as a')->select('a.*','b.typeName','c.companyName')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('vendor as c', 'c.parentId', '=', 'a.id')->where($data_arr)->whereNotIn('a.id',$LogInId)->whereNull('c.ext_link')->get();
	}
	
	
	public function get_vendor_detail_by_id($id){
		$data_arr='';
		$LogInId[] = Auth::user()->id;
		
			$data_arr = array('a.id'=>$id);
		
		 return  DB::table('users as a')->select('a.*','b.typeName','c.*')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('vendor as c', 'c.parentId', '=', 'a.id')->where($data_arr)->whereNotIn('a.id',$LogInId)->whereNull('c.ext_link')->first();
	}
	
	public function get_user_detail_by_id($id){
		$data_arr='';
		$LogInId[] = Auth::user()->id;
		
			$data_arr = array('a.id'=>$id);
		
		 return  DB::table('users as a')->select('a.*','b.typeName','c.*')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('user_additional as c', 'c.parentId', '=', 'a.id')->where($data_arr)->whereNotIn('a.id',$LogInId)->first();
	}
	


	/**
	 *Get Logged in user
	 *
	 * @return Response
	 */
	public function get_all_user_type(){
		 return  DB::table('usertype')->where(array('status'=>0))->get();
	
	}
	
	public function get_all_vendor_type(){
		 return  DB::table('vendor_type')->get();
	
	}
	
	
	
	/**
	 *Serach user by licence plate
	 *
	 * @return Response
	 */
	public function serach_user_by_licencePlate($licence_plate){
		
	if(!empty($licence_plate)){
		
		return DB::table('users as a')
		 ->select('a.*','b.licence_plate','b.userId as self','c.addedBy as NotificationFrom','c.notification_type')
		 ->leftJoin('vehicle as b', 'b.addedBy', '=', 'a.id')
		 ->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')
		 ->where(array('a.status'=>1,'a.is_deleted'=>0,'b.licence_plate'=>$licence_plate,'a.userType'=>1,'a.addedType'=>"External"))
		 ->groupBy('b.licence_plate')
		 ->first();	
		
	}
		 
	 
	
	}
	/**
	 * Serach vendor by company name
	 *
	 * @return Response
	 */
	public function serach_vendor_by_companyName($companyName){
		if(!empty($companyName))
     		 return DB::table('users as a')
		 ->select('a.*','b.*','c.addedBy as NotificationFrom','c.notification_type')
		 ->leftJoin('vendor as b', 'b.parentId', '=', 'a.id')
		 ->leftJoin('invite_user as c', 'c.userId', '=', 'b.parentId')
		 ->where(array('b.companyName'=>$companyName,'a.is_deleted'=>0,'a.userType'=>2,'a.addedType'=>"External",'a.status'=>1))
		 ->first();  
	
	}
	/**
	 * Get all driver.
	 *
	 * @return Response
	 */
	public function get_all_driver($addedBy=NULL){
		
		$data_arr = array('a.is_deleted'=>0,'a.userType' => 1,'b.driver'=>3);
		if(!empty($addedBy)){
			$data_arr['b.addedBy'] = $addedBy;
		}
     		 return DB::table('users as a')->select('a.*','b.*')->leftJoin('user_additional as b', 'b.parentId', '=', 'a.id')->where($data_arr)->get();  
	
	}
		
	/**
	 *Get all users
	 *
	 * @return Response
	 */
	public function get_users_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('a.id'=>$id,'a.is_deleted'=>0);
		}else{
			$data_arr= array('a.is_deleted'=>0);
		}
		 return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	
	}

	/**
	 *Get date time second and minit get 
	 *
	 * @return Response
	 */
 	public function secondsToWords($db_datetime)
    {
        $newDate = date('Y-m-d H:i:s', strtotime($db_datetime));
        $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($newDate);
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);
        if($day > 0)
        {
  		  return  date('g:ia \o\n l jS F Y', strtotime($newDate));
        }
        if($hours > 0)
        {
          return $hours.' hours ago';
        }
       
        if($mins > 0)
        {
          return $mins.' min ago';
        }
        
        if($secs > 0)
        {
            return $secs.' seconds ago';
        }
       
    }
	/**
	 *Get all connected users or vendors ETC.
	 *
	 * @return Response
	 */
	 
	 public function Contact_Connections_searchnew($type,$userId=NULL,$status,$request,$limit=NUll,$offset=NULL){
		 if($status=="sent"){
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit))
		{
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		$addedBy =  Auth::user()->id;
		$where = " c.addedBy='$userId'";
		$where .= "AND a.is_deleted='0'";
		$where .= "AND a.userType='$type'";
	    $where .= "AND c.connect!='2'";
		$where .= "AND c.connect!='0'";
		$where .= "AND c.connect!='4'";
                $where .= "AND c.connect!='5'";
		$where .= "AND a.id!='$userId'";
		
		$wheres = " c.userId='$userId'";
		$wheres .= "AND a.is_deleted='0'";
		$wheres .= "AND a.userType='$type'";
	    $wheres .= "AND c.connect!='2'";
		$wheres .= "AND c.connect!='0'";
		$wheres .= "AND c.connect!='4'";
                $wheres .= "AND c.connect!='5'";
		$wheres .= "AND a.id!='$userId'";
		if( !empty($request['userName']) ) {
			
			$where.=" AND a.name LIKE '".$request['userName']."%' "; 
		}
		if( !empty($request['userName']) ) {
			
			$wheres.=" AND a.name LIKE '".$request['userName']."%' "; 
		}
		
		$sqlQuery .= "SELECT a.*,c.addedBy,c.keyword,b.companyName FROM  `users` as a LEFT JOIN invite_user as c ON (a.id = c.userId) OR (a.id = c.addedBy)  LEFT JOIN vendor as b ON b.parentId = a.id WHERE (".$where." ) OR (".$wheres.") ";
		// echo $sqlQuery;die;
		
		//echo $sqlQuery;die;
		 $result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
		 }
		
	 }
	 
	 
	public function Contact_Connections_search($type,$userId=NULL,$status){
		$data_arr ='';
		$connect[]=2;
		$connect[]=0;
		$connect[]=4;
                $connect[]=5;
		if($status=="sent")
		{
			if(!empty($userId)){
				$data_arr = array('a.userType'=> @$type,'a.is_deleted'=>0,'c.addedBy' => @$userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.addedBy' => @$userId);
			}
			return DB::table('users as a')
			->select('a.*','c.addedBy','c.keyword', DB::raw('CONCAT(a.first_name, " ", a.last_name) AS name'),'b.companyName')
			->leftJoin('invite_user as c', 'a.id', '=', 'c.userId')
			->leftJoin('vendor as b', 'b.parentId', '=', 'a.id')
			->where($data_arr)
			->whereNotIn('c.connect',$connect)
			->get();
		}
		if($status=="receive")
		{
			if(!empty($userId)){
				$data_arr = array('a.userType'=> @$type,'a.is_deleted'=>0,'c.userId' => @$userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.userId' => @$userId);
			}
			return DB::table('users as a')
			->select('a.*','c.addedBy','c.keyword', DB::raw('CONCAT(a.first_name, " ", a.last_name) AS name'),'b.companyName')
			->leftJoin('invite_user as c', 'a.id', '=', 'c.addedBy')
			->leftJoin('vendor as b', 'b.parentId', '=', 'a.id')
			->where($data_arr)
			->whereNotIn('c.connect',$connect)
			->get();
		}
	}
	
	/**
		*Get all sending request of user but  request is Now pending
		*1. Here userTypeID is type of user which type we want to get like: veendor,user,driver etc.
		*2. Here userId is id of user whose pending request count we want to get like:if user want to get own connection Now put own id Otherwise Another
		* @return Response
	*/
		public function get_all_send_pending_request($userTypeID,$userId=NULL){
			if($userTypeID==2)
			{
			$data_arr ='';
			$connect[]=2;
			$connect[]=1;
			$connect[]=4;
                        $connect[]=5;
			$LogInId[] = Auth::user()->id;
			if(!empty($userId)){
				$data_arr = array('c.userType'=> $userTypeID,'a.is_deleted'=>0,'c.addedBy' => $userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.addedBy' => $userId);
			}
			 return DB::table('users as a')->select('a.*','c.addedBy','b.type as vendorTYPE','c.keyword as Recognize','d.Title')->leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->leftJoin('vendor as b', 'a.id', '=', 'b.parentId')->leftJoin('vendor_type as d', 'd.id', '=', 'b.type')->where($data_arr)->whereNotIn('c.connect',$connect)->whereNotIn('a.id',$LogInId)->get();
			}
			else
			{
				$data_arr ='';
			$connect[]=2;
			$connect[]=1;
			$connect[]=4;
                        $connect[]=5;

			$LogInId[] = Auth::user()->id;
			if(!empty($userId)){
				$data_arr = array('c.userType'=> $userTypeID,'a.is_deleted'=>0,'c.addedBy' => $userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.addedBy' => $userId);
			}
			 return DB::table('users as a')->select('a.*','c.*','c.keyword as Recognize','c.keyword as Title')->leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->where($data_arr)->whereNotIn('c.connect',$connect)->whereNotIn('a.id',$LogInId)->get();
		
			}
		}
	/**
		*Get all reiceved request of user but  request is Now pending
		*1. Here userTypeID is type of user which type we want to get like: veendor,user,driver etc.
		*2. Here userId is id of user whose pending request count we want to get like:if user want to get own connection Now put own id Otherwise Another
		* @return Response
	*/
	
		public function get_all_recieved_pending_request($userTypeID,$userId=NULL){
			//echo $userTypeID;die;
			if($userTypeID==2)
			{
			$data_arr ='';
			$LogInId[] = Auth::user()->id;
			$connect[]=2;
			$connect[]=1;
			$connect[]=4;
                        $connect[]=5;
			if(!empty($userId)){
				$data_arr = array('a.userType'=> $userTypeID,'a.is_deleted'=>0,'c.userId' => $userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.userId' => $userId);
			}
			//print_r($data_arr);die;
			// return DB::table('users as a')->select('a.*','c.*')->
			// leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->
			// where($data_arr)->whereNotIn('c.connect',$connect)->
			// whereNotIn('a.id',$LogInId)->get();//this code is commented  by nikita
			
			 return DB::table('users as a')->select('a.*','c.addedBy','b.type as vendorTYPE','d.Title')->
			 leftJoin('invite_user as c', 'a.id', '=', 'c.addedBy')->leftJoin('vendor as b', 'a.id', '=', 'b.parentId')->leftJoin('vendor_type as d', 'd.id', '=', 'b.type')->
			 where($data_arr)->whereNotIn('c.connect',$connect)->
			 get();
			}
			else
			{
			$data_arr ='';
			$LogInId[] = Auth::user()->id;
			$connect[]=2;
			$connect[]=1;
			$connect[]=4;
                        $connect[]=5;
			if(!empty($userId)){
				$data_arr = array('a.userType'=> $userTypeID,'a.is_deleted'=>0,'c.userId' => $userId);
			}else{
				$data_arr = array('a.is_deleted'=>0,'c.userId' => $userId);
			}
			//print_r($data_arr);die;
			// return DB::table('users as a')->select('a.*','c.*')->
			// leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->
			// where($data_arr)->whereNotIn('c.connect',$connect)->
			// whereNotIn('a.id',$LogInId)->get();//this code is commented  by nikita
			
			 return DB::table('users as a')->select('a.*','c.*','c.keyword as Recognize','c.keyword as Title')->
			 leftJoin('invite_user as c', 'a.id', '=', 'c.addedBy')->
			 where($data_arr)->whereNotIn('c.connect',$connect)->
			 get();
				
			}
		}
		
		/**
		*Get points of user to invite for join 
		* @return Response
	*/
		public function get_invite_points($addedBy=NULL){
		if(!empty($addedBy)){
			$data_arr = array('notification_type'=>4,'addedBy'=>$addedBy);
		}else{
			$data_arr= array('notification_type'=>4);
		}
		 return  DB::table('invite_user')->where($data_arr)->get();
	
	}
	/* documnet section */
			public function get_user_vehicle($where_arr=NULL){
				
				$query = DB::table('vehicle');
				if(!empty($where_arr)):
					$query->where($where_arr);
				endif;
				return $query->get();

			}
			
	public function get_insurance_company(){
		
		 return  DB::table('insurance_company')->get();
	
	}
	
	
	
	
	
	
	public function get_all_insurance_count($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('c.user_id'=>$addedBy,'c.insurance_type'=>"0",'c.is_deleted'=>0);
		}
		 //return  DB::table('insurance')->where($data_arr)->get();
	
	return  DB::table('insurance as c')->select('c.*','d.partialDate')
	->leftJoin('partial_paid_document as d', 'd.id', '=', DB::raw("(SELECT dd.id                
         FROM partial_paid_document as  dd
         WHERE c.id = dd.document_parentId AND c.user_id=$addedBy AND dd.document_parentType='insurance' AND dd.partialPaid='0'
         ORDER BY dd.id 
         LIMIT 1)"))->where($data_arr)->get();
	
	
    }
	
	public function get_all_insurance_counttwo($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('c.user_id'=>$addedBy,'c.insurance_type'=>"1",'c.is_deleted'=>0);
		}
		 //return  DB::table('insurance')->where($data_arr)->get();
	
	return  DB::table('insurance as c')->select('c.*','d.partialDate')
	->leftJoin('partial_paid_document as d', 'd.id', '=', DB::raw("(SELECT dd.id                
         FROM partial_paid_document as  dd
         WHERE c.id = dd.document_parentId AND c.user_id=$addedBy AND dd.document_parentType='insurance' AND dd.partialPaid='0'
         ORDER BY dd.id 
         LIMIT 1)"))->where($data_arr)->get();
	
	
	
	
	
	
	
	
	
	
    }
	
	
	public function get_all_roadtax_count($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
		 return  DB::table('road_tax')->where($data_arr)->get();
	
	
    }
	public function get_all_automobiletax_count($addedBy=NULL)
	{
		
		 
		 
		 if(!empty($addedBy))
		{
			$data_arr = array('c.user_id'=>$addedBy,'c.is_deleted'=>0);
		}
		 //return  DB::table('insurance')->where($data_arr)->get();
	
	return  DB::table('automobile_tax as c')->select('c.*','d.partialDate')
	->leftJoin('partial_paid_document as d', 'd.id', '=', DB::raw("(SELECT dd.id                
         FROM partial_paid_document as  dd
         WHERE c.id = dd.document_parentId AND c.user_id=$addedBy AND dd.document_parentType='automobile' AND dd.partialPaid='0'
         ORDER BY dd.id 
         LIMIT 1)"))->where($data_arr)->get();
	
		 
		 
	
	
    }
	public function get_all_inspection_count($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
		 return  DB::table('inspection')->where($data_arr)->get();
	
	
    }
	public function get_all_leasing_count($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
		 return  DB::table('leasing')->where($data_arr)->get();
	
	
    }
	public function get_all_fine_count($addedBy=NULL)
	{
		if(!empty($addedBy))
		{
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
		 return  DB::table('fines')->where($data_arr)->get();
	
	
    }
	public function get_insurance_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('a.id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('insurance as a')->select('a.*','b.logo')->leftJoin('insurance_company as b','b.company_name','=','a.insurance_company')->where($data_arr)->first();
	 
	 
	}
	public function get_insurance_by_ids($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('document_parentId'=>$id,'document_parentType'=>'insurance');
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('partial_paid_document')->where($data_arr)->get();
	 
	 
	}
	public function get_roadtax_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('road_tax')->where($data_arr)->first();
	 
	 
	}
	public function get_automobiletax_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('automobile_tax')->where($data_arr)->first();
	 
	 
	}
	
	public function get_automobiletax_by_ids($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('document_parentId'=>$id,'document_parentType'=>'automobile');
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('partial_paid_document')->where($data_arr)->get();
	 
	 
	}
	public function get_inspection_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('inspection')->where($data_arr)->first();
	 
	 
	}
	public function get_leasing_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('leasing')->where($data_arr)->first();
	 
	 
	}
	public function get_fine_by_id($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('fines')->where($data_arr)->first();
	 
	 
	}
	
	/**
		*Get Insurance company from vendor table 
		* @return Response
	*/
	public function get_all_vendors_forclaim($type=NUll,$addedBy)
	{
		$data_arr='';
		$LogInId[] = Auth::user()->id;
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>2,'a.addedBy'=>$addedBy);
		}
		$arr=array('a.id'=>$LogInId);
		return  DB::table('users as a')->select('a.*','b.*')->leftJoin('vendor as b', 'b.parentId', '=', 'a.id')->where($data_arr)->whereNotIn('a.id',$LogInId)->whereNull('b.ext_link')->get();
	
	}
	
	
	
	/**
		*Get all user 
		* @return Response
	*/
	public function get_all_internal_user($type=NULL,$addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		
		
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>3,'a.addedBy'=>$addedBy);
			
			$data_arr1 = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>1,'a.addedBy'=>$addedBy);
		}
		 return  DB::table('users as a')->select('a.*','b.*')->leftJoin('user_additional as b', 'b.parentId', '=', 'a.id')->where($data_arr)->orWhere($data_arr1)->whereNotIn('a.id',$LogInId)->get();
		
			
	}
	
	public function get_all_insurance_policy($addedBy=NULL)
	{
		$query = DB::table('insurance');
		
		if(!empty($addedBy)){
			$query->where('user_id',$addedBy);
		}
		
		return $query->get();
		 
		
	}
	
	///**
//		*Get get insurance detail by plate from insurance table
//		* @return Response
//	*/
//	public function get_insurance_detail_by_plate($plate,$addedBy)
//	{
//		if(!empty($addedBy)){
//			$data_arr = array('user_id'=>$addedBy,'vehicle_licence_plate'=>$plate,'is_deleted'=>0);
//		}
//		 
//	 return  DB::table('insurance')->where($data_arr)->get();
//	 
//		
//	}
	/**
		*Get get all insurance claim detial for the user
		* @return Response
	*/
	public function get_All_Insurance_Claim($addedBy)
	{
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
		 
	 return  DB::table('insurance_claim')->where($data_arr)->get();
	 
		
	}
	
	
	/**
		*Get get all insurance claim detial for the user
		* @return Response
	*/
	public function get_Claim_This_Year($addedBy,$start_date,$end_date)
	{ 
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0);
		}
	 //$range = [$start_date, $end_date];
	 return  DB::table('insurance_claim')->where($data_arr)->whereBetween('date_of_filling',array($start_date,$end_date))->get();
	 
		
	}
	/**
		*Get get all insurance claim detial by id
		* @return Response
	*/
	public function get_insurance_claim_by_id($id,$addedBy)
	{
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0,'id'=>$id);
		}
		 
	 return  DB::table('insurance_claim')->where($data_arr)->first();
	 
		
	}
	/**
		*Get get all insurance claim detial for the user
		* @return Response
	*/
	public function get_fault_driver_this_year($addedBy,$start_date,$end_date,$res)
	{  
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy,'is_deleted'=>0,'responsible'=>$res);
		}
	 return  DB::table('insurance_claim')->where($data_arr)->whereBetween('date_of_filling',array($start_date,$end_date))->get();
	 
	}
	/**
		*Get get all vehicle brand
		* @return Response
	*/
	public function get_vehicle_brand()
	{  
		
	 return  DB::table('bcb_brands')->get();
	 
	}
	/**
		*Get get all vehicle added by user
		* @return Response
	*/
	public function get_all_vehicle($addedBy){
		if(!empty($addedBy)){
			$data_arr = array('a.addedBy'=>$addedBy);
			}

  return  DB::table('traccar36.devices as a')->select('a.*')->get();


			//return  DB::table('thecoder_fleet1.vehicle as //a')->select('a.*','b.name','c.phone','c.photo','d.uniqueid','d.motion','e.latitude','e.longitude','e.attributes')->leftJoin('thec//oder_fleet1.users as b', 'a.driver_id', '=', 'b.id')->leftJoin('thecoder_fleet1.user_additional as c', 'c.parentId', '=', //'b.id')->leftJoin('traccar36.devices as d', 'd.id', '=', 'a.deviceId')->leftJoin('traccar36.positions as e', 'e.id', '=', //'d.positionid')->where($data_arr)->get();
		return  DB::table('vehicle as a')->select('a.*','b.name','c.phone','c.photo')->leftJoin('users as b', 'a.driver_id', '=', 'b.id')->leftJoin('user_additional as c', 'c.parentId', '=', 'b.id')->where($data_arr)->get();
		 //return DB::table('users as a')->select('a.*','c.*')->leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->where($data_arr)->whereNotIn('c.connect',$connect)->whereNotIn('a.id',$LogInId)->get();
//return  DB::table('users as a')->select('a.*','b.licence_plate','c.addedBy as NotificationFrom','c.notification_type')->leftJoin('vehicle as b', 'b.addedBy', '=', 'a.id')->leftJoin('invite_user as c', 'c.userId', '=', 'b.userId')->where(array('a.status'=>1,'a.is_deleted'=>0,'b.licence_plate'=>$licence_plate,'a.userType'=>1))->groupBy('b.licence_plate')->get();			
	}
	

	
	public function get_total_purchase_price($addedBy){
		if(!empty($addedBy)){
			$data_arr = array('addedBy'=>$addedBy);
			}
			
			return  DB::table('vehicle')->where($data_arr)->sum('purchase_price');
	}
	
	
	/**
		*Get get all vehicle brand
		* @return Response
	*/
	public function get_all_internal_driver($type=NULL,$addedBy,$withoutConnect=NULL){
		
		$data_arr = array(
				'a.addedType'=>$type,
				'a.is_deleted'=>0,
				'b.driver'=>3,
				'a.userType'=>3,
				'a.addedBy'=>$addedBy
			);
		if(!empty($withoutConnect)){
			return  DB::table('users as a')
			 ->select('a.*','b.*')
			 ->leftJoin('user_additional as b', 'b.parentId', '=', 'a.id')
			 ->where($data_arr)
			  ->orWhere('b.vehicle_assignment','=','')
			 ->whereNull('b.vehicle_assignment')
			 
			 ->get();
		}else{
			 return  DB::table('users as a')
			 ->select('a.*','b.*')
			 ->leftJoin('user_additional as b', 'b.parentId', '=', 'a.id')
			 ->where($data_arr)
			 ->get();
		}	
		
		
			
	}
	public function get_all_group($userId)
	{
		if(!empty($userId)){
			$data_arr = array('user_id'=>$userId,'is_deleted'=>0);
		}
		 
	 return  DB::table('vehicle_group')->where($data_arr)->get();
	 
	 
	}
	/**
		*Get get the model of vehicle on the basis of make
		* @return Response
	*/
	public function get_model($MakeSelected)
	{
		if(!empty($MakeSelected)){
			$data_arr = array('brand_id'=>$MakeSelected);
		}
		 
	 return  DB::table('bcb_models')->where($data_arr)->get();
	 
	 
	}
	/**
		*Get get the fuel type  vehicle on the basis of vehicle id and user who added the vehicle
		* @return Response
	*/
	public function get_fuel_type($MakeSelected)
	{
		if(!empty($MakeSelected)){
			$data_arr = array('licence_plate'=>$MakeSelected);
		}
		 
	 return  DB::table('vehicle')->where($data_arr)->get();
	 
	 
	}
	/**
		*Get get the fuel entry list of the vehicle added by current user
		* @return Response
	*/
	public function get_all_fuel_entry_list($addedBy)
	{
		if(!empty($addedBy))
		{
			
			$data_arr = array('a.user_id'=>$addedBy,'a.is_reset'=>0);
		}
		
	 return  DB::table('fuel as a')->select('a.*','b.licence_plate','b.make','b.model','b.status','b.file as Vehicle_Image','c.name')->leftJoin('vehicle as b', 'b.licence_plate', '=', 'a.vehicle_licence_plate')->leftJoin('bcb_brands as c', 'c.id', '=', 'b.make')->where($data_arr)->orderBy('a.created_at', 'asc')->
	 
get();
	 }
	
	public function get_all_fuel_entry_list_dataTable($request,$limit=NULL,$offset=NULL)
	{
		$where="a.is_deleted='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit))
		{
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		$addedBy =  Auth::user()->id;
		$where .= " AND a.user_id='$addedBy'";
		
		if(!empty($_REQUEST['paymentStatus']))
		{
			$where .=" AND a.paid ='".$request['paymentStatus']."'";
		}
		if(isset($_REQUEST['paymentStatus']))
		{
			if($_REQUEST['paymentStatus']=='0')
			$where .=" AND a.paid ='".$request['paymentStatus']."'";
		}
		
		if( !empty($request['servicePerformed']) ) {
 
			$where .= "AND a.services_performed IN (".(implode(',',$request['servicePerformed'])).")";
			
		}
		$sqlQuery .= "SELECT a.*,b.licence_plate,b.make,b.model,b.status,b.file as Vehicle_Image,b.type,c.name FROM  `fuel` as a LEFT JOIN vehicle as b ON b.licence_plate = a.vehicle_licence_plate LEFT JOIN bcb_brands as c ON c.id = b.make WHERE ".$where." ";
	    if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$sqlQuery.=" order by a.created_at DESC ";
		
		//echo $sqlQuery;die;
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		return $result;
		
	}
	
	
	/**
		*Get get all insurance claim detial by id
		* @return Response
	*/
	public function get_fuel_detail_by_id($id)
	{
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 
	 return  DB::table('fuel')->where($data_arr)->first();
	 
		
	}
	
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_all_vehicle_with_device($addedBy){
		if(!empty($addedBy)){
			$data_arr = array('a.addedBy'=>$addedBy);
			}
			return DB::table('vehicle as a')->select('a.*','b.name','c.phone','c.photo','c.licence_no','e.vehicle_licence_plate','f.*')->leftJoin('users as b', 'a.driver_id', '=', 'b.id')
			->leftJoin('user_additional as c', 'c.parentId', '=', 'b.id')->leftJoin('fuel as f','a.licence_plate','=','f.vehicle_licence_plate')->
			 join('device as e', 'a.licence_plate', '=', 'e.vehicle_licence_plate')->where($data_arr)->
			get();
					 			
	}
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_vehicle_detail($vehicle_id){
		
			$data_arr = array('a.vehicle_id'=>$vehicle_id);
			
			
			 return  DB::table('vehicle as a')->select('a.*','b.name as brand_name','c.name as driver_name')->leftJoin('bcb_brands as b','a.make','=','b.id')->leftJoin('users as c','a.driver_id','=','c.id')->where($data_arr)->first();
					 			
	}
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_driver_id_byName($driver_name,$userId){
		if(!empty($driver_name)){
			$data_arr = array('name'=>$driver_name,'addedBy'=>$userId);
			}
			
			 return  DB::table('users')->where($data_arr)->value('id');
					 			
	}
	
	public function get_vehicle_id_bylicenceplate($licence_plate,$userId=NULL,$vehicleId=NULL){
		
		$data_arr = array('licence_plate'=>$licence_plate);
		if(!empty($userId)){
				$data_arr['addedBy']=$userId;
			}
			$query=  DB::table('vehicle')
			 ->where($data_arr);
			 if(!empty($vehicleId)){
				$query->where('vehicle_id', '!=', $vehicleId);
		}
		return $query->value('vehicle_id');
					 			
	}
	
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_make_id_byName($make_name){
		if(!empty($make_name)){
			$data_arr = array('name'=>$make_name);
			}
			
			 return  DB::table('bcb_brands')->where($data_arr)->value('id');
					 			
	}
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_all_parts_and_accessories_detail($addedBy)
	{
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy);
		}
		 
	 return  DB::table('parts_and_accessories as a')->select('a.*','b.make','b.model','c.name')
	 ->leftJoin('vehicle as b','a.vehicle_licence_plate','=','b.licence_plate')->leftJoin('bcb_brands as c','b.make','=','c.id')->where($data_arr)->get();
	 
		
	}
	
	/**
		*Get get all insurance claim detial by id
		* @return Response
	*/
	public function get_part_detail_by_id($id)
	{
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 
	 return  DB::table('parts_and_accessories')->where($data_arr)->first();
	 
		
	}
	/**
		*Get get all vehicle of current user which has assingend device 
		* @return Response
	*/
	public function get_all_service_entry_list($request=NULL,$limit=NULL,$offset=NULL)
	{
		$where="a.is_deleted='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit))
		{
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id='$addedBy'";
		
		if(!empty($_REQUEST['paymentStatus']))
		{
			$where .="AND a.paid_status ='".$request['paymentStatus']."'";
		}
		
		if( !empty($request['servicePerformed']) ) {
 
			$where .= "AND a.services_performed IN (".(implode(',',$request['servicePerformed'])).")";
			
		}
		$sqlQuery .= "SELECT a.*,b.licence_plate,b.make,b.model,b.status,b.file as Vehicle_Image,b.type,c.name FROM  `service_entry` as a LEFT JOIN vehicle as b ON b.licence_plate = a.vehicle_licence_plate LEFT JOIN bcb_brands as c ON c.id = b.make WHERE ".$where." ";
	    if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		return $result;
		
	}
	
	
	public function update_service($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("service_entry")->where(array("user_id"=>$userId))->whereIn("service_id", $id_arr)->update($data_arr);
	}
	public function update_fuel($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("fuel")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	
	public function update_roadTax($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("road_tax")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	public function update_AutoMobileTax($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("automobile_tax")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function update_Laesing($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("leasing")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function update_Fine($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("fines")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	public function update_Inspection($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("inspection")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	
	
	public function update_insurance($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("insurance")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	/**
	 *Get service detail of the given id
	 *
	 * @return Response
	 */
	public function get_service_detail_by_id($id=NULL,$addedBy){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('service_id'=>$id,'user_id'=>$addedBy);
		}
		//print_r($data_arr);die;
		 return  DB::table('service_entry')->where($data_arr)->first();
	
	}
	/**
	 *get the detailed description of the given service id
	 *
	 * @return Response
	 */
	public function get_service_detail_desc_by_id($id=NULL,$addedBy){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('service_parentid'=>$id,'user_id'=>$addedBy);
		}
		//print_r($data_arr);die;
		 return  DB::table('service_entry_detail')->where($data_arr)->get();
	
	}
	/**
	 *get the reminder detail of given  service id
	 *
	 * @return Response
	 */
	public function get_service_reminder_by_id($id=NULL,$addedBy){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('service_parentId'=>$id,'user_id'=>$addedBy);
		}
		//print_r($data_arr);die;
		 return  DB::table('reminder')->where($data_arr)->get();
	
	}
	public function get_partial_paid_service($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('document_parentId'=>$id,'document_parentType'=>'service');
		}
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('partial_paid_document')->where($data_arr)->get();
	
	}
	/**
	 *Get all external vendor and user
	 *
	 * @return Response
	 */
	public function get_all_external_user_vendor($type=NULL,$addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		//echo $type;die();
		
		$connect[]=2;
		$connect[]=0;
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.status'=>'1','a.is_admin'=>'0','c.addedBy'=>$addedBy,'c.connect'=>'1');
			//$data_arr1 = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.status'=>'1','a.is_admin'=>'0','c.userId'=>$addedBy,'c.connect'=>'1');
		
		 return  DB::table('users as a')->select('a.*','c.connect')->leftJoin('invite_user as c', 'a.id', '=', 'c.userId')->where($data_arr)->whereNotIn('c.connect',$connect)->get();
		
		
			
	}
	public function get_all_external_user_vendor_receive($type=NULL,$addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		//echo $type;die();
		$connect[]=2;
		$connect[]=0;
		
			//$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.status'=>'1','a.is_admin'=>'0','c.addedBy'=>$addedBy,'c.connect'=>'1');
			$data_arr1 = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.status'=>'1','a.is_admin'=>'0','c.userId'=>$addedBy);
		
		 return  DB::table('users as a')->select('a.*','c.connect as connectd','c.invite_id as invite_id')->leftJoin('invite_user as c', 'a.id', '=', 'c.addedBy')->where($data_arr1)->whereNotIn('c.connect',$connect)->get();
		
			
	}
	
	 public function user_vendor_list_sent_receive($type,$userId=NULL,$request,$limit=NUll,$offset=NULL){
		 
		
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit))
		{
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		$addedBy =  Auth::user()->id;
		 $where = " a.addedType='$type'";
		$where .= " AND a.is_deleted='0'";
		$where .= " AND a.is_admin='0'";
               $where .= " AND c.addedBy='$userId'";
	         $where .= " AND c.connect!='2'";
		$where .= " AND c.connect!='0'";
		$where .= " AND a.id!='$userId'";
		
		$wheres = " a.addedType='$type'";
		$wheres .= " AND a.is_deleted='0'";
		$wheres .= " AND a.is_admin='0'";
        $wheres .= " AND c.userId='$userId'";
	    $wheres .= " AND c.connect!='2'";
		$wheres .= " AND c.connect!='0'";
		$wheres .= " AND a.id!='$userId'";

		if( !empty($request['userName']) ) {
			
			$where.=" AND a.name LIKE '".$request['userName']."%' "; 
		}
		if( !empty($request['userName']) ) {
			
			$wheres.=" AND a.name LIKE '".$request['userName']."%' "; 
		}
		
		$sqlQuery .= "SELECT a.*,c.connect from  `users` as a LEFT JOIN invite_user as c ON (a.id = c.userId) OR (a.id = c.addedBy)  LEFT JOIN vendor as b ON b.parentId = a.id WHERE (".$where." ) OR (".$wheres.") ";
		
		
		//cho $sqlQuery;die;
		 $result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
		
		
	 }
	
	public function get_detail_mutual_frnd($mutual_frnd)
	{
		
	return  DB::table('users as a')->select('a.*')->whereIn('a.id',$mutual_frnd)->get();
	}
	
	
	public function check_connection_status($loginUserId,$frndid)
	{
		$data_arr = array('a.userId'=>$loginUserId,'a.addedBy'=>$frndid);
		$data_arr1 = array('a.userId'=>$frndid,'a.addedBy'=>$loginUserId);
		return  DB::table('invite_user as a')->select('a.*')->where($data_arr)->orWhere($data_arr1)->first();
	}
	public function validate_other_user($user_two=NULL,$userId){
		$LogInId[] = Auth::user()->id;
		
		//echo $user_two;echo $userId;die();
			$data_arr = array('a.is_deleted'=>0,'a.status'=>'1','a.is_admin'=>'0','a.id'=>$user_two);
		
		 return  DB::table('users as a')->select('a.*')->where($data_arr)->whereNotIn('a.id',$LogInId)->get();
		
			
	}
	public function get_conversation($user_two=NULL,$userId){
		$LogInId[] = Auth::user()->id;
		
		//echo $user_two;echo $userId;die();
			$data_arr = array('a.user_one'=>$userId,'a.user_two'=>$user_two);
			$data_arr1 = array('a.user_one'=>$user_two,'a.user_two'=>$userId);
		
		 return  DB::table('conversation as a')->select('a.*')->where($data_arr)->orWhere($data_arr1)->first();
		
			
	}
	/**
	 *Get all users who are internal added by logged in user
	 *
	 * @return Response
	 */
	public function get_all_users_internal($type=NULL,$addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		
		
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.addedBy'=>$addedBy);
		}
		 return  DB::table('users as a')->select('a.*')->where($data_arr)->get();
		
			
	}
	/**
	 *Get information of current user
	 *
	 * @return Response
	 */
	public function get_my_info($addedBy){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		
		
		if(!empty($addedBy)){
			$data_arr = array('a.addedType'=>'external','a.is_deleted'=>0,'a.id'=>$addedBy);
		}
		 return  DB::table('users as a')->select('a.*')->where($data_arr)->first();
		
			
	}
	/**
	 *Get task list of current user which are pending and with due date
	 *
	 * @return Response
	 */
	public function get_all_task_duedate($addedBy=NULL){
		
		$data_arr='';$LogInId=array();
		$LogInId[] = Auth::user()->id;
		
		
		if(!empty($addedBy)){
			$data_arr = array('a.user_id'=>$addedBy,'a.status'=>0);
			$data_arrs = array('a.assign_user'=>$addedBy,'a.status'=>0);
			
		}
		 return  DB::table('task as a')->select('a.*','b.name as ASSIGN_USER_NAME')->leftJoin('users as b', 'b.id', '=', 'a.assign_user')->where($data_arr)->get();
		
			
	}
	/**
	 *Get services of particular vendor by their type
	 *
	 * @return Response
	 */
	public function get_services_of_vendor($vendor=NULL){
		
		
		if(!empty($vendor)){
			$data_arr['a.companyName'] = $vendor;
		}
		$data_arr['a.is_deleted'] = 0;
		
		 return  DB::table('vendor as a')->select('b.*','a.type')->leftJoin('vendor_services as b', 'a.type', '=', 'b.vendor_type')->where($data_arr)->groupBy('b.id')->get();
		
			
	}
	public function get_driver_with_vehicle($addedBy){
		if(!empty($addedBy)){
			$data_arr = array('a.addedBy'=>$addedBy);
			}
			return DB::table('vehicle as a')->select('b.name','c.phone','c.photo','c.licence_no','e.vehicle_licence_plate')->leftJoin('users as b', 'a.driver_id', '=', 'b.id')
			->leftJoin('user_additional as c', 'c.parentId', '=', 'b.id')->
			 join('device as e', 'a.licence_plate', '=', 'e.vehicle_licence_plate')->where($data_arr)->
			get();
					 			
	}
	//called from vendorDashboardController and it collect the information of all the previous users list who add the logged in user as a internal vendor
	public function Search_Claim_account($Contact_email,$Contact_phone){
		
			$data_arr = array('b.contactEmail'=>$Contact_email,'a.addedType'=>"Internal",'b.ext_link'=>NULL);
			$data_arr1 = array('b.contactPhone'=>$Contact_phone,'a.addedType'=>"Internal",'b.ext_link'=>NULL);
		
			return DB::table('vendor as b')->select('b.addedBy','b.companyName','b.contactName','b.ext_link','b.contactEmail','b.contactPhone','c.name','a.id as referanceId')->leftJoin('users as a', 'a.id', '=', 'b.parentId')->leftJoin('users as c', 'c.id', '=', 'b.addedBy')
			->where($data_arr)->orWhere($data_arr1)->get();
			
			
					 			
	}
	
	public function get_all_parts_and_accessories_detail_vendor($addedBy)
	{
		if(!empty($addedBy)){
			$data_arr = array('user_id'=>$addedBy);
		}
		 
	 return  DB::table('vendor_parts_accessories')->select('*')->where($data_arr)->get();
	 
		
	}
	
	public function get_part_detail_by_id_for_vendor($id)
	{
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		 
	 return  DB::table('vendor_parts_accessories')->where($data_arr)->first();
	 
		
	}
	
	
	
	public function get_all_pending_payment_entry_list($addedBy)
	{
		if(!empty($addedBy)){
			$data_arr = array('addedBy'=>$addedBy,'paid'=>0);
			$data_arr1 = array('addedBy'=>$addedBy,'paid'=>2);
		}
		 
	 return  DB::table('payment')->select('*')->where($data_arr)->orWhere($data_arr1)->get();
		
	}
	
	public function get_payment_by_id($id=NULL)
	{
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('id'=>$id);
		}
		//print_r($data_arr);die;
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('payment')->where($data_arr)->first();
	
	}
	
	public function get_payment_by_ids($id=NULL){
		$data_arr='';
		if(!empty($id)){
			$data_arr = array('document_parentId'=>$id,'document_parentType'=>'payment');
		}
		//print_r($data_arr);die;
		 //return  DB::table('users as a')->select('a.*','b.typeName','c.addedBy as NotificationFrom')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->leftJoin('invite_user as c', 'c.userId', '=', 'a.id')->where($data_arr)->first();
	 return  DB::table('partial_paid_document')->where($data_arr)->get();
	 
	 
	}
	
	public function get_all_external_users($type=NULL){
		
		
		
		if(!empty($type)){
			$data_arr = array('a.addedType'=>$type,'a.is_deleted'=>0,'a.userType'=>1,'a.status'=>1);
		}else{
			$data_arr= array('a.is_deleted'=>0,'a.userType'=>1,'a.status'=>1);
		}
		 return  DB::table('users as a')->select('a.name')->leftJoin('usertype as b', 'b.id', '=', 'a.userType')->where($data_arr)->get();
		
			
	}
	public function get_all_vehicle_of_system(){
		
		 return  DB::table('vehicle')->get();
	
	}
	
	public function parts_and_accessories_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.*,b.make,b.model,c.name FROM  `parts_and_accessories` as a LEFT JOIN vehicle as b ON b.licence_plate = a.vehicle_licence_plate LEFT JOIN bcb_brands as c ON c.id = b.make WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}

	public function update_accessories($id_arr,$data_arr){
		//echo "yes";die;
		$userId = Auth::user()->id;
		return DB::table("parts_and_accessories")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function insurance_one_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		$where .= "AND a.insurance_type ='one'";
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `insurance` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	public function insurance_two_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		$where .= "AND a.insurance_type ='two'";
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `insurance` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	
	
	public function road_tax_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `road_tax` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	public function automobile_tax_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `automobile_tax` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	
	public function Inspection_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `inspection` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	
	
	public function Fine_list($request,$limit=NUll,$offset=NULL)
	{
		
		$where = "a.is_deleted ='0'";
		
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `fines` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	public function Leasing_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		
		//pending from here
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `leasing` as a  WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		//echo $sqlQuery;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}
	
	public function get_all_payment_entry_list($request,$limit=NUll,$offset=NULL)
	{

		$where = "a.is_deleted ='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= "AND a.addedBy ='$addedBy'";
		$sqlQuery .= "SELECT a.*,b.name FROM  payment as a LEFT JOIN users as b ON b.id = vendor OR b.id = user WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.period_to) >= '".$request['from']."' "; 
		}
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.period_to) <='".$request['to']."' "; 
		}
		if( !empty($request['tag']) ) {
			
			$sqlQuery.=" AND (a.paid ='0' OR a.paid ='2') "; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;	
	}
	public function update_payment($id_arr,$data_arr){
		
		$userId = Auth::user()->id;
		return DB::table("payment")->where(array("addedBy"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function Internal_users(){
		
		$userId = Auth::user()->id;
		$allDrivers =  $this->get_all_driver($userId);

		$allusersInternal = $this->get_all_users(array('a.addedType'=>'Internal','a.addedBy'=>$userId,'c.driver'=>4,'a.userType'=>1));
              
		$combine = array_merge($allusersInternal,$allDrivers);
		$result['result'] =$combine;
		$result['drivers'] = $allDrivers;
		$result['users'] = $allusersInternal;
		return $result;
	}
	
	public function connected_users(){
		
		$userId = Auth::user()->id;
		$Connected_users_sent = $this->Contact_Connections_search('1',$userId,"sent");
		$Connected_users_receive = $this->Contact_Connections_search('1',$userId,"receive");
		$combine = array_merge($Connected_users_sent,$Connected_users_receive);
                $RecievedRequestUsers = $this->get_all_recieved_pending_request('1',$userId);
                $SendRequestUsers = $this->get_all_send_pending_request('1',$userId);
		$result['result'] =$combine;
                
		$result['received'] = $RecievedRequestUsers;
		$result['sent'] = $SendRequestUsers ;
                $total_combine = array_merge($combine,$RecievedRequestUsers,$SendRequestUsers);
                $result['total_combine'] =$total_combine;
		return $result;
	}
	
	public function getExternalOrConnectedVandors($Addedtype=NUll,$addedBy,$userType=NULL){
		$userId = Auth::user()->id;
		$LogInId[] = Auth::user()->id;
		$whereOr =array('a.addedBy'=>$addedBy);
		$data_arr= array('a.is_deleted'=>0,'a.userType'=>2);
		if(!empty($Addedtype)){
			$whereOr['a.addedType'] = "'".$Addedtype."'";
		}
		if(!empty($userType)){
			$whereOr['a.userType'] = $userType;
		}
		$result['totalVendor'] =  DB::table('users as a')
		->select('a.*','b.typeName','c.vendor_id')
		->leftJoin('usertype as b', 'b.id', '=', 'a.userType')
		->leftJoin('vendor as c', 'c.parentId', '=', 'a.id')
		->where($data_arr)
		->whereNotIn('a.id',$LogInId)
		->orWhere($whereOr)
		->get();
                $allVendorsInternal = $this->get_all_vandors('Internal',$userId);

		$Connected_Vendors_sent = $this->Contact_Connections_search('2',$addedBy,"sent");
		$Connected_Vendors_receive = $this->Contact_Connections_search('2',$addedBy,"receive");
		$combine = array_merge($Connected_Vendors_sent,$Connected_Vendors_receive);
                
                $RecievedRequestVendors = $this->get_all_recieved_pending_request('2',$userId);
                $SendRequestVendors = $this->get_all_send_pending_request('2',$userId);
		$result['ConnectResult'] =$combine;
                $connectedPlusInternal = array_merge($combine,$allVendorsInternal);
		$result['received'] = $RecievedRequestVendors ;
		$result['sent'] = $SendRequestVendors;
                $result['allVendorsInternal'] = $allVendorsInternal;
		$result['connectedPlusInternal'] = $connectedPlusInternal;
                $finalTotal= array_merge($connectedPlusInternal,$RecievedRequestVendors,$SendRequestVendors);
                $result['finalTotal']=$finalTotal;
                //echo "<pre>"; print_r($allVendorsInternal);die;
		return $result;
	}
	
	public function pending_task($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.status ='0' AND a.status !='1'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		
		$addedBy =  Auth::user()->id;
		$where .= " AND (a.user_id ='$addedBy' OR a.assign_user ='$addedBy')";
		$sqlQuery .= "SELECT a.*,b.name as addeByName , c.name as AssignName FROM  task as a LEFT JOIN users as b ON b.id=a.user_id LEFT JOIN users as c ON c.id=a.assign_user WHERE ".$where." ";
		
		if( !empty($request['tag']) ) {
			
			$sqlQuery.=" AND date(a.due_date) ='0000-00-00 00:00:00'"; 
		}else{
			
			$sqlQuery.=" AND date(a.due_date) !='0000-00-00 00:00:00'"; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;	
	}
	
	public function isEmailExist($email){
	
		 return  DB::table('users as a')
		 ->select('a.email')
		 ->where(array('is_deleted'=>0,'a.email'=>$email))
		 ->first();
	
	}
	
	public function isDriverFree($driver_id){
		
		DB::table('user_additional')
        ->where('parentId', $driver_id) 
        ->update(array('vehicle_assignment'=>'NULL'));
		
		 return DB::table('vehicle')
        ->where('driver_id', $driver_id) 
        ->update(array('driver_id'=>'0'));
		 
		return  $driver_id;
	}

	public function isValueExists($table,$where_arr,$whereNot=NULL){
	
		$query = DB::table($table)
		->select('*')
		->where($where_arr);
		if(!empty($whereNot) && count($whereNot)==2){
			$query->where($whereNot[0],'!=',$whereNot[1]);
		}
		return $query->first();
		
	}
	
	
	
}


 



