<?php
namespace App;
use Illuminate\Support\Facades\Auth;
use Eloquent;
use DB;
use Carbon\Carbon;

class Notification extends Eloquent {
	
	
	public function check_follow_status($followBy,$followTo)
	{
		$data_arr = array('a.follow_by'=>$followBy,'a.follow_to'=>$followTo);
		return  DB::table('follows as a')
		->select('a.*','b.name as followByName','c.name as followToName')
		->leftJoin('users as b', 'b.id', '=', 'a.follow_by')
		->leftJoin('users as c', 'c.id', '=', 'a.follow_to')
		->where($data_arr)
		->first();
	}
	

	public function deleted_records($tableName,$data_arr)
	{
		DB::table($tableName)
		->where($data_arr)
		->delete();
	}
	
	public function get_commented_user($data_arr){
		
		return DB::table('users as a')
		->select('a.*','a.photo','b.phone','b.country','b.birthday','b.occupation')
		->leftJoin('user_additional as b', 'a.id', '=', 'b.parentId')
		->where($data_arr)
		->first();
	}
	
	public function get_user_post($data_arr){
		
		return DB::table('posts as a')
		->select('a.*','b.name as ASSIGN_USER_NAME','b.photo as ASSIGN_USER_PHOTO')
		->leftJoin('users as b', 'b.id', '=', 'a.user_id')
		->where($data_arr)
		->orderBy('a.post_id', 'DESC')
		->get();
	}
	
	public function get_post_comments($data_arr){
		
		return DB::table('comments as a')
		->select('a.*','b.name as ASSIGN_USER_NAME','b.photo as ASSIGN_USER_PHOTO')
		->leftJoin('users as b', 'b.id', '=', 'a.user_id')
		->where($data_arr)
		->orderBy('a.comment_id', 'DESC')
		->get();
	}
	
	public function get_post_data($data_arr){
		
		return DB::table('posts as a')
		->select('a.*','b.name as ASSIGN_USER_NAME','b.photo as ASSIGN_USER_PHOTO')
		->leftJoin('users as b', 'b.id', '=', 'a.user_id')
		->where($data_arr)
		->orderBy('a.post_id', 'DESC')
		->first();
	}
	
	public function get_user_post_details($data_arr){
		
		$userId = Auth::user()->id;
		
		return DB::table('posts as a')
		->select('a.*','b.name as ASSIGN_USER_NAME','c.parent_postId as shareByMe','d.name as parentUser')
		->leftJoin('users as b', 'b.id', '=', 'a.user_id')
		
		->leftJoin('posts as c', function($q) use ($userId)
		{
			$q->on('c.parent_postId', '=', 'a.post_id')
			->where('c.user_id', '=', "$userId");
		})
		->leftJoin('users as d', 'd.id', '=', 'c.parent_userId')
		->where($data_arr)
		->orderBy('a.post_id', 'DESC')
		->take(3)
		->get();
		
		}
		
		
		public function sharableDocList($request,$limit=NUll,$offset=NULL)
		{
			
			$result=array();
			$addedBy =  Auth::user()->id;
			
			$query6=  DB::table('inspection as f')
			->select(DB::raw('(CASE WHEN f.id != 0 THEN "Inspection" ELSE "NOT" END) AS docType'),DB::raw('f.id as docId,f.created as created_At,f.vehicle_licence_plate as Ref'))
			->leftJoin('vehicle as vh', 'vh.licence_plate', '=', 'f.vehicle_licence_plate')
			->where(array('f.is_deleted'=>0,'f.user_id'=>$addedBy));
		
			
			$query5=  DB::table('service_entry as e')
			->select(DB::raw('(CASE WHEN e.service_id != 0 THEN "Service" ELSE "NOT" END) AS docType'),DB::raw('e.service_id as docId,e.created as created_At,e.vehicle_licence_plate as Ref'))
			->join('vehicle as vh', 'vh.licence_plate', '=', 'e.vehicle_licence_plate')
			->where(array('e.is_deleted'=>0,'e.user_id'=>$addedBy));
		
			
			$query4=  DB::table('vendor_parts_accessories as d')
			->select(DB::raw('(CASE WHEN d.id != 0 THEN "product and parts" ELSE "NOT" END) AS docType'),DB::raw('d.id as docId,d.created as created_At,d.product_code as Ref'))
			->where(array('d.is_deleted'=>0,'d.user_id'=>$addedBy));
			
			$query3=  DB::table('payment as c')
			->select(DB::raw('(CASE WHEN c.id != 0 THEN "payment" ELSE "NOT" END) AS docType'),DB::raw('c.id as docId,c.created as created_At,c.invoice_num as Ref'))
			->where(array('c.is_deleted'=>0,'c.addedBy'=>$addedBy));
			
		
			$query2=  DB::table('insurance as b')
			->select(DB::raw('(CASE WHEN b.id != 0 THEN "Insurence" ELSE "NOT" END) AS docType'),DB::raw('b.id as docId,b.created as created_At,b.vehicle_licence_plate as Ref'))
			->where(array('b.is_deleted'=>0,'b.user_id'=>$addedBy));

			$query=  DB::table('insurance_claim as a')
			->select(DB::raw('(CASE WHEN a.id != 0 THEN "Insurence Claim" ELSE "NOT" END) AS docType'),DB::raw('a.id as docId,a.created as created_At,a.vehicle_licence_plate as Ref'))
			->where(array('a.is_deleted'=>0,'a.user_id'=>$addedBy))
			->unionAll($query2)
			->unionAll($query3)
			->unionAll($query4)
			->unionAll($query5)
			->unionAll($query6);

			$result['num'] =  count($query->get());
			if(!empty($limit)){
				$query->take($limit)->offset($offset);
			}
				$result['result'] =  $query->get();
				return $result;
		}
		
		
		
