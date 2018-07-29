<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_field_visit_setup_farmer extends Root_Controller
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
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "list_all")
        {
            $this->system_list_all();
        }
        elseif ($action == "get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_unfilled")
        {
            $this->system_save_unfilled();
        }
        elseif($action=="edit_status")
        {
            $this->system_edit_status();
        }
        elseif($action=="edit_status_complete")
        {
            $this->system_edit_status_complete();
        }
        elseif($action=="delete")
        {
            $this->system_delete($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif ($action == "set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method = 'list')
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
        $data['status']= 1;
        if ($method == 'list_all')
        {
            $data['status_complete']= 1;
        }
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Farmer and Field Visit Setup Incomplete List";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
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
    private function system_get_items()
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
        $this->db->where('setup_farmer.status !=',$this->config->item('system_status_delete'));
        $this->db->where('setup_farmer.status_complete',$this->config->item('system_status_no'));
        $this->db->order_by('id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['farmer_name']=$item['name'];
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method = 'list_all';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Farmer and Field Visit Setup All List";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/list_all");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function system_get_items_all()
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
        $this->db->where('setup_farmer.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['farmer_name']=$item['name'];
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }
        $this->json_return($items);
    }
    private function system_list_variety()
    {
        $crop_type_id=$this->input->post('crop_type_id');
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.whose');
        $this->db->where('v.crop_type_id',$crop_type_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_add()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="New Farmer and Field Visit Setup";
            $data["item"] = Array(
                'id'=>0,
                'year' => date('Y'),
                'season_id' => '',
                'division_id' => '',
                'zone_id' => '',
                'territory_id' => '',
                'district_id' => '',
                'upazilla_id'=>'',
                'crop_id'=>'',
                'type_id'=>'',
                'name'=>'',
                'address' => '',
                'contact_no' => '',
                'date_sowing' => time(),
                'date_transplant' => '',
                'num_visits' => 10,
                'interval' => 10

            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['upazillas']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                        if($this->locations['district_id']>0)
                        {
                            $data['upazillas']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                        }
                    }
                }
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['types']=array();
            $data['varieties']=array();
            $data['seasons']=Query_helper::get_info($this->config->item('table_ems_setup_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
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
    private function system_edit($id)
    {
        if((isset($this->permissions['action2'])&&($this->permissions['action2']==1))||(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
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
            $results=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer_varieties'),'*',array('setup_id ='.$item_id,'revision =1'));
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
            $this->db->where('setup_farmer.status !=',$this->config->item('system_status_delete'));
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
                $ajax['system_message']='You are trying to edit others field visit setup which area is not assigned to you.';
                $this->json_return($ajax);
            }
            $data['seasons']=Query_helper::get_info($this->config->item('table_ems_setup_seasons'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['types']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['item']['crop_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['varieties']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['item']['type_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('whose ASC','ordering ASC'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['item']['division_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['item']['zone_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['item']['territory_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['upazillas']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$data['item']['district_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['title']="Edit Farmer and Field Visit Setup";
            $ajax['status']=true;
            if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            }
            elseif(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_unfilled",$data,true));
            }
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
        $item=$this->input->post('item');
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }

        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        /*--Start-- Valid Upazilla Id checking*/
        $this->db->from($this->config->item('table_login_setup_location_upazillas').' upazillas');
        $this->db->select('upazillas.id');
        $this->db->select('districts.id district_id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
        $this->db->select('territories.id territory_id');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('zones.id zone_id');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('divisions.id division_id');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->where('upazillas.id',$item['upazilla_id']);
        $this->db->where('upazillas.status !=',$this->config->item('system_status_delete'));
        $result=$this->db->get()->row_array();
        if(!$result)
        {
            System_helper::invalid_try('Save',$item['upazilla_id'],'Upazilla Id not found');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try';
            $this->json_return($ajax);
        }
        /*--End-- Valid Upazilla Id checking*/

        if(!$this->check_my_editable($result))
        {
            System_helper::invalid_try('Save',$item['upazilla_id'],'Save others');
            $ajax['status']=false;
            $ajax['system_message']='You are trying to save field visit setup for an area which is not assigned to you';
            $this->json_return($ajax);
        }
        else
        {
            $item['date_sowing']=System_helper::get_time($item['date_sowing']);
            $item['date_transplant']=System_helper::get_time($item['date_transplant']);
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = $time;
                Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer'),$item,array("id = ".$id));
            }
            else
            {
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                $setup_id=Query_helper::add($this->config->item('table_ems_ft_field_visit_setup_farmer'),$item);
                if($setup_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                    die();
                }
                else
                {
                    $id=$setup_id;
                }
            }
            $data=array();
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            $this->db->set('revision', 'revision+1', FALSE);
            Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer_varieties'),$data,array('setup_id='.$id),false);
            $variety_ids=$this->input->post('variety_ids');
            foreach($variety_ids as $variety_id)
            {
                $data=array();
                $data['setup_id']=$id;
                $data['variety_id']=$variety_id;
                $data['revision']=1;
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                Query_helper::add($this->config->item('table_ems_ft_field_visit_setup_farmer_varieties'),$data,false);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_save_unfilled()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item=$this->input->post('item');
        if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        $item['date_transplant']=System_helper::get_time($item['date_transplant']);
        $this->db->trans_start();  //DB Transaction Handle START
        $item['user_updated'] = $user->user_id;
        $item['date_updated'] = $time;
        Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer'),$item,array("id = ".$id));
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }

    }
    private function system_edit_status()
    {
        if(isset($this->permissions['action3'])&&($this->permissions['action3']==1))
        {
            $user = User_helper::get_user();
            $time=time();
            $id=$this->input->post('id');
            $result=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer'),'*',array('id ='.$id),1);
            if($result['status_complete']==$this->config->item('system_status_yes'))
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not change status of completed setup.';
                $this->json_return($ajax);
            }
            $status=$this->config->item('system_status_active');
            if($result['status']==$this->config->item('system_status_active'))
            {
                $status=$this->config->item('system_status_inactive');
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['status'] = $status;
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer'),$data,array("id = ".$id));
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message='Status Changed to '.$status;
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_status_complete()
    {
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            $user = User_helper::get_user();
            $time=time();
            $id=$this->input->post('id');
            $result=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer'),'*',array('id ='.$id),1);
            $status=$this->config->item('system_status_no');
            if($result['status_complete']==$this->config->item('system_status_no'))
            {
                $status=$this->config->item('system_status_yes');
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['status_complete'] = $status;
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer'),$data,array("id = ".$id));
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message='Status Changed to '.$status;
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_delete($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $user = User_helper::get_user();
            $time = time();
            $item=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item)
            {
                System_helper::invalid_try('Delete',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_ems_ft_field_visit_setup_farmer'),$data,array('id='.$item_id));
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status()===true)
            {
                $this->message=$this->lang->line("MSG_DELETED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_SAVED_FAIL');
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
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
            $results=Query_helper::get_info($this->config->item('table_ems_ft_field_visit_setup_farmer_varieties'),'*',array('setup_id ='.$item_id,'revision =1'));
            if(!$results)
            {
                System_helper::invalid_try('Details',$item_id,'Id Not Exists in field_visit_setup_farmer_varieties');
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
            $this->db->select('crops.name crop_name');
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
            $this->db->where('setup_farmer.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Details',$item_id,'Id Not Exists in field_visit_setup_farmer');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Details',$item_id,'View others');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to view details of others field visit setup which area is not assigned to you';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created']]=$data['item']['user_created'];
            $user_ids[$data['item']['user_updated']]=$data['item']['user_updated'];
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']="Details:: Farmer and Field Visit Setup";
            $data['varieties']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['item']['type_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('whose ASC','ordering ASC'));
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
        return true;
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[year]',$this->lang->line('LABEL_YEAR'),'required|numeric');
        $this->form_validation->set_rules('item[season_id]',$this->lang->line('LABEL_SEASON'),'required|numeric');
        $this->form_validation->set_rules('crop_id',$this->lang->line('LABEL_CROP_NAME'),'required');
        $this->form_validation->set_rules('crop_type_id',$this->lang->line('LABEL_CROP_TYPE_NAME'),'required');
        $this->form_validation->set_rules('item[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|numeric');
        $this->form_validation->set_rules('item[name]',"Farmer's Name",'required');
        $this->form_validation->set_rules('item[address]',$this->lang->line('LABEL_ADDRESS'),'required');
        $this->form_validation->set_rules('item[date_sowing]',$this->lang->line('LABEL_DATE_SOWING'),'required');
        $this->form_validation->set_rules('item[interval]',$this->lang->line('LABEL_INTERVAL'),'required|numeric');
        $this->form_validation->set_rules('item[num_visits]',$this->lang->line('LABEL_NUM_VISITS'),'required|numeric');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $variety_ids=$this->input->post('variety_ids');
        if(!((sizeof($variety_ids)>0)))
        {
            $this->message="Please Select at least One Variety";
            return false;
        }

        return true;
    }
    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
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
