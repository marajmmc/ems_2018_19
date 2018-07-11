<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_rnd_demo_picture extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="load_crops")
        {
            $this->system_load_crops();
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
            $data['title']="Search";
            $ajax['status']=true;
            $data['years']=Query_helper::get_info($this->config->item('table_ems_ft_farmers'),array('Distinct(year)'),array());
            $data['crops']=array();
            $data['seasons']=Query_helper::get_info($this->config->item('table_ems_setup_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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

        $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
        $this->db->select('rnd_demo_varieties.variety_id');
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties','rnd_demo_varieties.setup_id =rnd_demo_setup_demo.id','INNER');
        $this->db->select('v.name variety_name,v.whose');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_ems_setup_seasons').' season','season.id =rnd_demo_setup_demo.season_id','INNER');
        if($reports['crop_id']>0)
        {
            $this->db->where('crop.id',$reports['crop_id']);
            if($reports['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$reports['crop_type_id']);
            }
        }
        if($reports['season_id']>0)
        {
            $this->db->where('rnd_demo_setup_demo.season_id',$reports['season_id']);
        }
        if($reports['year']>0)
        {
            $this->db->where('rnd_demo_setup_demo.year',$reports['year']);

        }
        $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('rnd_demo_varieties.revision',1);
        $this->db->order_by('v.ordering','DESC');
        $this->db->group_by('rnd_demo_varieties.variety_id');
        $results=$this->db->get()->result_array();
        $data['arm_varieties']=array();
        $data['competitor_varieties']=array();
        $data['upcoming_varieties']=array();
        foreach($results as $result)
        {
            if($result['whose']=='ARM')
            {
                $data['arm_varieties'][]=$result;
            }
            elseif($result['whose']=='Competitor')
            {
                $data['competitor_varieties'][]=$result;
            }
            elseif($result['whose']=='Upcoming')
            {
                $data['upcoming_varieties'][]=$result;
            }
        }
        $data['report']=$reports;
        $ajax['status']=true;
        $ajax['system_content'][]=array('id'=>'#variety_list_container','html'=>$this->load->view($this->controller_url.'/list_variety',$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_load_crops()
    {
        $filters=$this->input->post('report');
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties','rnd_demo_varieties.setup_id =rnd_demo_setup_demo.id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->select('crop.name text,crop.id value');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_ems_setup_seasons').' season','season.id =rnd_demo_setup_demo.season_id','INNER');
        if($filters['season_id']>0)
        {
            $this->db->where('rnd_demo_setup_demo.season_id',$filters['season_id']);
        }
        if($filters['year']>0)
        {
            $this->db->where('rnd_demo_setup_demo.year',$filters['year']);
        }
        $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('rnd_demo_varieties.revision',1);
        $this->db->order_by('crop.ordering','DESC');
        $this->db->group_by('crop.id');
        $data['items']=$this->db->get()->result_array();
        $html_container_id='#crop_id';
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
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
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['max_visits']=1;
            $data['max_diseases']=1;
            $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
            $this->db->join($this->config->item('table_ems_setup_seasons').' season','season.id =rnd_demo_setup_demo.season_id','INNER');
            $this->db->select('Max(rnd_demo_picture.day_no) num_visit_done');
            $this->db->join($this->config->item('table_ems_ft_rnd_demo_picture').' rnd_demo_picture','rnd_demo_setup_demo.id =rnd_demo_picture.setup_id','LEFT');
            if($reports['season_id']>0)
            {
                $this->db->where('rnd_demo_setup_demo.season_id',$reports['season_id']);

            }
            if($reports['year']>0)
            {
                $this->db->where('rnd_demo_setup_demo.year',$reports['year']);
            }
            $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
            $this->db->where_in('rnd_demo_picture.variety_id',$variety_ids);
            $result=$this->db->get()->row_array();
            if($result)
            {
                if($result['num_visit_done']>0)
                {
                    $data['max_visits']=$result['num_visit_done'];
                }

            }

            $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
            $this->db->join($this->config->item('table_ems_setup_seasons').' season','season.id =rnd_demo_setup_demo.season_id','INNER');
            $this->db->join($this->config->item('table_ems_ft_rnd_demo_disease_picture').' rnd_demo_disease_picture','rnd_demo_setup_demo.id =rnd_demo_disease_picture.setup_id','LEFT');
            if($reports['season_id']>0)
            {
                $this->db->where('rnd_demo_setup_demo.season_id',$reports['season_id']);

            }
            if($reports['year']>0)
            {
                $this->db->where('rnd_demo_setup_demo.year',$reports['year']);

            }
            $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('rnd_demo_disease_picture.status',$this->config->item('system_status_active'));
            $this->db->where_in('rnd_demo_disease_picture.variety_id',$variety_ids);
            $results=$this->db->get()->result_array();

            $demo_disease_picture=array();
            foreach($results as $result)
            {
                $demo_disease_picture[$result['setup_id']][]=$result;
            }
            foreach($demo_disease_picture as $picture)
            {
                $counter=count($picture);
                if(count($picture)>$data['max_diseases'])
                {
                    $data['max_diseases']=$counter;
                }
            }

            $data['title']="R&D Demo Picture Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_report_container','html'=>$this->load->view($this->controller_url.'/list',$data,true));
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
    public function system_get_items()
    {
        $items=array();
        $user_ids=array();
        $year=$this->input->post('year');
        $season_id=$this->input->post('season_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_ids=$this->input->post('variety_ids');

        //Getting setup demo data
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_setup_demo').' rnd_demo_setup_demo');
        $this->db->select('rnd_demo_setup_demo.*');
        $this->db->select('season.name season_name');
        $this->db->join($this->config->item('table_ems_ft_rnd_demo_varieties').' rnd_demo_varieties','rnd_demo_setup_demo.id =rnd_demo_varieties.setup_id','INNER');
        $this->db->select('v.name variety_name,v.id variety_id');
        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id =rnd_demo_varieties.variety_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_ems_setup_seasons').' season','season.id =rnd_demo_setup_demo.season_id','INNER');
        if($season_id>0)
        {
            $this->db->where('rnd_demo_setup_demo.season_id',$season_id);
        }
        if($year>0)
        {
            $this->db->where('rnd_demo_setup_demo.year',$year);
        }
        $this->db->where('rnd_demo_setup_demo.status !=',$this->config->item('system_status_delete'));
        $this->db->where('rnd_demo_varieties.revision',1);
        $this->db->where_in('rnd_demo_varieties.variety_id',$variety_ids);
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','DESC');
        $results=$this->db->get()->result_array();

        //Storing setup_ids in new array
        $setup_ids=array();
        foreach($results as $result)
        {
            $item['setup_id']=$result['id'];
            $item['variety_id']=$result['variety_id'];
            $setup_ids[$result['id']]=$result['id'];
            $item['year_season']=$result['year'].'<br>'.$result['season_name'].'<br>'.$this->lang->line('LABEL_DATE_SOWING').':'.System_helper::display_date($result['date_sowing']);
            $item['crop_info']=$result['variety_name'].'<br>'.$result['crop_type_name'].'<br>'.$result['crop_name'];
            $item['location']='';
            $item['date_sowing']=$result['date_sowing'];
            $item['interval']=$result['interval'];
            $items[]=$item;
        }

        //Getting rnd demo picture
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_picture').' rnd_demo_picture');
        $this->db->select('rnd_demo_picture.*');
        $this->db->where_in('rnd_demo_picture.setup_id',$setup_ids);
        $results=$this->db->get()->result_array();
        $visit_infos=array();
        foreach($results as $result)
        {
            $visit_infos[$result['setup_id']][$result['variety_id']][$result['day_no']]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
        }

        //Getting fruit picture
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_fruit_picture').' rnd_demo_fruit_picture');
        $this->db->select('rnd_demo_fruit_picture.*');
        $this->db->where_in('rnd_demo_fruit_picture.setup_id',$setup_ids);
        $this->db->order_by('rnd_demo_fruit_picture.picture_id','ASC');
        $results=$this->db->get()->result_array();
        $fruit_infos=array();
        foreach($results as $result)
        {
            $fruit_infos[$result['setup_id']][$result['variety_id']][$result['picture_id']]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
        }

        //Getting disease picture
        $this->db->from($this->config->item('table_ems_ft_rnd_demo_disease_picture').' rnd_demo_disease_picture');
        $this->db->select('rnd_demo_disease_picture.*');
        $this->db->where_in('rnd_demo_disease_picture.setup_id',$setup_ids);
        $this->db->where('rnd_demo_disease_picture.status !=',$this->config->item('system_status_delete'));
        $results=$this->db->get()->result_array();
        $disease_infos=array();
        foreach($results as $result)
        {
            $disease_infos[$result['setup_id']][$result['variety_id']][]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
        }
        $users=System_helper::get_users_info($user_ids);

//        foreach($items as $i=>&$item)
//        {
//            if(isset($visit_infos[$item['setup_id']][$item['variety_id']]))
//            {
//                foreach($visit_infos[$item['setup_id']][$item['variety_id']] as $visit)
//                {
//                    $image=base_url().'images/no_image.jpg';
//                    if(strlen($visit['picture_url'])>0)
//                    {
//                        $image=$visit['picture_url'];
//                    }
//                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="visit_pictures_'.$visit['day_no'].'" style="height: 125px;width: 133px;cursor:pointer;">';
//                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
//                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($item['date_sowing']+24*3600*$visit['day_no']*$item['interval']).'</div>';
//                    $html_row.='</div>';
//                    $item['visit_pictures_'.$visit['day_no']]=$html_row;
//                    $html_tooltip='';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
//                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($item['date_sowing']+24*3600*$visit['day_no']*$item['interval']).'</div>';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
//                    $html_tooltip.='</div>';
//
//                    $html_tooltip.='</div>';
//                    $item['details']['visit_pictures_'.$visit['day_no']]=$html_tooltip;
//                }
//            }
//            if(isset($fruit_infos[$item['setup_id']][$item['variety_id']]))
//            {
//                foreach($fruit_infos[$item['setup_id']][$item['variety_id']] as $visit)
//                {
//                    $image=base_url().'images/no_image.jpg';
//                    if(strlen($visit['picture_url'])>0)
//                    {
//                        $image=$visit['picture_url'];
//                    }
//                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="fruit_pictures_'.$visit['picture_id'].'" style="height: 125px;width: 133px;cursor:pointer;">';
//                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
//                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($visit['date_created']).'</div>';
//                    $html_row.='</div>';
//                    $item['fruit_pictures_'.$visit['picture_id']]=$html_row;
//                    $html_tooltip='';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
//                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($visit['date_created']).'</div>';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
//                    $html_tooltip.='</div>';
//
//                    $html_tooltip.='</div>';
//                    $item['details']['fruit_pictures_'.$visit['picture_id']]=$html_tooltip;
//                }
//            }
//            if(isset($disease_infos[$item['setup_id']][$item['variety_id']]))
//            {
//                foreach($disease_infos[$item['setup_id']][$item['variety_id']] as $index=>$visit)
//                {
//                    $image=base_url().'images/no_image.jpg';
//                    if(strlen($visit['picture_url'])>0)
//                    {
//                        $image=$visit['picture_url'];
//                    }
//                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="disease_pictures_'.$index.'" style="height: 125px;width: 133px;cursor:pointer;">';
//                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
//                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($visit['date_created']).'</div>';
//                    $html_row.='</div>';
//                    $item['disease_pictures_'.$index]=$html_row;
//                    $html_tooltip='';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
//                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($visit['date_created']).'</div>';
//                    $html_tooltip.='<div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
//                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
//                    $html_tooltip.='</div>';
//
//                    $html_tooltip.='</div>';
//                    $item['details']['disease_pictures_'.$index]=$html_tooltip;
//                }
//            }
//        }

        $items=array();
        $this->json_return($items);
    }
}
