<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWCondition extends GWBaseModel
{
    protected $ConditionID;
	protected $QuestionnaireID;
	protected $LogicStatement;
	protected $JumpQNoOnFailure;
	protected $JumpQNoOnSuccess;
    protected $Deleted;
			  
    public static function get_primary_key()
    {
		return array('ConditionID');
    }

    public static function get_table()
    {
        return 'gwu_condition';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,
		'ConditionID',
		'QuestionnaireID',
		'LogicStatement',
		'JumpQNoOnFailure',
		'JumpQNoOnSuccess'
		);
		return $searchableFields;
    }
}

?>