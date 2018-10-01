<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_rnd_demo_setup_fruit_picture extends Root_Controller
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
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Fruit Picture Setup List";
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
        $items=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),array('id','name','ordering','image_location','image_name'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
        foreach($items as &$item)
        {
            $image=$this->config->item('system_base_url_ft_rnd_demo_setup_fruit_picture').$item['image_location'];
            $item['picture']='<img src="'.$image.'" style="max-height: 100px;max-width: 133px;">';
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Fruit Picture Setup";
            $data["item"] = Array(
                'id'=>0,
                'name'=>'',
                'image_location'=>'images/no_image.jpg',
                'image_name'=>'no_image.jpg',
                'ordering' => 99
            );
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
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
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['title']="Edit Fruit Picture Setup (".$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
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
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $path='images/ft_rnd_demo_setup_fruit_picture';
//            $dir=(FCPATH).$path;
//            if(!is_dir($dir))
//            {
//                mkdir($dir, 0777);
//            }
            $uploaded_files = System_helper::upload_file($path);
            if(array_key_exists('image',$uploaded_files))
            {
                if($uploaded_files['image']['status'])
                {
                    $item['image_name']=$uploaded_files['image']['info']['file_name'];
                    $item['image_location']=$path.'/'.$uploaded_files['image']['info']['file_name'];
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image']['message'];
                    $this->json_return($ajax);
                    die();
                }
            }
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = $time;
                Query_helper::update($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),$item,array("id = ".$id));
            }
            else
            {
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                Query_helper::add($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),$item);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
