<?php
/*
 * Fix wpdb to allow inserting/updating of null values into tables
 */
class wpdbfixed extends wpdb
{
    function insert($table, $data, $format = null) {
        $type = 'INSERT';
        if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
            return false;
        $this->insert_id = 0;
        $formats = $format = (array) $format;
        $fields = array_keys( $data );
        $formatted_fields = array();
        foreach ( $fields as $field ) {
            if ( !empty( $format ) )
                $form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
            elseif ( isset( $this->field_types[$field] ) )
                $form = $this->field_types[$field];
            else
                $form = '%s';

            //***Steve Lee edit begin here***
            if ($data[$field]===null) {
                unset($data[$field]); //Remove this element from array, so we don't try to insert its value into the %s/%d/%f parts during prepare().  Without this, array would become shifted.
                $formatted_fields[] = 'NULL';
            } else {
                $formatted_fields[] = $form; //Original line of code
            }
            //***Steve Lee edit ends here***
        }
        $sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $formatted_fields ) . ")";
        return $this->query( $this->prepare( $sql, $data ) );
    }

    function update($table, $data, $where, $format = null, $where_format = null)
    {
        if ( ! is_array( $data ) || ! is_array( $where ) )
            return false;

        $formats = $format = (array) $format;
        $bits = $wheres = array();
        foreach ( (array) array_keys( $data ) as $field ) {
            if ( !empty( $format ) )
                $form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
            elseif ( isset($this->field_types[$field]) )
                $form = $this->field_types[$field];
            else
                $form = '%s';

            //***Steve Lee edit begin here***
            if ($data[$field]===null)
            {
                unset($data[$field]); //Remove this element from array, so we don't try to insert its value into the %s/%d/%f parts during prepare().  Without this, array would become shifted.
                $bits[] = "`$field` = NULL";
            } else {
                $bits[] = "`$field` = {$form}"; //Original line of code
            }
            //***Steve Lee edit ends here***
        }

        $where_formats = $where_format = (array) $where_format;
        foreach ( (array) array_keys( $where ) as $field ) {
            if ( !empty( $where_format ) )
                $form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
            elseif ( isset( $this->field_types[$field] ) )
                $form = $this->field_types[$field];
            else
                $form = '%s';
            $wheres[] = "`$field` = {$form}";
        }

        $sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
        return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
    }

}
/*global $wpdb_allow_null;
$wpdb_allow_null = new wpdbfixed(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);*/
?>