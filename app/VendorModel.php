<?php
namespace App;
use Illuminate\Support\Facades\Auth;
use Eloquent;
use DB;
use Carbon\Carbon;

class VendorModel extends Eloquent {
	
	public function get_all_service_entry_list($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}	
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		if( !empty($request['paymentStatus']) ) {
			
			$where .= "AND a.paid_status ='".$request['paymentStatus']."'";
		}
		if( !empty($request['servicePerformed']) ) {
 
			$where .= "AND a.services_performed IN (".(implode(',',$request['servicePerformed'])).")";
			
		}
		$sqlQuery .= "SELECT a.*,b.licence_plate,b.make,b.model,b.make,b.status,b.file as Vehicle_Image,b.type,c.name FROM  `service_entry` as a LEFT JOIN vehicle as b ON b.licence_plate = a.vehicle_licence_plate LEFT JOIN bcb_brands as c ON c.id = b.make WHERE ".$where." ";
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}
		if( !empty($request['vendorName']) ) {
			
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		if( !empty($request['from']) ) {			$sqlQuery.=" AND (a.date) >= '".$request['from']."' "; 		}		if( !empty($request['to']) ) {						$sqlQuery.=" AND date(a.date) <='".$request['to']."' "; 		}
		
		/*if( !empty($request['search']['value']) ) {  
			$sqlQuery.=" AND ( a.vehicle_licence_plate LIKE '".$request['search']['value']."%' ";    
			$sqlQuery.=" OR a.vendor LIKE '".$request['search']['value']."%' ";
			$sqlQuery.=" OR a.odometer LIKE '".$request['search']['value']."%' )";
		}*/
		//echo $sqlQuery;die;
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		return $result;
	}
	
	public function vendor_info($vendorId=NULL){
		
		$userId = !empty($vendorId) ? $vendorId :Auth::user()->id;
		return DB::table('users as a')->select('a.*','b.*')->leftJoin('vendor as b', 'a.id', '=', 'b.parentId')->where(array('id'=>$userId))->first();
		
	}
	
	public function update_service($id_arr,$data_arr){
		
		$userId = Auth::user()->id;
		return DB::table("service_entry")->where(array("user_id"=>$userId))->whereIn("service_id", $id_arr)->update($data_arr);
		
	}
	public function update_accessories($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("vendor_parts_accessories")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
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
		$sqlQuery .= "SELECT a.* FROM  vendor_parts_accessories as a WHERE ".$where." ";
		if( !empty($request['from']) ) {
			$sqlQuery.=" AND (a.created) >= '".$request['from']."' "; 
		}
		if( !empty($request['to']) ) {
			
			$sqlQuery.=" AND date(a.created) <='".$request['to']."' "; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		
		return $result;
	}

		public function get_vendor_service($type=NULL){
		
		if(!empty($type)):
			$vendorType=$type;
		else:
			$additional = $this->vendor_info();	
			$vendorType=$additional->type;
		endif;		
		
		return DB::table('vendor_services as a')->select('a.*')->where(array('vendor_type'=>$vendorType,'is_deleted'=>'0'))->get();
	
	}
	
	public function update_payment($id_arr,$data_arr){
		
		$userId = Auth::user()->id;
		return DB::table("payment")->where(array("addedBy"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
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
		$sqlQuery .= "SELECT a.* FROM  payment as a WHERE ".$where." ";
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
	
		public function insuranceList($request,$limit=NUll,$offset=NULL)
		{
			$where = "a.is_deleted ='0'";
			$where .= "AND a.insurance_type ='0'";
			$sqlQuery =$limitQ = '';
			$result=array();
			
			if(!empty($limit)){
				$limitQ = " LIMIT ".$offset.','.$limit;
			}
			
			$addedBy =  Auth::user()->id;
			$where .= "AND a.user_id ='$addedBy'";
			$sqlQuery .= "SELECT a.* FROM  `insurance` as a  WHERE ".$where." ";
		
			if( !empty($request['vehicleName']) ) {
				$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
			}
			
			if( !empty($request['vendorName']) ) 
			{
				$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
			}
			//echo $sqlQuery;die;
			$result['num'] =  count(DB::select($sqlQuery));
			$sqlQuery .=$limitQ;
			$result['result'] =  DB::select($sqlQuery);
			return $result;
		}
	
	public function update_insurence($id_arr,$data_arr){
		
		$userId = Auth::user()->id;
		return DB::table("insurance")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function get_insurance_include(){
			$userId = Auth::user()->id;
			return DB::table('insurance_include as a')->select('a.*')->get();
		}
		public function geClaimList($request,$limit=NUll,$offset=NULL)
		{
			$where = "a.is_deleted ='0'";
			$sqlQuery =$limitQ = '';
			$result=array();
			if(!empty($limit)){
				$limitQ = " LIMIT ".$offset.','.$limit;
			}
		
			$addedBy =  Auth::user()->id;
			$where .= "AND a.user_id ='$addedBy'";
			$sqlQuery .= "SELECT a.* FROM  insurance_claim as a WHERE ".$where." ";
			$result['num'] =  count(DB::select($sqlQuery));
			$sqlQuery .=$limitQ;
			$result['result'] =  DB::select($sqlQuery);
			
			return $result;
		}
		
		
		public function update_claim($id_arr,$data_arr){
		
		$userId = Auth::user()->id;
		return DB::table("insurance_claim")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
	}
	
	public function vandorInspectionlist($request,$limit=NUll,$offset=NULL)
	{
		$where = "a.is_deleted ='0'";
		$sqlQuery =$limitQ = '';
		$result=array();
		if(!empty($limit)){
			$limitQ = " LIMIT ".$offset.','.$limit;
		}
		$addedBy =  Auth::user()->id;
		$where .= "AND a.user_id ='$addedBy'";
		$sqlQuery .= "SELECT a.* FROM  `inspection` as a  WHERE ".$where." ";
		if( !empty($request['vehicleName']) ) {
			$sqlQuery.=" AND a.vehicle_licence_plate LIKE '".$request['vehicleName']."%' "; 
		}

		if( !empty($request['vendorName']) ) 
		{
			$sqlQuery.=" AND a.vendor LIKE '".$request['vendorName']."%' "; 
		}
		$result['num'] =  count(DB::select($sqlQuery));
		$sqlQuery .=$limitQ;
		$result['result'] =  DB::select($sqlQuery);
		return $result;
	}
	
	public function update_inspection($id_arr,$data_arr){
		$userId = Auth::user()->id;
		return DB::table("inspection")->where(array("user_id"=>$userId))->whereIn("id", $id_arr)->update($data_arr);
		
	}		
	public function graph_cost_value(){	
	
		$userId = Auth::user()->id;	
		return DB::table("service_entry")	
		->select(DB::raw("(sum(total_cost)) as total_cost"))   
		->orderBy('date')	
		->where(array("user_id"=>$userId))	
		->where("date",">", Carbon::now()->subMonths(6))    
		->groupBy(DB::raw("MONTH(date)"))    
		->get();	
	}
}