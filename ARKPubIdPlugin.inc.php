<?php

/**
 * @file plugins/pubIds/ark/ARKPubIdPlugin.inc.php
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ARKPubIdPlugin
 * @ingroup plugins_pubIds_ark
 *
 * @brief ARK plugin class
 */


import('classes.plugins.PubIdPlugin');

class ARKPubIdPlugin extends PubIdPlugin {

	//
	// Implement template methods from Plugin.
	//
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.pubIds.ark.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.pubIds.ark.description');
	}


	//
	// Implement template methods from PubIdPlugin.
	//
	/**
	 * @copydoc PKPPubIdPlugin::constructPubId()
	 */
	function constructPubId($pubIdPrefix, $pubIdSuffix, $contextId) {
		$ark = $pubIdPrefix .'/'. $pubIdSuffix;
		return $ark;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdType()
	 */
	function getPubIdType() {
		return 'ark';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdDisplayType()
	 */
	function getPubIdDisplayType() {
		return 'ARK';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdFullName()
	 */
	function getPubIdFullName() {
		return 'Archival Resource Key';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getResolvingURL()
	 */
	function getResolvingURL($contextId, $pubId) {
		$resolverURL = $this->getSetting($contextId, 'arkResolver');
		return $resolverURL . $pubId;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdMetadataFile()
	 */
	function getPubIdMetadataFile() {
		return $this->getTemplateResource('arkSuffixEdit.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::addJavaScript()
	 */
	function addJavaScript($request, $templateMgr) {

	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdAssignFile()
	 */
	function getPubIdAssignFile() {
		return $this->getTemplateResource('arkAssign.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::instantiateSettingsForm()
	 */
	function instantiateSettingsForm($contextId) {
		$this->import('classes.form.ARKSettingsForm');
		return new ARKSettingsForm($this, $contextId);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getFormFieldNames()
	 */
	function getFormFieldNames() {
		return array('arkSuffix');
	}

	/**
	 * @copydoc PKPPubIdPlugin::getAssignFormFieldName()
	 */
	function getAssignFormFieldName() {
		return 'assignARK';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPrefixFieldName()
	 */
	function getPrefixFieldName() {
		return 'arkPrefix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixFieldName()
	 */
	function getSuffixFieldName() {
		return 'arkSuffix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getLinkActions()
	 */
	function getLinkActions($pubObject) {
		$linkActions = array();
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::getRequest();
		$userVars = $request->getUserVars();
		$userVars['pubIdPlugIn'] = get_class($this);
		// Clear object pub id
		$linkActions['clearPubIdLinkActionARK'] = new LinkAction(
			'clearPubId',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.ark.editor.clearObjectsARK.confirm'),
				__('common.delete'),
				$request->url(null, null, 'clearPubId', null, $userVars),
				'modal_delete'
			),
			__('plugins.pubIds.ark.editor.clearObjectsARK'),
			'delete',
			__('plugins.pubIds.ark.editor.clearObjectsARK')
		);

		if (is_a($pubObject, 'Issue')) {
			// Clear issue objects pub ids
			$linkActions['clearIssueObjectsPubIdsLinkActionARK'] = new LinkAction(
				'clearObjectsPubIds',
				new RemoteActionConfirmationModal(
					$request->getSession(),
					__('plugins.pubIds.ark.editor.clearIssueObjectsARK.confirm'),
					__('common.delete'),
					$request->url(null, null, 'clearIssueObjectsPubIds', null, $userVars),
					'modal_delete'
				),
				__('plugins.pubIds.ark.editor.clearIssueObjectsARK'),
				'delete',
				__('plugins.pubIds.ark.editor.clearIssueObjectsARK')
			);
		}

		return $linkActions;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixPatternsFieldName()
	 */
	function getSuffixPatternsFieldNames() {
		return  array(
			'Issue' => 'arkIssueSuffixPattern',
			'Submission' => 'arkSubmissionSuffixPattern',
			'Representation' => 'arkRepresentationSuffixPattern',
		);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getDAOFieldNames()
	 */
	function getDAOFieldNames() {
		return array('pub-id::ark');
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	function isObjectTypeEnabled($pubObjectType, $contextId) {
		return (boolean) $this->getSetting($contextId, "enable${pubObjectType}ARK");
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	function getNotUniqueErrorMsg() {
		return __('plugins.pubIds.ark.editor.arkSuffixCustomIdentifierNotUnique');
	}
}


