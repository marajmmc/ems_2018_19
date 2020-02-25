<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Survey_farmer_helper
{
    public static function get_yes_no($id=null)
    {
        $CI =& get_instance();
        $value[1]=$CI->lang->line('SURVEY_FARMER_YES');
        $value[0]=$CI->lang->line('SURVEY_FARMER_NO');
        if($id>=0)
        {
            $return_value=isset($value[$id])?$value[$id]:'';
        }
        else
        {
            $return_value=$value;
        }

        return $return_value;
    }
    public static function get_last_year_cultivated($id=null)
    {
        $CI =& get_instance();
        $value[1]=$CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_PADDY');
        $value[2]=$CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_JUTE');
        $value[3]=$CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_WHEAT');
        $value[4]=$CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MUSTARD');
        $value[5]=$CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MAIZE');
        $value[6]=$CI->lang->line('SURVEY_FARMER_OTHERS');
        if($id>0)
        {
            $return_value=isset($value[$id])?$value[$id]:'';
        }
        else
        {
            $return_value=$value;
        }

        return $return_value;
    }
    public static function get_seeds_collect($id=null)
    {
        $CI =& get_instance();
        $value[1]=$CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_DEALERS');
        $value[2]=$CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_RETAILERS');
        $value[3]=$CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_LEADFARMERS');
        $value[4]=$CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_HATBAZAR');
        $value[5]=$CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_OWNSEEDS');
        $value[6]=$CI->lang->line('SURVEY_FARMER_OTHERS');
        if($id>0)
        {
            $return_value=isset($value[$id])?$value[$id]:'';
        }
        else
        {
            $return_value=$value;
        }

        return $return_value;
    }

}
