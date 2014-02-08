<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DashboardDataAdministrationController extends DataDashboardBaseController {

	public function on_start() {
		parent::on_start();
		$this->set('navigation', array(
			Page::getByPath('/dashboard/data/management')
		));
	}

	public function view($dtID = null) {
		if (!$dtID) {
			$this->redirect($this->path('search'));
		}
		$dataType = new DataType;
		if (!$dataType->Load('dtID=?', array($dtID))) {
			$this->redirect($this->path('search'));
		}
		$this->set('dataType', $dataType);
	}

	public function search() {
		$DataType = new DataType;
		$this->set('dataTypes', $DataType->Find('1=1'));
		$this->render('search');
	}

	public function create() {
		if (!$this->isPost()) {
			$this->render('create');
		}

		$dataType = new DataType;
		$dataType->dtName = $this->post('dtName');
		$dataType->dtHandle = $this->post('dtHandle');

		$errors = $dataType->validate();
		if ($errors->has()) {
			$this->set('errors', $errors);
			return;
		}

		$dataType->Insert();

		if ($type = AttributeType::getByHandle('text')) {
			$DataAttributeKey = new DataAttributeKey;
			$DataAttributeKey->add(
				$type,
				array(
					'dtID' => $dataType->dtID,
					'akHandle' => 'name',
					'akName' => 'Name',
					'akIsSearchable' => 1
				),
				$dataType
			);
		}

		$this->flashSuccess(t('Data Type Created'));
		$this->redirect($this->path('attributes'), $dataType->dtID);
	}

	/**
	 * @param $dtID int DataType id
	 */
	public function edit($dtID) {
		$dataType = new DataType;
		if (!$dataType->Load('dtID=?', array($dtID))) {
			$this->flashError(t('Data Type Not Found'));
			$this->redirect($this->path());
		}

		if (!$dataType->permissions->canEditDataType()) {
			$this->flashError(t('Access Denied'));
			$this->redirect($this->path());
		}

		if ($this->isPost()) {
			$dataType->dtName = $this->post('dtName');
			$dataType->dtHandle = $this->post('dtHandle');

			$errors = $dataType->validate();
			if ($errors->has()) {
				$this->set('errors', $errors);
				$this->render('edit');
			}

			$dataType->Update();

			$this->flashSuccess(t('Data Type Updated'));
			$this->redirect($this->path());
		}

		$types = array();
		foreach (AttributeType::getList('data') as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$this->set('types', $types);
		$this->set('dataType', $dataType);

		$this->render('edit');
	}

	/**
	 * @param $dtID int DataType id
	 */
	public function delete($dtID) {
		$dataType = new DataType;
		if (!$dataType->Load('dtID=?', array($dtID))) {
			$this->flashError(t('Data Type Not Found'));
			$this->redirect($this->path());
		}

		if (!$dataType->permissions->canDeleteDataType()) {
			$this->flashError(t('Access Denied'));
			$this->redirect($this->path());
		}

		if ($this->isPost()) {
			$dataType->Delete();
			$this->flashSuccess(t('Data Type Deleted'));
			$this->redirect($this->path());
		}

		$this->set('dataType', $dataType);
		$this->render('delete');
	}

}
