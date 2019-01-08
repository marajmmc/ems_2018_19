<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ft_demonstration_status_recommendation extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;
    public $evaluation_items;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->common_view_location = 'Ft_demonstration_status';
        $this->locations = User_helper::get_locations();
        $this->evaluation_items = array('Excellent', 'Very Good', 'Good', 'Average', 'Poor');
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->language_config();
        $this->load->helper('Ft_demonstration');
    }

    private function language_config()
    {
        $this->lang->language['LABEL_GROWING_AREA'] = "Growing Area";
        $this->lang->language['LABEL_CROP_TYPE'] = 'Crop Type';
        $this->lang->language['LABEL_VARIETY1_NAME'] = 'Variety (Selected)';
        $this->lang->language['LABEL_VARIETY2_NAME'] = 'Variety (Compare with)';
        $this->lang->language['LABEL_DATE_SOWING_VARIETY1'] = 'Sowing Date (Selected)';
        $this->lang->language['LABEL_DATE_SOWING_VARIETY2'] = 'Sowing Date (Compare with)';
        $this->lang->language['LABEL_DATE_TRANSPLANTING_VARIETY1'] = 'Transplanting Date (Selected)';
        $this->lang->language['LABEL_DATE_TRANSPLANTING_VARIETY2'] = 'Transplanting Date (Compare with)';
        $this->lang->language['LABEL_FARMERS_COMMENT'] = 'Farmer\'s Comment';
        $this->lang->language['LABEL_TMPOS_COMMENT'] = 'TMPO\'s Comment';
        $this->lang->language['LABEL_ZSCS_COMMENT'] = 'ZSC\'s Comment';
        $this->lang->language['LABEL_STATUS_RECOMMENDATION'] = 'Status Recommendation';
        // Messages
        $this->lang->language['MSG_NOT_FORWARDED_DEMONSTRATION'] = 'This Demonstration has not been Forwarded yet.';
        $this->lang->language['MSG_RECOMMENDED_DEMONSTRATION'] = 'This Demonstration has been Recommended Already.';
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
        elseif ($action == "approve")
        {
            $this->system_approve($id);
        }
        elseif ($action == "save_approve")
        {
            $this->system_save_approve();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "set_preference_list")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_list_all")
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

    private function get_preference_headers($method)
    {
        $data = array(); // initialize
        $data['id'] = 1;
        $data['year'] = 1;
        $data['season'] = 1;
        $data['outlet_name'] = 1;
        $data['growing_area'] = 1;
        $data['lead_farmer_name'] = 1;
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['variety1_name'] = 1;
        $data['variety2_name'] = 1;
        $data['date_sowing_variety1'] = 1;
        $data['date_sowing_variety2'] = 1;
        $data['date_transplanting_variety1'] = 1;
        $data['date_transplanting_variety2'] = 1;
        $data['date_expected_evaluation'] = 1;
        $data['date_actual_evaluation'] = 1;
        if ($method == 'list_all')
        {
            $data['status_recommendation'] = 1;
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
            $data['title'] = "Demonstration Recommendation List";
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
        $this->db->from($this->config->item('table_ems_demonstration_status') . ' demonstration');
        $this->db->select('demonstration.*');

        $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $this->db->select('season.name season');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $this->db->select('areas.name growing_area');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $this->db->select('IF( (demonstration.lead_farmer_id > 0), lead_farmers.name, CONCAT(demonstration.name_other_farmer, " (Other)") ) AS lead_farmer_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = demonstration.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('demonstration.status', $this->config->item('system_status_active'));
        $this->db->where('demonstration.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('demonstration.status_recommendation', $this->config->item('system_status_pending'));
        $this->db->order_by('demonstration.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['date_sowing_variety1'] = System_helper::display_date($item['date_sowing_variety1']);
            $item['date_sowing_variety2'] = System_helper::display_date($item['date_sowing_variety2']);
            $item['date_transplanting_variety1'] = System_helper::display_date($item['date_transplanting_variety1']);
            $item['date_transplanting_variety2'] = System_helper::display_date($item['date_transplanting_variety2']);
            $item['date_expected_evaluation'] = System_helper::display_date($item['date_expected_evaluation']);
            $item['date_actual_evaluation'] = System_helper::display_date($item['date_actual_evaluation']);
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
            $data['title'] = "Demonstration Recommendation All List";
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
        $current_records = $this->input->post('total_records');
        if (!$current_records)
        {
            $current_records = 0;
        }
        $pagesize = $this->input->post('pagesize');
        if (!$pagesize)
        {
            $pagesize = 100;
        }
        else
        {
            $pagesize = $pagesize * 2;
        }
        $this->db->from($this->config->item('table_ems_demonstration_status') . ' demonstration');
        $this->db->select('demonstration.*');

        $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $this->db->select('season.name season');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $this->db->select('areas.name growing_area');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $this->db->select('IF( (demonstration.lead_farmer_id > 0), lead_farmers.name, CONCAT(demonstration.name_other_farmer, " (Other)") ) AS lead_farmer_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = demonstration.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('demonstration.status', $this->config->item('system_status_active'));
        $this->db->where('demonstration.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->order_by('demonstration.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['date_sowing_variety1'] = System_helper::display_date($item['date_sowing_variety1']);
            $item['date_sowing_variety2'] = System_helper::display_date($item['date_sowing_variety2']);
            $item['date_transplanting_variety1'] = System_helper::display_date($item['date_transplanting_variety1']);
            $item['date_transplanting_variety2'] = System_helper::display_date($item['date_transplanting_variety2']);
            $item['date_expected_evaluation'] = System_helper::display_date($item['date_expected_evaluation']);
            $item['date_actual_evaluation'] = System_helper::display_date($item['date_actual_evaluation']);
        }
        $this->json_return($items);
    }

    private function system_approve($id)
    {
        if (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }

            $this->db->from($this->config->item('table_ems_demonstration_status') . ' demonstration');
            $this->db->select('demonstration.*');

            $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
            $this->db->select('season.name season');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name outlet_name');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
            $this->db->select('areas.name growing_area');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
            $this->db->select('IF( (demonstration.lead_farmer_id > 0), CONCAT( lead_farmers.name, " (", lead_farmers.mobile_no, ")" ), CONCAT(demonstration.name_other_farmer, " (", demonstration.phone_other_farmer, ")") ) AS lead_farmer_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
            $this->db->select('crop.name crop_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = demonstration.crop_type_id', 'INNER');
            $this->db->select('crop_type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
            $this->db->select('variety1.name variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
            $this->db->select('variety2.name variety2_name');

            $this->db->where('demonstration.status', $this->config->item('system_status_active'));
            $this->db->where('demonstration.id', $item_id);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($result['status_forward'] != $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_NOT_FORWARDED_DEMONSTRATION');
                $this->json_return($ajax);
            }
            if ($result['status_recommendation'] != $this->config->item('system_status_pending'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_RECOMMENDED_DEMONSTRATION');
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;
            $data['accordion'] = array('collapse' => 'in');
            $data['info_basic'] = Ft_demonstration_helper::get_basic_info($result);

            // Image & Video data
            $result_file = Query_helper::get_info($this->config->item('table_ems_demonstration_status_image_video'), array('*'), array('demonstration_id =' . $item_id, 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('file_type'));
            $data['info_image'] = array();
            foreach ($result_file as $key => $file)
            {
                $data['info_image'][$file['file_type']][$key]['file_location_variety1'] = $file['file_location_variety1'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety1'] = $file['remarks_variety1'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety1'] = $file['date_uploaded_variety1'];

                $data['info_image'][$file['file_type']][$key]['file_location_variety2'] = $file['file_location_variety2'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety2'] = $file['remarks_variety2'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety2'] = $file['date_uploaded_variety2'];
            }

            $data['title'] = "Recommend Demonstration Status ( ID:" . $item_id . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/approve", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/approve/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_approve()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        //Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $result = Query_helper::get_info($this->config->item('table_ems_demonstration_status'), array('*'), array('id =' . $item_id, 'status ="' . $this->config->item('system_status_active') . '"'), 1);
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_forward'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_NOT_FORWARDED_DEMONSTRATION');
            $this->json_return($ajax);
        }
        if ($result['status_recommendation'] != $this->config->item('system_status_pending'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_RECOMMENDED_DEMONSTRATION');
            $this->json_return($ajax);
        }
        //Recommendation Validation Checking
        if (!$this->check_validation_recommendation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['date_recommendation'] = $time;
        $item['user_recommendation'] = $user->user_id;
        // Main Table UPDATE
        Query_helper::update($this->config->item('table_ems_demonstration_status'), $item, array("id =" . $item_id), FALSE);

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

            $this->db->from($this->config->item('table_ems_demonstration_status') . ' demonstration');
            $this->db->select('demonstration.*');

            $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
            $this->db->select('season.name season');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name outlet_name');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
            $this->db->select('areas.name growing_area');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
            $this->db->select('IF( (demonstration.lead_farmer_id > 0), CONCAT( lead_farmers.name, " (", lead_farmers.mobile_no, ")" ), CONCAT(demonstration.name_other_farmer, " (", demonstration.phone_other_farmer, ")") ) AS lead_farmer_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
            $this->db->select('crop.name crop_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = demonstration.crop_type_id', 'INNER');
            $this->db->select('crop_type.name crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
            $this->db->select('variety1.name variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
            $this->db->select('variety2.name variety2_name');

            $this->db->where('demonstration.status !=', $this->config->item('system_status_delete'));
            $this->db->where('demonstration.id', $item_id);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;
            $data['accordion'] = array('collapse' => 'in');
            $data['info_basic'] = Ft_demonstration_helper::get_details_info($result);

            // Image & Video data
            $result_file = Query_helper::get_info($this->config->item('table_ems_demonstration_status_image_video'), array('*'), array('demonstration_id =' . $item_id, 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('file_type'));
            $data['info_image'] = array();
            foreach ($result_file as $key => $file)
            {
                $data['info_image'][$file['file_type']][$key]['file_location_variety1'] = $file['file_location_variety1'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety1'] = $file['remarks_variety1'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety1'] = $file['date_uploaded_variety1'];

                $data['info_image'][$file['file_type']][$key]['file_location_variety2'] = $file['file_location_variety2'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety2'] = $file['remarks_variety2'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety2'] = $file['date_uploaded_variety2'];
            }

            $data['title'] = "Demonstration Status Details ( ID:" . $item_id . " )";
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

    private function check_validation_recommendation()
    {
        $item = $this->input->post('item');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[remarks_zsc]', $this->lang->line('LABEL_ZSCS_COMMENT'), 'required|trim');
        $this->form_validation->set_rules('item[evaluation]', 'Evaluation', 'required|trim');
        $this->form_validation->set_rules('item[status_recommendation]', $this->lang->line('LABEL_STATUS'), 'required|trim');
        if (($item['status_recommendation'] == $this->config->item('system_status_incomplete')) || ($item['status_recommendation'] == $this->config->item('system_status_damaged')))
        {
            $this->form_validation->set_rules('item[remarks_recommendation]', 'Recommendation', 'required|trim');
        }
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
