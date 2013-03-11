<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
News model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class News extends baseModel
{
    var $log_file = 'model_news.log';
    var $model_table = NEWS_DB_TABLE;
    var $insert_required_fields = array('lang_id','name','url_alias');

    function __construct()
    {
        parent::__construct();
    }

    /**
    *	Add new News
    *
    * @param	Array
    */

    function add($params)
    {
        (array)$params;
        $insert = array();

        $insert['lang_id'] = (array_key_exists('lang_id',$params) && !empty($params['lang_id'])) ? $params['lang_id'] : NULL;
        $insert['name'] = (array_key_exists('name',$params) && !empty($params['name'])) ? $params['name'] : NULL;
        $insert['url_alias'] = (array_key_exists('url_alias',$params) && !empty($params['url_alias'])) ? $params['url_alias'] : NULL;
        $insert['description'] = (array_key_exists('description',$params)) ? $params['description'] : NULL;
        $insert['content'] = (array_key_exists('content',$params)) ? $params['content'] : NULL;
        $insert['seo_title'] = (array_key_exists('seo_title',$params)) ? $params['seo_title'] : NULL;
        $insert['seo_description'] = (array_key_exists('seo_description',$params)) ? $params['seo_description'] : NULL;
        $insert['seo_keywords'] = (array_key_exists('seo_keywords',$params)) ? $params['seo_keywords'] : NULL;
        $insert['enabled'] = (array_key_exists('enabled',$params) && $this->isStatus($params['enabled'])) ? $params['enabled'] : STATUS_ENABLED;

        if(!$this->not_empty($insert,$this->insert_required_fields))
        {
            $params['desc'] = "Can not create News. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isLangExists($insert['lang_id']))
        {
            $params['desc'] = "Can not create News. Language not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_language_not_exists',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isUrlUnique($insert['url_alias'],$insert['lang_id']))
        {
            $params['desc'] = "Can not create News. News url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->insert($this->model_table,$insert);

        if($this->db->_error_message() || !$this->db->insert_id())
        {
            $params['desc'] = "Can not create News. Run SQL query failed";
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
    *	Update User
    *
    * @param	Int
    * @param	Array
    */

    function update($id,$params)
    {
        (array)$params;
        $update = array();

        if(array_key_exists('name',$params) && !empty($params['name']))
            $update['name'] = $params['name'];

        if(array_key_exists('url_alias',$params) && !empty($params['url_alias']))
            $update['url_alias'] = $params['url_alias'];

        if(array_key_exists('description',$params))
            $update['description'] = $params['description'];

        if(array_key_exists('content',$params))
            $update['content'] = $params['content'];

        if(array_key_exists('seo_title',$params))
            $update['seo_title'] = $params['seo_title'];

        if(array_key_exists('seo_description',$params))
            $update['seo_description'] = $params['seo_description'];

         if(array_key_exists('seo_keywords',$params))
            $update['seo_keywords'] = $params['seo_keywords'];

        if(array_key_exists('enabled',$params) && $this->isStatus($params['enabled']))
            $update['enabled'] = $params['enabled'];

        if(!count($update) || !(int)$id)
        {
            $params['desc'] = "Can not update News. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $news_info = $this->get($id);

        if(!$news_info)
        {
            $params['desc'] = "Can not update News. News does not exist";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_item_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(array_key_exists('url_alias', $update) && !$this->isUrlUnique($update['url_alias'],$news_info['lang_id'],$id))
        {
            $params['desc'] = "Can not update News. Url not unique";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_url_not_unique',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->where('id',(int)$id);
        $this->db->update($this->model_table,$update);

        if($this->db->_error_message())
        {
            $params['desc'] = "Can not update News. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }

    function getNews()
    {
        $this->db
                ->select(array('name','description',"DATE_FORMAT(date_add,'%b %d') AS date_show",'id'),FALSE)
                ->from($this->model_table)
                ->where('enabled',1)
                ->limit(10,0)
                ->order_by('date_add');

        $query = $this->db->get();

        if($query->num_rows())
        {
            return $query->result_array();
        }

        return array();
    }
}

?>

