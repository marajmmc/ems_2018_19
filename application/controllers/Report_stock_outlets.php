<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock_outlets extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->locations=User_helper::get_locations();
        $this->user=User_helper::get_user();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->controller_url = strtolower(get_class($this));
        $this->lang->load('report_sale_lang');
    }


    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items_stock_current")
        {
            $this->system_get_items_stock_current();
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
            $data['title']="Showrooms Stock Report";
            $ajax['status']=true;
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['outlets']=array();
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
                            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id=customer.id','INNER');
                            $this->db->select('customer.id value, cus_info.name text');
                            $this->db->where('customer.status',$this->config->item('system_status_active'));
                            $this->db->where('cus_info.district_id',$this->locations['district_id']);
                            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
                            $this->db->where('cus_info.revision',1);
                            $data['outlets']=$this->db->get()->result_array();
                        }
                    }

                }
            }

            $fiscal_years=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

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
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['report_name']!='stock_current')
            {
                if($reports['date_start']>=$reports['date_end'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Starting Date should be less than End date';
                    $this->json_return($ajax);
                }
            }

            $data['options']=$reports;

            $ajax['status']=true;
            if($reports['report_name']=='stock_current')
            {
                if($reports['outlet_id']>0)
                {
                    $data['areas']=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$reports['outlet_id'],'revision =1'));
                    $data['title']='Showroom Stock Report';
                }
                elseif($reports['district_id']>0)
                {

                    $data['areas']=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('district_id ='.$reports['district_id'],'revision =1','type ="'.$this->config->item('system_customer_type_outlet_id').'"'));
                    $data['title']='Showrooms Stock Report';
                }
                elseif($reports['territory_id']>0)
                {
                    $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$reports['territory_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    $data['title']='Districts Stock Report';
                }
                elseif($reports['zone_id']>0)
                {
                    $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$reports['zone_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    $data['title']='Territories Stock Report';
                }
                elseif($reports['division_id']>0)
                {
                    $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$reports['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                    $data['title']='Zones Stock Report';
                }
                else
                {
                    $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
                    $data['title']='Divisions Stock Report';
                }
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_stock_current",$data,true));
            }
            elseif($reports['report_name']=='stock_details')
            {
                //$data['title']="Product Sales Report";
                //$ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_variety_sale",$data,true));
                $ajax['status']=false;
                $ajax['system_message']='Under Development';
                $this->json_return($ajax);
            }
            else
            {
                $this->message='Invalid Report type';
            }

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

    private function system_get_items_stock_current()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $outlet_id=$this->input->post('outlet_id');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        if($outlet_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('customer_id ='.$outlet_id,'revision =1'));
            $location_type='outlet_id';
        }
        elseif($district_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),array('customer_id value','name text'),array('district_id ='.$district_id,'revision =1','type ="'.$this->config->item('system_customer_type_outlet_id').'"'));
            $location_type='outlet_id';
        }
        elseif($territory_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='district_id';
        }
        elseif($zone_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='territory_id';
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='zone_id';
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $location_type='division_id';
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.id','ASC');

        $varieties=$this->db->get()->result_array();
        $variety_ids=array();
        $variety_ids[0]=0;
        foreach($varieties as $result)
        {
            $variety_ids[$result['variety_id']]=$result['variety_id'];
        }

        $pack_sizes=array();
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_pack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        foreach($results as $result)
        {
            $pack_sizes[$result['value']]=$result['text'];
        }


        $this->db->from($this->config->item('table_pos_stock_summary_variety').' stock_summary_variety');
        $this->db->select('stock_summary_variety.variety_id,stock_summary_variety.pack_size_id');
        $this->db->select('SUM(stock_summary_variety.current_stock) current_stock',false);

        $this->db->select('stock_summary_variety.outlet_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id = stock_summary_variety.outlet_id and outlet_info.revision =1','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = outlet_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($outlet_id>0)
                        {
                            $this->db->where('stock_summary_variety.outlet_id',$outlet_id);
                        }
                    }
                }
            }
        }
        if($pack_size_id>0 && is_numeric($pack_size_id))
        {
            $this->db->where('stock_summary_variety.pack_size_id',$pack_size_id);
        }
        $this->db->group_by(array($location_type));
        $this->db->group_by('stock_summary_variety.variety_id');
        $this->db->group_by('stock_summary_variety.pack_size_id');
        $results=$this->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']][$result[$location_type]]=$result['current_stock'];
        }
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_stock_current($areas,'','','Total Type','');
        $crop_total=$this->initialize_row_stock_current($areas,'','Total Crop','','');
        $grand_total=$this->initialize_row_stock_current($areas,'Grand Total','','','');
        foreach($varieties as $variety)
        {
            if(isset($stocks[$variety['variety_id']]))
            {
                foreach($stocks[$variety['variety_id']] as $pack_size_id=>$stock_in_details)
                {
                    $info=$this->initialize_row_stock_current($areas,$variety['crop_name'],$variety['crop_type_name'],$variety['variety_name'],$pack_sizes[$pack_size_id]);
                    if(!$first_row)
                    {
                        if($prev_crop_name!=$variety['crop_name'])
                        {
                            $items[]=$this->get_row_stock_current($type_total);
                            $items[]=$this->get_row_stock_current($crop_total);
                            $type_total=$this->reset_row_stock_current($type_total);
                            $crop_total=$this->reset_row_stock_current($crop_total);

                            $prev_crop_name=$variety['crop_name'];
                            $prev_type_name=$variety['crop_type_name'];

                        }
                        elseif($prev_type_name!=$variety['crop_type_name'])
                        {
                            $items[]=$this->get_row_stock_current($type_total);
                            $type_total=$this->reset_row_stock_current($type_total);
                            $info['crop_name']='';
                            $prev_type_name=$variety['crop_type_name'];
                        }
                        else
                        {
                            $info['crop_name']='';
                            $info['crop_type_name']='';
                        }
                    }
                    else
                    {
                        $prev_crop_name=$variety['crop_name'];
                        $prev_type_name=$variety['crop_type_name'];
                        $first_row=false;
                    }
                    foreach($stock_in_details as  $area_id=>$quantity)
                    {
                        $info['stock_'.$area_id.'_pkt']=$quantity;
                        $info['stock_total_pkt']+=$quantity;
                        $info['stock_'.$area_id.'_kg']=$quantity*$pack_sizes[$pack_size_id]/1000;
                        $info['stock_total_kg']+=($quantity*$pack_sizes[$pack_size_id]/1000);
                    }
                    foreach($info as $key=>$r)
                    {
                        if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                        {
                            $type_total[$key]+=$info[$key];
                            $crop_total[$key]+=$info[$key];
                            $grand_total[$key]+=$info[$key];
                        }
                    }
                    $items[]=$this->get_row_stock_current($info);
                }
            }

        }
        $items[]=$this->get_row_stock_current($type_total);
        $items[]=$this->get_row_stock_current($crop_total);
        $items[]=$this->get_row_stock_current($grand_total);
        $this->json_return($items);
        $this->json_return($items);


    }
    private function initialize_row_stock_current($areas,$crop_name,$crop_type_name,$variety_name,$pack_size)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['pack_size']=$pack_size;
        $row['stock_total_pkt']=0;
        $row['stock_total_kg']=0;
        foreach($areas as $area)
        {
            $row['stock_'.$area['value'].'_pkt']=0;
            $row['stock_'.$area['value'].'_kg']=0;
        }
        return $row;
    }
    private function reset_row_stock_current($info)
    {
        foreach($info as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }
    private function get_row_stock_current($info)
    {
        $row=array();
        foreach($info as $key=>$r)
        {
            if(substr($key,-3)=='pkt')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=$info[$key];
                }
            }
            elseif(substr($key,-2)=='kg')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],3,'.','');
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }
        }
        return $row;
    }
}
