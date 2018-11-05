<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fd_budget_helper extends Root_Controller
{
    /* ARM & other Competitor varieties */
    public static function get_dropdown_all_crop_variety()
    {
        $CI =& get_instance();
        $results = Query_helper::get_info($CI->config->item('table_login_setup_classification_varieties'), array('id value', 'CONCAT(name, " ( ", whose, " )") text', 'crop_type_id'), array('status !="' . $CI->config->item('system_status_delete') . '"'), 0, 0, array('whose', 'ordering'));
        $data = array();
        foreach ($results as $result)
        {
            $data[$result['crop_type_id']][] = $result;
        }
        return $data;
    }

    /* Outlet wise Dealers */
    public static function get_all_area_dealers_by_outlet_id($id)
    {
        $CI =& get_instance();
        if ($id > 0)
        {
            $outlet_id = $id;
        }
        else
        {
            $outlet_id = $CI->input->post('outlet_id');
        }
        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_dealers') . ' dealers');
        $CI->db->select('dealers.*');

        $CI->db->join($CI->config->item('table_pos_setup_farmer_farmer') . ' farmer', 'farmer.id = dealers.dealer_id', 'INNER');
        $CI->db->select('farmer.name dealer_name, farmer.mobile_no, farmer.address');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = dealers.dealer_id', 'INNER');
        $CI->db->where('farmer.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('dealers.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('farmer.farmer_type_id >', 1);
        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->order_by('dealers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }
}
