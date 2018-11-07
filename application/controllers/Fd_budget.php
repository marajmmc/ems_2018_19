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
        elseif ($action == "set_preference")
        {
            $this->system_set_preference('list');
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
        $data['fdb_no'] = 1;
        $data['fdb_proposal_date'] = 1;
        $data['expected_date'] = 1;
        $data['total_budget'] = 1;
        $data['crop_name'] = 1;
        $data['crop_type_name'] = 1;
        $data['variety_name'] = 1;
        $data['com_variety_name'] = 1;
        $data['division_name'] = 1;
        $data['zone_name'] = 1;
        $data['territory_name'] = 1;
        $data['district_name'] = 1;
        $data['outlet_name'] = 1;
        $data['status_budget'] = 1;
        $data['status_requested'] = 1;
        $data['status_approved'] = 1;
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
            $ajax['system_page_url'] = site_url($this->controller_url);
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
        $this->db->from($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details');
        $this->db->select('fd_budget_details.*');

        $this->db->join($this->config->item('table_ems_fd_budget') . ' fd_budget', 'fd_budget.id = fd_budget_details.budget_id', 'INNER');
        $this->db->select('fd_budget.*, fd_budget.id AS fdb_no, fd_budget.date AS fdb_proposal_date');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety', 'variety.id = fd_budget_details.variety_id', 'INNER');
        $this->db->select('variety.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.competitor_variety_id', 'LEFT');
        $this->db->select('variety1.name com_variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety.crop_type_id', 'INNER');
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
        //$this->db->group_by('fd_budget.id');
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['fdb_proposal_date'] = System_helper::display_date($item['fdb_proposal_date']);
            $item['expected_date'] = System_helper::display_date($item['expected_date']);
            $item['total_budget'] = System_helper::get_string_amount($item['total_budget']);
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
            $ajax['system_page_url'] = site_url($this->controller_url);
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
        $this->db->from($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details');
        $this->db->select('fd_budget_details.*');

        $this->db->join($this->config->item('table_ems_fd_budget') . ' fd_budget', 'fd_budget.id = fd_budget_details.budget_id', 'INNER');
        $this->db->select('fd_budget.*, fd_budget.id AS fdb_no, fd_budget.date AS fdb_proposal_date');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety', 'variety.id = fd_budget_details.variety_id', 'INNER');
        $this->db->select('variety.name variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget_details.competitor_variety_id', 'LEFT');
        $this->db->select('variety1.name com_variety_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety.crop_type_id', 'INNER');
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
        //$this->db->group_by('fd_budget.id');
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();

        foreach ($items as &$item)
        {
            $item['fdb_proposal_date'] = System_helper::display_date($item['fdb_proposal_date']);
            $item['expected_date'] = System_helper::display_date($item['expected_date']);
            $item['total_budget'] = System_helper::get_string_amount($item['total_budget']);
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
                'variety_id' => '',
                'competitor_variety_id' => '',
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
                'total_budget' => 0,
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
                        if ($this->locations['district_id'] > 0)
                        {
                            $data['upazillas'] = Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'), array('id value', 'name text'), array('district_id =' . $this->locations['district_id'], 'status !="' . $this->config->item('system_status_delete') . '"'));
                        }
                    }
                }
            }
            $data['crops'] = Query_helper::get_info($this->config->item('table_login_setup_classification_crops'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'));
            $data['crop_types'] = array();
            $data['crop_varieties'] = array();
            $data['competitor_varieties'] = array();
            $data['participants'] = array();
            $data['dealers'] = array();
            $data['leading_farmers'] = array();
            $data['total'] = '';

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array('status !="' . $this->config->item('system_status_delete') . '"'), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = array();

            $data['system_all_varieties'] = Fd_budget_helper::get_dropdown_all_crop_variety();

            $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'), 0, 0, array('ordering ASC'));
            $data['file_details'] = array();

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

    /*

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

            $data['item'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('*'), array('id =' . $item_id, 'status !="' . $this->config->item('system_status_delete') . '"'), 1, 0, array('id ASC'));
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['title'] = "Edit Field Day Picture Category :: " . $data['item']['name'];
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
*/

    private function system_save()
    {
        //pr($this->input->post());

        $id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $items = $this->input->post('item_info');
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
        if (is_numeric($items['participant_through_customer']) && ($items['participant_through_customer'] > 0))
        {
            $no_of_participant += $items['participant_through_customer'];
        }
        if (is_numeric($items['participant_through_others']) && ($items['participant_through_others'] > 0))
        {
            $no_of_participant += $items['participant_through_others'];
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
        $items['expected_date'] = System_helper::get_time($items['expected_date']);
        $items['no_of_participant'] = $no_of_participant;
        $items['total_budget'] = $total_budget;

//        pr($item_head, 0);
//        pr($items, 0);
//        pr($expense_budget, 0);
//        pr($dealer_participant, 0);
//        pr($farmer_participant);

        $this->db->trans_start(); //DB Transaction Handle START

        if ($id > 0) // EDIT
        {
            $budget_id = $id;
            /* Details Table Revision Update */
            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details'));

            /* Expense Table Revision Update */
            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_details_expense'));

            /* Dealer, Lead Farmer Participant Table Revision Update */
            $this->db->where('budget_id', $budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'));
        }
        else // ADD
        {
            /* Master Table Insert */
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            $budget_id = Query_helper::add($this->config->item('table_ems_fd_budget'), $item_head);
        }

        /* Details Table Insert ( EDIT & ADD ) */
        $items['budget_id'] = $budget_id;
        $items['date_created'] = $time;
        $items['user_created'] = $user->user_id;
        $items['revision'] = 1;
        Query_helper::add($this->config->item('table_ems_fd_budget_details'), $items);

        /* Expense Table Insert ( EDIT & ADD ) */
        foreach ($expense_budget as $key => $value)
        {
            $data = array(
                'budget_id' => $budget_id,
                'item_id' => $key,
                'amount' => $value,
                'user_created' => $user->user_id,
                'date_created' => $time,
                'revision' => 1
            );
            Query_helper::add($this->config->item('table_ems_fd_budget_details_expense'), $data);
        }
        /* Expense Table Insert ( EDIT & ADD ) */
        foreach ($farmer_participant as $key => $value)
        {
            $data = array(
                'budget_id' => $budget_id,
                'item_id' => $key,
                'amount' => $value,
                'user_created' => $user->user_id,
                'date_created' => $time,
                'revision' => 1
            );
            Query_helper::add($this->config->item('table_ems_fd_budget_details_expense'), $data);
        }
        /* Dealer Participant & Farmer Participant Table Insert ( EDIT & ADD ) */
        foreach ($dealer_participant as $key => $value)
        {
            $data = array(
                'budget_id' => $budget_id,
                'participant_type' => 1, // 1 for Dealer Participant
                'dealer_leadfarmer_id' => $key,
                'participant_number' => $value,
                'user_created' => $user->user_id,
                'date_created' => $time,
                'revision' => 1
            );
            Query_helper::add($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'), $data);
        }
        foreach ($farmer_participant as $key => $value)
        {
            $data = array(
                'budget_id' => $budget_id,
                'participant_type' => 2, // 2 for Lead farmer Participant
                'dealer_leadfarmer_id' => $key,
                'participant_number' => $value,
                'user_created' => $user->user_id,
                'date_created' => $time,
                'revision' => 1
            );
            Query_helper::add($this->config->item('table_ems_fd_budget_dealer_leadfarmer_participant'), $data);
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

        /* $id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        // Validation Checking
        if ($id > 0) // EDIT
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $result = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), '*', array('id =' . $id, 'status != "' . $this->config->item('system_status_delete') . '"'), 1);
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Update Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else // ADD
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) // EDIT
        {
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_ems_setup_fd_picture_category'), $item, array('id=' . $id));
        }
        else // ADD
        {
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_ems_setup_fd_picture_category'), $item);
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new = $this->input->post('system_save_new_status');
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
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
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        } */
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


    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date]', $this->lang->line('LABEL_DATE'), 'required');
        $this->form_validation->set_rules('item_info[variety_id]', $this->lang->line('LABEL_VARIETY_NAME'), 'required');
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

        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
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


    /*    private function check_validation()
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('item[name]', $this->lang->line('LABEL_NAME'), 'required|trim');
            $this->form_validation->set_rules('item[ordering]', $this->lang->line('LABEL_ORDER'), 'required');
            $this->form_validation->set_rules('item[status]', $this->lang->line('LABEL_STATUS'), 'required');
            if ($this->form_validation->run() == FALSE)
            {
                $this->message = validation_errors();
                return false;
            }
            return true;
        }*/

}
