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
        elseif ($action == "save_forward")
        {
            $this->system_save_forward();
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
        $data['date_proposal'] = 1;
        $data['date_expected'] = 1;
        $data['amount_budget_total'] = 1;
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
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data = array();
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
            $data = array();
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
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'INNER');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
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
        $this->db->where('fd_budget.status_budget', $this->config->item('system_status_pending'));
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data = array();
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
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'INNER');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
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
        $this->db->order_by('fd_budget.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }
        $this->json_return($items);
    }

    private function system_list_waiting()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list_waiting';
            $data = array();
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
        $this->db->select('fd_budget.*, fd_budget.id AS budget_id');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
        $this->db->select('variety1.name variety1_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'INNER');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
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
        $this->db->where('fd_budget.status_budget', $this->config->item('system_status_forwarded'));
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $time = time();
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'date_proposal' => $time,
                'participant_total' => 0
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
                'date_expected' => '',
                'quantity_market_size_total' => '',
                'quantity_market_size_arm' => '',
                'quantity_sales_target' => '',
                'remarks_budget' => ''
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

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = array();

            $data['previous_update_history'] = "";

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
            $this->db->select('fd_budget.date_proposal, fd_budget.status_budget');

            $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
            $this->db->select('fd_budget_details.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
            $this->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'INNER');
            $this->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

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
            if ($result['status_budget'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This Budget has been Forwarded Already';
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = Array(
                'id' => $result['budget_id'],
                'date_proposal' => $result['date_proposal']
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

                'crop_name' => $result['crop_name'],
                'crop_type_name' => $result['crop_type_name'],
                'variety1_name' => $result['variety1_name'],
                'variety2_name' => $result['variety2_name'],
                'division_name' => $result['division_name'],
                'zone_name' => $result['zone_name'],
                'territory_name' => $result['territory_name'],
                'district_name' => $result['district_name'],
                'outlet_name' => $result['outlet_name'],

                'address' => $result['address'],
                'present_condition' => $result['present_condition'],
                'farmers_evaluation' => $result['farmers_evaluation'],
                'diff_between_varieties' => $result['diff_between_varieties'],
                'date_expected' => $result['date_expected'],
                'participant_customers' => $result['participant_customers'],
                'participant_others' => $result['participant_others'],
                'quantity_market_size_total' => $result['quantity_market_size_total'],
                'quantity_market_size_arm' => $result['quantity_market_size_arm'],
                'quantity_sales_target' => $result['quantity_sales_target'],
                'remarks_budget' => $result['remarks_budget']
            );
            /*$data['item_info'] = Array(
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
                'date_expected' => $result['date_expected'],
                'participant_customers' => $result['participant_customers'],
                'participant_others' => $result['participant_others'],
                'quantity_market_size_total' => $result['quantity_market_size_total'],
                'quantity_market_size_arm' => $result['quantity_market_size_arm'],
                'quantity_sales_target' => $result['quantity_sales_target'],
                'remarks_budget' => $result['remarks_budget']
            );*/

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
            $data['crop_varieties1'] = Fd_budget_helper::get_variety_arm_upcoming($data['item_info']['crop_type_id']);
            $data['crop_varieties1'] = $data['crop_varieties1'][$data['item_info']['crop_type_id']];
            $data['crop_varieties2'] = Fd_budget_helper::get_variety_all($data['item_info']['crop_type_id']);
            $data['crop_varieties2'] = $data['crop_varieties2'][$data['item_info']['crop_type_id']];

            $dealers_by_outlet = Fd_budget_helper::get_dealers_ga($result['outlet_id']);
            $data['dealers'] = array();
            foreach ($dealers_by_outlet as $item)
            {
                $data['dealers'][] = array(
                    'value' => $item['dealer_id'],
                    'text' => $item['dealer_name'],
                    'phone_no' => $item['mobile_no']
                );
            }

            $lead_farmers_by_outlet = Fd_budget_helper::get_lead_farmers_ga($result['outlet_id']);
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
            $participants_data = json_decode($result['participants_dealer_farmer'], true);
            $dealer_participant = $participants_data['dealer_participant'];
            if (sizeof($dealer_participant) > 0)
            {
                foreach ($dealer_participant as $id => $val)
                {
                    $data['participants'][$id] = $val;
                }
            }
            $farmer_participant = $participants_data['farmer_participant'];
            if (sizeof($farmer_participant) > 0)
            {
                foreach ($farmer_participant as $id => $val)
                {
                    $data['participants'][$id] = $val;
                }
            }

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = array();

            $budget_result = json_decode($result['amount_expense_items'], true);
            foreach ($budget_result as $id => $val)
            {
                $data['expense_budget'][$id] = $val;
            }

            $data['previous_update_history'] = Fd_budget_helper::get_fd_budget_history($this->controller_url . "/history", $item_id);

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
        $total_budget = $participant_total = 0;

        // Permission Checking
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $result = Query_helper::get_info($this->config->item('table_ems_fd_budget'), array('*'), array('id=' . $id, 'status_budget !="' . $this->config->item('system_status_delete') . '"'), 1);
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $id, 'Edit Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_budget'] == $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'This Budget has been Forwarded Already';
            $this->json_return($ajax);
        }

        if ($dealer_participant && !empty($dealer_participant))
        {
            foreach ($dealer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $participant_total += $value;
                }
            }
        }
        if ($farmer_participant && !empty($farmer_participant))
        {
            foreach ($farmer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $participant_total += $value;
                }
            }
        }
        if (is_numeric($item_info['participant_customers']) && ($item_info['participant_customers'] > 0))
        {
            $participant_total += $item_info['participant_customers'];
        }
        if (is_numeric($item_info['participant_others']) && ($item_info['participant_others'] > 0))
        {
            $participant_total += $item_info['participant_others'];
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

        $item_head['date_expected'] = System_helper::get_time($item_info['date_expected']);
        $item_head['participant_total'] = $participant_total;
        $item_head['amount_budget_total'] = $total_budget;

        $participants = array(
            'dealer_participant' => $dealer_participant,
            'farmer_participant' => $farmer_participant
        );
        $item_info['participants_dealer_farmer'] = json_encode($participants);
        $item_info['date_expected'] = System_helper::get_time($item_info['date_expected']);
        $item_info['amount_expense_items'] = json_encode($expense_budget);

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) // EDIT
        {
            $item_head['variety1_id'] = $item_info['variety1_id'] = $result['variety1_id'];
            $item_head['variety2_id'] = $item_info['variety2_id'] = $result['variety1_id'];
            $item_head['outlet_id'] = $item_info['outlet_id'] = $result['outlet_id'];
            $budget_id = $id;
            /* Master Table Update */
            Query_helper::update($this->config->item('table_ems_fd_budget'), $item_head, array("id =" . $budget_id));
            /* Revision Update for Details Table */
            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details'));
        }
        else // ADD
        { /* Master Table Insert */
            $item_head['variety1_id'] = $item_info['variety1_id'];
            $item_head['variety2_id'] = $item_info['variety2_id'];
            $item_head['outlet_id'] = $item_info['outlet_id'];
            $item_head['date_proposal'] = System_helper::get_time($item_head['date_proposal']);
            $budget_id = Query_helper::add($this->config->item('table_ems_fd_budget'), $item_head);
        }

        /* Details Table Insert ( EDIT & ADD ) */
        $item_info['budget_id'] = $budget_id;
        $item_info['date_created'] = $time;
        $item_info['user_created'] = $user->user_id;
        $item_info['revision'] = 1;
        Query_helper::add($this->config->item('table_ems_fd_budget_details'), $item_info);

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
        if ((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
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

            $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
            $this->db->select('fd_budget.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
            $this->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'INNER');
            $this->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.name AS crop_name');

            $this->db->where('fd_budget.status_budget !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $this->db->order_by('fd_budget.id', 'DESC');
            $data['item'] = $this->db->get()->row_array();
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if ($data['item']['status_budget'] == $this->config->item('system_status_forwarded'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This Budget has been Forwarded Already';
                $this->json_return($ajax);
            }

            $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_deleted') . '"'));
            if (sizeof($results) > 0)
            {
                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
                foreach ($results as $result)
                {
                    $data['file_details'][$result['category_id']] = array(
                        'file_location_variety1' => $result['file_location_variety1'],
                        'remarks_variety1' => $result['remarks_variety1'],
                        'file_location_variety2' => $result['file_location_variety2'],
                        'remarks_variety2' => $result['remarks_variety2'],
                    );
                }
            }
            else
            {
                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
                $data['file_details'] = array();
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
        $item_info = $this->input->post('item_info');
        $user = User_helper::get_user();
        $time = time();
        // Permission Checking
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $result = Query_helper::get_info($this->config->item('table_ems_fd_budget'), array('*'), array('id=' . $item_id, 'status_budget !="' . $this->config->item('system_status_delete') . '"'), 1);
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_budget'] == $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'This Budget has been Forwarded Already';
            $this->json_return($ajax);
        }

        $insert_data = array(); // Main array for INSERT

        $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_deleted') . '"'));
        if (sizeof($results) > 0) // EDIT
        {
            foreach ($results as $result)
            {
                $insert_data[$result['category_id']] = array(
                    'file_name_variety1' => $result['file_name_variety1'],
                    'file_location_variety1' => $result['file_location_variety1'],
                    'remarks_variety1' => $result['remarks_variety1'],

                    'file_name_variety2' => $result['file_name_variety2'],
                    'file_location_variety2' => $result['file_location_variety2'],
                    'remarks_variety2' => $result['remarks_variety2'],
                );
            }
        }

        foreach ($item_info as $category_id => $info) // Submitted remarks
        {
            $insert_data[$category_id]['remarks_variety1'] = $info['remarks_variety1'];
            $insert_data[$category_id]['remarks_variety2'] = $info['remarks_variety2'];
        }

        $path = 'images/fd_budget_variety/' . $item_id;
        $uploaded_files = System_helper::upload_file($path);
        if ($uploaded_files && (sizeof($uploaded_files) > 0)) // File Upload
        {
            foreach ($uploaded_files as $key => $uploaded_file)
            {
                list($index1, $variety_id, $index2, $category_id) = explode("_", $key);
                if ($uploaded_file['status'])
                {
                    $insert_data[$category_id]['file_name_variety' . $variety_id] = $uploaded_file['info']['file_name'];
                    $insert_data[$category_id]['file_location_variety' . $variety_id] = $path . '/' . $uploaded_file['info']['file_name'];
                }
            }
        }

        $this->db->trans_start(); //DB Transaction Handle START
        //Update Revision
        $this->db->where('budget_id', $item_id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_ems_fd_budget_details_picture'));
        //Insert New Image
        foreach ($insert_data as $category_id => $item)
        {
            $item['budget_id'] = $item_id;
            $item['category_id'] = $category_id;
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            $item['revision'] = 1;
            Query_helper::add($this->config->item('table_ems_fd_budget_details_picture'), $item);
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
            $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
            $this->db->select('fd_budget.date_proposal, fd_budget.status_budget');

            $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
            $this->db->select('fd_budget_details.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.variety1_id', 'INNER');
            $this->db->select('variety1.name AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget_details.variety2_id', 'INNER');
            $this->db->select('variety2.name AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.name AS crop_name');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.id = fd_budget_details.outlet_id AND cus_info.revision=1 AND cus_info.type = ' . $this->config->item('system_customer_type_outlet_id'), 'INNER');
            $this->db->select('cus_info.name AS outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
            $this->db->select('district.name AS district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.name AS territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.name AS zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.name AS division_name');

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
            if ($result['status_budget'] != $this->config->item('system_status_pending'))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'This Budget has been Forwarded Already';
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = Array(
                'id' => $result['budget_id'],
                'date_proposal' => System_helper::display_date($result['date_proposal']),
                'crop_name' => $result['crop_name'],
                'crop_type_name' => $result['crop_type_name'],
                'variety1_name' => $result['variety1_name'],
                'variety2_name' => $result['variety2_name'],
                'division_name' => $result['division_name'],
                'zone_name' => $result['zone_name'],
                'territory_name' => $result['territory_name'],
                'district_name' => $result['district_name'],
                'outlet_name' => $result['outlet_name'],
                'address' => $result['address'],
                'present_condition' => $result['present_condition'],
                'farmers_evaluation' => $result['farmers_evaluation'],
                'diff_between_varieties' => $result['diff_between_varieties'],
                'date_expected' => System_helper::display_date($result['date_expected']),
                'participant_customers' => $result['participant_customers'],
                'participant_others' => $result['participant_others'],
                'quantity_market_size_total' => $result['quantity_market_size_total'],
                'quantity_market_size_arm' => $result['quantity_market_size_arm'],
                'quantity_sales_target' => $result['quantity_sales_target'],
                'remarks_budget' => $result['remarks_budget']
            );

            $result_data = Fd_budget_helper::get_dealers_ga($result['outlet_id']);
            $data['dealers_by_outlet'] = array();
            foreach ($result_data as $item)
            {
                $data['dealers_by_outlet'][$item['dealer_id']] = $item;
            }
            $result_data = Fd_budget_helper::get_lead_farmers_ga($result['outlet_id']);
            $data['lead_farmers_by_outlet'] = array();
            foreach ($result_data as $item)
            {
                $data['lead_farmers_by_outlet'][$item['lead_farmers_id']] = $item;
            }

            $result_data = json_decode($result['participants_dealer_farmer'], TRUE);
            $data['dealers'] = array();
            foreach ($result_data['dealer_participant'] as $key => $value)
            {
                if (isset($data['dealers_by_outlet'][$key]) && ($value > 0))
                {
                    $data['dealers_by_outlet'][$key]['participant'] = $value;
                    $data['dealers'][] = $data['dealers_by_outlet'][$key];
                }
            }
            $data['lead_farmers'] = array();
            foreach ($result_data['farmer_participant'] as $key => $value)
            {
                if (isset($data['lead_farmers_by_outlet'][$key]) && ($value > 0))
                {
                    $data['lead_farmers_by_outlet'][$key]['participant'] = $value;
                    $data['lead_farmers'][] = $data['lead_farmers_by_outlet'][$key];
                }
            }


            $result_expense_items = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));
            $budget_result = json_decode($result['amount_expense_items'], TRUE);
            $data['expense_items'] = array();
            foreach ($result_expense_items as &$item)
            {
                if ((isset($budget_result[$item['id']])) && ($budget_result[$item['id']] > 0))
                {
                    if ($item['status'] == $this->config->item('system_status_inactive'))
                    {
                        $item['name'] = $item['name'] . ' <b>(' . $item['status'] . ')</b>';
                    }
                    $item['amount'] = $budget_result[$item['id']];
                    $data['expense_items'][] = $item;
                }
            }


            /* $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));
            $budget_result = json_decode($result['amount_expense_items'], TRUE);
            foreach ($data['expense_items'] as $key => &$item)
            {
                if ($item['status'] == $this->config->item('system_status_inactive'))
                {
                    $item['name'] = $item['name'] . ' <b>(' . $item['status'] . ')</b>';
                }

                if (!($budget_result[$item['id']] > 0))
                {
                    unset($data['expense_items'][$key]);
                }
                else
                {
                    $item['amount'] = $budget_result[$item['id']];
                }
            } */

            $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_deleted') . '"'));
            if (sizeof($results) > 0)
            {
                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
                foreach ($results as $result)
                {
                    $data['file_details'][$result['category_id']] = array(
                        'file_location_variety1' => $result['file_location_variety1'],
                        'remarks_variety1' => $result['remarks_variety1'],
                        'file_location_variety2' => $result['file_location_variety2'],
                        'remarks_variety2' => $result['remarks_variety2'],
                    );
                }
            }
            else
            {
                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
                $data['file_details'] = array();
            }


            $data['title'] = "Forward Field Day Budget ( ID:" . $result['budget_id'] . " )";
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
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();
        // Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        //validation
        if ($item['status_budget'] != $this->config->item('system_status_forwarded'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_FORWARD')) . ' field is required.';
            $this->json_return($ajax);
        }

        $result = Query_helper::get_info($this->config->item('table_ems_fd_budget'), '*', array('id =' . $item_id, 'status !="' . $this->config->item('system_status_deleted') . '"'), 1);
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Forward Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if ($result['status_budget'] != $this->config->item('system_status_pending'))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = 'This Budget has been Forwarded Already';
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['date_forwarded_budget'] = $time;
        $item['user_forwarded_budget'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_fd_budget'), $item, array("id = " . $item_id));
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
        $data['items'] = Fd_budget_helper::get_dealers_ga($item_id);
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
        $data['label'] = $this->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER');
        $data['name_index'] = 'farmer_participant';
        $data['items'] = Fd_budget_helper::get_lead_farmers_ga($item_id);
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

    public function system_get_fd_budget_varieties($id = 0)
    {
        if ($id > 0)
        {
            $crop_type_id = $id;
        }
        else
        {
            $crop_type_id = $this->input->post('id');
        }
        $variety_arm_upcoming = Fd_budget_helper::get_variety_arm_upcoming($crop_type_id);
        $variety_all = Fd_budget_helper::get_variety_all($crop_type_id);

        $arm_upcoming['items'] = (sizeof($variety_arm_upcoming) > 0) ? $variety_arm_upcoming[$crop_type_id] : array();
        $all['items'] = (sizeof($variety_all) > 0) ? $variety_all[$crop_type_id] : array();

        $ajax = array();
        $ajax['system_content'][] = array("id" => "#variety1_id", "html" => $this->load->view("dropdown_with_select", $arm_upcoming, true));
        $ajax['system_content'][] = array("id" => "#variety2_id", "html" => $this->load->view("dropdown_with_select", $all, true));
        if (!(sizeof($variety_arm_upcoming) > 0))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "No data found for " . $this->lang->line('LABEL_VARIETY1_NAME');
            $this->json_return($ajax);
        }
        elseif (!(sizeof($variety_all) > 0))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = "No data found for " . $this->lang->line('LABEL_VARIETY2_NAME');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = true;
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $id = $this->input->post('id');
        $this->load->library('form_validation');
        if (!($id > 0))
        {
            $this->form_validation->set_rules('item[date_proposal]', $this->lang->line('LABEL_DATE'), 'required');
            $this->form_validation->set_rules('item_info[variety1_id]', $this->lang->line('LABEL_VARIETY_NAME'), 'required');
            $this->form_validation->set_rules('item_info[outlet_id]', $this->lang->line('LABEL_OUTLET_NAME'), 'required');
        }
        $this->form_validation->set_rules('item_info[address]', $this->lang->line('LABEL_ADDRESS'), 'required');
        $this->form_validation->set_rules('item_info[present_condition]', $this->lang->line('LABEL_PRESENT_CONDITION'), 'required');
        $this->form_validation->set_rules('item_info[farmers_evaluation]', $this->lang->line('LABEL_DEALERS_EVALUATION'), 'required');
        $this->form_validation->set_rules('item_info[diff_between_varieties]', $this->lang->line('LABEL_SPECIFIC_DIFFERENCE'), 'required');

        $this->form_validation->set_rules('item_info[date_expected]', $this->lang->line('LABEL_DATE_EXPECTED'), 'required');
        $this->form_validation->set_rules('item_info[participant_customers]', $this->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER'), 'required|numeric');
        $this->form_validation->set_rules('item_info[participant_others]', $this->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'), 'required|numeric');

        $this->form_validation->set_rules('item_info[quantity_market_size_total]', $this->lang->line('LABEL_TOTAL_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_market_size_arm]', $this->lang->line('LABEL_ARM_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_sales_target]', $this->lang->line('LABEL_NEXT_SALES_TARGET'), 'required|numeric');
        $this->form_validation->set_rules('item_info[remarks_budget]', 'TI ' . $this->lang->line('LABEL_RECOMMENDATION'), 'required');
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
        if (!($total_budget >= 0))
        {
            $this->message = $this->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET') . ' cannot be Negative';
            return false;
        }
        return true;
    }
}
