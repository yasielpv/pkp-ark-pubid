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
	
/**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) {
            return $success;
        }
        if ($success && $this->getEnabled($mainContextId)) {
            HookRegistry::register('Publication::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Publication::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Publication::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Publication::validate', [$this, 'validatePublicationArk']);
            HookRegistry::register('Galley::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Galley::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Galley::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Issue::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Issue::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
            HookRegistry::register('Issue::getProperties::values', [$this, 'modifyObjectPropertyValues']);
            HookRegistry::register('Form::config::before', [$this, 'addPublicationFormFields']);
            HookRegistry::register('Form::config::before', [$this, 'addPublishFormNotice']);
            HookRegistry::register('TemplateManager::display', [$this, 'loadArkFieldComponent']);
        }
        return $success;
    }
	
	//
	// Implement template methods from Plugin.
	//
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	public function getDisplayName() {
		return __('plugins.pubIds.ark.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	public function getDescription() {
		return __('plugins.pubIds.ark.description');
	}


	//
	// Implement template methods from PubIdPlugin.
	//
	/**
	 * @copydoc PKPPubIdPlugin::constructPubId()
	 */
	public function constructPubId($pubIdPrefix, $pubIdSuffix, $contextId) {
		$ark = $pubIdPrefix .'/'. $pubIdSuffix;
		return $ark;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdType()
	 */
	public function getPubIdType() {
		return 'ark';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdDisplayType()
	 */
	public function getPubIdDisplayType() {
		return 'ARK';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdFullName()
	 */
	public function getPubIdFullName() {
		return 'Archival Resource Key';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getResolvingURL()
	 */
	public function getResolvingURL($contextId, $pubId) {
		$resolverURL = $this->getSetting($contextId, 'arkResolver');
		return $resolverURL . $pubId;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdMetadataFile()
	 */
	public function getPubIdMetadataFile() {
		return $this->getTemplateResource('arkSuffixEdit.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::addJavaScript()
	 */
	public function addJavaScript($request, $templateMgr) {
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdAssignFile()
	 */
	public function getPubIdAssignFile() {
		return $this->getTemplateResource('arkAssign.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::instantiateSettingsForm()
	 */
	public function instantiateSettingsForm($contextId) {
		$this->import('classes.form.ARKSettingsForm');
		return new ARKSettingsForm($this, $contextId);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getFormFieldNames()
	 */
	public function getFormFieldNames() {
		return array('arkSuffix');
	}

	/**
	 * @copydoc PKPPubIdPlugin::getAssignFormFieldName()
	 */
	public function getAssignFormFieldName() {
		return 'assignARK';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPrefixFieldName()
	 */
	public function getPrefixFieldName() {
		return 'arkPrefix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixFieldName()
	 */
	public function getSuffixFieldName() {
		return 'arkSuffix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getLinkActions()
	 */
	public function getLinkActions($pubObject) {
		$linkActions = array();
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::get()->getRequest();
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
	public function getSuffixPatternsFieldNames() {
		return  array(
			'Issue' => 'arkIssueSuffixPattern',
			'Submission' => 'arkSubmissionSuffixPattern',
			'Representation' => 'arkRepresentationSuffixPattern',
		);
	}

	/**
	 * @copydoc PKPPubIdPlugin::getDAOFieldNames()
	 */
	public function getDAOFieldNames() {
		return array('pub-id::ark');
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	public function isObjectTypeEnabled($pubObjectType, $contextId) {
		return (boolean) $this->getSetting($contextId, "enable${pubObjectType}ARK");
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	public function getNotUniqueErrorMsg() {

        return __('plugins.pubIds.ark.editor.arkSuffixCustomIdentifierNotUnique');
    }		 
	
	/**
     * Add ARK to submission, issue or galley properties
     *
     * @param $hookName string <Object>::getProperties::summaryProperties or
     *  <Object>::getProperties::fullProperties
     * @param $args array [
     * 		@option $props array Existing properties
     * 		@option $object Submission|Issue|Galley
     * 		@option $args array Request args
     * ]
     *
     * @return array
     */
    public function modifyObjectProperties($hookName, $args)
    {
        $props = & $args[0];

        $props[] = 'pub-id::ark';
    }

    /**
     * Add ARK submission, issue or galley values
     *
     * @param $hookName string <Object>::getProperties::values
     * @param $args array [
     * 		@option $values array Key/value store of property values
     * 		@option $object Submission|Issue|Galley
     * 		@option $props array Requested properties
     * 		@option $args array Request args
     * ]
     *
     * @return array
     */
    public function modifyObjectPropertyValues($hookName, $args)
    {
        $values = & $args[0];
        $object = $args[1];
        $props = $args[2];

        // ARKs are already added to property values for Publications and Galleys
        if (get_class($object) === 'IssueGalley' || get_class($object) === 'Publication' || get_class($object) === 'ArticleGalley') {
            return;
        }

        if (in_array('pub-id::ark', $props)) {
            $pubId = $this->getPubId($object);
            $values['pub-id::ark'] = $pubId ? $pubId : null;
        }
    }

    /**
     * Validate a publication's ARK against the plugin's settings
     *
     * @param $hookName string
     * @param $args array
     */
    public function validatePublicationArk($hookName, $args)
    {
        $errors = & $args[0];
        $action = $args[1];
        $props = & $args[2];

        if (empty($props['pub-id::ark'])) {
            return;
        }

        if ($action === VALIDATE_ACTION_ADD) {
            $submission = Services::get('submission')->get($props['submissionId']);
        } else {
            $publication = Services::get('publication')->get($props['id']);
            $submission = Services::get('submission')->get($publication->getData('submissionId'));
        }

        $contextId = $submission->getData('contextId');
        $arkPrefix = $this->getSetting($contextId, 'arkPrefix');

        $arkErrors = [];
        if (strpos($props['pub-id::ark'], $arkPrefix) !== 0) {
            $arkErrors[] = __('plugins.pubIds.ark.editor.missingPrefix', ['arkPrefix' => $arkPrefix]);
        }
        if (!$this->checkDuplicate($props['pub-id::ark'], 'Publication', $submission->getId(), $contextId)) {
            $arkErrors[] = $this->getNotUniqueErrorMsg();
        }
        if (!empty($arkErrors)) {
            $errors['pub-id::ark'] = $arkErrors;
        }
    }

    /**
     * Add ARK fields to the publication identifiers form
     *
     * @param $hookName string Form::config::before
     * @param $form FormComponent The form object
     */
    public function addPublicationFormFields($hookName, $form)
    {
        if ($form->id !== 'publicationIdentifiers') {
            return;
        }

        if (!$this->getSetting($form->submissionContext->getId(), 'enablePublicationARK')) {
            return;
        }

        $prefix = $this->getSetting($form->submissionContext->getId(), 'arkPrefix');

        $suffixType = $this->getSetting($form->submissionContext->getId(), 'arkSuffix');
        $pattern = '';
        if ($suffixType === 'default') {
            $pattern = '%j.v%vi%i.%a';
        } elseif ($suffixType === 'pattern') {
            $pattern = $this->getSetting($form->submissionContext->getId(), 'arkSubmissionSuffixPattern');
        }

        // If a pattern exists, use a DOI-like field to generate the ARK
        if ($pattern) {
            $fieldData = [
                'label' => __('plugins.pubIds.ark.displayName'),
                'value' => $form->publication->getData('pub-id::ark'),
                'prefix' => $prefix,
                'pattern' => $pattern,
                'contextInitials' => $form->submissionContext->getData('acronym', $form->submissionContext->getData('primaryLocale')) ?? '',
                'submissionId' => $form->publication->getData('submissionId'),
                'assignId' => __('plugins.pubIds.ark.editor.assignARK'),
                'clearId' => __('plugins.pubIds.ark.editor.clearObjectsARK'),
            ];
            if ($form->publication->getData('pub-id::publisher-id')) {
                $fieldData['publisherId'] = $form->publication->getData('pub-id::publisher-id');
            }
            if ($form->publication->getData('pages')) {
                $fieldData['pages'] = $form->publication->getData('pages');
            }
            if ($form->publication->getData('issueId')) {
                $issue = Services::get('issue')->get($form->publication->getData('issueId'));
                if ($issue) {
                    $fieldData['issueNumber'] = $issue->getNumber() ?? '';
                    $fieldData['issueVolume'] = $issue->getVolume() ?? '';
                    $fieldData['year'] = $issue->getYear() ?? '';
					$fieldData['issueId'] = $form->publication->getData('issueId') ?? '';
                }
            }
            if ($suffixType === 'default') {
                $fieldData['missingPartsLabel'] = __('plugins.pubIds.ark.editor.missingIssue');
            } else {
                $fieldData['missingPartsLabel'] = __('plugins.pubIds.ark.editor.missingParts');
            }
			$fieldData['separator'] = '/';
			$fieldData['assignIdLabel'] = __('plugins.pubIds.ark.editor.ark.assignArk');
			$fieldData['clearIdLabel'] = __('plugins.pubIds.ark.editor.ark.clearArk');
            $form->addField(new \PKP\components\forms\FieldPubId('pub-id::ark', $fieldData));

        // Otherwise add a field for manual entry that includes a button to generate
        // the check number
        } else {
            // Load the checkNumber.js file that is required for this field
            $this->addJavaScript(Application::get()->getRequest(), TemplateManager::getManager(Application::get()->getRequest()));

            $this->import('classes.form.FieldArk');
            $form->addField(new \Plugins\Generic\ARK\FieldArk('pub-id::ark', [
                'label' => __('plugins.pubIds.ark.displayName'),
                'description' => __('plugins.pubIds.ark.editor.ark.description', ['prefix' => $prefix]),
                'value' => $form->publication->getData('pub-id::ark'),
            ]));
        }
    }

    /**
     * Show ARK during final publish step
     *
     * @param $hookName string Form::config::before
     * @param $form FormComponent The form object
     */
    public function addPublishFormNotice($hookName, $form)
    {
        if ($form->id !== 'publish' || !empty($form->errors)) {
            return;
        }

        $submission = Services::get('submission')->get($form->publication->getData('submissionId'));
        $publicationArkEnabled = $this->getSetting($submission->getData('contextId'), 'enablePublicationARK');
        $galleyArkEnabled = $this->getSetting($submission->getData('contextId'), 'enableRepresentationARK');
        $warningIconHtml = '<span class="fa fa-exclamation-triangle pkpIcon--inline"></span>';

        if (!$publicationArkEnabled && !$galleyArkEnabled) {
            return;

        // Use a simplified view when only assigning to the publication
        } elseif (!$galleyArkEnabled) {
            if ($form->publication->getData('pub-id::ark')) {
                $msg = __('plugins.pubIds.ark.editor.preview.publication', ['ark' => $form->publication->getData('pub-id::ark')]);
            } else {
                $msg = '<div class="pkpNotification pkpNotification--warning">' . $warningIconHtml . __('plugins.pubIds.ark.editor.preview.publication.none') . '</div>';
            }
            $form->addField(new \PKP\components\forms\FieldHTML('ark', [
                'description' => $msg,
                'groupId' => 'default',
            ]));
            return;

        // Show a table if more than one ARK is going to be created
        } else {
            $arkTableRows = [];
            if ($publicationArkEnabled) {
                if ($form->publication->getData('pub-id::ark')) {
                    $arkTableRows[] = [$form->publication->getData('pub-id::ark'), 'Publication'];
                } else {
                    $arkTableRows[] = [$warningIconHtml . __('submission.status.unassigned'), 'Publication'];
                }
            }
            if ($galleyArkEnabled) {
                foreach ((array) $form->publication->getData('galleys') as $galley) {
                    if ($galley->getStoredPubId('ark')) {
                        $arkTableRows[] = [$galley->getStoredPubId('ark'), __('plugins.pubIds.ark.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
                    } else {
                        $arkTableRows[] = [$warningIconHtml . __('submission.status.unassigned'),__('plugins.pubIds.ark.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
                    }
                }
            }
            if (!empty($arkTableRows)) {
                $table = '<table class="pkpTable"><thead><tr>' .
                    '<th>' . __('plugins.pubIds.ark.displayName') . '</th>' .
                    '<th>' . __('plugins.pubIds.ark.editor.preview.objects') . '</th>' .
                    '</tr></thead><tbody>';
                foreach ($arkTableRows as $arkTableRow) {
                    $table .= '<tr><td>' . $arkTableRow[0] . '</td><td>' . $arkTableRow[1] . '</td></tr>';
                }
                $table .= '</tbody></table>';
            }
            $form->addField(new \PKP\components\forms\FieldHTML('ark', [
                'description' => $table,
                'groupId' => 'default',
            ]));
        }
    }

    /**
     * Load the FieldArk Vue.js component into Vue.js
     *
     * @param string $hookName
     * @param array $args
     */
    public function loadArkFieldComponent($hookName, $args)
    {
        $templateMgr = $args[0];
        $template = $args[1];

        if ($template !== 'workflow/workflow.tpl') {
            return;
        }

        $templateMgr->addJavaScript(
            'ark-field-component',
            Application::get()->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/js/FieldArk.js',
            [
                'contexts' => 'backend',
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );

        $templateMgr->addStyleSheet(
            'ark-field-component',
            '
				.pkpFormField--ark__input {
					display: inline-block;
				}

				.pkpFormField--ark__button {
					margin-left: 0.25rem;
					height: 2.5rem; // Match input height
				}
			',
            [
                'contexts' => 'backend',
                'inline' => true,
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );
    }
	
}


