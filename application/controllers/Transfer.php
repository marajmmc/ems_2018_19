<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        /*R&D Demo*/
        //$this->rnd_demo_varieties();
        //$this->setup_fruit_picture();
        //$this->rnd_demo_picture();
        //$this->rnd_demo_disease_picture();
        //$this->rnd_demo_fruit_picture();

        /*Farmer And Field Visit*/
        //$this->field_visit_setup_farmer();
        //$this->field_visit_setup_farmer_varieties();
        //$this->field_visit_visits_picture();
        //$this->field_visit_fruit_picture();
        //$this->field_visit_disease_picture();
        $this->survey_variety_arm();
    }

    /*R&D Demo*/
    private function rnd_demo_varieties()
    {
        $source_tables=array(
            'rnd_demo_varieties'=>'arm_ems.ems_tm_rnd_demo_varieties',
        );
        $destination_tables=array(
            'rnd_demo_varieties'=>$this->config->item('table_ems_ft_rnd_demo_varieties'),
        );
        $results=Query_helper::get_info($source_tables['rnd_demo_varieties'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $result['revision']=1;
            if($result['status']=='Deleted')
            {
                $result['revision']=2;
            }
            unset($result['status']);
            Query_helper::add($destination_tables['rnd_demo_varieties'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success varieties';
        }
        else
        {
            echo 'Failed varieties';
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

        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
            Query_helper::add($destination_tables['setup_fruit_picture'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success setup fruit picture';
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
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
            Query_helper::add($destination_tables['rnd_demo_picture'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success rnd demo picture';
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
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
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
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
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

    /*Farmer And Field Visit*/
    private function field_visit_setup_farmer()
    {
        $source_tables=array(
            'farmers'=>'arm_ems.ems_tm_farmers',
        );
        $destination_tables=array(
            'farmers'=>$this->config->item('table_ems_ft_field_visit_setup_farmer'),
        );
        $results=Query_helper::get_info($source_tables['farmers'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['farmers'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Farmers';
        }
        else
        {
            echo 'Failed Farmers';
        }
    }
    private function field_visit_setup_farmer_varieties()
    {
        $source_tables=array(
            'farmer_varieties'=>'arm_ems.ems_tm_farmer_varieties',
        );
        $destination_tables=array(
            'farmer_varieties'=>$this->config->item('table_ems_ft_field_visit_setup_farmer_varieties'),
        );
        $results=Query_helper::get_info($source_tables['farmer_varieties'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $result['revision']=1;
            if($result['status']=='Deleted')
            {
                $result['revision']=2;
            }
            unset($result['status']);
            Query_helper::add($destination_tables['farmer_varieties'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success farmer varieties';
        }
        else
        {
            echo 'Failed farmer varieties';
        }
    }
    private function field_visit_visits_picture()
    {
        $source_tables=array(
            'visits_picture'=>'arm_ems.ems_tm_visits_picture',
        );
        $destination_tables=array(
            'visits_picture'=>$this->config->item('table_ems_ft_field_visit_visits_picture'),
        );
        $results=Query_helper::get_info($source_tables['visits_picture'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            Query_helper::add($destination_tables['visits_picture'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success visits picture';
        }
        else
        {
            echo 'Failed visits picture';
        }
    }
    private function field_visit_fruit_picture()
    {
        $source_tables=array(
            'fruit_picture'=>'arm_ems.ems_tm_visits_fruit_picture',
        );
        $destination_tables=array(
            'fruit_picture'=>$this->config->item('table_ems_ft_field_visit_fruit_picture'),
        );
        $results=Query_helper::get_info($source_tables['fruit_picture'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
            Query_helper::add($destination_tables['fruit_picture'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success fruit picture';
        }
        else
        {
            echo 'Failed fruit picture';
        }
    }
    private function field_visit_disease_picture()
    {
        $source_tables=array(
            'disease_picture'=>'arm_ems.ems_tm_visits_disease_picture',
        );
        $destination_tables=array(
            'disease_picture'=>$this->config->item('table_ems_ft_field_visit_disease_picture'),
        );
        $results=Query_helper::get_info($source_tables['disease_picture'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
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
            }
            else
            {
                $result['image_location']='images/no_image.jpg';
            }
            unset($result['picture_file_name']);
            unset($result['picture_file_full']);
            unset($result['picture_url']);
            Query_helper::add($destination_tables['disease_picture'],$result,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success disease picture';
        }
        else
        {
            echo 'Failed disease picture';
        }
    }

    /*ARM Variety Info*/
    private function survey_variety_arm()
    {
        $source_tables=array(
            'variety_arm'=>'arm_ems.ems_survey_product',
        );
        $destination_tables=array(
            'variety_arm_characteristics'=>$this->config->item('table_ems_survey_variety_arm_characteristics'),
            'variety_arm_files'=>$this->config->item('table_ems_survey_variety_arm_files'),
        );
        $results=Query_helper::get_info($source_tables['variety_arm'],'*',array());
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($results as $result)
        {
            $data_characteristics=array();
            $data_characteristics['id']=$result['id'];
            $data_characteristics['variety_id']=$result['variety_id'];
            $data_characteristics['characteristics']=$result['characteristics'];
            $data_characteristics['comparison']=$result['comparison'];
            $data_characteristics['remarks']=$result['remarks'];
            $data_characteristics['date_start1']=$result['date_start'];
            $data_characteristics['date_end1']=$result['date_end'];
            $data_characteristics['date_start2']=$result['date_start2'];
            $data_characteristics['date_end2']=$result['date_end2'];
            $data_characteristics['date_created']=$result['date_created'];
            $data_characteristics['user_created']=$result['user_created'];
            $data_characteristics['date_updated']=$result['date_updated'];
            $data_characteristics['user_updated']=$result['user_updated'];
            Query_helper::add($destination_tables['variety_arm_characteristics'],$data_characteristics,false);

            $data_files=array();
            $data_files['variety_id']=$result['variety_id'];
            $data_files['file_type']=$this->config->item('system_file_type_image');
            if($result['picture_file_name'])
            {
                $data_files['file_name']=$result['picture_file_name'];
            }
            else
            {
                $data_files['file_name']='no_image.jpg';
            }

            if($result['picture_file_full'])
            {
                $data_files['file_location']=str_replace('survey_product','survey_variety_arm',$result['picture_file_full']);
            }
            else
            {
                $data_files['file_location']='images/no_image.jpg';
            }
            $data_files['date_created']=$result['date_created'];
            $data_files['user_created']=$result['user_created'];
            $data_files['date_updated']=$result['date_updated'];
            $data_files['user_updated']=$result['user_updated'];
            Query_helper::add($destination_tables['variety_arm_files'],$data_files,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success ARM Variety Info';
        }
        else
        {
            echo 'Failed ARM Variety Info';
        }
    }
}
