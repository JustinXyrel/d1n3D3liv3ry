<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/agent_helper');
        $this->load->model('dine/transorder_model');
        $this->load->model('site/site_model');
    }
    public function index(){
        $data = $this->syter->spawn(null);
        $list = array();
        $list['pending'] = $this->get_agents_trans('pending');
        $list['cancelled'] = $this->get_agents_trans('cancelled');
        $list['on_process'] = $this->get_agents_trans('on_process');
        $list['on_hold'] = $this->get_agents_trans('on_hold');
        $list['void'] = $this->get_agents_trans('void');

        $data['code'] = indexPage($list['pending'], $list['cancelled'], $list['on_process'], $list['on_hold'], $list['void']);
        $data['add_js'] = array('js/jquery.playSound.js');
        $data['load_js'] = 'dine/agent.php';
        $data['add_css'] = 'css/agents.css';
        $data['use_js'] = 'centralizedViewJs';
        $this->load->view('agent',$data);
    }
    public function get_list_pending($status){
        
        $result = $this->get_agents_trans($status);
        $code = makeFirstTable($result);

        echo json_encode(array('code'=>$code, 'result'=>$result));
    }
    
    public function get_list_by_status($status){
        $result = $this->get_agents_trans($status);

        $header = '';

        switch($status){
            case 'pending':
               $header = 'PENDING TRANSACTIONS';
            break;
            case 'cancelled':
              $header = 'NOT AVAILABLE TRANSACTIONS';
            break;
            case 'process':
            case 'on_process':
                $header = 'CONFIRMED TRANSACTIONS';
            break;
            case 'hold':
            case 'on_hold':
                $header = 'ON HOLD TRANSACTIONS';
            break;
            case 'voided':
            case 'void':
                $header = 'VOIDED TRANSACTIONS';
            break;
        }

         $code = makeSecondTable($result,$status, $header);
         echo json_encode(array('code'=>$code, 'result'=>$result));
    }

    public function update_centralized()
    {
        $arr = array('pending', 'cancelled','on_process', 'on_hold','void');
        $add_session = $result_ids = $new_set_trans = array();

        foreach($arr as $status)
        {
            $count = '';
            $current = $this->session->userdata('agent_'.$status);
            $data_ = $this->get_agents_trans($status, true, $current); 
        
            $result = $this->get_agents_trans($status);
            
            if($status == 'pending')
                $new_trans[$status]['code'] = makeAppendRow($status, $data_, count($result ));
            if($status == 'on_process')
            {
               $new_trans[$status]['code'] = makeAppendRow($status, $data_, count($result));
               $new_trans[$status]['tds']   = makeProgressBarTD($result);                
            }
            
            $new_trans[$status]['count'] = count($result);
            $new_trans[$status]['data'] = $data_; 
            $new_trans[$status]['current'] = $current;
        }

        echo json_encode($new_trans);
    }

    public function get_agents_trans($status='pending', $new_trans=false, $where_not_in=array()){
       $args = array(
                    // 'DATE(trans_sales.datetime)' => date('Y-m-d'),
                    'trans_sales.type_id'=>40, 
                    'trans_sales.inactive'=>0,
                    'trans_sales.trans_ref  IS NOT NULL' => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false));

        $args["DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')"] = date('Y-m-d');

        switch($status){
            case 'pending':
                $args['trans_sales.confirmed'] = 0; 
                $args['trans_sales.void_ref'] = NULL;
                $args['trans_sales.on_hold'] = 0;
            break;
            case 'cancelled':
                $args['trans_sales.confirmed'] = 3; 
                $args['trans_sales.void_ref'] = NULL;
                $args['trans_sales.on_hold'] = 0;
            break;
            case 'process':
            case 'on_process':
                $args['trans_sales.confirmed'] = 1; 
                $args['trans_sales.void_ref'] = NULL;
                $args['trans_sales.on_hold'] = 0;
            break;
            case 'hold':
            case 'on_hold':
                $args['trans_sales.void_ref'] = NULL;
                $args['trans_sales.on_hold'] = 1;
            break;
            case 'voided':
            case 'void':
                $args['trans_sales.inactive'] = 1;
                $args['trans_sales.reason  IS NOT NULL'] = array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false); 
            break;
        }

        if($new_trans == true)
        {
            $result = $this->transorder_model->get_transactions(null, $args, $where_not_in);
        }else{
            $result = $this->transorder_model->get_transactions(null, $args, null);
           
            $result_ids = array_map(function($e) {
                return is_object($e) ? $e->sales_id : $e['sales_id'];
            }, $result);       

            if(isset($_SESSION['agent_'.$status])){
                 $this->session->set_userdata('agent_'.$status, $result_ids);
            }else{
                $res_array = array('agent_'.$status=>$result_ids);
                $this->session->set_userdata($res_array);
            }

        }


        return $result;
    }

}
   