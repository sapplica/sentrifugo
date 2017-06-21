/* http://keith-wood.name/maxlength.html
   Textarea Max Length for jQuery v2.0.0.
   Written by Keith Wood (kwood{at}iinet.com.au) May 2009.
   Licensed under the MIT (https://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt) license. 
   Please attribute the author if you use it. */

(function($) { // hide the namespace

	var pluginName = 'maxlength';

	/** Create the maxlength plugin.
		<p>Sets a textarea to limit the number of characters that may be entered.</p>
		<p>Expects HTML like:</p>
		<pre>&lt;textarea></textarea>
		<p>Provide inline configuration like:</p>
		<pre>&lt;textarea data-maxlength="name: 'value'">&lt;/textarea></pre>
	 	@module MaxLength
		@augments JQPlugin
		@example $(selector).maxlength() */
	$.JQPlugin.createPlugin({
	
		/** The name of the plugin. */
		name: pluginName,
			
		/** Maxlength full callback.
			Triggered when the text area is full or overflowing.
			@callback fullCallback
			@param overflowing {boolean} True if overflowing, false if not.
			@example onFull: function(overflowing) {
	$(this).addClass(overflowing ? 'overflow' : 'full');
} */
			
		/** Default settings for the plugin.
			@property [max=200] {number} Maximum length.
			@property [truncate=true] {boolean} True to disallow further input, false to highlight only.
			@property [showFeedback=true] {boolean} True to always show user feedback, 'active' for hover/focus only.
			@property [feedbackTarget=null] {string|Element|jQuery|function} jQuery selector, element,
				or jQuery object, or function for element to fill with feedback.
			@property [onFull=null] {fullCallback} Callback when full or overflowing. */
		defaultOptions: {
			max: 200,
			truncate: true,
			showFeedback: true,
			feedbackTarget: null,
			onFull: null
		},

		/** Localisations for the plugin.
			Entries are objects indexed by the language code ('' being the default US/English).
			Each object has the following attributes.
			@property [feedbackText='{r}&nbsp;characters&nbsp;remaining&nbsp;({m}&nbsp;maximum)'] {string}
				Display text for feedback message, use {r} for remaining characters,
				{c} for characters entered, {m} for maximum.
			@property [overflowText='{o} characters too many ({m} maximum)'] {string}
				Display text when past maximum, use substitutions above and {o} for characters past maximum. */
		regionalOptions: { // Available regional settings, indexed by language/country code
			'': { // Default regional settings - English/US
				feedbackText: '{r} characters remaining ({m} maximum)',
				overflowText: '{o} characters too many ({m} maximum)'
			}
		},
		
		/** Names of getter methods - those that can't be chained. */
		_getters: ['curLength'],

		_feedbackClass: pluginName + '-feedback', //Class name for the feedback section
		_fullClass: pluginName + '-full', // Class name for indicating the textarea is full
		_overflowClass: pluginName + '-overflow', // Class name for indicating the textarea is overflowing
		_disabledClass: pluginName + '-disabled', // Class name for indicating the textarea is disabled

		_instSettings: function(elem, options) {
			return {feedbackTarget: $([])};
		},

		_postAttach: function(elem, inst) {
			elem.on('keypress.' + inst.name, function(event) {
					if (!inst.options.truncate) {
						return true;
					}
					var ch = String.fromCharCode(
						event.charCode == undefined ? event.keyCode : event.charCode);
					return (event.ctrlKey || event.metaKey || ch == '\u0000' ||
						$(this).val().length < inst.options.max);
				}).
				on('keyup.' + inst.name, function() { $.maxlength._checkLength(elem); });
		},

		_optionsChanged: function(elem, inst, options) {
			$.extend(inst.options, options);
			if (inst.feedbackTarget.length > 0) { // Remove old feedback element
				if (inst.hadFeedbackTarget) {
					inst.feedbackTarget.empty().val('').
						removeClass(this._feedbackClass + ' ' + this._fullClass + ' ' + this._overflowClass);
				}
				else {
					inst.feedbackTarget.remove();
				}
				inst.feedbackTarget = $([]);
			}
			if (inst.options.showFeedback) { // Add new feedback element
				inst.hadFeedbackTarget = !!inst.options.feedbackTarget;
				if ($.isFunction(inst.options.feedbackTarget)) {
					inst.feedbackTarget = inst.options.feedbackTarget.apply(elem[0], []);
				}
				else if (inst.options.feedbackTarget) {
					inst.feedbackTarget = $(inst.options.feedbackTarget);
				}
				else {/*
                                    var ele_name = $(elem).prop('name');
                                    if(ele_name == 'executor_comments')
					inst.feedbackTarget = $('<div></div>').insertAfter(elem);
                                    else */
                                        inst.feedbackTarget = $('<span></span>').insertAfter(elem);
				}
				inst.feedbackTarget.addClass(this._feedbackClass);
			}
			elem.off('mouseover.' + inst.name + ' focus.' + inst.name +
				'mouseout.' + inst.name + ' blur.' + inst.name);
			if (inst.options.showFeedback == 'active') { // Additional event handlers
				elem.on('mouseover.' + inst.name, function() {
						inst.feedbackTarget.css('visibility', 'visible');
					}).on('mouseout.' + inst.name, function() {
						if (!inst.focussed) {
							inst.feedbackTarget.css('visibility', 'hidden');
						}
					}).on('focus.' + inst.name, function() {
						inst.focussed = true;
						inst.feedbackTarget.css('visibility', 'visible');
					}).on('blur.' + inst.name, function() {
						inst.focussed = false;
						inst.feedbackTarget.css('visibility', 'hidden');
					});
				inst.feedbackTarget.css('visibility', 'hidden');
			}
			this._checkLength(elem);
		},

		/** Retrieve the counts of characters used and remaining.
			@param elem {jQuery} The control to check.
			@return {object} The current counts with attributes used and remaining.
			@example var lengths = $(selector).maxlength('curLength'); */
		curLength: function(elem) {
			var inst = this._getInst(elem);
			var value = elem.val();
			var len = value.replace(/\r\n/g, '~~').replace(/\n/g, '~~').length;
			return {used: len, remaining: inst.options.max - len};
		},

		/** Check the length of the text and notify accordingly.
			@private
			@param elem {jQuery} The control to check. */
		_checkLength: function(elem) {
			var inst = this._getInst(elem);
			var value = elem.val();
			var len = value.replace(/\r\n/g, '~~').replace(/\n/g, '~~').length;
			elem.toggleClass(this._fullClass, len >= inst.options.max).
			toggleClass(this._overflowClass, len > inst.options.max);
			if (len > inst.options.max && inst.options.truncate) { // Truncation
				var lines = elem.val().split(/\r\n|\n/);
				value = '';
				var i = 0;
				while (value.length < inst.options.max && i < lines.length) {
					value += lines[i].substring(0, inst.options.max - value.length) + '\r\n';
					i++;
				}
				elem.val(value.substring(0, inst.options.max));
				elem[0].scrollTop = elem[0].scrollHeight; // Scroll to bottom
				len = inst.options.max;
			}
			inst.feedbackTarget.toggleClass(this._fullClass, len >= inst.options.max).
				toggleClass(this._overflowClass, len > inst.options.max);
			var feedback = (len > inst.options.max ? // Feedback
				inst.options.overflowText : inst.options.feedbackText).
					replace(/\{c\}/, len).replace(/\{m\}/, inst.options.max).
					replace(/\{r\}/, inst.options.max - len).
					replace(/\{o\}/, len - inst.options.max);
			try {
				inst.feedbackTarget.text(feedback);
			}
			catch(e) {
				// Ignore
			}
			try {
				inst.feedbackTarget.val(feedback);
			}
			catch(e) {
				// Ignore
			}
			if (len >= inst.options.max && $.isFunction(inst.options.onFull)) {
				inst.options.onFull.apply(elem, [len > inst.options.max]);
			}
		},

		/** Enable the control.
			@param elem {Element} The control to affect.
			@example $(selector).maxlength('enable'); */
		enable: function(elem) {
			elem = $(elem);
			if (!elem.hasClass(this._getMarker())) {
				return;
			}
			var inst = this._getInst(elem);
			elem.prop('disabled', false).removeClass(inst.name + '-disabled');
			inst.feedbackTarget.removeClass(inst.name + '-disabled');
		},

		/** Disable the control.
			@param elem {Element} The control to affect.
			@example $(selector).maxlength('disable'); */
		disable: function(elem) {
			elem = $(elem);
			if (!elem.hasClass(this._getMarker())) {
				return;
			}
			var inst = this._getInst(elem);
			elem.prop('disabled', true).addClass(inst.name + '-disabled');
			inst.feedbackTarget.addClass(inst.name + '-disabled');
		},

		_preDestroy: function(elem, inst) {
			if (inst.feedbackTarget.length > 0) {
				if (inst.hadFeedbackTarget) {
					inst.feedbackTarget.empty().val('').css('visibility', 'visible').
						removeClass(this._feedbackClass + ' ' + this._fullClass + ' ' + this._overflowClass);
				}
				else {
					inst.feedbackTarget.remove();
				}
			}
			elem.removeClass(this._fullClass + ' ' + this._overflowClass).off('.' + inst.name);
		}
	});

})(jQuery);
