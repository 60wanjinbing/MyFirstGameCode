<?php
class MCBuilding extends MCObject
{
    public static function BuildingForUser($uid, $rid)
    {
        $uid = intval($uid);
        $rid = intval($rid);
        $db = get_db_for_uid($uid);
        $query = "SELECT * FROM user_buildings WHERE record_id = $rid";
        mdebug($query);
        $db->query($query);
        $rs = $db->getLastResultSet();
        $row = $rs->getNextRow();
        $building = self::BuildingWithRow($row);
        if ($building->user_id != $uid)
        {
            return null;
        }
        return $building;
    }
    
    public static function BuildingWithRow(&$row)
    {
        $building = new self;
        if (!$building->loadWithRow($row)) return null;
        
        return $building;
    }
    
    public static function CreateBuilding($uid, $x, $y, $type, $level = 1)
    {
        $uid = intval($uid);
        $type = intval($type);
        $level = intval($level);
        
        $x = round($x); $y = round($y);
        $db = get_db_for_uid($uid);
        $query = "INSERT INTO user_buildings (user_id, type, level, x, y) VALUES ("
        . "$uid, $type, $level, $x, $y"
        . ")";
        $db->query($query);
        
        $rid = $db->getInsertId();
        return self::BuildingForUser($uid, $rid);
    }
    
    public function loadWithRow(&$row)
    {
        if (!$row) return false;
        $this->resetData($row);
        return true;
    }
    
    public function save()
    {
        $db = get_db_for_uid($this->user_id);
        $query = "UPDATE user_buildings SET ";
        $dirtyFields = $this->getDirtyFields();
        $setQueries = generate_dirty_fields_update_query($dirtyFields);
        if (!$setQueries) return; // nothing to save
    
        $query .= $setQueries;
        $query .= " record_id = $this->record_id WHERE record_id = $this->record_id";
        $db->query($query);
    
        $this->setToSaved();
    }
}