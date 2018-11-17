<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_da_tmpo_growing_area_attendance extends Root_Controller
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
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['employee_name']= 1;
            $data['outlet_name']= 1;
            $data['number_of_day']= 1;
            $data['number_of_area_visit']= 1;
            $data['number_of_present']= 1;
            $data['number_of_absent']= 1;
            $data['number_of_leave']= 1;
            $data['number_of_nd']= 1;
            $data['number_of_friday']= 1;
            $data['number_of_extra_days']= 1;
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

            $data['title']="Growing Area Attendance Report";
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
        $user = User_helper::get_user();
        $method = 'list';
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

            $date_start=$reports['date_start'];
            $date_end=$reports['date_end'];
            $number_of_days=(round(($date_end-$date_start) / (60*60*24))) ;

            $number_of_friday=0;
            $start = new DateTime(System_helper::display_date($date_start));
            $end   = new DateTime(System_helper::display_date($date_end));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $dt)
            {
                if ($dt->format('N') == 5)
                {
                    $number_of_friday++;
                }
            }
            $data['number_of_days']=$number_of_days;
            $data['number_of_friday']=$number_of_friday;

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Growing Area Attendance Report";
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
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $date_start=$this->input->post('date_start');
        $date_end=$this->input->post('date_end');
        $number_of_days=(round(($date_end-$date_start) / (60*60*24))) ;

        $number_of_friday=0;
        $start = new DateTime(System_helper::display_date($date_start));
        $end   = new DateTime(System_helper::display_date($date_end));
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $dt)
        {
            if ($dt->format('N') == 5)
            {
                $number_of_friday++;
            }
        }

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
        $outlet_ids[0]=0;
        $outlet_info=array();
        foreach($outlets as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
            $outlet_info[$result['outlet_id']]=$result;
        }

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_growing_area_visit').' visit');
        $this->db->select('visit.*');
        $this->db->select('COUNT(visit.id) number_of_area_visit',true);
        $this->db->select('SUM(CASE WHEN visit.status_attendance="'.$this->config->item('system_status_present').'" then 1 ELSE 0 END) number_of_present',false);
        $this->db->select('SUM(CASE WHEN visit.status_attendance="'.$this->config->item('system_status_absent').'" then 1 ELSE 0 END) number_of_absent',false);
        $this->db->select('SUM(CASE WHEN visit.status_attendance="'.$this->config->item('system_status_cl').'" then 1 ELSE 0 END) number_of_leave',false);
        $this->db->select('SUM(CASE WHEN visit.status_attendance="'.$this->config->item('system_status_pending').'" then 1 ELSE 0 END) number_of_nd',false);

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas').' areas','areas.id=visit.area_id','INNER');
        $this->db->select('areas.outlet_id');

        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=visit.user_created AND user_info.revision = 1','INNER');
        $this->db->select('user_info.name visit_employee_name');

        $this->db->where('visit.date_visit >=',$date_start);
        $this->db->where('visit.date_visit <=',$date_end);
        $this->db->where_in('areas.outlet_id',$outlet_ids);
        $this->db->group_by('areas.outlet_id');
        $this->db->group_by('visit.user_created');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $info=$this->initialize_row($result,$number_of_days,$number_of_friday);
            $info['employee_name']=$result['visit_employee_name'];
            $info['outlet_name']=isset($outlet_info[$result['outlet_id']])?$outlet_info[$result['outlet_id']]['outlet_name']:'';
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row($info, $number_of_days, $number_of_friday)
    {
        $row=$this->get_preference_headers('list');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['employee_name']= $info['user_created'];
        $row['outlet_name']= $info['outlet_id'];
        $row['number_of_day']= $number_of_days;
        $row['number_of_area_visit']= $info['number_of_area_visit'];
        $row['number_of_present']= $info['number_of_present'];
        $row['number_of_absent']= $info['number_of_absent'];
        $row['number_of_leave']= $info['number_of_leave'];
        $row['number_of_nd']= $info['number_of_nd'];
        $row['number_of_friday']= $number_of_friday;
        $row['number_of_extra_days']= ($number_of_days-$number_of_friday-$info['number_of_area_visit']);

        return $row;
    }
    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
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

}
