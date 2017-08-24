<?php

namespace zauberfisch\PersistentDataObject\Admin;

use zauberfisch\PersistentDataObject\Form\GridField\Config_RecordEditor;
use zauberfisch\PersistentDataObject\Form\GridField\DetailForm;

/**
 * @author zauberfisch
 */
class ModelAdmin extends \ModelAdmin {
	public function getEditForm($id = null, $fields = null) {
		/** @var \Form $return */
		$return = parent::getEditForm($id, $fields);
		/** @var \GridField $grid */
		$grid = $return->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
		$config = new Config_RecordEditor();
		$config->removeComponentsByType(\GridFieldFilterHeader::class);
		// Validation
		if (singleton($this->modelClass)->hasMethod('getCMSValidator')) {
			$detailValidator = singleton($this->modelClass)->getCMSValidator();
			/** @var DetailForm $detailForm */
			$detailForm = $config->getComponentByType(DetailForm::class);
			$detailForm->setValidator($detailValidator);
		}
		// Import / Export
		$config->addComponent((new \GridFieldExportButton('buttons-before-left'))->setExportColumns($this->getExportFields()));
		//if ($this->showImportForm) {
		//	$config->addComponent(
		//		GridFieldImportButton::create('buttons-before-left')
		//			->setImportForm($this->ImportForm())
		//			->setModalTitle(_t('ModelAdmin.IMPORT', 'Import from CSV'))
		//	);
		//}
		$grid->setConfig($config);
		return $return;
	}
}

