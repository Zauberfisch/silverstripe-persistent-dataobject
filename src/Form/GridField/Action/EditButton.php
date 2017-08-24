<?php

namespace zauberfisch\PersistentDataObject\Form\GridField\Action;

use zauberfisch\PersistentDataObject\Model\VersionedDataObjectExtension;

class EditButton extends \GridFieldEditButton {
	public function getColumnContent($gridField, $record, $columnName) {
		if ($record->hasExtension(VersionedDataObjectExtension::class)) {
			/** @var \PersistentDataObject_Model_DataObject|VersionedDataObjectExtension $record */
			$data = new \ArrayData([
				'Link' => \Controller::join_links(
					$gridField->Link('version-group'),
					$record->VersionGroupID,
					'item',
					$record->isLatestVersion(true) ? 'latest' : $record->ID,
					'edit'
				),
			]);
			$template = \SSViewer::get_templates_by_class(\GridFieldEditButton::class, '', \GridFieldEditButton::class);
			return $data->renderWith($template);
		}
		return parent::getColumnContent($gridField, $record, $columnName);
	}
}
