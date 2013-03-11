<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Page model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Page extends baseModel
{
    var $log_file = 'model_page.log';
    var $model_table = PAGES_DB_TABLE;
    var $insert_required_fields = array('lang_id','name','url_alias');
    var $relative_field_name = 'parent_id';

    function __construct()
    {
        parent::__construct();
    }

    /**
    *	Add new Page
    *
    * @param	Array
    */

    function add($params)
    {
        (array)$params;
        $insert = array();

        $insert['lang_id'] = (array_key_exists('lang_id',$params) && !empty($params['lang_id'])) ? $params['lang_id'] : NULL;
        $insert['parent_id'] = (array_key_exists('parent_id',$params) && !empty($params['parent_id'])) ? $params['parent_id'] : NULL;
        $insert['name'] = (array_key_exists('name',$params) && !empty($params['name'])) ? $params['name'] : NULL;
        $insert['url_alias'] = (array_key_exists('url_alias',$params) && !empty($params['url_alias'])) ? $params['url_alias'] : NULL;
        $insert['description'] = (array_key_exists('description',$params)) ? $params['description'] : NULL;
        $insert['content'] = (array_key_exists('content',$params)) ? $params['content'] : NULL;
        $insert['isMenu'] = (array_key_exists('isMenu',$params) && $this->isMenuStatus($params['isMenu'])) ? $params['isMenu'] : 0;
        $insert['seo_title'] = (array_key_exists('seo_title',$params)) ? $params['seo_title'] : NULL;
        $insert['seo_description'] = (array_key_exists('seo_description',$params)) ? $params['seo_description'] : NULL;
        $insert['seo_keywords'] = (array_key_exists('seo_keywords',$params)) ? $params['seo_keywords'] : NULL;
        $insert['sort_order'] = (array_key_exists('sort_order',$params)) ? $params['sort_order'] : 0;
        $insert['enabled'] = (array_key_exists('enabled',$params) && $this->isStatus($params['enabled'])) ? $params['enabled'] : STATUS_ENABLED;

        if(!$this->not_empty($insert,$this->insert_required_fields))
        {
            $params['desc'] = "Can not create Page. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isLangExists($insert['lang_id']))
        {
            $params['desc'] = "Can not create Page. Language not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_language_not_exists',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isUrlUnique($insert['url_alias'],$insert['lang_id']))
        {
            $params['desc'] = "Can not create Page. News url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!empty($insert['parent_id']) && !$this->isParentExists($insert['parent_id'],$insert['lang_id']))
        {
            $params['desc'] = "Can not create Page. Parent page not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_parent_item_not_exists',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $fix_sort_order = $insert['sort_order'];
		$insert['sort_order'] = $this->getLastSortOrder($insert['lang_id'],$insert['parent_id']) + 1;

        if(!is_null($insert['parent_id']))
        {
            $parent_enabled = $this->get($insert['parent_id'],array('enabled'));

            if($parent_enabled['enabled'] == STATUS_DISABLED)
                $insert['enabled'] = STATUS_DISABLED;
        }

        $this->db->insert($this->model_table,$insert);

        $id = $this->db->insert_id();

        if(!empty($id) && $fix_sort_order != $insert['sort_order'])
		    $this->SetSortOrder($id,$fix_sort_order,$insert['parent_id']);

        if($this->db->_error_message() || !$id)
        {
            $params['desc'] = "Can not create Page. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }

    /**
    *	Update Page
    *
    * @param	Int
    * @param	Array
    */

    function update($id,$params)
    {
        (array)$params;
        $update = array();

        if(array_key_exists('parent_id',$params))
            $update['parent_id'] = (!empty($params['parent_id'])) ? $params['parent_id'] : NULL;

        if(array_key_exists('name',$params) && !empty($params['name']))
            $update['name'] = $params['name'];

        if(array_key_exists('url_alias',$params) && !empty($params['url_alias']))
            $update['url_alias'] = $params['url_alias'];

        if(array_key_exists('description',$params))
            $update['description'] = $params['description'];

        if(array_key_exists('content',$params))
            $update['content'] = $params['content'];

        if(array_key_exists('isMenu',$params) && $this->isMenuStatus($params['isMenu']))
            $update['isMenu'] = $params['isMenu'];

        if(array_key_exists('seo_title',$params))
            $update['seo_title'] = $params['seo_title'];

        if(array_key_exists('seo_description',$params))
            $update['seo_description'] = $params['seo_description'];

         if(array_key_exists('seo_keywords',$params))
            $update['seo_keywords'] = $params['seo_keywords'];

        if(array_key_exists('sort_order',$params))
            $update['sort_order'] = $params['sort_order'];

        if(array_key_exists('enabled',$params) && $this->isStatus($params['enabled']))
            $update['enabled'] = $params['enabled'];

        if(!count($update) || !$id)
        {
            $params['desc'] = "Can not update Page. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $info = $this->get($id);

        if(!$info)
        {
            $params['desc'] = "Can not update Page. Page does not exist";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_item_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('url_alias', $update) && !$this->isUrlUnique($update['url_alias'],$info['lang_id'],$id))
        {
            $params['desc'] = "Can not update News. Url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('parent_id',$update) && !$this->isParentExists($update['parent_id'],$info['lang_id']))
        {
            $params['desc'] = "Can not update Page. Parent page not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_parent_item_not_exists',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('parent_id',$update) && !$this->validateParent($id,$update['parent_id']))
        {
            $params['desc'] = "Can not update Page. Parent page not valid";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_parent_item_not_valid',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('parent_id',$update) && $update['parent_id'] != $info['parent_id'])
        {
            $update['sort_order'] = $this->getLastSortOrder($info['lang_id'],$update['parent_id']) + 1;
        }

        $parent_id = (array_key_exists('parent_id',$update)) ? $update['parent_id'] : $info['parent_id'];

        if(array_key_exists('enabled',$update))
        {
            $parent_info = $this->get($parent_id);

            if(!empty($parent_info) && $parent_info['enabled'] != $update['enabled'] && $parent_info['enabled'] == STATUS_DISABLED)
            {
                $update['enabled'] = STATUS_DISABLED;
            }
        }

        if(array_key_exists('sort_order',$update))
        {
            $sort_order = $update['sort_order'];

            if($info['sort_order'] != $sort_order)
                $update['sort_order'] = $this->getLastSortOrder($info['lang_id'],$parent_id) + 1;
        }

        $this->db->where('id',(int)$id);
        $this->db->update($this->model_table,$update);

        if(isset($sort_order))
        {
            if($info['sort_order'] != $sort_order)
                $this->SetSortOrder($id,$sort_order,$parent_id);
        }

        if($this->db->_error_message())
        {
            $params['desc'] = "Can not update Page. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        if(array_key_exists('enabled',$update))
        {
            $childNodes = $this->getTreeBranch($id);

            if(is_array($childNodes) && count($childNodes))
            {
                foreach($childNodes as $cn)
                {
                    $this->db->where('id',$cn['id'])->update($this->model_table,array('enabled' => $update['enabled']));
                }
            }
        }
        
        return TRUE;
    }

    function isMenuStatus($status)
    {
        if($status == 1 || $status == 0)
        {
            return TRUE;
        }

        return FALSE;
    }
}

?>

