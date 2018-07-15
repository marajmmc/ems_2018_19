<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_rnd_demo_setup_demo extends Root_Controller
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
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
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
        elseif($action=="save_unfilled")
        {
            $this->system_save_unfilled();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="delete")
        {
            $this->system_delete($id);
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
            $this->system_list();
        }
    }
    private function get_preference_headers()
    {
        $data['id']= 1;
        $data['pri_name']= 1;
        $data['year']= 1;
        $data['season']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
        $data['contact_no']= 1;
        $data['date_sowing']= 1;
        $data['num_visits']= 1;
        $data['interval']= 1;
        return $data;
    }
    private function get_preference($method = 'list')
    {
        $user = User_helper::get_user();
        $result = Query_helper::get_info($this->config->item('table_system_user_preference'), '*', array('user_id =' . $user->user_id, 'controller ="' . $this->controller_url . '"', 'method ="' . $method . '"'), 1);
        $data = $this->get_preference_headers($method);
        if ($result)
        {
            if ($result['preferences'] != null)
            {
                $preferences = json_decode($result['preferences'], true);
                foreach ($data as $key => $value)
                {
                    if (isset($preferences[$key]))
                    {
                        $data[$key] = $value;
                    }
                    else
                    {
                        $data[$key] = 0;
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
            $data['title']="R&D Demo Variety Setup List";
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
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
        $this->db->select('rnd_demo_setup_demo.*');
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties','rnd_demo_varieties.setup_id =rnd_demo_setup_demo.id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
        $this->db->select('crop_types.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
        $this->db->select('crops.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
        $this->db->select('seasons.name season');
        $this->db->join($this->config->item('table_ems_setup_seasons').' seasons','seasons.id =rnd_demo_setup_demo.season_id','INNER');
        $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('rnd_demo_varieties.revision',1);
        $this->db->order_by('rnd_demo_setup_demo.id','DESC');
        $this->db->group_by('rnd_demo_setup_demo.id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['pri_name']=$item['name'];
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }
        $this->json_return($items);
    }
    private function system_list_variety()
    {
        $crop_type_id=$this->input->post('crop_type_id');
        //ARM
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.whose');
        $this->db->where('v.crop_type_id',$crop_type_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view($this->controller_url."/list_variety",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create R&D Demo Variety Setup";
            $data["item"] = Array(
                'id'=>0,
                'year' => date('Y'),
                'season_id' => '',
                'crop_id'=>'',
                'type_id'=>'',
                'name'=>'',
                'address' => 'Makiks Farm - Birganj',
                'contact_no' => '',
                'date_sowing' => time(),
                'date_transplant' => '',
                'num_visits' => 10,
                'interval' => 10
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=array();
            $data['varieties']=array();
            $data['seasons']=Query_helper::get_info($this->config->item('table_ems_setup_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
        if((isset($this->permissions['action2'])&&($this->permissions['action2']==1))||(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['previous_varieties']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_varieties'),'*',array('setup_id ='.$item_id,'revision ='.'1'));
            if(!$results)
            {
                System_helper::invalid_try('Edit',$item_id,'Non-Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
            }
            $variety_id=0;
            foreach($results as $key=>$result)
            {
                if($key==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
            $this->db->select('rnd_demo_setup_demo.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->select('crop_types.id type_id,crop_types.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
            $this->db->select('crops.id crop_id,crops.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
            $this->db->select('seasons.name season');
            $this->db->join($this->config->item('table_ems_setup_seasons').' seasons','seasons.id =rnd_demo_setup_demo.season_id','INNER');
            $this->db->where('rnd_demo_setup_demo.id',$item_id);
            $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['types']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['item']['crop_id'],'status !="'.$this->config->item('system_status_delete').'"'));
            $data['varieties']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['item']['type_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('whose ASC','ordering ASC'));
            $data['seasons']=Query_helper::get_info($this->config->item('table_ems_setup_seasons'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['title']="Edit R&D Demo Variety Setup";
            $ajax['status']=true;
            if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            }
            elseif(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_unfilled",$data,true));
            }
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
            $item['date_sowing']=System_helper::get_time($item['date_sowing']);
            $item['date_transplant']=System_helper::get_time($item['date_transplant']);

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $item['user_updated'] = $user->user_id;
                $item['date_updated'] = $time;
                Query_helper::update($this->config->item('table_ems_ft_rnd_demo_setup_demo'),$item,array("id = ".$id));
            }
            else
            {
                $item['user_created'] = $user->user_id;
                $item['date_created'] = $time;
                $setup_id=Query_helper::add($this->config->item('table_ems_ft_rnd_demo_setup_demo'),$item);
                if($setup_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                    die();
                }
                else
                {
                    $id=$setup_id;
                }
            }

            $previous_varieties=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_varieties'),'*',array('setup_id ='.$id,'revision ='.'1'));
            foreach($results as $result)
            {
                $previous_varieties[$result['variety_id']]=$result;
            }

            $data=array();
            $data['date_updated']=$time;
            $data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_ems_ft_rnd_demo_varieties'),$data,array('revision=1','setup_id='.$id));
            $this->db->where('setup_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_ft_rnd_demo_varieties'));

            $variety_ids=$this->input->post('variety_ids');
            foreach($variety_ids as $variety_id)
            {
                $data=array();
                $data['setup_id']=$id;
                $data['variety_id']=$variety_id;
                $data['revision']=1;
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                Query_helper::add($this->config->item('table_ems_ft_rnd_demo_varieties'),$data);
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
    private function system_save_unfilled()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item=$this->input->post('item');
        if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        $item['date_transplant']=System_helper::get_time($item['date_transplant']);
        $this->db->trans_start();  //DB Transaction Handle START
        $item['user_updated'] = $user->user_id;
        $item['date_updated'] = $time;
        Query_helper::update($this->config->item('table_ems_ft_rnd_demo_setup_demo'),$item,array("id = ".$id));
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

            $data['previous_varieties']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_varieties'),'*',array('setup_id ='.$item_id,'revision ='.'1'));
            if(!$results)
            {
                System_helper::invalid_try('Details',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->json_return($ajax);
            }
            $variety_id=0;
            foreach($results as $key=>$result)
            {
                if($key==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
            $this->db->select('rnd_demo_setup_demo.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->select('crop_types.id type_id,crop_types.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_types','crop_types.id =v.crop_type_id','INNER');
            $this->db->select('crops.name crop_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crops','crops.id =crop_types.crop_id','INNER');
            $this->db->select('seasons.name season');
            $this->db->join($this->config->item('table_ems_setup_seasons').' seasons','seasons.id =rnd_demo_setup_demo.season_id','INNER');
            $this->db->where('rnd_demo_setup_demo.id',$item_id);
            $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Details',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created']]=$data['item']['user_created'];
            $user_ids[$data['item']['user_updated']]=$data['item']['user_updated'];
            $data['users']=System_helper::get_users_info($user_ids);

            $data['previous_varieties']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_varieties'),'*',array('setup_id ='.$item_id,'revision ='.'1'));
            foreach($results as $result)
            {
                $data['previous_varieties'][$result['variety_id']]=$result;
            }
            $data['title']="Detail R&D Demo Variety Setup";
            $data['varieties']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['item']['type_id'],'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('whose ASC','ordering ASC'));
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
    private function system_delete($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $user = User_helper::get_user();
            $time = time();
            $item_head=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_demo'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$item_id),1);
            if(!$item_head)
            {
                System_helper::invalid_try('Delete',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['user_updated']=$user->user_id;
            $data['date_updated']=$time;
            $data['status']=$this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_ems_ft_rnd_demo_setup_demo'),$data,array('id='.$item_id));
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status()===true)
            {
                $this->message=$this->lang->line("MSG_DELETED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_SAVED_FAIL');
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[year]',$this->lang->line('LABEL_YEAR'),'required|numeric');
        $this->form_validation->set_rules('item[season_id]',$this->lang->line('LABEL_SEASON'),'required|numeric');
        $this->form_validation->set_rules('crop_id',$this->lang->line('LABEL_CROP_NAME'),'required');
        $this->form_validation->set_rules('crop_type_id',$this->lang->line('LABEL_CROP_TYPE_NAME'),'required');
        $this->form_validation->set_rules('item[name]',"PRI's Name",'required');
        $this->form_validation->set_rules('item[date_sowing]',$this->lang->line('LABEL_DATE_SOWING'),'required');
        $this->form_validation->set_rules('item[num_visits]',$this->lang->line('LABEL_NUM_VISITS'),'required|numeric');
        $this->form_validation->set_rules('item[interval]',$this->lang->line('LABEL_INTERVAL'),'required|numeric');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $variety_ids=$this->input->post('variety_ids');
        if(!((sizeof($variety_ids)>0)))
        {
            $this->message="Please Select at least One Variety";
            return false;
        }

        return true;
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
}
