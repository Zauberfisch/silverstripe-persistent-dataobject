<?php

//namespace zauberfisch\PersistentDataObject\Model;

/**
 * DataObject classes can not be namespaced in SilverStripe 3.x
 * @author zauberfisch
 * @property boolean $Deleted
 * @property int $VersionGroupID
 * @property boolean $VersionGroupLatest
 */
class PersistentDataObject_Model_DataObject extends \DataObject {
	private static $db = [
		'Deleted' => 'Boolean',
		'VersionGroupID' => 'Int',
		'VersionGroupLatest' => 'Boolean',
	];
	private static $defaults = [
		'VersionGroupLatest' => true,
	];
	
	public function requireTable() {
		parent::requireTable();
	}
	
	
	public function getCMSFields() {
		$return = new FieldList([
			new TabSet('Root'),
		]);
		$return->addFieldsToTab('Root', [
			new Tab('Main', _t('PersistentDataObject_Model_DataObject.MainTab', 'Main')),
		]);
		$s = new FormScaffolder($this);
		$return->addFieldsToTab('Root.Main', $s->getFieldList()->toArray());
		$return->removeByName('Deleted');
		$return->removeByName('VersionGroupID');
		$return->removeByName('VersionGroupLatest');
		return $return;
	}
	
	public function delete() {
		$this->extend('onBeforeMarkDeleted');
		$this->Deleted = true;
		$this->write();
		$this->flushCache();
		$this->extend('onAfterMarkDeleted');
	}
	
	public function purge() {
		$this->extend('onBeforePurge');
		parent::delete();
		$this->extend('onAfterPurge');
	}
	
	public function canDelete($member = null) {
		return $this->isDeleted() ? false : parent::canDelete($member);
	}
	
	public function canPurge($member = null) {
		// only allow purging a record if it has been marked as deleted, making deletion a 2 step process
		return $this->isDeleted() ? parent::canDelete($member) : false;
	}
	
	public function canEdit($member = null) {
		return $this->isDeleted() ? false : parent::canEdit($member);
	}
	
	public function isDeleted() {
		$result = (bool)$this->Deleted;
		$results = $this->extend('isDeleted');
		if ($results && is_array($results)) {
			// Remove NULLs
			$results = array_filter($results, function ($v) {
				return !is_null($v);
			});
			// If there are any non-NULL responses, then return the lowest one of them.
			// If any explicitly deny the permission, then we don't get access
			if ($results) {
				$result = (bool)min($results);
			}
		}
		return $result;
	}
	
	protected function writeBaseRecord($baseTable, $now) {
		$this->extend('onBeforeWriteBaseRecord', $baseTable, $now);
		parent::writeBaseRecord($baseTable, $now);
		$this->onAfterWriteBaseRecord($baseTable, $now);
		$this->extend('onAfterWriteBaseRecord', $baseTable, $now);
	}
	
	public function onAfterWriteBaseRecord($baseTable, $now) {
		if (!$this->owner->getRawFieldData('VersionGroupID')) {
			$id = $this->owner->getRawFieldData('ID');
			$this->owner->setRawFieldData('VersionGroupID', $id);
			(new \SQLUpdate('"' . $baseTable . '"'))
				->assign('"VersionGroupID"', $id)
				//->assign('"VersionGroupLatest"', 1)
				->addWhere(['"ID"' => $id])
				->execute();
		}
	}
	
	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->owner->setRawFieldData('VersionGroupLatest', 1);
	}
	
	
	public function setRawFieldData($name, $value) {
		$this->record[$name] = $value;
		return $this;
	}
	
	public function getRawFieldData($name) {
		return isset($this->record[$name]) ? $this->record[$name] : null;
	}
}
