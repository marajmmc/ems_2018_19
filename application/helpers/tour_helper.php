<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tour_helper
{
    public static function to_label($input) // Converts INDEX type text into LABEL type text
    {
        return ucwords(str_replace('_', ' ', trim($input)));
    }

    public static function get_iou_items()
    {
        return array(
            'accommodation',
            'transportation',
            'food_allowance',
            'miscellaneous'
        );
    }

    public static function iou_items_summary_view(){
//    if ($iou_items)
//    {
//        $i = 0;
//        $amount_iou_items = array();
//        $total_iou_amount = 0.0;
//        if($item['amount_iou_items'] && ($item['amount_iou_items'] != '')){
//            $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
//        }
//        foreach ($iou_items as $iou_item)
//        {
//            ?>
<!--            <div class="row show-grid">-->
<!--                <div class="col-xs-4">-->
<!--                    --><?php //if ($i == 0)
//                    {
//                        ?>
<!--                        <label class="control-label pull-right">--><?php //echo 'IOU Items'; ?><!--:</label>-->
<!--                    --><?php
//                    }
//                    else
//                    {
//                        echo '';
//                    }
//                    ?>
<!--                </div>-->
<!--                <div class="col-xs-3">-->
<!--                    <label class="control-label pull-right normal">--><?php //echo Tour_helper::to_label($iou_item); ?><!--:</label>-->
<!--                </div>-->
<!--                <div class="col-xs-1" style="padding-left:0">-->
<!--                    <label class="control-label pull-right">--><?php //echo System_helper::get_string_amount( (isset($amount_iou_items[$iou_item]))? $amount_iou_items[$iou_item]: 0 ); ?><!--</label>-->
<!--                </div>-->
<!--            </div>-->
<!--            --><?php
//            $total_iou_amount += $amount_iou_items[$iou_item];
//            $i++;
//        }
//    }
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
