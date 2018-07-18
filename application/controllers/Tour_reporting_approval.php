<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_reporting_approval extends Root_Controller
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

    public function index($action = "list", $id = 0)
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
        elseif ($action == "approve")
        {
            $this->system_approve($id);
        }
        elseif ($action == "save_approve")
        {
            $this->system_save_approve();
        }
        elseif ($action == "reporting_details")
        {
            $this->system_reporting_details();
        }
        elseif ($action == "set_preference")
        {
            $this->system_set_preference();
        }
        elseif ($action == "set_preference_all")
        {
            $this->system_set_preference_all();
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "print_view")
        {
            $this->system_print_view($id);
        }
        elseif ($action == "print_requisition")
        {
            $this->system_print_requisition($id);
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
            $data['status_approved_reporting'] = 1;
        }
        return $data;
    }

    private function get_preference($method = 'list')
    {
        $user = User_helper::get_user();
        $result = Query_helper::get_info($this->config->item('table_system_user_preference'), '*', array('user_id =' . $user->user_id, 'controller ="' . $this->controller_url . '"', 'method ="' . $method . '"'), 1);
        $data = $this->get_preference_headers($method);
        if ($result)
        {
            if ($result['preferences'] != null)
            {
                $preferences = json_decode($result['preferences'], true);
                foreach ($data as $key => $value)
                {
                    if (isset($preferences[$key]))
                    {
                        $data[$key] = $value;
                    }
                    else
                    {
                        $data[$key] = 0;
                    }
                }
            }
        }
        return $data;
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour Pending list for Reporting Approval";
            $ajax['status'] = true;
            $data['system_preference_items'] = $this->get_preference();
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
        $designation_child_ids = Tour_helper::get_child_ids_designation($user->designation);

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
        $this->db->select('user_info.name');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1) // If not SuperAdmin, Then Only child's Tour list will appear.
        {
            $this->db->where_in('designation.id', $designation_child_ids);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
        $this->db->where('tour_setup.status_approved_reporting !=', $this->config->item('system_status_approved'));
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
        $this->db->order_by('tour_setup.id DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $items[$key]['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $items[$key]['department_name'] = '-';
            }
        }

        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour All list For Reporting Approval";
            $ajax['status'] = true;
            $data['system_preference_items'] = $this->get_preference('list_all');
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
        $designation_child_ids = Tour_helper::get_child_ids_designation($user->designation);

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
        $this->db->select('user_info.name');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1) // If not SuperAdmin, Then Only child's Tour list will appear.
        {
            $this->db->where_in('designation.id', $designation_child_ids);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
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
        $this->db->order_by('tour_setup.id DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $items[$key]['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $items[$key]['department_name'] = '-';
            }
        }

        $this->json_return($items);
    }

    private function system_approve($id)
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
            $designation_child_ids = Tour_helper::get_child_ids_designation($user->designation);

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.id, user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('user_area.revision', 1);
            if ($user->user_group != 1) // If not SuperAdmin, Then Only child's Tour list will appear.
            {
                $this->db->where_in('designation.id', $designation_child_ids);
            }
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
            $this->db->where('user_info.revision', 1);
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('Approve', $item_id, 'Approve Reporting Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Edit', $item_id, 'Trying to approve others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to approve others Tour Reporting';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_reporting'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Approved.';
                $this->json_return($ajax);
            }

            $user_ids = array();
            $user_ids[$data['item']['user_created']] = $data['item']['user_created'];
            $user_ids[$data['item']['user_updated']] = $data['item']['user_updated'];
            $user_ids[$data['item']['user_forwarded_tour']] = $data['item']['user_forwarded_tour'];
            $user_ids[$data['item']['user_approved_tour']] = $data['item']['user_approved_tour'];
            $user_ids[$data['item']['user_rollback_tour']] = $data['item']['user_rollback_tour'];
            $user_ids[$data['item']['user_forwarded_payment']] = $data['item']['user_forwarded_payment'];
            $user_ids[$data['item']['user_forwarded_reporting']] = $data['item']['user_forwarded_reporting'];
            $user_ids[$data['item']['user_approved_reporting']] = $data['item']['user_approved_reporting'];
            $user_ids[$data['item']['user_rollback_reporting']] = $data['item']['user_rollback_reporting'];
            $data['users'] = System_helper::get_users_info($user_ids);


            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_purpose');
            $this->db->select('tour_purpose.purpose, tour_purpose.id AS p_id, tour_purpose.type AS purpose_type');
            $this->db->join($this->config->item('table_ems_tour_reporting') . ' tour_reporting', 'tour_reporting.purpose_id = tour_purpose.id', 'LEFT');
            $this->db->select("GROUP_CONCAT( tour_reporting.date_reporting SEPARATOR ', ' ) AS reporting_dates");
            $this->db->where('tour_purpose.tour_id', $item_id);
            $this->db->group_by('p_id');
            $data['items'] = $this->db->get()->result_array();

            $data['item']['name'] = $data['item']['name'] . ' (' . $data['item']['employee_id'] . ')';
            $data['title'] = 'Tour Reporting Approval :: ' . $data['item']['title'];
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

        /* if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
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
            $designation_child_ids = Tour_helper::get_child_ids_designation($user->designation);

            $data = array();
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.id, user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('user_area.revision', 1);
            if ($user->user_group != 1) // If not SuperAdmin, Then Only child's Tour list will appear.
            {
                $this->db->where_in('designation.id', $designation_child_ids);
            }
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
            $this->db->where('user_info.revision', 1);
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('Approve', $item_id, 'Approve Reporting Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Edit', $item_id, 'Trying to approve others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to approve others Tour Reporting';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_reporting'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Approved.';
                $this->json_return($ajax);
            }

            $user_ids = array();
            $user_ids[$data['item']['user_created']] = $data['item']['user_created'];
            $user_ids[$data['item']['user_updated']] = $data['item']['user_updated'];
            $user_ids[$data['item']['user_forwarded_tour']] = $data['item']['user_forwarded_tour'];
            $user_ids[$data['item']['user_approved_tour']] = $data['item']['user_approved_tour'];
            $user_ids[$data['item']['user_rollback_tour']] = $data['item']['user_rollback_tour'];
            $user_ids[$data['item']['user_forwarded_payment']] = $data['item']['user_forwarded_payment'];
            $user_ids[$data['item']['user_forwarded_reporting']] = $data['item']['user_forwarded_reporting'];
            $user_ids[$data['item']['user_approved_reporting']] = $data['item']['user_approved_reporting'];
            $user_ids[$data['item']['user_rollback_reporting']] = $data['item']['user_rollback_reporting'];
            $data['users'] = System_helper::get_users_info($user_ids);


            $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
            $this->db->select('*, tour_reporting.id AS report_id');
            $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id', 'INNER');
            $this->db->select('tour_purpose.purpose, tour_purpose.type AS purpose_type');
            $this->db->where('tour_reporting.tour_id', $item_id);
            $this->db->where('tour_reporting.status !=', $this->config->item('system_status_delete'));
            $this->db->order_by('tour_reporting.purpose_id', 'ASC');
            $this->db->order_by('tour_reporting.date_reporting', 'ASC');
            $data['items'] = $this->db->get()->result_array();


            $data['item']['name'] = $data['item']['name'] . ' (' . $data['item']['employee_id'] . ')';
            $data['title'] = 'Tour Reporting Approval :: ' . $data['item']['title'];
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
        } */
    }

    private function system_save_approve()
    {
        $item_id = $this->input->post("id");
        $item = $this->input->post('item');
        $items = $this->input->post('items');

        $time = time();
        $user = User_helper::get_user();
        $designation_child_ids = Tour_helper::get_child_ids_designation($user->designation);
        /*-------------------------------VALIDATION CHECKING------------------------------------*/
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id');
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.id, user.employee_id, user.user_name, user.status');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
        $this->db->select('user_info.name, user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1) // If not SuperAdmin, Then Only child's Tour list will appear.
        {
            $this->db->where_in('designation.id', $designation_child_ids);
        }
        $this->db->where('tour_setup.id', $item_id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forwarded_reporting', $this->config->item('system_status_forwarded'));
        $this->db->where('user_info.revision', 1);
        $data = $this->db->get()->row_array();
        if (!$data)
        {
            System_helper::invalid_try('Approve', $item_id, 'Approve Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($data))
        {
            System_helper::invalid_try('Edit', $item_id, 'Trying to edit others Tour');
            $ajax['status'] = false;
            $ajax['system_message'] = 'You are trying to approve others Tour';
            $this->json_return($ajax);
        }
        if ($data['status_approved_reporting'] == $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Already Approved.';
            $this->json_return($ajax);
        }
        if (!$this->check_validation_approve())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        /*------------------------------VALIDATION CHECKING (END)---------------------------------*/

        $this->db->trans_start(); //DB Transaction Handle START
        if ($item['status_approved_reporting'] == $this->config->item('system_status_rollback'))
        {
            foreach ($items as $key => $row)
            {
                $update_items = array(
                    'status_completed' => $row
                );
                Query_helper::update($this->config->item('table_ems_tour_purpose'), $update_items, array("id = " . $key));
            }
            $item['status_approved_reporting'] = $this->config->item('system_status_pending');
            $item['status_forwarded_reporting'] = $this->config->item('system_status_pending');
            $item['date_rollback_reporting'] = $time;
            $item['user_rollback_reporting'] = $user->user_id;
            $this->db->set('revision_count_rollback_reporting', 'revision_count_rollback_reporting + 1', FALSE);
        }
        else
        {
            foreach ($items as $key => $row)
            {
                $update_items = array(
                    'status_completed' => $row,
                    'date_completed' => $time,
                    'user_completed' => $user->user_id
                );
                Query_helper::update($this->config->item('table_ems_tour_purpose'), $update_items, array("id = " . $key));
            }
            $item['date_approved_reporting'] = $time;
            $item['user_approved_reporting'] = $user->user_id;
        }
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $item_id));
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

    private function system_reporting_details()
    {
        $html_container_id = $this->input->post('html_container_id');
        $item_id = $this->input->post('id');
        $purpose_id = $this->input->post('p_id');
        $report_date = $this->input->post('r_date');

        $data = array();
        $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
        $this->db->select('*');
        $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id', 'INNER');
        $this->db->select('tour_purpose.purpose');
        $this->db->where('tour_reporting.tour_id', $item_id);
        $this->db->where('tour_reporting.purpose_id', $purpose_id);
        $this->db->where('tour_reporting.date_reporting', $report_date);
        $data['item'] = $this->db->get()->row_array();

        $data['title'] = "Tour Report Purpose";
        $ajax['status'] = true;
        $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view($this->controller_url . "/report_details", $data, true));
        if ($this->message)
        {
            $ajax['system_message'] = $this->message;
        }
        $this->json_return($ajax);
    }

    private function system_set_preference()
    {
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['preference_method_name'] = 'list';
            $ajax['status'] = true;
            $data['system_preference_items'] = $this->get_preference();
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

    private function system_set_preference_all()
    {
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['preference_method_name'] = 'list_all';
            $ajax['status'] = true;
            $data['system_preference_items'] = $this->get_preference('list_all');
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_all');
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

    private function check_validation_approve()
    {
        $item_id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        if ($items)
        {
            $item_ids = array();
            foreach ($items as $key => $item)
            {
                if (!trim($item))
                {
                    $this->message = 'Unfinished Status selection.';
                    return false;
                }
                $item_ids[] = $key;
            }
            $this->db->from($this->config->item('table_ems_tour_purpose'));
            $this->db->select("GROUP_CONCAT(id SEPARATOR ',') AS purpose_ids");
            $this->db->where('tour_id', $item_id);
            $this->db->where('status !=', $this->config->item('system_status_delete'));
            $purpose_ids = explode(',', $this->db->get()->row('purpose_ids'));

            $item_ids = array_map('trim', $item_ids);
            $purpose_ids = array_map('trim', $purpose_ids);
            $diff = array_diff($purpose_ids, $item_ids);
            if (!empty($diff))
            {
                System_helper::invalid_try('Reporting_Approve', $item_id, 'Approve others Report Purpose');
                $this->message = 'Trying to Approve others Tour Report Purpose';
                return false;
            }
        }
        else
        {
            $this->message = 'Status fields are required';
            return false;
        }

        $this->load->library('form_validation');
        if ($item_head['status_approved_reporting'] == $this->config->item('system_status_rollback')) // `Remarks` is mandatory if only Rollback.
        {
            $this->form_validation->set_rules('item[remarks_approved_reporting]', 'Remarks ', 'required');
        }
        $this->form_validation->set_rules('item[status_approved_reporting]', 'Approve ', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
