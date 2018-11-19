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
    public static function get_dealers_growing_area($outlet_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_dealers') . ' area_dealers');
        $CI->db->select('area_dealers.dealer_id, area_dealers.dealer_id AS value');

        $CI->db->join($CI->config->item('table_pos_setup_farmer_farmer') . ' dealer', 'dealer.id = area_dealers.dealer_id', 'INNER');
        $CI->db->select('dealer.name AS dealer_name, dealer.name AS text, dealer.mobile_no AS phone_no');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = area_dealers.area_id', 'INNER');

        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->group_by('area_dealers.dealer_id');
        $CI->db->order_by('area_dealers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Outlet wise GA Lead Farmers */
    public static function get_lead_farmers_growing_area($outlet_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers');
        $CI->db->select('lead_farmers.id AS lead_farmers_id, lead_farmers.id AS value');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = lead_farmers.area_id', 'INNER');
        $CI->db->select('lead_farmers.name AS lead_farmers_name, lead_farmers.name AS text, lead_farmers.mobile_no  AS phone_no');

        $CI->db->where('areas.status', $CI->config->item('system_status_active'));
        $CI->db->where('lead_farmers.status', $CI->config->item('system_status_active'));
        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->order_by('lead_farmers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Budget ID wise Update History */
    public static function get_fd_budget_history($budget_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_fd_budget') . ' fd_budget');
        $CI->db->select('fd_budget.date_proposal, fd_budget.status_budget_forward');

        $CI->db->join($CI->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $CI->db->select('fd_budget_details.*');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $CI->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'INNER');
        $CI->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $CI->db->select('crop_type.name AS crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $CI->db->select('crop.name AS crop_name');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $CI->config->item('system_customer_type_outlet_id'), 'INNER');
        $CI->db->select('cus_info.name AS outlet_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $CI->db->select('district.name AS district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.name AS territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.name AS zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.name AS division_name');

        $CI->db->where('fd_budget.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('fd_budget.id', $budget_id);
        $CI->db->order_by('fd_budget_details.revision');
        $results = $CI->db->get()->result_array();

        foreach ($results as &$result)
        {
            $user_ids = array($result['user_created'] => $result['user_created']); // Getting Name of Created By
            $result['user_info'] = System_helper::get_users_info($user_ids);

            $result_data = Fd_budget_helper::get_dealers_growing_area($result['outlet_id']);
            $dealers_by_outlet = array();
            foreach ($result_data as $item)
            {
                $dealers_by_outlet[$item['dealer_id']] = $item;
            }
            $result_data = Fd_budget_helper::get_lead_farmers_growing_area($result['outlet_id']);
            $lead_farmers_by_outlet = array();
            foreach ($result_data as $item)
            {
                $lead_farmers_by_outlet[$item['lead_farmers_id']] = $item;
            }

            $result_data = json_decode($result['participants_dealer_farmer'], TRUE);
            $result['dealers'] = array();
            foreach ($result_data['dealer_participant'] as $key => $value)
            {
                if (isset($dealers_by_outlet[$key]) && ($value > 0))
                {
                    $dealers_by_outlet[$key]['participant'] = $value;
                    $result['dealers'][] = $dealers_by_outlet[$key];
                }
            }
            $result['lead_farmers'] = array();
            foreach ($result_data['farmer_participant'] as $key => $value)
            {
                if (isset($lead_farmers_by_outlet[$key]) && ($value > 0))
                {
                    $lead_farmers_by_outlet[$key]['participant'] = $value;
                    $result['lead_farmers'][] = $lead_farmers_by_outlet[$key];
                }
            }

            $result_expense_items = Query_helper::get_info($CI->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));
            $budget_result = json_decode($result['amount_expense_items'], TRUE);
            $result['expense_items'] = array();
            foreach ($result_expense_items as &$item)
            {
                if ((isset($budget_result[$item['id']])) && ($budget_result[$item['id']] > 0))
                {
                    if ($item['status'] == $CI->config->item('system_status_inactive'))
                    {
                        $item['name'] = $item['name'] . ' <b>(' . $item['status'] . ')</b>';
                    }
                    $item['amount'] = $budget_result[$item['id']];
                    $result['expense_items'][] = $item;
                }
            }
        }

        return $results;
    }
}
