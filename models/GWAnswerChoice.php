<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWAnwerChoice extends GWBaseModel
{
    protected $OptionNumber;
	protected $QuestionnaireID;
	protected $Question_Number;
	protected $AnsValue;

    public static function get_primary_key()
    {
		return array('OptionNumber','QuestionnaireID','Question_Number');
    }

    public static function get_table()
    {
        return 'gwu_answerchoice';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,'OptionNumber','Question_Number','QuestionnaireID','AnsValue');
		return $searchableFields;
    }
}

?>