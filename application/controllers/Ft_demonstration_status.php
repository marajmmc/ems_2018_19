<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ft_demonstration_status extends Root_Controller
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
        $this->common_view_location = 'Ft_demonstration_status';
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('fd_budget');
        $this->lang->load('field_day');
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
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "get_growing_area")
        {
            $this->system_get_growing_area($id);
        }
        elseif ($action == "get_lead_farmer_by_growing_area")
        {
            $this->system_get_lead_farmer_by_growing_area($id);
        }
        elseif ($action == "get_arm_competitor_varieties")
        {
            $this->system_get_arm_competitor_varieties($id);
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
        $data = array();
        $data['id'] = 1;
        $data['year'] = 1;
        $data['season'] = 1;
        $data['outlet_name'] = 1;
        $data['growing_area'] = 1;
        $data['lead_farmer_name'] = 1;
        $data['other_farmer_name'] = 1;
        $data['variety1_name'] = 1;
        $data['variety2_name'] = 1;
        if ($method == 'list_all')
        {
            $data['status_forward'] = 1;
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
            $data['title'] = "Demonstration Status List";
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
        $this->db->select('demonstration.id, demonstration.year, CONCAT(demonstration.name_other_farmer, " (", demonstration.phone_other_farmer, ")") other_farmer_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $this->db->select('season.name season');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $this->db->select('areas.name growing_area');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $this->db->select('CONCAT(lead_farmers.name, " (", lead_farmers.mobile_no, ")") lead_farmer_name');

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
        $this->db->where('demonstration.status !=', $this->config->item('system_status_delete'));
        $this->db->where('demonstration.status_forward !=', $this->config->item('system_status_forwarded'));
        $this->db->order_by('demonstration.id', 'DESC');
        $items = $this->db->get()->result_array();
        /*foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }*/
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
            $data['title'] = "Demonstration Status All List";
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
        $this->db->select('demonstration.id, demonstration.year, CONCAT(demonstration.name_other_farmer, " (", demonstration.phone_other_farmer, ")") other_farmer_name, status_forward');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = demonstration.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = demonstration.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $this->db->select('season.name season');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $this->db->select('areas.name growing_area');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $this->db->select('CONCAT(lead_farmers.name, " (", lead_farmers.mobile_no, ")") lead_farmer_name');

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
        $this->db->where('demonstration.status !=', $this->config->item('system_status_delete'));
        $this->db->order_by('demonstration.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        /*foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }*/
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $time = time();
            $data = array();
            $data['item'] = array(
                'id' => 0,
                'year' => '',
                'season_id' => 0,
                'outlet_id' => 0,
                'crop_id' => 0,
                'crop_type_id' => 0,
                'date_variety1_sowing' => $time,
                'date_variety2_sowing' => $time,
                'date_expected_evaluation' => $time
            );

            $outlet_conditions = array('revision=1', 'type =' . $this->config->item('system_customer_type_outlet_id'));
            if ($this->locations['district_id'] > 0)
            {
                $outlet_conditions[] = 'district_id =' . $this->locations['district_id'];
            }
            else if ($this->locations['territory_id'] > 0)
            {
                $results = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id'), array('territory_id =' . $this->locations['territory_id']));
                $district_ids = implode(', ', array_column($results, 'id'));
                $outlet_conditions[] = 'district_id IN (' . $district_ids . ')';
            }
            $data['outlets'] = Query_helper::get_info($this->config->item('table_login_csetup_cus_info'), array('*, customer_id value', 'name text'), $outlet_conditions, 0, 0, array('name ASC'));

            $data['crops'] = Query_helper::get_info($this->config->item('table_login_setup_classification_crops'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $data['seasons'] = Query_helper::get_info($this->config->item('table_ems_setup_seasons'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

            $data['title'] = "Create new Demonstration Status";
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

    private function system_save()
    {
        $item_id = $this->input->post('id');
        $item_head = $this->input->post('item');

        $user = User_helper::get_user();
        $time = time();

        // Permission Checking
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        //pr($this->input->post());

        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        if ($item_id > 0) //EDIT
        {

        }
        else //ADD
        {
            //Main Table Insert
            $item_head['date_variety1_sowing'] = System_helper::get_time($item_head['date_variety1_sowing']);
            $item_head['date_variety2_sowing'] = System_helper::get_time($item_head['date_variety2_sowing']);
            $item_head['date_expected_evaluation'] = System_helper::get_time($item_head['date_expected_evaluation']);

            $item_info = $item_head; // Data for Info. table

            $item_head['status'] = $this->config->item('system_status_active');; //From Input
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;

            //pr($item_head);
            $demonstration_id = Query_helper::add($this->config->item('table_ems_demonstration_status'), $item_head, FALSE);
        }
        //Details Table Insert (EDIT & ADD)
        $item_info['demonstration_id'] = $demonstration_id;
        $item_info['revision'] = 1;
        $item_info['date_created'] = $time;
        $item_info['user_created'] = $user->user_id;
        Query_helper::add($this->config->item('table_ems_demonstration_status_info'), $item_info, FALSE);

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

    private function system_get_growing_area($id = 0)
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
        $html_container_id = $this->input->post('html_container_id');

        $condition = array('status !="' . $this->config->item('system_status_delete') . '"');
        if ($item_id > 0)
        {
            $condition[] = 'outlet_id =' . $item_id;
        }
        $data['items'] = Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_areas'), 'id value, CONCAT_WS(" - ", name, address) text', $condition, 0, 0, array('name'));
        if ($data['items'])
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view("dropdown_with_select", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }

    private function system_get_lead_farmer_by_growing_area($id = 0)
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
        $html_container_id = $this->input->post('html_container_id');

        $this->db->from($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers');
        $this->db->select('lead_farmers.id value, CONCAT(lead_farmers.name, " (", lead_farmers.mobile_no, ")") text');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = lead_farmers.area_id', 'INNER');

        $this->db->where('areas.status', $this->config->item('system_status_active'));
        $this->db->where('lead_farmers.status', $this->config->item('system_status_active'));
        $this->db->where('areas.id', $item_id);
        $this->db->order_by('areas.name', 'ASC');
        $this->db->order_by('lead_farmers.ordering', 'ASC');
        $data['items'] = $this->db->get()->result_array();
        if ($data['items'])
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view("dropdown_with_select", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }

    private function system_get_arm_competitor_varieties($id = 0)
    {
        if ($id > 0)
        {
            $crop_type_id = $id;
        }
        else
        {
            $crop_type_id = $this->input->post('id');
        }
        $this->load->helper('Fd_budget');
        $variety_arm_upcoming = Fd_budget_helper::get_variety_arm_upcoming($crop_type_id);
        $variety_all = Fd_budget_helper::get_variety_all($crop_type_id);

        $arm_upcoming['items'] = (sizeof($variety_arm_upcoming) > 0) ? $variety_arm_upcoming[$crop_type_id] : array();
        $all['items'] = (sizeof($variety_all) > 0) ? $variety_all[$crop_type_id] : array();

        $ajax['system_content'][] = array("id" => "#variety1_id", "html" => $this->load->view("dropdown_with_select", $arm_upcoming, true));
        $ajax['system_content'][] = array("id" => "#variety2_id", "html" => $this->load->view("dropdown_with_select", $all, true));
        if (!(sizeof($variety_arm_upcoming) > 0))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "No data found for " . $this->lang->line('LABEL_VARIETY1_NAME');
            $this->json_return($ajax);
        }
        elseif (!(sizeof($variety_all) > 0))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "No data found for " . $this->lang->line('LABEL_VARIETY2_NAME');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = true;
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $item = $this->input->post('item');

        $this->form_validation->set_rules('item[year]', $this->lang->line('LABEL_YEAR'), 'required|numeric');
        $this->form_validation->set_rules('item[season_id]', $this->lang->line('LABEL_SEASON'), 'required|numeric');
        $this->form_validation->set_rules('item[outlet_id]', $this->lang->line('LABEL_OUTLET_NAME'), 'required|numeric');
        $this->form_validation->set_rules('item[growing_area_id]', $this->lang->line('LABEL_GROWING_AREA'), 'required|numeric');

        $this->form_validation->set_rules('item[lead_farmer_id]', $this->lang->line('LABEL_LEAD_FARMER_NAME'), 'numeric'); // Here, Only checks if Numeric

        $this->form_validation->set_rules('item[crop_id]', $this->lang->line('LABEL_CROP_NAME'), 'required|numeric');
        $this->form_validation->set_rules('item[crop_type_id]', $this->lang->line('LABEL_CROP_TYPE'), 'required|numeric');
        $this->form_validation->set_rules('item[variety1_id]', $this->lang->line('LABEL_VARIETY1_NAME'), 'required|numeric');
        $this->form_validation->set_rules('item[date_variety1_sowing]', $this->lang->line('LABEL_DATE_SOWING') . ' of ' . $this->lang->line('LABEL_VARIETY1_NAME'), 'required');
        $this->form_validation->set_rules('item[date_expected_evaluation]', $this->lang->line('LABEL_DATE_EXPECTED_EVALUATION'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }

        if (!($item['lead_farmer_id'] > 0))
        {
            if (($item['name_other_farmer'] == "") && ($item['phone_other_farmer'] == "") && ($item['address_other_farmer'] == ""))
            {
                $this->message = $this->lang->line('LABEL_LEAD_FARMER_NAME') . ' field is required. OR, Enter New Farmer';
                return false;
            }
            if ($item['name_other_farmer'] == "")
            {
                $this->message = 'New Farmer Name cannot be Empty';
                return false;
            }
            else if ($item['phone_other_farmer'] == "")
            {
                $this->message = 'New Farmer Phone No. cannot be Empty';
                return false;
            }
            else if ($item['address_other_farmer'] == "")
            {
                $this->message = 'New Farmer Address cannot be Empty';
                return false;
            }
        }

        return true;
    }
}
