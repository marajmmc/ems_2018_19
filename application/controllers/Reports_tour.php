<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_tour extends Root_Controller
{
    public  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $user;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->locations=User_helper::get_locations();
        $this->user=User_helper::get_user();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_tour_lang');
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
            $data['designations']=Query_helper::get_info($this->config->item('table_login_setup_designation'),array('id value','name text'),array('parent ='.$this->user->designation,'status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Tour Report";
            $ajax['status']=true;

            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id,user.user_name,user.status');
            $this->db->join($this->config->item('table_login_setup_user_area').' user_area','user_area.user_id=user.id','INNER');
            $this->db->select('user_area.*');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->select('user_info.name,user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $this->db->select('designation.name designation_name, designation.id designation_id');
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
            if($this->locations['division_id']==0 && !($this->user->user_group==1 || $this->user->user_group==2))
            {
                if(sizeof($data['designations'])>0)
                {
                    $this->db->where('designation.parent',$this->user->designation);
                }
                else
                {
                    $this->db->where('designation.id',$this->user->designation);
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
            $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['my_designation']=Query_helper::get_info($this->config->item('table_login_setup_designation'),array('id value','name text'),array('id ='.$this->user->designation,'status ="'.$this->config->item('system_status_active').'"'),1);
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

    private function get_preference_headers()
    {
        $data['sl_no']= 1;
        $data['division_name']= 1;
        $data['zone_name']= 1;
        $data['territory_name']= 1;
        $data['date_setup']= 1;
        $data['date_approve']= 1;
        $data['employee']= 1;
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

            //Getting subordinate employee for validation
            $data['designations']=Query_helper::get_info($this->config->item('table_login_setup_designation'),array('id value','name text'),array('parent ='.$this->user->designation,'status ="'.$this->config->item('system_status_active').'"'));

            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->select('user.employee_id');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            if($this->locations['division_id']==0 && !($this->user->user_group==1 || $this->user->user_group==2))
            {
                if(sizeof($data['designations'])>0)
                {
                    $this->db->where('designation.parent',$this->user->designation);
                }
                else
                {
                    $this->db->where('designation.id',$this->user->designation);
                }
            }
            else
            {
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
                //$this->db->where('user_area.territory_id >',0);
                $this->db->where('user_area.revision',1);
            }
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
        $department_id=$this->input->post('department_id');
        $designation_id=$this->input->post('designation_id');
        $user_id=$this->input->post('user_id');
        $employee_id=$this->input->post('employee_id');
        $status_approve=$this->input->post('status_approve');
        $date_type=$this->input->post('date_type');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

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
            if($department_id)
            {
                if(!$designation_id)
                {
                    $this->db->where('department.id',$department_id);
                }
                else
                {
                    $this->db->where('designation.id',$designation_id);
                }
            }
            else
            {
                if($designation_id)
                {
                    $this->db->where('designation.id',$designation_id);
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
                $this->db->where('tour_setup.date_created <=',$date_end);
                $this->db->where('tour_setup.date_created >=',$date_start);
            }
            elseif($date_type=='approve_date_time')
            {
                $this->db->where('tour_setup.date_approved <=',$date_end);
                $this->db->where('tour_setup.date_approved >=',$date_start);
            }
            else
            {
                $this->db->where('tour_setup_purpose.date_reporting <=',$date_end);
                $this->db->where('tour_setup_purpose.date_reporting >=',$date_start);
            }
        }
        else
        {
            $this->db->where('tour_setup.date_to <=',$date_end);
            $this->db->where('tour_setup.date_from >=',$date_start);
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

    public function get_employee_info_list_by_designation()
    {
        $html_container_id='#employee_info_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->select('designation.name designation_name');
        $this->db->where('designation.parent',$this->user->designation);
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        $results=$this->db->get()->result_array();
        $all_user=array();
        foreach($results as &$result)
        {
            $result['value']=$result['id'];
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

            $user = User_helper::get_user();
            $data = array();

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.id AS designation_id, designation.parent AS parent_designation, designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('department.name AS department_name');
            //$this->db->where('user.status', $this->config->item('system_status_active'));
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();


            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_purpose');
            $this->db->select('tour_purpose.*');
            $this->db->where('tour_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_purpose.status', 'Active');
            $data['items'] = $this->db->get()->result_array();

            //Data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others') . ' tour_setup_purpose_others', 'tour_setup_purpose_others.tour_setup_purpose_id = tour_setup_purpose.id', 'LEFT');
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion,');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
            $results_purpose_others = $this->db->get()->result_array();

            $other_info = array();
            foreach ($results_purpose_others as $results_purpose_other)
            {
                $other_info[$results_purpose_other['id']]['purpose'] = $results_purpose_other['purpose'];
                $other_info[$results_purpose_other['id']]['date_reporting'] = $results_purpose_other['date_reporting'];
                $other_info[$results_purpose_other['id']]['report_description'] = $results_purpose_other['report_description'];
                $other_info[$results_purpose_other['id']]['recommendation'] = $results_purpose_other['recommendation'];
                $other_info[$results_purpose_other['id']]['purpose_others_id'] = $results_purpose_other['purpose_others_id'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['name'] = $results_purpose_other['name'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['contact_no'] = $results_purpose_other['contact_no'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['profession'] = $results_purpose_other['profession'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['discussion'] = $results_purpose_other['discussion'];
            }

            if (!$data['item'])
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($user->user_group != 1 && $user->user_group != 2)
            {
                if ((!$this->check_my_editable($data['item'])) && ($user->designation != $data['item']['parent_designation']))
                {
                    System_helper::invalid_try('Details', $item_id, 'Trying to view details others tour setup');
                    $ajax['status'] = false;
                    $ajax['system_message'] = 'You are trying to view details others tour setup';
                    $this->json_return($ajax);
                }
            }

            $data['items_purpose_others'] = $other_info;
            $data['title'] = 'Tour Setup And Reporting Details:: ' . $data['item']['title'];
            $ajax['status'] = true;
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            //$ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
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
