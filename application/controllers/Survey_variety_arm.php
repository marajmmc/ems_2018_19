<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Survey_variety_arm extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('market_survey');
    }
    public function index($action="list",$id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=='add_edit_characteristics')
        {
            $this->system_add_edit_characteristics($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
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
            $this->system_list($id);
        }
    }
    private function get_preference_headers($method)
    {
        $data['id']= 1;
        $data['name']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['characteristics']= 1;
        $data['number_of_images']= 1;
        $data['number_of_videos']= 1;
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="ARM Varieties Info";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
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
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_ems_survey_variety_arm_characteristics').' arm_characteristics','arm_characteristics.variety_id = v.id','LEFT');
        $this->db->select('arm_characteristics.characteristics');

        $this->db->join($this->config->item('table_ems_survey_variety_arm_files').' arm_files_images','arm_files_images.variety_id =v.id AND arm_files_images.file_type="'.$this->config->item('system_file_type_image').'"','LEFT');
        $this->db->select('count(DISTINCT arm_files_images.id) number_of_images',true);

        $this->db->join($this->config->item('table_ems_survey_variety_arm_files').' arm_files_videos','arm_files_videos.variety_id =v.id AND arm_files_videos.file_type="'.$this->config->item('system_file_type_video').'"','LEFT');
        $this->db->select('count(DISTINCT arm_files_videos.id) number_of_videos',true);

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where('v.whose','ARM');
        $this->db->group_by('v.id');
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            if(strlen($item['characteristics'])>0)
            {
                $item['characteristics']="Done";
            }
            else
            {
                $item['characteristics']="Not Done";
            }
        }
        $this->json_return($items);
    }
    private function system_add_edit_characteristics($id)
    {
        if($id>0)
        {
            $item_id=$id;
        }
        else
        {
            $item_id=$this->input->post('id');
        }

        if($item_id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');

        $this->db->where('v.id',$item_id);
        $this->db->where('v.whose','ARM');
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('Add_edit_characteristics',$item_id,'Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        $item=Query_helper::get_info($this->config->item('table_ems_survey_variety_arm_characteristics'),'*',array('variety_id ='.$item_id),1);
        if($item)
        {
            $data['item']=$item;
        }
        else
        {
            $data['item']['characteristics']='';
            $data['item']['comparison']='';
            $data['item']['remarks']='';
            $data['item']['remarks']='';
            $data['item']['date_start1']=time();
            $data['item']['date_end1']=time();
            $data['item']['date_start2']=0;
            $data['item']['date_end2']=0;
        }

        $data['title']="Edit ARM Variety Info for (".$data['item_head']['name'].')';
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_characteristics",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_characteristics/'.$item_id);
        $this->json_return($ajax);
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $item=$this->input->post('item');
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $variety=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),'*',array('id ='.$id,'whose ="ARM"'),1);
            if(!$variety)
            {
                System_helper::invalid_try('Save',$id,'Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $old_item=Query_helper::get_info($this->config->item('table_ems_survey_variety_arm_characteristics'),'*',array('variety_id ='.$id),1);

            $item['date_start1']=System_helper::get_time($item['date_start1'].'-1970');
            $item['date_end1']=System_helper::get_time($item['date_end1'].'-1970');
            if($item['date_end1']<$item['date_start1'])
            {
                $item['date_end1']=System_helper::get_time($this->input->post('date_end1').'-1971');
            }
            if($item['date_end1']!=0)
            {
                $item['date_end1']+=24*3600-1;
            }
            $item['date_start2']=System_helper::get_time($item['date_start2'].'-1970');
            $item['date_end2']=System_helper::get_time($item['date_end2'].'-1970');
            if($item['date_end2']<$item['date_start2'])
            {
                $item['date_end2']=System_helper::get_time($this->input->post('date_end2').'-1971');
            }
            if($item['date_end2']!=0)
            {
                $item['date_end2']+=24*3600-1;
            }

            $this->db->trans_start();  //DB Transaction Handle START

            if($old_item)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = $time;
                $this->db->set('revision_count', 'revision_count+1', FALSE);
                Query_helper::update($this->config->item('table_ems_survey_variety_arm_characteristics'),$item,array("id = ".$old_item['id']));
            }
            else
            {
                $item['variety_id'] = $id;
                $item['revision_count'] = 1;
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                Query_helper::add($this->config->item('table_ems_survey_variety_arm_characteristics'),$item);
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
    private function system_set_preference()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[characteristics]',$this->lang->line('LABEL_CHARACTERISTICS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
