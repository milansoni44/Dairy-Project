<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Favourite_report_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function FavouriteReportAddUpdate( $data, $favourite_report_id=0 )
	{
		$id = $this->session->userdata("id");
		$insert_into = "INSERT INTO ";
		$update = "UPDATE ";
		$field_set = " `favourite_report` SET 
						`report_name` = '".htmlentities($data['report_name'], ENT_QUOTES)."',
					   `period` = '".$data['period']."',
					   `shift` = '".$data['shift']."',
					
					   `user_id` = '". $id ."'";
		$where_clouse = " WHERE `id`=".$favourite_report_id;
		
		$insert_qry = $insert_into.$field_set;
		$update_qry = $update.$field_set.$where_clouse;
		$result = $this->db->query( $favourite_report_id==0 ? $insert_qry : $update_qry );
		return $result ? TRUE : FALSE;
	}
	
	public function get_favourite_report($favourite_report_id=0)
	{
		$id = $this->session->userdata("id");
		$select_qry = " SELECT *, 
							(CASE `period` 
							 WHEN 1 THEN 'Weekly' 
							 WHEN 2 THEN 'Monthly' 
							 WHEN 3 THEN 'Yearly' 
							 END) AS `period_word`,
							
							(CASE `shift` 
							 WHEN 'E' THEN 'Evening' 
							 WHEN 'M' THEN 'Morning' 
							 END) AS `shift_word`
						FROM `favourite_report` ";
		$where_all = " WHERE `user_id`=".$id;
		$where_fetch_one = " WHERE `id`=".$favourite_report_id;
		
		if ( $favourite_report_id )
		{
			$select_qry .= $where_fetch_one;
		}
		else
		{
			$select_qry .= $where_all;
		}
		
		$result = $this->db->query( $select_qry );
		if( $result )
		{
			return $favourite_report_id ? $result->row_array() : $result->result_array();
		}
		return FALSE;
	}
	
	public function delete_favourite_report($favourite_report_id)
	{
		$result = $this->db->query(" DELETE FROM `favourite_report` WHERE `id`=".$favourite_report_id);
		return $result ? TRUE : FALSE;
	}
}
?>