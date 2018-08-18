<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_survey_variety extends Root_Controller
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
        $this->lang->load('survey_variety');
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
            $data['title']="Arm & Competitor Variety Report Search";
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
        $this->db->where('v.whose','Competitor');
        $this->db->where('v.status =',$this->config->item('system_status_active'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['competitor_varieties']=$this->db->get()->result_array();

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
            $reports=$this->input->post('report');
            $variety_ids=$this->input->post('variety_ids');
            $reports['variety_ids']=$variety_ids;
            if(!((sizeof($variety_ids)>0)))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select at least One Variety";
                $this->json_return($ajax);
            }
            $data['options']=$reports;
            $data['title']="ARM & Competitor Variety Report";
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
        $variety_ids=$this->input->post('variety_ids');

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_ems_survey_variety_characteristics').' characteristics','characteristics.variety_id = v.id','LEFT');
        $this->db->select('characteristics.characteristics,characteristics.comparison,characteristics.remarks,characteristics.date_start1,characteristics.date_end1,characteristics.date_start2,characteristics.date_end2');

        $this->db->join($this->config->item('table_ems_survey_variety_files').' files_images','files_images.variety_id =v.id AND files_images.file_type="'.$this->config->item('system_file_type_image').'"  AND files_images.status="'.$this->config->item('system_status_active').'"' ,'LEFT');
        $this->db->select('count(DISTINCT files_images.id) number_of_images,files_images.file_location',true);

        $this->db->join($this->config->item('table_ems_survey_variety_files').' files_videos','files_videos.variety_id =v.id AND files_videos.file_type="'.$this->config->item('system_file_type_video').'" AND files_videos.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->select('count(DISTINCT files_videos.id) number_of_videos',true);

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('v.id',$variety_ids);
        $this->db->group_by('v.id');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['crop_info']=$result['crop_name'].'<br>'.$result['crop_type_name'].'<br>'.$result['variety_name'];
            $item['characteristics']=nl2br($result['characteristics']);
            $item['cultivation_period']='';
            if($result['date_start1']!=0)
            {
                $item['cultivation_period'].=''.date('d-F',$result['date_start1']).' to '.date('d-F',$result['date_end1']);
            }
            if($result['date_start2']!=0)
            {
                $item['cultivation_period'].='<br>'.date('d-F',$result['date_start2']).' to '.date('d-F',$result['date_end2']);
            }
            $image=$this->config->item('system_base_url_survey_variety').$result['file_location'];
            $item['picture']='<img src="'.$image.'" style="max-height: 100px;max-width: 133px;">';
            $item['comparison']=nl2br($result['comparison']);
            $item['remarks']=$result['remarks'];
            $item['number_of_images']=$result['number_of_images'];
            $item['number_of_videos']=$result['number_of_videos'];
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $html_container_id=$this->input->post('html_container_id');
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
            $this->db->select('v.id,v.name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->select('type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->select('crop.name crop_name');

            $this->db->where('v.id',$item_id);
            $data['item_head']=$this->db->get()->row_array();
            if(!$data['item_head'])
            {
                System_helper::invalid_try('Details',$item_id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['item_characteristics']=Query_helper::get_info($this->config->item('table_ems_survey_variety_characteristics'),'*',array('variety_id ='.$item_id),1);
            $item_files=Query_helper::get_info($this->config->item('table_ems_survey_variety_files'),'*',array('variety_id ='.$item_id,'status ="'.$this->config->item('system_status_active').'"'));
            $data['item_image']=array();
            $data['item_video']=array();
            foreach($item_files as $item_file)
            {
                if($item_file['file_type']==$this->config->item('system_file_type_image'))
                {
                    $data['item_image'][$item_file['id']]=$item_file;
                }
                else if($item_file['file_type']==$this->config->item('system_file_type_video'))
                {
                    $data['item_video'][$item_file['id']]=$item_file;
                }

            }
            $data['title']="Details ARM Variety Info Of (".$data['item_head']['name'].')';
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
}
