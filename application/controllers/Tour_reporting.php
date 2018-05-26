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
        elseif ($action == "list_reporting")
        {
            $this->system_list_reporting($id);
        }
        elseif ($action == "get_reporting_items")
        {
            $this->system_get_reporting_items();
        }
        elseif ($action == "reporting")
        {
            $this->system_reporting($id);
        }
        elseif ($action == "save_reporting")
        {
            $this->system_save_reporting();
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
        elseif ($action == "details_print")
        {
            $this->system_details_print($id);
        }
        elseif ($action == "requisition_print")
        {
            $this->system_requisition_print($id);
        }
        else
        {
            $this->system_list($id);
        }

    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
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
            $data['status_approve'] = 1;
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
            $data['title'] = "Approved Tour List for Reporting";
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
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
        $this->db->select('user_info.name');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status!=', $this->config->item('system_status_delete'));
        //$this->db->where('tour_setup.status_forward!=', 'Pending');
        $this->db->where('tour_setup.status_approve', 'Approved');
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


    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "All Tour List";
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
        $this->db->where('user.status', $this->config->item('system_status_active'));
        $this->db->where('user_info.revision', 1);
        if ($user->user_group != 1)
        {
            $this->db->where('tour_setup.user_id', $user->user_id);
        }
        $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
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
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
            $this->db->select('user_info.name');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            $this->db->where('user_area.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            if ($user->user_group != 1)
            {
                $this->db->where('tour_setup.user_id', $user->user_id);
            }
            $this->db->where('tour_setup.status !=', $this->config->item('system_status_delete'));
            $result = $this->db->get()->row_array();

            $data = array();
            if ($result)
            {
                $data['item'] = $result;
                $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            }
            if (!$data['item'])
            {
                System_helper::invalid_try('List_reporting', $item_id, 'Id Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title'] = "Tour Purpose And Reporting (" . $data['item']['title'] . ')';
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

    private function system_get_reporting_items()
    {
        $item_id = $this->input->post('id');
        $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
        $this->db->select('tour_setup_purpose.*');
        $this->db->join($this->config->item('table_ems_tour_setup') . ' tour_setup', 'tour_setup.id = tour_setup_purpose.tour_setup_id', 'LEFT');
        $this->db->select('tour_setup.title,tour_setup.date_from,tour_setup.date_to, tour_setup.user_id');
        $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
        $this->db->where('tour_setup_purpose.status !=', 'Delete');
        $this->db->order_by('tour_setup_purpose.id', 'ASC');
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['sl_no'] = $key + 1;
            if (trim($item['date_reporting']) != "")
            {
                $items[$key]['date_reporting'] = System_helper::display_date($item['date_from']);
            }
            else
            {
                $items[$key]['date_reporting'] = '-';
            }
        }

        $this->json_return($items);
    }


    private function system_reporting($id)
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

            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup') . ' tour_setup', 'tour_setup.id = tour_setup_purpose.tour_setup_id', 'INNER');
            $this->db->select('tour_setup.title, tour_setup.date_from, tour_setup.date_to, tour_setup.user_id');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id AND user_area.revision=1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            //------------USER PORTION---------------
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id', 'INNER');
            $this->db->select('user_info.name');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $this->db->select('department.name AS department_name');
            //---------------------------------------
            $this->db->where('tour_setup_purpose.id', $item_id);
            $data['item'] = $result = $this->db->get()->row_array();

            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';

            $this->db->from($this->config->item('table_ems_tour_setup_purpose_others'));
            $this->db->select('*');
            $this->db->where('tour_setup_purpose_id', $item_id);
            $this->db->where('status', 'Active');
            $data['items'] = $this->db->get()->result_array();

            if (!($data['item']['date_reporting']))
            {
                $data['item']['date_reporting'] = time();
            }

            $data['title'] = 'Edit Reporting For:: ' . $data['item']['purpose'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_reporting", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/reporting/' . $item_id);
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
        $id = $this->input->post("id");
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        $old_items = $this->input->post('old_items');

        $time = time();
        $user = User_helper::get_user();

        //--------Check Permission--------
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        //--------Check Validation--------
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $existing_items = Query_helper::get_info($this->config->item('table_ems_tour_setup_purpose_others'), '*', array('tour_setup_purpose_id =' . $id));

        $this->db->trans_start(); //DB Transaction Handle START
        //------Updating New Items------
        if ($existing_items)
        {
            Query_helper::update($this->config->item('table_ems_tour_setup_purpose_others'), array('status' => 'Delete', 'date_updated' => $time, 'user_updated' => $user->user_id), array("tour_setup_purpose_id =" . $id, "status='Active'"));
            if ($old_items)
            {
                foreach ($existing_items as $existing_item)
                {
                    if (isset($old_items[$existing_item['id']]))
                    {
                        $update_old_items = array(
                            "name" => $old_items[$existing_item['id']]['name'],
                            "contact_no" => $old_items[$existing_item['id']]['contact_no'],
                            "profession" => $old_items[$existing_item['id']]['profession'],
                            "discussion" => $old_items[$existing_item['id']]['discussion'],
                            "status" => 'Active',
                            "date_updated" => $time,
                            "user_updated" => $user->user_id
                        );
                        Query_helper::update($this->config->item('table_ems_tour_setup_purpose_others'), $update_old_items, array("id = " . $existing_item['id']));
                    }
                }
            }
        }
        //------Inserting New Items------
        if ($items)
        {
            foreach ($items as $item)
            {
                $insert_new_items = array(
                    "tour_setup_purpose_id" => $id,
                    "name" => $item['name'],
                    "contact_no" => $item['contact_no'],
                    "profession" => $item['profession'],
                    "discussion" => $item['discussion'],
                    "status" => 'Active',
                    "date_created" => $time,
                    "user_created" => $user->user_id
                );
                Query_helper::add($this->config->item('table_ems_tour_setup_purpose_others'), $insert_new_items);
            }
        }

        //------Updating Item Heads------
        $update_item_head = array(
            "date_reporting" => System_helper::get_time($item_head['date_reporting']),
            "report_description" => $item_head['report_description'],
            "recommendation" => $item_head['recommendation'],
        );
        $this->db->set('revision_count_reporting', 'revision_count_reporting+1', FALSE);
        Query_helper::update($this->config->item('table_ems_tour_setup_purpose'), $update_item_head, array("id = " . $id));

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $tour_purpose_data = Query_helper::get_info($this->config->item('table_ems_tour_setup_purpose'), 'tour_setup_id', array('id =' . $id), 1);
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_reporting($tour_purpose_data['tour_setup_id']);
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
            //----------------Action User's Info---------------------------------------------------
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_created', 'user_info_created.user_id=tour_setup.user_created', 'LEFT');
            $this->db->select('user_info_created.name AS create_user'); // Entry User
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_updated', 'user_info_updated.user_id=tour_setup.user_updated', 'LEFT');
            $this->db->select('user_info_updated.name AS update_user'); // Update user
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_forwarded', 'user_info_forwarded.user_id=tour_setup.user_forwarded', 'LEFT');
            $this->db->select('user_info_forwarded.name AS forward_user'); // Forward User
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info_approved', 'user_info_approved.user_id=tour_setup.user_approved', 'LEFT');
            $this->db->select('user_info_approved.name AS approve_user'); // Approve User
            //--------------------------------------------------------------------------------------
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $item = $this->db->get()->row_array();

            // Validation START
            if (!$item)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            // Validation END

            //data from tour setup purpose
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
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
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.tour_setup_purpose_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion');
            $this->db->where_in('tour_setup_purpose_others.tour_setup_purpose_id', $purpose_ids);
            $this->db->where('tour_setup_purpose_others.status', 'Active');
            $results_purpose_others = $this->db->get()->result_array();

            foreach ($results_purpose_others as $row)
            {
                $items[$row['tour_setup_purpose_id']]['others'][$row['purpose_others_id']] = array(
                    'name' => $row['name'],
                    'contact_no' => $row['contact_no'],
                    'profession' => $row['profession'],
                    'discussion' => $row['discussion']
                );
            }

            $data['item'] = $item;
            $data['items_purpose_others'] = $items;

            $data['title'] = 'Tour Setup And Reporting Details:: ' . $item['title'];

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

    private function system_details_print($id)
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
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $item = $this->db->get()->row_array();

            // Validation START
            if (!$item)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            // Validation END

            //data from tour setup purpose
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
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
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.tour_setup_purpose_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion');
            $this->db->where_in('tour_setup_purpose_others.tour_setup_purpose_id', $purpose_ids);
            $this->db->where('tour_setup_purpose_others.status', 'Active');
            $results_purpose_others = $this->db->get()->result_array();

            foreach ($results_purpose_others as $row)
            {
                $items[$row['tour_setup_purpose_id']]['others'][$row['purpose_others_id']] = array(
                    'name' => $row['name'],
                    'contact_no' => $row['contact_no'],
                    'profession' => $row['profession'],
                    'discussion' => $row['discussion']
                );
            }

            $data['item'] = $item;
            $data['items_purpose_others'] = $items;

            $data['title'] = 'Tour Setup And Reporting Print View:: ' . $item['title'];
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details_print", $data, true));
            if ($this->message)
            {
                $ajax['status'] = true;
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details_print/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_requisition_print($id)
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
            //$this->db->where('user.status', $this->config->item('system_status_active'));
            $this->db->where('user_info.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();


            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
            $this->db->order_by('tour_setup_purpose.id', 'ASC');
            $data['items'] = $this->db->get()->result_array();

            $data['title'] = 'Tour Setup And Reporting Print View:: ' . $data['item']['title'];

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/requisition_print", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/requisition_print/' . $item_id);
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

        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date_reporting]', 'Reporting Date', 'required');
        $this->form_validation->set_rules('item[report_description]', 'Report (Description)', 'required');
        $this->form_validation->set_rules('item[recommendation]', 'Recommendation', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        /*
        --- Manual Validation for BLANK or EMPTY items checking ---
        */
        if ($items)
        {
            foreach ($items as $item)
            {
                $name = trim($item['name']);
                if (empty($name))
                {
                    $this->message = 'Unfinished others information in entry. Atleast enter name.';
                    return false;
                }
            }
        }
        elseif ($old_items)
        {
            foreach ($old_items as $old_item)
            {
                $old_item = trim($old_item['name']);
                if (empty($old_item))
                {
                    $this->message = 'Unfinished others information in entry. Atleast enter name.';
                    return false;
                }
            }
        }

        return true;
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

}
