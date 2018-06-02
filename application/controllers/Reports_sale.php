<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_sale extends Root_Controller
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
        $this->lang->load('report_sale_lang');
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
        elseif($action=="set_preference_area_amount")
        {
            $this->system_set_preference_area_amount();
        }
        elseif($action=="get_items_area_amount")
        {
            $this->system_get_items_area_amount();
        }
        elseif($action=="set_preference_outlets_amount")
        {
            $this->system_set_preference_outlets_amount();
        }
        elseif($action=="get_items_outlets_amount")
        {
            $this->system_get_items_outlets_amount();
        }
//        elseif($action=='get_items_variety_sale')
//        {
//            $this->system_get_items_variety_sale();
//        }
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
            $data['title']="Outlets Sale Report";
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
            if($reports['report_name']=='area_amount')
            {
                $data['system_preference_items']= $this->get_preference_area_amount();
                if($reports['outlet_id']>0)
                {
                    $data['areas']='Outlet';
                }
                elseif($reports['district_id']>0)
                {
                    $data['areas']='Outlets';
                }
                elseif($reports['territory_id']>0)
                {
                    $data['areas']='Districts';
                }
                elseif($reports['zone_id']>0)
                {
                    $data['areas']='Territories';
                }
                elseif($reports['division_id']>0)
                {
                    $data['areas']='Zones';
                }
                else
                {
                    $data['areas']='Divisions';
                }
                $data['title']="Area Wise Sales Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_area_amount",$data,true));
            }
            elseif($reports['report_name']=='outlets_amount')
            {
                $data['title']="Outlet Wise Sales Report";
                $data['system_preference_items']= $this->get_preference_outlets_amount();
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_outlets_amount",$data,true));

            }
            elseif($reports['report_name']=='variety_amount_quantity')
            {
                //$data['title']="Product Sales Report";
                //$ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_variety_sale",$data,true));
                $ajax['status']=false;
                $ajax['system_message']='Under Development';
                $this->json_return($ajax);
            }
            else
            {
                $this->message='Invalid Report type';
            }

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
    private function get_preference_headers_area_amount()
    {
        $data['area']= 1;
        $data['amount_total']= 1;
        $data['amount_discount_variety']= 1;
        $data['amount_discount_self']= 1;
        $data['amount_discount_total']= 1;
        $data['amount_payable_all']= 1;
        $data['amount_payable_actual_all']= 1;
        $data['amount_payable_cancel']= 1;
        $data['amount_payable_actual_cancel']= 1;
        $data['amount_payable_paid']= 1;
        $data['amount_payable_actual_paid']= 1;
        return $data;
    }
    private function get_preference_area_amount()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_area_amount"'),1);
        $data=$this->get_preference_headers_area_amount();
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
    private function system_set_preference_area_amount()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_area_amount();
            $data['preference_method_name']='search_area_amount';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_area_amount');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_area_amount()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        if($outlet_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$outlet_id,'revision =1'));
            //$location_type='customer_id';
            $location_type='outlet_id';
        }
        elseif($district_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('district_id ='.$district_id,'revision =1','type ="'.$this->config->item('system_customer_type_outlet_id').'"'));
            //$location_type='customer_id';
            $location_type='outlet_id';
        }
        elseif($territory_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='district_id';
        }
        elseif($zone_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='territory_id';
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='zone_id';
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $location_type='division_id';
        }
        $area_initial=array();
        //setting 0
        foreach($areas as $area)
        {
            $area_initial[$area['value']]=$this->initialize_row_area_amount($area['text']);
        }
        //total sales
        $this->db->from($this->config->item('table_pos_sale').' sale');

        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_total ELSE 0 END) amount_total',false);
        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_discount_variety ELSE 0 END) amount_discount_variety',false);
        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_discount_self ELSE 0 END) amount_discount_self',false);


        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_payable ELSE 0 END) amount_payable_all',false);
        $this->db->select('SUM(CASE WHEN sale.date_cancel>='.$date_start.' and sale.date_cancel<='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then sale.amount_payable ELSE 0 END) amount_payable_cancel',false);

        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_payable_actual ELSE 0 END) amount_payable_actual_all',false);
        $this->db->select('SUM(CASE WHEN sale.date_cancel>='.$date_start.' and sale.date_cancel<='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then sale.amount_payable_actual ELSE 0 END) amount_payable_actual_cancel',false);


        $this->db->select('sale.outlet_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');


        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = sale.outlet_id and outlet_info.revision =1','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('sale.outlet_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $this->db->group_by(array($location_type));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $area_initial[$result[$location_type]]['amount_total']=$result['amount_total'];
            $area_initial[$result[$location_type]]['amount_discount_variety']=$result['amount_discount_variety'];
            $area_initial[$result[$location_type]]['amount_discount_self']=$result['amount_discount_self'];
            $area_initial[$result[$location_type]]['amount_discount_total']=$result['amount_discount_variety']+$result['amount_discount_self'];
            $area_initial[$result[$location_type]]['amount_payable_all']=$result['amount_payable_all'];
            $area_initial[$result[$location_type]]['amount_payable_cancel']=$result['amount_payable_cancel'];
            $area_initial[$result[$location_type]]['amount_payable_paid']=$result['amount_payable_all']-$result['amount_payable_cancel'];
            $area_initial[$result[$location_type]]['amount_payable_actual_all']=$result['amount_payable_actual_all'];
            $area_initial[$result[$location_type]]['amount_payable_actual_cancel']=$result['amount_payable_actual_cancel'];
            $area_initial[$result[$location_type]]['amount_payable_actual_paid']=$result['amount_payable_actual_all']-$result['amount_payable_actual_cancel'];
        }
        $grand_total=$this->initialize_row_area_amount('Grand Total');
        $headers=$this->get_preference_headers_area_amount();
        foreach($area_initial as $info)
        {
            foreach($headers  as $key=>$r)
            {
                if(!($key=='area'))
                {
                    $grand_total[$key]+=$info[$key];
                }
            }

            $items[]=$this->get_row_area_amount($info);
            //$items[]=$item;
        }
        $items[]=$this->get_row_area_amount($grand_total);
        $this->json_return($items);


    }
    private function initialize_row_area_amount($area_name)
    {
        $row=$this->get_preference_headers_area_amount();
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['area']=$area_name;
        return $row;
    }
    private function get_row_area_amount($info)
    {
        $row=array();
        foreach($info as $key=>$r)
        {
            if($key!='area')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],2);
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }
        }
        return $row;
    }

    private function get_preference_headers_outlets_amount()
    {
        $data['sl_no']= 1;
        $data['outlet']= 1;
        $data['amount_total']= 1;
        $data['amount_discount_variety']= 1;
        $data['amount_discount_self']= 1;
        $data['amount_discount_total']= 1;
        $data['amount_payable_all']= 1;
        $data['amount_payable_actual_all']= 1;
        $data['amount_payable_cancel']= 1;
        $data['amount_payable_actual_cancel']= 1;
        $data['amount_payable_paid']= 1;
        $data['amount_payable_actual_paid']= 1;
        return $data;
    }
    private function get_preference_outlets_amount()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_outlets_amount"'),1);
        $data=$this->get_preference_headers_outlets_amount();
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
    private function system_set_preference_outlets_amount()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference_outlets_amount();
            $data['preference_method_name']='search_outlets_amount';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_outlets_amount');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_outlets_amount()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->order_by('outlet_info.customer_id');
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
            $outlets[$result['outlet_id']]=$this->initialize_row_outlets_amount($result['outlet_name']);
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }

        //total sales
        $this->db->from($this->config->item('table_pos_sale').' sale');
        $this->db->select('sale.outlet_id');

        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_total ELSE 0 END) amount_total',false);
        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_discount_variety ELSE 0 END) amount_discount_variety',false);
        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_discount_self ELSE 0 END) amount_discount_self',false);


        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_payable ELSE 0 END) amount_payable_all',false);
        $this->db->select('SUM(CASE WHEN sale.date_cancel>='.$date_start.' and sale.date_cancel<='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then sale.amount_payable ELSE 0 END) amount_payable_cancel',false);

        $this->db->select('SUM(CASE WHEN sale.date_sale>='.$date_start.' and sale.date_sale<='.$date_end.' then sale.amount_payable_actual ELSE 0 END) amount_payable_actual_all',false);
        $this->db->select('SUM(CASE WHEN sale.date_cancel>='.$date_start.' and sale.date_cancel<='.$date_end.' and sale.status="'.$this->config->item('system_status_inactive').'" then sale.amount_payable_actual ELSE 0 END) amount_payable_actual_cancel',false);

        $this->db->where_in('sale.outlet_id',$outlet_ids);
        $this->db->group_by('sale.outlet_id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $outlets[$result['outlet_id']]['amount_total']=$result['amount_total'];
            $outlets[$result['outlet_id']]['amount_discount_variety']=$result['amount_discount_variety'];
            $outlets[$result['outlet_id']]['amount_discount_self']=$result['amount_discount_self'];
            $outlets[$result['outlet_id']]['amount_discount_total']=$result['amount_discount_variety']+$result['amount_discount_self'];
            $outlets[$result['outlet_id']]['amount_payable_all']=$result['amount_payable_all'];
            $outlets[$result['outlet_id']]['amount_payable_cancel']=$result['amount_payable_cancel'];
            $outlets[$result['outlet_id']]['amount_payable_paid']=$result['amount_payable_all']-$result['amount_payable_cancel'];
            $outlets[$result['outlet_id']]['amount_payable_actual_all']=$result['amount_payable_actual_all'];
            $outlets[$result['outlet_id']]['amount_payable_actual_cancel']=$result['amount_payable_actual_cancel'];
            $outlets[$result['outlet_id']]['amount_payable_actual_paid']=$result['amount_payable_actual_all']-$result['amount_payable_actual_cancel'];
        }
        $grand_total=$this->initialize_row_outlets_amount('Grand Total');
        $headers=$this->get_preference_headers_outlets_amount();
        foreach($outlets as $info)
        {
            foreach($headers  as $key=>$r)
            {
                if(!(($key=='outlet')||($key=='sl_no')))
                {
                    $grand_total[$key]+=$info[$key];
                }
            }

            $items[]=$this->get_row_outlets_amount($info);
            //$items[]=$item;
        }
        $items[]=$this->get_row_outlets_amount($grand_total);
        $this->json_return($items);


    }
    private function initialize_row_outlets_amount($outlet_name)
    {
        $row=$this->get_preference_headers_outlets_amount();
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['outlet']=$outlet_name;
        $row['sl_no']='';
        return $row;
    }
    private function get_row_outlets_amount($info)
    {
        $row=array();
        foreach($info as $key=>$r)
        {
            if(!(($key=='outlet')||($key=='sl_no')))
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],2);
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }
        }
        return $row;
    }
}
