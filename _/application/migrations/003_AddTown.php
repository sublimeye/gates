<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Addtown extends CI_Migration {
	
	public function up()
	{	
		if($this->db->table_exists(TOWN_DB_TABLE))
		{
            $insert['lang_id'] = 1;
            $insert['name'] = "Альпийка";
            
            $this->db->insert(TOWN_DB_TABLE,$insert);

            $insert['lang_id'] = 1;
            $insert['name'] = "Конык";

            $this->db->insert(TOWN_DB_TABLE,$insert);

            $insert['lang_id'] = 2;
            $insert['name'] = "Alpiyka";

            $this->db->insert(TOWN_DB_TABLE,$insert);

            $insert['lang_id'] = 2;
            $insert['name'] = "Konyk";

            $this->db->insert(TOWN_DB_TABLE,$insert);
		}
	}

	public function down()
	{
	}
}
