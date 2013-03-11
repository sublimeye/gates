<?php defined('BASEPATH') or die('No direct script access allowed');

class Towns extends MY_Controller
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

        $this->load->view('backend/towns/towns',$this->page_data);
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
            $add_data = $this->session->flashdata('add_data');

            if(is_array($add_data) && count($add_data))
                $this->page_data = $add_data;

            $this->page_data['town'] = $town;
            $this->page_data['url_action'] = $this->getModuleUrl()."/action_edit/id/".$id;
            $this->page_data['url'] = $this->getModuleUrl()."/get_list";
            $this->page_data['action'] = "edit";

            $this->load->view('backend/towns/towns_form',$this->page_data);
            $this->createFooter(0,TRUE);
        }
        else
        {
            show_404();
        }
    }

    public function action_edit()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->library('form_validation');
		$this->load->model('town');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['description'] = $this->input->post('description',TRUE);
        $params['map_code'] = $this->input->post('map_code',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);

        $this->session->set_flashdata('edit_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->town->update($id,$params);

		if($this->town->is_errors())
		{
			$errors = $this->town->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->town->is_warnings())
		{
			$warnings = $this->town->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/edit/id/".$id);
    }
     
}

?>