	public function date_text_formate($date){

		if($date != '0000-00-00 00:00:00'){
			return date_format(date_create($date),'j F Y | l G:i A');
		}
		
	}
	
	public function getDicType($value=NULL,$keys=NULL) {
	
		$array = array('1'=>'INSPECTION','2'=>'INSURENCE','3'=>'PAYMENT','4'=>'INSURENCE CLAIM','5'=>'PRODUCT AND PARTS','6'=>'SERVICE','7'=>'TASK','8'=>'INVITE');
		foreach ($array as $key => $val) {
			if(!empty($keys)){
			
				if($key == $keys) {
					return ucfirst(strtolower($val));
				}
			}	
			else{
				if($val == $value) {
					return strtolower($key);
				}
			}
			
		}
		return null;
	}
	
	
	
		
	public function get_insurence_data($docId){
		
		return DB::table('insurance as a')
		->select('a.*')
		->where(array('a.id'=>$docId))
		->first();
	}	
	
	public function get_payment_data($docId){
		
		return DB::table('payment as a')
		->select('a.*')
		->where(array('a.id'=>$docId))
		->first();
	}
	
	public function get_insurance_claim_data($docId){
		
		return DB::table('insurance_claim as a')
		->select('a.*')
		->where(array('a.id'=>$docId))
		->first();
	}
	
	public function get_inspection_data($docId){
		
		return DB::table('inspection as a')
		->select('a.*')
		->where(array('a.id'=>$docId))
		->first();
	}
	
	public function get_vendor_parts_accessories($docId){
		
		return DB::table('vendor_parts_accessories as a')
		->select('a.*')
		->where(array('a.id'=>$docId))
		->first();
	}
	
	public function get_service_entry_detail($docId){
		
		return DB::table('service_entry_detail as a')
		->select('a.*')
		->where(array('a.service_parentid'=>$docId))
		->get();
	}
	
	public function get_reminder($docId){
		
		return DB::table('reminder as a')
		->select('a.*')
		->where(array('a.service_parentId'=>$docId))
		->get();
	}
	
	
	public function get_service_entry($docId){
		
		return DB::table('service_entry as a')
		->select('a.*')
		->where(array('a.service_id'=>$docId))
		->first();
	}
	
	
	public function partial_paid_document($docId,$docType){
		
		return DB::table('partial_paid_document as a')
		->select('a.*')
		->where(array('a.document_parentId'=>$docId,'document_parentType'=>$docType))
		->get();
	}
	
