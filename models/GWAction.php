<?php


namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWAction extends GWBaseModel
{
    protected $ActionID;
	protected $QuestSequence;
	protected $QuestionnaireID;
	protected $ActionType;
	protected $LinkToAction;
	protected $Duration;
	protected $Sequence;
	protected $Content;
	protected $Deleted;


    public static function get_primary_key()
    {
		return array('ActionID');
    }

    public static function get_table()
    {
        return 'gwu_action';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields, 'ActionID', 'QuestionnaireID', 'Question_Number', 'ActionType', 'Duration', 'LinkToAction', 'Sequence');
		return $searchableFields;
    }
}

?>