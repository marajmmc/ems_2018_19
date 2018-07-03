<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_setup extends Root_Controller
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
        elseif ($action == "list_upcoming")
        {
            $this->system_list_upcoming();
        }
        elseif ($action == "get_items_upcoming")
        {
            $this->system_get_items_upcoming();
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
        $data['amount_iou'] = 1;
        if ($method == 'list_all')
        {
            $data['status_forward'] = 1;
            $data['status_approve'] = 1;
        }
        return $data;
    }

    private function get_iou_items()
    {
        return array(
            'accommodation',
            'transportation',
            'food_allowance',
            'miscellaneous'
        );
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
            $data['title'] = "Tour Setup Pending List";
            $data['system_preference_items'] = $this->get_preference();
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
        $this->db->where('tour_setup.status !="' . $this->config->item('system_status_delete') . '"');
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status_forward', $this->config->item('system_status_pending'));
        $this->db->order_by('tour_setup.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
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

    private function system_list_upcoming()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour Setup Upcoming List";
            $data['system_preference_items'] = $this->get_preference('list_upcoming');
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_upcoming", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/list_upcoming");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_upcoming()
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
        $this->db->where('tour_setup.status !="' . $this->config->item('system_status_delete') . '"');
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('tour_setup.status_approve', $this->config->item('system_status_pending'));
        $this->db->order_by('tour_setup.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
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
            $data['title'] = "Tour Setup All List";
            $data['system_preference_items'] = $this->get_preference('list_all');
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
        $user = User_helper::get_user();

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
        $this->db->where('tour_setup.status !="' . $this->config->item('system_status_delete') . '"');
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->order_by('tour_setup.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
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

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data['title'] = "New Tour Setup";

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
            $data["item"] = Array(
                'id' => 0,
                'name' => $result['name'],
                'employee_id' => $result['employee_id'],
                'designation' => $result['designation_name'],
                'department_name' => $result['department_name'],
                'title' => '',
                'date_from' => '',
                'date_to' => '',
                'amount_iou' => 0.00,
                'amount_iou_items' => '',
                'iou_details' => '',
                'remarks' => ''
            );
            $data['items'] = array();
            $data['iou_items'] = $this->get_iou_items();

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
            $this->db->select('tour_setup.*');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');

            $this->db->where('tour_setup.status !="' . $this->config->item('system_status_delete') . '"');
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('Edit', $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward'] != $this->config->item('system_status_pending'))
            {
                //System_helper::invalid_try('Edit', $item_id, 'Invalid try to edit after forward');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Tour Setup Already Forwarded. You can not edit it.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_purpose');
            $this->db->select('tour_purpose.*');
            $this->db->where('tour_purpose.tour_id', $item_id);
            $this->db->where('tour_purpose.status !="' . $this->config->item('system_status_delete') . '"');
            $data['items'] = $this->db->get()->result_array();

            $data['iou_items'] = $this->get_iou_items();
            $data['title'] = 'Edit Tour Setup';

            //pr($data, 1);

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
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $items_iou = $this->input->post('items_iou');

        /*--Start-- Permission Checking */
        if ($id > 0) /* EDIT */
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $delete_condition = array(
                'status !="' . $this->config->item('system_status_delete') . '"',
                'id =' . $id
            );
            if ($user->user_group != 1)
            {
                $delete_condition[] = 'user_id =' . $user->user_id;
            }
            $result = Query_helper::get_info($this->config->item('table_ems_tour_setup'), '*', $delete_condition, 1);

            if (!$result)
            {
                System_helper::invalid_try('save', $id, 'Update Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else /* ADD */
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        /*--End-- Permission Checking */

        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $old_item = array();
        $purposes = Query_helper::get_info($this->config->item('table_ems_tour_purpose'), '*', array('tour_id =' . $id));
        foreach ($purposes as $purpose)
        {
            $old_item[$purpose['id']] = $purpose['purpose'];
        }

        $total_amount_iou = 0.0;
        foreach ($items_iou as $item_iou => $amount_iou)
        {
            $total_amount_iou += $amount_iou;
        }
        $item_head['amount_iou'] = $total_amount_iou; // Common for both INSERT & UPDATE
        $item_head['amount_iou_items'] = json_encode($items_iou); // Common for both INSERT & UPDATE

        $this->db->trans_start(); //DB Transaction Handle START

        if ($id > 0) /* EDIT */
        {
            $item_head['date_from'] = System_helper::get_time($item_head['date_from']);
            $item_head['date_to'] = System_helper::get_time($item_head['date_to']);
            $item_head['user_updated'] = $user->user_id;
            $item_head['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_tour_setup'), $item_head, array('id=' . $id));

            /*-----UPDATE old purposes `status` to `Delete`-----*/
            $data = array();
            $data['status'] = $this->config->item('system_status_delete');
            Query_helper::update($this->config->item('table_ems_tour_purpose'), $data, array("tour_id=" . $id));

            /*-----UPDATE old/INSERT new purpose-----*/
            foreach ($items as $key => $item)
            {
                if (isset($old_item[$key]))
                {
                    $data = array
                    (
                        'purpose' => $item,
                        'status' => $this->config->item('system_status_active'),
                        'date_updated' => $time,
                        'user_updated' => $user->user_id
                    );
                    Query_helper::update($this->config->item('table_ems_tour_purpose'), $data, array('id=' . $key));
                }
                else
                {
                    $data = array
                    (
                        'tour_id' => $id,
                        'purpose' => $item,
                        'status' => $this->config->item('system_status_active'),
                        'date_created' => $time,
                        'user_created' => $user->user_id
                    );
                    Query_helper::add($this->config->item('table_ems_tour_purpose'), $data, false);
                }
            }
        }
        else /* ADD */
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
            $this->db->select('tour_setup.*, tour_setup.id AS tour_id');
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
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('delete', $item_id, 'Delete Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            //data from tour setup purpose table
            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_id', $item_id);
            $this->db->where('tour_setup_purpose.status !=', $this->config->item('system_status_delete'));
            $data['items'] = $this->db->get()->result_array();

            $data['iou_items'] = $this->get_iou_items();
            $data['title'] = 'Delete Tour :: ' . $data['item']['title'];
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time = time();
        $item = $this->input->post('item');
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

        $delete_condition = array(
            'id =' . $id,
            'status !="' . $this->config->item('system_status_delete') . '"'
        );
        if ($user->user_group != 1)
        {
            $delete_condition[] = 'user_id =' . $user->user_id;
        }

        $result = Query_helper::get_info($this->config->item('table_ems_tour_setup'), array('*'), $delete_condition, 1);
        if (!$result)
        {
            System_helper::invalid_try('save_delete', $id, 'Save Delete Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['user_updated'] = $user->user_id;
        $item['date_updated'] = $time;
        Query_helper::update($this->config->item('table_ems_tour_setup'), $item, array("id = " . $id));
        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_DELETE");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL_DELETE");
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
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('forward', $id, 'Forward Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_id', $item_id);
            $this->db->where('tour_setup_purpose.status !="' . $this->config->item('system_status_delete') . '"');
            $data['items'] = $this->db->get()->result_array();

            $data['title'] = 'Forward Tour Setup And Reporting:: ' . $data['item']['title'];
            $data['iou_items'] = $this->get_iou_items();

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
        $user = User_helper::get_user();
        $time = time();
        $item = $this->input->post('item');
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if ($item['status_forward'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_FORWARD')) . ' field is required.';
            $this->json_return($ajax);
        }

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
        $this->db->where('user_info.revision', 1);
        $this->db->where('tour_setup.id', $id);
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $data['item'] = $this->db->get()->row_array();

        if (!$data['item'])
        {
            System_helper::invalid_try('save_forward', $id, 'Save Forward Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($data['item']['status_forward'] == $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Already Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['user_forwarded'] = $user->user_id;
        $item['date_forwarded'] = $time;
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
            $data = array();

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('department.name AS department_name');

            /* $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = user.id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id'); */

            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try('details', $item_id, 'View Non Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids = array();
            $user_ids[$data['item']['user_created']] = $data['item']['user_created'];
            $user_ids[$data['item']['user_updated']] = $data['item']['user_updated'];
            $user_ids[$data['item']['user_forwarded']] = $data['item']['user_forwarded'];
            $user_ids[$data['item']['user_approved']] = $data['item']['user_approved'];
            $data['users'] = System_helper::get_users_info($user_ids);

            //data from tour setup purpose
            $this->db->from($this->config->item('table_ems_tour_purpose') . ' purpose');
            $this->db->select('purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others') . ' others', 'others.tour_setup_purpose_id=purpose.id AND others.status = "' . $this->config->item('system_status_active') . '"', 'LEFT');
            $this->db->select('
                others.id purpose_others_id,
                others.name,
                others.contact_no,
                others.profession,
                others.discussion
            ');
            $this->db->where('purpose.tour_id', $item_id);
            $this->db->where('purpose.status !="' . $this->config->item('system_status_delete') . '"');
            $this->db->order_by('purpose.id', 'ASC');
            $results = $this->db->get()->result_array();
            $items = array();

            foreach ($results as $result)
            {
                $items[$result['id']]['tour_setup_id'] = $result['tour_id'];
                $items[$result['id']]['purpose'] = $result['purpose'];
                $items[$result['id']]['date_reporting'] = $result['date_reporting'];
                $items[$result['id']]['report_description'] = $result['report_description'];
                $items[$result['id']]['recommendation'] = $result['recommendation'];
                $items[$result['id']]['revision_count_reporting'] = $result['revision_count_reporting'];
                if ($result['purpose_others_id'])
                {
                    $items[$result['id']]['others'][$result['purpose_others_id']]['name'] = $result['name'];
                    $items[$result['id']]['others'][$result['purpose_others_id']]['contact_no'] = $result['contact_no'];
                    $items[$result['id']]['others'][$result['purpose_others_id']]['profession'] = $result['profession'];
                    $items[$result['id']]['others'][$result['purpose_others_id']]['discussion'] = $result['discussion'];
                }
            }
            $data['items'] = $items;
            $data['title'] = 'Tour Setup And Reporting Details:: ' . $data['item']['title'];

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

    private function system_print_view($id)
    {
        if (isset($this->permissions['action4']) && ($this->permissions['action4'] == 1))
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
            $this->db->select('tour_setup.*');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');

            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');

            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');

            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('department.name AS department_name');

            $this->db->where('tour_setup.status !="' . $this->config->item('system_status_delete') . '"');
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();

            // Validation START
            if (!$data['item'])
            {
                System_helper::invalid_try('print_view', $item_id, 'Print View Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approve'] != $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Selected tour is not approved yet.';
                $this->json_return($ajax);
            }
            // Validation END

            //data from tour setup purpose
            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_id', $item_id);
            $this->db->where('tour_setup_purpose.status !="' . $this->config->item('system_status_delete') . '"');
            $this->db->order_by('tour_setup_purpose.id', 'ASC');
            $items = $this->db->get()->result_array();

            $tmp_arr = $purpose_ids = array();
            foreach ($items as $row)
            {
                $purpose_ids[] = $row['id'];
                $tmp_arr[$row['id']] = $row;
            }
            $items = $tmp_arr;

            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose_others') . ' tour_setup_purpose_others');
            $this->db->where_in('tour_setup_purpose_others.tour_setup_purpose_id', $purpose_ids);
            $this->db->where('tour_setup_purpose_others.status !="' . $this->config->item('system_status_delete') . '"');
            $results = $this->db->get()->result_array();

            foreach ($results as $result)
            {
                $items[$result['tour_setup_purpose_id']]['others'][$result['id']] = $result;
            }


            $data['items_purpose_others'] = $items;

            $data['title'] = 'Tour Setup And Reporting Print View:: ' . $data['item']['title'];
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/print_view", $data, true));
            $ajax['status'] = true;
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/print_view/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_print_requisition($id)
    {

        if (isset($this->permissions['action4']) && ($this->permissions['action4'] == 1))
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

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('print_requisition', $item_id, 'Print Requisition Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approve'] != $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Selected tour is not approved yet.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_ems_tour_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_id', $item_id);
            $this->db->where('tour_setup_purpose.status !="' . $this->config->item('system_status_delete') . '"');
            $this->db->order_by('tour_setup_purpose.id', 'ASC');
            $data['items'] = $this->db->get()->result_array();

            $data['title'] = 'Tour Setup And Reporting Print View:: ' . $data['item']['title'];

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/print_requisition", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/print_requisition/' . $item_id);
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
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $items_iou = $this->input->post('items_iou');
        $total_amount_iou = 0.0;
        if (!$item_head['title'])
        {
            $this->message = 'The Title field is required.';
            return false;
        }
        /*
        --- Manual Validation for FROM & TO date comparison ---
        */
        $date_from = System_helper::get_time($item_head['date_from']);
        $date_to = System_helper::get_time($item_head['date_to']);
        if ($date_from == '')
        {
            $this->message = 'The ' . $this->lang->line('LABEL_DATE') . ' From field is required.';
            return false;
        }
        else if ($date_to == '')
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
        --- Manual Validation for BLANK or EMPTY items checking ---
        */
        if ($items)
        {
            foreach ($items as $item)
            {
                $item = trim($item);
                if (empty($item))
                {
                    $this->message = 'Unfinished tour purpose in entry.';
                    return false;
                }
            }
        }
        else
        {
            $this->message = 'At least one purpose need to save.';
            return false;
        }
        /*
        --- Manual Validation for IOU AMOUNT items checking ---
        */
        if ($items_iou)
        {
            foreach ($items_iou as $item_iou => $amount_iou)
            {
                $total_amount_iou += $amount_iou;
            }
            if ($total_amount_iou <= 0.0)
            {
                $this->message = 'Total ' . $this->lang->line('LABEL_AMOUNT_IOU') . ' cannot be 0 or, negative.';
                return false;
            }
        }
        else
        {
            $this->message = $this->lang->line('LABEL_AMOUNT_IOU') . ' is required.';
            return false;
        }

        /* $this->load->library('form_validation');
        $this->form_validation->set_rules('item[amount_iou]', $this->lang->line('LABEL_AMOUNT_IOU'), 'required');
        $this->form_validation->set_rules('item[iou_details]', $this->lang->line('LABEL_IOU_DETAILS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        } */

        return true;
    }

    private function system_set_preference()
    {
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = $this->get_preference();
            $data['preference_method_name'] = 'list';
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

    private function system_set_preference_all()
    {
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = $this->get_preference('list_all');
            $data['preference_method_name'] = 'list_all';
            $ajax['status'] = true;
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
}
