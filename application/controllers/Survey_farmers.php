<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Survey_farmers extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->common_view_location = 'crop_type_preference_outlet_wise_request';
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_labels();
        $this->lang->load('survey_farmer');
        $this->load->helper('survey_farmer');
    }

    private function language_labels()
    {
        $this->lang->language['LABEL_FARMER_NAME'] = 'Farmer Name';
        $this->lang->language['LABEL_FATHER_HUSBAND_NAME'] = 'Farmer Father Name';
        $this->lang->language['LABEL_MOBILE_NO'] = 'Mobile No';
        $this->lang->language['LABEL_CULTIVATED_AREA_VEGETABLES'] = 'Cultivated Area (Vegetables)';
        $this->lang->language['LABEL_DATE_CREATED'] = 'Entry Time';
        $this->lang->language['LABEL_USER_CREATED'] = 'Entry By';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "list_all")
        {
            $this->system_list_all();
        }
        elseif ($action == "get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif ($action == "add")
        {
            $this->system_add();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $user = User_helper::get_user();
        $data = array();
        $data['id'] = 1;
        $data['farmer_name'] = 1;
        $data['father_husband_name'] = 1;
        $data['mobile_no'] = 1;
        $data['district_name'] = 1;
        $data['upazilla_name'] = 1;
        $data['union_name'] = 1;
        $data['cultivated_area_vegetables'] = 1;

        $data['date_created'] = 1;
        if (($method == 'list_all')||($user->user_group == $this->config->item('USER_GROUP_SUPER')))
        {
            $data['user_created'] = 1;
        }
        if ($method == 'list_all')
        {
            $data['status'] = 1;
            $data['status_forward'] = 1;
            $data['status_approve'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Farmer Base Line Survey Form 2020 List";

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        //$items=Query_helper::get_info($this->config->item('table_ems_survey_farmers'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->db->from($this->config->item('table_ems_survey_farmers').' item');
        $this->db->select('item.*');
        $this->db->select('IF(cultivated_area_vegetables>0, "Yes", "No") cultivated_area_vegetables');
        $this->db->join($this->config->item('table_ems_survey_farmers_districts').' districts','districts.id = item.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_ems_survey_farmers_upazilas').' upazilas','upazilas.id = item.upazilla_id','INNER');
        $this->db->select('upazilas.name upazilla_name');
        $this->db->join($this->config->item('table_ems_survey_farmers_unions').' unions','unions.id = item.union_id','INNER');
        $this->db->select('unions.name union_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=item.user_created AND user_info.revision = 1','INNER');
        $this->db->select('user_info.name user_created');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_created']=System_helper::display_date_time($item['date_created']);
        }
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Farmer Base Line Survey Form 2020 - All List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_all()
    {
        /*$current_records = $this->input->post('total_records');
        if (!$current_records) {
            $current_records = 0;
        }
        $pagesize = $this->input->post('pagesize');
        if (!$pagesize) {
            $pagesize = 100;
        } else {
            $pagesize = $pagesize * 2;
        }

        $this->common_query(); // Call Common part of below Query Stack

        // Additional Conditions -STARTS
        if ($this->locations['division_id'] > 0) {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0) {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0) {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0) {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('pref.status', $this->config->item('system_status_active'));
        $this->db->limit($pagesize, $current_records);
        // Additional Conditions -ENDS

        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        foreach ($items as &$item) {
            $item['requested_on'] = System_helper::display_date_time($item['date_created']);
        }*/

        $items=array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $fields = $this->db->field_data($this->config->item('table_ems_survey_farmers'));
            foreach ($fields as $field)
            {
                $data['item'][$field->name]='';
            }
            $data['items']=array();
            /*$data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $results=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text','district_id'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['upazillas']=array();
            foreach($results as $result)
            {
                $data['upazillas'][$result['district_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_login_setup_location_unions'),array('id value','name text','upazilla_id'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['unions']=array();
            foreach($results as $result)
            {
                $data['unions'][$result['upazilla_id']][]=$result;
            }*/

            $data['districts']=Query_helper::get_info($this->config->item('table_ems_survey_farmers_districts'),array('id value','name text'),array());
            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_upazilas'),array('id value','name text','district_id'),array());
            $data['upazillas']=array();
            foreach($results as $result)
            {
                $data['upazillas'][$result['district_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_unions'),array('id value','name text','upazilla_id'),array());
            $data['unions']=array();
            foreach($results as $result)
            {
                $data['unions'][$result['upazilla_id']][]=$result;
            }

            $data['title'] = "New Farmer Base Line Survey Form 2020";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();
            $data['item']=Query_helper::get_info($this->config->item('table_ems_survey_farmers'),'*',array('id='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),1);
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }

            $data['districts']=Query_helper::get_info($this->config->item('table_ems_survey_farmers_districts'),array('id value','name text'),array());
            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_upazilas'),array('id value','name text','district_id'),array());
            $data['upazillas']=array();
            foreach($results as $result)
            {
                $data['upazillas'][$result['district_id']][]=$result;
            }
            $results=Query_helper::get_info($this->config->item('table_ems_survey_farmers_unions'),array('id value','name text','upazilla_id'),array());
            $data['unions']=array();
            foreach($results as $result)
            {
                $data['unions'][$result['upazilla_id']][]=$result;
            }

            $data['items'] = Query_helper::get_info($this->config->item('table_ems_survey_farmers_details'), array('*'), array("survey_id=" . $item_id,'status ="'.$this->config->item('system_status_active').'"'));

            $data['title'] = "Edit Farmer Base Line Survey Form 2020";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $user = User_helper::get_user();
        $time = time();

        $id = $this->input->post('id');
        $item = $this->input->post('item');
        $items = $this->input->post('items');

        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        if ($id > 0)
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        $results = Query_helper::get_info($this->config->item('table_ems_survey_farmers_details'), array('*'), array("survey_id=" . $id,'status ="'.$this->config->item('system_status_active').'"'));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['id']]=$result;
        }
        $this->db->trans_start(); //DB Transaction Handle START

        $results = $this->db->field_data($this->config->item('table_ems_survey_farmers'));
        $fields=array();
        foreach ($results as $result)
        {
            $fields[$result->name]=isset($item[$result->name])?$item[$result->name]:'';
            $fields['status']=$this->config->item('system_status_active');
            $fields['date_created']=$time;
            $fields['user_created']=$user->user_id;
            unset($fields['id']);
        }

        if ($id > 0)
        {
            Query_helper::update($this->config->item('table_ems_survey_farmers'), $fields,array("id = ".$id), FALSE);

            Query_helper::update($this->config->item('table_ems_survey_farmers_details'), array('status' => $this->config->item('system_status_delete')), array("survey_id=" . $id));
            if(sizeof($items)>0)
            {
                foreach($items as $key=>$info)
                {
                    if(isset($items_old[$key]))
                    {
                        $data=$info;
                        $data['survey_id']=$id;
                        $data['status']= $this->config->item('system_status_active');
                        Query_helper::update($this->config->item('table_ems_survey_farmers_details'), $data,array("id = ".$key, "survey_id=" . $id), FALSE);
                    }
                    else
                    {
                        $data=$info;
                        $data['survey_id']=$id;
                        $data['status']= $this->config->item('system_status_active');
                        Query_helper::add($this->config->item('table_ems_survey_farmers_details'), $data, FALSE);
                    }
                }
            }
        }
        else
        {
            $survey_id=Query_helper::add($this->config->item('table_ems_survey_farmers'), $fields, FALSE);

            if(sizeof($items)>0)
            {
                foreach($items as $info)
                {
                    $data=$info;
                    $data['survey_id']=$survey_id;
                    $data['status']= $this->config->item('system_status_active');
                    Query_helper::add($this->config->item('table_ems_survey_farmers_details'), $data, FALSE);
                }
            }
        }

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);

            $data['title'] = ($this->lang->line('LABEL_OUTLET_NAME')) . "-wise Crop Type Preference Details (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[farmer_name]', $this->lang->line('SURVEY_FARMER_TITLE_FARMER_NAME'), 'required');
        $this->form_validation->set_rules('item[father_husband_name]', $this->lang->line('SURVEY_FARMER_TITLE_FATHER_HUSBAND_NAME'), 'required');
        $this->form_validation->set_rules('item[mobile_no]', $this->lang->line('SURVEY_FARMER_MOBILE_NO'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