	public function update_notification($data_arr,$where)
	{
		return DB::table('notification as a')
		->Join('notification_details as b', 'b.notifyId', '=', 'a.id')
		->where($where)
		->update($data_arr);
	}
	
	public function feedBack_notification($NotifyId,$data)	{
		
		$Notify_arr = $this->getNotification($NotifyId);
		//echo '<pre>';  print_R($Notify_arr[0]->notifyBy); die;
		$userId = Auth::user();
		$notifications_arr=array(
					'notifyBy'	=> $userId->id,
					'notifyTo'=>$Notify_arr[0]->notifyBy,
					'created'=> date('Y-m-d H:i:s'),
					'status'=> $data['NotiStatus'],
					);
				$lastInsertId = DB::table('notification')->insertGetId($notifications_arr);
				$details_arr=array(
					'notifyId'=> $lastInsertId,
					'toID'=>$Notify_arr[0]->notifyBy,
					'byId'=>$userId->id,
					'docId'	=>$Notify_arr[0]->docId,
					'docType'=>$Notify_arr[0]->docType,
					'created'=> date('Y-m-d H:i:s'),
					'status'=>$data['NotiDetailStatus']
					);
				$lastId = DB::table('notification_details')->insertGetId($details_arr);
				return $lastId;
	}
	
	public function add_notification($Notify_arr)
	{
		$userId = Auth::user()->id;
			$notifications_arr=array(
				'notifyBy'	=>$userId,
				'notifyTo'=>$Notify_arr['toID'],
				'created'=> date('Y-m-d H:i:s'),
				'status'=>$Notify_arr['status'],
			);
		$lastInsertId = DB::table('notification')->insertGetId($notifications_arr);
			$details_arr=array(
				'notifyId'=> $lastInsertId,
				'toID'=>$Notify_arr['toID'],
				'byId'=>$userId,
				'docId'	=>$Notify_arr['docId'],
				'docType'=>$Notify_arr['docType'],
				'created'=> date('Y-m-d H:i:s'),
				'status'=>0
			);
		$lastId = DB::table('notification_details')->insertGetId($details_arr);
	}
	
	
	public function update_records($table,$data_arr,$where)
	{
		return DB::table($table)
		->where($where)
		->update($data_arr);
	}
	
	public function getNotification($NotifyId=NULL){
		
		$userId = Auth::user()->id;
		$query  = DB::table('notification as a')
		->select('a.*','b.*','a.status as NotiStatus','b.status as NotiDetailStatus','c.name as fromName','d.name as toName','c.photo as fromImg','d.photo as ToImg')
		->Join('notification_details as b', 'b.notifyId', '=', 'a.id')
		->leftJoin('users as c', 'c.id', '=', 'a.notifyBy')
		->leftJoin('users as d', 'd.id', '=', 'a.notifyTo')
		->where(array('a.notifyTo'=>$userId));
		if(!empty($NotifyId)){
				$query->where('a.id',$NotifyId);
		}
		$query->whereIn('a.status',array('0','3','2','4'));
		return $query->get();
	}
	
	public function check_invite_user($Email){
		
		return DB::table('invite_user as a')
		->select('a.*')
		->where(array('a.email'=>$Email))
		->first();
	}
	
	
	public function isEmailExist($email){
	
		 return  DB::table('users as a')
		 ->select('a.*')
		 ->where(array('is_deleted'=>0,'a.email'=>$email))
		 ->first();
	
	}
	
	public function isConnectedMe($where_arr=NULL,$orWhere_arr=NULL){
		
		$query = DB::table('invite_user')
		->select('invite_user.*');
		
		if(!empty($where_arr)){
			$query->where($where_arr);
		}
		
		if(!empty($orWhere_arr)){
			$query->orWhere($orWhere_arr);
		}
		
		return $query->first();
		
	}
	
}






