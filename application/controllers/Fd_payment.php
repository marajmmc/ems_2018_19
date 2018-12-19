<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fd_payment extends Root_Controller
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
        elseif ($action == "payment")
        {
            $this->system_payment($id);
        }
        elseif ($action == "save_payment")
        {
            $this->system_save_payment();
        }
        elseif ($action == "approve")
        {
            $this->system_approve($id);
        }
        elseif ($action == "save_approve")
        {
            $this->system_save_approve();
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
            $data['status_payment_approve'] = 1;
            $data['status_payment_pay'] = 1;
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
            $data['title'] = "Field Day Payment Pending List";
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

        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.status_approve', $this->config->item('system_status_approved'));
        // if Only have Approve Permission
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) && (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $this->db->where('fd_budget.status_payment_approve !=', $this->config->item('system_status_approved'));
        }
        // if Only have Payment Permission
        if ((isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) && !(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)))
        {
            $this->db->where('fd_budget.status_payment_approve', $this->config->item('system_status_approved'));
        }
        $this->db->where('fd_budget.status_payment_pay !=', $this->config->item('system_status_paid'));
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
            $data['title'] = "Field Day Payment All List";
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

        $this->db->where('fd_budget.status !=', $this->config->item('system_status_delete'));
        $this->db->where('fd_budget.status_approve', $this->config->item('system_status_approved'));
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

    private function system_payment($id)
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
            $this->db->select('fd_budget.date_proposal,
                                fd_budget.participant_total,
                                fd_budget.amount_budget_total,
                                fd_budget.status,
                                fd_budget.date_created budget_date_created,
                                fd_budget.user_created budget_user_created,
                                fd_budget.remarks_delete,
                                fd_budget.date_deleted,
                                fd_budget.user_deleted,
                                fd_budget.status_budget_forward,
                                fd_budget.remarks_budget_forward,
                                fd_budget.date_budget_forwarded,
                                fd_budget.user_budget_forwarded,
                                fd_budget.status_recommendation,
                                fd_budget.remarks_recommendation,
                                fd_budget.date_recommendation,
                                fd_budget.user_recommendation,
                                fd_budget.status_approve,
                                fd_budget.remarks_approve,
                                fd_budget.date_approved,
                                fd_budget.user_approved,
                                fd_budget.status_payment_approve,
                                fd_budget.remarks_payment_approve,
                                fd_budget.date_payment_approved,
                                fd_budget.user_payment_approved,
                                fd_budget.status_payment_pay,
                                fd_budget.remarks_payment_pay,
                                fd_budget.date_payment_paid,
                                fd_budget.user_payment_paid');

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
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_APPROVED, FD_PAYMENT_NOT_PAID));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['info_basic'] = array();
            $data['item'] = $result;

            //---------Getting User Names------------
            $user_ids = array(
                $result['budget_user_created'] => $result['budget_user_created'],
                $result['user_deleted'] => $result['user_deleted'],
                $result['user_budget_forwarded'] => $result['user_budget_forwarded'],
                $result['user_recommendation'] => $result['user_recommendation'],
                $result['user_approved'] => $result['user_approved'],
                $result['user_payment_approved'] => $result['user_payment_approved'],
                $result['user_payment_paid'] => $result['user_payment_paid']
            );
            $result['user_info'] = System_helper::get_users_info($user_ids);

            //----------------Basic Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                'value_1' => $result['user_info'][$result['user_created']]['name'],
                'label_2' => $this->lang->line('LABEL_CREATED_TIME'),
                'value_2' => System_helper::display_date($result['date_created'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DATE_PROPOSAL'),
                'value_1' => System_helper::display_date($result['date_proposal']),
                'label_2' => $this->lang->line('LABEL_DATE_EXPECTED'),
                'value_2' => System_helper::display_date($result['date_expected'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CROP_NAME'),
                'value_1' => $result['crop_name'],
                'label_2' => $this->lang->line('LABEL_CROP_TYPE'),
                'value_2' => $result['crop_type_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_VARIETY1_NAME'),
                'value_1' => $result['variety1_name'],
                'label_2' => $this->lang->line('LABEL_VARIETY2_NAME'),
                'value_2' => ($result['variety2_name']) ? $result['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_PRESENT_CONDITION'),
                'value_1' => nl2br($result['present_condition']),
                'label_2' => $this->lang->line('LABEL_DEALERS_EVALUATION'),
                'value_2' => nl2br($result['farmers_evaluation'])
            );
            //----------------Location Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Location'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ADDRESS'),
                'value_1' => nl2br($result['address'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_OUTLET_NAME'),
                'value_1' => $result['outlet_name'],
                'label_2' => $this->lang->line('LABEL_GROWING_AREA'),
                'value_2' => ($result['growing_area_name']) ? $result['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DISTRICT_NAME'),
                'value_1' => $result['district_name'],
                'label_2' => $this->lang->line('LABEL_TERRITORY_NAME'),
                'value_2' => $result['territory_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ZONE_NAME'),
                'value_1' => $result['zone_name'],
                'label_2' => $this->lang->line('LABEL_DIVISION_NAME'),
                'value_2' => $result['division_name']
            );
            //----------------Market Size Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_MARKET_SIZE_TITLE')
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_TOTAL_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_total']),
                'label_2' => $this->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_total'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ARM_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_arm']),
                'label_2' => $this->lang->line('LABEL_ARM_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_arm'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_NEXT_SALES_TARGET'),
                'value_1' => System_helper::get_string_kg($result['quantity_sales_target'])
            );
            //----------------Status Info Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Budget Status'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_FORWARD'),
                'value_1' => $result['status_budget_forward'],
                'label_2' => 'Budget ' . $this->lang->line('LABEL_REMARKS'),
                'value_2' => ($result['remarks_budget_forward']) ? nl2br($result['remarks_budget_forward']) : '-'
            );
            if ($result['user_budget_forwarded'] > 0)
            {
                $data['info_basic'][] = array(
                    'label_1' => 'Forwarded By',
                    'value_1' => $result['user_info'][$result['user_budget_forwarded']]['name'],
                    'label_2' => 'Forward Time',
                    'value_2' => System_helper::display_date_time($result['date_budget_forwarded'])
                );

                $data['info_basic'][] = array(
                    'label_1' => $this->lang->line('LABEL_STATUS_RECOMMENDATION'),
                    'value_1' => $result['status_recommendation'],
                    'label_2' => 'Recommendation ' . $this->lang->line('LABEL_REMARKS'),
                    'value_2' => ($result['remarks_recommendation']) ? nl2br($result['remarks_recommendation']) : '-'
                );
                if ($result['user_recommendation'] > 0)
                {
                    $data['info_basic'][] = array(
                        'label_1' => 'Recommended By',
                        'value_1' => $result['user_info'][$result['user_recommendation']]['name'],
                        'label_2' => 'Recommendation Time',
                        'value_2' => System_helper::display_date_time($result['date_recommendation'])
                    );

                    $data['info_basic'][] = array(
                        'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_APPROVE'),
                        'value_1' => $result['status_approve'],
                        'label_2' => 'Approval ' . $this->lang->line('LABEL_REMARKS'),
                        'value_2' => ($result['remarks_approve']) ? nl2br($result['remarks_approve']) : '-'
                    );
                    if ($result['user_approved'] > 0)
                    {
                        $data['info_basic'][] = array(
                            'label_1' => 'Approved By',
                            'value_1' => $result['user_info'][$result['user_approved']]['name'],
                            'label_2' => 'Approval Time',
                            'value_2' => System_helper::display_date_time($result['date_approved'])
                        );
                    }
                }
            }

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

            $data['title'] = "Pay Field Day Payment ( ID:" . $result['budget_id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/payment", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/payment/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_payment()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();
        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        //validation
        if ($item['status_payment_pay'] == '')
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_PAYMENT')) . ' field is required.';
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
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_APPROVED, FD_PAYMENT_NOT_PAID));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['date_payment_paid'] = $time;
        $item['user_payment_paid'] = $user->user_id;
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

    private function system_approve($id)
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
            $this->db->select('fd_budget.date_proposal,
                                fd_budget.participant_total,
                                fd_budget.amount_budget_total,
                                fd_budget.status,
                                fd_budget.date_created budget_date_created,
                                fd_budget.user_created budget_user_created,
                                fd_budget.remarks_delete,
                                fd_budget.date_deleted,
                                fd_budget.user_deleted,
                                fd_budget.status_budget_forward,
                                fd_budget.remarks_budget_forward,
                                fd_budget.date_budget_forwarded,
                                fd_budget.user_budget_forwarded,
                                fd_budget.status_recommendation,
                                fd_budget.remarks_recommendation,
                                fd_budget.date_recommendation,
                                fd_budget.user_recommendation,
                                fd_budget.status_approve,
                                fd_budget.remarks_approve,
                                fd_budget.date_approved,
                                fd_budget.user_approved,
                                fd_budget.status_payment_approve,
                                fd_budget.remarks_payment_approve,
                                fd_budget.date_payment_approved,
                                fd_budget.user_payment_approved,
                                fd_budget.status_payment_pay,
                                fd_budget.remarks_payment_pay,
                                fd_budget.date_payment_paid,
                                fd_budget.user_payment_paid');

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
            $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_NOT_APPROVED));
            if (!$ajax['status'])
            {
                $this->json_return($ajax);
            }

            $data = array();
            $data['info_basic'] = array();
            $data['item'] = $result;

            //---------Getting User Names------------
            $user_ids = array(
                $result['budget_user_created'] => $result['budget_user_created'],
                $result['user_deleted'] => $result['user_deleted'],
                $result['user_budget_forwarded'] => $result['user_budget_forwarded'],
                $result['user_recommendation'] => $result['user_recommendation'],
                $result['user_approved'] => $result['user_approved'],
                $result['user_payment_approved'] => $result['user_payment_approved'],
                $result['user_payment_paid'] => $result['user_payment_paid']
            );
            $result['user_info'] = System_helper::get_users_info($user_ids);

            //----------------Basic Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                'value_1' => $result['user_info'][$result['user_created']]['name'],
                'label_2' => $this->lang->line('LABEL_CREATED_TIME'),
                'value_2' => System_helper::display_date($result['date_created'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DATE_PROPOSAL'),
                'value_1' => System_helper::display_date($result['date_proposal']),
                'label_2' => $this->lang->line('LABEL_DATE_EXPECTED'),
                'value_2' => System_helper::display_date($result['date_expected'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CROP_NAME'),
                'value_1' => $result['crop_name'],
                'label_2' => $this->lang->line('LABEL_CROP_TYPE'),
                'value_2' => $result['crop_type_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_VARIETY1_NAME'),
                'value_1' => $result['variety1_name'],
                'label_2' => $this->lang->line('LABEL_VARIETY2_NAME'),
                'value_2' => ($result['variety2_name']) ? $result['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_PRESENT_CONDITION'),
                'value_1' => nl2br($result['present_condition']),
                'label_2' => $this->lang->line('LABEL_DEALERS_EVALUATION'),
                'value_2' => nl2br($result['farmers_evaluation'])
            );
            //----------------Location Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Location'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ADDRESS'),
                'value_1' => nl2br($result['address'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_OUTLET_NAME'),
                'value_1' => $result['outlet_name'],
                'label_2' => $this->lang->line('LABEL_GROWING_AREA'),
                'value_2' => ($result['growing_area_name']) ? $result['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DISTRICT_NAME'),
                'value_1' => $result['district_name'],
                'label_2' => $this->lang->line('LABEL_TERRITORY_NAME'),
                'value_2' => $result['territory_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ZONE_NAME'),
                'value_1' => $result['zone_name'],
                'label_2' => $this->lang->line('LABEL_DIVISION_NAME'),
                'value_2' => $result['division_name']
            );
            //----------------Market Size Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_MARKET_SIZE_TITLE')
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_TOTAL_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_total']),
                'label_2' => $this->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_total'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ARM_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_arm']),
                'label_2' => $this->lang->line('LABEL_ARM_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_arm'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_NEXT_SALES_TARGET'),
                'value_1' => System_helper::get_string_kg($result['quantity_sales_target'])
            );
            //----------------Status Info Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Budget Status'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_FORWARD'),
                'value_1' => $result['status_budget_forward'],
                'label_2' => 'Budget ' . $this->lang->line('LABEL_REMARKS'),
                'value_2' => ($result['remarks_budget_forward']) ? nl2br($result['remarks_budget_forward']) : '-'
            );
            if ($result['user_budget_forwarded'] > 0)
            {
                $data['info_basic'][] = array(
                    'label_1' => 'Forwarded By',
                    'value_1' => $result['user_info'][$result['user_budget_forwarded']]['name'],
                    'label_2' => 'Forward Time',
                    'value_2' => System_helper::display_date_time($result['date_budget_forwarded'])
                );

                $data['info_basic'][] = array(
                    'label_1' => $this->lang->line('LABEL_STATUS_RECOMMENDATION'),
                    'value_1' => $result['status_recommendation'],
                    'label_2' => 'Recommendation ' . $this->lang->line('LABEL_REMARKS'),
                    'value_2' => ($result['remarks_recommendation']) ? nl2br($result['remarks_recommendation']) : '-'
                );
                if ($result['user_recommendation'] > 0)
                {
                    $data['info_basic'][] = array(
                        'label_1' => 'Recommended By',
                        'value_1' => $result['user_info'][$result['user_recommendation']]['name'],
                        'label_2' => 'Recommendation Time',
                        'value_2' => System_helper::display_date_time($result['date_recommendation'])
                    );

                    $data['info_basic'][] = array(
                        'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_APPROVE'),
                        'value_1' => $result['status_approve'],
                        'label_2' => 'Approval ' . $this->lang->line('LABEL_REMARKS'),
                        'value_2' => ($result['remarks_approve']) ? nl2br($result['remarks_approve']) : '-'
                    );
                    if ($result['user_approved'] > 0)
                    {
                        $data['info_basic'][] = array(
                            'label_1' => 'Approved By',
                            'value_1' => $result['user_info'][$result['user_approved']]['name'],
                            'label_2' => 'Approval Time',
                            'value_2' => System_helper::display_date_time($result['date_approved'])
                        );
                    }
                }
            }

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

            $data['title'] = "Approve Field Day Payment ( ID:" . $result['budget_id'] . " )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/approve", $data, true));
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
        if ($item['status_payment_approve'] == '')
        {
            $ajax['status'] = false;
            $ajax['system_message'] = ($this->lang->line('LABEL_APPROVE')) . ' field is required.';
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
        $ajax = Fd_budget_helper::fd_budget_status_check($result, array(FD_BUDGET_APPROVED, FD_PAYMENT_NOT_APPROVED));
        if (!$ajax['status'])
        {
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        $item['date_payment_approved'] = $time;
        $item['user_payment_approved'] = $user->user_id;
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
            $data = array();

            $this->db->from($this->config->item('table_ems_fd_budget') . ' fd_budget');
            $this->db->select('fd_budget.date_proposal,
                                fd_budget.participant_total,
                                fd_budget.amount_budget_total,
                                fd_budget.status,
                                fd_budget.date_created budget_date_created,
                                fd_budget.user_created budget_user_created,
                                fd_budget.remarks_delete,
                                fd_budget.date_deleted,
                                fd_budget.user_deleted,
                                fd_budget.status_budget_forward,
                                fd_budget.remarks_budget_forward,
                                fd_budget.date_budget_forwarded,
                                fd_budget.user_budget_forwarded,
                                fd_budget.status_recommendation,
                                fd_budget.remarks_recommendation,
                                fd_budget.date_recommendation,
                                fd_budget.user_recommendation,
                                fd_budget.status_approve,
                                fd_budget.remarks_approve,
                                fd_budget.date_approved,
                                fd_budget.user_approved,
                                fd_budget.status_payment_approve,
                                fd_budget.remarks_payment_approve,
                                fd_budget.date_payment_approved,
                                fd_budget.user_payment_approved,
                                fd_budget.status_payment_pay,
                                fd_budget.remarks_payment_pay,
                                fd_budget.date_payment_paid,
                                fd_budget.user_payment_paid');

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
            $data['item'] = $result;
            $data['info_basic'] = array();

            //---------Getting User Names------------
            $user_ids = array(
                $result['budget_user_created'] => $result['budget_user_created'],
                $result['user_deleted'] => $result['user_deleted'],
                $result['user_budget_forwarded'] => $result['user_budget_forwarded'],
                $result['user_recommendation'] => $result['user_recommendation'],
                $result['user_approved'] => $result['user_approved'],
                $result['user_payment_approved'] => $result['user_payment_approved'],
                $result['user_payment_paid'] => $result['user_payment_paid']
            );
            $result['user_info'] = System_helper::get_users_info($user_ids);

            //----------------Basic Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CREATED_BY'),
                'value_1' => $result['user_info'][$result['user_created']]['name'],
                'label_2' => $this->lang->line('LABEL_CREATED_TIME'),
                'value_2' => System_helper::display_date($result['date_created'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DATE_PROPOSAL'),
                'value_1' => System_helper::display_date($result['date_proposal']),
                'label_2' => $this->lang->line('LABEL_DATE_EXPECTED'),
                'value_2' => System_helper::display_date($result['date_expected'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_CROP_NAME'),
                'value_1' => $result['crop_name'],
                'label_2' => $this->lang->line('LABEL_CROP_TYPE'),
                'value_2' => $result['crop_type_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_VARIETY1_NAME'),
                'value_1' => $result['variety1_name'],
                'label_2' => $this->lang->line('LABEL_VARIETY2_NAME'),
                'value_2' => ($result['variety2_name']) ? $result['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_PRESENT_CONDITION'),
                'value_1' => nl2br($result['present_condition']),
                'label_2' => $this->lang->line('LABEL_DEALERS_EVALUATION'),
                'value_2' => nl2br($result['farmers_evaluation'])
            );
            //----------------Location Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Location'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ADDRESS'),
                'value_1' => nl2br($result['address'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_OUTLET_NAME'),
                'value_1' => $result['outlet_name'],
                'label_2' => $this->lang->line('LABEL_GROWING_AREA'),
                'value_2' => ($result['growing_area_name']) ? $result['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_DISTRICT_NAME'),
                'value_1' => $result['district_name'],
                'label_2' => $this->lang->line('LABEL_TERRITORY_NAME'),
                'value_2' => $result['territory_name']
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ZONE_NAME'),
                'value_1' => $result['zone_name'],
                'label_2' => $this->lang->line('LABEL_DIVISION_NAME'),
                'value_2' => $result['division_name']
            );
            //----------------Market Size Info. Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_MARKET_SIZE_TITLE')
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_TOTAL_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_total']),
                'label_2' => $this->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_total'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_ARM_MARKET_SIZE'),
                'value_1' => System_helper::get_string_kg($result['quantity_market_size_showroom_arm']),
                'label_2' => $this->lang->line('LABEL_ARM_GA_MARKET_SIZE'),
                'value_2' => System_helper::get_string_kg($result['quantity_market_size_ga_arm'])
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_NEXT_SALES_TARGET'),
                'value_1' => System_helper::get_string_kg($result['quantity_sales_target'])
            );
            //----------------Status Info Array Generate----------------
            $data['info_basic'][] = array(
                'label_1' => 'Budget Status'
            );
            $data['info_basic'][] = array(
                'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_FORWARD'),
                'value_1' => $result['status_budget_forward'],
                'label_2' => 'Budget ' . $this->lang->line('LABEL_REMARKS'),
                'value_2' => ($result['remarks_budget_forward']) ? nl2br($result['remarks_budget_forward']) : '-'
            );
            if ($result['user_budget_forwarded'] > 0)
            {
                $data['info_basic'][] = array(
                    'label_1' => 'Forwarded By',
                    'value_1' => $result['user_info'][$result['user_budget_forwarded']]['name'],
                    'label_2' => 'Forward Time',
                    'value_2' => System_helper::display_date_time($result['date_budget_forwarded'])
                );

                $data['info_basic'][] = array(
                    'label_1' => $this->lang->line('LABEL_STATUS_RECOMMENDATION'),
                    'value_1' => $result['status_recommendation'],
                    'label_2' => 'Recommendation ' . $this->lang->line('LABEL_REMARKS'),
                    'value_2' => ($result['remarks_recommendation']) ? nl2br($result['remarks_recommendation']) : '-'
                );
                if ($result['user_recommendation'] > 0)
                {
                    $data['info_basic'][] = array(
                        'label_1' => 'Recommended By',
                        'value_1' => $result['user_info'][$result['user_recommendation']]['name'],
                        'label_2' => 'Recommendation Time',
                        'value_2' => System_helper::display_date_time($result['date_recommendation'])
                    );

                    $data['info_basic'][] = array(
                        'label_1' => $this->lang->line('LABEL_STATUS_BUDGET_APPROVE'),
                        'value_1' => $result['status_approve'],
                        'label_2' => 'Approval ' . $this->lang->line('LABEL_REMARKS'),
                        'value_2' => ($result['remarks_approve']) ? nl2br($result['remarks_approve']) : '-'
                    );
                    if ($result['user_approved'] > 0)
                    {
                        $data['info_basic'][] = array(
                            'label_1' => 'Approved By',
                            'value_1' => $result['user_info'][$result['user_approved']]['name'],
                            'label_2' => 'Approval Time',
                            'value_2' => System_helper::display_date_time($result['date_approved'])
                        );

                        //----------------Payment Status Array Generate----------------
                        $data['info_basic'][] = array(
                            'label_1' => 'Payment Status'
                        );
                        $data['info_basic'][] = array(
                            'label_1' => $this->lang->line('LABEL_STATUS_PAYMENT_APPROVE'),
                            'value_1' => $result['status_payment_approve'],
                            'label_2' => 'Payment Approval ' . $this->lang->line('LABEL_REMARKS'),
                            'value_2' => ($result['remarks_payment_approve']) ? nl2br($result['remarks_payment_approve']) : '-'
                        );
                        if ($result['user_payment_approved'] > 0)
                        {
                            $data['info_basic'][] = array(
                                'label_1' => 'Payment Approved By',
                                'value_1' => $result['user_info'][$result['user_payment_approved']]['name'],
                                'label_2' => 'Payment Approval Time',
                                'value_2' => System_helper::display_date_time($result['date_payment_approved'])
                            );

                            $data['info_basic'][] = array(
                                'label_1' => $this->lang->line('LABEL_STATUS_PAYMENT_PAY'),
                                'value_1' => $result['status_payment_pay'],
                                'label_2' => 'Payment Paid ' . $this->lang->line('LABEL_REMARKS'),
                                'value_2' => ($result['remarks_payment_pay']) ? nl2br($result['remarks_payment_pay']) : '-'
                            );
                            if ($result['user_payment_paid'] > 0)
                            {
                                $data['info_basic'][] = array(
                                    'label_1' => 'Payment Paid By',
                                    'value_1' => $result['user_info'][$result['user_payment_paid']]['name'],
                                    'label_2' => 'Payment Pay Time',
                                    'value_2' => System_helper::display_date_time($result['date_payment_paid'])
                                );
                            }
                        }
                    }
                }
            }
            //------------------Participants Array Generate-------------------
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
            //------------------Expense Array Generate-------------------
            $data['expense_items'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_expense_items'), array('id', 'name', 'status'), array('status !="' . $this->config->item('system_status_inactive') . '"'), 0, 0, array('ordering ASC'));
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
            }
            //------------------Uploaded Image Array Generate-------------------
            $picture_data = Query_helper::get_info($this->config->item('table_ems_fd_budget_details_picture'), '*', array('budget_id =' . $item_id, 'revision=1', 'status !="' . $this->config->item('system_status_delete') . '"'));
            $data['image_details'] = array();
            foreach ($picture_data as $picture)
            {
                $data['image_details'][$picture['category_id']] = $picture;
            }
            $data['picture_categories'] = Query_helper::get_info($this->config->item('table_ems_setup_fd_picture_category'), array('id value', 'name text', 'status'), array('status="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
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

            $data['title'] = 'Field Day Budget Details ( ID: ' . $item_id . ' )';
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
}
