<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* ------Tour Status Constants----- */
CONST TOUR_FORWARDED = 1;
CONST TOUR_NOT_FORWARDED = 2;
CONST TOUR_APPROVED = 3;
CONST TOUR_NOT_APPROVED = 4;
CONST TOUR_REJECTED = 5;
CONST TOUR_NOT_REJECTED = 6;

/* ------Tour Payment Constants----- */
CONST TOUR_PAYMENT_APPROVED = 7;
CONST TOUR_PAYMENT_NOT_APPROVED = 8;
CONST TOUR_PAYMENT_PAID = 9;
CONST TOUR_PAYMENT_NOT_PAID = 10;

/* ------Tour Adjustment Constants----- */
CONST TOUR_IOU_ADJUSTMENT_FORWARDED = 11;
CONST TOUR_IOU_ADJUSTMENT_NOT_FORWARDED = 12;
CONST TOUR_IOU_ADJUSTMENT_APPROVED = 13;
CONST TOUR_IOU_ADJUSTMENT_NOT_APPROVED = 14;

/* ------Tour Reporting Constants----- */
CONST TOUR_REPORTING_FORWARDED = 15;
CONST TOUR_REPORTING_NOT_FORWARDED = 16;
CONST TOUR_REPORTING_APPROVED = 17;
CONST TOUR_REPORTING_NOT_APPROVED = 18;

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

    public static function get_iou_items($status = false)
    {
        $CI =& get_instance();
        $status_active = $CI->config->item('system_status_active');
        if ($status)
        {
            $condition = array('status="' . $CI->config->item('system_status_active') . '"');
        }
        else
        {
            $condition = array();
        }
        $results = Query_helper::get_info($CI->config->item('table_login_setup_expense_item_iou') . ' iou', array('iou.*', "IF(iou.status='$status_active', iou.name, CONCAT( iou.name, ' (', iou.status, ')' )) name"), $condition, 0, 0, array('ordering', 'id'));

        $items = array();
        foreach ($results as $result)
        {
            $items[$result['id']] = $result;
        }
        return $items;
    }

    public static function calculate_total_iou($json_data='') // $json_data = JSON code of IOU request/ IOU adjustment
    {
        $total_iou_amount = 0.0;
        if(!empty($json_data))
        {
            $iou_item_amount = json_decode($json_data);
            foreach ($iou_item_amount as $amount)
            {
                $total_iou_amount += $amount;
            }
        }
        return $total_iou_amount;
    }

    public static function tour_duration($date_from = 0, $date_to = 0)
    {
        if (($date_from > 0) && ($date_to > 0))
        {
            $duration = (round(($date_to - $date_from) / (60 * 60 * 24)) + 1) . ' Day(s)';
        }
        else
        {
            return '';
        }
        return $duration;
    }

    public static function tour_purpose_view($tour_id = '', $items = array(), $col_1 = 4, $col_2 = 4)
    {
        $CI =& get_instance();
        if (empty($items))
        {
            $items = $CI->db->select('purpose')->get_where($CI->config->item('table_ems_tour_purpose'), array('tour_id' => $tour_id, 'status !=' => $CI->config->item('system_status_delete')))->result_array();
        }

        $data = array(
            'items'=>$items,
            'col_1'=>$col_1,
            'col_2'=>$col_2,
            'slno_label'=>$CI->lang->line('LABEL_SL_NO')
        );

        $html = $CI->load->view("tour_setup/view_purpose_summary", $data, true);
        return $html;
    }

    public static function iou_items_summary_view($tour_id = 0, $item = array(), $col_1 = 4, $col_2 = 3, $col_3 = 1) // PARAMETER: Either pass Tour ID or, Tour Setup Array
    {
        $CI =& get_instance();
        if (empty($item))
        {
            $item = $CI->db->select('amount_iou_items')->get_where($CI->config->item('table_ems_tour_setup'), array('id' => $tour_id, 'status !=' => $CI->config->item('system_status_delete')))->row_array();
        }

        $data = array(
            'item' => $item,
            'iou_items' => Tour_helper::get_iou_items(),
            'col_1' => $col_1,
            'col_2' => $col_2,
            'col_3' => $col_3,
            'iou_rqst_label'=>$CI->lang->line('LABEL_AMOUNT_IOU_REQUEST')
        );

        $html = $CI->load->view("tour_setup/view_iou_summary", $data, true);
        return $html;
    }

    public static function tour_status_check($tour_array = array(), $check_status = array())
    {
        if (!empty($tour_array) && !empty($check_status))
        {
            $CI =& get_instance();
            foreach ($check_status AS $flag)
            { /*
                ----------------Tour Status Constants----------------
                */
                if ((TOUR_FORWARDED == $flag) && ($tour_array['status_forwarded_tour'] != $CI->config->item('system_status_forwarded'))) // Checks if TOUR FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour is not Forwarded yet.'
                    );
                }
                elseif ((TOUR_NOT_FORWARDED == $flag) && ($tour_array['status_forwarded_tour'] == $CI->config->item('system_status_forwarded'))) // Checks if TOUR not FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour has been Forwarded Already.'
                    );
                }
                elseif ((TOUR_APPROVED == $flag) && ($tour_array['status_approved_tour'] != $CI->config->item('system_status_approved'))) // Checks if TOUR APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour is not Approved yet.'
                    );
                }
                elseif ((TOUR_NOT_APPROVED == $flag) && ($tour_array['status_approved_tour'] == $CI->config->item('system_status_approved'))) // Checks if TOUR not APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour has been Approved Already.'
                    );
                }
                elseif ((TOUR_REJECTED == $flag) && ($tour_array['status_approved_tour'] != $CI->config->item('system_status_rejected'))) // Checks if TOUR REJECTED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour is not Rejected.'
                    );
                }
                elseif ((TOUR_NOT_REJECTED == $flag) && ($tour_array['status_approved_tour'] == $CI->config->item('system_status_rejected'))) // Checks if TOUR not REJECTED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour has been Rejected Already.'
                    );
                }
                /*
                ----------------Tour Payment Constants----------------
                */
                elseif ((TOUR_PAYMENT_APPROVED == $flag) && ($tour_array['status_approved_payment'] != $CI->config->item('system_status_approved'))) // Checks if TOUR PAYMENT APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour IOU is not Approved yet.'
                    );
                }
                elseif ((TOUR_PAYMENT_NOT_APPROVED == $flag) && ($tour_array['status_approved_payment'] == $CI->config->item('system_status_approved'))) // Checks if TOUR PAYMENT not APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour IOU has been Approved Already.'
                    );
                }
                elseif ((TOUR_PAYMENT_PAID == $flag) && ($tour_array['status_paid_payment'] != $CI->config->item('system_status_paid'))) // Checks if TOUR PAYMENT PAID
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour IOU is not Paid yet.'
                    );
                }
                elseif ((TOUR_PAYMENT_NOT_PAID == $flag) && ($tour_array['status_paid_payment'] == $CI->config->item('system_status_paid'))) // Checks if TOUR PAYMENT not PAID
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour IOU has been Paid Already.'
                    );
                }
                /*
                ----------------Tour Adjustment Constants----------------
                */
                elseif ((TOUR_IOU_ADJUSTMENT_FORWARDED == $flag) && ($tour_array['status_approved_adjustment'] != $CI->config->item('system_status_forwarded'))) // Checks if TOUR IOU ADJUSTMENT FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'IOU Adjustment is not Forwarded yet.'
                    );
                }
                elseif ((TOUR_IOU_ADJUSTMENT_NOT_FORWARDED == $flag) && ($tour_array['status_approved_adjustment'] == $CI->config->item('system_status_forwarded'))) // Checks if TOUR IOU ADJUSTMENT not FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'IOU Adjustment has been Forwarded Already.'
                    );
                }
                elseif ((TOUR_IOU_ADJUSTMENT_APPROVED == $flag) && ($tour_array['status_approved_adjustment'] != $CI->config->item('system_status_approved'))) // Checks if TOUR IOU ADJUSTMENT APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour is not Approved yet.'
                    );
                }
                elseif ((TOUR_IOU_ADJUSTMENT_NOT_APPROVED == $flag) && ($tour_array['status_approved_adjustment'] == $CI->config->item('system_status_approved'))) // Checks if TOUR IOU ADJUSTMENT not APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour has been Approved Already.'
                    );
                }
                /*
                ----------------Tour Reporting Constants----------------
                */
                elseif ((TOUR_REPORTING_FORWARDED == $flag) && ($tour_array['status_forwarded_reporting'] != $CI->config->item('system_status_forwarded'))) // Checks if TOUR REPORTING FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour is not Forwarded yet.'
                    );
                }
                elseif ((TOUR_REPORTING_NOT_FORWARDED == $flag) && ($tour_array['status_forwarded_reporting'] == $CI->config->item('system_status_forwarded'))) // Checks if TOUR REPORTING not FORWARDED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'This Tour has been Forwarded Already.'
                    );
                }
                elseif ((TOUR_REPORTING_APPROVED == $flag) && ($tour_array['status_approved_reporting'] != $CI->config->item('system_status_approved'))) // Checks if TOUR REPORTING APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour Reporting is not Approved yet.'
                    );
                }
                elseif ((TOUR_REPORTING_NOT_APPROVED == $flag) && ($tour_array['status_approved_reporting'] == $CI->config->item('system_status_approved'))) // Checks if TOUR REPORTING not APPROVED
                {
                    return array(
                        'status' => false,
                        'system_message' => 'Tour Reporting has been Approved Already.'
                    );
                }
            }
        }
        return array('status' => true);
    }

    /*------------------Convert Numeric Amount INTO In-Word------------------*/
    public static function get_string_amount_inword($number)
    {
        $number = (float)$number;
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => 'Zero', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_length)
        {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number)
            {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            }
            else $str[] = null;
        }
        $Taka = implode('', array_reverse($str));
        $Paisa = ($decimal) ? ", " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paisa' : '';

        return ($Taka ? $Taka . 'Taka' : '') . $Paisa;
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
