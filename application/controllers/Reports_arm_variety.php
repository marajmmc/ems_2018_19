<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_arm_variety extends Root_Controller
{
    public  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('survey_variety_arm');
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
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
            $data['title']="Arm Variety Report Search";
            $ajax['status']=true;
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array(),0,0,array('ordering ASC'));
            $data['types']=array();
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
    private function system_list_variety()
    {
        $reports=$this->input->post('report');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        if($reports['crop_id']>0)
        {
            $this->db->where('crop.id',$reports['crop_id']);
            if($reports['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$reports['crop_type_id']);
            }
        }

        $this->db->where('v.whose','ARM');
        $this->db->where('v.status =',$this->config->item('system_status_active'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['arm_varieties']=$this->db->get()->result_array();

        $data['report']=$reports;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));

        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $variety_ids=$this->input->post('variety_ids');
            if(!((sizeof($variety_ids)>0)))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select at lease One Variety";
                $this->json_return($ajax);
            }
            $data['options']=$variety_ids;
            $data['title']="ARM  Variety Report ";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $this->json_return($items);
    }
}
