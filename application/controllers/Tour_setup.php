<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_setup extends Root_Controller
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
        elseif ($action == "list_waiting")
        {
            $this->system_list_waiting();
        }
        elseif ($action == "get_items_waiting")
        {
            $this->system_get_items_waiting();
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
        elseif ($action == "delete")
        {
            $this->system_delete($id);
        }
        elseif ($action == "save_delete")
        {
            $this->system_save_delete($id);
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
            $data['status_forwarded_tour'] = 1;
            $data['status_approved_tour'] = 1;
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
            $data['title'] = "Tour Pending List";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            $ajax['status'] = true;
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
        $this->db->where('tour_setup.status_forwarded_tour', $this->config->item('system_status_pending'));
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
            $data['title'] = "Tour All List";
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            $ajax['status'] = true;
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
            $data['title'] = "Tour Waiting List";
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
        $this->db->where('tour_setup.status_forwarded_tour', $this->config->item('system_status_forwarded'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_pending'));
        $this->db->order_by('tour_setup.id', 'DESC');
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

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $user = User_helper::get_user();
            $this->db->from($this->config->item('table_login_setup_user') . ' user');
            $this->db->select('user.id, user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name,user_info.ordering');
            
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation_name');
            
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->where('user.status', $this->config->item('system_status_active'));
            $this->db->where('user_info.revision', 1);
            $this->db->where('user.id', $user->user_id);
            $result = $this->db->get()->row_array();

            $data['item'] = Array(
                'id' => 0,
                'name' => $result['name'],
                'employee_id' => $result['employee_id'],
                'designation' => $result['designation_name'],
                'department_name' => $result['department_name'],
                'title' => '',
                'date_from' => '',
                'date_to' => '',
                'amount_iou_request' => System_helper::get_string_amount(0),
                'amount_iou_items' => '',
                'iou_details' => '',
                'remarks' => ''
            );

            $data['items'] = array();
            $data['iou_items'] = Tour_helper::get_iou_items(true);

            $data['title'] = "New Tour";
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/add");
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
            $user = User_helper::get_user();

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*, tour_setup.id AS tour_setup_id, tour_setup.user_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');
            
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
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit others Tour';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Tour of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit Tour of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_purpose');
            $this->db->select('tour_purpose.*');
            $this->db->where('tour_purpose.tour_id', $item_id);
            $this->db->where('tour_purpose.status !=', $this->config->item('system_status_delete'));
            $data['items'] = $this->db->get()->result_array();

            $data['iou_items'] = Tour_helper::get_iou_items();
            $data['title'] = 'Tour Edit :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_setup_id'] . ' )';
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
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
        $id = $this->input->post("id");
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $old_items = $this->input->post('old_items');
        $items_iou = $this->input->post('items_iou');

        $time = time();
        $user = User_helper::get_user();

        /*-----------START Permission & Validation Checking-----------*/
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($id > 0) //for EDIT
        {
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');

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
            $this->db->where('tour_setup.id', $id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Update Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Update others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("Trying to Update others Tour");
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result))
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Update Tour of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Update Tour of other Location';
                $this->json_return($ajax);
            }
            $ajax = Tour_helper::tour_status_check($result, array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        /*-----------END Permission & Validation Checking-----------*/

        $total_amount_iou_request = 0;
        foreach ($items_iou as $amount_iou)
        {
            $total_amount_iou_request += $amount_iou;
        }
        $item_head['amount_iou_request'] = $total_amount_iou_request; // Common for both INSERT & UPDATE
        $item_head['amount_iou_items'] = json_encode($items_iou); // Common for both INSERT & UPDATE

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) //EDIT
        {
            // UPDATE Tour Setup data
            $item_head['date_from'] = System_helper::get_time($item_head['date_from']);
            $item_head['date_to'] = System_helper::get_time($item_head['date_to']);
            $item_head['user_updated'] = $user->user_id;
            $item_head['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_tour_setup'), $item_head, array('id=' . $id));

            // UPDATE old purposes status to Deleted
            Query_helper::update($this->config->item('table_ems_tour_purpose'), array('status' => $this->config->item('system_status_delete')), array("tour_id=" . $id));

            // UPDATE old purposes
            if ($old_items)
            {
                foreach ($old_items as $key => $purpose)
                {
                    $data = array
                    (
                        'purpose' => $purpose,
                        'status' => $this->config->item('system_status_active'),
                        'date_updated' => $time,
                        'user_updated' => $user->user_id
                    );
                    Query_helper::update($this->config->item('table_ems_tour_purpose'), $data, array('id=' . $key));
                }
            }

            // INSERT new purposes
            if ($items)
            {
                $old_purposes = $this->db->get_where($this->config->item('table_ems_tour_purpose'), array('tour_id' => $id))->result_array();
                foreach ($items as $purpose)
                {
                    $old_existing_updated = false;
                    $purpose = trim($purpose);

                    if ($old_purposes)
                    {
                        foreach ($old_purposes AS $old_purpose)
                        {
                            if (strtolower(trim($old_purpose['purpose'])) == (strtolower($purpose))) // Checking, if New purpose already exist and Deleted.
                            {
                                $data = array
                                (
                                    'purpose' => $purpose,
                                    'status' => $this->config->item('system_status_active'),
                                    'date_updated' => $time,
                                    'user_updated' => $user->user_id
                                );
                                $old_existing_updated = Query_helper::update($this->config->item('table_ems_tour_purpose'), $data, array('id=' . $old_purpose['id']));
                            }
                        }
                    }

                    if ($old_existing_updated)
                    {
                        continue;
                    }

                    $data = array
                    (
                        'tour_id' => $id,
                        'purpose' => $purpose,
                        'type' => $this->config->item('system_status_initial'),
                        'status' => $this->config->item('system_status_active'),
                        'date_created' => $time,
                        'user_created' => $user->user_id,
                    );
                    Query_helper::add($this->config->item('table_ems_tour_purpose'), $data, false);
                }
            }
        }
        else //ADD
        {
            $item_head['user_id'] = $user->user_id;
            $item_head['date_from'] = System_helper::get_time($item_head['date_from']);
            $item_head['date_to'] = System_helper::get_time($item_head['date_to']);
            $item_head['user_created'] = $user->user_id;
            $item_head['date_created'] = $time;

            $item_id = Query_helper::add($this->config->item('table_ems_tour_setup'), $item_head);
            foreach ($items as $item)
            {
                $data = array();
                $data['tour_id'] = $item_id;
                $data['purpose'] = $item;
                $data['type'] = $this->config->item('system_status_initial');
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_ems_tour_purpose'), $data, false);
            }
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === true)
        {
            $save_and_new = $this->input->post('system_save_new_status');
            $this->message = $this->lang->line('MSG_SAVED_SUCCESS');
            if ($save_and_new == 1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_SAVED_FAIL');
            $this->json_return($ajax);
        }
    }

    private function system_delete($id)
    {
        if (isset($this->permissions['action3']) && ($this->permissions['action3'] == 1))
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
            $this->db->select('tour_setup.*, tour_setup.id AS tour_id, tour_setup.status AS tour_status');

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
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Delete Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group != $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Delete others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("Trying to Delete others Tour");
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Delete Tour of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Delete Tour of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_PAYMENT_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data['title'] = 'Tour Delete :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_id'] . ' )';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/delete", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/delete/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_delete()
    {
        $item_id = $this->input->post("id");
        $item = $this->input->post('item');
        $time = time();
        $user = User_helper::get_user();
        if (!(isset($this->permissions['action3']) && ($this->permissions['action3'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($item['status'] != $this->config->item('system_status_delete'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Delete field is required.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        /*$this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id=tour_setup.user_id', 'INNER');
        //$this->db->select('user.id, user.employee_id, user.user_name, user.status');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
        //$this->db->select('user_info.name, user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        //$this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        //$this->db->select('department.name AS department_name');*/

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.id', $item_id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Delete Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        if (($user->user_group !=  $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Delete others Tour');
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("Trying to Delete others Tour");
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Delete Tour of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Delete Tour of other Location';
            $this->json_return($ajax);
        }

        $ajax = Tour_helper::tour_status_check($result, array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_PAYMENT_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['user_updated'] = $user->user_id;
        $item['date_updated'] = $time;
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $item_id));
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_DELETED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_DELETED_FAIL");
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
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Forward Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            if (($user->user_group !=  $this->config->item('USER_GROUP_SUPER')) && ($data['item']['user_id'] != $user->user_id))
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Forward others Tour');
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("Trying to Forward others Tour");
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Forward Tour of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Forward Tour of other Location';
                $this->json_return($ajax);
            }

            $ajax = Tour_helper::tour_status_check($data['item'], array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data['title'] = 'Tour Forward :: ' . $data['item']['title'] . ' ( Tour ID:' . $data['item']['tour_setup_id'] . ' )';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward", $data, true));
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
        $id = $this->input->post("id");
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($item['status_forwarded_tour'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_FORWARD')) . ' field is required.';
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
        $this->db->select('tour_setup.*');

        /* $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id=tour_setup.user_id', 'INNER');
        $this->db->select('user.id, user.employee_id, user.user_name, user.status');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
        $this->db->select('user_info.name, user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name'); */

        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('user_area.revision', 1);
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.id', $id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $id, 'Forward Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        if (($user->user_group !=  $this->config->item('USER_GROUP_SUPER')) && ($result['user_id'] != $user->user_id))
        {
            System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Forward others Tour');
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("Trying to Forward others Tour");
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $id, 'Trying to Forward Tour of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Forward Tour of other Location';
            $this->json_return($ajax);
        }

        $ajax = Tour_helper::tour_status_check($result, array(TOUR_NOT_REJECTED, TOUR_REPORTING_NOT_APPROVED, TOUR_NOT_APPROVED, TOUR_NOT_FORWARDED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['date_forwarded_tour'] = $time;
        $item['user_forwarded_tour'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $id));
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_FORWARD_SUCCESS");
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
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to View Tour Details of others');
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("Trying to View Tour Details of others");
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Trying to View Tour Details of other Location');
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
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details", $data, true));
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
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $old_items = $this->input->post('old_items');
        $items_iou = $this->input->post('items_iou');
        
        if (!$item_head['title'])
        {
            $this->message = 'The Title field is required.';
            return false;
        }
        /*--- Manual Validation for FROM & TO date comparison ---*/
        $date_from = System_helper::get_time($item_head['date_from']);
        $date_to = System_helper::get_time($item_head['date_to']);
        if (!$date_from)
        {
            $this->message = 'The ' . $this->lang->line('LABEL_DATE') . ' From field is required.';
            return false;
        }
        else if (!$date_to)
        {
            $this->message = 'The ' . $this->lang->line('LABEL_DATE') . ' To field is required.';
            return false;
        }
        else if ($date_from > $date_to)
        {
            $this->message = 'From Date cannot be greater than To Date.';
            return false;
        }
        /*
        --- Manual Validation for checking BLANK or EMPTY items ---
        */
        /* for add*/
        $post_purposes = array();
        if($items)
        {
            foreach ($items as $item)
            {
                $purpose = trim($item);
                $post_purposes[] = strtolower($purpose);
                if (empty($purpose))
                {
                    $this->message = 'Unfinished tour purpose in entry.';
                    return false;
                }
            }
        }
        /* for edit*/
        if($old_items)
        {
            foreach ($old_items as $old_item)
            {
                $purpose = trim($old_item);
                $post_purposes[] = strtolower($purpose);
                if (empty($purpose))
                {
                    $this->message = 'Unfinished tour purpose in entry.';
                    return false;
                }
            }
        }
        if (empty($post_purposes))
        {
            $this->message = 'At least one purpose need to save.';
            return false;
        }
        /*--- Manual Validation for DUPLICATE purpose entry ---*/
        //$post_purposes_compare = $post_purposes;
        $arr_diff = array_diff_assoc($post_purposes, array_unique($post_purposes));
        if ($arr_diff)
        {
            $this->message = 'Duplicate tour purpose in entry.';
            return false;
        }
        /*--- Manual Validation for IOU AMOUNT items checking ---*/
        $total_amount_iou_request = 0;
        if ($items_iou)
        {
            foreach ($items_iou as $amount_iou)
            {
                $total_amount_iou_request += $amount_iou;
            }
            if ($total_amount_iou_request<=0)
            {
                $this->message = $this->lang->line('LABEL_AMOUNT_TOTAL_IOU') . ' cannot be 0 or, negative.';
                return false;
            }
        }
        else
        {
            $this->message = $this->lang->line('LABEL_AMOUNT_TOTAL_IOU') . ' is required.';
            return false;
        }

        return true;
    }

}