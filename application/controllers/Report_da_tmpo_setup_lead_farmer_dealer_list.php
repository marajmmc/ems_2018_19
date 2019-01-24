<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_da_tmpo_setup_lead_farmer_dealer_list extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_labels();
    }
    public function index($action="search")
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('search');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }
    private function language_labels()
    {
        // jqx grid
        $this->lang->language['LABEL_GROWING_AREA_NAME']='Growing Area Name';
        $this->lang->language['LABEL_DEALER_NAME']='Name';
        $this->lang->language['LABEL_DEALER_MOBILE_NO']='Mobile No';
        $this->lang->language['LABEL_LEAD_FARMER_MOBILE_NO']='Mobile No';
        $this->lang->language['LABEL_LEAD_FARMER_CREATED_DATE']='Created Date';

    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='search')
        {
            $data['growing_area_name']= 1;
            $data['dealer_name']= 1;
            $data['dealer_mobile_no']= 1;
            $data['lead_farmer_name']= 1;
            $data['lead_farmer_mobile_no']= 1;
            $data['lead_farmer_created_date']= 1;
        }
        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $this->db->from($this->config->item('table_login_csetup_customer').' outlet');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = outlet.id','INNER');
            $this->db->select('outlet_info.customer_id id,outlet_info.name outlet_name, outlet_info.ordering order');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');

            if($this->locations['division_id']>0)
            {
                $this->db->where('division.id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('zone.id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('t.id',$this->locations['territory_id']);
                        if($this->locations['district_id']>0)
                        {
                            $this->db->where('d.id',$this->locations['district_id']);
                        }
                    }
                }
            }
            $this->db->where('outlet.status',$this->config->item('system_status_active'));
            $this->db->where('outlet_info.revision',1);
            $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->order_by('outlet_info.ordering','ASC');
            $data['outlets']=$this->db->get()->result_array();

            $data['title']="Growing Area Lead Farmer & Dealer";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
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
            $method='search';
            $user = User_helper::get_user();
            $reports=$this->input->post('report');
            if(!($reports['outlet_id']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Select outlet.';
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            //$data['system_preference_items']= $this->get_preference_headers('search');
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['title']="Lead Farmer & Dealer List";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

            $ajax['status']=true;
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $outlet_id=$this->input->post('outlet_id');

        /*get outlets */
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' area');
        $this->db->select('area.*');
        $this->db->where('area.status',$this->config->item('system_status_active'));
        $this->db->where('area.outlet_id',$outlet_id);
        $results=$this->db->get()->result_array();
        $areas=array();
        $area_ids[0]=0;
        foreach($results as $result)
        {
            $area_ids[$result['id']]=$result['id'];
            $areas[$result['id']]=$result;
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmer');
        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' area','area.id=lead_farmer.area_id','INNER');
        $this->db->select('lead_farmer.*');
        $this->db->where('lead_farmer.status',$this->config->item('system_status_active'));
        $this->db->where_in('lead_farmer.area_id',$area_ids);
        $results=$this->db->get()->result_array();
        $lead_farmers=array();
        foreach($results as $result)
        {
            $lead_farmers[$result['area_id']][]=array(
                'lead_farmer_name'=>$result['name'],
                'lead_farmer_mobile_no'=>$result['mobile_no'],
                'lead_farmer_created_date'=>System_helper::display_date($result['date_created'])
            );
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
        $this->db->select('dealers.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','INNER');
        $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');
        $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
        $this->db->where('farmer.farmer_type_id>', 1);
        $this->db->where_in('dealers.area_id',$area_ids);
        $this->db->where('dealers.status',$this->config->item('system_status_active'));
        $this->db->order_by('dealers.ordering','ASC');
        $results=$this->db->get()->result_array();
        $dealers=array();
        foreach($results as $result)
        {
            $dealers[$result['area_id']][]=array(
                'dealer_name'=>$result['dealer_name'],
                'dealer_mobile_no'=>$result['mobile_no'],
            );
        }

        $items=array();
        foreach($areas as $area)
        {

            $number_of_lead_farmer=isset($lead_farmers[$area['id']])?sizeof($lead_farmers[$area['id']]):0;
            $number_of_dealer=isset($dealers[$area['id']])?sizeof($dealers[$area['id']]):0;
            $max_rows=max($number_of_lead_farmer,$number_of_dealer);
            $info=$this->initialize_row();
            $info['growing_area_name']=$area['name'];
            if($max_rows==0)
            {
                $info['dealer_name']='Not Found';
                $info['lead_farmer_name']='Not Found';
                $items[]=$info;
            }
            else
            {
                for($i=0; $i<$max_rows; $i++)
                {
                    $info['dealer_name']=isset($dealers[$area['id']][$i]['dealer_name'])?$dealers[$area['id']][$i]['dealer_name']:'';
                    $info['dealer_mobile_no']=isset($dealers[$area['id']][$i]['dealer_mobile_no'])?$dealers[$area['id']][$i]['dealer_mobile_no']:'';
                    $info['lead_farmer_name']=isset($lead_farmers[$area['id']][$i]['lead_farmer_name'])?$lead_farmers[$area['id']][$i]['lead_farmer_name']:'';
                    $info['lead_farmer_mobile_no']=isset($lead_farmers[$area['id']][$i]['lead_farmer_mobile_no'])?$lead_farmers[$area['id']][$i]['lead_farmer_mobile_no']:'';
                    $info['lead_farmer_created_date']=isset($lead_farmers[$area['id']][$i]['lead_farmer_created_date'])?$lead_farmers[$area['id']][$i]['lead_farmer_created_date']:'';
                    $items[]=$info;
                }
            }
        }
        $this->json_return($items);
    }
    private function initialize_row()
    {
        $row=$this->get_preference_headers('search');
        foreach($row  as $key=>$r)
        {
            $row[$key]='';
        }
        return $row;
    }


}
