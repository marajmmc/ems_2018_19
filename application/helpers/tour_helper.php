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
