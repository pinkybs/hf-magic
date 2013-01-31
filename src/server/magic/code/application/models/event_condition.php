<?php
class Event_Condition_Model extends Model_Core
{
	protected $table_pre = 'role_event_condition';
	
	public function getEventConditionByTypeId($type, $id)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'type' => $type, 'type_id' => $id));
		
		return $result;
	}
	
	public function updateByType($type, $num)
	{
		$this->db_cache->increment($this->table, array('num' => $num), array('role_id' => $this->role_id, 'type' => $type));
	}
	
	public function update($id, $num)
	{
		$this->db_cache->increment($this->table, array('num' => $num), array('id' => $id));
	}
	
	public function delete($event_id)
	{
		$event_data = $this->getEventConditionByRoleIdEventId($event_id);
		
		foreach ($event_data as $vl) {
			$this->db_cache->delete($this->table, array('id' => $vl[0]['id']));
		}
	}
	
	public function getEventConditionByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		return $result;
	}
	
	public function getEventConditionByRoleIdEventId($event_id)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'event_id' => $event_id));
		
		return $result;
	}
}