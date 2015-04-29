<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {
	//-----------Branch Details-----start-----allyn
	public function details(){
        $this->load->model('dine/setup_model');
        $this->load->helper('dine/setup_helper');
        $details = $this->setup_model->get_details(1);
		$det = $details[0];
        $data = $this->syter->spawn('setup');
        $data['page_subtitle'] = 'Edit Branch Setup';
        $data['code'] = makeDetailsForm($det);
		$data['load_js'] = 'dine/setup.php';
		$data['use_js'] = 'detailsJs';
        $this->load->view('page',$data);
    }
    public function details_db(){
        $this->load->model('dine/setup_model');
        $items = array(
            "branch_code"=>$this->input->post('branch_code'),
            "branch_name"=>$this->input->post('branch_name'),
            "branch_desc"=>$this->input->post('branch_desc'),
            "contact_no"=>$this->input->post('contact_no'),
            "delivery_no"=>$this->input->post('delivery_no'),
            "address"=>$this->input->post('address'),
            "tin"=>$this->input->post('tin'),
            "machine_no"=>$this->input->post('machine_no'),
            "bir"=>$this->input->post('bir'),
            "permit_no"=>$this->input->post('permit_no'),
            "serial"=>$this->input->post('serial'),
            "email"=>$this->input->post('email'),
            "website"=>$this->input->post('website')
            // "currency"=>$this->input->post('currency')
        );

            $this->setup_model->update_details($items, 1);
            // $id = $this->input->post('cat_id');
            $act = 'update';
            $msg = 'Updated Branch Details';
        
        echo json_encode(array('msg'=>$msg));
    }
	//-----------Branch Details-----end-----allyn
}