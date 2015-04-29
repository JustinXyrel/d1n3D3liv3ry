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
            $date_from = $date_to = $date = '';
            
            $pos = strpos($daterange, 'to');
            
            if($pos === false)
            {   
                $date = (empty($daterange) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $daterange))));
                $date_title = ' As Of '.$date;
            }else
            {
                $dates = explode(" to ",$daterange);

                $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));
                $date_title = ' From '. str_replace('-', '/', $dates[0]) . ' to ' . str_replace('-', '/', $dates[1]);
            }  

            $branches = $this->cashier_model->get_branches_list();
            $list_result = array();
            
            $args = array('trans_sales.type_id'=>40, 
                        'trans_sales.void_ref'=>NULL,  
                        'trans_sales.inactive'=>0, 
                        "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where',
                                                                      'val'=>null,
                                                                      'third'=>false));

            foreach($branches as $key => $br)
            {
               $results = $this->reports_model->get_hit_rate_report_data($br->branch_id, $date_from, $date_to, $date, $args);
               if(!empty($results))
               {
                    $list_result[$br->branch_code] = $results;
               }
            }

            $code = make_hit_rate_report_data($list_result);
            if($act == 'view')
                echo json_encode(array('code'=>$code));
            else
            {
                if($pos === true)
                { 
                    $title = 'Store Hit Rate Report' . $date_title;
                    $file_name = 'StoreHitRateReport('. $date_title.')';
                    // $data['range']= array('date_from'=>$date_from, 'date_to'=>$date_to);
                }else{
                    $title = 'Store Hit Rate Report' . $date_title;
                    $file_name = 'StoreHitRateReport('.$date_title.')';
                }

                $data['details'] = array('type'=>$type, 'code'=>$code, 'title'=>$title, 'filename'=>$file_name);
                $data['list'] = array('list'=>$list_result);
             
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

        #Range Date From to To
        public function print_complaint_report($type='pdf', $act='print', $daterange){
            $daterange = urldecode($daterange);

            $branches = $this->cashier_model->get_branches_list();
            $date_from = $date_to = $date = '';
            
            $pos = strpos($daterange, 'to');
            
            if($pos === false)
            {   
                $date = (empty($daterange) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $daterange))));
                $date_title = ' As Of '.$date;
            }else
            {
                $dates = explode(" to ",$daterange);

                $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime(str_replace('-', '/', $dates[0]))));
                $date_to = (empty($dates[1]) ? date('Y-m-d') :  date('Y-m-d', strtotime(str_replace('-', '/', $dates[1]))));
                $date_title = ' From '. str_replace('-', '/', $dates[0]) . ' to ' . str_replace('-', '/', $dates[1]);
            }  

            $branches = $this->cashier_model->get_branches_list();
            $complaints_reason = array();

            foreach($branches as $key => $br)
            {

               $complaints = $this->reports_model->get_complaints_report_data($br->branch_id, $date_from, $date_to, $date);
               // print_r($complaints);
               if(!empty($complaints))
               {
                    foreach($complaints as $key=>$c)
                    {
                        $complaints_reason[$br->branch_code][$c->reason] = $c->count_reason;
                    }
               }
            }
           
            $code = make_complaint_report_data($complaints_reason);
            if($act == 'view')
                echo json_encode(array('code'=>$code));
            else
            {
                if($pos === true)
                { 
                    $title = 'Complaint Report' . $date_title;
                    $file_name = 'ComplaintReport('. $date_title.')';
                    // $data['range']= array('date_from'=>$date_from, 'date_to'=>$date_to);
                }else{
                    $title = 'Complaint Report' . $date_title;
                    $file_name = 'ComplaintReport('.$date_title.')';
                }

                $data['details'] = array('type'=>$type, 'code'=>$code, 'title'=>$title, 'filename'=>$file_name);
                $data['list'] = array('list'=>$complaints_reason);
             
                if($type == 'pdf')
                    $this->load->view('reports/prints/print_complaint_report_pdf.php',$data);
                else
                    $this->load->view('reports/prints/print_complaint_report_excel.php',$data);
            }
        }

        // public function hold_report(){
        //     $data = $this->syter->spawn(null);
        //     $data['page_title'] = "Hold Report";
        //     $this->load->view('page',$data);
        // }

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

        public function print_cancelled_report($type='pdf', $act='print', $daterange){
                $daterange = urldecode($daterange);

                $branches = $this->cashier_model->get_branches_list();
                $date_from = $date_to = $date = '';
                
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
                $list_result = array();
            
                $args = array(
                    'trans_sales.type_id'=>40, 
                    'trans_sales.inactive'=>1,
                    'trans_sales.trans_ref  IS NOT NULL' => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false),
                    'trans_sales.reason  IS NOT NULL' => array('use'=>'where',
                                                                  'val'=>null,
                                                                  'third'=>false));
                
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
            
                $code = make_cancelled_report_data($list_result);
                if($act == 'view')
                    echo json_encode(array('code'=>$code));
                else
                {
                    if($pos === true)
                    { 
                        $title = 'Cancelled Report '. $date_title;
                        $file_name = 'Cancelled Report '. $date_name;
                        // $data['range']= array('date_from'=>$date_from, 'date_to'=>$date_to);
                    }else{
                        $title = 'Cancelled Report '. $date_title;
                        $file_name = 'CancelledReport'.$date_name;
                    }

                    $data['details'] = array('type'=>$type, 'code'=>$code,'sheetTitle'=>$date_title, 'title'=>$title, 'filename'=>$file_name);
                    $data['list'] = array('list'=>$list_result);
                 
                    if($type == 'pdf')
                        $this->load->view('reports/prints/print_cancelled_report_pdf.php',$data);
                    else
                        $this->load->view('reports/prints/print_cancelled_report_excel.php',$data);
                }
        }
}   