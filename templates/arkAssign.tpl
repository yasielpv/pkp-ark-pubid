{**
 * @file plugins/pubIds/ark/templates/arkAssign.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Assign ARK to an object option.
 *}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectARK value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`ARK")}
{if $enableObjectARK}
	{fbvFormArea id="pubIdARKFormArea" class="border" title="plugins.pubIds.ark.editor.ark"}
	{if $pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.assignARK.assigned" pubId=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}</p>
		{/fbvFormSection}
	{else}
		{assign var=pubId value=$pubIdPlugin->getPubId($pubObject)}
		{if !$canBeAssigned}
			{fbvFormSection}
				{if !$pubId}
					<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.assignARK.emptySuffix"}</p>
				{else}
					<p class="pkp_help">{translate key="plugins.pubIds.ark.editor.assignARK.pattern" pubId=$pubId}</p>
				{/if}
			{/fbvFormSection}
		{else}
			{assign var=templatePath value=$pubIdPlugin->getTemplateResource('arkAssignCheckBox.tpl')}
			{include file=$templatePath pubId=$pubId pubObjectType=$pubObjectType}
		{/if}
	{/if}
	{/fbvFormArea}
{/if}
