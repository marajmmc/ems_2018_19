<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        $this->rnd_demo_varieties();
        //$this->setup_fruit_picture();
        //$this->rnd_demo_picture();
        //$this->rnd_demo_disease_picture();
        //$this->rnd_demo_fruit_picture();
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
    private function setup_fruit_picture()
    {
        $source_tables=array(
            'setup_fruit_picture'=>'arm_ems.ems_setup_tm_fruit_picture',
        );
        $destination_tables=array(
            'setup_fruit_picture'=>$this->config->item('table_ems_ft_rnd_demo_setup_fruit_picture'),
        );
        $results=Query_helper::get_info($source_tables['setup_fruit_picture'],'*',array());
        foreach($results as &$result)
        {
            if($result['picture_file_name'])
            {
                $result['image_name']=$result['picture_file_name'];
            }
            else
            {
                $result['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $result['image_location']=$result['picture_file_full'];
            }else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['setup_fruit_picture'],$result,false);
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
    private function rnd_demo_picture()
    {
        $source_tables=array(
            'rnd_demo_picture'=>'arm_ems.ems_tm_rnd_demo_picture',
        );
        $destination_tables=array(
            'rnd_demo_picture'=>$this->config->item('table_ems_ft_rnd_demo_picture'),
        );
        $results=Query_helper::get_info($source_tables['rnd_demo_picture'],'*',array());
        foreach($results as &$result)
        {
            if($result['picture_file_name'])
            {
                $result['image_name']=$result['picture_file_name'];
            }
            else
            {
                $result['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $result['image_location']=$result['picture_file_full'];
            }else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['rnd_demo_picture'],$result,false);
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
    private function rnd_demo_disease_picture()
    {
        $source_tables=array(
            'rnd_demo_disease_picture'=>'arm_ems.ems_tm_rnd_demo_disease_picture',
        );
        $destination_tables=array(
            'rnd_demo_disease_picture'=>$this->config->item('table_ems_ft_rnd_demo_disease_picture'),
        );
        $results=Query_helper::get_info($source_tables['rnd_demo_disease_picture'],'*',array());
        foreach($results as &$result)
        {
            if($result['picture_file_name'])
            {
                $result['image_name']=$result['picture_file_name'];
            }
            else
            {
                $result['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $result['image_location']=$result['picture_file_full'];
            }else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['rnd_demo_disease_picture'],$result,false);
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
    private function rnd_demo_fruit_picture()
    {
        $source_tables=array(
            'rnd_demo_fruit_picture'=>'arm_ems.ems_tm_rnd_demo_fruit_picture',
        );
        $destination_tables=array(
            'rnd_demo_fruit_picture'=>$this->config->item('table_ems_ft_rnd_demo_fruit_picture'),
        );
        $results=Query_helper::get_info($source_tables['rnd_demo_fruit_picture'],'*',array());
        foreach($results as &$result)
        {
            if($result['picture_file_name'])
            {
                $result['image_name']=$result['picture_file_name'];
            }
            else
            {
                $result['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $result['image_location']=$result['picture_file_full'];
            }else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['rnd_demo_fruit_picture'],$result,false);
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
