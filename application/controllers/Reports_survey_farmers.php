<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_survey_farmers extends Root_Controller
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
        $this->lang->load('survey_farmer');
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
            $data['title']="Farmer Base Line Survey Form 2020 Report";
            $ajax['status']=true;

            $data['districts']=Query_helper::get_info($this->config->item('table_ems_survey_farmers_districts'),array('id value','name text'),array(),0,0,array('name ASC'));

            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_upazilas'),array('id value','name text','district_id'),array(),0,0,array('name ASC'));
            $data['upazillas']=array();
            foreach($results as $result)
            {
                $data['upazillas'][$result['district_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_unions'),array('id value','name text','upazilla_id'),array(),0,0,array('name ASC'));
            $data['unions']=array();
            foreach($results as $result)
            {
                $data['unions'][$result['upazilla_id']][]=$result;
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
        $data['id']= 1;
        $data['sl_no']= 1;
        //$data['date_collection_data']= 1;
        $data['farmer_name']= 1;
        //$data['father_husband_name']= 1;
        $data['mobile_no']= 1;
        $data['district_name']= 1;
        $data['upazilla_name']= 1;
        //$data['village_name']= 1;
        $data['union_name']= 1;
        //$data['nid_no']= 1;
        //$data['growing_area']= 1;
        //$data['family_member_female']= 1;
        //$data['family_member_male']= 1;
        //$data['family_member_others']= 1;
        //$data['family_member_total']= 1;
        //$data['land_size_cultivable']= 1;
        //$data['land_size_residential']= 1;
        //$data['land_size_others']= 1;
        //$data['land_size_total']= 1;
        $data['cultivated_area']= 1;
        $data['cultivated_area_vegetables']= 1;
        $data['cultivated_area_others']= 1;
        //$data['last_year_cultivated_paddy']= 1;
        //$data['last_year_cultivated_jute']= 1;
        //$data['last_year_cultivated_wheat']= 1;
        //$data['last_year_cultivated_mustard']= 1;
        //$data['last_year_cultivated_maize']= 1;
        //$data['last_year_cultivated_others']= 1;
        //$data['cropping_intensity_single']= 1;
        //$data['cropping_intensity_double']= 1;
        //$data['cropping_intensity_triple']= 1;
        //$data['cropping_intensity_multiple']= 1;
        $data['have_vegetables_training']= 1;
        //$data['need_technical_knowledge_cultivation']= 1;
        //$data['technical_knowledge_vegetables_cultivation']= 1;
        //$data['technical_knowledge_quality_seeds']= 1;
        //$data['technical_knowledge_pest_management']= 1;
        //$data['technical_knowledge_others']= 1;
        $data['seeds_collect']= 1;
        $data['seeds_collect_dealers']= 1;
        $data['seeds_collect_retailers']= 1;
        $data['seeds_collect_leadfarmers']= 1;
        $data['seeds_collect_hatbazar']= 1;
        $data['seeds_collect_ownseeds']= 1;
        $data['seeds_collect_others']= 1;
        //$data['ensure_seed_quality_germination']= 1;
        //$data['ensure_seed_quality_faith']= 1;
        //$data['ensure_seed_quality_others']= 1;
        //$data['good_seed_purchase']= 1;
        $data['sell_vegetables']= 1;
        $data['sell_vegetables_to_artodar_paikar']= 1;
        $data['sell_vegetables_to_hatbazar']= 1;
        $data['sell_vegetables_in_group']= 1;
        $data['sell_vegetables_others']= 1;
        //$data['does_artodar_paikar_helps']= 1;
        //$data['does_artodar_paikar_helps_supplying_seeds']= 1;
        //$data['does_artodar_paikar_helps_credit_facilities']= 1;
        //$data['does_artodar_paikar_helps_others']= 1;
        //$data['does_artodar_paikar_helps_remarks']= 1;
        $data['do_know_arm']= 1;
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
            /*$reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_end']>0)
            {
                $reports['date_end']=$reports['date_end']+3600*24-1;
            }*/
            $data['options']=$reports;
            $data['title']="Farmer Base Line Survey Form 2020 Report";
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
        $district_id=$this->input->post('district_id');
        $upazilla_id=$this->input->post('upazilla_id');
        $union_id=$this->input->post('union_id');
        $items=array();
        $this->db->from($this->config->item('table_ems_survey_farmers').' item');
        $this->db->select('item.*');
        $this->db->select('IF(have_vegetables_training>0, "Yes", "No") have_vegetables_training');
        $this->db->select('IF(seeds_collect_dealers>0, "Yes", "") seeds_collect_dealers');
        $this->db->select('IF(seeds_collect_retailers>0, "Yes", "") seeds_collect_retailers');
        $this->db->select('IF(seeds_collect_leadfarmers>0, "Yes", "") seeds_collect_leadfarmers');
        $this->db->select('IF(seeds_collect_hatbazar>0, "Yes", "") seeds_collect_hatbazar');
        $this->db->select('IF(seeds_collect_ownseeds>0, "Yes", "") seeds_collect_ownseeds');
        $this->db->select('IF(seeds_collect_others>0, "Yes", "") seeds_collect_others');
        $this->db->select('IF(sell_vegetables_to_artodar_paikar>0, "Yes", "") sell_vegetables_to_artodar_paikar');
        $this->db->select('IF(sell_vegetables_to_hatbazar>0, "Yes", "") sell_vegetables_to_hatbazar');
        $this->db->select('IF(sell_vegetables_in_group>0, "Yes", "") sell_vegetables_in_group');
        $this->db->select('IF(sell_vegetables_others>0, "Yes", "") sell_vegetables_others');
        $this->db->join($this->config->item('table_ems_survey_farmers_districts').' districts','districts.id = item.district_id','LEFT');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_ems_survey_farmers_upazilas').' upazilas','upazilas.id = item.upazilla_id','LEFT');
        $this->db->select('upazilas.name upazilla_name');
        $this->db->join($this->config->item('table_ems_survey_farmers_unions').' unions','unions.id = item.union_id','LEFT');
        $this->db->select('unions.name union_name');
        /*$this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=item.user_created AND user_info.revision = 1','INNER');
        $this->db->select('user_info.name user_created');*/
        $this->db->where('item.status',$this->config->item('system_status_active'));
        /*if($user->user_group>2)
        {
            $this->db->where('item.user_created',$user->user_id);
        }*/
        if($district_id>0)
        {
            $this->db->where('districts.id',$district_id);
            if($upazilla_id>0)
            {
                $this->db->where('upazilas.id',$upazilla_id);
                if($union_id>0)
                {
                    $this->db->where('unions.id',$union_id);
                }
            }
        }
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_created']=System_helper::display_date_time($item['date_created']);
            $item['cultivated_area_vegetables']=$item['cultivated_area_vegetables']?$item['cultivated_area_vegetables']:'';
            $item['cultivated_area_others']=$item['cultivated_area_others']?$item['cultivated_area_others']:'';
        }
        $this->json_return($items);
    }

    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();
            $user=User_helper::get_user();
            $this->db->from($this->config->item('table_ems_survey_farmers') . ' survey_farmer');
            $this->db->select('survey_farmer.*');

            $this->db->join($this->config->item('table_ems_survey_farmers_unions') . ' union', 'union.id = survey_farmer.union_id', 'LEFT');
            $this->db->select('union.bn_name union_name');

            $this->db->join($this->config->item('table_ems_survey_farmers_upazilas') . ' upazilla', 'upazilla.id = survey_farmer.upazilla_id', 'LEFT');
            $this->db->select('upazilla.bn_name upazilla_name');

            $this->db->join($this->config->item('table_ems_survey_farmers_districts') . ' district', 'district.id = survey_farmer.district_id', 'LEFT');
            $this->db->select('district.bn_name district_name');

            $this->db->where('survey_farmer.id', $item_id);
            $this->db->where('survey_farmer.status !=', $this->config->item('system_status_delete'));
            /*if($user->user_group>2)
            {
                $this->db->where('survey_farmer.user_created',$user->user_id);
            }*/
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                //System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }
            // Details Table data
            $data['items'] = Query_helper::get_info($this->config->item('table_ems_survey_farmers_details'), array('*'), array("survey_id=" . $item_id,'status ="'.$this->config->item('system_status_active').'"'));

            $this->db->from($this->config->item('table_login_setup_user_info').' user_info');
            $this->db->select('user_info.name user_name, user_info.mobile_no');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','left');
            $this->db->select('designation.name designation_name');
            $this->db->where('user_info.revision',1);
            $this->db->where('user_info.user_id',$data['item']['user_created']);
            //$this->db->where('user_info.status',$this->config->item('system_status_active'));
            $user_info=$this->db->get()->row();

            $data['user_info']['designation']=$user_info->designation_name;
            $data['user_info']['name']=$user_info->user_name;
            $data['user_info']['mobile_no']=$user_info->mobile_no;

            $data['title'] = "Farmer based Survey Details - 2020";
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
