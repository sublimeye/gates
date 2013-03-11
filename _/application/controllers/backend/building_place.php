<?php defined('BASEPATH') or die('No direct script access allowed');

class Building_place extends MY_Controller
{
    var $auth_only = TRUE;
    var $enabled_sort_fields = array('id','name');

    public function get_list()
	{
        $this->createHeader();
        $this->load->model('town');

        $page = (!is_null($this->getVarUrl(2,'page'))) ? (int)$this->getVarUrl(2,'page') : 0;
        $sort_fields = (!is_null($this->getVarUrl(2,'sort'))) ? $this->getVarUrl(2,'sort') : NULL;
        $sort_dir = (!is_null($this->getVarUrl(2,'type'))) ? $this->getVarUrl(2,'type') : NULL;

        $params['sort_field'] = (in_array($sort_fields,$this->enabled_sort_fields)) ? $sort_fields : 'name';
		$params['sort_dir'] = ($sort_dir == 'asc' || $sort_dir == 'desc') ? $sort_dir : 'desc';
		$params['page'] = $page;
		$params['limit'] = $this->limit;
        $params['fields'] = array('id','name');
        $params['where'] = array('lang_id' => $this->language_id);

        $towns = $this->town->getList($params);
      
        $this->page_data['towns'] = $towns['items'];
        $this->page_data['sort'] = $params['sort_field'];
		$this->page_data['sort_type'] = $params['sort_dir'];
		$this->page_data['sort_type_next'] = ($params['sort_dir'] == 'asc') ? 'desc' : 'asc';
		$this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/building_place/building_place',$this->page_data);
        $this->createFooter($towns['total']);
	}

    public function edit()
    {
        $this->createHeader();

        $id = (int)$this->getVarUrl(2,'id');

        $this->load->model('town');

        $town = $this->town->get($id);
        $this->page_data = $town;

        if(!empty($town))
        {
            $this->load->model('building_places');
            $building_places = $this->building_places->getBuildingPlaces($id);

            if(is_array($building_places))
            {
                $this->load->model('building_place_images');

                $params['fields'] = array('id','img');
                $params['sort_field'] = 'id';
                $params['sort_dir'] = 'desc';

                foreach($building_places as $i=>$bpi)
                {
                    $building_places[$i]['params'] = unserialize($bpi['params']);

                    $params['where'] = array('building_place_id' => $bpi['id']);

                    $building_place_images = $this->building_place_images->getList($params);
                    $building_places[$i]['images'] = $building_place_images['items'];
                }
            }

            $this->load->model('building');

            $params['fields'] = array(BUILDING_DB_TABLE.'.id',BUILDING_DB_TABLE.'.name');
            $params['sort_field'] = 'name';
            $params['sort_dir'] = 'asc';
            $params['language_id'] = $this->language_id;
            $params['where'] = array('town_id' => $id);

            $buildings = $this->building->getList($params);

            $this->page_data['building_places'] = json_encode($building_places);
            $this->page_data['buildings'] = $buildings;
            $this->page_data['url'] = $this->getModuleUrl()."/get_list";
            $this->page_data['url_action_add_image'] = $this->getModuleUrl()."/action_add_image";
            $this->page_data['url_action_delete_image'] =  $this->getModuleUrl()."/action_delete_image";

            $this->load->view('backend/building_place/building_place_form',$this->page_data);
            $this->createFooter(0,TRUE);
        }
        else
        {
            show_404();
        }
    }

    public function action_add()
    {
        $params['building_id'] = $this->input->post('building_id');
        $params['enabled'] = $this->input->post('enabled');
        $params['availability'] = $this->input->post('availability');
        $params['state'] = $this->input->post('state');
        $params['interior'] = $this->input->post('interior');
        $params['params'] = serialize($this->input->post('points'));

        $this->load->model('building_places');

        $id = $this->building_places->add($params);

        echo json_encode($id);
    }

    public function action_update()
    {
        $id = $this->input->post('id');
        
        $params['building_id'] = $this->input->post('building_id');
        $params['enabled'] = $this->input->post('enabled');
        $params['availability'] = $this->input->post('availability');
        $params['state'] = $this->input->post('state');
        $params['interior'] = $this->input->post('interior');
        $params['params'] = serialize($this->input->post('points'));

        $this->load->model('building_places');

        $id = $this->building_places->update($id,$params);

        echo json_encode($id);
    }

    public function action_delete()
    {
        $id = $this->input->post('id');

        $this->load->model('building_places');
        $result = $this->building_places->delete($id);

        echo json_encode($result);
    }

    public function action_add_image()
    {
        $building_place_id = $this->input->post('building_place_id');

        $this->load->model('building_place_images');

        $this->building_place_images->add($building_place_id);

        if($this->building_place_images->is_errors())
		{
			$errors = $this->building_place_images->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->building_place_images->is_warnings())
		{
			$warnings = $this->building_place_images->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirectReferrer();
    }

    public function action_delete_image()
    {
        $id = $this->getVarUrl(2,'id');

        $this->load->model('building_place_images');

        $this->building_place_images->delete($id);

        if($this->building_place_images->is_errors())
		{
			$errors = $this->building_place_images->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->building_place_images->is_warnings())
		{
			$warnings = $this->building_place_images->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirectReferrer();
    }
}

?>
