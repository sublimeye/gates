<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Addlanguage extends CI_Migration {
	
	public function up()
	{	
		if($this->db->table_exists(LANGUAGES_DB_TABLE))
		{
            $insert['name'] = "Русский";
            $insert['url_alias'] = "ru";
            
            $this->db->insert(LANGUAGES_DB_TABLE,$insert);

            $insert['name'] = "English";
            $insert['url_alias'] = "en";

            $this->db->insert(LANGUAGES_DB_TABLE,$insert);
		}
	}

	public function down()
	{
	}
}
