<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_field_visit_feedback extends Root_Controller
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
        $this->lang->load('field_visit');
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->get_items();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
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
        else
        {
            $this->system_list($id);
        }
    }
    private function get_preference_headers()
    {
        $data['id']= 1;
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
        $data['feedback_require']= 1;
        $data['num_visit_done']= 1;
        $data['num_visit_done_feedback']= 1;
        $data['num_fruit_picture']= 1;
        $data['num_fruit_picture_feedback']= 1;
        $data['num_disease_picture']= 1;
        $data['num_disease_picture_feedback']= 1;
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Field Visit Feedback List";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers());
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
    private function get_items()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=40;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
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
        $this->db->select('count(distinct visits_picture.day_no) num_visit_done',true);
        $this->db->select('count(distinct case when visits_picture.user_feedback>0 then visits_picture.day_no end) num_visit_done_feedback',true);
        $this->db->join($this->config->item('table_ems_ft_field_visit_visits_picture').' visits_picture','setup_farmer.id =visits_picture.setup_id','LEFT');
        $this->db->select('count(distinct fruit_picture.picture_id) num_fruit_picture',false);
        $this->db->select('count(distinct case when fruit_picture.user_feedback>0 then fruit_picture.picture_id end) num_fruit_picture_feedback',false);
        $this->db->join($this->config->item('table_ems_ft_field_visit_fruit_picture').' fruit_picture','setup_farmer.id =fruit_picture.setup_id','LEFT');
        $this->db->select('count(distinct case when disease_picture.status="Active" then disease_picture.id end) num_disease_picture',false);
        $this->db->select('count(distinct case when disease_picture.status="Active" and disease_picture.user_feedback>0 then disease_picture.id end) num_disease_picture_feedback',false);
        $this->db->join($this->config->item('table_ems_ft_field_visit_disease_picture').' disease_picture','setup_farmer.id =disease_picture.setup_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('divisions.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zones.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('territories.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('districts.id',$this->locations['district_id']);
                        if($this->locations['upazilla_id']>0)
                        {
                            $this->db->where('upazillas.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $this->db->where('setup_farmer.status',$this->config->item('system_status_active'));
        $this->db->order_by('setup_farmer.id','DESC');
        $this->db->group_by('setup_farmer.id');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['farmer_name']=$item['name'];
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
            if(($item['num_visit_done']!=$item['num_visit_done_feedback'])||($item['num_fruit_picture']!=$item['num_fruit_picture_feedback'])||($item['num_disease_picture']!=$item['num_disease_picture_feedback']))
            {
                $item['feedback_require']='Yes';
            }
            else
            {
                $item['feedback_require']='No';
            }
        }
        $this->json_return($items);
    }
    private function system_edit($id)
    {
        if((isset($this->permissions['action1'])&&($this->permissions['action1']==1))||(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
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
                System_helper::invalid_try('Edit',$item_id,'Id Non-Exists in field_visit_setup_farmer_varieties');
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
            $this->db->select('crop_types.id type_id,crop_types.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
            $this->db->select('crops.id crop_id,crops.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
            $this->db->select('upazillas.name upazilla_name');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' upazillas','upazillas.id = setup_farmer.upazilla_id','INNER');
            $this->db->select('districts.name district_name,districts.id district_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
            $this->db->select('territories.name territory_name,territories.id territory_id');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('zones.name zone_name,zones.id zone_id');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('divisions.name division_name,divisions.id division_id');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->where('setup_farmer.id',$item_id);
            $this->db->where('setup_farmer.status',$this->config->item('system_status_active'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit',$item_id,'Id Non-Exists in field_visit_setup_farmer');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Edit',$item_id,'Edit others');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to give feedback on others field visit setup which area is not assigned to you';
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
            $data['title']="Field Visit Feedback";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!((isset($this->permissions['action2'])&&($this->permissions['action2']==1))||(isset($this->permissions['action1'])&&($this->permissions['action1']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        $visit_remarks=$this->input->post('visit_remarks');
        $fruit_remarks=$this->input->post('fruit_remarks');
        $diseases=$this->input->post('disease');
        $this->db->trans_start();
        if(sizeof($visit_remarks)>0)
        {
            foreach($visit_remarks as $day_no=>$variety)
            {
                foreach($variety as $variety_id=>$feedback)
                {
                    if((strlen($feedback))>0)
                    {
                        $data=array();
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_ems_ft_field_visit_visits_picture'),$data,array("setup_id = ".$id,'day_no ='.$day_no,'variety_id ='.$variety_id));
                    }
                }
            }
        }
        if(sizeof($fruit_remarks)>0)
        {
            foreach($visit_remarks as $picture_id=>$variety)
            {
                foreach($variety as $variety_id=>$feedback)
                {
                    if((strlen($feedback))>0)
                    {
                        $data=array();
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_ems_ft_field_visit_fruit_picture'),$data,array("setup_id = ".$id,'picture_id ='.$picture_id,'variety_id ='.$variety_id));
                    }
                }
            }
        }
        if((sizeof($diseases))>0)
        {
            foreach($diseases as $disease)
            {
                if((strlen($disease['feedback']))>0)
                {
                    $data=array();
                    $data['feedback']=$disease['feedback'];
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    $data['user_feedback'] = $user->user_id;
                    $data['date_feedback'] = $time;
                    Query_helper::update($this->config->item('table_ems_ft_field_visit_disease_picture'),$data,array("id = ".$disease['id']));
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
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
            $this->db->select('crop_types.id type_id,crop_types.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
            $this->db->select('crops.id crop_id,crops.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
            $this->db->select('upazillas.name upazilla_name');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' upazillas','upazillas.id = setup_farmer.upazilla_id','INNER');
            $this->db->select('districts.name district_name,districts.id district_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
            $this->db->select('territories.name territory_name,territories.id territory_id');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('zones.name zone_name,zones.id zone_id');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('divisions.name division_name,divisions.id division_id');
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
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Details',$item_id,'View others');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to view details of others field visit feedback which area is not assigned to you';
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_my_editable($item)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$item['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$item['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$item['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$item['district_id']))
        {
            return false;
        }
        if(($this->locations['upazilla_id']>0)&&($this->locations['upazilla_id']!=$item['upazilla_id']))
        {
            return false;
        }
        return true;
    }
    private function system_set_preference()
    {
        $method = 'list';
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
