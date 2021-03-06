<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_da_tmpo_setup_area_visit_attendance extends Root_Controller
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
        $this->lang->load('field_visit');
        $this->lang->load('report_da_tmpo_setup_area_visit_attendance');
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
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search_transfer"'),1);

        $data['visit_id']= 1;
        $data['outlet_name']= 1;
        $data['date_attendance']= 1;
        $data['schedule_area']= 1;
        $data['visited_area']= 1;
        $data['status_attendance']= 1;
        $data['details_view']= 1;
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
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
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

            $data['title']="Growing Area Daily Attendance";
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
            $reports=$this->input->post('report');
            if(!($reports['date_start'] || $reports['date_end']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Minimum provide the start or end date.';
                $this->json_return($ajax);
            }
            $reports['date_end']=System_helper::get_time($reports['date_end'])+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->json_return($ajax);
            }

            $data['options']=$reports;

            $data['system_preference_items']= $this->get_preference();
            $data['title']="Growing Area Daily Attendance Report";
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
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        //$status_attendance=$this->input->post('status_attendance');

        /*get outlets */
        $this->db->from($this->config->item('table_login_csetup_cus_info').' outlet_info');
        $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.id division_id, divisions.name division_name');
        $this->db->order_by('divisions.id, zones.id, territories.id, districts.id, outlet_info.customer_id');
        $this->db->where('outlet_info.revision',1);
        $this->db->where('outlet_info.type',$this->config->item('system_customer_type_outlet_id'));
        if($division_id)
        {
            $this->db->where('divisions.id',$division_id);
            if($zone_id)
            {
                $this->db->where('zones.id',$zone_id);
                if($territory_id)
                {
                    $this->db->where('territories.id',$territory_id);
                    if($district_id)
                    {
                        $this->db->where('districts.id',$district_id);
                        if($outlet_id)
                        {
                            $this->db->where('outlet_info.customer_id',$outlet_id);
                        }
                    }
                }
            }
        }
        $outlets=$this->db->get()->result_array();
        $outlet_ids=array();
        $outlet_ids[0]=0;
        foreach($outlets as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
        }

        /*get Schedule wise areas*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_visit_schedules').' schedules');
        $this->db->select('schedules.outlet_id,schedules.ordering');
        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas_odd','areas_odd.id=schedules.area_id_odd','INNER');
        $this->db->select('areas_odd.id odd_areas_id, CONCAT_WS(" - ",areas_odd.name, areas_odd.address) odd_area_name,areas_odd.address odd_area_address');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas_even','areas_even.id=schedules.area_id_even','INNER');
        $this->db->select('areas_even.id even_area_id, CONCAT_WS(" - ",areas_even.name, areas_even.address) even_area_name,areas_even.address even_area_address');

        $this->db->where_in('schedules.outlet_id',$outlet_ids);
        $this->db->where('schedules.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $schedule_areas=array();
        foreach($results as $result)
        {
            $schedule_areas[$result['outlet_id']][$result['ordering']]=$result;
        }

        /*get visited areas*/
        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
        $this->db->select('areas.outlet_id, CONCAT_WS(" - ",name, address) area_name,areas.address area_address,areas.address status_visit_area');

        $this->db->where('areas.status',$this->config->item('system_status_active'));
        $this->db->where_in('areas.outlet_id',$outlet_ids);
        /*if($status_attendance)
        {
            $this->db->where('visit.status_attendance',$status_attendance);
        }*/
        $results=$this->db->get()->result_array();
        $visited_areas=array();
        foreach($results as $result)
        {
            $visited_areas[$result['outlet_id']][$result['date_visit']][$result['week_odd_even']]=$result;
        }

        /*jqx grid data manipulation*/
        $date_diff = $date_end - $date_start;
        $day=ceil($date_diff / (60 * 60 * 24));
        $items=array();
        foreach($outlets as $outlet)
        {
            //$first_row=true;
            $date_time=$date_start;
            for($i=1;$i<=$day;$i++)
            {
                $date_string=System_helper::display_date($date_time);
                $week_number = date('W', $date_time);
                $week_odd_even=($week_number%2);
                $day_key = date('w', $date_time);


                /*if($first_row)
                {
                    $first_row=false;
                    $item['outlet_name']=$outlet['outlet_name'];
                }
                else
                {
                    $item['outlet_name']='';
                }*/
                $item['outlet_name']=$outlet['outlet_name'];
                $item['date_attendance']=$date_string;
                if($week_odd_even)
                {
                    $item['schedule_area']=isset($schedule_areas[$outlet['outlet_id']][$day_key])?$schedule_areas[$outlet['outlet_id']][$day_key]['odd_area_name']:'--';
                }
                else
                {
                    $item['schedule_area']=isset($schedule_areas[$outlet['outlet_id']][$day_key])?$schedule_areas[$outlet['outlet_id']][$day_key]['even_area_name']:'--';
                }

                $item['visit_id']='';
                $item['visited_area']='';
                $item['status_attendance']='';
                if(isset($visited_areas[$outlet['outlet_id']][$date_time][$week_odd_even]))
                {
                    $item['visit_id']=$visited_areas[$outlet['outlet_id']][$date_time][$week_odd_even]['id'];
                    $item['visited_area']=$visited_areas[$outlet['outlet_id']][$date_time][$week_odd_even]['area_name'];
                    $item['status_attendance']=$visited_areas[$outlet['outlet_id']][$date_time][$week_odd_even]['status_attendance'];
                }

                $items[]=$item;
                $date_time=$date_time+86400;
            }
        }
        $this->json_return($items);
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search_transfer';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_transfer');
            $this->json_return($ajax);
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

            $user_ids=array();
            $user_ids[$data['item_head']['user_created']]=$data['item_head']['user_created'];
            if($data['item_head']['user_updated'])
            {
                $user_ids[$data['item_head']['user_updated']]=$data['item_head']['user_updated'];
            }
            if($data['item_head']['user_attendance'])
            {
                $user_ids[$data['item_head']['user_attendance']]=$data['item_head']['user_attendance'];
            }
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_dealers').' dealers','dealers.id = details.dealer_id','LEFT');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealers.dealer_id','LEFT');
            $this->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers').' lead_farmers','lead_farmers.id = details.farmer_id','LEFT');
            $this->db->select('lead_farmers.name lead_farmers_name');

            $this->db->where('details.visit_id',$data['item_head']['id']);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();

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
            $results=$this->db->get()->result_array();
            $result_area_ids[0]=0;
            $data['previous_visits']=array();
            foreach($results as $result)
            {
                $result_area_ids[$result['id']]=$result['id'];
                $data['previous_visits'][$result['id']]=$result;
            }

            $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit_details').' details');
            $this->db->select('details.*');
            $this->db->where_in('details.visit_id',$result_area_ids);
            $this->db->where('details.status',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();
            $data['previous_dealers']=array();
            $data['previous_farmers']=array();
            foreach($results as $result)
            {
                if($result['dealer_id'])
                {
                    $data['previous_dealers'][$result['visit_id']][$result['dealer_id']]=$result;
                }
                if($result['farmer_id'])
                {
                    $data['previous_farmers'][$result['visit_id']][$result['farmer_id']]=$result;
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
            $data['title']="Growing Area Visit :: Outlet: ".$data['item_head']['outlet_name'].", Growing Area: ".$data['item_head']['area_name'].", Address: ".$data['item_head']['area_address'].", <span class='text-danger'>Date: ".System_helper::display_date($data['item_head']['date_visit']).'</span> ';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
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

}
