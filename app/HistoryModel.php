<?php
namespace App;
use Illuminate\Support\Facades\Auth;
use Eloquent;
use DB;

class HistoryModel extends Eloquent {
	
	
	public function userLog($tag=NULL,$change_arr=array()){
			$message ='';
			$user = Auth::user();
			
			if($user->userType =='1'):
			
				$logFile = public_path()."/userLog/user$user->id.txt";
					if($tag =='Logout' || $tag =='Login' ):
						$message = "$user->id $tag $user->name ".date('Y-m-d H:i:s');
					else:
						
						foreach($change_arr as $key=>$value):
							$message .= "$user->id $tag $user->name ".date('Y-m-d H:i:s')." {".$key.":".$value."}\n";
						endforeach;
					endif;
					if(file_exists($logFile)):
						$fh = fopen($logFile, 'a');
						fwrite($fh, $message."\n");
						//print_R($message);die;
					 else:
						$fh = fopen($logFile, 'w');
						fwrite($fh, $message."\n");
					endif;
				
			endif;
		
	}
	
	public function getHistoryKeywords($keyword){
		
		 return  DB::table('Keywords')->where(array('is_deleetd'=>0,'keyword'=>$keyword))->get();
		 
	}
	
	public function date_text_formate($date){
		
		return date_format(date_create($date),'j F | l' );
	}
	
	public function date_time_formate($date){
		
		return date_format(date_create($date),'G:i a'); 
	}
	
	public function addToTask($data_arr=NULL,$action_arr=NULL)
	{
		$lastInsertId = '';
		if(!empty($action_arr)){
			DB::table('task')
			->where($action_arr)
			->delete();
		}
		else{
			$lastInsertId = DB::table('task')->insertGetId($data_arr);
		}
		return $lastInsertId;
	}
	
}