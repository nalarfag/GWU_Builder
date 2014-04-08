<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWQuestion extends GWBaseModel
{
    protected $Question_Number;
	protected $QuestionnaireID;
	protected $AnsType;
	protected $Text;
	protected $Mandatory;


    public static function get_primary_key()
    {
		return array('Question_Number','QuestionnaireID');
    }

    public static function get_table()
    {
        return 'gwu_question';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,'Question_Number','QuestionnaireID','AnsType','Text','Mandatory');
		return $searchableFields;
    }
}

?>