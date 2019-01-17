<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ft_demonstration_helper
{
    public static function get_demonstration_by_id($item_id, $method_name = '')
    {
        $CI = & get_instance();

        $CI->db->from($CI->config->item('table_ems_demonstration_status') . ' demonstration');
        $CI->db->select('demonstration.*');

        $CI->db->join($CI->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $CI->db->select('season.name season');

        $CI->db->join($CI->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $CI->db->select('cus_info.name outlet_name');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $CI->db->select('areas.name growing_area');

        $CI->db->join($CI->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $CI->db->select('IF( (demonstration.lead_farmer_id > 0), CONCAT( lead_farmers.name, " (", lead_farmers.mobile_no, ")" ), CONCAT(demonstration.name_other_farmer, " (", demonstration.phone_other_farmer, ")") ) AS lead_farmer_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
        $CI->db->select('crop.name crop_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = demonstration.crop_type_id', 'INNER');
        $CI->db->select('crop_type.name crop_type_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
        $CI->db->select('variety1.name variety1_name');

        $CI->db->join($CI->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
        $CI->db->select('variety2.name variety2_name');

        $CI->db->join($CI->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $CI->db->select('district.id district_id, district.name district_name');

        $CI->db->join($CI->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $CI->db->select('territory.id territory_id, territory.name territory_name');

        $CI->db->join($CI->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $CI->db->select('zone.id zone_id, zone.name zone_name');

        $CI->db->join($CI->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $CI->db->select('division.id division_id, division.name division_name');

        $CI->db->where('demonstration.status !=', $CI->config->item('system_status_delete'));
        $CI->db->where('demonstration.id', $item_id);
        $result = $CI->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try($method_name, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $CI->json_return($ajax);
        }
        if (!Ft_demonstration_helper::check_my_editable($result))
        {
            System_helper::invalid_try($method_name, $item_id, 'Trying to View or, Edit Information of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to View or, Edit Information of other Location';
            $CI->json_return($ajax);
        }
        return $result;
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
            $result['user_inactive'] => $result['user_inactive'],
            $result['user_deleted'] => $result['user_deleted'],
            $result['user_forwarded'] => $result['user_forwarded']
        );
        $user_info = System_helper::get_users_info($user_ids);

        //----------------Basic Info. Array Generate----------------
        $data = array();
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_YEAR'),
            'value_1' => $result['year'],
            'label_2' => $CI->lang->line('LABEL_SEASON'),
            'value_2' => $result['season']
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_OUTLET_NAME'),
            'value_1' => $result['outlet_name'],
            'label_2' => $CI->lang->line('LABEL_GROWING_AREA'),
            'value_2' => $result['growing_area']
        );
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_FARMER_NAME'),
            'value_1' => $result['lead_farmer_name'],
            'label_2' => 'Farmer Type',
            'value_2' => ($result['lead_farmer_id'] > 0) ? $CI->lang->line('LABEL_LEAD_FARMER_NAME') : $CI->lang->line('LABEL_OTHER_FARMER_NAME')
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
        if (!($result['variety2_id'] > 0))
        {
            $data[] = array(
                'label_1' => $CI->lang->line('LABEL_DATE_SOWING_VARIETY1'),
                'value_1' => System_helper::display_date($result['date_sowing_variety1'])
            );
            $data[] = array(
                'label_1' => $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY1'),
                'value_1' => ($result['date_transplanting_variety1']) ? System_helper::display_date($result['date_transplanting_variety1']) : '<i style="font-weight:normal">- No Date Selected -</i>'
            );
        }
        else
        {
            $data[] = array(
                'label_1' => $CI->lang->line('LABEL_DATE_SOWING_VARIETY1'),
                'value_1' => System_helper::display_date($result['date_sowing_variety1']),
                'label_2' => $CI->lang->line('LABEL_DATE_SOWING_VARIETY2'),
                'value_2' => ($result['date_sowing_variety2']) ? System_helper::display_date($result['date_sowing_variety2']) : '<i style="font-weight:normal">- No Date Selected -</i>'
            );
            $data[] = array(
                'label_1' => $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY1'),
                'value_1' => ($result['date_transplanting_variety1']) ? System_helper::display_date($result['date_transplanting_variety1']) : '<i style="font-weight:normal">- No Date Selected -</i>',
                'label_2' => $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY2'),
                'value_2' => ($result['date_transplanting_variety2']) ? System_helper::display_date($result['date_transplanting_variety2']) : '<i style="font-weight:normal">- No Date Selected -</i>'
            );
        }
        $data[] = array(
            'label_1' => $CI->lang->line('LABEL_DATE_EXPECTED_EVALUATION'),
            'value_1' => System_helper::display_date($result['date_expected_evaluation']),
            'label_2' => $CI->lang->line('LABEL_DATE_ACTUAL_EVALUATION'),
            'value_2' => ($result['date_actual_evaluation']) ? System_helper::display_date($result['date_actual_evaluation']) : '<i style="font-weight:normal;color:#FF0000">- No Date Selected -</i>'
        );
        $data[] = array(
            'label_1' => 'Created By',
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => 'Created Time',
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        return $data;
    }

    public static function get_details_info($result)
    {
        $CI = & get_instance();
        //---------Getting User Names------------
        $user_ids = array(
            $result['user_created'] => $result['user_created'],
            $result['user_inactive'] => $result['user_inactive'],
            $result['user_deleted'] => $result['user_deleted'],
            $result['user_forwarded'] => $result['user_forwarded'],
            $result['user_rollback'] => $result['user_rollback'],
            $result['user_recommendation'] => $result['user_recommendation'],
        );
        $user_info = System_helper::get_users_info($user_ids);

        $data = array();
        $data = Ft_demonstration_helper::get_basic_info($result); // Fetch Basic Info
        if ($result['status'] == $CI->config->item('system_status_inactive'))
        {
            $data[] = array(
                'label_1' => '<span class="text-danger">' . $CI->config->item('system_status_inactive') . ' By</span>',
                'value_1' => '<span class="text-danger">' . $user_info[$result['user_inactive']]['name'] . ' ( ' . $user_info[$result['user_inactive']]['employee_id'] . ' )</span>',
                'label_2' => '<span class="text-danger">' . $CI->config->item('system_status_inactive') . ' Time</span>',
                'value_2' => '<span class="text-danger">' . System_helper::display_date_time($result['date_inactive']) . '</span>'
            );
            $data[] = array(
                'label_1' => '<span class="text-danger">' . $CI->config->item('system_status_inactive') . ' Reason</span>',
                'value_1' => '<span class="text-danger">' . nl2br($result['remarks_inactive']) . '</span>'
            );
        }
        if ($result['status_forward'] == $CI->config->item('system_status_forwarded'))
        {
            $data[] = array(
                'label_1' => 'Demonstration Forward Status'
            );
            $data[] = array(
                'label_1' => 'Forwarded Status',
                'value_1' => $CI->config->item('system_status_forwarded'),
                'label_2' => $CI->lang->line('LABEL_TMPOS_COMMENT'),
                'value_2' => nl2br($result['remarks_forward'])
            );
            $data[] = array(
                'label_1' => $CI->lang->line('LABEL_FARMERS_COMMENT'),
                'value_1' => nl2br($result['remarks_farmer'])
            );
            $data[] = array(
                'label_1' => 'Forwarded By',
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => 'Forwarded Time',
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
        }
        if (($result['status_forward'] != $CI->config->item('system_status_forwarded')) && ($result['user_rollback'] > 0))
        {
            $data[] = array(
                'label_1' => '<span class="text-danger">Demonstration Rollback Information</span>'
            );
            $data[] = array(
                'label_1' => '<span class="text-danger">' . $CI->lang->line('LABEL_REASON_REMARKS') . '</span>',
                'value_1' => '<span class="text-danger">' . nl2br($result['remarks_rollback']) . '</span>'
            );
            $data[] = array(
                'label_1' => '<span class="text-danger">Rollback By</span>',
                'value_1' => '<span class="text-danger">' . $user_info[$result['user_rollback']]['name'] . ' ( ' . $user_info[$result['user_rollback']]['employee_id'] . ' )</span>',
                'label_2' => '<span class="text-danger">Rollback Time</span>',
                'value_2' => '<span class="text-danger">' . System_helper::display_date_time($result['date_rollback']) . '</span>'
            );
        }
        if ($result['status_recommendation'] != $CI->config->item('system_status_pending'))
        {
            if ($result['status_recommendation'] == $CI->config->item('system_status_complete'))
            {
                $style_color = '';
            }
            else
            {
                $style_color = 'color:#a94442';
            }
            $data[] = array(
                'label_1' => '<span style="' . $style_color . '">Recommendation Status</span>'
            );
            $data[] = array(
                'label_1' => '<span style="' . $style_color . '">' . $CI->lang->line('LABEL_ZSCS_COMMENT') . '</span>',
                'value_1' => '<span style="' . $style_color . '">' . nl2br($result['remarks_zsc']) . '</span>'
            );
            $data[] = array(
                'label_1' => '<span style="' . $style_color . '">Evaluation</span>',
                'value_1' => '<span style="' . $style_color . '">' . $result['evaluation'] . '</span>',
                'label_2' => '<span style="' . $style_color . '">' . $CI->lang->line('LABEL_STATUS') . '</span>',
                'value_2' => '<span style="' . $style_color . '">' . $result['status_recommendation'] . '</span>'
            );
            if ($result['remarks_recommendation'])
            {
                $data[] = array(
                    'label_1' => '<span style="' . $style_color . '">Recommendation</span>',
                    'value_1' => '<span style="' . $style_color . '">' . nl2br($result['remarks_recommendation']) . '</span>'
                );
            }
            $data[] = array(
                'label_1' => '<span style="' . $style_color . '">Recommended By</span>',
                'value_1' => '<span style="' . $style_color . '">' . $user_info[$result['user_recommendation']]['name'] . ' ( ' . $user_info[$result['user_recommendation']]['employee_id'] . ' )</span>',
                'label_2' => '<span style="' . $style_color . '">Recommendation Time</span>',
                'value_2' => '<span style="' . $style_color . '">' . System_helper::display_date_time($result['date_recommendation']) . '</span>'
            );
        }
        return $data;
    }
}
