<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_reporting extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;
    public $custom_image_types;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->common_view_location = 'tour_setup';
        $this->locations = User_helper::get_locations();
        $this->custom_image_types = 'gif|jpg|jpe|jpeg|png';
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('tour');
    }

    public function index($action = "list", $id = 0, $id1 = 0)
    {
        if ($action == "list")
        {
            $this->system_list($id);
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
        elseif ($action == "list_waiting")
        {
            $this->system_list_waiting();
        }
        elseif ($action == "get_items_waiting")
        {
            $this->system_get_items_waiting();
        }
        elseif ($action == "list_reporting")
        {
            $this->system_list_reporting($id);
        }
        elseif ($action == "get_reporting_items")
        {
            $this->system_get_reporting_items($id);
        }
        elseif ($action == "reporting")
        {
            $this->system_reporting($id, $id1);
        }
        elseif ($action == "save_reporting")
        {
            $this->system_save_reporting();
        }
        elseif ($action == "forward")
        {
            $this->system_forward($id);
        }
        elseif ($action == "save_forward")
        {
            $this->system_save_forward();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif ($action == "set_preference_waiting")
        {
            $this->system_set_preference('list_waiting');
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
            $this->system_list($id);
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        $data['id'] = 1;
        $data['name'] = 1;
        $data['employee_id'] = 1;
        $data['department_name'] = 1;
        $data['designation'] = 1;
        $data['title'] = 1;
        $data['date_from'] = 1;
        $data['date_to'] = 1;
        $data['amount_iou_request'] = 1;
        if ($method == 'list_all')
        {
            $data['status_approved_payment'] = 1;
            $data['status_paid_payment'] = 1;
            $data['status_forwarded_reporting'] = 1;
            $data['status_approved_reporting'] = 1;
            $data['status_approved_adjustment'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
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

    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour Pending List for Reporting";
            $ajax['status'] = true;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/list");
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
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
        $this->db->select('user_info.name');

        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');

        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        $this->db->where('tour_setup.status_forwarded_reporting !=', $this->config->item('system_status_forwarded'));
        if ($user->user_group != $this->config->item('USER_GROUP_SUPER'))
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        else
        {
            if ($this->locations['division_id'] > 0)
            {
                $this->db->where('user_area.division_id', $this->locations['division_id']);
                if ($this->locations['zone_id'] > 0)
                {
                    $this->db->where('user_area.zone_id', $this->locations['zone_id']);
                    if ($this->locations['territory_id'] > 0)
                    {
                        $this->db->where('user_area.territory_id', $this->locations['territory_id']);
                        if ($this->locations['district_id'] > 0)
                        {
                            $this->db->where('user_area.district_id', $this->locations['district_id']);
                        }
                    }
                }
            }
        }
        $this->db->order_by('tour_setup.id DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_from'] = System_helper::display_date($item['date_from']);
            $item['date_to'] = System_helper::display_date($item['date_to']);
            $item['amount_iou_request'] = System_helper::get_string_amount($item['amount_iou_request']);
            if ($item['designation'] == '')
            {
                $item['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $item['department_name'] = '-';
            }
        }

        $this->json_return($items);
    }

    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method = 'list_all';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour All List for Reporting";
            $ajax['status'] = true;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/list_all");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    public function system_get_items_all()
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
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id, user.user_name, user.status');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
        $this->db->select('user_info.name,user_info.ordering');

        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');

        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        if ($user->user_group != $this->config->item('USER_GROUP_SUPER'))
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        else
        {
            if ($this->locations['division_id'] > 0)
            {
                $this->db->where('user_area.division_id', $this->locations['division_id']);
                if ($this->locations['zone_id'] > 0)
                {
                    $this->db->where('user_area.zone_id', $this->locations['zone_id']);
                    if ($this->locations['territory_id'] > 0)
                    {
                        $this->db->where('user_area.territory_id', $this->locations['territory_id']);
                        if ($this->locations['district_id'] > 0)
                        {
                            $this->db->where('user_area.district_id', $this->locations['district_id']);
                        }
                    }
                }
            }
        }
        $this->db->order_by('tour_setup.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['date_from'] = System_helper::display_date($item['date_from']);
            $item['date_to'] = System_helper::display_date($item['date_to']);
            $item['amount_iou_request'] = System_helper::get_string_amount($item['amount_iou_request']);
            if ($item['designation'] == '')
            {
                $item['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $item['department_name'] = '-';
            }
        }

        $this->json_return($items);
    }

    private function system_list_waiting()
    {
        $user = User_helper::get_user();
        $method = 'list_waiting';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour Waiting List for Reporting";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_waiting", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/list_waiting");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_waiting()
    {
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id, user.user_name, user.status');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
        $this->db->select('user_info.name,user_info.ordering');

        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');

        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
        $this->db->where('tour_setup.status_approved_reporting', $this->config->item('system_status_pending'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        if ($user->user_group != $this->config->item('USER_GROUP_SUPER'))
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        else
        {
            if ($this->locations['division_id'] > 0)
            {
                $this->db->where('user_area.division_id', $this->locations['division_id']);
                if ($this->locations['zone_id'] > 0)
                {
                    $this->db->where('user_area.zone_id', $this->locations['zone_id']);
                    if ($this->locations['territory_id'] > 0)
                    {
                        $this->db->where('user_area.territory_id', $this->locations['territory_id']);
                        if ($this->locations['district_id'] > 0)
                        {
                            $this->db->where('user_area.district_id', $this->locations['district_id']);
                        }
                    }
                }
            }
        }
        $this->db->order_by('tour_setup.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['date_from'] = System_helper::display_date($item['date_from']);
            $item['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $item['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $item['department_name'] = '-';
            }
        }
        $this->json_return($items);
    }

    private function system_list_reporting($id)
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
            $user = User_helper::get_user();

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

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
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Reporting ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit others Tour Reporting');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit others Tour Reporting';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Tour Reporting of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit Tour Reporting of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_APPROVED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data['item']['name'] = $data['item']['name'] . ' (' . $data['item']['employee_id'] . ')';
            $data['title'] = 'Tour Dates for Reporting :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_setup_id'] . ' )';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_reporting", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/list_reporting/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_reporting_items($id)
    {
        if ($id > 0)
        {
            $item_id = $id;
        }
        else
        {
            $item_id = $this->input->post('id');
        }
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        $this->db->where('tour_setup.id', $item_id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Id Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit others Tour Reporting');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Edit others Tour Reporting';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Tour Reporting of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Edit Tour Reporting of other Location';
            $this->json_return($ajax);
        }

        $items = $temp_dates = array();
        $reporting_dates = array(0);

        $date_from = date('Y-m-d H:i:s', $result['date_from']);
        $day_between_dates = (round(($result['date_to'] - $result['date_from']) / (60 * 60 * 24)) + 1);
        for ($i = 0; $i < $day_between_dates; $i++)
        {
            $reporting_date_format = date('d-M-Y', strtotime($date_from . ' +' . $i . ' day'));
            $reporting_date_int = System_helper::get_time($reporting_date_format);
            $items[$i]['sl_no'] = $i + 1;
            $items[$i]['date_reporting'] = $reporting_date_format;
            $items[$i]['date_reporting_int'] = $reporting_date_int;
            $items[$i]['id'] = $reporting_dates[$i] = $reporting_date_int;
            $items[$i]['purpose'] = '-';

            $temp_dates[$reporting_date_int] = $i; // Just for Assigning Purposes, collected from below Query
        }

        $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
        $this->db->select("tour_reporting.date_reporting, GROUP_CONCAT( tour_purpose.purpose SEPARATOR '; ' ) AS purposes");
        $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id', 'INNER');
        $this->db->where('tour_reporting.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_reporting.tour_id', $item_id);
        $this->db->where_in('tour_reporting.date_reporting', $reporting_dates);
        $this->db->group_by('tour_reporting.date_reporting');
        $collected_purposes = $this->db->get()->result_array();
        if ($collected_purposes)
        {
            foreach ($collected_purposes as $collected_purpose)
            {
                $array_key = $temp_dates[$collected_purpose['date_reporting']];
                $items[$array_key]['purpose'] = $collected_purpose['purposes'];
            }
        }

        $this->json_return($items);
    }

    private function system_reporting($item_id, $id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($id > 0)
            {
                $reporting_date = $id;
            }
            else
            {
                $reporting_date = $this->input->post('id');
            }
            $user = User_helper::get_user();

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->where('user_area.revision', 1);
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $data['item'] = $result = $this->db->get()->row_array();
            //------------------Validation-----------------------------------
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Reporting ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit others Tour Reporting');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit others Tour Reporting';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Tour Reporting of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit Tour of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_APPROVED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }
            if (!($reporting_date >= $data['item']['date_from'] && $reporting_date <= $data['item']['date_to']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Invalid Reporting Date');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are Trying with Invalid Reporting Date.';
                $this->json_return($ajax);
            }
            //------------------Validation(END)------------------------------

            $this->db->from($this->config->item('table_ems_tour_purpose'));
            $this->db->select('*');
            $this->db->where('tour_id', $item_id);
            $this->db->where('status !=', $this->config->item('system_status_delete'));
            $data['item']['purposes'] = $this->db->get()->result_array();


            $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
            $this->db->select('tour_reporting.*, tour_reporting.id AS report_id');
            $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id');
            $this->db->select('tour_purpose.purpose');
            $this->db->where('tour_reporting.tour_id', $item_id);
            $this->db->where('tour_reporting.date_reporting', $reporting_date);
            $this->db->where('tour_reporting.status !=', $this->config->item('system_status_delete'));
            $data['items'] = $this->db->get()->result_array();


            $data['item']['image_location_lead_farmer_visit_one'] = "";
            $data['item']['image_name_lead_farmer_visit_one'] = "";

            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            $data['title'] = 'Edit Tour Reporting :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_id'] . ' )';
            $data['reporting_date'] = $reporting_date;

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/reporting", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/reporting/' . $item_id . '/' . $reporting_date);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_reporting()
    {
        $item_id = $this->input->post("id");
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $old_items = $this->input->post('old_items');
        $reporting_date = $item_head['date_reporting'];
        $time = time();
        $user = User_helper::get_user();

        //----------------------------Validation STARTS------------------------------
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        $this->db->where('tour_setup.id', $item_id);
        $this->db->where('status !=', $this->config->item('system_status_delete'));
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Reporting ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Save others Tour Reporting');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Save others Tour Reporting';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Save Tour Reporting of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Save Tour Reporting of other Location';
            $this->json_return($ajax);
        }

        $ajax = Tour_helper::tour_status_check($result, array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_APPROVED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }
        if (!($reporting_date >= $result['date_from'] && $reporting_date <= $result['date_to']))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Invalid Reporting Date');
            $ajax['status'] = false;
            $ajax['system_message'] = 'You are Trying with Invalid Reporting Date.';
            $this->json_return($ajax);
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        //----------------------------Validation ENDS------------------------------

        $this->db->trans_start(); //DB Transaction Handle START

        $reporting_ids = $old_reporting_ids = array();

        // IN-ACTIVE ALL OLD RECORDS
        $data = array('status' => $this->config->item('system_status_delete'));
        Query_helper::update($this->config->item('table_ems_tour_reporting'), $data, array("tour_id=" . $item_id, "date_reporting=" . $item_head['date_reporting']));

        // -----------------------------UPDATE OLD RECORDS BY id
        if ($old_items)
        {
            foreach ($old_items as $key => $old_item)
            {
                $update_item = array(
                    'report_description' => $old_item['report_description'],
                    'recommendation' => $old_item['recommendation'],
                    'name' => $old_item['other_name'],
                    'contact_no' => $old_item['other_contact'],
                    'profession' => $old_item['other_profession'],
                    'discussion' => $old_item['other_discussion'],
                    'status' => $this->config->item('system_status_active'),
                    'date_updated' => $time,
                    'user_updated' => $user->user_id
                );
                $this->db->set('revision_count_reporting', 'revision_count_reporting + 1', FALSE);
                Query_helper::update($this->config->item('table_ems_tour_reporting'), $update_item, array('id=' . $key));

                $old_reporting_ids[] = $key;
            }
        }

        // -------------------------------- INSERT NEW RECORD
        if ($items)
        {
            foreach ($items as $index => $item)
            {
                $item['purpose_additional'] = (isset($item['purpose_additional'])) ? trim($item['purpose_additional']) : '';
                $purpose_id = $item['purpose'];

                if ((trim($item['purpose']) != "") && (trim($item['purpose_additional']) == "")) // IF Purpose Selected
                {
                    $update_item = array(
                        'report_description' => $item['report_description'],
                        'recommendation' => $item['recommendation'],
                        'name' => $item['other_name'],
                        'contact_no' => $item['other_contact'],
                        'profession' => $item['other_profession'],
                        'discussion' => $item['other_discussion'],
                        'status' => $this->config->item('system_status_active'),
                        'date_updated' => $time,
                        'user_updated' => $user->user_id
                    );
                    $this->db->set('revision_count_reporting', 'revision_count_reporting + 1', FALSE);
                    $Query = Query_helper::update($this->config->item('table_ems_tour_reporting'), $update_item, array('tour_id=' . $item_id, 'date_reporting=' . $reporting_date, 'purpose_id=' . $purpose_id));
                    if ($Query) //IF found in Reporting table, then UPDATE and continue for next.
                    {
                        $this->db->where('tour_id', $item_id);
                        $this->db->where('date_reporting', $reporting_date);
                        $this->db->where('purpose_id', $purpose_id);
                        $reporting_ids[$index] = $this->db->get($this->config->item('table_ems_tour_reporting'))->row('id');

                        continue;
                    }
                }
                elseif ((trim($item['purpose']) == "") && (trim($item['purpose_additional']) != "")) // IF New Purpose Entered
                {
                    $this->db->from($this->config->item('table_ems_tour_purpose'));
                    $this->db->select('*');
                    $this->db->where('tour_id', $item_id);
                    $this->db->like('purpose', trim($item['purpose_additional']), 'none');
                    $result = $this->db->get()->row_array();

                    if ($result)
                    {
                        $purpose_id = $result['id'];
                        $update_item = array(
                            'report_description' => $item['report_description'],
                            'recommendation' => $item['recommendation'],
                            'name' => $item['other_name'],
                            'contact_no' => $item['other_contact'],
                            'profession' => $item['other_profession'],
                            'discussion' => $item['other_discussion'],
                            'status' => $this->config->item('system_status_active'),
                            'date_updated' => $time,
                            'user_updated' => $user->user_id
                        );
                        $this->db->set('revision_count_reporting', 'revision_count_reporting + 1', FALSE);
                        $Query = Query_helper::update($this->config->item('table_ems_tour_reporting'), $update_item, array('tour_id=' . $item_id, 'date_reporting=' . $reporting_date, 'purpose_id=' . $purpose_id));
                        if ($Query) // IF New Purpose Already exist in Reporting Table, THEN Activate & UPDATE and continue for next.
                        {
                            $this->db->where('tour_id', $item_id);
                            $this->db->where('date_reporting', $reporting_date);
                            $this->db->where('purpose_id', $purpose_id);
                            $reporting_ids[$index] = $this->db->get($this->config->item('table_ems_tour_reporting'))->row('id');

                            continue;
                        }
                    }
                    else
                    {
                        $insert_purpose = array(
                            'tour_id' => $item_id,
                            'purpose' => trim($item['purpose_additional']),
                            'type' => $this->config->item('system_status_additional'),
                            'status' => $this->config->item('system_status_active'),
                            'date_created' => $time,
                            'user_created' => $user->user_id
                        );
                        $purpose_id = Query_helper::add($this->config->item('table_ems_tour_purpose'), $insert_purpose);
                    }
                }

                $insert_new_items = array(
                    'tour_id' => $item_id,
                    'purpose_id' => $purpose_id,
                    'date_reporting' => $item_head['date_reporting'],
                    'report_description' => $item['report_description'],
                    'recommendation' => $item['recommendation'],
                    'revision_count_reporting' => 1,
                    'name' => trim($item['other_name']),
                    'contact_no' => trim($item['other_contact']),
                    'profession' => trim($item['other_profession']),
                    'discussion' => $item['other_discussion'],
                    'status' => $this->config->item('system_status_active'),
                    'date_created' => $time,
                    'user_created' => $user->user_id
                );
                $reporting_ids[$index] = Query_helper::add($this->config->item('table_ems_tour_reporting'), $insert_new_items);
            }
        }

        // Image Saving
        $path = 'images/tour_reporting/' . $item_id;
        $uploaded_files = System_helper::upload_file($path, $this->custom_image_types);
        foreach($uploaded_files as $file) // Validation for uploaded Files
        {
            if(!$file['status'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$file['message'];
                $this->json_return($ajax);
                die();
            }
        }

        $image_info = array();

        //-----------Image save into DB -START-------------
        foreach ($old_reporting_ids as $old_id)
        {
            $old_img_key = 'old_image_' . $old_id;
            if (array_key_exists($old_img_key, $uploaded_files))
            {
                $image_info[$old_id] = array(
                    'image_name' => $uploaded_files[$old_img_key]['info']['file_name'],
                    'image_location' => $path . '/' . $uploaded_files[$old_img_key]['info']['file_name']
                );
            }
        }
        foreach ($reporting_ids as $KEY => $new_id)
        {
            $new_img_key = 'new_image_' . $KEY;
            if (array_key_exists($new_img_key, $uploaded_files))
            {
                $image_info[$new_id] = array(
                    'image_name' => $uploaded_files[$new_img_key]['info']['file_name'],
                    'image_location' => $path . '/' . $uploaded_files[$new_img_key]['info']['file_name'],
                );
            }
        }
        foreach ($image_info as $rep_id => $image)
        { // Insert Image name & location into DB
            Query_helper::update($this->config->item('table_ems_tour_reporting'), $image, array('id=' . $rep_id));
        }
        //-----------Image save into DB -END-------------

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_reporting($item_id);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }

    }

    private function system_forward($id)
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
            $user = User_helper::get_user();

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id=tour_setup.user_id', 'INNER');
            $this->db->select('user.id, user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->where('user_area.revision', 1);
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $data['item'] = $item = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Reporting Forward Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward others Tour Reporting');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Forward others Tour Reporting';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Tour Reporting of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Forward Tour Reporting of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_REPORTING_NOT_FORWARDED, TOUR_PAYMENT_PAID, TOUR_APPROVED));
            if (!$ajax['status'])
            {
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

            $data['purposes'] = $this->db->get_where($this->config->item('table_ems_tour_purpose'), array('tour_id' => $item_id))->result_array();

            $items = $temp_dates = array();
            $reporting_dates = array(0);

            $date_from = date('Y-m-d H:i:s', $item['date_from']);
            $day_between_dates = (round(($item['date_to'] - $item['date_from']) / (60 * 60 * 24)) + 1);
            for ($i = 0; $i < $day_between_dates; $i++)
            {
                $reporting_date_format = date('d-M-Y', strtotime($date_from . ' +' . $i . ' day'));
                $reporting_date_int = System_helper::get_time($reporting_date_format);
                $items[$i]['sl_no'] = $i + 1;
                $items[$i]['date_reporting'] = $reporting_date_format;
                $items[$i]['date_reporting_int'] = $reporting_dates[$i] = $reporting_date_int;
                $items[$i]['purpose'] = '-';

                $temp_dates[$reporting_date_int] = $i; // Just for Assigning Purposes, collected from below Query
            }

            $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
            $this->db->select("tour_reporting.date_reporting, GROUP_CONCAT( tour_purpose.purpose SEPARATOR ';' ) AS purposes");
            $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id', 'INNER');
            $this->db->where('tour_reporting.status !=', $this->config->item('system_status_delete'));
            $this->db->where('tour_reporting.tour_id', $item_id);
            $this->db->where_in('tour_reporting.date_reporting', $reporting_dates);
            $this->db->group_by('tour_reporting.date_reporting');
            $collected_purposes = $this->db->get()->result_array();
            if ($collected_purposes)
            {
                foreach ($collected_purposes as $collected_purpose)
                {
                    $array_key = $temp_dates[$collected_purpose['date_reporting']];
                    $items[$array_key]['purpose'] = $collected_purpose['purposes'];
                }
            }
            $data['items'] = $items;

            $data['title'] = 'Tour Reporting Forward :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_setup_id'] . ' )';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward_reporting", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/forward/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_forward()
    {
        $item_id = $this->input->post("id");
        $item_head = $this->input->post('item');
        $time = time();
        $user = User_helper::get_user();

        //--------Check Permission--------
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        /*------------------Check Validation--------------------*/
        if (!($item_head['status_forwarded_reporting']))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Forward field is required.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        $this->db->where('tour_setup.id', $item_id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Reporting Forward Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward others Tour Reporting');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Forward others Tour Reporting';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Tour Reporting of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Forward Tour Reporting of other Location';
            $this->json_return($ajax);
        }

        $ajax = Tour_helper::tour_status_check($result, array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_REPORTING_NOT_FORWARDED, TOUR_PAYMENT_PAID, TOUR_APPROVED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $update_item = $item_head;
        $update_item['date_forwarded_reporting'] = $time;
        $update_item['user_forwarded_reporting'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_tour_setup'), $update_item, array("id = " . $item_id));
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
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
            $user = User_helper::get_user();

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=tour_setup.user_id', 'INNER');
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

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to View Tour Details of others');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to View Tour Details of others';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to View Tour Details of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to View Tour Details of other Location';
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

    private function check_validation()
    {
        $items = $this->input->post('items');
        $old_items = $this->input->post('old_items');

        //--- Manual Validation for BLANK or EMPTY items checking ---
        $purpose_count = 0;
        if ($items)
        {
            foreach ($items as $item)
            {
                $purpose = $item['purpose'];
                $additional_purpose = (isset($item['purpose_additional'])) ? trim($item['purpose_additional']) : '';
                $report_description = trim($item['report_description']);
                $recommendation = trim($item['recommendation']);
                if ((empty($purpose) && empty($additional_purpose)) || empty($report_description) || empty($recommendation))
                {
                    $this->message = 'Unfinished reporting in entry.';
                    return false;
                }
                if (($purpose != '') && ($additional_purpose != ''))
                {
                    $this->message = 'Both Select Purpose & New Purpose not allowed at a time.';
                    return false;
                }
                $purpose_count++;
            }
        }
        if ($old_items)
        {
            foreach ($old_items as $item)
            {
                $purpose = $item['purpose'];
                $report_description = trim($item['report_description']);
                $recommendation = trim($item['recommendation']);
                if ((empty($purpose) && empty($additional_purpose)) || empty($report_description) || empty($recommendation))
                {
                    $this->message = 'Unfinished reporting in entry.';
                    return false;
                }
                $purpose_count++;
            }
        }
        if ($purpose_count == 0)
        {
            $this->message = 'Atleast one Report need to be Saved.';
            return false;
        }

        return true;
    }
}
