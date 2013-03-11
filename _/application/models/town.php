<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Town model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Town extends baseModel
{
    var $log_file = 'model_town.log';
    var $model_table = TOWN_DB_TABLE;

    function __construct()
    {
        parent::__construct();

    }

    /**
    *	Update Town
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

        if(array_key_exists('description',$params) && !empty($params['description']))
            $update['description'] = $params['description'];

         if(array_key_exists('map_code',$params))
            $update['map_code'] = $params['map_code'];

        if(array_key_exists('seo_title',$params))
            $update['seo_title'] = $params['seo_title'];

        if(array_key_exists('seo_description',$params))
            $update['seo_description'] = $params['seo_description'];

         if(array_key_exists('seo_keywords',$params))
            $update['seo_keywords'] = $params['seo_keywords'];


        if(!(int)$id)
        {
            $params['desc'] = "Can not update Town. Incorrect parameters";
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
            $params['desc'] = "Can not update Town. Town does not exist";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_item_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!count($update))
        {
            $params['desc'] = "Can not update Town. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->where('id',(int)$id);
        $this->db->update($this->model_table,$update);

        if($this->db->_error_message())
        {
            $params['desc'] = "Can not update Town. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }
}

?>

