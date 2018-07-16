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
        $data=array();
        foreach($results as &$result)
        {
            $data[$result['id']]['id']=$result['id'];
            $data[$result['id']]['setup_id']=$result['setup_id'];
            $data[$result['id']]['variety_id']=$result['variety_id'];
            $data[$result['id']]['date_created']=$result['date_created'];
            $data[$result['id']]['user_created']=$result['user_created'];
            $data[$result['id']]['date_updated']=$result['date_updated'];
            $data[$result['id']]['user_updated']=$result['user_updated'];

            $data[$result['id']]['revision']=1;
            if($result['status']=='Deleted')
            {
                $data[$result['id']]['revision']=2;
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($data as $row)
        {
            Query_helper::add($destination_tables['rnd_demo_varieties'],$row,false);
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
        $data=array();
        foreach($results as &$result)
        {
            $data[$result['id']]['id']=$result['id'];
            $data[$result['id']]['name']=$result['name'];
            $data[$result['id']]['status']=$result['status'];
            $data[$result['id']]['ordering']=$result['ordering'];
            $data[$result['id']]['date_created']=$result['date_created'];
            $data[$result['id']]['user_created']=$result['user_created'];
            $data[$result['id']]['date_updated']=$result['date_updated'];
            $data[$result['id']]['user_updated']=$result['user_updated'];

            if($result['picture_file_name'])
            {
                $data[$result['id']]['image_name']=$result['picture_file_name'];
            }
            else
            {
                $data[$result['id']]['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $data[$result['id']]['image_location']=$result['picture_file_full'];
            }
            else
            {
                $data[$result['id']]['image_location']='images/no_image.jpg';
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($data as $row)
        {
            Query_helper::add($destination_tables['setup_fruit_picture'],$row,false);
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
        $data=array();
        foreach($results as &$result)
        {
            $data[$result['id']]['id']=$result['id'];
            $data[$result['id']]['setup_id']=$result['setup_id'];
            $data[$result['id']]['day_no']=$result['day_no'];
            $data[$result['id']]['variety_id']=$result['variety_id'];
            $data[$result['id']]['remarks']=$result['remarks'];
            $data[$result['id']]['feedback']=$result['feedback'];
            $data[$result['id']]['date_created']=$result['date_created'];
            $data[$result['id']]['user_created']=$result['user_created'];
            $data[$result['id']]['date_feedback']=$result['date_feedback'];
            $data[$result['id']]['user_feedback']=$result['user_feedback'];
            $data[$result['id']]['date_updated']=$result['date_updated'];
            $data[$result['id']]['user_updated']=$result['user_updated'];

            if($result['picture_file_name'])
            {
                $data[$result['id']]['image_name']=$result['picture_file_name'];
            }
            else
            {
                $data[$result['id']]['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $data[$result['id']]['image_location']=$result['picture_file_full'];
            }
            else
            {
                $data[$result['id']]['image_location']='images/no_image.jpg';
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($data as $row)
        {
            Query_helper::add($destination_tables['rnd_demo_picture'],$row,false);
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
        $data=array();
        foreach($results as &$result)
        {
            $data[$result['id']]['id']=$result['id'];
            $data[$result['id']]['setup_id']=$result['setup_id'];
            $data[$result['id']]['variety_id']=$result['variety_id'];
            $data[$result['id']]['remarks']=$result['remarks'];
            $data[$result['id']]['feedback']=$result['feedback'];
            $data[$result['id']]['status']=$result['status'];
            $data[$result['id']]['date_created']=$result['date_created'];
            $data[$result['id']]['user_created']=$result['user_created'];
            $data[$result['id']]['date_feedback']=$result['date_feedback'];
            $data[$result['id']]['user_feedback']=$result['user_feedback'];
            $data[$result['id']]['date_updated']=$result['date_updated'];
            $data[$result['id']]['user_updated']=$result['user_updated'];

            if($result['picture_file_name'])
            {
                $data[$result['id']]['image_name']=$result['picture_file_name'];
            }
            else
            {
                $data[$result['id']]['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $data[$result['id']]['image_location']=$result['picture_file_full'];
            }
            else
            {
                $data[$result['id']]['image_location']='images/no_image.jpg';
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($data as $row)
        {
            Query_helper::add($destination_tables['rnd_demo_disease_picture'],$row,false);
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
        $data=array();
        foreach($results as &$result)
        {
            $data[$result['id']]['id']=$result['id'];
            $data[$result['id']]['setup_id']=$result['setup_id'];
            $data[$result['id']]['picture_id']=$result['picture_id'];
            $data[$result['id']]['variety_id']=$result['variety_id'];
            $data[$result['id']]['remarks']=$result['remarks'];
            $data[$result['id']]['feedback']=$result['feedback'];
            $data[$result['id']]['date_created']=$result['date_created'];
            $data[$result['id']]['user_created']=$result['user_created'];
            $data[$result['id']]['date_feedback']=$result['date_feedback'];
            $data[$result['id']]['user_feedback']=$result['user_feedback'];
            $data[$result['id']]['date_updated']=$result['date_updated'];
            $data[$result['id']]['user_updated']=$result['user_updated'];

            if($result['picture_file_name'])
            {
                $data[$result['id']]['image_name']=$result['picture_file_name'];
            }
            else
            {
                $data[$result['id']]['image_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $data[$result['id']]['image_location']=$result['picture_file_full'];
            }
            else
            {
                $data[$result['id']]['image_location']='images/no_image.jpg';
            }
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($data as $row)
        {
            Query_helper::add($destination_tables['rnd_demo_fruit_picture'],$row,false);
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
