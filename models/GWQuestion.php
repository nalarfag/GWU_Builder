<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWQuestion extends GWBaseModel
{
    protected $QuestionnaireID;
	protected $QuestSequence;
	protected $ConditionID;
	protected $QuestionNumber;
	protected $AnsType;
    protected $Text;
    protected $Mandatory;
	protected $Deleted;
	
			  
    public static function get_primary_key()
    {
		return array('QuestSequence','QuestionnaireID');
    }

    public static function get_table()
    {
        return 'gwu_question';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,
		'QuestionnaireID',
		'QuestSequence',
		'ConditionID',
		'QuestionNumber',
		'AnsType',
		'Text',
		'Mandatory'		
		);
		return $searchableFields;
    }
}

?>