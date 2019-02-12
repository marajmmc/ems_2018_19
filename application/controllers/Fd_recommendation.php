<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fd_recommendation extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->common_view_location = 'fd_budget';
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
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "get_fd_budget_varieties")
        {
            $this->system_get_fd_budget_varieties();
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
        elseif ($action == "set_preference_list")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_list_all")
        {
            $this->system_set_preference('list_all');
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
        $data['outlet_name'] = 1;
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
        if ($method == 'list_all')
        {
            $data['status_recommendation'] = 1;
            $data['status_approve'] = 1;
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
            $data['title'] = "Field Day Recommendation Pending List";
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

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
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
        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.status_budget_forward', $this->config->item('system_status_forwarded'));
        $this->db->where('fd_budget.status_recommendation', $this->config->item('system_status_pending'));
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
            $data['title'] = "Field Day Recommendation All List";
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

    private function system_get_items_all()
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

        $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
        $this->db->select('variety2.name variety2_name');

        $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
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
        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.status_budget_forward', $this->config->item('system_status_forwarded'));
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
            $this->db->select('fd_budget.date_proposal, fd_budget.status_budget_forward, fd_budget.status_recommendation, fd_budget.status_approve');

            $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
            $this->db->select('fd_budget_details.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
            $this->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
            $this->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.id AS crop_type_id, crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.id AS crop_id, crop.name AS crop_name');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name AS outlet_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
            $this->db->select('district.id AS district_id, district.name AS district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id AS territory_id, territory.name AS territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id AS zone_id, zone.name AS zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id AS division_id, division.name AS division_name');

            $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $this->db->where('fd_budget_details.revision', 1);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Field Day Budget of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Edit Field Day Budget of other Location';
                $this->json_return($ajax);
            }
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['item_info'] = $result;
            $data['item'] = Array(
                'id' => $result['budget_id'],
                'date_proposal' => $result['date_proposal']
            );

            $data['crops'] = Query_helper::get_info($this->config->item('table_login_setup_classification_crops'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $data['crop_types'] = Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'), array('id value', 'name text'), array('crop_id =' . $result['crop_id'], 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));

            $variety_arm_upcoming = Fd_budget_helper::get_variety_arm_upcoming($result['crop_type_id']);
            $variety_all = Fd_budget_helper::get_variety_all($result['crop_type_id']);
            $data['crop_varieties1'] = (sizeof($variety_arm_upcoming) > 0) ? $variety_arm_upcoming[$result['crop_type_id']] : array();
            $data['crop_varieties2'] = (sizeof($variety_all) > 0) ? $variety_all[$result['crop_type_id']] : array();

            $data['growing_area'] = Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_areas'), 'id AS value, CONCAT_WS(" - ",name, address) text', array('status !="' . $this->config->item('system_status_delete') . '"', 'outlet_id =' . $result['outlet_id']), 0, 0, array('name'));
            $data['dealers'] = Fd_budget_helper::get_dealers_growing_area($result['outlet_id']);
            $data['leading_farmers'] = Fd_budget_helper::get_lead_farmers_growing_area($result['outlet_id']);

            $participants_data = json_decode($result['participants_dealer_farmer'], true);
            $data['participants'] = array();
            $data['participants'] = $participants_data['dealer_participant'] + $participants_data['farmer_participant'];

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
            $data['expense_budget'] = json_decode($result['amount_expense_items'], true);

            $data['items_history'] = Fd_budget_helper::get_fd_budget_history($item_id);

            $data['title'] = "Edit Field Day Recommendation ( ID:" . $result['budget_id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/edit", $data, true));
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
        $item_id = $this->input->post('id');
        $item_head = $this->input->post('item');
        $item_info = $this->input->post('item_info');
        $dealer_participant = $this->input->post('dealer_participant');
        $farmer_participant = $this->input->post('farmer_participant');
        $expense_budget = $this->input->post('expense_budget');

        $user = User_helper::get_user();
        $time = time();
        $total_budget = $participant_total = 0;

        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = fd_budget.user_created AND user_area.revision = 1', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.id', $item_id);
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Edit Field Day Budget of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Edit Field Day Budget of other Location';
            $this->json_return($ajax);
        }
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        if (sizeof($dealer_participant) > 0)
        {
            foreach ($dealer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $participant_total += $value;
                }
            }
        }
        if (sizeof($farmer_participant) > 0)
        {
            foreach ($farmer_participant as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $participant_total += $value;
                }
            }
        }
        if (is_numeric($item_info['participant_others']) && ($item_info['participant_others'] > 0))
        {
            $participant_total += $item_info['participant_others'];
        }
        if (sizeof($expense_budget) > 0)
        {
            foreach ($expense_budget as $value)
            {
                if (is_numeric($value) && ($value > 0))
                {
                    $total_budget += $value;
                }
            }
        }

        $budget_id = $item_id;
        // Prepare Master table data
        $item_head['date_expected'] = $item_info['date_expected'] = System_helper::get_time($item_info['date_expected']);
        $item_head['variety1_id'] = $item_info['variety1_id'];
        $item_head['variety2_id'] = $item_info['variety2_id'];
        $item_head['participant_total'] = $participant_total;
        $item_head['amount_budget_total'] = $total_budget;

        // Prepare Details table data
        $item_info['budget_id'] = $budget_id;
        $item_info['outlet_id'] = $result['outlet_id']; //From Last Revision
        $item_info['participants_dealer_farmer'] = json_encode(array('dealer_participant' => $dealer_participant, 'farmer_participant' => $farmer_participant));
        $item_info['amount_expense_items'] = json_encode($expense_budget);
        $item_info['date_created'] = $time;
        $item_info['user_created'] = $user->user_id;
        $item_info['revision'] = 1;

        $this->db->trans_start(); //DB Transaction Handle START
        //Master Table Update
        Query_helper::update($this->config->item('table_ems_fd_budget'), $item_head, array("id =" . $budget_id), FALSE);

        //Details Table Revision Update
        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_ems_fd_budget_details'), array(), array("budget_id =" . $budget_id), FALSE);

        //Details Table Insert
        Query_helper::add($this->config->item('table_ems_fd_budget_details'), $item_info, FALSE);
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
            $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
            $this->db->select('fd_budget.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
            $this->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
            $this->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.name AS crop_name');

            $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = fd_budget.user_created AND user_area.revision = 1', 'INNER');
            $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');

            $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Upload Picture of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Upload Field Day Picture of other Location';
                $this->json_return($ajax);
            }
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;

            $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $picture_data = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_delete') . '"'));
            if ($picture_data)
            {
                foreach ($picture_data as $value)
                {
                    $data['image_details'][$value['category_id']] = $value;
                }
                foreach ($data['picture_categories'] as $value)
                {
                    if (!isset($data['image_details'][$value['value']]))
                    {
                        $data['image_details'][$value['value']] = array(
                            'image_location_variety1' => FD_NO_IMAGE_PATH,
                            'remarks_variety1' => '',
                            'image_location_variety2' => FD_NO_IMAGE_PATH,
                            'remarks_variety2' => ''
                        );
                    }
                }
            }
            else
            {
                foreach ($data['picture_categories'] as $value)
                {
                    $data['image_details'][$value['value']] = array(
                        'image_location_variety1' => FD_NO_IMAGE_PATH,
                        'remarks_variety1' => '',
                        'image_location_variety2' => FD_NO_IMAGE_PATH,
                        'remarks_variety2' => ''
                    );
                }
            }

            $data['title'] = "Edit Field Day Recommendation Picture ( ID:" . $data['item']['id'] . " )";
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

        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = fd_budget.user_created AND user_area.revision = 1', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.id', $item_id);
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Upload Picture of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Upload Field Day Picture of other Location';
            $this->json_return($ajax);
        }
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $insert_data = array(); //Main array for INSERT

        foreach ($item_info as $category_id => $info) //Submitted remarks
        {
            if (trim($info['remarks_variety1']))
            {
                $insert_data[$category_id]['remarks_variety1'] = $info['remarks_variety1'];
            }
            if (($result['variety2_id'] > 0) && trim($info['remarks_variety2']))
            {
                $insert_data[$category_id]['remarks_variety2'] = $info['remarks_variety2'];
            }
        }

        $path = 'images/fd_budget_variety/' . $item_id;
        $uploaded_files = System_helper::upload_file($path);
        foreach ($uploaded_files as $file) // Validation for uploaded Files
        {
            if (!$file['status'])
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $file['message'];
                $this->json_return($ajax);
                die();
            }
        }
        if ($uploaded_files && (sizeof($uploaded_files) > 0)) // File/Image Upload
        {
            foreach ($uploaded_files as $key => $uploaded_file)
            {
                list($index1, $variety_id, $index2, $category_id) = explode("_", $key);
                if ($uploaded_file['status'])
                {
                    $insert_data[$category_id]['image_name_variety' . $variety_id] = $uploaded_file['info']['file_name'];
                    $insert_data[$category_id]['image_location_variety' . $variety_id] = $path . '/' . $uploaded_file['info']['file_name'];
                }
            }
        }

        $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_delete') . '"'));
        if ($results) //EDIT
        {
            foreach ($results as $row) // if No Image Selected in EDIT mode, but OLD Image Exist
            {
                if (!isset($insert_data[$row['category_id']]['image_location_variety1']))
                {
                    $insert_data[$row['category_id']]['image_name_variety1'] = $row['image_name_variety1'];
                    $insert_data[$row['category_id']]['image_location_variety1'] = $row['image_location_variety1'];
                }
                if (!isset($insert_data[$row['category_id']]['image_location_variety2']))
                {
                    $insert_data[$row['category_id']]['image_name_variety2'] = $row['image_name_variety2'];
                    $insert_data[$row['category_id']]['image_location_variety2'] = $row['image_location_variety2'];
                }
            }
        }
        else // First time ADD
        {
            if (empty($insert_data)) // Validation: If No Image is Selected/ No Remarks is given
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'No Remarks added/ New Image Selected.';
                $this->json_return($ajax);
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
            Query_helper::add($this->config->item('table_ems_fd_budget_details_picture'), $item, FALSE);
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
            $this->db->select('fd_budget.date_proposal, fd_budget.status_budget_forward, fd_budget.status_recommendation, fd_budget.status_approve');

            $this->db->join($this->config->item('table_ems_fd_budget_details') . ' fd_budget_details', 'fd_budget_details.budget_id = fd_budget.id', 'INNER');
            $this->db->select('fd_budget_details.*');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety1', 'variety1.id = fd_budget.variety1_id', 'INNER');
            $this->db->select('CONCAT(variety1.name, " ( ", variety1.whose, " )") AS variety1_name');

            $this->db->join($this->config->item('table_login_setup_classification_varieties') . ' variety2', 'variety2.id = fd_budget.variety2_id', 'LEFT');
            $this->db->select('CONCAT(variety2.name, " ( ", variety2.whose, " )") AS variety2_name');

            $this->db->join($this->config->item('table_login_setup_classification_crop_types') . ' crop_type', 'crop_type.id = variety1.crop_type_id', 'INNER');
            $this->db->select('crop_type.id AS crop_type_id, crop_type.name AS crop_type_name');

            $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = crop_type.crop_id', 'INNER');
            $this->db->select('crop.id AS crop_id, crop.name AS crop_name');

            $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = fd_budget.outlet_id AND cus_info.revision=1', 'INNER');
            $this->db->select('cus_info.name AS outlet_name');

            $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = fd_budget_details.growing_area_id', 'LEFT');
            $this->db->select('CONCAT_WS(" - ", areas.name, areas.address) AS growing_area_name');

            $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
            $this->db->select('district.id AS district_id, district.name AS district_name');

            $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
            $this->db->select('territory.id AS territory_id, territory.name AS territory_name');

            $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
            $this->db->select('zone.id AS zone_id, zone.name AS zone_name');

            $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
            $this->db->select('division.id AS division_id, division.name AS division_name');

            $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
            $this->db->where('fd_budget.id', $item_id);
            $this->db->where('fd_budget_details.revision', 1);
            $result = $this->db->get()->row_array();
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($result))
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Field Day Recommendation of other Location');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Trying to Forward Field Day Recommendation of other Location';
                $this->json_return($ajax);
            }
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;

            $data['dealers'] = Fd_budget_helper::get_dealers_growing_area($result['outlet_id']);
            $data['lead_farmers'] = Fd_budget_helper::get_lead_farmers_growing_area($result['outlet_id']);
            $result_data = json_decode($result['participants_dealer_farmer'], TRUE);

            foreach ($data['dealers'] as &$value)
            {
                if (isset($result_data['dealer_participant'][$value['dealer_id']]) && ($result_data['dealer_participant'][$value['dealer_id']] > 0))
                {
                    $value['participant'] = $result_data['dealer_participant'][$value['dealer_id']];
                }
                else
                {
                    unset($data['dealers'][$value['dealer_id']]);
                }
            }
            foreach ($data['lead_farmers'] as &$value)
            {
                if (isset($result_data['farmer_participant'][$value['lead_farmers_id']]) && ($result_data['farmer_participant'][$value['lead_farmers_id']] > 0))
                {
                    $value['participant'] = $result_data['farmer_participant'][$value['lead_farmers_id']];
                }
                else
                {
                    unset($data['lead_farmers'][$value['lead_farmers_id']]);
                }
            }

            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array(), 0, 0, array('ordering ASC'));
            $result_data = json_decode($result['amount_expense_items'], TRUE);
            foreach ($data['expense_items'] as &$expense_item)
            {
                if (isset($result_data[$expense_item['id']]))
                {
                    $expense_item['amount'] = $result_data[$expense_item['id']];
                }
                else
                {
                    $expense_item['amount'] = 0;
                }

                if ($expense_item['status'] == $this->config->item('system_status_inactive'))
                {
                    $expense_item['name'] = $expense_item['name'] . ' <b>(' . $expense_item['status'] . ')</b>';
                }
            }

            $picture_data = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_delete') . '"'));
            if ($picture_data)
            {
                foreach ($picture_data as $picture)
                {
                    $data['image_details'][$picture['category_id']] = $picture;
                }

                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array(), 0, 0, array('ordering ASC'));
                foreach ($data['picture_categories'] as $picture)
                {
                    if (!isset($data['image_details'][$picture['value']]))
                    {
                        $data['image_details'][$picture['value']] = array(
                            'image_location_variety1' => FD_NO_IMAGE_PATH,
                            'remarks_variety1' => '',
                            'image_location_variety2' => FD_NO_IMAGE_PATH,
                            'remarks_variety2' => ''
                        );
                    }
                }
            }
            else
            {
                $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
                foreach ($data['picture_categories'] as $picture)
                {
                    $data['image_details'][$picture['value']] = array(
                        'image_location_variety1' => FD_NO_IMAGE_PATH,
                        'remarks_variety1' => '',
                        'image_location_variety2' => FD_NO_IMAGE_PATH,
                        'remarks_variety2' => ''
                    );
                }
            }

            $data['title'] = "Forward Field Day Recommendation ( ID:" . $result['budget_id'] . " )";
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
        //Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        //validation
        if ($item['status_recommendation'] == '')
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_FORWARD')) . ' field is required.';
            $this->json_return($ajax);
        }
        if (trim($item['remarks_recommendation']) == '')
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_REMARKS')) . ' field is required.';
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
        $this->db->select('fd_budget.*');
        $this->db->join($this->config->item('table_login_setup_user_area') . ' user_area', 'user_area.user_id = fd_budget.user_created AND user_area.revision = 1', 'INNER');
        $this->db->select('user_area.division_id, user_area.zone_id, user_area.territory_id, user_area.district_id');
        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.id', $item_id);
        $result = $this->db->get()->row_array();
        if (!$result)
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'ID Not Exists');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Try.';
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result))
        {
            System_helper::invalid_try(__FUNCTION__, $item_id, 'Trying to Forward Field Day Budget Recommendation of other Location');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Trying to Forward Field Day Budget Recommendation of other Location';
            $this->json_return($ajax);
        }
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_FORWARDED, FD_BUDGET_NOT_REJECTED_ZI, FD_RECOMMENDATION_NOT_FORWARDED, FD_BUDGET_NOT_REJECTED_DI));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        // Update details table if Rollback
        if ($item['status_recommendation'] == $this->config->item('system_status_pending'))
        {
            $item_info = array(
                'remarks_rollback_zi' => $item['remarks_recommendation'],
                'date_rollback_zi' => $time,
                'user_rollback_zi' => $user->user_id
            );
            Query_helper::update($this->config->item('table_ems_fd_budget_details'), $item_info, array("budget_id =" . $item_id, "revision=1"), FALSE);
            $item['status_budget_forward'] = $this->config->item('system_status_pending');
        }
        // Update Master table
        $item['date_recommendation'] = $time;
        $item['user_recommendation'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_fd_budget'), $item, array("id = " . $item_id), FALSE);

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

            $data = Fd_budget_helper::get_fd_budget_details_data($item_id); // Fetching all Data from 'Fd_budget_helper' along with Page Title

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details", $data, true));
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item_info[date_expected]', $this->lang->line('LABEL_DATE_EXPECTED'), 'required');
        $this->form_validation->set_rules('item_info[variety1_id]', $this->lang->line('LABEL_VARIETY1_NAME'), 'required|numeric');
        $this->form_validation->set_rules('item_info[present_condition]', $this->lang->line('LABEL_PRESENT_CONDITION'), 'required');
        $this->form_validation->set_rules('item_info[farmers_evaluation]', $this->lang->line('LABEL_DEALERS_EVALUATION'), 'required');

        $this->form_validation->set_rules('item_info[address]', $this->lang->line('LABEL_ADDRESS'), 'required');
        $this->form_validation->set_rules('item_info[participant_others]', $this->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_market_size_showroom_total]', $this->lang->line('LABEL_TOTAL_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_market_size_ga_total]', $this->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_market_size_showroom_arm]', $this->lang->line('LABEL_ARM_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_market_size_ga_arm]', $this->lang->line('LABEL_ARM_GA_MARKET_SIZE'), 'required|numeric');
        $this->form_validation->set_rules('item_info[quantity_sales_target]', $this->lang->line('LABEL_NEXT_SALES_TARGET'), 'required|numeric');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }

        $expense_budget = $this->input->post('expense_budget');
        $total_budget = 0;
        if ($expense_budget)
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

    private function system_get_fd_budget_varieties($id = 0)
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
}
