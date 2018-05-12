<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_field_visit_attendance extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_field_visit_attendance');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url='reports_field_visit_attendance';
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
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['title']="Field Visit And Attendance Report";
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
            $data['title']="Field Visit And Attendance Report";
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
        $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_farmer_visit');
        $this->db->select('dealer_farmer_visit.*');
        $this->db->join($this->config->item('table_login_setup_user').' user','user.id = dealer_farmer_visit.user_created','INNER');
        $this->db->select('user.id user_id, user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name username');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_farmer_visit.farmer_id','INNER');
        $this->db->select('farmer.name dealer');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_farmer_visit.customer_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.name outlet');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','LEFT');
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
            $this->db->where('dealer_farmer_visit.user_created',$user_id);
        }
        if($date_end>0)
        {
            $this->db->where('dealer_farmer_visit.date <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('dealer_farmer_visit.date >=',$date_start);
        }
        $this->db->where('user_info.revision',1);
        $this->db->where('cus_info.revision',1);
        $this->db->where('dealer_farmer_visit.status!=',$this->config->item('system_status_delete'));
        $this->db->order_by('dealer_farmer_visit.id DESC');
        $dealer_farmer_visit=$this->db->get()->result_array();

        // Arranging data in new array
        $dealer_farmer_visit_list=array();
        foreach($dealer_farmer_visit as &$visit)
        {
            $date_string=System_helper::display_date($visit['date']);
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date']=$date_string;
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['id']=$visit['id'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['outlet']=$visit['outlet'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['dealer']=$visit['dealer'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['division_name']=$visit['division_name'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['zone_name']=$visit['zone_name'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['territory_name']=$visit['territory_name'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['district_name']=$visit['district_name'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['username']=$visit['employee_id'].'-'.$visit['username'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['status']=$visit['status'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date']=$visit['date'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date_created']=$visit['date_created'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['user_created']=$visit['user_created'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date_updated']=$visit['date_updated'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['user_updated']=$visit['user_updated'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['zsc_comment']=$visit['zsc_comment'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['status_attendance']=$visit['status_attendance'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date_created_attendance']=$visit['date_created_attendance'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['user_created_attendance']=$visit['user_created_attendance'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['date_updated_attendance']=$visit['date_updated_attendance'];
            $dealer_farmer_visit_list[$date_string][$visit['user_created']]['user_updated_attendance']=$visit['user_updated_attendance'];
        }

        //Searched User Info
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
        if(!$user_id)
        {
            if($division_id>0)
            {
                $this->db->where('user_area.division_id',$division_id);
                if($zone_id>0)
                {
                    $this->db->where('user_area.zone_id',$zone_id);
                    if($territory_id>0)
                    {
                        $this->db->where('user_area.territory_id',$territory_id);
                    }
                }
            }
            $this->db->where('user_area.territory_id >',0);
        }
        if($user_id)
        {
            $this->db->where('user_area.user_id',$user_id);
        }
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = user_area.territory_id','LEFT');
        $this->db->select('t.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' z','z.id = user_area.zone_id','LEFT');
        $this->db->select('z.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' d','d.id = user_area.division_id','LEFT');
        $this->db->select('d.name division_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        $this->db->select('user_area.*');
        $this->db->where('user_area.revision',1);
        $this->db->where('user_info.revision',1);
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->order_by('d.id, z.id, t.id');
        $this->db->group_by('user.id');
        $searched_user=$this->db->get()->result_array();

        $date_diff = $date_end - $date_start;
        $day=ceil($date_diff / (60 * 60 * 24));
        $date_time=$date_start;
        $items=array();
        for($i=1;$i<=$day;$i++)
        {
            $date_string=System_helper::display_date($date_time);
            for($j=0;$j<count($searched_user);$j++)
            {
                if(isset($dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]))
                {
                    $item['id']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['id'];
                    $item['date']=System_helper::display_date_time($dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['date']);
                    $item['division_name']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['division_name'];
                    $item['zone_name']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['zone_name'];
                    $item['territory_name']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['territory_name'];
                    $item['dealer']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['dealer'];
                    $item['username']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['username'];
                    $item['created_time']=System_helper::display_date_time($dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['date_created']);
                    $item['status_attendance']=$dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['status_attendance'];
                    $item['attendance_taken_time']=System_helper::display_date_time($dealer_farmer_visit_list[$date_string][$searched_user[$j]['user_id']]['date_created_attendance']);
                    $items[]=$item;
                }
                else
                {
                    $item['id']=0;
                    $item['date']=$date_string;
                    if($searched_user[$j]['division_name'])
                    {
                        $item['division_name']=$searched_user[$j]['division_name'];
                    }
                    else
                    {
                        $item['division_name']='-';
                    }
                    if($searched_user[$j]['zone_name'])
                    {
                        $item['zone_name']=$searched_user[$j]['zone_name'];
                    }
                    else
                    {
                        $item['zone_name']='-';
                    }

                    if($searched_user[$j]['territory_name'])
                    {
                        $item['territory_name']=$searched_user[$j]['territory_name'];
                    }
                    else
                    {
                        $item['territory_name']='-';
                    }

                    $item['dealer']='-';
                    $item['username']=$searched_user[$j]['employee_id'].'-'.$searched_user[$j]['name'];
                    $item['created_time']='-';
                    $item['status_attendance']='-';
                    $item['attendance_taken_time']='-';
                    $items[]=$item;
                }
            }
            $date_time=$date_time+86400;
        }
        $this->json_return($items);
    }

    private function system_details($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            $html_container_id=$this->input->post('html_container_id');
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_farmer_visit');
            $this->db->select('dealer_farmer_visit.*');
            $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id = dealer_farmer_visit.user_created','INNER');
            $this->db->select('user_area.user_id');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_farmer_visit.farmer_id','INNER');
            $this->db->select('farmer.name dealer');
            $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_farmer_visit.customer_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.name outlet');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','LEFT');
            $this->db->select('districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = districts.territory_id','LEFT');
            $this->db->select('t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' z','z.id = t.zone_id','LEFT');
            $this->db->select('z.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' d','d.id = z.division_id','LEFT');
            $this->db->select('d.name division_name');
            $this->db->where('dealer_farmer_visit.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            $data['title']="Dealer And Field Visit Task Details";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view($this->controller_url."/details",$data,true));
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
}
