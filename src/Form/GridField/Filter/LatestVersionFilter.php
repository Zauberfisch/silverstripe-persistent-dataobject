<?php

namespace zauberfisch\PersistentDataObject\Form\GridField\Filter;

class LatestVersionFilter implements \GridField_DataManipulator {
	/**
	 * @param \GridField $gridField
	 * @param \SS_List|\DataList|\ArrayList $dataList
	 * @return mixed
	 */
	public function getManipulatedData(\GridField $gridField, \SS_List $dataList) {
		return $dataList->filter('VersionGroupLatest', true);
	}
}
