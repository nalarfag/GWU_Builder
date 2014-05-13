<?php

namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;
use JsonSerializable;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWFlag extends GWBaseModel implements JsonSerializable
{
    protected $FlagID;
	protected $OptionNumber;
	protected $QuestSequence;
	protected $QuestionnaireID;
	protected $FlagName;
	protected $FlagValue;
	protected $Deleted;

    public static function get_primary_key()
    {
		return array('FlagID');
    }

    public static function get_table()
    {
        return 'gwu_flag';
    }

    public static function get_searchable_fields()
    {
        $searchableFields = array();
		array_push($searchableFields,'FlagID','FlagName','FlagValue','OptionNumber','QuestSequence','QuestionnaireID');
		return $searchableFields;
    }
	
	public function __toString() {
        return $this->FlagName;
    }
	
	public function jsonSerialize() {
        return [
            'FlagID' => $this->get_FlagID(),
            'OptionNumber' => $this->get_OptionNumber(),
            'QuestSequence' => $this->get_QuestSequence(),
			'QuestionnaireID' => $this->get_QuestionnaireID(),
			'FlagName' => $this->get_FlagName(),
			'FlagValue' => $this->get_FlagValue(),
			'Deleted' => $this->get_Deleted()
        ];
    }
}

?>