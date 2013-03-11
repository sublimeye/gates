<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/*
Building places model class
(c) NaBalkone http://www.to.net.ua
@package Models
*/

class Building_places extends baseModel
{
    var $log_file = 'model_building_places.log';
    var $model_table = BUILDING_PLACES_DB_TABLE;
    var $insert_required_fields = array('building_id','params');
    var $building_place_images_path = '';

    function __construct()
    {
        parent::__construct();

        $this->building_place_images_path = getenv('DOCUMENT_ROOT').'/user_files/building_place_images';
    }

    /**
    *	Add new Building places
    *
    * @param	Array
    */

    function add($params)
    {
        (array)$params;
        $insert = array();

        $insert['building_id'] = (array_key_exists('building_id',$params) && !empty($params['building_id'])) ? $params['building_id'] : NULL;
        $insert['params'] = (array_key_exists('params',$params) && !empty($params['params'])) ? $params['params'] : NULL;
        $insert['state'] = (array_key_exists('state',$params) && $this->isState($params['state'])) ? $params['state'] : 1;
        $insert['enabled'] = (array_key_exists('enabled',$params) && $this->isStatus($params['enabled'])) ? $params['enabled'] : 1;

        if(!$this->not_empty($insert,$this->insert_required_fields))
        {
            $params['desc'] = "Can not create Building places. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(is_null($insert['state']) || is_null($insert['enabled']))
        {
            $params['desc'] = "Can not create Building places. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isBuildingExists($insert['building_id']))
        {
            $params['desc'] = "Can not create Building places. Building not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $this->db->insert($this->model_table,$insert);

        if($this->db->_error_message() || !$this->db->insert_id())
        {
            $params['desc'] = "Can not create Building places. Run SQL query failed";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

            return FALSE;
        }

        return $this->db->insert_id();
    }

    /**
    *	Add new Building places
    *
    * @param	Array
    */

    function update($id,$params)
    {
        $update['building_id'] = (array_key_exists('building_id',$params) && !empty($params['building_id'])) ? $params['building_id'] : NULL;
        $update['params'] = (array_key_exists('params',$params) && !empty($params['params'])) ? $params['params'] : NULL;
        $update['state'] = (array_key_exists('state',$params) && $this->isState($params['state'])) ? $params['state'] : 1;
        $update['enabled'] = (array_key_exists('enabled',$params) && $this->isStatus($params['enabled'])) ? $params['enabled'] : 1;

        if(empty($id) || is_null($update['state']) || is_null($update['enabled']))
        {
            $params['desc'] = "Can not update Building places. Incorrect parameters";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_invalid_params',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        if(!$this->isBuildingExists($update['building_id']))
        {
            $params['desc'] = "Can not update Building places. Building not exists";
            $params['file'] = __FILE__;
            $params['line'] = __LINE__;
            $params['class'] = __CLASS__;
            $params['method'] = __METHOD__;

            $this->set_error('error_child_element_does_not_exist',DEBUG_LEVEL_CUSTOM,$params);

            return FALSE;
        }

        $info = $this->get($id);

        if(is_array($info) && count($info))
        {
            $this->db->where('id',$id)->update($this->model_table,$update);

            if($this->db->_error_message())
            {
                $params['desc'] = "Can not update Building places. Run SQL query failed";
                $params['file'] = __FILE__;
                $params['line'] = __LINE__;
                $params['class'] = __CLASS__;
                $params['method'] = __METHOD__;

                $this->set_error('error_run_sql_failed',DEBUG_LEVEL_SQL,$params);

                return FALSE;
            }

            return $info['id'];
        }

        return FALSE;
    }

    function delete($ids)
    {
        if(!empty($ids))
        {
            $ids = (is_array($ids)) ? $ids : array($ids);

            foreach($ids as $id)
            {
                $building_place_images = $this->get_building_place_images($id);

                if(is_array($building_place_images) && count($building_place_images))
                {
                    foreach($building_place_images as $bpi)
                    {
                        if(!empty($bpi['img']) && is_file($this->building_place_images_path."/".$bpi['img']))
                        {
                            unlink($this->building_place_images_path."/".$bpi['img']);
                        }
                    }
                }
            }
        }

        return parent::delete($ids);
    }

    function get_building_place_images($building_place_id)
    {
        if(!empty($building_place_id))
        {
            $this->db->select('img')->from(BUILDING_PLACE_IMAGES_DB_TABLE)->where('building_place_id',$building_place_id);
            $query = $this->db->get();

            if(!$this->db->_error_message() && $query->num_rows())
            {
                return $query->result_array();
            }
        }

        return array();
    }

    function isState($state)
    {
        if($state == 0 || $state == 1 || $state == 2 || $state == 3)
        {
            return TRUE;
        }

        return FALSE;
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

    function getBuildingPlaces($town_id,$enabled_filter = FALSE)
    {
        $this->db->select(
            array(
                 BUILDING_PLACES_DB_TABLE.".*",
                 BUILDING_DB_TABLE.".name",
                 BUILDING_DB_TABLE.".url_alias",
                 BUILDING_DB_TABLE.".square",
                 BUILDING_DB_TABLE.".description",
                 BUILDING_DB_TABLE.".img")
        );

        $this->db->from(BUILDING_PLACES_DB_TABLE);
        $this->db->join(BUILDING_DB_TABLE,BUILDING_PLACES_DB_TABLE.".building_id=".BUILDING_DB_TABLE.".id");
        $this->db->where(array(BUILDING_DB_TABLE.'.town_id' => $town_id));

        if($enabled_filter)
        {
            $this->db->where('enabled',1);
        }

        $query = $this->db->get();

        if(!$this->db->_error_message() && $query->num_rows())
        {
            return $query->result_array();
        }

        return array();
    }
}

?>

