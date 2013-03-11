<?php defined('BASEPATH') or die('No direct script access allowed');

class Pages extends MY_Controller
{
    var $auth_only = TRUE;

    public function get_list()
	{
        $this->createHeader();
        $this->load->model('page');

        $items = $this->page->getTree($this->language_id);
        $show_tree = $this->_build_tree($items,NULL);

        $this->page_data['items'] = $show_tree;

        $this->load->view('backend/pages/pages',$this->page_data);
        $this->createFooter(0,FALSE);
	}

    public function add()
    {
        $this->createHeader();
        $this->load->model('page');

        $id = $this->getVarUrl(2,'id');

        $add_data = $this->session->flashdata('add_data');

        $this->page_data['selected_id'] = (!empty($id)) ? $id : 0;
        $this->page_data['sort_order'] = 0;

        if(is_array($add_data) && count($add_data))
			$this->page_data = $add_data;

        if(array_key_exists('parent_id',$this->page_data))
			$this->page_data['selected_id'] = $this->page_data['parent_id'];

        $items = $this->page->getTree($this->language_id);
		$this->_build_select_tree($items,NULL);

        $this->page_data['url_action'] = $this->getModuleUrl()."/action_add";
        $this->page_data['url'] = $this->getModuleUrl()."/get_list";
        $this->page_data['pages_list'] = $items;

        $this->load->view('backend/pages/pages_form',$this->page_data);
        $this->createFooter(0,TRUE);

    }

    public function edit()
    {
        $this->createHeader();

        $id = (int)$this->getVarUrl(2,'id');
        $this->load->model('page');

        $page = $this->page->get($id);

        if(!empty($page))
        {
            $this->page_data = $page;
            $add_data = $this->session->flashdata('add_data');

            if(is_array($add_data) && count($add_data))
                $this->page_data = $add_data;

            $items = $this->page->getTree($this->language_id);
		    $this->_build_select_tree($items,NULL);

            $this->page_data['url_action'] = $this->getModuleUrl()."/action_edit/id/".$id;
            $this->page_data['url'] = $this->getModuleUrl()."/get_list";
            $this->page_data['selected_id'] = $this->page_data['parent_id'];
            $this->page_data['pages_list'] = $items;

            $this->load->view('backend/pages/pages_form',$this->page_data);
            $this->createFooter(0,TRUE);
        }
        else
        {
            show_404();
        }
    }

    public function action_add()
    {
        $this->load->library('form_validation');
		$this->load->model('page');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['parent_id'] = $this->input->post('parent_id',TRUE);
		$params['lang_id'] = $this->language_id;
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['isMenu'] = $this->input->post('isMenu',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
        $params['sort_order'] = $this->input->post('sort_order',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

		$this->session->set_flashdata('add_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->page->add($params);

		if($this->page->is_errors())
		{
			$errors = $this->page->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->page->is_warnings())
		{
			$warnings = $this->page->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/get_list");
    }

    public function action_edit()
    {
        $id = (int)$this->getVarUrl(2,'id');

        $this->load->library('form_validation');
		$this->load->model('page');

		$this->form_validation->set_rules('name','Имя','required|min_length[3]|max_length[200]');
        $this->form_validation->set_rules('url_alias','URL Alias','required|min_length[3]|max_length[200]');

		$params['name'] = $this->input->post('name',TRUE);
		$params['parent_id'] = $this->input->post('parent_id',TRUE);
        $params['url_alias'] = $this->input->post('url_alias',TRUE);
        $params['description'] = $this->input->post('description',TRUE);
        $params['content'] = $this->input->post('content',TRUE);
        $params['isMenu'] = $this->input->post('isMenu',TRUE);
        $params['seo_title'] = $this->input->post('seo_title',TRUE);
        $params['seo_description'] = $this->input->post('seo_description',TRUE);
        $params['seo_keywords'] = $this->input->post('seo_keywords',TRUE);
        $params['sort_order'] = $this->input->post('sort_order',TRUE);
		$params['enabled'] = $this->input->post('enabled',TRUE);

        $this->session->set_flashdata('edit_data',$params);

		if($this->form_validation->run() == FALSE)
		{
			$errors = validation_errors();
			$this->session->set_flashdata('errors',$errors);

			$this->redirectReferrer();
		}

		$this->page->update($id,$params);

		if($this->page->is_errors())
		{
			$errors = $this->page->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));

			$this->redirectReferrer();
		}

		if($this->page->is_warnings())
		{
			$warnings = $this->page->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}

		$this->redirect($this->getModuleUrl()."/edit/id/".$id);
    }

    function delete()
    {
        $this->load->model('page');
		$id = $this->getVarUrl(2,'id');
       
		if(!empty($id))
		{
			$this->page->delete($id);
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

		if($this->page->is_errors())
		{
			$errors = $this->page->get_errors();
			$this->session->set_flashdata('errors',$this->getDisplayMsg($errors));
		}

		if($this->page->is_warnings())
		{
			$warnings = $this->page->get_warnings();
			$this->session->set_flashdata('warnings',$this->getDisplayMsg($warnings));
		}
				
		$this->redirectReferrer();
    }

    private function _build_tree(&$items,$parent_id)
	{
		$result = "";

		if(is_array($items) && count($items))
		{
			foreach($items as $i)
			{
				if($parent_id == $i['parent_id'])
				{
					$class = array();

					$children = $this->_build_tree($items,$i['id']);
					$node='<li id="'.$i['id'].'">';

					if(!empty($children))
						$class[] = 'liclose';

					if($i['enabled'] == STATUS_DISABLED)
						$class[] = 'noactive';

					$node.='<span node="tree_node" class="'.implode(" ",$class).'" ><b class="numspan">'.$i['sort_order'].'</b>'.$i['name'];
					$node.=$children.'</span>';
					$node.='</li>';

					$result.= $node;
				}
			}

			if(!empty($result))
				$result = "<ul>".$result."</ul>";

			return $result;
		}

		return '';
	}

    private function _build_select_tree(&$items,$parent_id,$parent_prefix = "----")
	{
		if(is_array($items) && count($items))
		{
			foreach($items as $k => $i)
			{
				if($parent_id == $i['parent_id'])
				{
					$items[$k]['name'] = $parent_prefix.$i['name'];
					$this->_build_select_tree($items,$i['id'],$parent_prefix."----");
				}
			}
		}
	}
}

?>
