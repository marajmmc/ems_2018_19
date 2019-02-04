<?php
defined('BASEPATH') OR exit('No direct script access allowed');

CONST FD_IMAGE_DISPLAY_STYLE = 'max-height:200px';
CONST FD_NO_IMAGE_PATH = 'images/no_image.jpg';
/* ------FD Budget Status Constants----- */
CONST FD_BUDGET_FORWARDED = 1;
CONST FD_BUDGET_NOT_FORWARDED = 2;

/* ------FD Recommendation Status Constants----- */
CONST FD_RECOMMENDATION_FORWARDED = 3;
CONST FD_RECOMMENDATION_NOT_FORWARDED = 4;
CONST FD_BUDGET_REJECTED_ZI = 5;
CONST FD_BUDGET_NOT_REJECTED_ZI = 6;

/* ------FD Approve & Reject Status Constants----- */
CONST FD_BUDGET_APPROVED = 7;
CONST FD_BUDGET_NOT_APPROVED = 8;
CONST FD_BUDGET_REJECTED_DI = 9;
CONST FD_BUDGET_NOT_REJECTED_DI = 10;

/* ------FD Payment Status Constants----- */
CONST FD_PAYMENT_APPROVED = 11;
CONST FD_PAYMENT_NOT_APPROVED = 12;
CONST FD_PAYMENT_PAID = 13;
CONST FD_PAYMENT_NOT_PAID = 14;

/* ------FD Reporting Status Constants----- */
CONST FD_REPORTING_FORWARDED = 15;
CONST FD_REPORTING_NOT_FORWARDED = 16;
CONST FD_REPORTING_APPROVED = 17;
CONST FD_REPORTING_NOT_APPROVED = 18;


