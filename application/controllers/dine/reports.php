<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {
    #CALL CENTER REPORTS
    #USER: CALL CENTER MANAGER	
        public function __construct(){
            parent::__construct();
            $this->load->model('dine/reports_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $this->load->helper('dine/reports_helper');
            $this->load->helper('pdf_helper');
            $this->load->helper('excel_helper');
            date_default_timezone_set('Asia/Manila');

        }
        public function index(){
            $data = $this->syter->spawn(null);
            $data['page_title'] = "Generate Reports";
            $data['add_css'] = array('css/daterangepicker/daterangepicker-bs3.css', 'css/agents.css', 'css/datepicker.css');
            $data['add_js'] = array('js/plugins/daterangepicker/daterangepicker.js', 'js/bootstrap-datepicker.js');
            $data['code'] = makeIndexPage();
            $data['load_js'] = 'dine/reports.php';
            $data['use_js'] = 'reportIndexJS';
            $this->load->view('page',$data);
        }

        public function hit_rate_report(){
            $data = $this->syter->spawn(null);
            $data['page_title'] = "Hit Rate Report";
            $data['add_css'] = array('css/daterangepicker/daterangepicker-bs3.css', 'css/agents.css', 'css/datepicker.css');
            $data['add_js'] = array('js/plugins/daterangepicker/daterangepicker.js', 'js/bootstrap-datepicker.js');
            $data['code'] = make_hit_rate_report_form();
            $data['load_js'] = 'dine/reports.php';
            $data['use_js'] = 'hitRateJs';
            $this->load->view('page',$data);
        }

        public function print_hit_rate_report($type='pdf', $act='print', $daterange){
            $daterange = urldecode($daterange);

            $branches = $this->cashier_model->get_branches_list();
            $datefrom = $dateto = $date_from = $date_to = $date = '';
           
            $pos = strpos($daterange, 'to');
           
            if($pos === false)
            {   
                $date = (empty($daterange) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $daterange))));
                $date_title = ' as of '.date('m-d-y',strtotime(str_replace('-', '/', $date)));
                $date_name = ' as of '.date('M-d-y',strtotime(str_replace('-', '/', $date)));
            }else
            {
                $dates = explode(" to ",$daterange);

                $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));
                $date_title = date('m-d-y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('m-d-y',strtotime(str_replace('-', '/', $dates[1])));
                $date_name = ' from '. date('M-d-y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('M-d-y',strtotime(str_replace('-', '/', $dates[1])));
            }  

            
            $branches = $this->cashier_model->get_branches_list();

            $list = $list_result = array();
            
            $args = array('trans_sales.type_id'=>40, 
                        'trans_sales.void_ref'=>NULL,  
                        'trans_sales.inactive'=>0, 
                        "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where',
                                                                      'val'=>null,
                                                                      'third'=>false));

            // foreach($branches as $key => $br)
            // {
            //    $results = $this->reports_model->get_hit_rate_report_data($br->branch_id, null, null, $date, $args);
            //    if(!empty($results))
            //    {
            //         $list_result[$br->branch_code] = $results;
            //    }
            // }

            if($pos === false)
            {
               
                foreach($branches as $key => $br)
                { 
                   $result = $this->reports_model->get_hit_rate_report_data($br->branch_id, null, null, $date, $args);
                   $list_result[$br->branch_code] = $result;
                }

                $list[$date] = $list_result;
            }else{
                    $datefrom = strtotime(str_replace('-', '/', $dates[0]));
                    $dateto = strtotime(str_replace('-', '/', $dates[1]));

                    $count = 0;
                    while($datefrom <= $dateto)
                    {
                        $datefrom =  date('Y-m-d', $datefrom);
                        if($count == 0){
                           $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom)));
                           $count++;
                        }   
                        else
                            $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom) . ' + 1 day'));

                        if(strtotime($datefrom) <=  $dateto)
                        {
                            foreach($branches as $key => $br)
                            {
                                $result = $this->reports_model->get_hit_rate_report_data($br->branch_id, null, null, $datefrom, $args);
                                $list_result[$br->branch_code] = $result;
                            }

                            $list[$datefrom] = $list_result;   
                        }
                        $datefrom = strtotime($datefrom);
                    }
            }

           
            if($act == 'view'){
                $code = make_hit_rate_report_data($list_result);
                echo json_encode(array('code'=>$code));
            }else{
                if($pos === true)
                { 
                    $title = 'Store Hit Rate Report';
                    $file_name = 'StoreHitRateReport('. $date_title.')';
                }else{
                    $title = 'Store Hit Rate Report';
                    $file_name = 'StoreHitRateReport('.$date_title.')';
                }

                $data['details'] = array('type'=>$type, 'title'=>$title, 'filename'=>$file_name);
                $data['list'] = array('list'=>$list);

                if($type == 'pdf')
                    $this->load->view('reports/prints/print_hit_rate_report_pdf.php',$data);
                else
                    $this->load->view('reports/prints/print_hit_rate_report_excel.php',$data);
            }
        }

        public function complaint_report(){
            $data = $this->syter->spawn(null);
            $data['page_title'] = "Complaint Report";
            $data['add_css'] = array('css/daterangepicker/daterangepicker-bs3.css', 'css/agents.css', 'css/datepicker.css');
            $data['add_js'] = array('js/plugins/daterangepicker/daterangepicker.js', 'js/bootstrap-datepicker.js');
            $data['code'] = make_complaint_report_form();
            $data['load_js'] = 'dine/reports.php';
            $data['use_js'] = 'complaintJs';
            $this->load->view('page',$data);
        }
     
        public function print_complaint_report($type='pdf', $act='print', $daterange){
            $daterange = urldecode($daterange);

            $branches = $this->cashier_model->get_branches_list();
            $datefrom = $dateto = $date_from = $date_to = $date = '';
           
            $pos = strpos($daterange, 'to');
           
            if($pos === false)
            {   
                $date = (empty($daterange) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $daterange))));
                $date_title = ' as of '.date('m-d-y',strtotime(str_replace('-', '/', $date)));
                $date_name = ' as of '.date('M-d-y',strtotime(str_replace('-', '/', $date)));
            }else
            {
                $dates = explode(" to ",$daterange);

                $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));
                $date_title = date('m-d-y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('m-d-y',strtotime(str_replace('-', '/', $dates[1])));
                $date_name = ' from '. date('M-d-y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('M-d-y',strtotime(str_replace('-', '/', $dates[1])));
            }  

            $branches = $this->cashier_model->get_branches_list();
            $list = $complaints_reason = array();

                if($pos === false)
                {
                    foreach($branches as $key => $br)
                    { 
                       $complaints = $this->reports_model->get_complaints_report_data($br->branch_id, $date_from, $date_to, $date);
                        if(!empty($complaints))
                        {
                            foreach($complaints as $key=>$c)
                            {
                                $complaints_reason[$br->branch_code][$c->reason] = $c->count_reason;
                            }
                        }else{
                            $complaints_reason[$br->branch_code] = null;
                        }
                    }
                    $list[$date] = $complaints_reason;
                }else{
                    $datefrom = strtotime(str_replace('-', '/', $dates[0]));
                    $dateto = strtotime(str_replace('-', '/', $dates[1]));

                    $count = 0;
                    while($datefrom <= $dateto)
                    {
                        $datefrom =  date('Y-m-d', $datefrom);
                        if($count == 0)
                        {
                           $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom)));
                           $count++;
                        }   
                        else
                            $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom) . ' + 1 day'));


                        if(strtotime($datefrom) <=  $dateto)
                        {
                            foreach($branches as $key => $br)
                            {
                                $results = $this->reports_model->get_complaints_report_data($br->branch_id, null, null, $datefrom);
                                if(!empty($results))
                                {
                                    foreach($results as $key=>$c)
                                       $list_result[$br->branch_code][$c->reason] = $c->count_reason;
                                }else{
                                    $list_result[$br->branch_code] = null;
                                }
                            }

                            $list[$datefrom] = $list_result;   
                        }
                        $datefrom = strtotime($datefrom);
                    }
                }

                if($act == 'view')
                {
                    $code = make_complaint_report_data($complaints_reason);
                    echo json_encode(array('code'=>$code));
                } 
                else
                {
                    if($pos === true)
                    { 
                        $title = 'Complaint Report';
                        $file_name = 'ComplaintReport('. $date_title.')';
                    }else{
                        $title = 'Complaint Report';
                        $file_name = 'ComplaintReport('.$date_title.')';
                    }

                    $data['details'] = array('type'=>$type, 'title'=>$title, 'filename'=>$file_name);
                    $data['list'] = array('list'=>$list);
                    
                    // print_r($data);

                    if($type == 'pdf')
                        $this->load->view('reports/prints/print_complaint_report_pdf.php',$data);
                    else
                        $this->load->view('reports/prints/print_complaint_report_excel.php',$data);
                }
        }

        public function cancelled_report(){
            $data = $this->syter->spawn(null);
            $data['page_title'] = "Store Cancelled Report";
            $data['add_css'] = array('css/daterangepicker/daterangepicker-bs3.css', 'css/agents.css', 'css/datepicker.css');
            $data['add_js'] = array('js/plugins/daterangepicker/daterangepicker.js', 'js/bootstrap-datepicker.js');
            $data['code'] = make_cancelled_report_form();
            $data['load_js'] = 'dine/reports.php';
            $data['use_js'] = 'cancelledJs';
            $this->load->view('page',$data);
        }

        public function print_cancelled_report($type='pdf', $act='print', $daterange=null)
        {
                $daterange = urldecode($daterange);

                $branches = $this->cashier_model->get_branches_list();
                $datefrom = $dateto = $date_from = $date_to = $date = '';
                
                $void_reason = array(
                    "Change of Mind",
                    "Change Order",
                    "No Show Delivery",
                    "Took Too long",
                    "Others"
                );

                $pos = strpos($daterange, 'to');
               
                if($pos === false)
                {   
                    $date = (empty($daterange) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $daterange))));
                    $date_title = ' as of '.date('M d, y',strtotime(str_replace('-', '/', $date)));
                    $date_name = ' as of '.date('M-d-y',strtotime(str_replace('-', '/', $date)));
                }else
                {
                    $dates = explode(" to ",$daterange);

                    $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                    $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));
                    $date_title = ' from '. date('M d, Y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('M d,Y',strtotime(str_replace('-', '/', $dates[1])));
                    $date_name = ' from '. date('M-d-y',strtotime(str_replace('-', '/', $dates[0]))) . ' to ' . date('M-d-y',strtotime(str_replace('-', '/', $dates[1])));
                }  

                $branches = $this->cashier_model->get_branches_list();
                $list = $list_result = array();
            
                $args = array(
                    'trans_sales.type_id'=>40, 
                    'trans_sales.inactive'=>1,
                    'trans_sales.trans_ref  IS NOT NULL' => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false),
                    'trans_sales.reason  IS NOT NULL' => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false));
                if($pos === false)
                {
                    foreach($branches as $key => $br)
                    { 
                       $results = $this->reports_model->get_cancelled_report($br->branch_id, $date_from, $date_to, $date, $args);
                       if(!empty($results))
                       {
                            foreach($results as $key=>$c)
                                $list_result[$br->branch_code][$c->reason] = $c->count_reason;
                       }else{
                            $list_result[$br->branch_code] = null;
                       }
                    }
                    $list[$date] = $list_result;
                }else{
                    $datefrom = strtotime(str_replace('-', '/', $dates[0]));
                    $dateto = strtotime(str_replace('-', '/', $dates[1]));

                    $count = 0;
                    while($datefrom <= $dateto)
                    {
                        $datefrom =  date('Y-m-d', $datefrom);
                        if($count == 0)
                        {
                           $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom)));
                           $count++;
                        }   
                        else
                            $datefrom = date('Y-m-d', strtotime(str_replace('-', '/', $datefrom) . ' + 1 day'));


                        if(strtotime($datefrom) <=  $dateto)
                        {
                            foreach($branches as $key => $br)
                            {

                               $results = $this->reports_model->get_cancelled_report($br->branch_id, null, null, $datefrom, $args);
                               if(!empty($results))
                               {
                                    foreach($results as $key=>$c)
                                        $list_result[$br->branch_code][$c->reason] = $c->count_reason;
                               }else{
                                    $list_result[$br->branch_code] = null;
                               }
                            }

                            $list[$datefrom] = $list_result;   
                        }
                        $datefrom = strtotime($datefrom);
                    }
                }

               
                if($act == 'view')
                {
                  $code = make_cancelled_report_data($list);
                  echo json_encode(array('code'=>$code));
                }   
                else
                {
                    if($pos === true)
                    { 
                        $title = 'Cancelled Report ';
                        $file_name = 'Cancelled Report '. $date_name;
                        // $data['range']= array('date_from'=>$date_from, 'date_to'=>$date_to);
                    }else{
                        $title = 'Cancelled Report ';
                        $file_name = 'CancelledReport'.$date_name;
                    }

                    $data['details'] = array('type'=>$type, 'title'=>$title, 'filename'=>$file_name);
                    $data['list'] = array('list'=>$list);
                 
                    if($type == 'pdf')
                        $this->load->view('reports/prints/print_cancelled_report_pdf.php',$data);
                    else
                        $this->load->view('reports/prints/print_cancelled_report_excel.php',$data);
                }
        }
}   