<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_payment extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->locations=User_helper::get_locations();
        $this->user=User_helper::get_user();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_payment_lang');
    }


    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="set_preference_outlets_payment")
        {
            $this->system_set_preference_outlets_payment();
        }
        elseif($action=="get_items_outlets_payment")
        {
            $this->system_get_items_outlets_payment();        }

        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Payment Report";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {
                            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
                            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id=customer.id','INNER');
                            $this->db->select('customer.id value, cus_info.name text');
                            $this->db->where('customer.status',$this->config->item('system_status_active'));
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
                            $this->db->where('cus_info.revision',1);
                            $data['outlets']=$this->db->get()->result_array();
                        }
                    }

                }
            }

            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }

            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $ajax['status']=true;
            $data['title']="Outlets Payment Sales Report";
            $data['system_preference_items']= $this->get_preference_outlets_payment();
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_outlets_payment",$data,true));

            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }

            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }


    private function get_preference_headers_outlets_payment()
    {
        $data['sl_no']= 1;
        $data['outlet']= 1;
        $data['amount_payment']= 1;
        $data['amount_bank_charge']= 1;
        $data['amount_receive']= 1;
        return $data;
    }
    private function get_preference_outlets_payment()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_outlets_payment"'),1);
        $data=$this->get_preference_headers_outlets_payment();
        if($result)
        {
            if($result['preferences']!=null)
            {
                $preferences=json_decode($result['preferences'],true);
                foreach($data as $key=>$value)
                {
                    if(isset($preferences[$key]))
                    {
                        $data[$key]=$value;
                    }
                    else
                    {
                        $data[$key]=0;
                    }
                }
            }
        }
        return $data;
    }
    private function system_set_preference_outlets_payment()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_outlets_payment();
            $data['preference_method_name']='search_outlets_payment';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_outlets_payment');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_outlets_payment()
    {
        $items=array();

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $status_deposit_forward=$this->input->post('status_deposit_forward');
        $status_payment_receive=$this->input->post('status_payment_receive');
        $search_by=$this->input->post('search_by');


        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->order_by('outlet_info.ordering');
        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));

        if($division_id>0)
        {
            $this->db->where('zones.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('districts.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $results=$this->db->get()->result_array();
        $outlets=array();
        $outlet_ids=array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlets[$result['outlet_id']]=$this->initialize_row_outlets_payment($result['outlet_name']);
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }

        $this->db->from($this->config->item('table_pos_payment').' payment');
        $this->db->select('payment.outlet_id');
        $this->db->select('SUM(payment.amount_payment) amount_payment',false);
        $this->db->select('SUM(payment.amount_bank_charge) amount_bank_charge',false);
        $this->db->select('SUM(payment.amount_receive) amount_receive',false);

        $this->db->where('payment.status !=',$this->config->item('system_status_delete'));
        $this->db->where('payment.status_deposit_forward',$status_deposit_forward);
        if($status_payment_receive)
        {
            $this->db->where('payment.status_payment_receive',$status_payment_receive);
        }

        if($search_by=='search_by_sale_date')
        {
            $this->db->where('payment.date_sale >=',$date_start);
            $this->db->where('payment.date_sale <=',$date_end);
        }
        else if($search_by=='search_by_payment_date')
        {
            $this->db->where('payment.date_payment >=',$date_start);
            $this->db->where('payment.date_payment <=',$date_end);
        }
        $this->db->where_in('payment.outlet_id',$outlet_ids);
        $this->db->order_by('payment.id','DESC');
        $this->db->group_by('payment.outlet_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $outlets[$result['outlet_id']]['amount_payment']=$result['amount_payment'];
            $outlets[$result['outlet_id']]['amount_bank_charge']=$result['amount_bank_charge'];
            $outlets[$result['outlet_id']]['amount_receive']=$result['amount_receive'];
        }

        $grand_total=$this->initialize_row_outlets_payment('Grand Total');
        foreach($outlets as $info)
        {
            foreach($info  as $key=>$r)
            {
                if(!(($key=='outlet')||($key=='sl_no')))
                {
                    $grand_total[$key]+=$info[$key];
                }
            }

            $items[]=$info;
            //$items[]=$item;
        }
        $items[]=$grand_total;
        $this->json_return($items);

    }
    private function initialize_row_outlets_payment($outlet_name)
    {
        $row=$this->get_preference_headers_outlets_payment();
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['outlet']=$outlet_name;
        $row['sl_no']='';
        return $row;
    }
}
