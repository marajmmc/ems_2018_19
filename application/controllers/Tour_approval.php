<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tour_approval extends Root_Controller
{
    private $message;
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
            $data['remarks'] = 1;
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
            $data['title'] = "Pending Tour List For Approval";
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
        /*-------------------USER PORTION--------------------*/
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
        $this->db->select('user_info.name');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        /*---------------------------------------------------*/
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1 && $user->user_group != 2)
        {
            $this->db->where('tour_setup.user_id!=', $user->user_id);
            $this->db->where('designation.parent', $user->designation);
        }
        $this->db->where('tour_setup.status!=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forward!=', 'Pending');
        $this->db->where('tour_setup.status_approve!=', 'Approved');
        $this->db->order_by('tour_setup.id DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
        }

        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['title'] = "All Tour List For Approval";
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
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        /*-------------------USER PORTION--------------------*/
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
        $this->db->select('user.employee_id');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = user.id AND user_info.revision=1', 'INNER');
        $this->db->select('user_info.name');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        /*---------------------------------------------------*/
        $this->db->where('user_area.revision', 1);
        if ($user->user_group != 1 && $user->user_group != 2)
        {
            $this->db->where('tour_setup.user_id!=', $user->user_id);
            $this->db->where('designation.parent', $user->designation);
        }
        $this->db->where('tour_setup.status!=', $this->config->item('system_status_delete'));
        $this->db->where('tour_setup.status_forward', 'Forwarded');
        $this->db->order_by('tour_setup.id DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => $item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
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

            $data['title'] = 'Tour Setup And Reporting Approval:: ' . $data['item']['title'];

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_approve", $data, true));
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
        $user = User_helper::get_user();
        $time = time();
        $item = $this->input->post('item');
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
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
        else
        {
            $this->db->trans_start(); //DB Transaction Handle START
            if ($item['status_approve'] == 'Back_to_setup')
            {
                $item['status_approve'] = 'Pending';
                $item['status_forward'] = 'Pending';
            }
            if ($item['status_approve'] == 'Deleted')
            {
                $item['status'] = 'Deleted';
            }
            $item['user_approved'] = $user->user_id;
            $item['date_approved'] = $time;
            $this->db->set('revision_count_approve', 'revision_count_approve+1', FALSE);
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

    private function check_validation_approve()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[superior_comment]', 'Supervisors Comment ', 'required');
        $this->form_validation->set_rules('item[status_approve]', 'Approve ', 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
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

            /* $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('user_area.revision', 1);
            $this->db->where('tour_setup.id', $item_id);
            $data['item'] = $this->db->get()->row_array();

            $db_login = $this->load->database('armalik_login', TRUE);
            $db_login->from($this->config->item('table_setup_user') . ' user');
            $db_login->select('user.id,user.employee_id,user.user_name,user.status');
            $db_login->select('user_info.name,user_info.ordering');
            $db_login->join($this->config->item('table_setup_user_info') . ' user_info', 'user.id = user_info.user_id', 'INNER');
            $db_login->join($this->config->item('table_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $db_login->select('designation.name designation');
            $db_login->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $db_login->select('department.name department_name');
            $db_login->where('user_info.revision', 1);
            $db_login->where('user.id', $data['item']['user_created']);
            $result = $db_login->get()->row_array();
            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            $data['item']['designation'] = $result['designation'];
            $data['item']['department_name'] = $result['department_name'];

            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others') . ' tour_setup_purpose_others', 'tour_setup_purpose_others.tour_setup_purpose_id = tour_setup_purpose.id', 'LEFT');
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion,');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $results_purpose_others = $this->db->get()->result_array();
            $other_info = array();
            foreach ($results_purpose_others as $results_purpose_other)
            {
                $other_info[$results_purpose_other['id']]['purpose'] = $results_purpose_other['purpose'];
                $other_info[$results_purpose_other['id']]['date_reporting'] = $results_purpose_other['date_reporting'];
                $other_info[$results_purpose_other['id']]['report_description'] = $results_purpose_other['report_description'];
                $other_info[$results_purpose_other['id']]['recommendation'] = $results_purpose_other['recommendation'];
                $other_info[$results_purpose_other['id']]['purpose_others_id'] = $results_purpose_other['purpose_others_id'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['name'] = $results_purpose_other['name'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['contact_no'] = $results_purpose_other['contact_no'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['profession'] = $results_purpose_other['profession'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['discussion'] = $results_purpose_other['discussion'];
            } */


            $user = User_helper::get_user();
            $data = array();

            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour_setup.user_id', 'INNER');
            $this->db->select('user.employee_id, user.user_name, user.status');
            $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
            $this->db->select('user_info.name, user_info.ordering');
            $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $this->db->select('designation.id AS designation_id, designation.parent AS parent_designation, designation.name AS designation');
            $this->db->join($this->config->item('table_login_setup_department') . ' department', 'designation.id = user_info.designation', 'LEFT');
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

            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others') . ' tour_setup_purpose_others', 'tour_setup_purpose_others.tour_setup_purpose_id = tour_setup_purpose.id', 'LEFT');
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion,');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $this->db->where('tour_setup_purpose.status', 'Active');
            $results_purpose_others = $this->db->get()->result_array();

            $other_info = array();
            foreach ($results_purpose_others as $results_purpose_other)
            {
                $other_info[$results_purpose_other['id']]['purpose'] = $results_purpose_other['purpose'];
                $other_info[$results_purpose_other['id']]['date_reporting'] = $results_purpose_other['date_reporting'];
                $other_info[$results_purpose_other['id']]['report_description'] = $results_purpose_other['report_description'];
                $other_info[$results_purpose_other['id']]['recommendation'] = $results_purpose_other['recommendation'];
                $other_info[$results_purpose_other['id']]['purpose_others_id'] = $results_purpose_other['purpose_others_id'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['name'] = $results_purpose_other['name'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['contact_no'] = $results_purpose_other['contact_no'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['profession'] = $results_purpose_other['profession'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['discussion'] = $results_purpose_other['discussion'];
            }

//            pr($user,0);
//            pr($data);

            if (!$data['item'])
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($user->user_group != 1 && $user->user_group != 2)
            {
                if ((!$this->check_my_editable($data['item'])) && ($user->designation != $data['item']['parent_designation']))
                {
                    System_helper::invalid_try('Details', $item_id, 'Trying to view details others tour setup');
                    $ajax['status'] = false;
                    $ajax['system_message'] = 'You are trying to view details others tour setup';
                    $this->json_return($ajax);
                }
            }

            $data['items_purpose_others'] = $other_info;
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
            $this->db->from($this->config->item('table_ems_tour_setup') . ' tour_setup');
            $this->db->select('tour_setup.*');
            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = tour_setup.user_id', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
            $this->db->where('user_area.revision', 1);
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
                System_helper::invalid_try('Details_print', $item_id, 'Trying to print others tour setup');
                $ajax['status'] = false;
                $ajax['system_message'] = 'You are trying to print others tour setup';
                $this->json_return($ajax);
            }
            $db_login = $this->load->database('armalik_login', TRUE);
            $db_login->from($this->config->item('table_setup_user') . ' user');
            $db_login->select('user.id,user.employee_id,user.user_name,user.status');
            $db_login->select('user_info.name,user_info.ordering');
            $db_login->join($this->config->item('table_setup_user_info') . ' user_info', 'user.id = user_info.user_id', 'INNER');
            $db_login->join($this->config->item('table_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
            $db_login->select('designation.name designation');
            $db_login->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
            $db_login->select('department.name department_name');
            $db_login->where('user_info.revision', 1);
            $db_login->where('user.id', $data['item']['user_created']);
            $result = $db_login->get()->row_array();
            $data['item']['name'] = $result['name'] . ' (' . $result['employee_id'] . ')';
            $data['item']['designation'] = $result['designation'];
            $data['item']['department_name'] = $result['department_name'];
            //data from tour setup others table
            $this->db->from($this->config->item('table_ems_tour_setup_purpose') . ' tour_setup_purpose');
            $this->db->select('tour_setup_purpose.*');
            $this->db->join($this->config->item('table_ems_tour_setup_purpose_others') . ' tour_setup_purpose_others', 'tour_setup_purpose_others.tour_setup_purpose_id = tour_setup_purpose.id', 'LEFT');
            $this->db->select('tour_setup_purpose_others.id purpose_others_id, tour_setup_purpose_others.name, tour_setup_purpose_others.contact_no, tour_setup_purpose_others.profession, tour_setup_purpose_others.discussion,');
            $this->db->where('tour_setup_purpose.tour_setup_id', $item_id);
            $results_purpose_others = $this->db->get()->result_array();
            $other_info = array();
            foreach ($results_purpose_others as $results_purpose_other)
            {
                $other_info[$results_purpose_other['id']]['purpose'] = $results_purpose_other['purpose'];
                $other_info[$results_purpose_other['id']]['date_reporting'] = $results_purpose_other['date_reporting'];
                $other_info[$results_purpose_other['id']]['report_description'] = $results_purpose_other['report_description'];
                $other_info[$results_purpose_other['id']]['recommendation'] = $results_purpose_other['recommendation'];
                $other_info[$results_purpose_other['id']]['purpose_others_id'] = $results_purpose_other['purpose_others_id'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['name'] = $results_purpose_other['name'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['contact_no'] = $results_purpose_other['contact_no'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['profession'] = $results_purpose_other['profession'];
                $other_info[$results_purpose_other['id']]['others'][$results_purpose_other['purpose_others_id']]['discussion'] = $results_purpose_other['discussion'];
            }
            $data['items_purpose_others'] = $other_info;
            $data['title'] = 'Tour Setup And Reporting Print View:: ' . $data['item']['title'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/details_print", $data, true));
            if ($this->message)
            {
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
            $data['preference_method_name'] = 'list';
            $ajax['status'] = true;
            $data['system_preference_items'] = $this->get_preference('list_all');
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
