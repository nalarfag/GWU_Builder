<?php


namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWResponse extends GWBaseModel
{
    protected $ResponceID;
	protected $SessionID;
	protected $QuestionnaireID;
	protected $Question_Number;
	protected $AnswerNumber;
	protected $ResponceType;
	protected $ResponceContent;
	protected $CodeToProcessResponce;
	protected $ProcessingResult;


    public static function get_primary_key()
    {
		return array('ResponceID');
    }

    public static function get_table()
    {
        return 'gwu_response';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields, 'ResponceID', 'SessionID', 'QuestionnaireID', 'Question_Number', 'AnswerNumber', 'ResponceType', 'ResponceContent', 'CodeToProcessResponce', 'ProcessingResult');
		return $searchableFields;
    }
}

?>