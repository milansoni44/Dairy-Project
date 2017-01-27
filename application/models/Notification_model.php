<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Notification_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function get_all_notification()
	{
		$type = $this->session->userdata("group");
        $id = $this->session->userdata("id");
		
		$query = '';
		if( $type === "dairy" )
		{
			$query = " SELECT * 
						FROM `notification` 
						WHERE `for_whom`=1 AND `dairy_id`=".$id." ORDER BY `created_at` DESC";
		}
		else if( $type === "society" )
		{
			//print "ihi..";exit;
			$query = "SELECT * FROM (
						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `society_id`=".$id." 

						UNION

						SELECT * 
						FROM `notification` 
						WHERE 
						`for_whom`=2 
						AND `dairy_id`=( SELECT `u`.`dairy_id` FROM `users` `u` WHERE `u`.`id`=".$id." AND `notification`.`for_whom`=2 )
					) AS `tmp`
					ORDER BY `tmp`.`created_at` DESC";
		}
		
		if( $query != '' )
		{
			$result = $this->db->query( $query );
			return $result->result_array();
		}
	}
}
?>