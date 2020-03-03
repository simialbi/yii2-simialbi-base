window.yii.sa = (function ($) {
	var pub = {
		isActive: true,

		init: function () {
			initDynamicModal();
		}
	};

	function initDynamicModal() {
		$('#dynamicModal').on('show.bs.modal', function (evt) {
			var link = $(evt.relatedTarget);
			var href = link.prop('href');

			var modal = $(this);
			modal.find('.modal-content').load(href);
		});
	}

	return pub;
})(jQuery);

window.jQuery(function () {
	window.yii.initModule(window.yii.sa);
});