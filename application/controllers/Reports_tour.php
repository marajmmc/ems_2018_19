<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports_tour extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('tour');
    }

    public function index($action = "search", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "search")
        {
            $this->system_search();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference();
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        else
        {
            $this->system_search();
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        $data['sl_no'] = 1;
        $data['id'] = 1;
        $data['employee'] = 1;
        $data['department_name'] = 1;
        $data['designation_name'] = 1;
        $data['division_name'] = 1;
        $data['zone_name'] = 1;
        $data['territory_name'] = 1;
        $data['title'] = 1;
        $data['date_from'] = 1;
        $data['date_to'] = 1;
        $data['amount_iou_request'] = 1;
        $data['status_forwarded_tour'] = 1;
        $data['status_approved_tour'] = 1;
        $data['status_approved_payment'] = 1;
        $data['status_paid_payment'] = 1;
        $data['status_forwarded_reporting'] = 1;
        $data['status_approved_reporting'] = 1;
        $data['status_approved_adjustment'] = 1;
        $data['status_extended_tour'] = 1;
        $data['details_button'] = 1;
        return $data;
    }

    private function system_set_preference($method = 'search')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_search()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $this->db->from($this->config->item('table_login_setup_user') . ' user');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation_name');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = user.id', 'INNER');
            $this->db->select('user_area.*');
            if ($this->locations['division_id'] > 0)
            {
                $this->db->where('user_area.division_id', $this->locations['division_id']);
                if ($this->locations['zone_id'] > 0)
                {
                    $this->db->where('user_area.zone_id', $this->locations['zone_id']);
                    if ($this->locations['territory_id'] > 0)
                    {
                        $this->db->where('user_area.territory_id', $this->locations['territory_id']);
                    }
                }
            }
            $this->db->where('user_area.revision', 1);
            $this->db->where('user_info.revision', 1);
            $this->db->where('user.status !=', $this->config->item('system_status_delete'));
            $this->db->order_by('user_info.ordering', 'ASC');
            $results = $this->db->get()->result_array();
            $all_user = array();
            foreach ($results as &$result)
            {
                $result['value'] = $result['user_id'];
                $result['text'] = $result['employee_id'] . '-' . $result['name'] . ' (' . $result['designation_name'] . ')';
                $all_user[] = $result;
            }

            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));
            $data['zones'] = array();
            $data['territories'] = array();
            if ($this->locations['division_id'] > 0)
            {
                $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id ="' . $this->locations['division_id'] . '"'));
                if ($this->locations['zone_id'] > 0)
                {
                    $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id ="' . $this->locations['zone_id'] . '"'));
                }
            }

            $data['user_info'] = $all_user;
            $data['user_counter'] = count($data['user_info']);
            $data['date_from'] = '';
            $data['date_to'] = '';

            $data['title'] = "Tour Report";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/search", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
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
        $user = User_helper::get_user();
        $method = 'search';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['options'] = $this->input->post('report');
            // Storing search options in session for 'Refresh' & 'Load More'
            if (!$data['options'] && $this->session->has_userdata('tour_report_criteria' . $user->user_id))
            {
                $data['options'] = $this->session->userdata('tour_report_criteria' . $user->user_id);
            }
            else
            {
                $this->session->set_userdata('tour_report_criteria' . $user->user_id, $data['options']);
            }

            $data['title'] = "Tour Report ( Search Result )";
            $ajax['status'] = true;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_report_container", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/" . $method);
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

        $employee_id = $this->input->post('employee_id');
        $user_id = $this->input->post('user_id');

        $division_id = $this->input->post('division_id');
        $zone_id = $this->input->post('zone_id');
        $territory_id = $this->input->post('territory_id');

        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        //getting tour data for grid list view
        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
        $this->db->select('user_info.name AS username');

        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation_name');

        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = user.id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' divisions', 'divisions.id = user_area.division_id', 'LEFT');
        $this->db->select('divisions.name AS division_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zones', 'zones.id = user_area.zone_id', 'LEFT');
        $this->db->select('zones.name AS zone_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territories', 'territories.id = user_area.territory_id', 'LEFT');
        $this->db->select('territories.name AS territory_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' districts', 'districts.id = user_area.district_id', 'LEFT');
        $this->db->select('districts.name district_name');

        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        if ($date_from)
        {
            $this->db->where('tour_setup.date_from >=', System_helper::get_time($date_from));
        }
        if ($date_to)
        {
            $this->db->where('tour_setup.date_to <=', System_helper::get_time($date_to));
        }
        if ($division_id > 0)
        {
            $this->db->where('divisions.id', $division_id);
            if ($zone_id > 0)
            {
                $this->db->where('zones.id', $zone_id);
                if ($territory_id > 0)
                {
                    $this->db->where('territories.id', $territory_id);
                }
            }
        }
        // Either from Dropdown or, from Input box
        if (is_numeric($employee_id) && ($employee_id > 0))
        {
            $this->db->where('user.employee_id', $employee_id);
        }
        else if ($user_id && ($user_id != ""))
        {
            $this->db->where('tour_setup.user_id', $user_id);
        }
        $this->db->order_by('divisions.id, zones.id, territories.id, districts.id, tour_setup.id DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['employee'] = $item['employee_id'] . '-' . $item['username'];
            if (!$item['district_name'])
            {
                $item['district_name'] = '-';
            }
            if (!$item['territory_name'])
            {
                $item['territory_name'] = '-';
            }
            if (!$item['zone_name'])
            {
                $item['zone_name'] = '-';
            }
            if (!$item['division_name'])
            {
                $item['division_name'] = '-';
            }
            $item['date_from'] = System_helper::display_date($item['date_from']);
            $item['date_to'] = System_helper::display_date($item['date_to']);
            $item['amount_iou_request'] = System_helper::get_string_amount($item['amount_iou_request']);
        }
        $this->json_return($items);
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
            $method = 'search';
            $data = array();

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation, designation.id as designation_id');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->where('user_area.revision', 1);
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'View Details Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to View Tour Details of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to View Tour Details of other Location';
                $this->json_return($ajax);
            }

            $user_ids = array();
            $user_ids[$data['item']['user_created']] = $data['item']['user_created'];
            $user_ids[$data['item']['user_updated']] = $data['item']['user_updated'];
            $user_ids[$data['item']['user_forwarded_tour']] = $data['item']['user_forwarded_tour'];
            $user_ids[$data['item']['user_approved_tour']] = $data['item']['user_approved_tour'];
            $user_ids[$data['item']['user_rejected_tour']] = $data['item']['user_rejected_tour'];
            $user_ids[$data['item']['user_rollback_tour']] = $data['item']['user_rollback_tour'];
            $user_ids[$data['item']['user_approved_payment']] = $data['item']['user_approved_payment'];
            $user_ids[$data['item']['user_paid_payment']] = $data['item']['user_paid_payment'];
            $user_ids[$data['item']['user_updated_adjustment']] = $data['item']['user_updated_adjustment'];
            $user_ids[$data['item']['user_approved_adjustment']] = $data['item']['user_approved_adjustment'];
            $user_ids[$data['item']['user_forwarded_reporting']] = $data['item']['user_forwarded_reporting'];
            $user_ids[$data['item']['user_approved_reporting']] = $data['item']['user_approved_reporting'];
            $user_ids[$data['item']['user_rollback_reporting']] = $data['item']['user_rollback_reporting'];
            $data['users'] = System_helper::get_users_info($user_ids);

            $data['title'] = 'Tour Details :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_setup_id'] . ' )';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#popup_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/" . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_my_editable($item)
    {
        if (($this->locations['division_id'] > 0) && ($this->locations['division_id'] != $item['division_id']))
        {
            return false;
        }
        if (($this->locations['zone_id'] > 0) && ($this->locations['zone_id'] != $item['zone_id']))
        {
            return false;
        }
        if (($this->locations['territory_id'] > 0) && ($this->locations['territory_id'] != $item['territory_id']))
        {
            return false;
        }
        if (($this->locations['district_id'] > 0) && ($this->locations['district_id'] != $item['district_id']))
        {
            return false;
        }
        return true;
    }
}
