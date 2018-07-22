<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        die();
    }
    public function get_current_stock()
    {
        $warehouse_id = $this->input->post('warehouse_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $variety_id = $this->input->post('variety_id');
        $html_container_id='#current_stock_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $result=Stock_helper::get_variety_stock(array($variety_id));
        $stock_current=0;
        if(isset($result[$variety_id][$pack_size_id][$warehouse_id]))
        {
            $stock_current=$result[$variety_id][$pack_size_id][$warehouse_id]['current_stock'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$stock_current);
        $this->json_return($ajax);
    }


    //Added By saiful. Need to review
    public function get_raw_current_stock()
    {
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $packing_item = $this->input->post('packing_item');
        $html_container_id='#current_stock_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $result=Stock_helper::get_raw_stock(array($variety_id));
        $stock_current=0;
        if(isset($result[$variety_id][$pack_size_id][$packing_item]))
        {
            $stock_current=$result[$variety_id][$pack_size_id][$packing_item]['current_stock'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$stock_current);
        $this->json_return($ajax);
    }

    public function get_dropdown_upazillas_by_districtid()
    {
        $district_id = $this->input->post('district_id');
        $html_container_id='#upazilla_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }
}
