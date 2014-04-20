<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWQuestionnaire extends GWBaseModel
{
    protected $QuestionnaireID;
    protected $Title;
    protected $Topic;
    protected $CreatorName;
    protected $AllowMultiple;
    protected $AllowAnnonymous;
    protected $DateCreated;
    protected $DateModified;
    protected $InactiveDate;
    protected $IntroText;
    protected $ThankyouText;
    //protected $Link;
    protected $PostId;
    protected $PublishFlag;
    protected $PublishDate;
    protected $Deleted;


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
        array_push($searchableFields,
            'QuestionnaireID',
            'Title',
            'Topic',
            'CreatorName',
            'AllowMultiple',
            'AllowAnnonymous',
            'DateCreated',
            'DateModified',
            'InactiveDate',
            'IntroText',
            'ThankyouText',
            'Link',
            'PublishFlag',
            'PublishDate'
        );
        return $searchableFields;
    }
}