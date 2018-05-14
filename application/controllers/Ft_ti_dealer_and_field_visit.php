<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_ti_dealer_and_field_visit extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Ft_ti_dealer_and_field_visit');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url='ft_ti_dealer_and_field_visit';
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
        elseif($action=="add")
        {
            $this->system_add();
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
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference_all();
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

    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference();
            $data['title']="Dealer And Field Visit Pending List";
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/list");
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
        $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
        $this->db->select('dealer_field_visit.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.name outlet');
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
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('dealer_field_visit.status_attendance','Pending');
        $this->db->where('dealer_field_visit.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.revision',1);
        $this->db->order_by('dealer_field_visit.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date']=System_helper::display_date($item['date']);
        }
        $this->json_return($items);
    }

    private function get_preference()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
        $data['date']= 1;
        $data['outlet']= 1;
        $data['farmer_name']= 1;
        $data['dealer_visit_activities']= 1;
        $data['lead_farmer_visit_activities_one']= 1;
        $data['lead_farmer_visit_activities_two']= 1;
        $data['lead_farmer_visit_activities_three']= 1;
        $data['farmer_visit_activities']= 1;
        $data['other_activities']= 1;
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

    private function system_list_all()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_all();
            $data['title']="Dealer And Farmer Visit All List";
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

    public function system_get_items_all()
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
        $this->db->from($this->config->item('table_ems_ft_ti_dealer_and_field_visit').' dealer_field_visit');
        $this->db->select('dealer_field_visit.*');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.name outlet');
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
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('dealer_field_visit.status',$this->config->item('system_status_active'));
        $this->db->where('cus_info.revision',1);
        $this->db->order_by('dealer_field_visit.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date']=System_helper::display_date($item['date']);
            if($item['status_attendance']=='Pending')
            {
                $item['status_attendance']='-';
            }
        }
        $this->json_return($items);
    }

    private function get_preference_all()
    {
        $user = User_helper::get_user();
        $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list_all"'),1);
        $data['date']= 1;
        $data['outlet']= 1;
        $data['farmer_name']= 1;
        $data['dealer_visit_activities']= 1;
        $data['lead_farmer_visit_activities_one']= 1;
        $data['lead_farmer_visit_activities_two']= 1;
        $data['lead_farmer_visit_activities_three']= 1;
        $data['farmer_visit_activities']= 1;
        $data['other_activities']= 1;
        $data['status_attendance']= 1;
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

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="New Dealer And Farmer Visit";
            $data["item"] = Array(
                'id'=>0,
                'date'=>time(),
                'division_id' => '',
                'zone_id' => '',
                'territory_id' => '',
                'district_id' => '',
                'customer_id' => '',
                'farmer_id' => '',
                'dealer_visit_activities'=>'',
                'image_location_dealer_visit'=>'',
                'image_name_dealer_visit'=>'',
                'lead_farmer_visit_activities_one'=>'',
                'image_location_lead_farmer_visit_one'=>'',
                'image_name_lead_farmer_visit_one'=>'',
                'lead_farmer_visit_activities_two'=>'',
                'image_location_lead_farmer_visit_two'=>'',
                'image_name_lead_farmer_visit_two'=>'',
                'lead_farmer_visit_activities_three'=>'',
                'image_location_lead_farmer_visit_three'=>'',
                'image_name_lead_farmer_visit_three'=>'',
                'farmer_visit_activities'=>'',
                'image_location_farmer_visit'=>'',
                'image_name_farmer_visit'=>'',
                'other_activities'=>''
            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['customers']=array();
            $data['dealer_info_file']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {

                            $this->db->from($this->config->item('table_login_csetup_customer').' customer');

                            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
                            $this->db->select('cus_info.id value, cus_info.name text');
                            $this->db->where('customer.status',$this->config->item('system_status_active'));
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
                            $data['customers']=$this->db->get()->result_array();
                        }
                    }
                }
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
            $this->db->select('cus_info.district_id, cus_info.name customer_name');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->select('d.territory_id, d.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.zone_id, t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.division_id, zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');
            $this->db->where('dealer_field_visit.id',$item_id);
            $this->db->where('cus_info.revision',1);
            $data['item']=$this->db->get()->row_array();
            $data['dealer_info_file']=Query_helper::get_info($this->config->item('table_ems_setup_ft_dealer_file'),array('*'),array('farmer_id ='.$data['item']['farmer_id']));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if($data['item']['status_attendance']!='Pending')
            {
                System_helper::invalid_try('Edit',$item_id,'Invalid try to edit after taken attendance');
                $ajax['status']=false;
                $ajax['system_message']='Attendance Taken. You can not edit it.';
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Edit',$item_id,$this->config->item('system_edit_others'));
                $ajax['status']=false;
                $ajax['system_message']='You are trying to edit others file';
                $this->json_return($ajax);
            }
            $data['title']='Edit Dealer And Farmer Visit';
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['item']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['item']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['item']['territory_id']));

            $this->db->from($this->config->item('table_login_csetup_customer').' customer');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.id value, cus_info.name text');
            $this->db->where('customer.status !=',$this->config->item('system_status_delete'));
            $this->db->where('cus_info.district_id',$data['item']['district_id']);
            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            $data['customers']=$this->db->get()->result_array();

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
        if($id>0)
        {
            if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
            $result=Query_helper::get_info($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),array('*'),array('id ='.$id,'status !="'.$this->config->item('system_status_delete').'"'),1);
            $item['customer_id']=$result['customer_id'];
            $item['farmer_id']=$result['farmer_id'];
            if(!$result)
            {
                System_helper::invalid_try('Save',$id,'Id not exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        if(!$id>0)
        {
            if(!$this->check_validation())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }
        if(!$id>0)
        {
            $date=System_helper::get_time($item['date']);
            $duplicate_entry_check=Query_helper::get_info($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),array('id'),array('date ='.$date,'customer_id ='.$item['customer_id']));
            if($duplicate_entry_check)
            {
                $ajax['status']=false;
                $ajax['system_message']='You already saved information in given date. You can edit it.';
                $this->json_return($ajax);
                die();
            }
        }
        //Valid Farmer Checking
        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->select('farmer_outlet.*');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = farmer_outlet.outlet_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.district_id');
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
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('cus_info.revision',1);
        $this->db->where('farmer_outlet.revision',1);
        $result=$this->db->get()->result_array();
        $farmer_ids=array();
        foreach($result as $result)
        {
            $farmer_ids[]=$result['farmer_id'];
        }
        if(!in_array($item['farmer_id'],$farmer_ids))
        {
            System_helper::invalid_try('Save',$id,'farmer id '.$item['farmer_id'].' not assigned');
            $ajax['status']=false;
            $ajax['system_message']='You are trying to save visit information for a dealer who is not assigned to you.';
            $this->json_return($ajax);
            die();
        }
        else
        {
            $path='images/dealer_and_farmer_visit/'.$item['farmer_id'];
            $dir=(FCPATH).$path;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_files = System_helper::upload_file($path);
            if(array_key_exists('image_dealer_activities',$uploaded_files))
            {
                if($uploaded_files['image_dealer_activities']['status'])
                {
                    $item['image_name_dealer_visit']=$uploaded_files['image_dealer_activities']['info']['file_name'];
                    $item['image_location_dealer_visit']=$path.'/'.$uploaded_files['image_dealer_activities']['info']['file_name'];
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_dealer_activities']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            if(array_key_exists('image_lead_farmer_activities_one',$uploaded_files))
            {
                if($uploaded_files['image_lead_farmer_activities_one']['status'])
                {
                    $item['image_name_lead_farmer_visit_one']=$uploaded_files['image_lead_farmer_activities_one']['info']['file_name'];
                    $item['image_location_lead_farmer_visit_one']=$path.'/'.$uploaded_files['image_lead_farmer_activities_one']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_lead_farmer_activities_one']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            if(array_key_exists('image_lead_farmer_activities_two',$uploaded_files))
            {
                if($uploaded_files['image_lead_farmer_activities_two']['status'])
                {
                    $item['image_name_lead_farmer_visit_two']=$uploaded_files['image_lead_farmer_activities_two']['info']['file_name'];
                    $item['image_location_lead_farmer_visit_two']=$path.'/'.$uploaded_files['image_lead_farmer_activities_two']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_lead_farmer_activities_two']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            if(array_key_exists('image_lead_farmer_activities_three',$uploaded_files))
            {
                if($uploaded_files['image_lead_farmer_activities_three']['status'])
                {
                    $item['image_name_lead_farmer_visit_three']=$uploaded_files['image_lead_farmer_activities_three']['info']['file_name'];
                    $item['image_location_lead_farmer_visit_three']=$path.'/'.$uploaded_files['image_lead_farmer_activities_three']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_lead_farmer_activities_three']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            if(array_key_exists('image_farmer_activities',$uploaded_files))
            {
                if($uploaded_files['image_farmer_activities']['status'])
                {
                    $item['image_name_farmer_visit']=$uploaded_files['image_farmer_activities']['info']['file_name'];
                    $item['image_location_farmer_visit']=$path.'/'.$uploaded_files['image_farmer_activities']['info']['file_name'];
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_farmer_activities']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = time();
                $this->db->set('revision_count', 'revision_count+1', FALSE);
                Query_helper::update($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),$item,array("id = ".$id));
            }
            else
            {
                $item['date']=System_helper::get_time($item['date']);
                $item['date_created']=$time;
                $item['user_created']=$user->user_id;
                Query_helper::add($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),$item, true);
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
        $this->form_validation->set_rules('item[date]',$this->lang->line('LABEL_DATE'),'required');
        $this->form_validation->set_rules('item[customer_id]','Outlet','required');
        $this->form_validation->set_rules('item[farmer_id]','Dealer','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        return true;
    }

    public function get_dropdown_farmers_by_customer_id()
    {
        $outlet_id = $this->input->post('customer_id');
        $html_container_id='#farmer_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->select('farmer_outlet.farmer_id value');
        $this->db->select('CONCAT(farmer.name," - ",farmer.mobile_no) text');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
        $this->db->where('farmer.status',$this->config->item('system_status_active'));
        $this->db->where('farmer.farmer_type_id>', 1);
        $this->db->where('farmer_outlet.revision',1);
        $this->db->where('farmer_outlet.outlet_id',$outlet_id);
        $this->db->order_by('farmer.id ASC');
        $data['items']=$this->db->get()->result_array();
        if($data['items'])
        {
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
            $this->json_return($ajax);
        }
    }

    public function duplicate_entry_validation()
    {
        $date=$this->input->post('date');
        $date=System_helper::get_time($date);
        $customer_id=$this->input->post('customer_id');
        $result=Query_helper::get_info($this->config->item('table_ems_ft_ti_dealer_and_field_visit'),array('id'),array('date ='.$date,'customer_id ='.$customer_id));
        if($result)
        {
            $ajax['status']=false;
            $ajax['system_message']='You already saved information in given date. You can edit it.';
            $this->json_return($ajax);
            die();
        }
        else
        {
            $data['status']=true;
            $this->json_return($data);
        }
    }

    public function get_dealer_info_file()
    {
        $farmer_id=$this->input->post('farmer_id');
        $data['dealer_info_file']=Query_helper::get_info($this->config->item('table_ems_setup_ft_dealer_file'),array('*'),array('farmer_id ='.$farmer_id));
        if($data['dealer_info_file'])
        {
            $html_container_id='#dealer_info_file_id';
            if($this->input->post('html_container_id'))
            {
                $html_container_id=$this->input->post('html_container_id');
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view($this->controller_url."/dealer_info_file",$data,true));
            $this->json_return($ajax);
        }
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
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info_created','user_info_created.user_id=dealer_field_visit.user_created','INNER');
            $this->db->select('user_info_created.name created_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info_updated','user_info_updated.user_id=dealer_field_visit.user_updated','LEFT');
            $this->db->select('user_info_updated.name updated_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info_attendance','user_info_attendance.user_id=dealer_field_visit.user_created_attendance','LEFT');
            $this->db->select('user_info_attendance.name attendance_taken_by');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info_attendance_updated','user_info_attendance_updated.user_id=dealer_field_visit.user_updated_attendance','LEFT');
            $this->db->select('user_info_attendance_updated.name attendance_updated_by');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = dealer_field_visit.farmer_id','INNER');
            $this->db->select('farmer.name farmer_name');
            $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = dealer_field_visit.customer_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.district_id, cus_info.name customer_name');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->select('d.territory_id, d.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.zone_id, t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.division_id, zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');
            $this->db->where('user_info_created.revision',1);
            $this->db->where('cus_info.revision',1);
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
                System_helper::invalid_try('Details',$item_id,'Details');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to view details others file';
                $this->json_return($ajax);
            }
            $data['title']='Dealer And Farmer Visit Details';
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

    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference();
            $data['preference_method_name']='list';
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

    private function system_set_preference_all()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=$this->get_preference_all();
            $data['preference_method_name']='list_all';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_all');
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
