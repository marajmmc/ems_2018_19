<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fd_budget extends Root_Controller
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
        $this->load->helper('fd_budget');
        $this->lang->load('field_day');
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
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
        elseif ($action == "edit_image")
        {
            $this->system_edit_image($id);
        }
        elseif ($action == "save_image")
        {
            $this->system_save_image();
        }
        elseif ($action == "forward")
        {
            $this->system_forward($id);
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "get_dealers")
        {
            $this->system_get_dealers($id);
        }
        elseif ($action == "get_lead_farmers")
        {
            $this->system_get_lead_farmers($id);
        }
        elseif ($action == "get_fd_budget_varieties")
        {
            $this->system_get_fd_budget_varieties();
        }
        elseif ($action == "set_preference_list")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_list_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif ($action == "set_preference_list_waiting")
        {
            $this->system_set_preference('list_waiting');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method)
    {
        $data = array();
        $data['id'] = 1;
        $data['fdb_proposal_date'] = 1;
        $data['expected_date'] = 1;
        $data['total_budget'] = 1;
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['variety1_name'] = 1;
        $data['variety2_name'] = 1;
        $data['division_name'] = 1;
        $data['zone_name'] = 1;
        $data['territory_name'] = 1;
        $data['district_name'] = 1;
        $data['outlet_name'] = 1;
        if ($method == 'list_all')
        {
            $data['status_budget'] = 1;
            $data['status_requested'] = 1;
            $data['status_approved'] = 1;
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
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
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
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Field Day Budget Pending List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
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
        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id, fd_budget.date AS fdb_proposal_date, fd_budget.total_budget');

        $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $this->db->select('fd_budget_details.expected_date');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('fd_budget_details.revision', 1);
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['fdb_proposal_date'] = System_helper::display_date($item['fdb_proposal_date']);
            $item['expected_date'] = System_helper::display_date($item['expected_date']);
        }
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Field Day Budget All List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
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
        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id, fd_budget.date AS fdb_proposal_date, fd_budget.total_budget');

        $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $this->db->select('fd_budget_details.expected_date');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('fd_budget_details.revision', 1);
        $this->db->order_by('fd_budget.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['fdb_proposal_date'] = System_helper::display_date($item['fdb_proposal_date']);
            $item['expected_date'] = System_helper::display_date($item['expected_date']);
        }
        $this->json_return($items);
    }

    private function system_list_waiting()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_waiting';
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Field Day Budget Waiting List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_waiting", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
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
        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id, fd_budget.date AS fdb_proposal_date, fd_budget.total_budget');

        $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
        $this->db->select('fd_budget_details.expected_date');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');
        if ($this->locations['division_id'] > 0)
        {
            $this->db->where('division.id', $this->locations['division_id']);
            if ($this->locations['zone_id'] > 0)
            {
                $this->db->where('zone.id', $this->locations['zone_id']);
                if ($this->locations['territory_id'] > 0)
                {
                    $this->db->where('territory.id', $this->locations['territory_id']);
                    if ($this->locations['district_id'] > 0)
                    {
                        $this->db->where('district.id', $this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('fd_budget_details.revision', 1);
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['fdb_proposal_date'] = System_helper::display_date($item['fdb_proposal_date']);
            $item['expected_date'] = System_helper::display_date($item['expected_date']);
        }
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $time = time();
            $data['item'] = Array(
                'id' => 0,
                'date' => $time,
                'remarks' => ''
            );
            $data['item_info'] = Array(
                'crop_id' => '',
                'crop_type_id' => '',
                'variety1_id' => '',
                'variety2_id' => '',
                'division_id' => $this->locations['division_id'],
                'zone_id' => $this->locations['zone_id'],
                'territory_id' => $this->locations['territory_id'],
                'district_id' => $this->locations['district_id'],
                'outlet_id' => '',
                'address' => '',
                'present_condition' => '',
                'farmers_evaluation' => '',
                'diff_between_varieties' => '',
                'no_of_participant' => '',
                'expected_date' => '',
                'arm_market_size' => '',
                'total_market_size' => '',
                'sales_target' => ''
            );

            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'));
            $data['zones'] = array();
            $data['territories'] = array();
            $data['districts'] = array();
            $data['outlets'] = array();
            if ($this->locations['division_id'] > 0)
            {
                $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id =' . $this->locations['division_id']));
                if ($this->locations['zone_id'] > 0)
                {
                    $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id =' . $this->locations['zone_id']));
                    if ($this->locations['territory_id'] > 0)
                    {
                        $data['districts'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('territory_id =' . $this->locations['territory_id']));
                    }
                }
            }

            $data['crops'] = array();
            $data['crop_types'] = array();
            $data['crop_varieties1'] = array();
            $data['crop_varieties2'] = array();

            $data['participants'] = array();
            $data['dealers'] = array();
            $data['leading_farmers'] = array();
            $data['total'] = '';

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array('status !="' . $this->config->item('system_status_delete') . '"'), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = array();
            $data['system_all_varieties'] = Fd_budget_helper::get_dropdown_all_crop_variety();

            $data['title'] = "Create new Field Day Budget";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add');
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
            $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
            $this->db->select('fd_budget.id AS budget_id, fd_budget.date AS fdb_proposal_date, fd_budget.remarks');

            $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
            $this->db->select('fd_budget_details.*, fd_budget_details.expected_date');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
            $this->db->select('variety1.name AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
            $this->db->select('variety2.name AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.id AS crop_type_id, crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.id AS crop_id, crop.name AS crop_name');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
            $this->db->select('cus_info.name AS outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
            $this->db->select('district.id AS district_id, district.name AS district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id AS territory_id, territory.name AS territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id AS zone_id, zone.name AS zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id AS division_id, division.name AS division_name');

            $this->db->where('fd_budget.status_budget !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $this->db->where('fd_budget_details.revision', 1);
            $this->db->order_by('fd_budget.id', 'DESC');
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['item'] = Array(
                'id' => $result['budget_id'],
                'date' => $result['fdb_proposal_date'],
                'remarks' => $result['remarks']
            );

            $data['item_info'] = Array(
                'crop_id' => $result['crop_id'],
                'crop_type_id' => $result['crop_type_id'],
                'variety1_id' => $result['variety1_id'],
                'variety2_id' => $result['variety2_id'],
                'division_id' => $result['division_id'],
                'zone_id' => $result['zone_id'],
                'territory_id' => $result['territory_id'],
                'district_id' => $result['district_id'],
                'outlet_id' => $result['outlet_id'],
                'address' => $result['address'],
                'present_condition' => $result['present_condition'],
                'farmers_evaluation' => $result['farmers_evaluation'],
                'diff_between_varieties' => $result['diff_between_varieties'],
                'expected_date' => $result['expected_date'],
                'participant_through_customer' => $result['participant_through_customer'],
                'participant_through_others' => $result['participant_through_others'],
                'arm_market_size' => $result['total_market_size'],
                'total_market_size' => $result['arm_market_size'],
                'sales_target' => $result['sales_target']
            );

            $query_division_id = ($this->locations['division_id'] > 0) ? $this->locations['division_id'] : $result['division_id'];
            $query_zone_id = ($this->locations['zone_id'] > 0) ? $this->locations['zone_id'] : $result['zone_id'];
            $query_territory_id = ($this->locations['territory_id'] > 0) ? $this->locations['territory_id'] : $result['territory_id'];
            $query_district_id = ($this->locations['district_id'] > 0) ? $this->locations['division_id'] : $result['district_id'];
            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'));
            $data['zones'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id value', 'name text'), array('division_id =' . $query_division_id));
            $data['territories'] = Query_helper::get_info($this->config->item('table_login_setup_location_territories'), array('id value', 'name text'), array('zone_id =' . $query_zone_id));
            $data['districts'] = Query_helper::get_info($this->config->item('table_login_setup_location_districts'), array('id value', 'name text'), array('territory_id =' . $query_territory_id));

            $this->db->from($this->config->item('table_login_csetup_cus_info') . ' cus_info');
            $this->db->select('cus_info.customer_id AS value, cus_info.name AS text');
            $this->db->where('cus_info.district_id', $query_district_id);
            $this->db->where('cus_info.type', $this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision', 1);
            $data['outlets'] = $this->db->get()->result_array();

            $data['crop_types'] = Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'), array('id value', 'name text'), array('crop_id =' . $data['item_info']['crop_id']));
            /*
            $data['crop_varieties1'] = Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'), array('id value', 'name text'), array('crop_type_id =' . $data['item_info']['crop_type_id']));
            $data['crop_varieties2'] = Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'), array('id value', 'name text'), array('crop_type_id =' . $data['item_info']['crop_type_id'], 'whose ="Competitor"'));
            */
            $data['crop_varieties1'] = Fd_budget_helper::get_dropdown_arm_and_upcoming_crop_variety($data['item_info']['crop_type_id']);
            $data['crop_varieties1'] = $data['crop_varieties1'][$data['item_info']['crop_type_id']];
            $data['crop_varieties2'] = Fd_budget_helper::get_dropdown_all_crop_variety($data['item_info']['crop_type_id']);
            $data['crop_varieties2'] = $data['crop_varieties2'][$data['item_info']['crop_type_id']];

            //pr($data['crop_varieties1'],0);
            //pr($data['crop_varieties2']);

            $dealers_by_outlet = Fd_budget_helper::get_all_area_dealers_by_outlet($result['outlet_id']);
            $data['dealers'] = array();
            foreach ($dealers_by_outlet as $item)
            {
                $data['dealers'][] = array(
                    'value' => $item['dealer_id'],
                    'text' => $item['dealer_name'],
                    'phone_no' => $item['mobile_no']
                );
            }

            $lead_farmers_by_outlet = Fd_budget_helper::get_all_area_lead_farmers_by_outlet($result['outlet_id']);
            $data['leading_farmers'] = array();
            foreach ($lead_farmers_by_outlet as $item)
            {
                $data['leading_farmers'][] = array(
                    'value' => $item['lead_farmers_id'],
                    'text' => $item['name'],
                    'phone_no' => $item['mobile_no']
                );
            }


            $data['participants'] = array();
            $participants_data = json_decode($result['dealer_farmer_participants'], true);

            $dealer_participant = $participants_data['dealer_participant'];
            if(sizeof($dealer_participant) > 0){
                foreach($dealer_participant as $id => $val){
                    $data['participants'][$id] = $val;
                }
            }

            $farmer_participant = $participants_data['farmer_participant'];
            if(sizeof($farmer_participant) > 0){
                foreach($farmer_participant as $id => $val){
                    $data['participants'][$id] = $val;
                }
            }

            //pr($dealer_farmer_participants,0);
            //pr($dealer_farmer_participants,0);
            //pr($data['participants'], 0);
//            $participant_result = Query_helper::get_info($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'), '*', array('budget_id =' . $item_id, 'revision=1'));
//
//            foreach ($participant_result as $result)
//            {
//                $data['participants'][$result['dealer_leadfarmer_id']] = $result;
//            }
            //pr($data['participants']);

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = array();

//            $budget_result = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_expense'), '*', array('budget_id =' . $item_id, 'revision=1'));
//            foreach ($budget_result as $result)
//            {
//                $data['expense_budget'][$result['item_id']] = $result;
//            }
//            pr($budget_result);

            $budget_result = json_decode($result['expense_budget_items']);
            foreach ($budget_result as $id => $val)
            {
                $data['expense_budget'][$id] = $val;
            }

            $data['title'] = "Edit Field Day Budget ( ID:" . $result['budget_id'] . " )";
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
        $id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $item_info = $this->input->post('item_info');
        $dealer_participant = $this->input->post('dealer_participant');
        $farmer_participant = $this->input->post('farmer_participant');
        $expense_budget = $this->input->post('expense_budget');

        $user = User_helper::get_user();
        $time = time();
        $total_budget = $no_of_participant = 0;

        // Permission Checking
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        if ($dealer_participant && !empty($dealer_participant))
        {
            foreach ($dealer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $no_of_participant += $value;
                }
            }
        }
        if ($farmer_participant && !empty($farmer_participant))
        {
            foreach ($farmer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $no_of_participant += $value;
                }
            }
        }
        if (is_numeric($item_info['participant_through_customer']) && ($item_info['participant_through_customer'] > 0))
        {
            $no_of_participant += $item_info['participant_through_customer'];
        }
        if (is_numeric($item_info['participant_through_others']) && ($item_info['participant_through_others'] > 0))
        {
            $no_of_participant += $item_info['participant_through_others'];
        }
        if ($expense_budget && !empty($expense_budget))
        {
            foreach ($expense_budget as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $total_budget += $value;
                }
            }
        }

        // Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $item_head['date'] = System_helper::get_time($item_head['date']);
        $item_head['no_of_participant'] = $no_of_participant;
        $item_head['total_budget'] = $total_budget;


        $participants = array(
            'dealer_participant' => $dealer_participant,
            'farmer_participant' => $farmer_participant
        );
        $item_info['dealer_farmer_participants'] = json_encode($participants);
        $item_info['expected_date'] = System_helper::get_time($item_info['expected_date']);
        $item_info['expense_budget_items'] = json_encode($expense_budget);


//        pr($item_head,0);
//        pr($item_info);


        $this->db->trans_start(); //DB Transaction Handle START

        if ($id > 0) // EDIT
        {
            $budget_id = $id;
            /* Master Table Update */
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            Query_helper::update($this->config->item('table_ems_fd_budget'), $item_head, array("id =" . $budget_id));

            /* Revision Update for (1)Details Table (2)Expense Table (3)Dealer, Lead Farmer Participant Table */
            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details'));

/*            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details_expense'));

            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'));*/
        }
        else // ADD
        { /* Master Table Insert */
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            $budget_id = Query_helper::add($this->config->item('table_ems_fd_budget'), $item_head);
        }

        /* Details Table Insert ( EDIT & ADD ) */
        $item_info['budget_id'] = $budget_id;
        $item_info['date_created'] = $time;
        $item_info['user_created'] = $user->user_id;
        $item_info['revision'] = 1;
        Query_helper::add($this->config->item('table_ems_fd_budget_details'), $item_info);

//        /* Expense Table Insert ( EDIT & ADD ) */
//        foreach ($expense_budget as $key => $value)
//        {
//            $data = array(
//                'budget_id' => $budget_id,
//                'item_id' => $key,
//                'amount' => $value,
//                'user_created' => $user->user_id,
//                'date_created' => $time,
//                'revision' => 1
//            );
//            Query_helper::add($this->config->item('table_ems_fd_budget_details_expense'), $data);
//        }
//
//        /* Dealer Participant & Farmer Participant Table Insert ( EDIT & ADD ) */
//        if ($dealer_participant && !empty($dealer_participant))
//        {
//            foreach ($dealer_participant as $key => $value)
//            {
//                $data = array(
//                    'budget_id' => $budget_id,
//                    'participant_type' => 1, // 1 for Dealer Participant
//                    'dealer_leadfarmer_id' => $key,
//                    'participant_number' => $value,
//                    'user_created' => $user->user_id,
//                    'date_created' => $time,
//                    'revision' => 1
//                );
//                Query_helper::add($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'), $data);
//            }
//        }
//        if ($farmer_participant && !empty($farmer_participant))
//        {
//            foreach ($farmer_participant as $key => $value)
//            {
//                $data = array(
//                    'budget_id' => $budget_id,
//                    'participant_type' => 2, // 2 for Lead farmer Participant
//                    'dealer_leadfarmer_id' => $key,
//                    'participant_number' => $value,
//                    'user_created' => $user->user_id,
//                    'date_created' => $time,
//                    'revision' => 1
//                );
//                Query_helper::add($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'), $data);
//            }
//        }

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $ajax['status'] = true;
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

    private function system_edit_image($id)
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
            $this->db->from($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details');

            $this->db->join($this->config->item('table_ems_fd_budget') . ' fd_budget', 'fd_budget.id = fd_budget_details.budget_id', 'INNER');
            $this->db->select('fd_budget.id, fd_budget.date AS fdb_proposal_date');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
            $this->db->select('variety1.name AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'LEFT');
            $this->db->select('variety2.name AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.name AS crop_name');

            $this->db->where('fd_budget.status_budget !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $this->db->where('fd_budget_details.revision', 1);
            $this->db->order_by('fd_budget.id', 'DESC');
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'), 0, 0, array('ordering ASC'));

            $data['file_details'] = array();
            $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status ="' . $this->config->item('system_status_active') . '"'));
            foreach ($results as $result)
            {
                $data['file_details'][$result['category_id']] = array(
                    'variety1_file_location' => $result['variety1_file_location'],
                    'variety1_file_remarks' => $result['variety1_file_remarks'],
                    'variety2_file_location' => $result['variety2_file_location'],
                    'variety2_file_remarks' => $result['variety2_file_remarks'],
                );
            }

            $data['title'] = "Edit Field Day Budget Picture ( ID:" . $data['item']['id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_image", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit_image/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_image()
    {
        $item_id = $this->input->post('id');
        $variety1_file_remarks = $this->input->post('variety1_file_remarks');
        $variety2_file_remarks = $this->input->post('variety2_file_remarks');

        $user = User_helper::get_user();
        $time = time();

        // Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $path = 'images/fd_budget_variety/' . $item_id;
        $uploaded_files = System_helper::upload_file($path);
        if ($uploaded_files && (sizeof($uploaded_files) > 0))
        {
            $this->db->trans_start(); //DB Transaction Handle START

            $items_by_category = array();
            $category_ids = array();
            foreach ($uploaded_files as $key => $uploaded_file)
            {
                list($index1, $variety_id, $index2, $category_id) = explode("_", $key);
                if ($uploaded_file['status'])
                {
                    $remarks = '';
                    if ($variety_id == 1)
                    {
                        $remarks = $variety1_file_remarks[$category_id];
                    }
                    elseif ($variety_id == 2)
                    {
                        $remarks = $variety2_file_remarks[$category_id];
                    }
                    $category_ids[] = $category_id;
                    $items_by_category[$category_id]['budget_id'] = $item_id;
                    $items_by_category[$category_id]['variety' . $variety_id . '_file_name'] = $uploaded_file['info']['file_name'];
                    $items_by_category[$category_id]['variety' . $variety_id . '_file_location'] = $path . '/' . $uploaded_file['info']['file_name'];
                    $items_by_category[$category_id]['variety' . $variety_id . '_file_remarks'] = $remarks;
                }
            }
            //Update Revision
            $this->db->where('budget_id', $item_id);
            $this->db->where_in('category_id', $category_ids);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details_picture'));

            foreach ($items_by_category as $category_id => $item_by_category)
            { //Insert New Image
                $items = $item_by_category;
                $items['budget_id'] = $item_id;
                $items['category_id'] = $category_id;
                $items['date_created'] = $time;
                $items['user_created'] = $user->user_id;
                $items['revision'] = 1;
                Query_helper::add($this->config->item('table_ems_fd_budget_details_picture'), $items);
            }

            $this->db->trans_complete(); //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $ajax['status'] = true;
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
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "File file was Selected/ Uploaded";
            $this->json_return($ajax);
        }
    }

    private function system_get_dealers($id = 0)
    {
        if ($id > 0)
        {
            $item_id = $id;
        }
        else
        {
            $item_id = $this->input->post('id');
        }
        $html_container_id = $this->input->post('html_container_id');
        $data = array();
        $data['label'] = $this->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER');
        $data['name_index'] = 'dealer_participant';
        $data['items'] = Fd_budget_helper::get_all_area_dealers_by_outlet($item_id);
        foreach ($data['items'] as &$item)
        {
            $item['value'] = $item['dealer_id'];
            $item['text'] = $item['dealer_name'];
            $item['phone_no'] = $item['mobile_no'];
        }

        if ($data['items'])
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view($this->controller_url . "/input_fields_with_item", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }

    private function system_get_lead_farmers($id = 0)
    {
        if ($id > 0)
        {
            $item_id = $id;
        }
        else
        {
            $item_id = $this->input->post('id');
        }
        $html_container_id = $this->input->post('html_container_id');
        $data = array();
        $data['label'] = $this->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_DEALER');
        $data['name_index'] = 'farmer_participant';
        $data['items'] = Fd_budget_helper::get_all_area_lead_farmers_by_outlet($item_id);
        foreach ($data['items'] as &$item)
        {
            $item['value'] = $item['lead_farmers_id'];
            $item['text'] = $item['name'];
            $item['phone_no'] = $item['mobile_no'];
        }

        if ($data['items'])
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view($this->controller_url . "/input_fields_with_item", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }

    public function system_get_fd_budget_varieties()
    {
        $ajax = array();
        $ajax['arm_and_upcoming_crop_variety'] = Fd_budget_helper::get_dropdown_arm_and_upcoming_crop_variety();
        $ajax['all_crop_variety'] = Fd_budget_helper::get_dropdown_all_crop_variety();

        if ($ajax)
        {
            $ajax['status'] = true;
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date]', $this->lang->line('LABEL_DATE'), 'required');
        $this->form_validation->set_rules('item_info[variety1_id]', $this->lang->line('LABEL_VARIETY_NAME'), 'required');
        $this->form_validation->set_rules('item_info[outlet_id]', $this->lang->line('LABEL_OUTLET_NAME'), 'required');

        $this->form_validation->set_rules('item_info[address]', $this->lang->line('LABEL_ADDRESS'), 'required');
        $this->form_validation->set_rules('item_info[present_condition]', $this->lang->line('LABEL_PRESENT_CONDITION'), 'required');
        $this->form_validation->set_rules('item_info[farmers_evaluation]', $this->lang->line('LABEL_DEALERS_EVALUATION'), 'required');
        $this->form_validation->set_rules('item_info[diff_between_varieties]', $this->lang->line('LABEL_SPECIFIC_DIFFERENCE'), 'required');

        $this->form_validation->set_rules('item_info[expected_date]', $this->lang->line('LABEL_EXPECTED_DATE'), 'required');
        $this->form_validation->set_rules('item_info[participant_through_customer]', $this->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER'), 'required|numeric');
        $this->form_validation->set_rules('item_info[participant_through_others]', $this->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'), 'required|numeric');

        $this->form_validation->set_rules('item_info[sales_target]', $this->lang->line('LABEL_NEXT_SALES_TARGET'), 'required|numeric');
        $this->form_validation->set_rules('item_info[arm_market_size]', $this->lang->line('LABEL_ARM_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[total_market_size]', $this->lang->line('LABEL_TOTAL_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item[remarks]', 'TI ' . $this->lang->line('LABEL_RECOMMENDATION'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }

        $expense_budget = $this->input->post('expense_budget');
        $total_budget = 0;
        if ($expense_budget && !empty($expense_budget))
        {
            foreach ($expense_budget as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $total_budget += $value;
                }
            }
        }
        if (!($total_budget > 0))
        {
            $this->message = $this->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET') . ' cannot be zero or, negative';
            return false;
        }

        /*if ($upazilla_id)
        {
            $farmers = Query_helper::get_info($this->config->item('table_setup_fsetup_leading_farmer'), array('id value', 'CONCAT(name," (",phone_no,")") text', 'status'), array('upazilla_id=' . $upazilla_id), 0, 0, array('ordering ASC'));
            $check = array();
            $fmr = array();
            foreach ($farmers as $farmer)
            {
                $check[$farmer['value']] = $farmer['value'];
                $fmr[$farmer['value']] = $farmer['text'];
            }
            if ($ids)
            {
                foreach ($ids as $key => $id)
                {
                    if (!in_array($key, $check))
                    {
                        $this->message = 'Invalid Try';
                        return false;
                    }
                }
                foreach ($ids as $index => $id)
                {
                    if (!$id)
                    {
                        $this->form_validation->set_rules('farmer_participant[' . $index . ']', $fmr[$index], 'required');
                    }
                }
            }
        }
        if ($expense_budget)
        {
            $expenses = Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
            $expense = array();
            foreach ($expenses as $exp)
            {
                $expense[$exp['value']] = $exp['text'];
            }
            foreach ($expense_budget as $index => $exp)
            {
                if (!$exp)
                {
                    $this->form_validation->set_rules('expense_budget[' . $index . ']', $expense[$index], 'required');
                }
            }
        }*/

        /*if (!$ids)
        {
            $this->message = $this->lang->line('SET_LEADING_FARMER');
            return false;
        }
        if (!$expense_budget)
        {
            $this->message = $this->lang->line('SET_BUDGET_EXPENSE_ITEMS');
            return false;
        } */
        return true;
    }
}
