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
        $this->locations = User_helper::get_locations();
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
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
        else
        {
            $this->system_list($id);
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        if ($method == 'list_all')
        {
            $data['name'] = 1;
            $data['employee_id'] = 1;
            $data['department_name'] = 1;
            $data['designation'] = 1;
            $data['title'] = 1;
            $data['date_from'] = 1;
            $data['date_to'] = 1;
            $data['amount_iou'] = 1;
            $data['iou_details'] = 1;
            $data['remarks'] = 1;
            $data['status_forward'] = 1;
            $data['status_approve'] = 1;
        }
        else
        {
            $data['name'] = 1;
            $data['employee_id'] = 1;
            $data['department_name'] = 1;
            $data['designation'] = 1;
            $data['title'] = 1;
            $data['date_from'] = 1;
            $data['date_to'] = 1;
            $data['amount_iou'] = 1;
            $data['iou_details'] = 1;
            $data['remarks'] = 1;
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
            $data['title'] = "Tour Setup Pending List";
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
        $this->db->where('user.status', $this->config->item('system_status_active'));
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1 && $user->user_group != 2)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forward', 'Pending');
        $this->db->order_by('tour_setup.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $items[$key]['designation'] = 'n/a';
            }
            if ($item['department_name'] == '')
            {
                $items[$key]['department_name'] = 'n/a';
            }
        }

        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "Tour Setup All List";
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
        $this->db->where('user.status', $this->config->item('system_status_active'));
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1 && $user->user_group != 2)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->order_by('tour_setup.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $items[$key]['designation'] = 'n/a';
            }
            if ($item['department_name'] == '')
            {
                $items[$key]['department_name'] = 'n/a';
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
                'name' => $result['name'] . ' (' . $result['employee_id'] . ')',
                'designation' => $result['designation_name'],
                'department_name' => $result['department_name'],
                'title' => '',
                'date_from' => time(),
                'date_to' => time(),
                'amount_iou' => '',
                'iou_details' => '',
                'remarks' => ''
            );

            $data['items'] = array();
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
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
            if (($this->input->post('id')))
            {
                $item_id = $this->input->post('id');
            }
            else
            {
                $item_id = $id;
            }

            $data = array();

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
            //$this->db->where('user.status', $this->config->item('system_status_active'));
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();


            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_purpose');
            $this->db->select('tour_purpose.*');
            $this->db->where('tour_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_purpose.status', 'Active');
            $data['items'] = $this->db->get()->result_array();

            if ($data['item'])
            {
                $data['item']['name'] = $data['item']['name'] . ' (' . $data['item']['employee_id'] . ')';
            }
            if (!$data['item'])
            {
                System_helper::invalid_try('Edit', $item_id, 'Id Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward'] != 'Pending')
            {
                System_helper::invalid_try('Edit', $item_id, 'Invalid try to edit after forward');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Tour Setup Already Forwarded. You can not edit it.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Edit', $item_id, $this->config->item('system_edit_others'));
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to edit others tour setup';
                $this->json_return($ajax);
            }

            $data['title'] = 'Edit Tour Setup';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
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
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $results_old = array();
        $time = time();
        /*--Start-- Permission Checking */
        if ($id > 0) /* EDIT */
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $old_item = Query_helper::get_info($this->config->item('table_ems_tour_setup'), '*', array('status !="' . $this->config->item('system_status_delete') . '"', 'id =' . $id), 1);
            $results = Query_helper::get_info($this->config->item('table_ems_tour_setup_purpose'), '*', array('tour_setup_id =' . $id));

            foreach ($results as $result)
            {
                $results_old[$result['id']] = $result['purpose'];
            }
            if (!$old_item)
            {
                System_helper::invalid_try('Save Non Exists', $id);
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

        $this->db->trans_start(); //DB Transaction Handle START

        if ($id > 0) /* EDIT */
        {
            /*-----UPDATE MASTER TABLE DATA-----*/
            $data = array();
            $data['title'] = $item_head['title'];
            $data['date_from'] = System_helper::get_time($item_head['date_from']);
            $data['date_to'] = System_helper::get_time($item_head['date_to']);
            $data['amount_iou'] = $item_head['amount_iou'];
            $data['iou_details'] = $item_head['iou_details'];
            $data['remarks'] = $item_head['remarks'];
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ems_tour_setup'), $data, array('id=' . $id));

            /*-----UPDATE old purposes `status` to `Delete`-----*/
            Query_helper::update($this->config->item('table_ems_tour_setup_purpose'), array('status' => 'Delete'), array('tour_setup_id=' . $id));

            /*-----UPDATE old/INSERT new purpose-----*/
            foreach ($items as $key => $item)
            {
                if (isset($results_old[$key]))
                {
                    $data = array(
                        'purpose' => $item,
                        'status' => 'Active',
                        'date_updated' => $time,
                        'user_updated' => $user->user_id
                    );
                    Query_helper::update($this->config->item('table_ems_tour_setup_purpose'), $data, array('id=' . $key));
                }
                else
                {
                    $data = array(
                        'tour_setup_id' => $id,
                        'purpose' => $item,
                        'status' => 'Active',
                        'date_created' => $time,
                        'user_created' => $user->user_id
                    );
                    Query_helper::add($this->config->item('table_ems_tour_setup_purpose'), $data, false);
                }
            }
            /* --End-- Item saving (In three table consequently)*/
        }
        else /* ADD */
        {
            /* --Start-- Item saving (In two table consequently)*/
            $data = array(); //Main Data
            $data['user_id'] = $user->user_id;
            $data['title'] = $item_head['title'];
            $data['date_from'] = System_helper::get_time($item_head['date_from']);
            $data['date_to'] = System_helper::get_time($item_head['date_to']);
            $data['amount_iou'] = $item_head['amount_iou'];
            $data['iou_details'] = $item_head['iou_details'];
            $data['remarks'] = $item_head['remarks'];
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['status'] = $this->config->item('system_status_active');
            $item_id = Query_helper::add($this->config->item('table_ems_tour_setup'), $data);
            foreach ($items as $item)
            {
                $data = array();
                $data['tour_setup_id'] = $item_id;
                $data['purpose'] = $item;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_ems_tour_setup_purpose'), $data, false);
            }
            /* --End-- Item saving (In three table consequently)*/
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

    private function system_forward($id)
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
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item']))
            {
                System_helper::invalid_try('Forward', $item_id, 'Trying to forward others tour setup');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to forward others tour setup';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_login_setup_user') . ' user');
            $this->db->select('user.id, user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->where('user_info.revision', 1);
            $this->db->where('user.id', $data['item']['user_created']);
            $result = $this->db->get()->row_array();

            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            $data['item']['designation'] = $result['designation'];
            $data['item']['department_name'] = $result['department_name'];

            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
            $data['items'] = $this->db->get()->result_array();

            $data['title'] = 'Forward Tour Setup And Reporting:: ' . $data['item']['title'];

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_forward", $data, true));
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
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if (!$this->check_validation_forward())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start(); //DB Transaction Handle START
            $item['status_forward'] = 'Forwarded';
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[title]', 'Title', 'required');
        $this->form_validation->set_rules('item[date_from]', $this->lang->line('LABEL_DATE') . ' From', 'required');
        $this->form_validation->set_rules('item[date_to]', $this->lang->line('LABEL_DATE') . ' To', 'required');
        $this->form_validation->set_rules('item[amount_iou]', $this->lang->line('LABEL_IOU_AMOUNT'), 'required');
        $this->form_validation->set_rules('item[iou_details]', $this->lang->line('LABEL_IOU_DETAILS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }

        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        /*
        --- Manual Validation for FROM & TO date comparison ---
        */
        $date_from = System_helper::get_time($item_head['date_from']);
        $date_to = System_helper::get_time($item_head['date_to']);
        if ($date_from > $date_to)
        {
            $this->message = 'From Date cannot be greater than To Date';
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
                    $this->message = 'Unfinished tour setup in entry.';
                    return false;
                }
            }
        }
        else
        {
            $this->message = 'At least one purpose need to save.';
            return false;
        }

        return true;
    }

    private function check_validation_forward()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[status_forward]', 'Forward ', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
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
}
