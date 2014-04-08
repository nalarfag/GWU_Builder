<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWQuestionnaire extends GWBaseModel
{
    protected $QuestionnaireID;
	protected $Title;
	protected $Topic;
	protected $AllowAnonymous;
	protected $AllowMultiple;
	protected $CreatorName;
	protected $DateCreated;

    public static function get_primary_key()
    {
		return array('QuestionnaireID');
    }

    public static function get_table()
    {
        return 'gwu_questionnaire';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields, 'QuestionnaireID', 'Title', 'Topic', 'AllowAnonymous', 'AllowMultiple', 'CreatorName', 'DateCreated');
		return $searchableFields;
    }
}