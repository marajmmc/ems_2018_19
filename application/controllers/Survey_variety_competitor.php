<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Survey_variety_competitor extends Root_Controller
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
        $this->lang->load('survey_variety');
    }
    public function index($action="list",$id=0,$id1='',$id2=0)
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
        elseif($action=="save_characteristics")
        {
            $this->system_save_characteristics();
        }
        elseif($action=="list_image")
        {
            $this->system_list_file($id,$this->config->item('system_file_type_image'));
        }
        elseif($action=="list_video")
        {
            $this->system_list_file($id,$this->config->item('system_file_type_video'));
        }
        elseif($action=="get_items_files")
        {
            $this->system_get_items_files($id);
        }
        elseif($action=='add_edit_image')
        {
            $this->system_add_edit_file($id,$this->config->item('system_file_type_image'),$id1);
        }
        elseif($action=='add_edit_video')
        {
            $this->system_add_edit_file($id,$this->config->item('system_file_type_video'),$id1);
        }
        elseif($action=="save_file")
        {
            $this->system_save_file();
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
            $this->system_list($id);
        }
    }
    private function get_preference_headers($method)
    {
        if($method=='list')
        {
            $data['id']= 1;
            $data['name']= 1;
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['characteristics']= 1;
            $data['number_of_images']= 1;
            $data['number_of_videos']= 1;
        }
        else if($method=='list_file')
        {
            $data['id']= 1;
            $data['file_name']= 1;
            $data['remarks']= 1;
            $data['status']= 1;
        }
        else
        {
            $data=array();
        }
        return $data;
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Competitor Varieties Info";
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

        $this->db->join($this->config->item('table_ems_survey_variety_characteristics').' characteristics','characteristics.variety_id = v.id','LEFT');
        $this->db->select('characteristics.characteristics');

        $this->db->join($this->config->item('table_ems_survey_variety_files').' files_images','files_images.variety_id =v.id AND files_images.file_type="'.$this->config->item('system_file_type_image').'"  AND files_images.status="'.$this->config->item('system_status_active').'"' ,'LEFT');
        $this->db->select('count(DISTINCT files_images.id) number_of_images',true);

        $this->db->join($this->config->item('table_ems_survey_variety_files').' files_videos','files_videos.variety_id =v.id AND files_videos.file_type="'.$this->config->item('system_file_type_video').'" AND files_videos.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->select('count(DISTINCT files_videos.id) number_of_videos',true);

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where('v.whose','Competitor');
        $this->db->group_by('v.id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if($item['characteristics'])
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
    private function system_add_edit_characteristics($variety_id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($variety_id>0)
            {
                $item_id=$variety_id;
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
            $this->db->where('v.whose','Competitor');
            $data['item_head']=$this->db->get()->row_array();
            if(!$data['item_head'])
            {
                System_helper::invalid_try('Add_edit_characteristics',$item_id,'Variety Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $item=Query_helper::get_info($this->config->item('table_ems_survey_variety_characteristics'),'*',array('variety_id ='.$item_id),1);
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

            $data['title']="Edit Competitor Variety Characteristics (".$data['item_head']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_characteristics",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_characteristics/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

    }
    private function system_save_characteristics()
    {
        $variety_id = $this->input->post("id");
        $item=$this->input->post('item');
        $user = User_helper::get_user();
        $time=time();
        if(!((isset($this->permissions['action1'])&&($this->permissions['action1']==1))||(isset($this->permissions['action2'])&&($this->permissions['action2']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation_characteristics())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $variety=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),'*',array('id ='.$variety_id,'whose ="Competitor"'),1);
            if(!$variety)
            {
                System_helper::invalid_try('save_characteristics',$variety_id,'Variety Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $old_item=Query_helper::get_info($this->config->item('table_ems_survey_variety_characteristics'),'*',array('variety_id ='.$variety_id),1);

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
                Query_helper::update($this->config->item('table_ems_survey_variety_characteristics'),$item,array("id = ".$old_item['id']));
            }
            else
            {
                $item['variety_id'] = $variety_id;
                $item['revision_count'] = 1;
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                Query_helper::add($this->config->item('table_ems_survey_variety_characteristics'),$item);
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
    private function system_list_file($variety_id,$file_type)
    {
        $user = User_helper::get_user();
        $method = 'list_file';
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($variety_id>0)
            {
                $item_id=$variety_id;
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
            $this->db->where('v.whose','Competitor');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('list_file '.$file_type,$item_id,'Variety Id Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $data['file_type']=$this->config->item('system_file_type_image');
                $data['title']="Image setup list (Variety: ".$data['item']['name'].')';
            }
            else
            {
                $data['file_type']=$this->config->item('system_file_type_video');
                $data['title']="Video setup list (Variety: ".$data['item']['name'].')';
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_file",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $ajax['system_page_url']=site_url($this->controller_url.'/index/list_image/'.$item_id);
            }
            else
            {
                $ajax['system_page_url']=site_url($this->controller_url.'/index/list_video/'.$item_id);
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
    private function system_get_items_files()
    {
        $variety_id=$this->input->post('variety_id');
        $file_type=$this->input->post('file_type');

        $this->db->from($this->config->item('table_ems_survey_variety_files').' files');
        $this->db->select('files.*');
        $this->db->where('files.variety_id',$variety_id);
        if($file_type==$this->config->item('system_file_type_image'))
        {
            $this->db->where('files.file_type',$this->config->item('system_file_type_image'));
        }
        else
        {
            $this->db->where('files.file_type',$this->config->item('system_file_type_video'));
        }
        $this->db->where('files.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add_edit_file($variety_id,$file_type,$file_id=0)
    {
        if($file_id>0)
        {
            $item_id=$file_id;//edit by refresh
        }
        elseif(($this->input->post('id'))>0)
        {
            $item_id=$this->input->post('id');//edit from list
        }
        else
        {
            $item_id=0;//new
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
        $this->db->select('v.id variety_id,v.name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');

        $this->db->where('v.id',$variety_id);
        $this->db->where('v.whose','Competitor');
        $data['item_head']=$this->db->get()->row_array();
        if(!$data['item_head'])
        {
            System_helper::invalid_try('Add_edit_image',$variety_id,'Variety Id Non-Exists');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }

        if($item_id>0)
        {
            $this->db->from($this->config->item('table_ems_survey_variety_files').' files');
            $this->db->select('files.*');
            $this->db->where('files.id',$item_id);
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $this->db->where('files.file_type',$this->config->item('system_file_type_image'));
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                $this->db->where('files.file_type',$this->config->item('system_file_type_video'));
            }

            $this->db->where('files.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                if($file_type==$this->config->item('system_file_type_image'))
                {
                    System_helper::invalid_try('Edit_file(image)',$item_id,'File Id Non-Exists');
                }
                else if($file_type==$this->config->item('system_file_type_video'))
                {
                    System_helper::invalid_try('Edit_file(video)',$item_id,'File Id Non-Exists');
                }

                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $data['title']='Edit Image Of Variety ('.$data['item_head']['name'].')';
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                $data['title']='Edit Video Of Variety ('.$data['item_head']['name'].')';
            }

        }
        else
        {
            $data['item']=array(
                'id'=>0,
                'remarks'=>'',
                'status'=>$this->config->item('system_status_active')
            );
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $data['item']['file_name']='no_image.jpg';
                $data['item']['file_location']='images/no_image.jpg';
                $data['title']='Add Image Of Variety ('.$data['item_head']['name'].')';
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                $data['item']['file_name']='';
                $data['item']['file_location']='';
                $data['title']='Add Video Of Variety ('.$data['item_head']['name'].')';
            }

        }
        $data['file_type']=$file_type;

        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_file",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        if($file_type==$this->config->item('system_file_type_image'))
        {
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_image/'.$variety_id.'/'.$item_id);
        }
        else if($file_type==$this->config->item('system_file_type_video'))
        {
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_video/'.$variety_id.'/'.$item_id);
        }

        $this->json_return($ajax);
    }
    private function system_save_file()
    {
        $id = $this->input->post("id");
        $item=$this->input->post('item');
        $file_type=$item['file_type'];
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_survey_variety_files').' files');
            $this->db->select('files.*');
            $this->db->where('files.id',$id);
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $this->db->where('files.file_type',$this->config->item('system_file_type_image'));
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                $this->db->where('files.file_type',$this->config->item('system_file_type_video'));
            }

            $this->db->where('files.status !=',$this->config->item('system_status_delete'));
            $file_info=$this->db->get()->row_array();
            if(!$file_info)
            {
                if($file_type==$this->config->item('system_file_type_image'))
                {
                    System_helper::invalid_try('Save_file(image)',$id,'File Id Non-Exists');
                }
                else if($file_type==$this->config->item('system_file_type_video'))
                {
                    System_helper::invalid_try('Save_file(video)',$id,'File Id Non-Exists');
                }

                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
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
        if(!$this->check_validation_file_info())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->where('v.id',$item['variety_id']);
        $this->db->where('v.whose','Competitor');
        $variety_info=$this->db->get()->row_array();
        if(!$variety_info)
        {
            if($file_type==$this->config->item('system_file_type_image'))
            {
                System_helper::invalid_try('Save_file(image)',$item['variety_id'],'Variety Id Non-Exists');
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                System_helper::invalid_try('Save_file(video)',$item['variety_id'],'Variety Id Non-Exists');
            }
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        $path='images/survey_variety/'.$item['variety_id'];
//        $dir=(FCPATH).$path;
//        if(!is_dir($dir))
//        {
//            mkdir($dir, 0777);
//        }
        $uploaded_files=array();
        if($file_type==$this->config->item('system_file_type_video'))
        {
            $uploaded_files = System_helper::upload_file($path,$this->config->item('system_file_type_video_ext'),$this->config->item('system_file_type_video_max_size'));
        }
        else if($file_type==$this->config->item('system_file_type_image'))
        {
            $uploaded_files = System_helper::upload_file($path);
        }
        if(!($id>0))
        {
            if(!$uploaded_files)
            {
                $ajax['status']=false;
                if($file_type==$this->config->item('system_file_type_image'))
                {
                    $ajax['system_message']='The Picture field is required';
                }
                else if($file_type==$this->config->item('system_file_type_video'))
                {
                    $ajax['system_message']='The Video field is required';
                }
                $this->json_return($ajax);
                die();
            }
        }
        if(array_key_exists('file_name',$uploaded_files))
        {
            if($uploaded_files['file_name']['status'])
            {
                $item['file_name']=$uploaded_files['file_name']['info']['file_name'];
                $item['file_location']=$path.'/'.$uploaded_files['file_name']['info']['file_name'];
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$uploaded_files['file_name']['message'];
                $this->json_return($ajax);
                die();
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            $item['user_updated'] = $user->user_id;
            $item['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_survey_variety_files'),$item,array("id = ".$id));
        }
        else
        {
            $item['user_created'] = $user->user_id;
            $item['date_created'] = $time;
            $item['revision_count'] = 1;
            Query_helper::add($this->config->item('table_ems_survey_variety_files'),$item);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($file_type==$this->config->item('system_file_type_image'))
            {
                $this->system_list_file($item['variety_id'],$this->config->item('system_file_type_image'));
            }
            else if($file_type==$this->config->item('system_file_type_video'))
            {
                $this->system_list_file($item['variety_id'],$file_type=$this->config->item('system_file_type_video'));
            }

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
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
            $this->db->where('v.whose','Competitor');
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
            $data['title']="Details Competitor Variety Info Of (".$data['item_head']['name'].')';
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
    private function check_validation_characteristics()
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
    private function check_validation_file_info()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status]',$this->lang->line('LABEL_STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
