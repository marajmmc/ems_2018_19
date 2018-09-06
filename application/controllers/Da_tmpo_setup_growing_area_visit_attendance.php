<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Da_tmpo_setup_growing_area_visit_attendance extends Root_Controller
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
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="list_all")
        {
            $this->system_list_all();
        }
        elseif($action=="get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=='attendance')
        {
            $this->system_attendance($id);
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
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference('list_all');
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
            $data['date_visit']= 1;
            $data['area_name']= 1;
            $data['area_address']= 1;
            $data['division_name']= 1;
            $data['zone_name']= 1;
            $data['territory_name']= 1;
            $data['district_name']= 1;
        }
        else
        {
            $data['id']= 1;
            $data['outlet']= 1;
            $data['date_visit']= 1;
            $data['area_name']= 1;
            $data['area_address']= 1;
            $data['division_name']= 1;
            $data['zone_name']= 1;
            $data['territory_name']= 1;
            $data['district_name']= 1;
            $data['status_attendance']= 1;
        }

        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Growing Area Visit List";
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
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
        $this->db->select('areas.name area_name,areas.address area_address,areas.address status_visit_area');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');
        $this->db->select('outlet_info.name outlet');

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
        $this->db->where('visit.status_attendance',$this->config->item('system_status_pending'));
        $this->db->order_by('areas.outlet_id','ASC');
        $this->db->order_by('areas.ordering','ASC');
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as &$item)
        {
            $item['id']=$item['id'];
            $item['date_visit']=System_helper::display_date($item['date_visit']);
            $items[]=$item;
        }

        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method = 'list_all';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="All Growing Area Visit List.";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list_all/");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=100;
        }
        else
        {
            $pagesize=$pagesize*2;
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
        $this->db->select('areas.name area_name,areas.address area_address,areas.address status_visit_area');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=areas.outlet_id AND outlet_info.revision=1','INNER');
        $this->db->select('outlet_info.name outlet');

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
        $this->db->order_by('areas.outlet_id','ASC');
        $this->db->order_by('areas.ordering','ASC');
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as &$item)
        {
            $item['date_visit']=System_helper::display_date($item['date_visit']);
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_attendance($id)
    {
        if($id>0)
        {
            $item_id=$id;
        }
        else
        {
            $item_id=$this->input->post('id');
        }

        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || !(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        /*get area information*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
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
        $this->db->where('areas.status !=',$this->config->item('system_status_delete'));
        $this->db->where('visit.id',$item_id);
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('Save',$item_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$item_id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($data['item_head']['status_attendance']!=$this->config->item('system_status_pending'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Attendance submitted. Try to another visiting area.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
        $this->db->select('details.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.id = details.dealer_id','LEFT');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','LEFT');
        $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.id = details.farmer_id','LEFT');
        $this->db->select('lead_farmers.name lead_farmers_name');

        /*$this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
        $this->db->where('farmer.farmer_type_id>', 1);*/

        $this->db->where('details.visit_id',$data['item_head']['id']);
        $this->db->where('details.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();

        $data['dealers']=array();
        $data['farmers']=array();
        foreach($results as $result)
        {
            if($result['dealer_id'])
            {
                $data['dealers'][]=$result;
            }
            if($result['farmer_id'])
            {
                $data['farmers'][]=$result;
            }
        }

        /*get previous visit details information*/
        $week_number = date('W', $data['item_head']['date_visit']);
        $week_odd_even=($week_number%2);

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');
        $this->db->where('visit.area_id',$data['item_head']['area_id']);
        $this->db->where('visit.week_odd_even',$week_odd_even);
        $this->db->where('visit.date_visit !=',$data['item_head']['date_visit']);
        $this->db->where('visit.date_visit < ',$data['item_head']['date_visit']);
        $this->db->where('visit.status',$this->config->item('system_status_active'));
        $this->db->order_by('visit.id', 'DESC');
        $this->db->limit(1);
        $result=$this->db->get()->row_array();
        $result_area_id=0;
        $data['date_visit_previous']=0;
        if($result)
        {
            $result_area_id=$result['id'];
            $data['date_visit_previous']=$result['date_visit'];
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
        $this->db->select('details.*');
        $this->db->where('details.visit_id',$result_area_id);
        $this->db->where('details.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $data['previous_dealers']=array();
        $data['previous_farmers']=array();
        foreach($results as $result)
        {
            if($result['dealer_id'])
            {
                $data['previous_dealers'][$result['dealer_id']]=$result;
            }
            if($result['farmer_id'])
            {
                $data['previous_farmers'][$result['farmer_id']]=$result;
            }
        }

        /*get setup variety items*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
        $this->db->select('varieties.*');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = varieties.crop_id','INNER');
        $this->db->select('crop.name crop_name, crop.id crop_id');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = varieties.crop_type_id','LEFT');
        $this->db->select('crop_type.name crop_type_name, crop_type.id crop_type_id');

        $this->db->where('varieties.area_id',$data['item_head']['area_id']);
        $this->db->where('varieties.month',date('n',$data['item_head']['date_visit']));
        $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('varieties.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();

        $date_visit_title='';
        if($data['date_visit_previous'])
        {
            $date_visit_title=" || <span class='text-danger'>Previous visit date: ".System_helper::display_date($data['date_visit_previous'])."</span>";
        }
        $data['title']="Growing Area Visit :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'].", <span class='text-danger'>Date: ".System_helper::display_date($data['item_head']['date_visit']).'</span> '.$date_visit_title;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/attendance",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/attendance/'.$item_id);
        $this->json_return($ajax);
    }
    private function system_save()
    {

        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');

        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || !(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$item_head['status_attendance'])
        {
            $ajax['status']=false;
            $ajax['system_message']='Attendance field is required';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
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
        $this->db->where('areas.status !=',$this->config->item('system_status_delete'));
        $this->db->where('visit.id',$id);
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('Save',$id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($data['item_head']['status_attendance']!=$this->config->item('system_status_pending'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Attendance submitted. Try to another visiting area.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $item_head['user_attendance'] = $user->user_id;
        $item_head['date_attendance'] = $time;
        Query_helper::update($this->config->item('table_ems_da_tmpo_setup_growing_area_visit'),$item_head, array('id='.$id));

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
            /*get area information*/
            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
            $this->db->select('visit.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
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
            $this->db->where('areas.status !=',$this->config->item('system_status_delete'));
            $this->db->where('visit.id',$item_id);
            $data['item_head']=$this->db->get()->row_array();
            if(!$data['item_head'])
            {
                System_helper::invalid_try('Save',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item_head']))
            {
                System_helper::invalid_try('Save',$item_id,'User location not assign');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item_head']['user_created']]=$data['item_head']['user_created'];
            $user_ids[$data['item_head']['user_updated']]=$data['item_head']['user_updated'];
            $user_ids[$data['item_head']['user_attendance']]=$data['item_head']['user_attendance'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.id = details.dealer_id','LEFT');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','LEFT');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.id = details.farmer_id','LEFT');
            $this->db->select('lead_farmers.name lead_farmers_name');

            /*$this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id>', 1);*/

            $this->db->where('details.visit_id',$data['item_head']['id']);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();
            //echo $this->db->last_query();

            $data['dealers']=array();
            $data['farmers']=array();
            foreach($results as $result)
            {
                if($result['dealer_id'])
                {
                    $data['dealers'][]=$result;
                }
                if($result['farmer_id'])
                {
                    $data['farmers'][]=$result;
                }
            }

            /*get previous visit details information*/
            $week_number = date('W', $data['item_head']['date_visit']);
            $week_odd_even=($week_number%2);

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
            $this->db->select('visit.*');
            $this->db->where('visit.area_id',$data['item_head']['area_id']);
            $this->db->where('visit.week_odd_even',$week_odd_even);
            $this->db->where('visit.date_visit !=',$data['item_head']['date_visit']);
            $this->db->where('visit.date_visit < ',$data['item_head']['date_visit']);
            $this->db->where('visit.status',$this->config->item('system_status_active'));
            $this->db->order_by('visit.id', 'DESC');
            $this->db->limit(1);
            $result=$this->db->get()->row_array();
            $result_area_id=0;
            $data['date_visit_previous']=0;
            if($result)
            {
                $result_area_id=$result['id'];
                $data['date_visit_previous']=$result['date_visit'];
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');
            $this->db->where('details.visit_id',$result_area_id);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();
            $data['previous_dealers']=array();
            $data['previous_farmers']=array();
            foreach($results as $result)
            {
                if($result['dealer_id'])
                {
                    $data['previous_dealers'][$result['dealer_id']]=$result;
                }
                if($result['farmer_id'])
                {
                    $data['previous_farmers'][$result['farmer_id']]=$result;
                }
            }

            /*get setup variety items*/
            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
            $this->db->select('varieties.*');

            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = varieties.crop_id','INNER');
            $this->db->select('crop.name crop_name, crop.id crop_id');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = varieties.crop_type_id','LEFT');
            $this->db->select('crop_type.name crop_type_name, crop_type.id crop_type_id');

            $this->db->where('varieties.area_id',$data['item_head']['area_id']);
            $this->db->where('varieties.month',date('n',$data['item_head']['date_visit']));
            $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
            $this->db->order_by('varieties.ordering','ASC');
            $data['varieties']=$this->db->get()->result_array();

            $date_visit_title='';
            if($data['date_visit_previous'])
            {
                $date_visit_title=" || <span class='text-danger'>Previous visit date: ".System_helper::display_date($data['date_visit_previous'])."</span>";
            }
            $data['title']="Growing Area Visit :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'].", <span class='text-danger'>Date: ".System_helper::display_date($data['item_head']['date_visit']).'</span> '.$date_visit_title;
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
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            if($method=='list_all')
            {
                $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_all');
            }
            else
            {
                $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference');
            }
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
