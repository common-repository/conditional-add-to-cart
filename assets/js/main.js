(function ($) {
	/**
	 * @var {object} catc_ajax
	 */

	bindSearchInputKeyUp();
	$(document).on('click', '.catcAddConditionGroup', handleAddConditionGroup);
	$(document).on('click', '.catcAddCondition', handleAddCondition);
	$(document).on('click', '.catcDeleteCondition', handleDeletionCondition);
	$(document).on('change', '.catcActions', handleChangeAction);
	$(document).on('change', '.catcConditionSelect', handleChangeCondition);

	// -------------------------------------------------------------------------
	//  Handle condition search type input
	// -------------------------------------------------------------------------

	function bindSearchInputKeyUp($parent) {
		var $element = $parent ? $parent.find('.catcConditionSearch') : $('.catcConditionSearch');
		if ($element.length === 0) return;
		$element.select2({
			placeholder: 'Search...',
			minimumInputLength: 2,
			width: 300,
			ajax: {
				url: catc_ajax.ajax_url,
				dataType: 'json',
				data: function (params) {
					return {
						type: "public",
						nonce: catc_ajax.nonce,
						action: 'conditions/searchOptions',
						keyword: params.term,
						selected_items: $element.val(),
						condition_slug: $element.data('condition-slug')
					};
				},
				processResults: function (response) {
					return {
						results: response.data
					}
				}
			}
		});
	}


	// -------------------------------------------------------------------------
	//  Add condition group
	// -------------------------------------------------------------------------

	function handleAddConditionGroup() {

		var $this = $(this);

		var requestPayload = {
			action: 'conditions/addConditionGroup',
			nonce: catc_ajax.nonce
		};
		$this.prop('disabled', true);
		$this.next('.spinner').addClass('is-active');

		$.get(catc_ajax.ajax_url, requestPayload, function (template) {
			var $group = $(template);
			$this.before($group);
			bindSearchInputKeyUp($group.find('.catcCondition'));

		}).always(function () {
			$this.prop('disabled', false);
			$this.next('.spinner').removeClass('is-active');
		});
	}


	// -------------------------------------------------------------------------
	//  Add single condition
	// -------------------------------------------------------------------------

	function handleAddCondition() {
		var $this = $(this);
		var $group = $this.closest('.catcConditionGroup');

		var data = {
			action: 'conditions/addCondition',
			groupId: $group.data('group-id'),
			nonce: catc_ajax.nonce
		};

		$this.prop('disabled', true);
		$this.next('.spinner').addClass('is-active');

		$.get(catc_ajax.ajax_url, data, function (template) {
			var $condition = $(template);
			$this.closest('.catcCondition').after($condition);
			bindSearchInputKeyUp($condition);

		}).always(function () {
			$this.prop('disabled', false);
			$this.next('.spinner').removeClass('is-active');
		});
	}


	function handleDeletionCondition() {
		var $this = $(this);
		var $group = $this.closest('.catcConditionGroup');
		if ($group.find('.catcCondition').length === 1) {
			$group.remove();
		} else {
			$this.closest('.catcCondition').remove();
		}
	}

	// -------------------------------------------------------------------------
	//  Change action
	// -------------------------------------------------------------------------

	function handleChangeAction() {
		var $this = $(this);
		var $wrapper = $this.closest('.catcActionsWrapper');
		var selectedActionValue = $this.val();
		$wrapper.find('[data-bind-to]').addClass('hidden');
		$wrapper.find('[data-bind-to="' + selectedActionValue + '"]').removeClass('hidden');
	}

	// -------------------------------------------------------------------------
	// Handle change condition
	// -------------------------------------------------------------------------
	// Reload condition row according to selected item.

	function handleChangeCondition() {
		var $this = $(this);
		var $condition = $this.closest('.catcCondition');
		var data = {
			action: 'conditions/changeCondition',
			conditionSlug: $this.val(),
			conditionId: $condition.data('condition-id'),
			groupId: $condition.closest('.catcConditionGroup').data('group-id'),
			nonce: catc_ajax.nonce
		};
		$condition.find(':input').attr('disabled', true);
		$.get(catc_ajax.ajax_url, data, function (template) {
			var $newCondition = $(template);
			$condition.replaceWith($newCondition);
			bindSearchInputKeyUp($newCondition);

		}).always(function () {
			$condition.find(':input').removeAttr('disabled');

		});
	}


}

)(jQuery);
