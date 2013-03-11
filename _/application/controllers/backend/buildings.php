<?php defined('BASEPATH') or die('No direct script access allowed');

class Buildings extends MY_Controller
{
    var $auth_only = TRUE;
    var $enabled_sort_fields = array('id','name','square');

    public function get_list()
	{
        $this->createHeader();
        $this->load->model('building');

        $page = (!is_null($this->getVarUrl(2,'page'))) ? (int)$this->getVarUrl(2,'page') : 0;
        $sort_fields = (!is_null($this->getVarUrl(2,'sort'))) ? $this->getVarUrl(2,'sort') : NULL;
        $sort_dir = (!is_null($this->getVarUrl(2,'type'))) ? $this->getVarUrl(2,'type') : NULL;

        $params['sort_field'] = (in_array($sort_fields,$this->enabled_sort_fields)) ? $sort_fields : 'name';
		$params['sort_dir'] = ($sort_dir == 'asc' || $sort_dir == 'desc') ? $sort_dir : 'desc';
		$params['page'] = $page;
		$params['limit'] = $this->limit;
        $params['fields'] = array(BUILDING_DB_TABLE.'.id',BUILDING_DB_TABLE.'.name',BUILDING_DB_TABLE.'.square');
        $params['language_id'] = $this->language_id;

        $buildings = $this->building->getList($params);
      
        $this->page_data['buildings'] = $buildings['items'];
        $this->page_data['sort'] = $params['sort_field'];
		$this->page_data['sort_type'] = $params['sort_dir'];
		$this->page_data['sort_type_next'] = ($params['sort_dir'] == 'asc') ? 'desc' : 'asc';
		$this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/buildings/buildings',$this->page_data);
        $this->createFooter($buildings['total']);
	}

    public function add()
    {
        $this->createHeader();
        $this->load->model('building');

        $add_data = $this->session->flashdata('add_data');

        if(is_array($add_data) && count($add_data))
			$this->page_data = $add_data;

        $towns = $this->building->getTowns($this->language_id);

        $this->page_data['towns'] = $towns;
        $this->page_data['url_action'] = $this->getModuleUrl()."/action_add";
        $this->page_data['url'] = $this->getModuleUrl()."/get_list";

        $this->load->view('backend/buildings/buildings_form',$this->page_data);
        $this->createFooter(0,TRUE);

    }

    public function edit()
    {
        $this->createHeader();

        $id = (int)$this->getVarUrl(2,'id');
        $tab = (int)$this->getVarUrl(2,'tab');

        $this->load->model('building');

        $building = $this->building->get($id);
        $this->page_data = $building;

        if(!empty($building))
        {
            if($tab == 0)
            {
                $add_data = $this->session->flashdata('add_data');

                if(is_array($add_data) && count($add_data))
                    $this->page_data = $add_data;

                 $towns = $this->building->getTowns($this->language_id);

                $this->page_data['towns'] = $towns;
                $this->page_data['url_action'] = $this->getModuleUrl()."/action_edit/id/".$id;
                $this->page_data['url'] = $this->getModuleUrl()."/get_list";
                $this->page_data['action'] = "edit";
                $this->page_data['active_tab'] = $tab;

                $this->load->view('backend/buildings/buildings_form',$this->page_data);
                $this->createFooter(0,TRUE);
            }

            if($tab == 1)
            {
                $this->load->model('building_plans');

                $params['fields'] = array('id','name','description','sort_order','img');
                $params['sort_field'] = 'sort_order';
                $params['sort_dir'] = 'asc';
                $params['where'] = array('building_id' => $id);

                $building_plans = $this->building_plans->getList($params);

                $this->page_data['building_plans_items'] = $building_plans['items'];
                $this->page_data['url_action'] = $this->getModuleUrl()."/action_add_building_plans/id/".$id;
                $this->page_data['url'] = $this->getModuleUrl()."/get_list";
                $this->page_data['action'] = "edit";
                $this->page_data['active_tab'] = $tab;

                $this->load->view('backend/buildings/buildings_plans_form',$this->page_data);
                $this->createFooter(0,TRUE);
            }

            if($tab == 2)
            {
                $this->load->model('building_images');

                $params['fields'] = array('id','img');
                $params['sort_field'] = 'id';
                $params['sort_dir'] = 'asc';
                $params['where'] = array('building_id' => $id);

                $building_images = $this->building_images->getList($params);

                $this->page_data['building_images_items'] = $building_images['items'];
                $this->page_data['url_action'] = $this->getModuleUrl()."/action_add_building_image/id/".$id;
                $this->page_data['url'] = $this->getModuleUrl()."/get_list";
                $this->page_data['action'] = "edit";
                $this->page_data['active_tab'] = $tab;

                $this->load->view('backend/buildings/buildings_images_form',$this->page_data);
                $this->createFooter(0,TRUE);
            }
        }
        else
        {
            show_404();
        }
    }

