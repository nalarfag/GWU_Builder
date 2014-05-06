<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWFlagSet extends GWBaseModel
{
    protected $FlagID;
	protected $QuestionnaireID;
	protected $Qustion_Number;
	protected $OptionNumber;

    public static function get_primary_key()
    {
		return array('FlagID','QuestionnaireID','Question_Number','OptionNumber');
    }

    public static function get_table()
    {
        return 'gwu_flagset';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,'FlagID','QuestionnaireID','Question_Number','OptionNumber');
		return $searchableFields;
    }
}

?>