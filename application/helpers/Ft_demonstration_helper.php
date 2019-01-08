<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ft_demonstration_helper
{
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
                'value_2' => ($result['date_sowing_variety2']) ? System_helper::display_date($result['date_sowing_variety2']) : '<i style="font-weight:normal;color:#FF0000">- No Date Selected -</i>'
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
            'value_1' => $user_info[$result['user_created']]['name'],
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
            $result['user_recommendation'] => $result['user_recommendation'],
        );
        $user_info = System_helper::get_users_info($user_ids);

        $data = array();
        $data = Ft_demonstration_helper::get_basic_info($result); // Fetch Basic Info
        if ($result['status'] == $CI->config->item('system_status_inactive'))
        {
            $data[] = array(
                'label_1' => '<span class="text-danger">' . $CI->config->item('system_status_inactive') . ' By</span>',
                'value_1' => '<span class="text-danger">' . $user_info[$result['user_inactive']]['name'] . '</span>',
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
                'label_1' => 'Demonstration Forwarded Status'
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
                'value_1' => $user_info[$result['user_forwarded']]['name'],
                'label_2' => 'Forwarded Time',
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
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
                'value_1' => '<span style="' . $style_color . '">' . $user_info[$result['user_recommendation']]['name'] . '</span>',
                'label_2' => '<span style="' . $style_color . '">Recommendation Time</span>',
                'value_2' => '<span style="' . $style_color . '">' . System_helper::display_date_time($result['date_recommendation']) . '</span>'
            );

        }
        return $data;
    }
}
