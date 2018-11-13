<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fd_budget_helper
{
    /* ARM & Upcoming varieties */
    public static function get_variety_arm_upcoming($crop_type_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_login_setup_classification_varieties'));
        $CI->db->select('id AS value, CONCAT(name, " ( ", whose, " )") AS text, crop_type_id');

        $CI->db->where('status', $CI->config->item('system_status_active'));
        $CI->db->where('whose !=', 'Competitor');
        if (is_numeric($crop_type_id) && ($crop_type_id > 0))
        {
            $CI->db->where('crop_type_id', $crop_type_id);
        }
        $CI->db->order_by('whose, ordering');
        $results = $CI->db->get()->result_array();

        $data = array();
        foreach ($results as $result)
        {
            $data[$result['crop_type_id']][] = $result;
        }
        return $data;
    }

    /* All varieties */
    public static function get_variety_all($crop_type_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_login_setup_classification_varieties'));
        $CI->db->select('id AS value, CONCAT(name, " ( ", whose, " )") AS text, crop_type_id');

        $CI->db->where('status', $CI->config->item('system_status_active'));
        if (is_numeric($crop_type_id) && ($crop_type_id > 0))
        {
            $CI->db->where('crop_type_id', $crop_type_id);
        }
        $CI->db->order_by('whose, ordering');
        $results = $CI->db->get()->result_array();

        $data = array();
        foreach ($results as $result)
        {
            $data[$result['crop_type_id']][] = $result;
        }
        return $data;
    }

    /* Outlet wise GA Dealers */
    public static function get_dealers_ga($outlet_id)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_dealers') . ' area_dealers');
        $CI->db->select('area_dealers.dealer_id');

        $CI->db->join($CI->config->item('table_pos_setup_farmer_farmer') . ' dealer', 'dealer.id = area_dealers.dealer_id', 'INNER');
        $CI->db->select('dealer.name dealer_name, dealer.mobile_no, dealer.address');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = area_dealers.area_id', 'INNER');

        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->group_by('area_dealers.dealer_id');
        $CI->db->order_by('area_dealers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Outlet wise GA Lead Farmers */
    public static function get_lead_farmers_ga($outlet_id)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers');
        $CI->db->select('lead_farmers.*, lead_farmers.id AS lead_farmers_id');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = lead_farmers.area_id', 'INNER');
        $CI->db->select('lead_farmers.*, lead_farmers.id AS lead_farmers_id');

        $CI->db->where('areas.status', $CI->config->item('system_status_active'));
        $CI->db->where('lead_farmers.status', $CI->config->item('system_status_active'));
        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->order_by('lead_farmers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }
}
