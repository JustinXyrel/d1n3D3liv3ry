<?php
class Trans_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	public function get_trans_types($id=null,$ref=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('trans_types');
			if($id != null){
				$this->db->where('trans_types.type_id',$id);
			}
			if($ref != null){
				$this->db->where('trans_types.reference',$ref);
			}
			$this->db->order_by('type_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_next_ref($type=null){
		$this->db->trans_start();
			$this->db->select('next_ref');
			$this->db->from('trans_types');
			if($type != null){
				$this->db->where('trans_types.type_id',$type);
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result[0]->next_ref;
	}
	function ref_unused($trans_type,$ref){
		$this->db->from('trans_refs');
		$this->db->where('type_id',$trans_type);
		$this->db->where('trans_ref',$ref);
		$query=$this->db->get();
		return ($query->num_rows()>0)?false:true;		
	}
	public function save_ref($type=null,$ref=null){
		if($this->ref_unused($type,$ref)){
			$user = $this->session->userdata('user');
			$refs=$this->write_ref($type,$ref,$user['id']);
			$this->update_next_ref($type,$refs['ref']);
		}
	}
	public function write_ref($trans_type,$ref=null,$user_id=null){
		$this->db->trans_start();			
			if($ref==null)
				$ref=$this->get_next_ref($trans_type);
			$items = array(
				'type_id'=>$trans_type,
				'trans_ref'=>$ref,
				'user_id'=>$user_id
			);
			$this->db->insert('trans_refs',$items);
		$this->db->trans_complete();
		return array('ref'=>$ref);		
	}
	public function update_next_ref($trans_type,$ref){
        if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1) 
        {
			list($all, $prefix, $number, $postfix) = $result;
			$dig_count = strlen($number); // How many digits? eg. 0003 = 4
			$fmt = '%0' . $dig_count . 'd'; // Make a format string - leading zeroes
			$nextval =  sprintf($fmt, intval($number + 1)); // Add one on, and put prefix back on

			$new_ref=$prefix.$nextval.$postfix;
        }
        else 
            $new_ref=$ref;		
		$this->db->update('trans_types',array('next_ref'=>$new_ref),array('type_id'=>$trans_type));
	}
	
	 public function finish_trans($sales_id=null,$move=false,$void=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/items_model');
        $this->load->model('core/trans_model');
        $loc_id = 2;
        $trans_type = SALES_TRANS;
        if($void)
            $trans_type = SALES_VOID_TRANS;
        $ref = $this->trans_model->get_next_ref($trans_type);
        $this->trans_model->db->trans_start();
            $this->trans_model->save_ref($trans_type,$ref);
            $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1),$sales_id);
        $this->trans_model->db->trans_complete();
    }
}
?>