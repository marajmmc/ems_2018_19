<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_tour extends Root_Controller
{
    public  $message;
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
        elseif($action=="list")
        {
            $this->system_list();
        }elseif($action=="get_items")
        {
            $this->system_get_items();
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
            $this->system_search();
        }
    }

    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Tour Report";
            $ajax['status']=true;
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id,user.user_name,user.status');
            $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
            $this->db->select('user_area.*');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->select('user_info.name,user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->select('designation.name designation_name');
            if($this->locations['division_id']>0)
            {
                $this->db->where('user_area.division_id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('user_area.zone_id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('user_area.territory_id',$this->locations['territory_id']);
                    }
                }
            }
            //$this->db->where('user_area.territory_id >',0);
            $this->db->where('user_area.revision',1);
            $this->db->where('user.status',$this->config->item('system_status_active'));
            $this->db->where('user_info.revision',1);
            $this->db->order_by('user_info.ordering','ASC');
            $results=$this->db->get()->result_array();
            $all_user=array();
            foreach($results as &$result)
            {
                $result['value']=$result['user_id'];
                $result['text']=$result['employee_id'].'-'.$result['name'].' ('.$result['designation_name'].')';
                $all_user[]=$result;
            }
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));

                }
            }
            $data['user_info']=$all_user;
            $data['user_counter']=count($data['user_info']);
            $data['date_start']=System_helper::display_date(time());
            $data['date_end']=System_helper::display_date(time());
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
            $data['system_preference_items']= $this->get_preference();
            $reports=$this->input->post('report');
            if(!($reports['date_start']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select a Starting Date';
                $this->json_return($ajax);
            }
            if(!($reports['date_end']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select a Ending Date';
                $this->json_return($ajax);
            }
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_end']>0)
            {
                $reports['date_end']=$reports['date_end']+3600*24-1;
            }
            $data['options']=$reports;
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id,user.user_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->select('user_info.name');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->select('designation.name designation_name');
            $this->db->where('user.status',$this->config->item('system_status_active'));
            $this->db->where('user_info.revision',1);
            $this->db->where('user.id',$reports['user_id']);
            $this->db->order_by('user_info.ordering','ASC');
            $result=$this->db->get()->row_array();
            $result['date_start']=System_helper::display_date($reports['date_start']);
            $result['date_end']=System_helper::display_date($reports['date_end']);
            $data['employee_info']=$result;
            $ajax['status']=true;
            $data['title']="Tour Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }else
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
        $user_id=$this->input->post('user_id');

        //Data from source table
        $this->db->from($this->config->item('table_ems_tour_setup').' tour_setup');
        $this->db->select('tour_setup.*');
        $this->db->join($this->config->item('table_login_setup_user').' user','user.id = tour_setup.user_created','INNER');
        $this->db->select('user.id user_id, user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name username');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = user_area.district_id','LEFT');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = districts.territory_id','LEFT');
        $this->db->select('t.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' z','z.id = t.zone_id','LEFT');
        $this->db->select('z.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' d','d.id = z.division_id','LEFT');
        $this->db->select('d.name division_name');
        $this->db->order_by('d.id, z.id, t.id');
        if(!$user_id)
        {
            if($division_id>0)
            {
                $this->db->where('d.id',$division_id);
                if($zone_id>0)
                {
                    $this->db->where('z.id',$zone_id);
                    if($territory_id>0)
                    {
                        $this->db->where('t.id',$territory_id);
                    }
                }
            }
        }
        if($user_id)
        {
            $this->db->where('tour_setup.user_created',$user_id);
        }
        if($date_end>0)
        {
            $this->db->where('tour_setup.date_to <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('tour_setup.date_from >=',$date_start);
        }
        $this->db->where('user_info.revision',1);
        $this->db->where('tour_setup.status!=',$this->config->item('system_status_delete'));
        $this->db->order_by('tour_setup.id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['employee']=$item['employee_id'].'-'.$item['username'];
            if(!$item['district_name'])
            {
                $item['district_name']='N/A';
            }
            if(!$item['territory_name'])
            {
                $item['territory_name']='N/A';
            }
            if(!$item['zone_name'])
            {
                $item['zone_name']='N/A';
            }
            if(!$item['division_name'])
            {
                $item['division_name']='N/A';
            }
        }
        $this->json_return($items);
    }

    public function get_employee_info_list()
    {
        $html_container_id='#employee_info_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.employee_id,user.user_name,user.status');
        $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
        $this->db->select('user_area.*');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        if($this->locations['division_id']>0)
        {
            $this->db->where('user_area.division_id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('user_area.zone_id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('user_area.territory_id',$this->locations['territory_id']);
                }
            }
        }
        $this->db->where('user_area.territory_id >',0);
        $this->db->where('user_area.revision',1);
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        $results=$this->db->get()->result_array();
        $all_user=array();
        foreach($results as &$result)
        {
            $result['value']=$result['user_id'];
            $result['text']=$result['employee_id'].'-'.$result['name'].' ('.$result['designation_name'].')';
            $all_user[]=$result;
        }
        $data['items']=$all_user;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->json_return($ajax);
    }

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['preference_method_name']='search';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data['sl_no']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['employee']= 1;
        $data['department_name']= 1;
        $data['designation_name']= 1;
        $data['title']= 1;
        $data['details_button']= 1;
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
}
