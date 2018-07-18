<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_reporting extends Root_Controller
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
            $data['status_forwarded_reporting'] = 1;
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
            $data['title'] = "Tour Pending List for Reporting";
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
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        $this->db->where('tour_setup.status_forwarded_reporting !=', $this->config->item('system_status_forwarded'));
        $this->db->where('user_info.revision', 1);
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
            $data['title'] = "Tour All List for Reporting";
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
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_approved_tour', $this->config->item('system_status_approved'));
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('tour_setup.id', 'DESC');
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
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
            $this->db->select('user_info.name');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $data['item'] = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('List_reporting', $item_id, 'Id Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_tour'] != $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This Tour is not Approved yet.';
                $this->json_return($ajax);
            }

            $data['item']['name'] = $data['item']['name'] . ' (' . $data['item']['employee_id'] . ')';
            $data['title'] = "Tour Dates for Reporting :: " . $data['item']['title'];
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

        if ($user->user_group != 1)
        {
            $this->db->where('user_id', $user->user_id);
        }
        $this->db->where('id', $item_id);
        $item = $this->db->get($this->config->item('table_ems_tour_setup'))->row_array();

        if (!$item)
        {
            System_helper::invalid_try('List_reporting', $item_id, 'Id Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }

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
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $this->db->where('tour_setup.id', $item_id);
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $data['item'] = $result = $this->db->get()->row_array();
            //------------------Validation-----------------------------------
            if (!$data['item'])
            {
                System_helper::invalid_try('Reporting', $item_id, 'Id Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_approved_tour'] != $this->config->item('system_status_approved'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This Tour is not Approved yet.';
                $this->json_return($ajax);
            }
            if (!($reporting_date >= $data['item']['date_from'] && $reporting_date <= $data['item']['date_to']))
            {
                System_helper::invalid_try('Reporting', $item_id, 'Invalid Date');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are Trying with Invalid Date.';
                $this->json_return($ajax);
            }
            //------------------Validation(END)------------------------------

            $this->db->from($this->config->item('table_ems_tour_purpose'));
            $this->db->select('*');
            $this->db->where('tour_id', $item_id);
            $data['item']['purposes'] = $this->db->get()->result_array();


            $this->db->from($this->config->item('table_ems_tour_reporting') . ' tour_reporting');
            $this->db->select('tour_reporting.*, tour_reporting.id AS report_id');
            $this->db->join($this->config->item('table_ems_tour_purpose') . ' tour_purpose', 'tour_purpose.id = tour_reporting.purpose_id');
            $this->db->select('tour_purpose.purpose');
            $this->db->where('tour_reporting.tour_id', $item_id);
            $this->db->where('tour_reporting.date_reporting', $reporting_date);
            $this->db->where('tour_reporting.status !=', $this->config->item('system_status_delete'));
            $data['items'] = $this->db->get()->result_array();

            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            $data['title'] = 'Edit Tour Reporting :: ' . $result['title'];
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

        //--------Check Permission--------
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $time = time();
        $user = User_helper::get_user();
        $data = array();
        //----------------Check Validation---------------
        $this->db->from($this->config->item('table_ems_tour_setup'));
        $this->db->select('*');
        $this->db->where('id', $item_id);
        $this->db->where('status !=', $this->config->item('system_status_delete'));
        if ($user->user_group != 1)
        {
            $this->db->where('user_id', $user->user_id);
        }
        $data['item'] = $result = $this->db->get()->row_array();
        if (!$data['item'])
        {
            System_helper::invalid_try('Reporting', $item_id, 'Id Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($data['item']['status_approved_tour'] != $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'This Tour is not Approved yet.';
            $this->json_return($ajax);
        }
        if (!($reporting_date >= $data['item']['date_from'] && $reporting_date <= $data['item']['date_to']))
        {
            System_helper::invalid_try('Reporting', $item_id, 'Invalid Date');
            $ajax['status'] = false;
            $ajax['system_message'] = 'You are Trying with Invalid Date.';
            $this->json_return($ajax);
        }
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        //-----------------------------------------------

        $this->db->trans_start(); //DB Transaction Handle START

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
            }
        }

        // -------------------------------- INSERT NEW RECORD
        if ($items)
        {
            foreach ($items as $item)
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
                    if ($Query) //IF found in Reporting table, then UPDATE
                    {
                        continue;
                    }
                }
                elseif ((trim($item['purpose']) == "") && (trim($item['purpose_additional']) != "")) // IF New Purpose Entered
                {
                    $result = $this->db->query("SELECT *
                                  FROM " . $this->config->item('table_ems_tour_purpose') . "
                                  WHERE `tour_id` = '" . $item_id . "'
                                  AND `purpose` LIKE '" . trim($item['purpose_additional']) . "'")->row_array();
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
                        if ($Query) // IF New Purpose Already exist in Reporting Table, THEN Activate & UPDATE
                        {
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
                Query_helper::add($this->config->item('table_ems_tour_reporting'), $insert_new_items);
            }
        }

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
            $data['item'] = $item = $this->db->get()->row_array();

            if (!$data['item'])
            {
                System_helper::invalid_try('forward', $id, 'Forward Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_forwarded_reporting'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Already Forwarded.';
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

            $data['purposes'] = $this->db->get_where($this->config->item('table_ems_tour_purpose'), array('tour_id'=>$item_id))->result_array();

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

            $data['title'] = 'Tour Reporting Forward :: ' . $data['item']['title'];
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

        //--------Check Permission--------
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $time = time();
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
        $this->db->where('tour_setup.status_approved_tour !=', $this->config->item('system_status_pending'));
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $data['item'] = $this->db->get()->row_array();

        if (!$data['item'])
        {
            System_helper::invalid_try('forward', $item_id, 'Forward Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'IIInvalid Try.';
            $this->json_return($ajax);
        }
        if ($data['item']['status_forwarded_reporting'] == $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Already Forwarded.';
            $this->json_return($ajax);
        }
        if ($data['item']['status_approved_reporting'] == $this->config->item('system_status_approved'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Already Approved.';
            $this->json_return($ajax);
        }
        /*------------------Check Validation--------------------*/
        if (!($item_head['status_forwarded_tour']))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'Forward field is required.';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $update_item = array(
            'status_forwarded_reporting' => $this->config->item('system_status_forwarded'),
            'remarks_forwarded_reporting' => $item_head['remarks_forward_reporting'],
            'date_forwarded_reporting' => $time,
            'user_forwarded_reporting' => $user->user_id,
        );
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

        $data['title'] = "Tour Report Details";
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
