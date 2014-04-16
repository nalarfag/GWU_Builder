<?php


namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWResponse extends GWBaseModel
{
    protected $ResponseID;
	protected $QuestSequence;
	protected $SessionID;
	protected $QuestionnaireID;
	protected $OptionNumber;
	protected $ResponseType;
	protected $ResponseContent;
	protected $CodeToProcessResponse;
	protected $ProcessingResult;


    public static function get_primary_key()
    {
		return array('ResponseID');
    }

    public static function get_table()
    {
        return 'gwu_response';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields, 'ResponseID', 'QuestSequence', 'SessionID', 'QuestionnaireID', 'OptionNumber', 'ResponseType', 'ResponseContent', 'CodeToProcessResponse', 'ProcessingResult');
		return $searchableFields;
    }
}

?>