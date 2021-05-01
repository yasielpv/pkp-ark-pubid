{**
 * plugins/pubIds/ark/templates/settingsForm.tpl
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ARK plugin settings
 *
 *}

<div id="description">{translate key="plugins.pubIds.ark.manager.settings.description"}</div>

<script src="{$baseUrl}/plugins/pubIds/ark/js/ARKSettingsFormHandler.js"></script>
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#arkSettingsForm').pkpHandler('$.pkp.plugins.pubIds.ark.js.ARKSettingsFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="arkSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="pubIds" plugin=$pluginName verb="save"}">
	{csrf}
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="arkObjectsFormArea" title="plugins.pubIds.ark.manager.settings.arkObjects"}
		<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.explainARKs"}</p>
		{fbvFormSection list="true"}
			{fbvElement type="checkbox" label="plugins.pubIds.ark.manager.settings.enableIssueARK" id="enableIssueARK" maxlength="40" checked=$enableIssueARK|compare:true}
			{fbvElement type="checkbox" label="plugins.pubIds.ark.manager.settings.enablePublicationARK" id="enablePublicationARK" maxlength="40" checked=$enablePublicationARK|compare:true}
			{fbvElement type="checkbox" label="plugins.pubIds.ark.manager.settings.enableRepresentationARK" id="enableRepresentationARK" maxlength="40" checked=$enableRepresentationARK|compare:true}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="arkPrefixFormArea" title="plugins.pubIds.ark.manager.settings.arkPrefix"}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.arkPrefix.description"}</p>
			{fbvElement type="text" id="arkPrefix" value=$arkPrefix required="true" label="plugins.pubIds.ark.manager.settings.arkPrefix" maxlength="40" size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="arkSuffixFormArea" title="plugins.pubIds.ark.manager.settings.arkSuffix"}
		<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.arkSuffix.description"}</p>
		{fbvFormSection list="true"}
			{if !in_array($arkSuffix, array("pattern", "customId"))}
				{assign var="checked" value=true}
			{else}
				{assign var="checked" value=false}
			{/if}
			{fbvElement type="radio" id="arkSuffixDefault" name="arkSuffix" value="default" label="plugins.pubIds.ark.manager.settings.arkSuffixDefault" checked=$checked}
			<span class="instruct">{translate key="plugins.pubIds.ark.manager.settings.arkSuffixDefault.description"}</span>
		{/fbvFormSection}
		{fbvFormSection list="true"}
			{fbvElement type="radio" id="arkSuffixCustomId" name="arkSuffix" value="customId" label="plugins.pubIds.ark.manager.settings.arkSuffixCustomIdentifier" checked=$arkSuffix|compare:"customId"}
		{/fbvFormSection}
		{fbvFormSection list="true"}
			{fbvElement type="radio" id="arkSuffixPattern" name="arkSuffix" value="pattern" label="plugins.pubIds.ark.manager.settings.arkSuffixPattern" checked=$arkSuffix|compare:"pattern"}
			<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.arkSuffixPattern.example"}</p>
			{fbvElement type="text" label="plugins.pubIds.ark.manager.settings.arkSuffixPattern.issues" id="arkIssueSuffixPattern" value=$arkIssueSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.pubIds.ark.manager.settings.arkSuffixPattern.submissions" id="arkPublicationSuffixPattern" value=$arkPublicationSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="text" label="plugins.pubIds.ark.manager.settings.arkSuffixPattern.representations" id="arkRepresentationSuffixPattern" value=$arkRepresentationSuffixPattern maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="arkResolverFormArea" title="plugins.pubIds.ark.manager.settings.arkResolver"}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.ark.manager.settings.arkResolver.description"}</p>
			{fbvElement type="text" id="arkResolver" value=$arkResolver required="true" label="plugins.pubIds.ark.manager.settings.arkResolver"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="arkReassignFormArea" title="plugins.pubIds.ark.manager.settings.arkReassign"}
		{fbvFormSection}
			<div class="instruct">{translate key="plugins.pubIds.ark.manager.settings.arkReassign.description"}</div>
			{include file="linkAction/linkAction.tpl" action=$clearPubIdsLinkAction contextId="arkSettingsForm"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons submitText="common.save"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
