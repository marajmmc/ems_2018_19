<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_ti_attendance extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Ft_ti_attendance');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url='ft_ti_attendance';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
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
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="TI Attendance (Dealer And Field visit) Pending List";
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
            $pagesize=100;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        $results=$this->db->get()->result_array();
        $users_info=array();
        foreach($results as $result)
        {
            $users_info[$result['id']]['employee_id']=$result['employee_id'];
            $users_info[$result['id']]['name']=$result['name'];
        }
        $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
        $this->db->select('dealer_field_visit.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.name customer_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                }
            }
        }
        $this->db->where('dealer_field_visit.status_attendance','Pending');
        $this->db->where('dealer_field_visit.status',$this->config->item('system_status_active'));
        $this->db->order_by('dealer_field_visit.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
//        print_r($items);
//        exit;
        foreach($items as &$item)
        {
            if($item['user_updated']==null)
            {
                if(isset($users_info[$item['user_created']]))
                {
                    $item['employee_id']=$users_info[$item['user_created']]['employee_id'];
                    $item['name']=$users_info[$item['user_created']]['name'];
                }
            }
            else
            {
                if(isset($users_info[$item['user_updated']]))
                {
                    $item['employee_id']=$users_info[$item['user_updated']]['employee_id'];
                    $item['name']=$users_info[$item['user_updated']]['name'];
                }
            }
            if($item['status_attendance']=='Pending')
            {
                $item['status_attendance']='-';
            }
            $item['date']=System_helper::display_date($item['date']);
        }
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="TI Attendance (Dealer And Field visit) All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list_all");
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
        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id','INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->where('user.status',$this->config->item('system_status_active'));
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        $results=$this->db->get()->result_array();
        $users_info=array();
        foreach($results as $result)
        {
            $users_info[$result['id']]['employee_id']=$result['employee_id'];
            $users_info[$result['id']]['name']=$result['name'];
        }
        $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
        $this->db->select('dealer_field_visit.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.name customer_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                }
            }
        }
        $this->db->where('dealer_field_visit.status',$this->config->item('system_status_active'));
        $this->db->order_by('dealer_field_visit.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if($item['user_updated']==null)
            {
                if(isset($users_info[$item['user_created']]))
                {
                    $item['employee_id']=$users_info[$item['user_created']]['employee_id'];
                    $item['name']=$users_info[$item['user_created']]['name'];
                }
            }
            else
            {
                if(isset($users_info[$item['user_updated']]))
                {
                    $item['employee_id']=$users_info[$item['user_updated']]['employee_id'];
                    $item['name']=$users_info[$item['user_updated']]['name'];
                }
            }
            if($item['status_attendance']=='Pending')
            {
                $item['status_attendance']='-';
            }
            $item['date']=System_helper::display_date($item['date']);
        }
        $this->json_return($items);
    }

    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
            $this->db->select('dealer_field_visit.*');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
            $this->db->select('farmer.name farmer_name');
            $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.name customer_name, cus_info.district_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->select('d.territory_id, d.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.zone_id, t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.division_id, zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');
            $this->db->where('dealer_field_visit.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            $data['dealer_info_file']=Query_helper::get_info($this->config->item('table_ems_setup_ft_dealer_file'),array('*'),array('farmer_id ='.$data['item']['farmer_id']));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Attendance',$item_id,$this->config->item('system_edit_others'));
                $ajax['status']=false;
                $ajax['system_message']='You are trying to take attendance of employee who is not assigned to you.';
                $this->json_return($ajax);
            }
            $data['title']='TI Attendance';
            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
            $this->db->select('farmer.name text, farmer.id value');
            $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id >',1);
            $this->db->where('farmer_outlet.revision',1);
            $this->db->where('farmer_outlet.outlet_id',$data['item']['customer_id']);
            $data['farmers']=$this->db->get()->result_array();
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
        $result=Query_helper::get_info($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),array('*'),array('id ='.$id,'status ="'.$this->config->item('system_status_active').'"'),1);
        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();

        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            if($result['revision_count_attendance']>0)
            {
                $item['user_updated_attendance'] = $user->user_id;
                $item['date_updated_attendance'] = $time;
                $this->db->set('revision_count_attendance', 'revision_count_attendance+1', FALSE);
                Query_helper::update($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),$item,array("id = ".$id));
            }
            else
            {
                $item['user_created_attendance'] = $user->user_id;
                $item['date_created_attendance'] = $time;
                $this->db->set('revision_count_attendance', 'revision_count_attendance+1', FALSE);
                Query_helper::update($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),$item,array("id = ".$id));
            }
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
        $this->form_validation->set_rules('item[status_attendance]',$this->lang->line('LABEL_ATTENDANCE_STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
            $this->db->select('dealer_field_visit.*');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
            $this->db->select('farmer.name farmer_name');
            $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.name customer_name, cus_info.district_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->select('d.territory_id, d.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.zone_id, t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.division_id, zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');
            $this->db->where('dealer_field_visit.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            $data['dealer_info_file']=Query_helper::get_info($this->config->item('table_ems_setup_ft_dealer_file'),array('*'),array('farmer_id ='.$data['item']['farmer_id']));
            if(!$data['item'])
            {
                System_helper::invalid_try('Details',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Details',$item_id,'view others');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to view details of others.';
                $this->json_return($ajax);
            }
            $data['title']='TI Attendance (Dealer And Farmer visit) Details';
            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
            $this->db->select('farmer.name text, farmer.id value');
            $this->db->where('farmer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('farmer.farmer_type_id >',1);
            $this->db->where('farmer_outlet.revision',1);
            $this->db->where('farmer_outlet.outlet_id',$data['item']['customer_id']);
            $data['farmers']=$this->db->get()->result_array();
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
}
