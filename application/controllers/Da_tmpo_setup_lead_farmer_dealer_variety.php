<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Da_tmpo_setup_lead_farmer_dealer_variety extends Root_Controller
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
        $this->lang->load('daily_activities');
    }
    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="list_lead_farmer")
        {
            $this->system_list_lead_farmer($id);
        }
        elseif($action=="get_lead_farmers")
        {
            $this->system_get_lead_farmers($id);
        }
        elseif($action=='add_edit_lead_farmer')
        {
            $this->system_add_edit_lead_farmer($id,$id1);
        }
        elseif($action=="save_lead_farmer")
        {
            $this->system_save_lead_farmer();
        }
        elseif($action=="list_dealer")
        {
            $this->system_list_dealer($id);
        }
        elseif($action=="get_dealers")
        {
            $this->system_get_dealers($id);
        }
        elseif($action=='add_edit_dealer')
        {
            $this->system_add_edit_dealer($id,$id1);
        }
        elseif($action=="save_dealer")
        {
            $this->system_save_dealer();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety($id);
        }
        elseif($action=="get_varieties")
        {
            $this->system_get_varieties($id);
        }
        elseif($action=='add_edit_variety')
        {
            $this->system_add_edit_variety($id,$id1);
        }
        elseif($action=="save_variety")
        {
            $this->system_save_variety();
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
    private function get_preference_headers($method)
    {
        if($method=='list')
        {
            $data['id']= 1;
            $data['outlet']= 1;
            $data['area_name']= 1;
            $data['area_address']= 1;
            $data['division_name']= 1;
            $data['zone_name']= 1;
            $data['territory_name']= 1;
            $data['district_name']= 1;
            $data['number_of_lead_farmers']= 1;
            $data['number_of_dealers']= 1;
            $data['number_of_varieties']= 1;
        }
        else if($method=='list_lead_farmer')
        {
            $data['id']= 1;
            $data['name']= 1;
            $data['mobile_no']= 1;
            $data['address']= 1;
            $data['remarks']= 1;
            $data['ordering']= 1;
            $data['status']= 1;
        }
        else if($method=='list_dealer')
        {
            $data['id']= 1;
            $data['dealer_name']= 1;
            $data['mobile_no']= 1;
            $data['address']= 1;
            $data['remarks']= 1;
            $data['ordering']= 1;
            $data['status']= 1;
        }
        else if($method=='list_variety')
        {
            $data['id']= 1;
            $data['month']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['remarks']= 1;
            $data['ordering']= 1;
            $data['status']= 1;
        }
        else
        {
            $data=array();
        }

        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Growing Area List (For Lead Farmer, Dealer & Variety Setup)";
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
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->select('areas.id,areas.name area_name,areas.address area_address');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');
        $this->db->select('outlet_info.name outlet');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.area_id =areas.id AND lead_farmers.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->select('count(DISTINCT lead_farmers.id) number_of_lead_farmers',true);

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.area_id =areas.id AND dealers.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->select('count(DISTINCT dealers.id) number_of_dealers',true);

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties','varieties.area_id =areas.id AND dealers.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->select('count(DISTINCT varieties.id) number_of_varieties',true);

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
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->order_by('areas.outlet_id','ASC');
        $this->db->order_by('areas.ordering','ASC');
        $this->db->group_by('areas.id');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_list_lead_farmer($id)
    {
        $user = User_helper::get_user();
        $method = 'list_lead_farmer';
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
            $this->db->select('areas.id,areas.name area_name,areas.address area_address,areas.outlet_id');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
            $this->db->select('outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');
            $this->db->where('areas.status',$this->config->item('system_status_active'));
            $this->db->where('areas.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('list_lead_farmer',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Area_list',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Lead Farmer Setup List :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_lead_farmer",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_lead_farmer/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_lead_farmers()
    {
        $id=$this->input->post('id');
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers');
        $this->db->select('lead_farmers.*');
        $this->db->where('lead_farmers.area_id',$id);
        $this->db->where('lead_farmers.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('lead_farmers.ordering','ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add_edit_lead_farmer($area_id,$id='')
    {
        if($id>0)
        {
            $item_id=$id;
        }
        else
        {
            $item_id=$this->input->post('id');
        }

        if($item_id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');
        $this->db->select('outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id, d.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id, t.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id, division.name division_name');
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$area_id);

        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('add_edit_lead_farmer',$area_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('add_edit_lead_farmer',$area_id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if($item_id>0)
        {
            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers');
            $this->db->select('lead_farmers.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=lead_farmers.area_id','INNER');
            $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');
            $this->db->select('outlet_info.customer_id, outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');
            $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('lead_farmers.id',$item_id);
            $this->db->where('lead_farmers.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('add_edit_lead_farmer',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('add_edit_lead_farmer',$item_id,'User location not assign. outlet_id ('.$data['item']['outlet_id'].')');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['title']="Edit Lead Farmer (".$data['item']['name'].") :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
        }
        else
        {
            $data['item']=array
            (
                'id'=>'',
                'name'=>'',
                'mobile_no'=>'',
                'address'=>'',
                'remarks'=>'',
                'status'=>$this->config->item('system_status_active'),
                'ordering'=>99
            );
            $data['title']="Create Lead Farmer :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_lead_farmer",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_lead_farmer/'.$area_id.'/'.$item_id);
        $this->json_return($ajax);
    }
    private function system_save_lead_farmer()
    {
        $id = $this->input->post("id");
        $item=$this->input->post('item');
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers');
            $this->db->select('lead_farmers.*');
            $this->db->where('lead_farmers.id',$id);
            $this->db->where('lead_farmers.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Save',$id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation_lead_farmer())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id');
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$item['area_id']);
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('Save',$item['area_id'],'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$item['area_id'],'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            $item['user_updated'] = $user->user_id;
            $item['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers'),$item,array("id = ".$id));
        }
        else
        {
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $item['revision_count'] = 1;
            Query_helper::add($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers'),$item);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add_edit_lead_farmer($item['area_id']);
            }
            else
            {
                $this->system_list_lead_farmer($item['area_id']);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation_lead_farmer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('LABEL_STATUS'),'required');
        $this->form_validation->set_rules('item[ordering]',$this->lang->line('LABEL_ORDER'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    private function system_list_dealer($id)
    {
        $user = User_helper::get_user();
        $method = 'list_dealer';
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
            $this->db->select('areas.id,areas.name area_name,areas.address area_address,areas.outlet_id');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
            $this->db->select('outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');
            $this->db->where('areas.status',$this->config->item('system_status_active'));
            $this->db->where('areas.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('list_dealer',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Area_list',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Dealer Setup List :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_dealer",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_dealer/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_dealers()
    {
        $id=$this->input->post('id');
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
        $this->db->select('dealers.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','INNER');
        $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');
        $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
        $this->db->where('farmer.farmer_type_id>', 1);
        $this->db->where('dealers.area_id',$id);
        $this->db->where('dealers.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('dealers.ordering','ASC');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add_edit_dealer($area_id,$id='')
    {
        if($id>0)
        {
            $item_id=$id;
        }
        else
        {
            $item_id=$this->input->post('id');
        }

        if($item_id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id, d.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id, t.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id, division.name division_name');

        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$area_id);
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('add_edit_dealer',$area_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('add_edit_dealer',$area_id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if($item_id>0)
        {
            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
            $this->db->select('dealers.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=dealers.area_id','INNER');
            $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
            $this->db->select('outlet_info.customer_id, outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('dealers.id',$item_id);
            $this->db->where('dealers.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('add_edit_dealer',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('add_edit_dealer',$item_id,'User location not assign. outlet_id ('.$data['item']['outlet_id'].')');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->select('farmer_outlet.farmer_id value');
            $this->db->select('name text');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
            $this->db->where('farmer.status',$this->config->item('system_status_active'));
            $this->db->where('farmer.farmer_type_id>', 1);
            $this->db->where('farmer_outlet.revision',1);
            $this->db->where('farmer_outlet.outlet_id',$data['item_head']['outlet_id']);
            $this->db->order_by('farmer.id ASC');
            $data['dealers']=$this->db->get()->result_array();

            $data['title']="Edit Dealer Assign :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
        }
        else
        {
            $data['item']=array
            (
                'id'=>'',
                'dealer_id'=>'',
                'remarks'=>'',
                'status'=>$this->config->item('system_status_active'),
                'ordering'=>99
            );

            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->select('farmer_outlet.farmer_id value');
            $this->db->select('name text');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
            $this->db->where('farmer.status',$this->config->item('system_status_active'));
            $this->db->where('farmer.farmer_type_id>', 1);
            $this->db->where('farmer_outlet.revision',1);
            $this->db->where('farmer_outlet.outlet_id',$data['item_head']['outlet_id']);
            $this->db->where("farmer_outlet.farmer_id NOT IN (select dealer_id from ".$this->config->item('table_ems_da_tmpo_setup_area_dealers')." where status !='".$this->config->item('system_status_delete')."' AND area_id=".$area_id.")");
            $this->db->order_by('farmer.id ASC');
            $data['dealers']=$this->db->get()->result_array();

            $data['title']="Dealer Assign :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'];
        }


        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_dealer",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_dealer/'.$area_id.'/'.$item_id);
        $this->json_return($ajax);
    }
    private function system_save_dealer()
    {
        $id = $this->input->post("id");
        $item=$this->input->post('item');
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
            $this->db->select('dealers.*');
            $this->db->where('dealers.id',$id);
            $this->db->where('dealers.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Save',$id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_area_dealers'),'*',array('dealer_id='.$item['dealer_id'], 'area_id='.$item['area_id'], 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if($result)
            {
                $ajax['status']=false;
                $ajax['system_message']='You are already assign to dealer. (ID: '.$result['id'].' & status: '.$result['status'].')';
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation_dealer())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id');

        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$item['area_id']);
        $data['item_head']=$this->db->get()->row_array();

        if(!$data['item_head'])
        {
            System_helper::invalid_try('Save',$item['area_id'],'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$item['area_id'],'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            $item['user_updated'] = $user->user_id;
            $item['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_da_tmpo_setup_area_dealers'),$item,array("id = ".$id));
        }
        else
        {
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $item['revision_count'] = 1;
            Query_helper::add($this->config->item('table_ems_da_tmpo_setup_area_dealers'),$item);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add_edit_dealer($item['area_id']);
            }
            else
            {
                $this->system_list_dealer($item['area_id']);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation_dealer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[dealer_id]',$this->lang->line('LABEL_DEALER_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('LABEL_STATUS'),'required');
        $this->form_validation->set_rules('item[ordering]',$this->lang->line('LABEL_ORDER'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    private function system_list_variety($id)
    {
        $user = User_helper::get_user();
        $method = 'list_variety';
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
            $this->db->select('areas.id,areas.name area_name,areas.address area_address,areas.outlet_id');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
            $this->db->select('outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');
            $this->db->where('areas.status',$this->config->item('system_status_active'));
            $this->db->where('areas.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('list_variety',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Area_list',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Variety Setup List :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_variety/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_varieties()
    {
        $id=$this->input->post('id');
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
        $this->db->select('varieties.*');

        /*$this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = varieties.variety_id','INNER');
        $this->db->select('v.name variety_name, v.id variety_id');*/

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = varieties.crop_id','INNER');
        $this->db->select('crop.name crop_name, crop.id crop_id');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = varieties.crop_type_id','LEFT');
        $this->db->select('crop_type.name crop_type_name, crop_type.id crop_type_id');

        $this->db->where('varieties.area_id',$id);
        $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('varieties.ordering','ASC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if(!$item['crop_type_id'])
            {
                $item['crop_type_name']='All';
            }
            $item['month']=date("F", mktime(0, 0, 0,  $item['month'],1, 2000));
        }
        $this->json_return($items);
    }
    private function system_add_edit_variety($area_id,$id='')
    {
        if($id>0)
        {
            $item_id=$id;
        }
        else
        {
            $item_id=$this->input->post('id');
        }

        if($item_id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id, d.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id, t.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id, zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id, division.name division_name');

        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$area_id);
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('add_edit_variety',$area_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('add_edit_variety',$area_id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if($item_id>0)
        {
            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
            $this->db->select('varieties.*');

            /*$this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = varieties.variety_id','INNER');
            $this->db->select('v.name variety_name, v.id variety_id');*/

            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = varieties.crop_id','INNER');
            $this->db->select('crop.name crop_name, crop.id crop_id');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = varieties.crop_type_id','LEFT');
            $this->db->select('crop_type.name crop_type_name, crop_type.id crop_type_id');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=varieties.area_id','INNER');
            $this->db->select('areas.id area_id,areas.name area_name,areas.address area_address');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');
            $this->db->select('outlet_info.customer_id, outlet_info.name outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
            $this->db->select('d.id district_id, d.name district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.id territory_id, t.name territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.id zone_id, zone.name zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.id division_id, division.name division_name');

            $this->db->where('varieties.id',$item_id);
            $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('add_edit_variety',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('add_edit_variety',$item_id,'User location not assign. outlet_id ('.$data['item']['outlet_id'].')');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['types']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['item']['crop_id'],'status !="'.$this->config->item('system_status_delete').'"'));

            $data['title']="Edit Variety Assign :: Outlet: ".$data['item']['outlet_name'].", Growing Area: ".$data['item']['area_name'].", Address: ".$data['item']['area_address'];
        }
        else
        {
            $data['item']=array
            (
                'id'=>'',
                'month'=>date('n', time()),
                'crop_id'=>'',
                'crop_type_id'=>'',
                'remarks'=>'',
                'status'=>$this->config->item('system_status_active'),
                'ordering'=>99
            );

            $data['crops']=array();
            $data['types']=array();
            $data['varieties']=array();

            $data['title']="Variety Assign :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'];
        }


        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_variety",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_variety/'.$area_id.'/'.$item_id);
        $this->json_return($ajax);
    }
    private function system_save_variety()
    {
        $id = $this->input->post("id");
        $item=$this->input->post('item');
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
            $this->db->select('varieties.*');
            $this->db->where('varieties.id',$id);
            $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Save',$id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            /*$result=Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_area_varieties'),'*',array('variety_id='.$item['variety_id'], 'area_id='.$item['area_id'], 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if($result)
            {
                $ajax['status']=false;
                $ajax['system_message']='You are already assign to variety. (ID: '.$result['id'].' & status: '.$result['status'].')';
                $this->json_return($ajax);
            }*/
        }
        if(!$this->check_validation_variety())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_areas').' areas');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1 AND outlet_info.type = '.$this->config->item('system_customer_type_outlet_id'),'INNER');

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->select('d.id district_id');

        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->select('t.id territory_id');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->select('zone.id zone_id');

        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->select('division.id division_id');

        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where('areas.id',$item['area_id']);
        $data['item_head']=$this->db->get()->row_array();

        if(!$data['item_head'])
        {
            System_helper::invalid_try('Save',$item['area_id'],'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$item['area_id'],'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            $item['user_updated'] = $user->user_id;
            $item['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_da_tmpo_setup_area_varieties'),$item,array("id = ".$id));
        }
        else
        {
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $item['revision_count'] = 1;
            Query_helper::add($this->config->item('table_ems_da_tmpo_setup_area_varieties'),$item);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add_edit_variety($item['area_id']);
            }
            else
            {
                $this->system_list_variety($item['area_id']);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation_variety()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[month]',$this->lang->line('LABEL_MONTH'),'required');
        $this->form_validation->set_rules('item[crop_id]',$this->lang->line('LABEL_CROP_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('LABEL_STATUS'),'required');
        $this->form_validation->set_rules('item[ordering]',$this->lang->line('LABEL_ORDER'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method = 'list';
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
    private function check_my_editable($outlet)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$outlet['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$outlet['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$outlet['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$outlet['district_id']))
        {
            return false;
        }
        return true;
    }
}
