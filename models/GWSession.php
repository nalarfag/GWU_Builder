<?php


namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWSession extends GWBaseModel
{
	protected $SessionID;
	protected $User_name;
	protected $SurveyCompleted;
	protected $Duration;
	protected $SurveyTakenDate;
	protected $IP;
	protected $City;
	protected $Country;


    public static function get_primary_key()
    {
		return array('SessionID');
    }

    public static function get_table()
    {
        return 'gwu_session';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields, 'SessionID', 'User_name', 'SurveyCompleted', 'Duration', 'SurveyTakenDate', 'IP', 'City', 'Country');
		return $searchableFields;
    }
}

?>