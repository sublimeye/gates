<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Building model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Language extends baseModel
{
    var $log_file = 'model_language.log';
    var $model_table = LANGUAGE_DB_TABLE;

    function __construct()
    {
        parent::__construct();

    }

    function isLanguage($language_id)
    {
        if(!empty($language_id))
        {
            $this->db->select('id')->from($this->model_table)->where('id',$language_id);
            $this->db->limit(1,0);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }
}

?>

