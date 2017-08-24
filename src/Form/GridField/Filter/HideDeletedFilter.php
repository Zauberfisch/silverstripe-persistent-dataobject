<?php

namespace zauberfisch\PersistentDataObject\Form\GridField\Filter;

class HideDeletedFilter implements \GridField_DataManipulator {
	/**
	 * @param \GridField $gridField
	 * @param \SS_List|\DataList|\ArrayList $dataList
	 * @return mixed
	 */
	public function getManipulatedData(\GridField $gridField, \SS_List $dataList) {
		$latest = $dataList->filter('VersionGroupLatest', true);
		$groupIDs = array_merge(
			[0],
			$latest->exclude('Deleted', 1)->column('VersionGroupID')
		);
		return $dataList->filter('VersionGroupID', $groupIDs);
	}
}
