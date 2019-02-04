<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fd_reporting extends Root_Controller
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
        $this->common_view_location = 'Fd_budget';
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
        elseif ($action == "reporting")
        {
            $this->system_reporting($id);
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
        /*
        elseif ($action == "details")
        {
            $this->system_details($id);
        }*/
        elseif ($action == "set_preference_list")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_list_all")
        {
            $this->system_set_preference('list_all');
        }
        /* elseif ($action == "set_preference_list_waiting")
        {
            $this->system_set_preference('list_waiting');
        } */
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
        if ($method == 'list')
        {
            $data['reporting_count'] = 1;
        }
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
            $data['status_reporting_forward'] = 1;
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
            $data['title'] = "Field Day Reporting Pending List";
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
        $this->db->where('fd_budget.status_approve', $this->config->item('system_status_approved'));
        $this->db->where('fd_budget.status_payment_pay', $this->config->item('system_status_paid'));
        $this->db->where('fd_budget.status_reporting_forward !=', $this->config->item('system_status_forwarded'));
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();

        $this->db->group_by('budget_id');
        $results = Query_helper::get_info($this->config->item('table_ems_fd_budget_reporting'), array('budget_id, COUNT(*) reporting_count'), array());

        $reporting = array();
        foreach ($results as $result)
        {
            $reporting[$result['budget_id']] = $result['reporting_count'];
        }

        foreach ($items as &$item)
        {
            $item['reporting_count'] = (isset($reporting[$item['budget_id']])) ? $reporting[$item['budget_id']] : "";
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
            $data['title'] = "Field Day Reporting All List";
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
        $this->db->where('fd_budget.status_approve', $this->config->item('system_status_approved'));
        $this->db->where('fd_budget.status_payment_pay', $this->config->item('system_status_paid'));
        $this->db->order_by('fd_budget.id', 'DESC');
        $items = $this->db->get()->result_array();
        foreach ($items as &$item)
        {
            $item['date_proposal'] = System_helper::display_date($item['date_proposal']);
            $item['date_expected'] = System_helper::display_date($item['date_expected']);
        }
        $this->json_return($items);
    }

    private function system_reporting($id)
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
            $result = Fd_budget_helper::get_fd_budget_by_id($item_id, __FUNCTION__);
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_APPROVED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;
            $data['info_basic'] = Fd_budget_helper::get_basic_info($result);
            //$data['accordion']['collapse'] = 'in';

            $old_reporting = Query_helper::get_info($this->config->item('table_ems_fd_budget_reporting'), array('*'), array('budget_id=' . $item_id, 'revision=1'), 1);
            if ($old_reporting)
            {
                $old_reporting['reporting_participants_dealer'] = json_decode($old_reporting['reporting_participants_dealer'], true);
                $old_reporting['reporting_participants_farmer'] = json_decode($old_reporting['reporting_participants_farmer'], true);
                $old_reporting['reporting_amount_expense_items'] = json_decode($old_reporting['reporting_amount_expense_items'], true);
            }
            else
            {
                $old_reporting = array();
            }
            $data['old_reporting'] = $old_reporting;

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

            $data['title'] = "Field Day Reporting ( ID:" . $result['budget_id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/reporting", $data, true));
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
        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $item = $this->input->post('item');
        $dealer = $this->input->post('dealer_participants');
        $farmer = $this->input->post('farmer_participants');
        $expense_item = $this->input->post('expense_item_id');
        $user = User_helper::get_user();
        $time = time();
        if ($dealer)
        {
            foreach ($dealer as &$value)
            {
                if (!($value > 0))
                {
                    $value = 0;
                }
            }
            $item['reporting_participants_dealer'] = json_encode($dealer);
        }
        if ($farmer)
        {
            foreach ($farmer as &$value)
            {
                if (!($value > 0))
                {
                    $value = 0;
                }
            }
            $item['reporting_participants_farmer'] = json_encode($farmer);
        }
        if ($expense_item)
        {
            foreach ($expense_item as &$value)
            {
                if (!($value > 0))
                {
                    $value = 0;
                }
            }
            $item['reporting_amount_expense_items'] = json_encode($expense_item);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $this->db->set('revision', 'revision+1', FALSE);
        Query_helper::update($this->config->item('table_ems_fd_budget_reporting'), array(), array("budget_id =" . $item['budget_id']), FALSE);

        $item['revision'] = 1;
        $item['date_reported'] = $time;
        $item['user_reported'] = $user->user_id;
        Query_helper::add($this->config->item('table_ems_fd_budget_reporting'), $item, FALSE);

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

            $result = Fd_budget_helper::get_fd_budget_by_id($item_id, __FUNCTION__);
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_APPROVED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['item'] = $result;
            $data['info_basic'] = Fd_budget_helper::get_basic_info($result);
            $data['accordion']['collapse'] = 'in';

            $old_reporting = Query_helper::get_info($this->config->item('table_ems_fd_budget_reporting'), array('*'), array('budget_id=' . $item_id, 'revision=1'), 1);
            if ($old_reporting)
            {
                $old_reporting['reporting_participants_dealer'] = json_decode($old_reporting['reporting_participants_dealer'], true);
                $old_reporting['reporting_participants_farmer'] = json_decode($old_reporting['reporting_participants_farmer'], true);
                $old_reporting['reporting_amount_expense_items'] = json_decode($old_reporting['reporting_amount_expense_items'], true);
            }
            else
            {
                $old_reporting = array();
            }
            $data['old_reporting'] = $old_reporting;

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

            $data['title'] = "Field Day Reporting Forward ( ID:" . $result['budget_id'] . " )";
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
        //Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $item_id = $this->input->post('budget_id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        $result = Fd_budget_helper::get_fd_budget_by_id($item_id, __FUNCTION__);
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_APPROVED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['date_reporting_forward'] = $time;
        $item['user_reporting_forward'] = $user->user_id;
        Query_helper::update($this->config->item('table_ems_fd_budget'), $item, array("id =" . $item_id), FALSE);

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
}
