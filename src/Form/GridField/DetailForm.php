<?php

namespace zauberfisch\PersistentDataObject\Form\GridField;

use zauberfisch\PersistentDataObject\Model\VersionedDataObjectExtension;

class DetailForm extends \GridFieldDetailForm {
	public function getURLHandlers($gridField) {
		return array_merge([
			'version-group/$VersionGroupID/item/$ID' => 'handleVersionedItem',
		], parent::getURLHandlers($gridField));
	}
	
	/**
	 * @param \GridField $gridField
	 * @param \SS_HTTPRequest $request
	 * @return \GridFieldDetailForm_ItemRequest|\RequestHandler
	 */
	public function handleVersionedItem($gridField, $request) {
		// Our getController could either give us a true Controller, if this is the top-level GridField.
		// It could also give us a RequestHandler in the form of GridFieldDetailForm_ItemRequest if this is a
		// nested GridField.
		$requestHandler = $gridField->getForm()->getController();
		
		$versionedID = $request->param('VersionGroupID');
		$id = $request->param('ID');
		
		if ($versionedID && is_numeric($versionedID)) {
			/** @var \DataList $list */
			$list = $gridField->getList();
			$list = $list->filter('VersionGroupID', $versionedID);
			if ($id && is_numeric($id)) {
				/** @var \DataObject|\PersistentDataObject_Model_DataObject|VersionedDataObjectExtension $record */
				$record = $list->byID($id);
			} else {
				$record = $list->filter('VersionGroupLatest', true)->first();
			}
		} else {
			$record = \Object::create($gridField->getModelClass());
		}
		$class = $this->getItemRequestClass();
		/** @var DetailForm_ItemRequest $handler */
		$handler = \Object::create($class, $gridField, $this, $record, $requestHandler, $this->name);
		$handler->setTemplate($this->template);
		
		// if no validator has been set on the GridField and the record has a CMS validator, use that.
		if (!$this->getValidator()
			&& (
				method_exists($record, 'getCMSValidator')
				|| $record instanceof \Object && $record->hasMethod('getCMSValidator')
			)
		) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->setValidator($record->getCMSValidator());
		}
		return $handler->handleRequest($request, \DataModel::inst());
	}
	
}
