<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	var $data = null;
	public function index(){
		$this->load->model('core/user_model');
		$this->load->helper('site/site_forms_helper');
		$user_list = $this->user_model->get_users();
        $data = $this->syter->spawn('user');
        $data['code'] = site_list_form("user/users_form","users_form","Users",$user_list,array('fname','mname','lname','suffix'),"id");
        $data['add_js'] = 'js/site_list_forms.js';
 		$this->load->view('page',$data);
	}
	public function users_form($ref=null){
        $this->load->helper('core/user_helper');
        $this->load->model('core/user_model');
        $user = array();
        if($ref != null){
            $users = $this->user_model->get_users($ref);
            $user = $users[0];
        }
        // echo var_dump($user);
        $this->data['code'] = makeUserForm($user);
        $this->load->view('load',$this->data);
    }
    public function users_db(){
        $this->load->model('core/user_model');
        $items = array();

        if($this->input->post('id')){
            $items = array(
                "fname"=>$this->input->post('fname'),
                "mname"=>$this->input->post('mname'),
                "lname"=>$this->input->post('lname'),
                "role"=>$this->input->post('role'),
                "suffix"=>$this->input->post('suffix'),
                "gender"=>$this->input->post('gender'),
                "email"=>$this->input->post('email'),
                "pin"=>$this->input->post('pin'),
                "inactive"=>$this->input->post('inactive'),
            );

            $this->user_model->update_users($items,$this->input->post('id'));
            $id = $this->input->post('id');
            $act = 'update';
            $msg = 'Updated User '.$this->input->post('fname').' '.$this->input->post('lname');
        }
        else{
            $items = array(
                "username"=>$this->input->post('uname'),
                "password"=>md5($this->input->post('password')),
                "fname"=>$this->input->post('fname'),
                "mname"=>$this->input->post('mname'),
                "lname"=>$this->input->post('lname'),
                "role"=>$this->input->post('role'),
                "suffix"=>$this->input->post('suffix'),
                "gender"=>$this->input->post('gender'),
                "email"=>$this->input->post('email'),
				"pin"=>$this->input->post('pin'),
            );

            $id = $this->user_model->add_users($items);
            $act = 'add';
            $msg = 'Added  new User '.$this->input->post('fname').' '.$this->input->post('lname');
        }
        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('fname').' '.$this->input->post('lname'),"act"=>$act,'msg'=>$msg));
    }
}