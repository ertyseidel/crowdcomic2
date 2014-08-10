;(function(exports) {

	var $element = null;

	exports.Modal = function() {
	};

	exports.Modal.prototype = {
		render: function(contents) {
			this.$element = $(templates.modal({contents: contents}));
			$('body').append(this.$element);
			this.$element.css({
				'height' : $(document).height() + 'px'
			});
			this.$element.show();
			this.$element.click(this.outerClose.bind(this));
			this.$element.find('.modal-close').click(this.close.bind(this));
		},
		outerClose: function(evt) {
			if (evt.target != this.$element.get()[0]) return;
			this.close();
		},
		close: function(evt) {
			this.$element.remove();
		}
	};

})(window);
