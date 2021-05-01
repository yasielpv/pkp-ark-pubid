<?php

/**
 * @file plugins/pubIds/ark/classes/form/ARKSettingsForm.inc.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ARKSettingsForm
 * @ingroup plugins_pubIds_ark
 *
 * @brief Form for journal managers to setup ARK plugin
 */


import('lib.pkp.classes.form.Form');

class ARKSettingsForm extends Form {

	//
	// Private properties
	//
	/** @var integer */
	public $_contextId;

	/**
	 * Get the context ID.
	 * @return integer
	 */
	public function _getContextId() {
		return $this->_contextId;
	}

	/** @var ARKPubIdPlugin */
	public $_plugin;

	/**
	 * Get the plugin.
	 * @return ARKPubIdPlugin
	 */
	public function _getPlugin() {
		return $this->_plugin;
	}

	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin ARKPubIdPlugin
	 * @param $contextId integer
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

		$form = $this;
		$this->addCheck(new FormValidatorCustom($this, 'arkObjects', 'required', 'plugins.pubIds.ark.manager.settings.arkObjectsRequired', function($enableIssueARK) use ($form) {
			return $form->getData('enableIssueARK') || $form->getData('enablePublicationARK') || $form->getData('enableRepresentationARK');
		}));
		$this->addCheck(new FormValidatorRegExp($this, 'arkPrefix', 'required', 'plugins.pubIds.ark.manager.settings.form.arkPrefixPattern', '/^ark:\/[0-9]+$/'));
		$this->addCheck(new FormValidatorCustom($this, 'arkIssueSuffixPattern', 'required', 'plugins.pubIds.ark.manager.settings.arkIssueSuffixPatternRequired', function($arkIssueSuffixPattern) use ($form) {
			if ($form->getData('arkSuffix') == 'pattern' && $form->getData('enableIssueARK')) return $arkIssueSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'arkSubmissionSuffixPattern', 'required', 'plugins.pubIds.ark.manager.settings.arkSubmissionSuffixPatternRequired', function($arkSubmissionSuffixPattern) use ($form) {
			if ($form->getData('arkSuffix') == 'pattern' && $form->getData('enablePublicationARK')) return $arkSubmissionSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'arkRepresentationSuffixPattern', 'required', 'plugins.pubIds.ark.manager.settings.arkRepresentationSuffixPatternRequired', function($arkRepresentationSuffixPattern) use ($form) {
			if ($form->getData('arkSuffix') == 'pattern' && $form->getData('enableRepresentationARK')) return $arkRepresentationSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorUrl($this, 'arkResolver', 'required', 'plugins.pubIds.ark.manager.settings.form.arkResolverRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

		// for ARK reset requests
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::getRequest();
		$this->setData('clearPubIdsLinkAction', new LinkAction(
			'reassignARKs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.ark.manager.settings.arkReassign.confirm'),
				__('common.delete'),
				$request->url(null, null, 'manage', null, array('verb' => 'clearPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_delete'
			),
			__('plugins.pubIds.ark.manager.settings.arkReassign'),
			'delete'
		));
		$this->setData('pluginName', $plugin->getName());
	}


	//
	// Implement template methods from Form
	//

	/**
	 * @copydoc Form::initData()
	 */
	public function initData() {
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($contextId, $fieldName));
		}
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	public function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * @copydoc Form::execute()
	 */
	public function execute(...$functionArgs){
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($contextId, $fieldName, $this->getData($fieldName), $fieldType);
		}
		parent::execute(...$functionArgs);
	}

	//
	// Private helper methods
	//
	public function _getFormFields() {
		return array(
			'enableIssueARK' => 'bool',
			'enablePublicationARK' => 'bool',
			'enableRepresentationARK' => 'bool',
			'arkPrefix' => 'string',
			'arkSuffix' => 'string',
			'arkIssueSuffixPattern' => 'string',
			'arkSubmissionSuffixPattern' => 'string',
			'arkRepresentationSuffixPattern' => 'string',
			'arkResolver' => 'string',
		);
	}
}


