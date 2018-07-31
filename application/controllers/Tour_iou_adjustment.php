<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_iou_adjustment extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
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
            $this->system_get_items($id);
        }
        elseif ($action == "list_all")
        {
            $this->system_list_all();
        }
        elseif ($action == "get_items_all")
        {
            $this->system_get_items_all($id);
        }
        elseif ($action == "adjustment")
        {
            $this->system_edit_adjustment($id);
        }
        elseif ($action == "save_adjustment")
        {
            $this->system_save_adjustment($id);
        }
        elseif ($action == "approve")
        {
            $this->system_approve($id);
        }
        elseif ($action == "save_approve")
        {
            $this->system_save_approve();
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
        /* elseif ($action == "details")
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
        } */
        else
        {
            $this->system_list($id);
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $user = User_helper::get_user();
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
        if (($method == 'list_all') || ($user->user_id == 1))
        {
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
            $data['user_group'] = $user->user_group;
            $data['title'] = "Tour List for IOU Adjustment";
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

    private function system_get_items($action = 0)
    {
        $user = User_helper::get_user();
        /*  $action :-
                1 = Adjustment
                2 = Approve
                0 = SuperAdmin & others
        */
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
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        $this->db->where('tour_setup.status_paid_payment', $this->config->item('system_status_paid'));
        $this->db->where('tour_setup.status_approved_adjustment !=', $this->config->item('system_status_approved'));
        if (($user->user_id != 1) && ($action == 2)) //Only for Approve Permission
        {
            $this->db->where('tour_setup.revision_count_edit_adjustment >', 0);
            $this->db->where('tour_setup.status_approved_adjustment', $this->config->item('system_status_forwarded'));
        }
        $this->db->order_by('tour_setup.id DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            $items[$key]['date_to'] = $item['status_approved_adjustment'];
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
        $user = User_helper::get_user();
        $method = 'list_all';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['user_group'] = $user->user_group;
            $data['title'] = "Tour All List for IOU Adjustment";
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

    public function system_get_items_all($action = 0)
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
        /*  $action :-
                1 = Adjustment
                2 = Approve
                0 = SuperAdmin & others
        */
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
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        $this->db->where('tour_setup.status_paid_payment', $this->config->item('system_status_paid'));
        if (($user->user_id != 1) && ($action == 1)) //Only for Adjustment Permission
        {
            $this->db->where('tour_setup.status_approved_adjustment !=', $this->config->item('system_status_approved'));
        }
        if (($user->user_id != 1) && ($action == 2)) //Only for Approve Permission
        {
            $this->db->where('tour_setup.revision_count_edit_adjustment >', 0);
            $this->db->where('tour_setup.status_approved_adjustment !=', $this->config->item('system_status_pending'));
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

    private function system_edit_adjustment($id)
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
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
            $this->db->where('tour_setup.status_approved_payment', $this->config->item('system_status_approved'));
            $this->db->where('tour_setup.status_approved_reporting', $this->config->item('system_status_approved'));
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('Adjustment', $id, 'Edit Adjustment Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_paid_payment'] != $this->config->item('system_status_paid'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Not Paid yet. Cannot Adjust before Pay.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_adjustment'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Approved. Cannot Adjust now.';
                $this->json_return($ajax);
            }

            $data['total_iou_amount'] = Tour_helper::tour_amount($item_id);
            $data['title'] = 'Tour IOU Adjustment :: ' . $data['item']['title'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/edit_adjustment", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/adjustment/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_adjustment()
    {
        $id = $this->input->post("id");
        $item = $this->input->post('item');
        $time = time();
        $user = User_helper::get_user();

        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if (!$this->check_validation_adjustment())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $check_condition = array(
            'id =' . $id,
            'status ="' . $this->config->item('system_status_active') . '"',
            'status_approved_tour ="' . $this->config->item('system_status_approved') . '"',
            'status_approved_payment ="' . $this->config->item('system_status_approved') . '"',
            'status_approved_reporting ="' . $this->config->item('system_status_approved') . '"'
        );
        $result = Query_helper::get_info($this->config->item('table_ems_tour_setup'), '*', $check_condition, 1);
        if (!$result)
        {
            System_helper::invalid_try('Payment', $id, 'Edit Payment Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_paid_payment'] != $this->config->item('system_status_paid'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Not Paid yet. Cannot Adjust before Pay.';
            $this->json_return($ajax);
        }
        if ($result['status_approved_adjustment'] == $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Already Approved. Cannot Adjust now.';
            $this->json_return($ajax);
        }
        if(Tour_helper::tour_amount($id) == trim($item['amount_iou_adjustment']))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Same Voucher Amount. No Adjustment Needed.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['amount_iou_adjustment'] = trim($item['amount_iou_adjustment']);
        $item['status_approved_adjustment'] = $this->config->item('system_status_forwarded');
        $item['date_updated_adjustment'] = $time;
        $item['user_updated_adjustment'] = $user->user_id;
        $this->db->set('revision_count_edit_adjustment', 'revision_count_edit_adjustment + 1', FALSE);
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $id));
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
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
            $this->db->where('tour_setup.status_approved_payment', $this->config->item('system_status_approved'));
            $this->db->where('tour_setup.status_approved_reporting', $this->config->item('system_status_approved'));
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try('Approve', $id, 'Approve Adjustment Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_adjustment'] != $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'No Adjustment done yet.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_adjustment'] == $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Adjustment already Approved. Cannot Approved again';
                $this->json_return($ajax);
            }

            $data['total_iou_amount'] = Tour_helper::tour_amount($item_id);
            $data['title'] = 'Tour IOU Adjustment Approval :: ' . $data['item']['title'];
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
        $id = $this->input->post("id");
        $item = $this->input->post('item');
        $time = time();
        $user = User_helper::get_user();

        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if (!$this->check_validation_approve())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $check_condition = array(
            'id =' . $id,
            'status ="' . $this->config->item('system_status_active') . '"',
            'status_approved_tour ="' . $this->config->item('system_status_approved') . '"',
            'status_approved_payment ="' . $this->config->item('system_status_approved') . '"',
            'status_approved_reporting ="' . $this->config->item('system_status_approved') . '"'
        );
        $result = Query_helper::get_info($this->config->item('table_ems_tour_setup'), '*', $check_condition, 1);
        if (!$result)
        {
            System_helper::invalid_try('Approve', $id, 'Approve Adjustment Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_approved_adjustment'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'No Adjustment done yet.';
            $this->json_return($ajax);
        }
        if ($result['status_approved_adjustment'] == $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Adjustment already Approved. Cannot Approved again';
            $this->json_return($ajax);
        }

        //pr($this->input->post());

        $this->db->trans_start(); //DB Transaction Handle START
        $item['date_approved_adjustment'] = $time;
        $item['user_approved_adjustment'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $id));
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

    private function check_validation_adjustment()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[amount_iou_adjustment]', 'IOU Voucher', 'trim|required|greater_than_equal_to[0]');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }

    private function check_validation_approve()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status_approved_adjustment]', 'Approve', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}