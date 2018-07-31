<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_field_visit extends Root_Controller
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
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        elseif($action=="list")
        {
            $this->system_list();
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
            $data['title']="Search";
            $ajax['status']=true;
            $data['years']=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer'),array('Distinct(year)'),array());
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['zones']=array();
            $data['territories']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                }
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url."/search", $data, true));
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
    private function get_preference_headers()
    {
        $data['sl_no']= 1;
        $data['farmer_name']= 1;
        $data['year']= 1;
        $data['season']= 1;
        $data['upazilla_name']= 1;
        $data['district_name']= 1;
        $data['territory_name']= 1;
        $data['zone_name']= 1;
        $data['division_name']= 1;
        $data['contact_no']= 1;
        $data['date_sowing']= 1;
        $data['num_visits']= 1;
        $data['interval']= 1;
        $data['num_visit_done']= 1;
        $data['details_button']= 1;
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'search';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_end']>0)
            {
                $reports['date_end']=$reports['date_end']+3600*24-1;
            }
            $data['options']=$reports;
            $data['title']="Field Visit Report";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers());
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_ems_ft_field_visit_setup_farmer').' setup_farmer');
        $this->db->select('setup_farmer.*');
        $this->db->select('upazillas.name upazilla_name');
        $this->db->join($this->config->item('table_login_setup_location_upazillas').' upazillas','upazillas.id = setup_farmer.upazilla_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('seasons.name season');
        $this->db->join($this->config->item('table_ems_setup_seasons').' seasons','seasons.id =setup_farmer.season_id','INNER');
        $this->db->select('count(distinct case when visits_picture.date_created>='.$date_start.' and visits_picture.date_created<='.$date_end.' then visits_picture.day_no end) num_visit_done',false);
        $this->db->join($this->config->item('table_ems_ft_field_visit_visits_picture').' visits_picture','setup_farmer.id =visits_picture.setup_id','LEFT');
        if($division_id>0)
        {
            $this->db->where('divisions.id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('territories.id',$territory_id);
                }
            }
        }
        if($date_end>0)
        {
            $this->db->where('setup_farmer.date_created <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('setup_farmer.date_created >=',$date_start);
        }
        $this->db->where('setup_farmer.status',$this->config->item('system_status_active'));
        $this->db->order_by('setup_farmer.id','DESC');
        $this->db->group_by('setup_farmer.id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['farmer_name']=$item['name'];
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }
        $this->json_return($items);
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['previous_varieties']=array();
            $this->db->from($this->config->item('table_ems_ft_field_visit_setup_farmer_varieties').' farmer_varieties');
            $this->db->select('farmer_varieties.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =farmer_varieties.variety_id','INNER');
            $this->db->where('farmer_varieties.setup_id',$item_id);
            $this->db->where('farmer_varieties.revision',1);
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            if(!$results)
            {
                System_helper::invalid_try('Details',$item_id,'Id Non-Exists in field_visit_setup_farmer_varieties');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
            }
            $variety_id=0;
            foreach($results as $key=>$result)
            {
                if($key==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_ems_ft_field_visit_setup_farmer').' setup_farmer');
            $this->db->select('setup_farmer.*');
            $this->db->select('seasons.name season');
            $this->db->join($this->config->item('table_ems_setup_seasons').' seasons','seasons.id =setup_farmer.season_id','INNER');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->select('crop_types.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
            $this->db->select('crops.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
            $this->db->select('upazillas.name upazilla_name');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' upazillas','upazillas.id = setup_farmer.upazilla_id','INNER');
            $this->db->select('districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
            $this->db->select('territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('divisions.name division_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->where('setup_farmer.id',$item_id);
            $this->db->where('setup_farmer.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Details',$item_id,'Id Non-Exists in field_visit_setup_farmer');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_visits_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_fruit_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_disease_picture'),'*',array('setup_id ='.$item_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('id'));
            $data['users']=System_helper::get_users_info(array());
            $data['title']="Details:: Field Visit";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
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
    private function system_set_preference()
    {
        $method = 'search';
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers());
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
}
