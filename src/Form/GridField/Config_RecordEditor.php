<?php

namespace zauberfisch\PersistentDataObject\Form\GridField;

use zauberfisch\PersistentDataObject\Form\GridField\Action\EditButton;
use zauberfisch\PersistentDataObject\Form\GridField\Filter\HideDeletedFilter;
use zauberfisch\PersistentDataObject\Form\GridField\Filter\LatestVersionFilter;

class Config_RecordEditor extends \GridFieldConfig_RecordEditor {
	public function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->removeComponentsByType(\GridFieldDetailForm::class);
		$this->addComponent(new DetailForm());
		$this->removeComponentsByType(\GridFieldEditButton::class);
		$this->addComponent(new EditButton());
		$this->addComponent(new LatestVersionFilter());
		$this->addComponent(new HideDeletedFilter());
	}
}
