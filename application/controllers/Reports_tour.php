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
        $this->lang->load('report_tour');
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
            $data['date_from']=System_helper::display_date(time());
            $data['date_to']=System_helper::display_date(time());
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
    private function get_preference_headers()
    {
        $data['sl_no']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['employee']= 1;
        $data['date_setup']= 1;
        $data['date_approve']= 1;
        $data['department_name']= 1;
        $data['designation_name']= 1;
        $data['title']= 1;
        $data['amount_iou']= 1;
        $data['status_approve']= 1;
        $data['no_of_purpose']= 1;
        $data['complete_reporting']= 1;
        $data['incomplete_reporting']= 1;
        $data['details_button']= 1;
        return $data;
    }
    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="search"'),1);
        $data=$this->get_preference_headers();
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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $reports=$this->input->post('report');
            if(!($reports['date_from']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select From Date';
                $this->json_return($ajax);
            }
            if(!($reports['date_to']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select To Date';
                $this->json_return($ajax);
            }
            $reports['date_to']=System_helper::get_time($reports['date_to']);
            $reports['date_from']=System_helper::get_time($reports['date_from']);
            if($reports['date_to']>0)
            {
                $reports['date_to']=$reports['date_to']+3600*24-1;
            }
            if ($reports['date_from']>$reports['date_to'])
            {
                $ajax['status']=false;
                $ajax['system_message']='From Date cannot be greater than To Date.';
                $this->json_return($ajax);
            }

            //Getting subordinate employee for validation

            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
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
            $this->db->where('user_area.revision',1);
            $this->db->where('user.status',$this->config->item('system_status_active'));
            $this->db->where('user_info.revision',1);
            $this->db->order_by('user_info.ordering','ASC');
            $results_subordinate=$this->db->get()->result_array();
            $subordinate_employee=array();
            foreach($results_subordinate as $subordinate)
            {
                $subordinate_employee[]=$subordinate['employee_id'];
            }
            if(isset($reports['employee_id']) && $reports['employee_id'])
            {
                if(!(in_array($reports['employee_id'],$subordinate_employee)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You can not search report for this employee';
                    $this->json_return($ajax);
                }
            }

            $data['options']=$reports;
            $result['date_from']=System_helper::display_date($reports['date_from']);
            $result['date_to']=System_helper::display_date($reports['date_to']);
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
        $user_id=$this->input->post('user_id');
        $employee_id=$this->input->post('employee_id');
        $status_approve=$this->input->post('status_approve');
        $date_type=$this->input->post('date_type');
        $date_to=$this->input->post('date_to');
        $date_from=$this->input->post('date_from');

        //Getting tour data to calculate total no of purpose, complete reporting and incomplete reporting number
        $this->db->from($this->config->item('table_ems_tour_setup').' tour_setup');
        $this->db->join($this->config->item('table_ems_tour_setup_purpose').' tour_setup_purpose','tour_setup_purpose.tour_setup_id = tour_setup.id','INNER');
        $this->db->select('tour_setup_purpose.*');
        $this->db->where('tour_setup.status!=',$this->config->item('system_status_delete'));
        $this->db->where('tour_setup_purpose.status!=',$this->config->item('system_status_delete'));
        $results=$this->db->get()->result_array();
        $reporting_summary=array();
        foreach($results as $result)
        {
            if(isset($reporting_summary[$result['tour_setup_id']]))
            {
                $reporting_summary[$result['tour_setup_id']]['no_of_purpose']++;
                if($result['date_reporting']!=null)
                {
                    $reporting_summary[$result['tour_setup_id']]['complete_reporting']++;
                }
                else
                {
                    $reporting_summary[$result['tour_setup_id']]['incomplete_reporting']++;
                }
            }
            else
            {
                $reporting_summary[$result['tour_setup_id']]['no_of_purpose']=1;
                if($result['date_reporting']!=null)
                {
                    $reporting_summary[$result['tour_setup_id']]['complete_reporting']=1;
                    $reporting_summary[$result['tour_setup_id']]['incomplete_reporting']=0;
                }
                else
                {
                    $reporting_summary[$result['tour_setup_id']]['incomplete_reporting']=1;
                    $reporting_summary[$result['tour_setup_id']]['complete_reporting']=0;
                }
            }
        }

        //getting tour data for grid list view
        $this->db->from($this->config->item('table_ems_tour_setup').' tour_setup');
        $this->db->select('tour_setup.*');
        $this->db->join($this->config->item('table_login_setup_user').' user','user.id = tour_setup.user_created','INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name username');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = user_area.district_id','LEFT');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = user_area.territory_id','LEFT');
        $this->db->select('t.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' z','z.id = user_area.zone_id','LEFT');
        $this->db->select('z.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' d','d.id = user_area.division_id','LEFT');
        $this->db->select('d.name division_name');
        $this->db->order_by('d.id, z.id, t.id');
        if($employee_id)
        {
            $this->db->where('user.employee_id',$employee_id);
        }
        else
        {
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
            else
            {
                $this->db->where('tour_setup.user_created',$user_id);
            }
        }
        if($status_approve)
        {
            $this->db->where('tour_setup.status_approve',$status_approve);
        }
        if($date_type)
        {
            if($date_type=='tour_created_time')
            {
                $this->db->where('tour_setup.date_created <=',$date_to);
                $this->db->where('tour_setup.date_created >=',$date_from);
            }
            elseif($date_type=='approve_date_time')
            {
                $this->db->where('tour_setup.date_approved <=',$date_to);
                $this->db->where('tour_setup.date_approved >=',$date_from);
            }
            else
            {
                $this->db->where('tour_setup_purpose.date_reporting <=',$date_to);
                $this->db->where('tour_setup_purpose.date_reporting >=',$date_from);
            }
        }
        else
        {
            $this->db->where('tour_setup.date_to <=',$date_to);
            $this->db->where('tour_setup.date_from >=',$date_from);
        }
        $this->db->where('user_info.revision',1);
        $this->db->where('user_area.revision',1);
        $this->db->where('tour_setup.status!=',$this->config->item('system_status_delete'));
        $this->db->order_by('tour_setup.id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_setup']=System_helper::display_date_time($item['date_created']);
            if($item['date_approved'])
            {
                $item['date_approve']=System_helper::display_date_time($item['date_approved']);
            }
            else
            {
                $item['date_approve']='-';
            }
            $item['employee']=$item['employee_id'].'-'.$item['username'];
            if(!$item['district_name'])
            {
                $item['district_name']='-';
            }
            if(!$item['territory_name'])
            {
                $item['territory_name']='-';
            }
            if(!$item['zone_name'])
            {
                $item['zone_name']='-';
            }
            if(!$item['division_name'])
            {
                $item['division_name']='-';
            }
            if(isset($reporting_summary[$item['id']]))
            {
                $item['no_of_purpose']=$reporting_summary[$item['id']]['no_of_purpose'];
                $item['complete_reporting']=$reporting_summary[$item['id']]['complete_reporting'];
                $item['incomplete_reporting']=$reporting_summary[$item['id']]['incomplete_reporting'];
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
    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
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
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = user.id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try('details',$item_id,'View Non Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created']]=$data['item']['user_created'];
            $user_ids[$data['item']['user_updated']]=$data['item']['user_updated'];
            $user_ids[$data['item']['user_forwarded']]=$data['item']['user_forwarded'];
            $user_ids[$data['item']['user_approved']]=$data['item']['user_approved'];
            $data['users']=System_helper::get_users_info($user_ids);

            //data from tour setup purpose
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' purpose');
            $this->db->select('purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others').' others','others.tour_setup_purpose_id=purpose.id AND others.status = "'.$this->config->item('system_status_active').'"','LEFT');
            $this->db->select(
                '
                others.id purpose_others_id,
                others.name,
                others.contact_no,
                others.profession,
                others.discussion
                ');
            $this->db->where('purpose.tour_setup_id', $item_id);
            $this->db->where('purpose.status', $this->config->item('system_status_active'));
            $this->db->order_by('purpose.id', 'ASC');
            $results = $this->db->get()->result_array();
            $items=array();
            foreach ($results as $result)
            {
                $items[$result['id']]['tour_setup_id']=$result['tour_setup_id'];
                $items[$result['id']]['purpose']=$result['purpose'];
                $items[$result['id']]['date_reporting']=$result['date_reporting'];
                $items[$result['id']]['report_description']=$result['report_description'];
                $items[$result['id']]['recommendation']=$result['recommendation'];
                $items[$result['id']]['revision_count_reporting']=$result['revision_count_reporting'];
                $items[$result['id']]['others'][$result['purpose_others_id']]['name'] = $result['name'];
                $items[$result['id']]['others'][$result['purpose_others_id']]['contact_no'] = $result['contact_no'];
                $items[$result['id']]['others'][$result['purpose_others_id']]['profession'] = $result['profession'];
                $items[$result['id']]['others'][$result['purpose_others_id']]['discussion'] = $result['discussion'];
            }
            $data['items'] = $items;
            $data['title'] = 'Tour Setup And Reporting Details:: ' . $data['item']['title'];
            $ajax['status'] = true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
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
