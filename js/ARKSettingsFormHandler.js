/**
 * @defgroup plugins_pubIds_ark_js
 */
/**
 * @file plugins/pubIds/ark/js/ARKSettingsFormHandler.js
 *
 * Copyright (c) 2021 Yasiel Pérez Vera
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ARKSettingsFormHandler.js
 * @ingroup plugins_pubIds_ark_js
 *
 * @brief Handle the ARK Settings form.
 */
(function($) {

	/** @type {Object} */
	$.pkp.plugins.pubIds.ark =
			$.pkp.plugins.pubIds.ark ||
			{ js: { } };



	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.form.AjaxFormHandler
	 *
	 * @param {jQueryObject} $form the wrapped HTML form element.
	 * @param {Object} options form options.
	 */
	$.pkp.plugins.pubIds.ark.js.ARKSettingsFormHandler =
			function($form, options) {

		this.parent($form, options);

		$(':radio, :checkbox', $form).click(
				this.callbackWrapper(this.updatePatternFormElementStatus_));
		//ping our handler to set the form's initial state.
		this.callbackWrapper(this.updatePatternFormElementStatus_());
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.plugins.pubIds.ark.js.ARKSettingsFormHandler,
			$.pkp.controllers.form.AjaxFormHandler);


	/**
	 * Callback to replace the element's content.
	 *
	 * @private
	 */
	$.pkp.plugins.pubIds.ark.js.ARKSettingsFormHandler.prototype.
			updatePatternFormElementStatus_ =
			function() {
		var $element = this.getHtmlElement(), pattern, $contentChoices;
		if ($('[id^="arkSuffix"]').filter(':checked').val() == 'pattern') {
			$contentChoices = $element.find(':checkbox');
			pattern = new RegExp('enable(.*)ARK');
			$contentChoices.each(function() {
				var patternCheckResult = pattern.exec($(this).attr('name')),
						$correspondingTextField = $element.find('[id*="' +
						patternCheckResult[1] + 'SuffixPattern"]').
						filter(':text');

				if (patternCheckResult !== null &&
						patternCheckResult[1] !== 'undefined') {
					if ($(this).is(':checked')) {
						$correspondingTextField.removeAttr('disabled');
					} else {
						$correspondingTextField.attr('disabled', 'disabled');
					}
				}
			});
		} else {
			$element.find('[id*="SuffixPattern"]').filter(':text').
					attr('disabled', 'disabled');
		}
	};

/** @param {jQuery} $ jQuery closure. */
}(jQuery));
