<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Da_tmpo_setup_growing_area_visit extends Root_Controller
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
    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="list_previous")
        {
            $this->system_list_previous($id);
        }
        elseif($action=="get_items_previous")
        {
            $this->system_get_items_previous();
        }
        elseif($action=='add_edit')
        {
            $this->system_add_edit($id,$id1);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_previous")
        {
            $this->system_set_preference('list_previous');
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
            $data['status_visit_area']= 1;
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
        }

        return $data;
    }
    private function system_list($date)
    {
        if(System_helper::get_time($date)>0)
        {
            $date_visit=System_helper::get_time($date);
        }
        else
        {
            if(System_helper::get_time(System_helper::display_date($date))>0)
            {
                $date_visit=System_helper::get_time(System_helper::display_date($date));
            }
            else
            {
                $date_visit=System_helper::get_time(System_helper::display_date(time()));
            }
        }

        $reports['date_visit']=$date_visit;

        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['options']=$reports;
            $data['title']="Growing Area List. <span class='text-danger'>Date: ".System_helper::display_date($date_visit).'</span>';
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list/".$date_visit);
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
        $date_visit=$this->input->post('date_visit');
        $week_number = date('W', $date_visit);
        $week_odd_even=($week_number%2);
        $day_of_week = date('N', $date_visit)+3;
        $day_key=($day_of_week%7);

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_visit_schedules').' schedules');
        if($week_odd_even)
        {
            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=schedules.area_id_even','INNER');
        }
        else
        {
            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=schedules.area_id_odd','INNER');
        }
        $this->db->select('areas.id,areas.name area_name,areas.address area_address,areas.address status_visit_area');

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

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit','visit.area_id = areas.id AND visit.date_visit='.$date_visit,'LEFT');
        $this->db->select("IF(COUNT(visit.id)>0, 'YES', 'NO') status_visit_area");

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
        $this->db->where('schedules.ordering',$day_key);
        $this->db->order_by('areas.outlet_id','ASC');
        $this->db->order_by('areas.ordering','ASC');
        $this->db->group_by('areas.id');
        $items=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $this->json_return($items);
    }
    private function system_list_previous()
    {
        $user = User_helper::get_user();
        $method = 'list_previous';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Previous Growing Area Visit List.";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_previous",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list_previous/");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_get_items_previous()
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
        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->order_by('areas.outlet_id','ASC');
        $this->db->order_by('areas.ordering','ASC');
        //$this->db->group_by('areas.id');
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
    private function system_add_edit($date_visit,$id)
    {
        if($id>0)
        {
            $area_id=$id;
        }
        else
        {
            $area_id=$this->input->post('id');
        }

        if(!System_helper::get_time(System_helper::display_date($date_visit)))
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid Visit Date.';
            $this->json_return($ajax);
        }
        else
        {
            $date_visit=System_helper::get_time(System_helper::display_date($date_visit));
            $week_number = date('W', $date_visit);
            $week_odd_even=($week_number%2);
        }

        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || !(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if(!(isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            $current_date=System_helper::get_time(System_helper::display_date(time()));
            if($current_date!=$date_visit)
            {
                $ajax['status']=false;
                $ajax['system_message']="You can't update record in date: (".System_helper::display_date($date_visit).")";
                $this->json_return($ajax);
            }
        }

        /*get area information*/
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
            System_helper::invalid_try('Save',$area_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        if(!$this->check_my_editable($data['item_head']))
        {
            System_helper::invalid_try('Save',$area_id,'User location not assign');
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        /*get setup variety items*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_varieties').' varieties');
        $this->db->select('varieties.*');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = varieties.crop_id','INNER');
        $this->db->select('crop.name crop_name, crop.id crop_id');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = varieties.crop_type_id','LEFT');
        $this->db->select('crop_type.name crop_type_name, crop_type.id crop_type_id');

        $this->db->where('varieties.area_id',$area_id);
        $this->db->where('varieties.month',date('n',$date_visit));
        $this->db->where('varieties.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('varieties.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();

        /*get previous visit information*/
        //$data['previous_visit']=Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_growing_area_visit'),'*',array('week_odd_even='.$week_odd_even),1,0,array('id DESC'));
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');
        $this->db->where('visit.area_id',$area_id);
        $this->db->where('visit.week_odd_even',$week_odd_even);
        $this->db->where('visit.date_visit !=',$date_visit);
        $this->db->where('visit.date_visit < ',$date_visit);
        $this->db->where('visit.status',$this->config->item('system_status_active'));
        $this->db->order_by('visit.id', 'DESC');
        $this->db->limit(1);
        $result=$this->db->get()->row_array();
        $result_area_id=0;
        $date_visit_previous=0;
        if($result)
        {
            $result_area_id=$result['id'];
            $date_visit_previous=$result['date_visit'];
        }

        /*get previous visit details information*/
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

        /*get visit information*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->where('visit.area_id',$area_id);
        $this->db->where('visit.date_visit',$date_visit);
        $this->db->where('visit.status',$this->config->item('system_status_active'));
        $result=$this->db->get()->row_array();


        if($result)
        {
            $data['item']=array
            (
                'area_id'=>$area_id,
                'date_visit'=>$date_visit,
                'other_info'=>$result['other_info'],
                'remarks'=>$result['remarks']
            );

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.id = details.dealer_id','LEFT');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','LEFT');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.id = details.farmer_id','LEFT');
            $this->db->select('lead_farmers.name lead_farmers_name');

            /*$this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id>', 1);*/

            $this->db->where('details.visit_id',$result['id']);
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
        }
        else
        {
            $data['item']=array
            (
                'area_id'=>$area_id,
                'date_visit'=>$date_visit,
                'other_info'=>'',
                'remarks'=>''
            );

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
            $this->db->select('dealers.*, dealers.id dealer_id');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','INNER');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');
            $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id>', 1);
            $this->db->where('dealers.area_id',$area_id);
            $this->db->where('dealers.status',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();
            $data['dealers']=array();
            foreach($results as &$result)
            {
                $result['description']='';
                $result['image_location']='';
                $result['image_name']='';
                $data['dealers'][]=$result;
            }

            $results=Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' farmers',array('farmers.*, farmers.id farmer_id, farmers.name lead_farmers_name'),array('area_id='.$area_id, 'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['farmers']=array();
            foreach($results as &$result)
            {
                $result['description']='';
                $result['image_location']='';
                $result['image_name']='';
                $data['farmers'][]=$result;
            }

        }

        $data['title']="Growing Area Visit :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'].", <span class='text-danger'>Date: ".System_helper::display_date($date_visit).'</span>';
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit/'.$date_visit.'/'.$area_id);
        $this->json_return($ajax);
    }
    private function system_save()
    {
        $item=$this->input->post('item');
        $area_id = $item["area_id"];
        $date_visit = $item["date_visit"];
        $dealer_items=$this->input->post('dealer_items');
        $farmer_items=$this->input->post('farmer_items');
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)) || !(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!System_helper::get_time(System_helper::display_date($date_visit)))
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid Visit Date.';
            $this->json_return($ajax);
        }
        else
        {
            $date_visit=System_helper::get_time(System_helper::display_date($date_visit));
            $week_number = date('W', $date_visit);
            $week_odd_even=($week_number%2);
        }
        if(!(isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            $current_date=System_helper::get_time(System_helper::display_date(time()));
            if($current_date!=$date_visit)
            {
                $ajax['status']=false;
                $ajax['system_message']="You can't update record in date: (".System_helper::display_date($date_visit).")";
                $this->json_return($ajax);
            }
        }

        //$path=site_url($this->controller_url.'/images/growing_area_visit/');
        $path='images/growing_area_visit';
        $dir=(FCPATH).$path;
        if(!is_dir($dir))
        {
            mkdir($dir, 0777);
        }
        $uploaded_files = System_helper::upload_file($path);

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

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->where('visit.area_id',$area_id);
        $this->db->where('visit.date_visit',$date_visit);
        $this->db->where('visit.status',$this->config->item('system_status_active'));
        $result=$this->db->get()->row_array();

        if($result)
        {
            $data=array();
            $data['other_info'] = $item['other_info'];
            $data['remarks'] = $item['remarks'];
            $data['week_odd_even'] = $week_odd_even;
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            Query_helper::update($this->config->item('table_ems_da_tmpo_setup_growing_area_visit'),$data, array('id='.$result['id']));

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.id = details.dealer_id','LEFT');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','LEFT');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.id = details.farmer_id','LEFT');
            $this->db->select('lead_farmers.name lead_farmers_name');

            /*$this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id>', 1);*/

            $this->db->where('details.visit_id',$result['id']);
            $this->db->where('details.status',$this->config->item('system_status_active'));

            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                if($result['dealer_id'])
                {
                    if(isset($dealer_items[$result['id']]))
                    {
                        $data=array();
                        if(isset($uploaded_files['dealer_file_'.$result['id']]) && $uploaded_files['dealer_file_'.$result['id']]['status'])
                        {
                            $data['image_name']=$uploaded_files['dealer_file_'.$result['id']]['info']['file_name'];
                            $data['image_location']=$path.'/'.$data['image_name'];
                        }
                        /*$data['visit_id']=$visit_id;
                        $data['dealer_id']=$result['id'];*/
                        $data['farmer_id']=0;
                        $data['description']=$dealer_items[$result['id']]['description'];
                        $this->db->set('revision_count_dealer', 'revision_count_dealer+1', FALSE);
                        Query_helper::update($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details'),$data,array('id='.$result['id']));
                    }
                }
                if($result['farmer_id'])
                {
                    if(isset($farmer_items[$result['id']]))
                    {
                        $data=array();
                        if(isset($uploaded_files['farmer_file_'.$result['id']]) && $uploaded_files['farmer_file_'.$result['id']]['status'])
                        {
                            $data['image_name']=$uploaded_files['farmer_file_'.$result['id']]['info']['file_name'];
                            $data['image_location']=$path.'/'.$data['image_name'];
                        }
                        /*$data['visit_id']=$visit_id;
                        $data['farmer_id']=$result['id'];*/
                        $data['dealer_id']=0;
                        $data['description']=$farmer_items[$result['id']]['description'];
                        $this->db->set('revision_count_farmer', 'revision_count_farmer+1', FALSE);
                        Query_helper::update($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details'),$data,array('id='.$result['id']));
                    }
                }
            }
        }
        else
        {
            $item['week_odd_even'] = $week_odd_even;
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $visit_id=Query_helper::add($this->config->item('table_ems_da_tmpo_setup_growing_area_visit'),$item);

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers');
            $this->db->select('dealers.*');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','INNER');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');
            $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id>', 1);
            $this->db->where('dealers.area_id',$area_id);
            $this->db->where('dealers.status',$this->config->item('system_status_active'));
            $dealers=$this->db->get()->result_array();

            foreach($dealers as $dealer)
            {
                if(isset($dealer_items[$dealer['id']]))
                {
                    $data=array();
                    if(isset($uploaded_files['dealer_file_'.$dealer['id']]) && $uploaded_files['dealer_file_'.$dealer['id']]['status'])
                    {
                        $data['image_name']=$uploaded_files['dealer_file_'.$dealer['id']]['info']['file_name'];
                        $data['image_location']=$path.'/'.$data['image_name'];
                    }
                    $data['visit_id']=$visit_id;
                    $data['dealer_id']=$dealer['id'];
                    $data['farmer_id']=0;
                    $data['description']=$dealer_items[$dealer['id']]['description'];
                    $this->db->set('revision_count_dealer', 'revision_count_dealer+1', FALSE);
                    Query_helper::add($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details'),$data);

                }
            }

            $farmers=Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers'),array('*'),array('area_id='.$area_id, 'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            foreach($farmers as $farmer)
            {
                if(isset($farmer_items[$farmer['id']]))
                {
                    $data=array();
                    if(isset($uploaded_files['farmer_file_'.$farmer['id']]) && $uploaded_files['farmer_file_'.$farmer['id']]['status'])
                    {
                        $data['image_name']=$uploaded_files['farmer_file_'.$farmer['id']]['info']['file_name'];
                        $data['image_location']=$path.'/'.$data['image_name'];
                    }
                    $data['visit_id']=$visit_id;
                    $data['farmer_id']=$farmer['id'];
                    $data['dealer_id']=0;
                    $data['description']=$farmer_items[$farmer['id']]['description'];
                    $this->db->set('revision_count_farmer', 'revision_count_farmer+1', FALSE);
                    Query_helper::add($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details'),$data);
                }
            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list($date_visit,$area_id);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
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
            if($method=='list_previous')
            {
                $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_previous');
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
