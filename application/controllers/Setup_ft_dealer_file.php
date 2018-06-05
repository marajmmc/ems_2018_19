<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_ft_dealer_file extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_ft_dealer_file');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url='setup_ft_dealer_file';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
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
            $data['title']="Dealer File Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
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
        $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
        $this->db->select('farmer_outlet.outlet_id, farmer_outlet.farmer_id id');
        $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = farmer_outlet.outlet_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.district_id, cus_info.name outlet');
        $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
        $this->db->select('farmer.name farmer_name, farmer.mobile_no');
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
        $this->db->where('farmer.status',$this->config->item('system_status_active'));
        $this->db->where('farmer_outlet.revision',1);
        $this->db->where('farmer.farmer_type_id>', 1);
        $this->db->order_by('division.id,zone.id,t.id,d.id,customer.id,farmer.id');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_edit($farmer_id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$farmer_id;
            }
            $this->db->from($this->config->item('table_pos_setup_farmer_outlet').' farmer_outlet');
            $this->db->select('farmer_outlet.outlet_id, farmer_outlet.farmer_id');
            $this->db->join($this->config->item('table_login_csetup_customer').' customer','customer.id = farmer_outlet.outlet_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
            $this->db->select('cus_info.district_id, cus_info.name outlet');
            $this->db->join($this->config->item('table_pos_setup_farmer_farmer').' farmer','farmer.id = farmer_outlet.farmer_id','INNER');
            $this->db->select('farmer.name farmer_name');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->select('d.territory_id, d.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->select('t.zone_id, t.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->select('zone.division_id, zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->select('division.name division_name');
            $this->db->join($this->config->item('table_ems_setup_ft_dealer_file').' dealer_file','dealer_file.farmer_id = farmer_outlet.farmer_id','LEFT');
            $this->db->select('dealer_file.farmer_id id');
            $this->db->where('farmer_outlet.farmer_id',$item_id);
            $this->db->where('farmer_outlet.revision',1);
            $data['item_head']=$this->db->get()->row_array();
            if(!($data['item_head']))
            {
                System_helper::invalid_try('Edit',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_my_editable($data['item_head']))
            {
                System_helper::invalid_try('Edit','Trying to edit others file');
                $ajax['status']=false;
                $ajax['system_message']='You are trying to edit others file';
                $this->json_return($ajax);
            }
            $this->db->from($this->config->item('table_ems_setup_ft_dealer_file').' dealer_file');
            $this->db->select('dealer_file.*');
            $this->db->where('dealer_file.farmer_id',$item_id);
            $data['items']=$this->db->get()->result_array();
            $data['title']='Setup Dealer File';
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
            $results=Query_helper::get_info($this->config->item('table_ems_setup_ft_dealer_file'),array('*'),array('farmer_id ='.$id,'status !="'.$this->config->item('system_status_delete').'"'));
            $item_old=array();
            foreach($results as $result)
            {
                $item_old[$result['id']]=$result;

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
            $ajax['system_message']='You are trying to save file for a dealer who is not assigned to you.';
            $this->json_return($ajax);
            die();
        }
        else
        {
            $allowed_types='pdf|doc|docx|xls|xlsx';
            $path='images/setup_dealer_file/'.$item['farmer_id'];
            $dir=(FCPATH).$path;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_files = System_helper::upload_file($path,$allowed_types);
            $this->db->trans_start();  //DB Transaction Handle START
            $files=$this->input->post('files');
            $old_files=$this->input->post('old_files');
            if($uploaded_files)
            {
                if($old_files)
                {
                    foreach($old_files as $old_file)
                    {
                        if(isset($uploaded_files['file_'.$old_file]))
                        {
                            if($uploaded_files['file_'.$old_file]['status'])
                            {
                                $item['image_name']=$uploaded_files['file_'.$old_file]['info']['file_name'];
                                $item['image_location']=$path.'/'.$uploaded_files['file_'.$old_file]['info']['file_name'];
                                $item['user_updated'] = $user->user_id;
                                $item['date_updated'] = $time;
                                Query_helper::update($this->config->item('table_ems_setup_ft_dealer_file'),$item,array("id = ".$old_file,"farmer_id = ".$id));
                            }
                            else
                            {
                                $ajax['status']=false;
                                $ajax['system_message']=$uploaded_files['file_'.$old_file]['message'];
                                $this->json_return($ajax);
                                die();
                            }
                        }
                    }
                }
                if($files)
                {
                    foreach($files as $key=>$file)
                    {
                        if(isset($uploaded_files['file_'.$key]))
                        {
                            if($uploaded_files['file_'.$key]['status'])
                            {
                                $item['image_name']=$uploaded_files['file_'.$key]['info']['file_name'];
                                $item['image_location']=$path.'/'.$uploaded_files['file_'.$key]['info']['file_name'];
                                $item['date_created']=$time;
                                $item['user_created']=$user->user_id;
                                Query_helper::add($this->config->item('table_ems_setup_ft_dealer_file'),$item, true);
                            }
                            else
                            {
                                $ajax['status']=false;
                                $ajax['system_message']=$uploaded_files['file_'.$key]['message'];
                                $this->json_return($ajax);
                                die();
                            }
                        }
                    }
                }
            }
            else
            {
                if(!$id>0)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Upload at least one file';
                    $this->json_return($ajax);
                    die();
                }
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

}
