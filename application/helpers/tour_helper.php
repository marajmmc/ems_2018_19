<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ------Tour Status Constants----- */
CONST TOUR_PENDING = 1;
CONST TOUR_FORWARDED = 2;
CONST TOUR_APPROVED = 3;
CONST TOUR_REJECTED = 4;


class Tour_helper
{
    public static function get_child_ids_designation($designation_id)
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_login_setup_designation'));
        $CI->db->order_by('ordering');
        $results = $CI->db->get()->result_array();

        $child_ids[0] = 0;
        $parents = array();
        foreach ($results as $result)
        {
            $parents[$result['parent']][] = $result;
        }
        Tour_helper::get_sub_child_ids_designation($designation_id, $parents, $child_ids);
        return $child_ids;
    }

    public static function get_sub_child_ids_designation($id, $parents, &$child_ids)
    {
        if (isset($parents[$id]))
        {
            foreach ($parents[$id] as $child)
            {
                $child_ids[$child['id']] = $child['id'];
                if (isset($parents[$child['id']]) && sizeof($parents[$child['id']]) > 0)
                {
                    Tour_helper::get_sub_child_ids_designation($child['id'], $parents, $child_ids);
                }
            }
        }
    }

    public static function to_label($input) // Converts INDEX type text into LABEL type text
    {
        return ucwords(str_replace('_', ' ', trim($input)));
    }

    public static function get_iou_items()
    {
        return array(
            'accommodation',
            'ground_transportation',
            'per-diem',
            'miscellaneous',
            'local_conveyance'
        );
    }

    public static function tour_status_check($tour_array = array(), $check_status = array())
    {
        $CI =& get_instance();

        /* if ((in_array(TOUR_FORWARDED, $check_status)) && ($tour_array['status_forwarded_tour'] != $CI->config->item('system_status_forwarded')))
        {
            return array(
                'status' => false,
                'system_message' => 'This Tour is not Forwarded yet.'
            );
        }
        if ((in_array(TOUR_APPROVED, $check_status)) && ($tour_array['status_approved_tour'] != $CI->config->item('system_status_approved')))
        {
            return array(
                'status' => false,
                'system_message' => 'This Tour is not Approved yet.'
            );
        }
        if ((in_array(TOUR_REJECTED, $check_status)) && ($tour_array['status_approved_tour'] != $CI->config->item('system_status_rejected')))
        {
            return array(
                'status' => false,
                'system_message' => 'This Tour has been Rejected.'
            );
        }
        if ((in_array(TOUR_APPROVED, $check_status)) && ($tour_array['status_approved_tour'] != $CI->config->item('system_status_approved')))
        {
            return array(
                'status' => false,
                'system_message' => 'This Tour\'s IOU is not Approved yet.'
            );
        } */

        if (!empty($check_status))
        {
            foreach ($check_status AS $flag)
            {
                switch ($flag)
                {
                    CASE TOUR_FORWARDED:
                        if ($tour_array['status_forwarded_tour'] != $CI->config->item('system_status_forwarded'))
                        {
                            return array(
                                'status' => false,
                                'system_message' => 'This Tour is not Forwarded yet.'
                            );
                        }
                        break;

                    CASE TOUR_APPROVED:
                        if ($tour_array['status_forwarded_tour'] != $CI->config->item('system_status_forwarded'))
                        {
                            return array(
                                'status' => false,
                                'system_message' => 'This Tour is not Forwarded yet.'
                            );
                        }
                        break;
                }
            }
        }
        else
        {

        }
    }

    public static function tour_duration($date_from = '', $date_to = '', $tour_id = 0)
    {
        if (($date_from != '') && ($date_to != ''))
        {
            $duration = (round(($date_to - $date_from) / (60 * 60 * 24)) + 1);
        }
        elseif ($tour_id > 0)
        {
            $CI =& get_instance();
            $row = $CI->db->select('date_from, date_to')->get_where($CI->config->item('table_ems_tour_setup'), array('id' => $tour_id))->row_array();
            $duration = (round(($row['date_to'] - $row['date_from']) / (60 * 60 * 24)) + 1);
        }
        else
        {
            return '';
        }
        return $duration . ' Day(s)';
    }

    public static function tour_amount($tour_id = 0) // IOU Requested OR, IOU Paid
    {
        if ($tour_id > 0)
        {
            $CI =& get_instance();
            $item = $CI->db->select('amount_iou_items')->get_where($CI->config->item('table_ems_tour_setup'), array('id' => $tour_id, 'status !=' => $CI->config->item('system_status_delete')))->row_array();
            $iou_items = Tour_helper::get_iou_items();
            if ($iou_items)
            {
                $amount_iou_items = array();
                $total_iou_amount = 0.0;
                if ($item['amount_iou_items'] && ($item['amount_iou_items'] != ''))
                {
                    $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
                }
                // EACH IOU Items
                foreach ($iou_items as $iou_item)
                {
                    if (isset($amount_iou_items[$iou_item]))
                    {
                        $total_iou_amount += $amount_iou_items[$iou_item];
                    }
                }
                return $total_iou_amount;
            }
        }
        return '';
    }

    public static function tour_purpose_view($tour_id = '', $items = array(), $col_1 = 4, $col_2 = 4)
    {
        $CI =& get_instance();

        if (empty($items))
        {
            $items = $CI->db->select('purpose')->get_where($CI->config->item('table_ems_tour_purpose'), array('tour_id' => $tour_id, 'status !=' => $CI->config->item('system_status_delete')))->result_array();
        }
        $output = '';

        $output .= '<div class="row show-grid">';
        $output .= '    <div class="col-xs-' . $col_1 . '">';
        $output .= '        <label class="control-label pull-right">Purpose(s):</label>';
        $output .= '    </div>';
        $output .= '    <div class="col-xs-' . $col_2 . ' purpose-list">';
        $output .= '        <table class="table table-bordered table-striped table-hover">';
        $output .= '            <thead>';
        $output .= '                <tr>';
        $output .= '                    <th>' . $CI->lang->line('LABEL_SL_NO') . '</th>';
        $output .= '                    <th>Purpose</th>';
        $output .= '                </tr>';
        $output .= '            </thead>';
        $output .= '            <tbody>';
        if ($items)
        {
            $serial = 0;
            foreach ($items as $row)
            {
                ++$serial;
                $output .= '        <tr>';
                $output .= '            <td>' . $serial . '.</td>';
                $output .= '            <td>' . $row['purpose'] . '</td>';
                $output .= '        </tr>';
            }
        }
        else
        {
            $output .= '            <tr><td colspan="2"> Tour Purpose has Not been Setup </td></tr>';
        }
        $output .= '            </tbody>';
        $output .= '        </table>';
        $output .= '    </div>';
        $output .= '</div>';

        return $output;
    }

    public static function iou_items_summary_view($tour_id = '', $item = array(), $col_1 = 4, $col_2 = 3, $col_3 = 1) // PARAMETER: Either pass Tour ID or, Tour Setup Array
    {
        $CI =& get_instance();
        if (empty($item))
        {
            $item = $CI->db->select('amount_iou_items')->get_where($CI->config->item('table_ems_tour_setup'), array('id' => $tour_id, 'status !=' => $CI->config->item('system_status_delete')))->row_array();
        }

        $output = '';
        $iou_items = Tour_helper::get_iou_items();
        if ($iou_items)
        {
            $i = 0;
            $amount_iou_items = array();
            $total_iou_amount = 0.0;
            if ($item['amount_iou_items'] && ($item['amount_iou_items'] != ''))
            {
                $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
            }
            // EACH IOU Items
            foreach ($iou_items as $iou_item)
            {
                $iou_amount = 0;
                if (isset($amount_iou_items[$iou_item]))
                {
                    $iou_amount = $amount_iou_items[$iou_item];
                }
                $output .= '<div class="row show-grid">';
                $output .= '    <div class="col-xs-' . $col_1 . '">';
                if ($i == 0)
                {
                    $output .= '    <label class="control-label pull-right">IOU Items:</label>';
                }
                $output .= '    </div>';
                $output .= '    <div class="col-xs-' . $col_2 . '">';
                $output .= '        <label class="control-label pull-right normal">' . Tour_helper::to_label($iou_item) . ':</label>';
                $output .= '    </div>';
                $output .= '    <div class="col-xs-' . $col_3 . '" style="padding-left:0">';
                $output .= '        <label class="control-label pull-right">' . (System_helper::get_string_amount($iou_amount)) . '</label>';
                $output .= '    </div>';
                $output .= '</div>';

                $total_iou_amount += $iou_amount;
                $i++;
            }

            // SUMMATION of the IOU Items
            $output .= '<div class="row show-grid" style="margin-bottom:30px">';
            $output .= '    <div class="col-xs-' . $col_1 . '"> &nbsp; </div>';
            $output .= '    <div class="col-xs-' . $col_2 . '" style="border-top:1px solid #000; padding-top:5px">';
            $output .= '        <label class="control-label pull-right">Total ' . $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST') . ':</label>';
            $output .= '    </div>';
            $output .= '    <div class="col-xs-' . $col_3 . '" style="border-top:1px solid #000; padding-top:5px; padding-left:0; text-align:right">';
            $output .= '        <label class="control-label">' . (System_helper::get_string_amount($total_iou_amount)) . '</label>';
            $output .= '    </div>';
            $output .= '</div>';
        }

        return $output;
    }
}

/*-------------------------- DEBUGGING FUNCTIONS --------------------------*/
// Added by Mahmud (Temporarily just for debugging ARRAY)
if (!function_exists('pr'))
{
    function pr($arr, $die = 1)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
        if ($die)
            die();

    }
}
// Added by Mahmud (Temporarily just for debugging QUERY)
if (!function_exists('show_query'))
{
    function show_query($die = 1)
    {
        $CI =& get_instance();
        echo $CI->db->last_query() . '<br/>';
        if ($die)
            die();

    }
}
/*---------------------- DEBUGGING FUNCTIONS (ENDS) ------------------------*/
