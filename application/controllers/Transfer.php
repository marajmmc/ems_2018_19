<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        $this->rnd_demo_varieties();
    }
    private function rnd_demo_varieties()
    {
        $source_tables=array(
            'rnd_demo_varieties'=>'arm_ems.ems_tm_rnd_demo_varieties',
        );
        $destination_tables=array(
            'rnd_demo_varieties'=>$this->config->item('table_ems_ft_rnd_demo_varieties'),
        );
        $results=Query_helper::get_info($source_tables['rnd_demo_varieties'],'*',array());
        foreach($results as &$result)
        {
            $result['revision']=1;
            if($result['status']=='Deleted')
            {
                $result['revision']=2;
            }
            unset($result['status']);
            unset($result['id']);
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['rnd_demo_varieties'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success';
        }
        else
        {
            echo 'Failed';
        }
    }
}
