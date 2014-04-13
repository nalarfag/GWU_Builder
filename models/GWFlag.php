<?php



namespace WordPress\ORM\Model;

use WordPress\ORM\GWBaseModel;

include_once WP_PLUGIN_DIR . '/GWU_Builder/lib/GWBaseModel.php';

class GWFlag extends GWBaseModel
{
    protected $FlagID;
	protected $FlagName;
	protected $FlagValue;

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
		array_push($searchableFields,'FlagID','FlagName','FlagValue');
		return $searchableFields;
    }
}

?>