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
    public static function get_dealers_ga($outlet_id = 0)
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
    public static function get_lead_farmers_ga($outlet_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers');
        $CI->db->select('lead_farmers.id AS lead_farmers_id');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = lead_farmers.area_id', 'INNER');
        $CI->db->select('lead_farmers.name, lead_farmers.mobile_no, lead_farmers.address');

        $CI->db->where('areas.status', $CI->config->item('system_status_active'));
        $CI->db->where('lead_farmers.status', $CI->config->item('system_status_active'));
        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->order_by('lead_farmers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Budget ID wise Update History */
    public static function get_fd_budget_history($view_location, $budget_id = 0)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_fd_budget') . ' fd_budget');
        $CI->db->select('fd_budget.date_proposal, fd_budget.status_budget');

        $CI->db->join($CI->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $CI->db->select('fd_budget_details.*');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
        $CI->db->select('variety1.name AS variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'INNER');
        $CI->db->select('variety2.name AS variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $CI->db->select('crop_type.name AS crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $CI->db->select('crop.name AS crop_name');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $CI->config->item('system_customer_type_outlet_id'), 'INNER');
        $CI->db->select('cus_info.name AS outlet_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $CI->db->select('district.name AS district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.name AS territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.name AS zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.name AS division_name');

        $CI->db->where('fd_budget.status_budget !=', $CI->config->item('system_status_delete'));
        $CI->db->where('fd_budget.id', $budget_id);
        //$CI->db->where('fd_budget_details.revision', 1);
        $CI->db->order_by('fd_budget_details.revision');
        $results = $CI->db->get()->result_array();

        $data = array();
        $data['items'] = $results;
        //pr($results);

        $html = $CI->load->view($view_location, $data, true);
        return $html;

        /*$data = array();
        $data['item'] = Array(
            'id' => $result['budget_id'],
            'date_proposal' => System_helper::display_date($result['date_proposal']),
            'crop_name' => $result['crop_name'],
            'crop_type_name' => $result['crop_type_name'],
            'variety1_name' => $result['variety1_name'],
            'variety2_name' => $result['variety2_name'],
            'division_name' => $result['division_name'],
            'zone_name' => $result['zone_name'],
            'territory_name' => $result['territory_name'],
            'district_name' => $result['district_name'],
            'outlet_name' => $result['outlet_name'],
            'address' => $result['address'],
            'present_condition' => $result['present_condition'],
            'farmers_evaluation' => $result['farmers_evaluation'],
            'diff_between_varieties' => $result['diff_between_varieties'],
            'date_expected' => System_helper::display_date($result['date_expected']),
            'participant_customers' => $result['participant_customers'],
            'participant_others' => $result['participant_others'],
            'quantity_market_size_total' => $result['quantity_market_size_total'],
            'quantity_market_size_arm' => $result['quantity_market_size_arm'],
            'quantity_sales_target' => $result['quantity_sales_target'],
            'remarks_budget' => $result['remarks_budget']
        );

        $result_data = Fd_budget_helper::get_dealers_ga($result['outlet_id']);
        $data['dealers_by_outlet'] = array();
        foreach ($result_data as $item)
        {
            $data['dealers_by_outlet'][$item['dealer_id']] = $item;
        }
        $result_data = Fd_budget_helper::get_lead_farmers_ga($result['outlet_id']);
        $data['lead_farmers_by_outlet'] = array();
        foreach ($result_data as $item)
        {
            $data['lead_farmers_by_outlet'][$item['lead_farmers_id']] = $item;
        }

        $result_data = json_decode($result['participants_dealer_farmer'], TRUE);
        foreach ($result_data['dealer_participant'] as $key => $value)
        {
            if (!($value > 0))
            {
                unset($data['dealers_by_outlet'][$key]);
            }
            else
            {
                $data['dealers_by_outlet'][$key]['participant'] = $value;
            }
        }
        foreach ($result_data['farmer_participant'] as $key => $value)
        {
            if (!($value > 0))
            {
                unset($data['lead_farmers_by_outlet'][$key]);
            }
            else
            {
                $data['lead_farmers_by_outlet'][$key]['participant'] = $value;
            }
        }

        $data['expense_items'] = Query_helper::get_info($CI->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));
        $budget_result = json_decode($result['amount_expense_items'], TRUE);
        foreach ($data['expense_items'] as $key => &$item)
        {
            if ($item['status'] == $CI->config->item('system_status_inactive'))
            {
                $item['name'] = $item['name'] . ' <b>(' . $item['status'] . ')</b>';
            }

            if (!($budget_result[$item['id']] > 0))
            {
                unset($data['expense_items'][$key]);
            }
            else
            {
                $item['amount'] = $budget_result[$item['id']];
            }
        }

        $results = Query_helper::get_info($CI->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $budget_id, 'revision=1', 'status !="' . $CI->config->item('system_status_deleted') . '"'));
        if (sizeof($results) > 0)
        {
            $data['picture_categories'] = Query_helper::get_info($CI->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
            foreach ($results as $result)
            {
                $data['file_details'][$result['category_id']] = array(
                    'file_location_variety1' => $result['file_location_variety1'],
                    'remarks_variety1' => $result['remarks_variety1'],
                    'file_location_variety2' => $result['file_location_variety2'],
                    'remarks_variety2' => $result['remarks_variety2'],
                );
            }
        }
        else
        {
            $data['picture_categories'] = Query_helper::get_info($CI->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $CI->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $data['file_details'] = array();
        }*/
    }
}
