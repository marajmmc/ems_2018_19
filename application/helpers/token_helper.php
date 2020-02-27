<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Token_helper
{
    public static function get_token($token)
    {
        $CI =& get_instance();
        $user = User_helper::get_user();
        $result=Query_helper::get_info($CI->config->item('table_system_token'),array('id, token'),array('user_id='.$user->user_id),1);
        if($result)
        {
            if($result['token']==$token)
            {
                return array('status'=>true, 'id'=>$result['id']);
            }
            else
            {
                return array('status'=>false, 'id'=>$result['id']);
            }
        }

        return array('status'=>false, 'id'=>0);
    }
    public static function update_token($id, $token)
    {
        $CI =& get_instance();
        $user = User_helper::get_user();
        if($id>0)
        {
            $data=array();
            $data['token']=$token;
            $data['date_updated']=time();
            $CI->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($CI->config->item('table_system_token'), $data,array("id = ".$id));
        }
        else
        {
            $data=array();
            $data['user_id']=$user->user_id;
            $data['token']=$token;
            $data['date_updated']=time();
            Query_helper::add($CI->config->item('table_system_token'), $data);
        }
    }

    /*public static function get_yes_no($id=null)
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
    }*/


}
