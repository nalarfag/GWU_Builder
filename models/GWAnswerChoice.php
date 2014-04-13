<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWAnswerChoice extends GWBaseModel
{
    protected $QuestionnaireID;
    protected $QuestSequence;
    protected $OptionNumber;
    protected $AnsValue;
    protected $Deleted;

    public static function get_primary_key()
    {
        return array('OptionNumber','QuestionnaireID','QuestSequence');
    }

    public static function get_table()
    {
        return 'gwu_answerChoice';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
        array_push($searchableFields,
            'QuestionnaireID',
            'QuestSequence',
            'OptionNumber',
            'AnsValue'
        );
        return $searchableFields;
    }
}

?>