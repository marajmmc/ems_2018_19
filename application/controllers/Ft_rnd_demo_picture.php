<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ft_rnd_demo_picture extends Root_Controller
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
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
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
            $this->system_list();
        }
    }
    private function get_preference_headers()
    {
        $data['pri_name']= 1;
        $data['year']= 1;
        $data['season']= 1;
        $data['crop_name']= 1;
        $data['crop_type_name']= 1;
//        $data['contact_no']= 1;
        $data['date_sowing']= 1;
        $data['num_visits']= 1;
        $data['interval']= 1;
        $data['num_visit_done']= 1;
//        $data['num_fruit_picture']= 1;
        $data['num_disease_picture']= 1;
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
            $data['title']="R&D Demo Picture List";
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
        $this->db->select('count(distinct rnd_demo_picture.day_no) num_visit_done',true);
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_picture').' rnd_demo_picture','rnd_demo_setup_demo.id =rnd_demo_picture.setup_id','LEFT');
        $this->db->select('count(distinct fruit_picture.picture_id) num_fruit_picture',false);
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_fruit_picture').' fruit_picture','rnd_demo_setup_demo.id =fruit_picture.setup_id','LEFT');
        $this->db->select('count(distinct case when disease_picture.status="Active" then disease_picture.id end) num_disease_picture',false);
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_disease_picture').' disease_picture','rnd_demo_setup_demo.id =disease_picture.setup_id','LEFT');
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
    private function system_edit($id)
    {
        if((isset($this->permissions['action1'])&&($this->permissions['action1']==1))||(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
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
            $this->db->from($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties');
            $this->db->select('rnd_demo_varieties.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
            $this->db->where('rnd_demo_varieties.setup_id',$item_id);
            $this->db->where('rnd_demo_varieties.revision',1);
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
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
                System_helper::invalid_try('Edit',$item_id,'Id Not Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }
            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_fruit_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_disease_picture'),'*',array('setup_id ='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('id'));
            $data['title']="Edit R&D Demo Picture";
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
        if(!((isset($this->permissions['action2'])&&($this->permissions['action2']==1))||(isset($this->permissions['action1'])&&($this->permissions['action1']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }

        $result_setup_demo=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_demo'),'*',array('status !="'.$this->config->item('system_status_delete').'"','id ='.$id),1);
        if(!$result_setup_demo)
        {
            System_helper::invalid_try('Save',$id,'Non-existing');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try.';
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties');
        $this->db->select('rnd_demo_varieties.*');
        $this->db->select('v.name variety_name,v.whose');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
        $this->db->where('rnd_demo_varieties.setup_id',$id);
        $this->db->where('rnd_demo_varieties.revision',1);
        $this->db->order_by('v.whose ASC');
        $this->db->order_by('v.ordering ASC');
        $previous_varieties=$this->db->get()->result_array();

        $path='images/ft_rnd_demo_picture/'.$id;
        $dir=(FCPATH).$path;
        if(!is_dir($dir))
        {
            mkdir($dir, 0777);
        }
        $uploaded_files = System_helper::upload_file($path);
        foreach($uploaded_files as $file)
        {
            if(!$file['status'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$file['message'];
                $this->json_return($ajax);
                die();
            }
        }
        $visits_picture=array();
        $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_picture'),'*',array('setup_id ='.$id));
        foreach($results as $result)
        {
            $visits_picture[$result['day_no']][$result['variety_id']]=$result;
        }
        $visit_remarks=$this->input->post('visit_remarks');

        $fruits_picture_headers=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
        $fruits_picture=array();
        $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_fruit_picture'),'*',array('setup_id ='.$id));
        foreach($results as $result)
        {
            $fruits_picture[$result['picture_id']][$result['variety_id']]=$result;
        }
        $fruit_remarks=$this->input->post('fruit_remarks');

        $this->db->trans_start();
        for($i=1;$i<=$result_setup_demo['num_visits'];$i++)
        {
            foreach($previous_varieties as $variety)
            {
                $data=array();
                if(isset($visit_remarks[$i][$variety['variety_id']]))
                {
                    if((strlen($visit_remarks[$i][$variety['variety_id']]))>0)
                    {
                        $data['remarks']=$visit_remarks[$i][$variety['variety_id']];
                    }
                    elseif(isset($visits_picture[$i][$variety['variety_id']]))
                    {
                        $data['remarks']='';
                    }
                }
                if(isset($uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]))
                {
                    $data['image_name']=$uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]['info']['file_name'];
                    $data['image_location']=$path.'/'.$uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]['info']['file_name'];
                }
                if($data)
                {
                    if(isset($visits_picture[$i][$variety['variety_id']]))
                    {
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        Query_helper::update($this->config->item('table_ems_ft_rnd_demo_picture'),$data,array("id = ".$visits_picture[$i][$variety['variety_id']]['id']));
                    }
                    else
                    {
                        $data['setup_id'] = $id;
                        $data['day_no'] = $i;
                        $data['variety_id'] = $variety['variety_id'];
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        Query_helper::add($this->config->item('table_ems_ft_rnd_demo_picture'),$data);
                    }
                }

            }
        }
        foreach($fruits_picture_headers as $header)
        {
            foreach($previous_varieties as $variety)
            {
                $data=array();
                if(isset($fruit_remarks[$header['id']][$variety['variety_id']]))
                {
                    if((strlen($fruit_remarks[$header['id']][$variety['variety_id']]))>0)
                    {
                        $data['remarks']=$fruit_remarks[$header['id']][$variety['variety_id']];
                    }

                    elseif(isset($fruits_picture[$header['id']][$variety['variety_id']]))
                    {
                        $data['remarks']='';
                    }
                }
                if(isset($uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]))
                {
                    $data['image_name']=$uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]['info']['file_name'];
                    $data['image_location']=$path.'/'.$uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]['info']['file_name'];
                }
                if($data)
                {
                    if(isset($fruits_picture[$header['id']][$variety['variety_id']]))
                    {
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        Query_helper::update($this->config->item('table_ems_ft_rnd_demo_fruit_picture'),$data,array("id = ".$fruits_picture[$header['id']][$variety['variety_id']]['id']));
                    }
                    else
                    {
                        $data['setup_id'] = $id;
                        $data['picture_id'] = $header['id'];
                        $data['variety_id'] = $variety['variety_id'];;
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        Query_helper::add($this->config->item('table_ems_ft_rnd_demo_fruit_picture'),$data);
                    }
                }

            }
        }
        $this->db->where('setup_id',$id);
        $this->db->set('status', $this->config->item('system_status_delete'));
        $this->db->update($this->config->item('table_ems_ft_rnd_demo_disease_picture'));

        $diseases=$this->input->post('disease');
        if(sizeof($diseases)>0)
        {
            foreach($diseases as $i=>$disease)
            {
                $data=array();
                $data['remarks']=$disease['remarks'];
                if(isset($uploaded_files['disease_image_'.$i]))
                {
                    $data['image_name']=$uploaded_files['disease_image_'.$i]['info']['file_name'];
                    $data['image_location']=$path.'/'.$uploaded_files['disease_image_'.$i]['info']['file_name'];
                }
                if($disease['id']>0)
                {
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::update($this->config->item('table_ems_ft_rnd_demo_disease_picture'),$data,array("id = ".$disease['id']));
                }
                else
                {
                    $data['setup_id'] = $id;
                    $data['variety_id'] = $disease['variety_id'];
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::add($this->config->item('table_ems_ft_rnd_demo_disease_picture'),$data);
                }
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
            $this->db->from($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties');
            $this->db->select('rnd_demo_varieties.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
            $this->db->where('rnd_demo_varieties.setup_id',$item_id);
            $this->db->where('rnd_demo_varieties.revision',1);
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            if(!$results)
            {
                System_helper::invalid_try('Details',$item_id,'Non-Exists');
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

            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_fruit_picture'),'*',array('setup_id ='.$item_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_disease_picture'),'*',array('setup_id ='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),0,0,array('id'));
            $data['users']=System_helper::get_users_info(array());
            $data['title']="Details R&D Demo Picture";
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
}
