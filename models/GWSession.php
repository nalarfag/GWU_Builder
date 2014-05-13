<?php


namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWSession extends GWBaseModel
{
	protected $SessionID;
	protected $UserName;
	protected $IP;
	protected $City;
	protected $Country;
	protected $Duration;
	protected $SurveyTakenDate;
	protected $SurveyCompleted;


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
		array_push($searchableFields, 'SessionID', 'UserName', 'IP', 'City', 'Country', 'Duration', 'SurveyTakenDate', 'SurveyCompleted');
		return $searchableFields;
    }
}

?>