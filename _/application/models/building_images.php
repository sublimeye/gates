<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Building model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Building_images extends baseModel
{
    var $log_file = 'model_building_images.log';
    var $model_table = BUILDING_IMAGES_DB_TABLE;
    var $upload_field_name = array('img');
	var $upload_path = array('');

    function __construct()
    {
        parent::__construct();

        $this->upload_path['0'] = getenv('DOCUMENT_ROOT').'/user_files/building_additional_images';
    }

    /**
    *	Add new Building Image
    *
    * @param	Array
    */

    function add($building_id)
    {
        if(!$this->isBuildingExists($building_id))
        {
            $params['desc'] = "Can not create Building Image. Building not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $insert = array();

        $config['upload_path'] = $this->upload_path['0'];
        $config['encrypt_name'] = TRUE;
        $config['allowed_types'] = 'gif|jpg|png';

        $upload_result = $this->do_upload($this->upload_field_name['0'],$config);

        if($upload_result !== FALSE)
        {
            $insert[$this->upload_field_name['0']] = $upload_result['file_name'];

            copy($this->upload_path['0']."/".$upload_result['file_name'],$this->upload_path['0']."/middle_".$upload_result['file_name']);
            $this->croupImage($this->upload_path['0']."/middle_".$upload_result['file_name'],434,284);

            copy($this->upload_path['0']."/".$upload_result['file_name'],$this->upload_path['0']."/small_".$upload_result['file_name']);
            $this->croupImage($this->upload_path['0']."/small_".$upload_result['file_name'],60,60);
        }
        else
        {
            $params['msg'] = $this->upload->display_errors();
            $params['desc'] = "Can not upload file.";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('custom',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $insert['building_id'] = $building_id;
        $this->db->insert($this->model_table,$insert);

        if($this->db->_error_message() || !$this->db->insert_id())
        {
            $params['desc'] = "Can not create Building. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return TRUE;
    }
    
    function isBuildingExists($building_id)
    {
        if(!empty($building_id))
        {
            $this->db->select('id')->from(BUILDING_DB_TABLE)->where('id',$building_id);
            $this->db->limit(1,0);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    function getImages($building_id,$limit = 0)
    {
        $this ->db
                ->select('img')
                ->from(BUILDING_IMAGES_DB_TABLE)
                ->where('building_id',$building_id);

        if(!empty($limit))
        {
            $this->db->limit($limit,0);
        }

        $query = $this->db->get();

        if($query->num_rows())
        {
            return $query->result_array();
        }

        return array();
    }
}

?>

