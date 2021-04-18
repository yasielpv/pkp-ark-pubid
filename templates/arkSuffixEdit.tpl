{**
 * plugins/pubIds/ark/templates/arkSuffixEdit.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Edit custom ARK suffix for an object (issue, submission, file)
 *
 *}
{load_script context="publicIdentifiersForm" scripts=$scripts}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectARK value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`ARK")}
{if $enableObjectARK}
	{assign var=storedPubId value=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
	{fbvFormArea id="pubIdARKFormArea" class="border" title="plugins.pubIds.ark.editor.ark"}
		{assign var=formArea value=true}
		{if $pubIdPlugin->getSetting($currentJournal->getId(), 'arkSuffix') == 'customId' || $storedPubId}
			{if empty($storedPubId)} {* edit custom suffix *}
				{fbvFormSection}
					<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.arkSuffix.description"}</p>
					{fbvElement type="text" label="plugins.pubIds.ark.manager.settings.arkPrefix" id="arkPrefix" disabled=true value=$pubIdPlugin->getSetting($currentContext->getId(), 'arkPrefix') size=$fbvStyles.size.SMALL inline=true }
					{fbvElement type="text" label="plugins.pubIds.ark.manager.settings.arkSuffix" id="arkSuffix" value=$arkSuffix size=$fbvStyles.size.MEDIUM inline=true }
				{/fbvFormSection}
				{if $canBeAssigned}
					<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.canBeAssigned"}</p>
					{assign var=templatePath value=$pubIdPlugin->getTemplateResource('arkAssignCheckBox.tpl')}
					{include file=$templatePath pubId=$pubIdPlugin->getPubId($pubObject) pubObjectType=$pubObjectType}
				{else}
					<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.customSuffixMissing"}</p>
				{/if}
			{else} {* stored pub id and clear option *}
				<p>
					{$storedPubId|escape}<br />
					{capture assign=translatedObjectType}{translate key="plugins.pubIds.ark.editor.arkObjectType"|cat:$pubObjectType}{/capture}
					{capture assign=assignedMessage}{translate key="plugins.pubIds.ark.editor.assigned" pubObjectType=$translatedObjectType}{/capture}
					<p class="pkp_help">{$assignedMessage}</p>
					{include file="linkAction/linkAction.tpl" action=$clearPubIdLinkActionARK contextId="publicIdentifiersForm"}
				</p>
			{/if}
		{else} {* pub id preview *}
			<p>{$pubIdPlugin->getPubId($pubObject)|escape}</p>
			{if $canBeAssigned}
				<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.canBeAssigned"}</p>
				{assign var=templatePath value=$pubIdPlugin->getTemplateResource('arkAssignCheckBox.tpl')}
				{include file=$templatePath pubId=$pubIdPlugin->getPubId($pubObject) pubObjectType=$pubObjectType}
			{else}
				<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.patternNotResolved"}</p>
			{/if}
		{/if}
	{/fbvFormArea}
{/if}
{* issue pub object *}
{if $pubObjectType == 'Issue'}
	{assign var=enableSubmissionARK value=$pubIdPlugin->getSetting($currentContext->getId(), "enableSubmissionARK")}
	{assign var=enableRepresentationARK value=$pubIdPlugin->getSetting($currentContext->getId(), "enableRepresentationARK")}
	{if $enableSubmissionARK || $enableRepresentationARK}
		{if !$formArea}
			{assign var="formAreaTitle" value="plugins.pubIds.ark.editor.ark"}
		{else}
			{assign var="formAreaTitle" value=""}
		{/if}
		{fbvFormArea id="pubIdARKIssueobjectsFormArea" class="border" title=$formAreaTitle}
			{fbvFormSection list="true" description="plugins.pubIds.ark.editor.clearIssueObjectsARK.description"}
				{include file="linkAction/linkAction.tpl" action=$clearIssueObjectsPubIdsLinkActionARK contextId="publicIdentifiersForm"}
			{/fbvFormSection}
		{/fbvFormArea}
	{/if}
{/if}
