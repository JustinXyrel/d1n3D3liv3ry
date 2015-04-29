<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trans_order extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$this->load->model('site/site_model');
		$this->load->model('dine/transorder_model');
		$this->load->helper('dine/transorder_helper');
	}
	public function index(){
		$data = $this->syter->spawn('trans_order');
        $data['page_title'] = "Transaction Orders";
        $data['load_js'] = "dine/trans_order.php";
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css');

        $this->load->model('dine/trans_model');

        $agent = sess('user');

		$pending = $this->transorder_model->get_transactions(null, array('type_id'=>40, 
															        	'confirmed'=>array(0,3), 
															        	'void_ref'=>NULL,  
															        	// 'user_id'=>$agent['id'], 
															        	'trans_sales.inactive'=>0, 
															        	"trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where',
															        												  'val'=>null,
															        												  'third'=>false)));
		
		$pending_ids = array_map(function($e) {
		    return is_object($e) ? $e->sales_id : $e['sales_id'];
		}, $pending);		


		if(isset($_SESSION['pending'])){
             $this->session->set_userdata('pending', $pending_ids);
        }else{
            $pending_array = array('pending'=>$pending_ids);
            $this->session->set_userdata($pending_array);
        }

		$branches = $this->trans_model->get_branches_list(null);       
        $data['code'] = makeTransListDisplay($branches, $pending);
		$data['use_js'] = "transListJS";

		$this->load->view('page',$data);
	}
	public function load_trans_list($branch){
		if(isset($branch))
		{		
			$this->load->model('dine/transorder_model');
			if($branch == 'voided')
			{
				$transactions = $this->transorder_model->get_transactions(null, array('type_id'=>11));
				$data['code'] = makeLoadVoidList($transactions);
			}else if($branch == 'open')
			{
				$transactions = $this->transorder_model->get_transactions(null, array('confirmed'=>1, 'type_id'=>40, 'void_ref'=>NULL, 'trans_sales.inactive'=>0, "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false)));
				$data['code'] = makeLoadTransList($transactions);
			}else{
				$transactions = $this->transorder_model->get_transactions(null, array('confirmed'=>1, 'trans_sales.branch_id'=>$branch, 'void_ref'=>NULL, 'trans_sales.inactive'=>0));
		        $data['code'] = makeLoadTransList($transactions);
			}

			$this->load->view('load',$data);
		}
	}
	public function pop_void_order($id){
		$data['code'] = make_pop_void_order($id);
		$this->load->view('load',$data);
	}
	public function pop_void_order_db(){
		$this->load->model('dine/transorder_model');
		$id = $this->input->post('id');

		$items = array('reason' => $this->input->post('reason'),
					   'inactive'=> $this->input->post('inactive'));

		echo $this->transorder_model->update_transaction($items, $id);
	}
	public function view_transaction($id){
		$data = $this->syter->spawn('trans_order');
		$data['page_title'] = "Transaction Details";

 		$data['load_js'] = "dine/trans_order.php";
        $this->load->model('dine/cashier_model');
		$this->load->model('dine/customers_model');
		$this->load->model('dine/trans_model');

       	$orders = $this->cashier_model->get_trans_sales($id);
		
		$trans = $orders[0];

		$cust_details = $this->customers_model->get_customer($trans->customer_id);
		$cust_address = $this->customers_model->get_customer_address($trans->customer_id, $trans->address_id);
		$branch = $this->trans_model->get_branches_list($trans->branch_id);

		$order_menus = $this->cashier_model->get_trans_sales_menus(null,array("trans_sales_menus.sales_id"=>$id));
        $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$id));
        
        $payment_mode = $this->cashier_model->get_mode_payment($id);


        $data['code'] = makeTransDetailsList($trans, $cust_details[0], $cust_address[0], $branch[0], $order_menus, $order_mods,  $payment_mode[0]);
        // $data['code'] = makeTransDetailsList($trans, $cust_details[0], $branch[0], $order_menus, $order_mods, $payment_mode[0]);
		$data['use_js'] = "viewTransactionJS";
		
		$this->load->view('page',$data);
	}
	public function transaction_search(){
		$search = $this->input->post('search');
		$branch_id = $this->input->post('branch_id');

		$args = array();		
		if($branch_id == 'open'){
			$args =array('inactive'=>0, 'type_id'=>'40');
		}else if($branch_id == 'voided'){
			$args =array('inactive'=>1);
		}else{
			$args =array('branch_id'=>$branch_id, 'inactive'=>0, 'type_id'=>'40');
		}
        $this->load->model('dine/transorder_model');
        $found = $this->transorder_model->get_transaction_search($search, $args);

        $trans = array();

        if(count($found) > 0 ){
            foreach ($found as $res) {
                $trans[] = array('key'=>$res->trans_ref,'value'=>$res->sales_id);
            }
        }
        echo json_encode($trans);
	}
	public function get_found_transaction(){
		$id = null;
		$args=array();

		$this->load->model('dine/transorder_model');

		if($this->input->post('branch_id'))
		{
			$branch_id = $this->input->post('branch_id');
			if($branch_id != 'open')
				$args = array('branch_id' => $this->input->post('branch_id'));
		}		

		if($this->input->post('id'))
			$id = $this->input->post('id');

		$trans = $this->transorder_model->get_transactions($id,$args);
		$code = makeTransactionSearch($trans);
		echo json_encode(array('code'=>$code));
	}
	public function countNotif(){

		$this->load->model('dine/transorder_model');
		$notif = $this->transorder_model->count_warning_status();
		echo json_encode($notif);		
	}
	public function get_pending_trans($get_code=false){
		$count_new_p_ids = $code = $sub_code = null;
		$p_ids = $this->session->userdata('pending');
		
		$this->load->model('site/site_model');
		$time = $this->site_model->get_db_now();
		$agent = sess('user');
		$args = array('type_id'=>40, 
		        	'confirmed'=>array(0,3), 
		        	'void_ref'=>NULL,  
		        	'user_id'=>$agent['id'], 
		        	'trans_sales.inactive'=>0, 
		        	"trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where',
		        												  'val'=>null,
		        												  'third'=>false));

		$pending = $this->transorder_model->get_transactions(null, $args, $p_ids);
		
		//retrieve new transactions
		if(!empty($pending))
		{
			$pending_ids = array_map(function($e) {
		  	  return is_object($e) ? $e->sales_id : $e['sales_id'];
			}, $pending);

			$pending_array = array('pending'=>$pending_ids);
			$merge_p_ids = array_merge($pending_ids, $p_ids);

			$this->session->set_userdata('pending', $merge_p_ids);

			
			foreach ($pending as $val) {
				$cust_name = $val->fname.' '.$val->mname. ' ' .$val->lname;		

				$this->make->sRow(array('class'=>'t-rows row-'.$val->sales_id));
	            $this->make->td(sql2DateTime($val->datetime));
	            $this->make->td($val->trans_ref);	
				$this->make->td(tagWord(strtoupper(ago($val->datetime,$time) ), 'warning' ));
				$this->make->td(ucwords($cust_name));

				$sub_code = ($val->confirmed == 3) ? tagWord('Cancelled', 'danger' ) : '';
				
				$CI->make->td('<span class="result">'.$sub_code.'</span>');	
				if($val->tr_inactive == 0) 
            		$a = $this->make->A(fa('fa-minus-circle fa-lg'),'#',array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'Void Transaction', 'id'=>'void-'.$val->sales_id,'class'=>'void-transaction','ref'=>$val->sales_id,'return'=>true));
				if($val->tr_inactive == 1 || $val->completed == 1 || $val->delivered == 1)
					$a = $this->make->A(fa('fa-minus-circle fa-lg'),'#',array('id'=>'voided-'.$val->sales_id,'ref'=>$val->sales_id, 'style'=> 'color: #DB9598;', 'return'=>true));
					$a .= $this->make->A(fa('fa-book fa-fw fa-lg'),base_url().'trans_order/view_transaction/'.$val->sales_id, array('data-toggle'=>'tooltip', 'data-placement'=>'top','title'=>'View Details',  'id'=>'view-'.$val->sales_id,'class'=>'view-transaction','ref'=>$val->sales_id,'return'=>true));
				$this->make->td($a);						            
				$this->make->eRow();
			}
		
			$code = $this->make->code();
		}
	
		$new_p_ids = $this->session->userdata('pending');
		$count_new_p_ids = count($new_p_ids);

		$status_pending = $this->transorder_model->get_transactions(null, $args, null, ' trans_sales.datetime, trans_sales.sales_id, trans_sales.confirmed ');
		$status = array();
		$sub_code = $sub_time = '';

		foreach($status_pending as $key => $val)
		{
			$sub_code = ($val->confirmed == 3) ? tagWord('Cancelled', 'danger' ) : '';
			$sub_time = tagWord(strtoupper(ago($val->datetime,$time) ), 'warning' );

			$status[$val->sales_id]['result'] = $sub_code;
			$status[$val->sales_id]['time_ago'] = $sub_time;
		}

		echo json_encode(array('code'=>$code, 'count'=>$count_new_p_ids, 'status'=>$status));
	}

}