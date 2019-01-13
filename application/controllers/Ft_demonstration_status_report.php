<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ft_demonstration_status_report extends Root_Controller
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
        $this->locations = User_helper::get_locations();
        $this->common_view_location = 'Ft_demonstration_status';
        if (!($this->locations))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->load->helper('Ft_demonstration');
        $this->lang->load('Ft_demonstration');
    }

    public function index($action = "search", $id = 0)
    {
        if ($action == "search")
        {
            $this->system_search();
        }
        elseif ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "details")
        {
            $this->system_details($id);
        }
        elseif ($action == "get_growing_area")
        {
            $this->system_get_growing_area($id);
        }
        elseif ($action == "set_preference_search_list")
        {
            $this->system_set_preference('search_list');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }

    private function get_preference_headers($method)
    {
        $data = array(); // initialize
        $data['id'] = 1;
        $data['no_of_images'] = 1;
        $data['no_of_videos'] = 1;
        $data['year'] = 1;
        $data['season'] = 1;

        $data['division_name'] = 1;
        $data['zone_name'] = 1;
        $data['territory_name'] = 1;
        $data['district_name'] = 1;
        $data['outlet_name'] = 1;
        $data['growing_area'] = 1;

        $data['crop_name'] = 1;
        $data['lead_farmer_name'] = 1;
        $data['date_sowing_variety1'] = 1;
        $data['date_sowing_variety2'] = 1;
        $data['date_transplanting_variety1'] = 1;
        $data['date_transplanting_variety2'] = 1;
        $data['date_expected_evaluation'] = 1;
        $data['date_actual_evaluation'] = 1;
        $data['zsc_evaluation'] = 1;
        $data['zsc_status'] = 1;
        $data['details_button'] = 1;
        return $data;
    }

    private function system_set_preference($method = 'search')
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

    private function system_search()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['divisions'] = Query_helper::get_info($this->config->item('table_login_setup_location_divisions'), array('id value', 'name text'), array('status !="' . $this->config->item('system_status_delete') . '"'));
            $data['seasons'] = Query_helper::get_info($this->config->item('table_ems_setup_seasons'), array('id value', 'name text'), array('status ="' . $this->config->item('system_status_active') . '"'));

            $data['title'] = "Demonstration Report Search";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/search", $data, true));
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

    private function system_list()
    {
        $user = User_helper::get_user();
        $method = 'search_list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data = array();
            $data['item'] = $this->input->post('item');

            $data['title'] = "Demonstration Report";
            $ajax['status'] = true;
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $ajax['system_content'][] = array("id" => "#system_report_container", "html" => $this->load->view($this->controller_url . "/list", $data, true));

            $ajax['status'] = true;
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
        $item = $this->input->post('item');

        $this->db->from($this->config->item('table_ems_demonstration_status') . ' demonstration');
        $this->db->select('demonstration.*, demonstration.id');

        $this->db->join($this->config->item('table_ems_setup_seasons') . ' season', 'season.id = demonstration.season_id', 'INNER');
        $this->db->select('season.name season');

        $this->db->join($this->config->item('table_login_csetup_cus_info') . ' cus_info', 'cus_info.customer_id = demonstration.outlet_id AND cus_info.revision=1', 'INNER');
        $this->db->select('cus_info.name outlet_name');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_areas') . ' areas', 'areas.id = demonstration.growing_area_id', 'INNER');
        $this->db->select('areas.name growing_area');

        $this->db->join($this->config->item('table_ems_da_tmpo_setup_area_lead_farmers') . ' lead_farmers', 'lead_farmers.id = demonstration.lead_farmer_id', 'LEFT');
        $this->db->select('IF( (demonstration.lead_farmer_id > 0), lead_farmers.name, CONCAT(demonstration.name_other_farmer, " (Other)") ) AS lead_farmer_name');

        $this->db->join($this->config->item('table_login_setup_classification_crops') . ' crop', 'crop.id = demonstration.crop_id', 'INNER');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_login_setup_location_districts') . ' district', 'district.id = cus_info.district_id', 'INNER');
        $this->db->select('district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories') . ' territory', 'territory.id = district.territory_id', 'INNER');
        $this->db->select('territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_location_zones') . ' zone', 'zone.id = territory.zone_id', 'INNER');
        $this->db->select('zone.name zone_name');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = zone.division_id', 'INNER');
        $this->db->select('division.name division_name');

        $this->db->where('demonstration.status !=', $this->config->item('system_status_delete'));
        $this->db->where('demonstration.status_recommendation !=', $this->config->item('system_status_pending'));
        // Search Conditions
        if ($item['year'])
        {
            $this->db->where('demonstration.year', $item['year']);
        }
        if ($item['season_id'] > 0)
        {
            $this->db->where('demonstration.season_id', $item['season_id']);
        }
        if ($item['crop_id'])
        {
            $this->db->where('demonstration.crop_id', $item['crop_id']);
        }
        if ($item['date_type'])
        {
            if ($item['start_date'])
            {
                $this->db->where($item['date_type'] . ' >=', System_helper::get_time($item['start_date']));
            }
            if ($item['end_date'])
            {
                $this->db->where($item['date_type'] . ' <=', System_helper::get_time($item['end_date']));
            }
        }
        if ($item['division_id'] > 0)
        {
            $this->db->where('division.id', $item['division_id']);
            if ($item['zone_id'])
            {
                $this->db->where('zone.id', $item['zone_id']);
                if ($item['territory_id'])
                {
                    $this->db->where('territory.id', $item['territory_id']);
                    if ($item['district_id'])
                    {
                        $this->db->where('district.id', $item['district_id']);
                        if ($item['outlet_id'])
                        {
                            $this->db->where('demonstration.outlet_id', $item['outlet_id']);
                            if ($item['growing_area_id'])
                            {
                                $this->db->where('demonstration.growing_area_id', $item['growing_area_id']);
                            }
                        }
                    }
                }
            }
        }
        $this->db->order_by('demonstration.id', 'DESC');
        $items = $this->db->get()->result_array();

        // Image & Video count
        $file_data = Query_helper::get_info($this->config->item('table_ems_demonstration_status_image_video'), array('*'), array('status ="' . $this->config->item('system_status_active') . '"'));
        $file_count = array();
        foreach ($file_data as $file)
        {
            $uploaded_file_count = (($file['date_uploaded_variety1'] > 0) && ($file['date_uploaded_variety2'] > 0)) ? 2 : 1;
            $file_count[$file['demonstration_id']][$file['file_type']] = (isset($file_count[$file['demonstration_id']][$file['file_type']])) ? $file_count[$file['demonstration_id']][$file['file_type']] + $uploaded_file_count : $uploaded_file_count;
        }

        foreach ($items as &$item)
        {
            $item['no_of_images'] = isset($file_count[$item['id']][$this->config->item('system_file_type_image')]) ? $file_count[$item['id']][$this->config->item('system_file_type_image')] : 0;
            $item['no_of_videos'] = isset($file_count[$item['id']][$this->config->item('system_file_type_video')]) ? $file_count[$item['id']][$this->config->item('system_file_type_video')] : 0;
            $item['date_sowing_variety1'] = System_helper::display_date($item['date_sowing_variety1']);
            $item['date_sowing_variety2'] = System_helper::display_date($item['date_sowing_variety2']);
            $item['date_transplanting_variety1'] = System_helper::display_date($item['date_transplanting_variety1']);
            $item['date_transplanting_variety2'] = System_helper::display_date($item['date_transplanting_variety2']);
            $item['date_expected_evaluation'] = System_helper::display_date($item['date_expected_evaluation']);
            $item['date_actual_evaluation'] = System_helper::display_date($item['date_actual_evaluation']);
            $item['zsc_evaluation'] = $item['evaluation'];
            $item['zsc_status'] = $item['status_recommendation'];
        }
        $this->json_return($items);
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
            $html_container_id = $this->input->post('html_container_id');
            $result = Ft_demonstration_helper::get_demonstration_by_id($item_id, __FUNCTION__);

            $method = 'search';
            $data = array();
            $data['item'] = $result;
            $data['accordion'] = array('collapse' => 'in');
            $data['info_basic'] = Ft_demonstration_helper::get_details_info($result);

            // Image & Video data
            $result_file = Query_helper::get_info($this->config->item('table_ems_demonstration_status_image_video'), array('*'), array('demonstration_id =' . $item_id, 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('file_type'));
            $data['info_image'] = array();
            foreach ($result_file as $key => $file)
            {
                $data['info_image'][$file['file_type']][$key]['file_location_variety1'] = $file['file_location_variety1'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety1'] = $file['remarks_variety1'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety1'] = $file['date_uploaded_variety1'];

                $data['info_image'][$file['file_type']][$key]['file_location_variety2'] = $file['file_location_variety2'];
                $data['info_image'][$file['file_type']][$key]['remarks_variety2'] = $file['remarks_variety2'];
                $data['info_image'][$file['file_type']][$key]['date_uploaded_variety2'] = $file['date_uploaded_variety2'];
            }

            $data['title'] = "Demonstration Status Details ( ID:" . $item_id . " )";
            $data['no_details_menu'] = true;

            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view($this->common_view_location . "/details", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/" . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_growing_area($id = 0)
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
        $html_container_id = $this->input->post('html_container_id');

        $condition = array('status !="' . $this->config->item('system_status_delete') . '"');
        if ($item_id > 0)
        {
            $condition[] = 'outlet_id =' . $item_id;
        }
        $data['items'] = Query_helper::get_info($this->config->item('table_ems_da_tmpo_setup_areas'), 'id value, CONCAT_WS(" - ", name, address) text', $condition, 0, 0, array('name'));
        if ($data['items'])
        {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $html_container_id, "html" => $this->load->view("dropdown_with_select", $data, true));
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("SET_LEADING_FARMER_AND_DEALER");
            $this->json_return($ajax);
        }
    }
}
