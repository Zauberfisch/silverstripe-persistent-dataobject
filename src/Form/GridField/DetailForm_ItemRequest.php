<?php

namespace zauberfisch\PersistentDataObject\Form\GridField;

use zauberfisch\PersistentDataObject\Model\VersionedDataObjectExtension;

class DetailForm_ItemRequest extends \GridFieldDetailForm_ItemRequest {
	private static $allowed_actions = [
		'edit',
		'view',
		'ItemEditForm',
	];
	
	public function Link($action = null) {
		$r = $this->record;
		if ($r && $r->hasExtension(VersionedDataObjectExtension::class)) {
			/** @var \DataObject|VersionedDataObjectExtension $r */
			return \Controller::join_links(
				$this->gridField->Link('version-group'),
				$r->VersionGroupID ?: 'new',
				'item',
				$r->isLatestVersion(true) ? 'latest' : $r->ID,
				$action
			);
		}
		return parent::Link($action);
	}
}
