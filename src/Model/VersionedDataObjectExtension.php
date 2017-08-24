<?php

namespace zauberfisch\PersistentDataObject\Model;

/**
 * @author zauberfisch
 * @property VersionedDataObjectExtension|\PersistentDataObject_Model_DataObject $owner
 */
class VersionedDataObjectExtension extends \DataExtension {
	private static $db = [
		'VersionGroupPreviousID' => 'Int',
		//'VersionGroupID' => 'Int',
		//'VersionGroupLatest' => 'Boolean',
	];
	
	public function onBeforeWriteBaseRecord($baseTable, $now) {
		$versionID = $this->owner->getRawFieldData('VersionGroupID');
		if ($versionID) {
			(new \SQLUpdate('"' . $baseTable . '"'))
				->assign('"VersionGroupLatest"', 0)
				->addWhere(['"VersionGroupID"' => $versionID, 'VersionGroupLatest' => 1])
				->execute();
		}
	}
	
	//public function onAfterWriteBaseRecord($baseTable, $now) {
	//	if (!$this->owner->getRawFieldData('VersionGroupID')) {
	//		$id = $this->owner->getRawFieldData('ID');
	//		$this->owner->setRawFieldData('VersionGroupID', $id);
	//		(new \SQLUpdate('"' . $baseTable . '"'))
	//			->assign('"VersionGroupID"', $id)
	//			//->assign('"VersionGroupLatest"', 1)
	//			->addWhere(['"ID"' => $id])
	//			->execute();
	//	}
	//}
	
	public function onBeforeWrite() {
		$isInDB = $this->owner->isInDB();
		// TODO how do we handle saving of old versions?
		// TODO The PageBuilder module may need to revert back to an older version of a record and continue from there
		//if ($isInDB && !$this->owner->isLatestVersion(true)) {
		//	throw new \ValidationException("Old versions can not be written");
		//}
		$this->owner->setRawFieldData('VersionGroupPreviousID', $this->owner->ID);
		$this->owner->setRawFieldData('ID', 0);
		parent::onBeforeWrite();
	}
	
	public function onBeforePurge() {
		if ($this->owner->isLatestVersion(true)) {
			// TODO handle purging of latest version
		}
	}
	
	public function purgeAllVersions() {
		$id = $this->owner->getRawFieldData('ID');
		$this->owner->getVersions()->each(function ($obj) {
			/** @var \PersistentDataObject_Model_DataObject $obj */
			$obj->purge();
		});
		$this->owner->flushCache();
		$this->owner->Deleted = true;
		$this->owner->OldID = $id;
		$this->owner->ID = 0;
	}
	
	///**
	// * @param \Member $member
	// * @return bool|null
	// */
	//public function canEdit($member = null) {
	//	if ($this->owner->isLatestVersion()) {
	//		return parent::canEdit($member);
	//	}
	//	return false;
	//}
	//
	///**
	// * @param \Member $member
	// * @return bool|null
	// */
	//public function canDelete($member = null) {
	//	if ($this->owner->isLatestVersion()) {
	//		return parent::canDelete($member);
	//	}
	//	return false;
	//}
	
	/**
	 * @param \Member $member
	 * @return bool|null
	 */
	public function canPurge($member = null) {
		// TODO
		return false;
	}
	
	/**
	 * @param bool $skipCache
	 * @return bool
	 */
	public function isLatestVersion($skipCache = false) {
		if ($this->owner->isInDB()) {
			return !$skipCache ? $this->owner->VersionGroupLatest : $this->owner->get()->filter(['ID' => $this->owner->ID, 'VersionGroupLatest' => true])->exists();
		}
		return true;
	}
	
	/**
	 * @param bool $skipCache
	 * @return $this|\PersistentDataObject_Model_DataObject
	 */
	public function getLatestVersion($skipCache = false) {
		if (!$this->owner->isInDB() || $this->owner->isLatestVersion($skipCache)) {
			return $this;
		}
		return $this->owner->getVersions()->filter(['VersionGroupLatest' => true])->first();
	}
	
	/**
	 * @return \DataList|\DataObject[]|static[]
	 */
	public function getVersions() {
		return $this->owner->get()->filter(['VersionGroupID' => $this->owner->VersionGroupID])->sort('ID', 'DESC');
	}
	
	/**
	 * @return bool
	 */
	public function isDeleted() {
		//return $this->isLatestVersion(true) ? $this->Versioned_Deleted : static::get()->filter(['VersionGroupID' => $this->VersionGroupID, 'VersionGroupLatest' => true, 'Versioned_Deleted' => true])->exists();
		return $this->owner->isLatestVersion(true) ? $this->owner->Deleted : $this->owner->getVersions()->filter(['VersionGroupLatest' => true, 'Deleted' => true])->exists();
	}
}
