<?php

    /*$var = "( F1 == 2 ) AND ( F2 >= 3 )";
    $arr = ConditionParser::parseCondition($var);
    //arr->display();
    //var_dump(arr[0]->flagName);
    $x = $arr[0];
    //var_dump($arr[0]);
    $t = $x->getFlagName();
    var_dump($t);*/
    
class ConditionParser{
    protected $flagName;
    protected $conditionOperator;
    protected $flagValue;
    protected $logicalOperator;

    public static function parseCondition($conditionString){
        //$conditionString = "( F1 == 2 ) AND ( F2 >= 3 )"
        
        $members = explode(" ",$conditionString);
        $conditionArray = array();

        for($i = 0; $i < count($members);$i++){
            $obj = new ConditionParser();
            $braces = $members[$i];
            $i++;
            $obj->flagName = $members[$i];
            $i++;
            $obj->conditionOperator = $members[$i];
            $i++;
            $obj->flagValue = $members[$i];
			$i++;
            $braces = $members[$i];
            if($i < count($members) -1) {
                $i++;
                $obj->logicalOperator = $members[$i];
            }
            array_push($conditionArray,$obj);
        }

        return $conditionArray;      
    }
    
    public function getFlagName(){
        return $this->flagName;
    }
    
    public function getConditionOperator(){
        return $this->conditionOperator;
    }
    
    public function getFlagValue(){
        return $this->flagValue;
    }
    
    public function getLogicalOperator(){
        return $this->logicalOperator;
    }

    /*public function display(){
        var_dump($this->flagName);
        var_dump($this->conditionOperator);
        var_dump($this->flagValue);
        var_dump($this->logicalOperator);
    }  */  
}

?>