class Fd_budget_helper
{
    public static function get_fd_budget_by_id($item_id, $method_name = '')
    {
        $CI = & get_instance();

        $CI->db->from($CI->config->item('table_ems_fd_budget') . ' fd_budget');
        $CI->db->select('fd_budget.date_proposal, fd_budget.status_budget_forward, fd_budget.status_recommendation, fd_budget.status_approve, fd_budget.status_payment_approve, fd_budget.status_payment_pay, fd_budget.status_reporting_forward');

        $CI->db->join($CI->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id AND fd_budget_details.revision = 1', 'INNER');
        $CI->db->select('fd_budget_details.*');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $CI->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
        $CI->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $CI->db->select('crop_type.id AS crop_type_id, crop_type.name AS crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $CI->db->select('crop.id AS crop_id, crop.name AS crop_name');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
        $CI->db->select('cus_info.name AS outlet_name');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = fd_budget_details.growing_area_id', 'LEFT');
        $CI->db->select('CONCAT_WS(" - ", areas.name, areas.address) AS growing_area_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $CI->db->select('district.id AS district_id, district.name AS district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.id AS territory_id, territory.name AS territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.id AS zone_id, zone.name AS zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.id AS division_id, division.name AS division_name');

        $CI->db->where('fd_budget.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('fd_budget.id', $item_id);
        $result = $CI->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try($method_name, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $CI->json_return($ajax);
        }
        if (!Fd_budget_helper::check_my_editable($result))
        {
            System_helper::invalid_try($method_name, $item_id, 'Trying to View or, Edit Information of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to View or, Edit Information of other Location';
            $CI->json_return($ajax);
        }
        return $result;
    }

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
    public static function get_dealers_growing_area($outlet_id)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_dealers') . ' area_dealers');
        $CI->db->select('area_dealers.dealer_id, area_dealers.dealer_id AS value');

        $CI->db->join($CI->config->item('table_pos_setup_farmer_farmer') . ' dealer', 'dealer.id = area_dealers.dealer_id', 'INNER');
        $CI->db->select('dealer.name AS dealer_name, dealer.name AS text, dealer.mobile_no AS phone_no');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = area_dealers.area_id', 'INNER');
        $CI->db->select('areas.id AS ga_id, areas.name AS ga_name');

        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->group_by('area_dealers.dealer_id');
        $CI->db->order_by('areas.id', 'ASC');
        $CI->db->order_by('area_dealers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Outlet wise GA Lead Farmers */
    public static function get_lead_farmers_growing_area($outlet_id)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers');
        $CI->db->select('lead_farmers.id AS lead_farmers_id, lead_farmers.id AS value, lead_farmers.name AS lead_farmers_name, lead_farmers.name AS text, lead_farmers.mobile_no  AS phone_no');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = lead_farmers.area_id', 'INNER');
        $CI->db->select('areas.id AS ga_id, areas.name AS ga_name');

        $CI->db->where('areas.status', $CI->config->item('system_status_active'));
        $CI->db->where('lead_farmers.status', $CI->config->item('system_status_active'));
        $CI->db->where('areas.outlet_id', $outlet_id);
        $CI->db->order_by('areas.id', 'ASC');
        $CI->db->order_by('lead_farmers.ordering', 'ASC');
        $result = $CI->db->get()->result_array();
        return $result;
    }

    /* Budget ID wise Update History */
    public static function get_fd_budget_history($budget_id)
    {
        $CI =& get_instance();

        $CI->db->from($CI->config->item('table_ems_fd_budget') . ' fd_budget');
        $CI->db->select('fd_budget.date_proposal, fd_budget.status_budget_forward');

        $CI->db->join($CI->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $CI->db->select('fd_budget_details.*');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
        $CI->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
        $CI->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $CI->db->select('crop_type.name AS crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $CI->db->select('crop.name AS crop_name');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
        $CI->db->select('cus_info.name AS outlet_name');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = fd_budget_details.growing_area_id', 'LEFT');
        $CI->db->select('CONCAT_WS(" - ", areas.name, areas.address) AS growing_area_name');

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

        $expense_items = Query_helper::get_info($CI->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));

        foreach ($results as &$result)
        {
            $user_ids = array($result['user_created'] => $result['user_created']); // Getting Name of Created By
            if ($result['user_rollback_zi'] > 0) // Getting Name of ZI Rollback By
            {
                $user_ids[$result['user_rollback_zi']] = $result['user_rollback_zi'];
            }
            if ($result['user_rollback_di'] > 0) // Getting Name of DI Rollback By
            {
                $user_ids[$result['user_rollback_di']] = $result['user_rollback_di'];
            }
            $result['user_info'] = System_helper::get_users_info($user_ids);

            $result['dealers'] = Fd_budget_helper::get_dealers_growing_area($result['outlet_id']);
            $result['lead_farmers'] = Fd_budget_helper::get_lead_farmers_growing_area($result['outlet_id']);
            $result_data = json_decode($result['participants_dealer_farmer'], TRUE);

            foreach ($result['dealers'] as $key => $value)
            {
                if (isset($result_data['dealer_participant'][$value['dealer_id']]))
                {
                    $result['dealers'][$key]['participant'] = $result_data['dealer_participant'][$value['dealer_id']];
                }
                else
                {
                    unset($result['dealers'][$value['dealer_id']]);
                }
            }
            foreach ($result['lead_farmers'] as $key => $value)
            {
                if (isset($result_data['farmer_participant'][$value['lead_farmers_id']]))
                {
                    $result['lead_farmers'][$key]['participant'] = $result_data['farmer_participant'][$value['lead_farmers_id']];
                }
                else
                {
                    unset($result['lead_farmers'][$value['lead_farmers_id']]);
                }
            }

            $result['expense_items'] = $expense_items;
            $result_data = json_decode($result['amount_expense_items'], TRUE);
            foreach ($result['expense_items'] as $key => $value)
            {
                if (isset($result_data[$value['id']]))
                {
                    $result['expense_items'][$key]['amount'] = $result_data[$value['id']];
                }
                else
                {
                    $result['expense_items'][$key]['amount'] = 0;
                }

                if ($value['status'] == $CI->config->item('system_status_inactive'))
                {
                    $result['expense_items'][$key]['name'] = $value['name'] . ' <b>(' . $value['status'] . ')</b>';
                }
            }
        }

        return $results;
    }

    public static function get_fd_budget_details_data($item_id)
    {
        $CI =& get_instance();
        $data = array();

        $CI->db->from($CI->config->item('table_ems_fd_budget') . ' fd_budget');
        $CI->db->select('fd_budget.date_proposal,
                                fd_budget.participant_total,
                                fd_budget.amount_budget_total,
                                fd_budget.status,
                                fd_budget.date_created budget_date_created,
                                fd_budget.user_created budget_user_created,
                                fd_budget.remarks_delete,
                                fd_budget.date_deleted,
                                fd_budget.user_deleted,
                                fd_budget.status_budget_forward,
                                fd_budget.remarks_budget_forward,
                                fd_budget.date_budget_forwarded,
                                fd_budget.user_budget_forwarded,
                                fd_budget.status_recommendation,
                                fd_budget.remarks_recommendation,
                                fd_budget.date_recommendation,
                                fd_budget.user_recommendation,
                                fd_budget.status_approve,
                                fd_budget.remarks_approve,
                                fd_budget.date_approved,
                                fd_budget.user_approved,
                                fd_budget.status_payment_approve,
                                fd_budget.remarks_payment_approve,
                                fd_budget.date_payment_approved,
                                fd_budget.user_payment_approved,
                                fd_budget.status_payment_pay,
                                fd_budget.remarks_payment_pay,
                                fd_budget.date_payment_paid,
                                fd_budget.user_payment_paid');

        $CI->db->join($CI->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $CI->db->select('fd_budget_details.*');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $CI->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
        $CI->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $CI->db->select('crop_type.id AS crop_type_id, crop_type.name AS crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $CI->db->select('crop.id AS crop_id, crop.name AS crop_name');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
        $CI->db->select('cus_info.name AS outlet_name');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = fd_budget_details.growing_area_id', 'LEFT');
        $CI->db->select('CONCAT_WS(" - ", areas.name, areas.address) AS growing_area_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $CI->db->select('district.id AS district_id, district.name AS district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.id AS territory_id, territory.name AS territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.id AS zone_id, zone.name AS zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.id AS division_id, division.name AS division_name');

        $CI->db->where('fd_budget.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('fd_budget.id', $item_id);
        $CI->db->where('fd_budget_details.revision', 1);
        $result = $CI->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $CI->json_return($ajax);
        }
        $data['item'] = $result;
        $data['info_basic'] = array();

        //---------Getting User Names------------
        $user_ids = array(
            $result['budget_user_created'] => $result['budget_user_created'],
            $result['user_deleted'] => $result['user_deleted'],
            $result['user_budget_forwarded'] => $result['user_budget_forwarded'],
            $result['user_recommendation'] => $result['user_recommendation'],
            $result['user_approved'] => $result['user_approved'],
            $result['user_payment_approved'] => $result['user_payment_approved'],
            $result['user_payment_paid'] => $result['user_payment_paid']
        );
        $result['user_info'] = System_helper::get_users_info($user_ids);

        //----------------Basic Info. Array Generate----------------
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_CREATED_BY'),
            'value_1' => $result['user_info'][$result['user_created']]['name'],
            'label_2' => $CI->lang->line('LABEL_CREATED_TIME'),
            'value_2' => System_helper::display_date($result['date_created'])
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_DATE_PROPOSAL'),
            'value_1' => System_helper::display_date($result['date_proposal']),
            'label_2' => $CI->lang->line('LABEL_DATE_EXPECTED'),
            'value_2' => System_helper::display_date($result['date_expected'])
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_CROP_NAME'),
            'value_1' => $result['crop_name'],
            'label_2' => $CI->lang->line('LABEL_CROP_TYPE'),
            'value_2' => $result['crop_type_name']
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_VARIETY1_NAME'),
            'value_1' => $result['variety1_name'],
            'label_2' => $CI->lang->line('LABEL_VARIETY2_NAME'),
            'value_2' => ($result['variety2_name']) ? $result['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_PRESENT_CONDITION'),
            'value_1' => nl2br($result['present_condition']),
            'label_2' => $CI->lang->line('LABEL_DEALERS_EVALUATION'),
            'value_2' => nl2br($result['farmers_evaluation'])
        );
        //----------------Location Array Generate----------------
        $data['info_basic'][] = array(
            'label_1' => 'Location'
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_ADDRESS'),
            'value_1' => nl2br($result['address'])
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_OUTLET_NAME'),
            'value_1' => $result['outlet_name'],
            'label_2' => $CI->lang->line('LABEL_GROWING_AREA'),
            'value_2' => ($result['growing_area_name']) ? $result['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_DISTRICT_NAME'),
            'value_1' => $result['district_name'],
            'label_2' => $CI->lang->line('LABEL_TERRITORY_NAME'),
            'value_2' => $result['territory_name']
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_ZONE_NAME'),
            'value_1' => $result['zone_name'],
            'label_2' => $CI->lang->line('LABEL_DIVISION_NAME'),
            'value_2' => $result['division_name']
        );
        //----------------Market Size Info. Array Generate----------------
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_MARKET_SIZE_TITLE')
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_TOTAL_MARKET_SIZE'),
            'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_total']),
            'label_2' => $CI->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'),
            'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_total'])
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_ARM_MARKET_SIZE'),
            'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_arm']),
            'label_2' => $CI->lang->line('LABEL_ARM_GA_MARKET_SIZE'),
            'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_arm'])
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_NEXT_SALES_TARGET'),
            'value_1' => System_helper::get_string_kg($result['quantity_sales_target'])
        );
        //----------------Status Info Array Generate----------------
        $data['info_basic'][] = array(
            'label_1' => 'Budget Status'
        );
        $data['info_basic'][] = array(
            'label_1' => $CI->lang->line('LABEL_STATUS_BUDGET_FORWARD'),
            'value_1' => $result['status_budget_forward'],
            'label_2' => 'Budget Forward ' . $CI->lang->line('LABEL_REMARKS'),
            'value_2' => ($result['remarks_budget_forward']) ? nl2br($result['remarks_budget_forward']) : '-'
        );
        if ($result['user_budget_forwarded'] > 0)
        {
            $data['info_basic'][] = array(
                'label_1' => 'Forwarded By',
                'value_1' => $result['user_info'][$result['user_budget_forwarded']]['name'],
                'label_2' => 'Forward Time',
                'value_2' => System_helper::display_date_time($result['date_budget_forwarded'])
            );

            if ($result['status_recommendation'] == $CI->config->item('system_status_rejected')) // checking Reject, then Forward - RECOMMENDATION TASK
            {
                $data['info_basic'][] = array(
                    'label_1' => '<span class="text-danger">' . $CI->lang->line('LABEL_STATUS_RECOMMENDATION') . '</span>',
                    'value_1' => '<span class="text-danger">' . $result['status_recommendation'] . '</span>',
                    'label_2' => '<span class="text-danger">Reject Remarks</span>',
                    'value_2' => ($result['remarks_recommendation']) ? '<span class="text-danger">' . nl2br($result['remarks_recommendation']) . '</span>' : '-'
                );
            }
            else
            {
                $data['info_basic'][] = array(
                    'label_1' => $CI->lang->line('LABEL_STATUS_RECOMMENDATION'),
                    'value_1' => $result['status_recommendation'],
                    'label_2' => 'Recommendation ' . $CI->lang->line('LABEL_REMARKS'),
                    'value_2' => ($result['remarks_recommendation']) ? nl2br($result['remarks_recommendation']) : '-'
                );
            }
            if (($result['user_recommendation'] > 0) && ($result['status_recommendation'] != $CI->config->item('system_status_pending')))
            {
                if ($result['status_recommendation'] == $CI->config->item('system_status_rejected')) // checking Reject, then Forward - RECOMMENDATION TASK
                {
                    $data['info_basic'][] = array(
                        'label_1' => '<span class="text-danger">Rejected By</span>',
                        'value_1' => '<span class="text-danger">' . $result['user_info'][$result['user_recommendation']]['name'] . '</span>',
                        'label_2' => '<span class="text-danger">Reject Time</span>',
                        'value_2' => '<span class="text-danger">' . System_helper::display_date_time($result['date_recommendation']) . '</span>'
                    );
                }
                else
                {
                    $data['info_basic'][] = array(
                        'label_1' => 'Recommended By',
                        'value_1' => $result['user_info'][$result['user_recommendation']]['name'],
                        'label_2' => 'Recommendation Time',
                        'value_2' => System_helper::display_date_time($result['date_recommendation'])
                    );

                    if ($result['status_approve'] == $CI->config->item('system_status_rejected')) // checking Reject, then Approve - APPROVAL TASK
                    {
                        $data['info_basic'][] = array(
                            'label_1' => '<span class="text-danger">' . $CI->lang->line('LABEL_STATUS_BUDGET_APPROVE') . '</span>',
                            'value_1' => '<span class="text-danger">' . $result['status_approve'] . '</span>',
                            'label_2' => '<span class="text-danger">Reject Remarks</span>',
                            'value_2' => ($result['remarks_approve']) ? '<span class="text-danger">' . nl2br($result['remarks_approve']) . '</span>' : '-'
                        );
                    }
                    else
                    {
                        $data['info_basic'][] = array(
                            'label_1' => $CI->lang->line('LABEL_STATUS_BUDGET_APPROVE'),
                            'value_1' => $result['status_approve'],
                            'label_2' => 'Approval ' . $CI->lang->line('LABEL_REMARKS'),
                            'value_2' => ($result['remarks_approve']) ? nl2br($result['remarks_approve']) : '-'
                        );
                    }
                    if (($result['user_approved'] > 0) && ($result['status_approve'] != $CI->config->item('system_status_pending')))
                    {
                        if ($result['status_approve'] == $CI->config->item('system_status_rejected')) // checking Reject, then Approve - APPROVAL TASK
                        {
                            $data['info_basic'][] = array(
                                'label_1' => '<span class="text-danger">Rejected By</span>',
                                'value_1' => '<span class="text-danger">' . $result['user_info'][$result['user_approved']]['name'] . '</span>',
                                'label_2' => '<span class="text-danger">Reject Time</span>',
                                'value_2' => '<span class="text-danger">' . System_helper::display_date_time($result['date_approved']) . '</span>'
                            );
                        }
                        else
                        {
                            $data['info_basic'][] = array(
                                'label_1' => 'Approved By',
                                'value_1' => $result['user_info'][$result['user_approved']]['name'],
                                'label_2' => 'Approval Time',
                                'value_2' => System_helper::display_date_time($result['date_approved'])
                            );

                            //----------------Payment Status Array Generate----------------
                            $data['info_basic'][] = array(
                                'label_1' => 'Payment Status'
                            );
                            $data['info_basic'][] = array(
                                'label_1' => $CI->lang->line('LABEL_STATUS_PAYMENT_APPROVE'),
                                'value_1' => $result['status_payment_approve'],
                                'label_2' => 'Payment Approval ' . $CI->lang->line('LABEL_REMARKS'),
                                'value_2' => ($result['remarks_payment_approve']) ? nl2br($result['remarks_payment_approve']) : '-'
                            );
                            if ($result['user_payment_approved'] > 0)
                            {
                                $data['info_basic'][] = array(
                                    'label_1' => 'Payment Approved By',
                                    'value_1' => $result['user_info'][$result['user_payment_approved']]['name'],
                                    'label_2' => 'Payment Approval Time',
                                    'value_2' => System_helper::display_date_time($result['date_payment_approved'])
                                );

                                $data['info_basic'][] = array(
                                    'label_1' => $CI->lang->line('LABEL_STATUS_PAYMENT_PAY'),
                                    'value_1' => $result['status_payment_pay'],
                                    'label_2' => 'Payment Paid ' . $CI->lang->line('LABEL_REMARKS'),
                                    'value_2' => ($result['remarks_payment_pay']) ? nl2br($result['remarks_payment_pay']) : '-'
                                );
                                if ($result['user_payment_paid'] > 0)
                                {
                                    $data['info_basic'][] = array(
                                        'label_1' => 'Payment Paid By',
                                        'value_1' => $result['user_info'][$result['user_payment_paid']]['name'],
                                        'label_2' => 'Payment Pay Time',
                                        'value_2' => System_helper::display_date_time($result['date_payment_paid'])
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        //------------------Participants Array Generate-------------------
        $data['dealers'] = Fd_budget_helper::get_dealers_growing_area($result['outlet_id']);
        $data['lead_farmers'] = Fd_budget_helper::get_lead_farmers_growing_area($result['outlet_id']);
        $result_data = json_decode($result['participants_dealer_farmer'], TRUE);

        foreach ($data['dealers'] as &$value)
        {
            if (isset($result_data['dealer_participant'][$value['dealer_id']]) && ($result_data['dealer_participant'][$value['dealer_id']] > 0))
            {
                $value['participant'] = $result_data['dealer_participant'][$value['dealer_id']];
            }
            else
            {
                unset($data['dealers'][$value['dealer_id']]);
            }
        }
        foreach ($data['lead_farmers'] as &$value)
        {
            if (isset($result_data['farmer_participant'][$value['lead_farmers_id']]) && ($result_data['farmer_participant'][$value['lead_farmers_id']] > 0))
            {
                $value['participant'] = $result_data['farmer_participant'][$value['lead_farmers_id']];
            }
            else
            {
                unset($data['lead_farmers'][$value['lead_farmers_id']]);
            }
        }
        //------------------Expense Array Generate-------------------
        $data['expense_items'] = Query_helper::get_info($CI->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array('status !="' . $CI->config->item('system_status_inactive') . '"'), 0, 0, array('ordering ASC'));
        $result_data = json_decode($result['amount_expense_items'], TRUE);
        foreach ($data['expense_items'] as &$expense_item)
        {
            if (isset($result_data[$expense_item['id']]))
            {
                $expense_item['amount'] = $result_data[$expense_item['id']];
            }
            else
            {
                $expense_item['amount'] = 0;
            }
        }
        //------------------Uploaded Image Array Generate-------------------
        $picture_data = Query_helper::get_info($CI->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $CI->config->item('system_status_delete') . '"'));
        $data['image_details'] = array();
        foreach ($picture_data as $picture)
        {
            $data['image_details'][$picture['category_id']] = $picture;
        }
        $data['picture_categories'] = Query_helper::get_info($CI->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $CI->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
        foreach ($data['picture_categories'] as $picture)
        {
            if (!isset($data['image_details'][$picture['value']]))
            {
                $data['image_details'][$picture['value']] = array(
                    'image_location_variety1' => FD_NO_IMAGE_PATH,
                    'remarks_variety1' => '',
                    'image_location_variety2' => FD_NO_IMAGE_PATH,
                    'remarks_variety2' => ''
                );
            }
        }

        $data['title'] = 'Field Day Budget Details ( ID: ' . $item_id . ' )';
        return $data;
    }

    private static function check_my_editable($item)
    {
        $CI = & get_instance();
        if (($CI->locations['division_id'] > 0) && ($CI->locations['division_id'] != $item['division_id']))
        {
            return false;
        }
        if (($CI->locations['zone_id'] > 0) && ($CI->locations['zone_id'] != $item['zone_id']))
        {
            return false;
        }
        if (($CI->locations['territory_id'] > 0) && ($CI->locations['territory_id'] != $item['territory_id']))
        {
            return false;
        }
        if (($CI->locations['district_id'] > 0) && ($CI->locations['district_id'] != $item['district_id']))
        {
            return false;
        }
        return true;
    }

    public static function get_basic_info($result)
    {
        $CI = & get_instance();
        //---------Getting User Names------------
        $user_ids = array(
            $result['user_created'] => $result['user_created'],
            /*$result['user_deleted'] => $result['user_deleted'],
            $result['user_budget_forwarded'] => $result['user_budget_forwarded'],
            $result['user_recommendation'] => $result['user_recommendation'],
            $result['user_approved'] => $result['user_approved'],
            $result['user_payment_approved'] => $result['user_payment_approved'],
            $result['user_payment_paid'] => $result['user_payment_paid']*/
        );
        $user_info = System_helper::get_users_info($user_ids);

        //----------------Basic Info. Array Generate----------------

        $data = array();
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_DATE_PROPOSAL'),
            'value_1' => System_helper::display_date($result['date_proposal']),
            'label_2' => $CI->lang->line('LABEL_DATE_EXPECTED'),
            'value_2' => System_helper::display_date($result['date_expected'])
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_CROP_NAME'),
            'value_1' => $result['crop_name'],
            'label_2' => $CI->lang->line('LABEL_CROP_TYPE'),
            'value_2' => $result['crop_type_name']
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_VARIETY1_NAME'),
            'value_1' => $result['variety1_name'],
            'label_2' => $CI->lang->line('LABEL_VARIETY2_NAME'),
            'value_2' => ($result['variety2_name']) ? $result['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_PRESENT_CONDITION'),
            'value_1' => nl2br($result['present_condition'])
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_DEALERS_EVALUATION'),
            'value_1' => nl2br($result['farmers_evaluation'])
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_DIVISION_NAME'),
            'value_1' => $result['division_name'],
            'label_2' => $CI->lang->line('LABEL_ZONE_NAME'),
            'value_2' => $result['zone_name']
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_TERRITORY_NAME'),
            'value_1' => $result['territory_name'],
            'label_2' => $CI->lang->line('LABEL_DISTRICT_NAME'),
            'value_2' => $result['district_name']
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_OUTLET_NAME'),
            'value_1' => $result['outlet_name'],
            'label_2' => $CI->lang->line('LABEL_GROWING_AREA'),
            'value_2' => ($result['growing_area_name']) ? $result['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_ADDRESS'),
            'value_1' => nl2br($result['address'])
        );
        $data[] = array(
            'label_1' => 'Created By',
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => 'Created Time',
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        return $data;
    }

    public static function fd_budget_status_check($item_array, $check_status)
    {

        $CI =& get_instance();
        foreach ($check_status AS $flag)
        {
            /*
            ----------------FD Budget Status Constants----------------
            */
            if ((FD_BUDGET_FORWARDED == $flag) && ($item_array['status_budget_forward'] != $CI->config->item('system_status_forwarded'))) // Checks if FD Budget FORWARDED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Budget is not Forwarded yet.'
                );
            }
            elseif ((FD_BUDGET_NOT_FORWARDED == $flag) && ($item_array['status_budget_forward'] == $CI->config->item('system_status_forwarded'))) // Checks if FD Budget not FORWARDED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Budget has been Forwarded Already.'
                );
            }
            /*
            ----------------FD Recommendation Status Constants----------------
            */
            elseif ((FD_RECOMMENDATION_FORWARDED == $flag) && ($item_array['status_recommendation'] != $CI->config->item('system_status_forwarded'))) // Checks if FD Budget Recommendation FORWARDED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Budget Recommendation is not Forwarded yet.'
                );
            }
            elseif ((FD_RECOMMENDATION_NOT_FORWARDED == $flag) && ($item_array['status_recommendation'] == $CI->config->item('system_status_forwarded'))) // Checks if FD Budget Recommendation not FORWARDED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Budget Recommendation has been Forwarded Already.'
                );
            }
            elseif ((FD_BUDGET_REJECTED_ZI == $flag) && ($item_array['status_recommendation'] != $CI->config->item('system_status_rejected'))) // Checks if FD Budget Recommendation REJECTED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget Recommendation is not Rejected.'
                );
            }
            elseif ((FD_BUDGET_NOT_REJECTED_ZI == $flag) && ($item_array['status_recommendation'] == $CI->config->item('system_status_rejected'))) // Checks if FD Budget Recommendation not REJECTED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget Recommendation has been Rejected Already.'
                );
            }
            /*
            ----------------FD Approve & Reject Status Constants----------------
            */
            elseif ((FD_BUDGET_APPROVED == $flag) && ($item_array['status_approve'] != $CI->config->item('system_status_approved'))) // Checks if FD Budget APPROVED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget is not Approved yet.'
                );
            }
            elseif ((FD_BUDGET_NOT_APPROVED == $flag) && ($item_array['status_approve'] == $CI->config->item('system_status_approved'))) // Checks if FD Budget not APPROVED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget has been Approved Already.'
                );
            }
            elseif ((FD_BUDGET_REJECTED_DI == $flag) && ($item_array['status_approve'] != $CI->config->item('system_status_rejected'))) // Checks if FD Budget Approval REJECTED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget Approval is not Rejected.'
                );
            }
            elseif ((FD_BUDGET_NOT_REJECTED_DI == $flag) && ($item_array['status_approve'] == $CI->config->item('system_status_rejected'))) // Checks if FD Budget Approval not REJECTED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Budget Approval has been Rejected Already.'
                );
            }
            /*
            ----------------FD Payment Status Constants----------------
            */
            elseif ((FD_PAYMENT_APPROVED == $flag) && ($item_array['status_payment_approve'] != $CI->config->item('system_status_approved'))) // Checks if FD Budget Payment APPROVED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Payment is not Approved yet.'
                );
            }
            elseif ((FD_PAYMENT_NOT_APPROVED == $flag) && ($item_array['status_payment_approve'] == $CI->config->item('system_status_approved'))) // Checks if FD Budget Payment not APPROVED
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Payment has been Approved Already.'
                );
            }
            elseif ((FD_PAYMENT_PAID == $flag) && ($item_array['status_payment_pay'] != $CI->config->item('system_status_paid'))) // Checks if FD Budget Payment PAID
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Payment is not Paid yet.'
                );
            }
            elseif ((FD_PAYMENT_NOT_PAID == $flag) && ($item_array['status_payment_pay'] == $CI->config->item('system_status_paid'))) // Checks if FD Budget Payment not PAID
            {
                return array(
                    'status' => false,
                    'system_message' => 'This Field day Payment has been Paid Already.'
                );
            }
        }
        return array('status' => true, 'system_message' => '');
    }
}