    public function action_add()
    {
        $this->load->library('form_validation');
		$this->load->model('building');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['town_id'] = $this->input->post('town_id',TRUE);
        $params['lang_id'] = $this->language_id;
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['square'] = $this->input->post('square',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
				$params['swf_embed'] = $this->input->post('swf_embed',TRUE);

		$this->session->set_flashdata('add_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->building->add($params);

		if($this->building->is_errors())
		{
			$errors = $this->building->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->building->is_warnings())
		{
			$warnings = $this->building->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/get_list");
    }

    public function action_edit()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->library('form_validation');
		$this->load->model('building');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['town_id'] = $this->input->post('town_id',TRUE);
        $params['lang_id'] = $this->language_id;
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['square'] = $this->input->post('square',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
				$params['swf_embed'] = $this->input->post('swf_embed',TRUE);

        $this->session->set_flashdata('edit_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->building->update($id,$params);

		if($this->building->is_errors())
		{
			$errors = $this->building->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->building->is_warnings())
		{
			$warnings = $this->building->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/edit/id/".$id);
    }

    function action_add_building_plans()
    {
        $id = (int)$this->getVarUrl(2,'id');

		$this->load->model('building_plans');

		$ids = $this->input->post('ids',TRUE);
        $names = $this->input->post('name',TRUE);
        $descriptions = $this->input->post('description',TRUE);
        $sort_order = $this->input->post('sort_order',TRUE);

		if(is_array($names) && count($names) && count($descriptions) == count($names) && count($sort_order) == count($names) &&
            count($ids) == count($names))
        {
            $files = $_FILES;

            foreach($names as $k => $n)
            {
                $_FILES = $files;

                if(array_key_exists('img',$_FILES) && array_key_exists($k,$_FILES['img']['name']))
                {
                    $_FILES['img']['name'] = $_FILES['img']['name'][$k];
                    $_FILES['img']['type'] = $_FILES['img']['type'][$k];
                    $_FILES['img']['tmp_name'] = $_FILES['img']['tmp_name'][$k];
                    $_FILES['img']['error'] = $_FILES['img']['error'][$k];
                    $_FILES['img']['size'] = $_FILES['img']['size'][$k];
                }
                else
                {
                    $_FILES = array();
                }

                $params['name'] = $n;
                $params['building_id'] = $id;
                $params['description'] = (array_key_exists($k,$descriptions)) ? $descriptions[$k] : NULL;
                $params['sort_order'] = (array_key_exists($k,$sort_order)) ? $sort_order[$k] : NULL;

                if(array_key_exists($k,$ids) && !empty($ids[$k]))
                {
                    $this->building_plans->update($ids[$k],$params);
                }
                else
                {
                    $this->building_plans->add($params);
                }

                if($this->building_plans->is_errors())
                {
                    $errors = $this->building_plans->get_errors();
                    $this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

                    $this->redirectReferrer();
                }

                if($this->building_plans->is_warnings())
                {
                    $warnings = $this->building_plans->get_warnings();
                    $this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
                }
            }
        }

		$this->redirectReferrer();
    }

    function action_add_building_image()
    {
        $id = (int)$this->getVarUrl(2,'id');
		$this->load->model('building_images');
 
        $this->building_images->add($id);

        if($this->building_images->is_errors())
        {
            $errors = $this->building_images->get_errors();
            $this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

            $this->redirectReferrer();
        }

        if($this->building_images->is_warnings())
        {
            $warnings = $this->building_images->get_warnings();
            $this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
        }
     
		$this->redirectReferrer();
    }

    function delete()
    {
        $this->load->model('building');
		$items_delete = $this->input->post('items');

		if(is_array($items_delete) && count($items_delete))
		{
			$this->building->delete($items_delete);
		}
		else
		{
			$params['desc'] = "Can not delete. Not selected items";
			$params['file'] = __FILE__;
			$params['line'] = __LINE__;
			$params['class'] = __CLASS__;
			$params['method'] = __METHOD__;

			$this->set_warning('warning_not_selected_item',$params);
		}

		if($this->building->is_errors())
		{
			$errors = $this->building->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->building->is_warnings())
		{
			$warnings = $this->building->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}
				
		$this->redirectReferrer();
    }

    function delete_building_plan()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->model('building_plans');

		if(!empty($id))
		{
			$this->building_plans->delete($id);
		}
		else
		{
			$params['desc'] = "Can not delete. Not selected items";
			$params['file'] = __FILE__;
			$params['line'] = __LINE__;
			$params['class'] = __CLASS__;
			$params['method'] = __METHOD__;

			$this->set_warning('warning_not_selected_item',$params);
		}

		if($this->building_plans->is_errors())
		{
			$errors = $this->building_plans->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->building_plans->is_warnings())
		{
			$warnings = $this->building_plans->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirectReferrer();
    }

    function delete_building_image()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->model('building_images');

		if(!empty($id))
		{
			$this->building_images->delete($id);
		}
		else
		{
			$params['desc'] = "Can not delete. Not selected items";
			$params['file'] = __FILE__;
			$params['line'] = __LINE__;
			$params['class'] = __CLASS__;
			$params['method'] = __METHOD__;

			$this->set_warning('warning_not_selected_item',$params);
		}

		if($this->building_images->is_errors())
		{
			$errors = $this->building_images->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->building_images->is_warnings())
		{
			$warnings = $this->building_images->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirectReferrer();
    }

    function file_delete()
    {
        $this->load->model('building');

		$id = $this->getVarUrl(2,'item');
		$file_field = $this->getVarUrl(2,'name');

		if((int)$id > 0 && !empty($file_field))
		{
			$this->building->delete_file($id,$file_field);

			if($this->building->is_errors())
			{
				$errors = $this->building->get_errors();
				$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
			}

			if($this->building->is_warnings())
			{
				$warnings = $this->building->get_warnings();
				$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
			}

			$this->redirectReferrer();
		}
		else
		{
			show_404();
		}
    }
}

?>
