$(document)	.ready(function() {

	$('tbody.construct').find('tr').each(function() {
		var type = $(this).find('.type').val();
		convert($(this).find('.default'), type);
	});

	$('select.type').each(function() {
		showButton($(this));
	});

	$('input.calculate').each(function() {
		showButton($(this));
	});
});

function showButton(el) {
	if(el.val().substring(0, 4) == 'enum') {
		el.closest('td').append('<button type="button" class="btn btn-light btn-sm openEnum" data-toggle="modal" data-target="#enum">...</button>');
	} else {
		el.closest('td').find('button').remove();
	}
	if(el.prop('checked')) {
		el.closest('label').append('<button type="button" class="btn btn-light btn-sm openCalculate" data-toggle="modal" data-target="#calculate">...</button>');
	} else {
		el.closest('label').find('button').remove();
	}
}

function convert(el, type) {
	let value = getValue(el);
	var cs = type;
	let temp = type;
	if(temp.substring(0, 4) == 'enum') {
		cs = 'enum';
	}
	switch (cs) {
		case 'date':
			el.html('<input type="date" class="form-control" value="' + value + '">');
			break;
		case 'int(11)':
			el.html('<input type="number" class="form-control" value="' + value + '">');
			break;
		case 'float':
			el.html('<input type="number" step="0.01" class="form-control" value="' + value + '">');
			break;
		case 'tinyint(1)':
			el.html('<input type="checkbox" class="form-control" value="1"' + (value ? 'checked' : '') + '>');
			break;
		case 'enum':
			var html = ('<select class="form-control"><option value="">*не выбрано*</option>');
			var arr = type.substring(5, type.length - 1).split(',');
			for(i = 0; i < arr.length; i++) {
				let option = arr[i].substring(1, arr[i].length - 1);
				html += '<option' + (option == value ? ' selected ' : '') + ' value="' + option + '">' + option + '</option>';
			}
			html += '</select>';
			el.html(html);
			break;
		default: 
			el.html('<input type="text" autocomplete="off" class="form-control" value="' + value + '">');
			break;
	}
}

function getValue(td) {
	if(td.find('input').length) {
		if(td.find('input').attr('type') == 'checkbox' && !td.find('input').prop('checked'))
			return '';
		return td.find('input').val();
	}
	if(td.find('select').length) {
		return td.find('select').val();
	}
	return td.html();
}

function addRow() {
	if(!$('tr.empty').length) {
		var tr = $('tbody.data').find('tr').last().clone();
		tr.find('td').each(function() {
			if($(this).find('input').length) {
				$(this).find('input').val('');
			} else if ($(this).find('select').length) {
				$(this).find('select').prop('selectedIndex', 0);
			} else {
				$(this).html('');
			}
		});
		tr.addClass('empty');
		tr.removeClass('edited');
		tr.attr('data-id', 0);
		$('tbody.data').append(tr);
	}
}







	$('#addfield').click(function() {
		var tr = $('tbody').find('tr').last().clone();
		var count = $('tbody').find('tr').length;
		tr.find('.old').val('');
		tr.find('.old').attr('name', 'col[' + count + '][old]');
		tr.find('.name').val('');
		tr.find('.name').attr('name', 'col[' + count + '][name]');
		tr.find('.type').prop('selectedIndex', 0);
		tr.find('.type').attr('name', 'col[' + count + '][type]');
		tr.find('.required').prop('checked', false);
		tr.find('.required').attr('name', 'col[' + count + '][required]');
		tr.find('.key').prop('checked', false);
		tr.find('.key').attr('name', 'col[' + count + '][key]');
		tr.find('.ai').prop('checked', false);
		tr.find('.ai').attr('name', 'col[' + count + '][ai]');
		tr.find('.calculate').prop('checked', false);
		tr.find('.calculate').attr('name', 'col[' + count + '][formula]');
		tr.find('.default').html('');
		tr.find('.default_value').val('');
		tr.find('.default_value').attr('name', 'col[' + count + '][default]');
		showButton(tr.find('.type'));
		showButton(tr.find('.calculate'));
		var type = tr.find('.type').val();
		convert(tr.find('.default'), type);
		$('tbody').append(tr);
	});

	$('tbody').on('click', 'button.drop', function() {
		$(this).closest('tr').remove();
	});

	$('#save').click(function() {
		var data = {};
		data['db'] = $('tbody.data').data('db');
		data['tbl'] = $('tbody.data').data('tbl');
		data['pk'] = $('tbody.data').data('pk');
		data['rows'] = [];
		$('tbody.data tr.edited').each(function() {
			var row = {};
			row['id'] = $(this).data('id');
			row['cols'] = {};
			$(this).find('td[class]').each(function() {
				row['cols'][$(this).attr('class')] = getValue($(this));
			});
			data['rows'].push(row);
		});
		$.ajax({
			url: '/tbl/update.php',
			method: 'post',
			data: JSON.stringify(data),
			success: function(response) {
				location.reload();
				///console.log(response);
			}
		});
	});

	$('tbody.data').on('click', 'button.deleteRow', function() {
		var tr = $(this).closest('tr');
		if(tr.data('id')) {
			var data = {};
			data['db'] = $('tbody.data').data('db');
			data['tbl'] = $('tbody.data').data('tbl');
			data['pk'] = $('tbody.data').data('pk');
			data['id'] = tr.data('id');
			$.ajax({
				url: '/tbl/delete.php',
				method: 'post',
				data: JSON.stringify(data),
				success: function(response) {
					tr.remove();
				}
			});
		} else {
			tr.remove();
		}
	});

	$('tbody.data').on('input', '[contenteditable]', function() {
		$(this).closest('tr').addClass('edited');
		$(this).closest('tr').removeClass('empty');
		addRow();
	});

	$('tbody.data').on('change', 'input', function() {
		$(this).closest('tr').addClass('edited');
		$(this).closest('tr').removeClass('empty');
		addRow();
	});

	$('tbody.data').on('change', 'select', function() {
		$(this).closest('tr').addClass('edited');
		$(this).closest('tr').removeClass('empty');
		addRow();
	});

	$('tbody.construct').on('change', '.ai', function() {
		if($(this).prop('checked')) {
			$('.ai').each(function() {
				$(this).prop('checked', false);
			});
			$(this).prop('checked', true);
			$(this).closest('tr').find('.key').prop('checked', true);
		}
	});

	$('tbody.construct').on('change', '.key', function() {
		if($(this).closest('tr').find('.ai').prop('checked')) {
			$(this).prop('checked', true);
		}
	});

	$('tbody.construct').on('input', '.default', function() {
		$(this).parent().find('.default_value').val(getValue($(this)));
	});

	$('tbody.construct').on('change', '.default input', function() {
		$(this).parent().find('.default_value').val($(this).val());
	});

	$('tbody.construct').on('change', '.type', function() {
		let type = $(this).val().substring(0, 4);
		if(type == 'enum') {
			$(this).closest('td').append('<button type="button" class="btn btn-light" data-toggle="modal" data-target="#enum">...</button>');
			$(this).addClass('current');
			$('.current option:selected').val('enum()');
		} else {
			$(this).closest('td').find('button').remove();
		}
		convert($(this).closest('tr').find('.default'), $(this).val());
	});

	$('#saveEnum').click(function() {
		var vars = $('#enum textarea').val().split("\n");
		for(i = 0; i < vars.length; i++) {
			vars[i] = "'" + vars[i] + "'";
		};
		$('.current option:selected').val('enum(' + vars.join(',') + ')');
		convert($('.current').closest('tr').find('.default'), $('.current').val());
	});

	$('tbody.construct').on('click', '.openEnum', function() {
		var str = $(this).closest('td').find('.type').val();
		var arr = str.substring(5, str.length - 1).split(',');
		for(i = 0; i < arr.length; i++) {
			arr[i] = arr[i].substring(1, arr[i].length - 1);
		}
		$('#enum textarea').val(arr.join("\n"));
		$(this).closest('td').find('.type').addClass('current');
	});
	$('input[type="radio"]:checked').click(function() {
		$(this).prop('checked', false);
		this.form.submit();
	});

	$('form').on('input blur', '.nospace', function() {
		$(this).val($(this).val().replace(/\s+/g, ''));
	});

	$('tbody.construct').on('change', '.calculate', function() {
		if($(this).prop('checked')) {
			$(this).closest('label').append('<button type="button" class="btn btn-light btn-sm openCalculate" data-toggle="modal" data-target="#calculate">...</button>');
		} else {
			$(this).closest('label').find('button').remove();
		}
	});

	$('tbody.construct').on('click', '.openCalculate', function() {
		$('.calculated').removeClass('calculated');
		$(this).closest('td').find('input.calculate').addClass('calculated');
		$('#calculate textarea').val($('.calculated').attr('value') ? $('.calculated').val() : '');
	});

	$('#saveFormula').click(function() {
		var fields = [];
		$('.construct .name').each(function() {
			fields.push($(this).val());
		});
		mystring = $('#calculate textarea').val().replace(/[\-\+\/\*\(\)]/g, " ");
		mystring = mystring.replace(/\s+/g, ' ');
		var arr = mystring.split(' ');
		var valid = true;
		for(var i = 0; i < arr.length; i++) {
			if(!$.isNumeric(arr[i]) && $.inArray(arr[i], fields) < 0) {
				$('.error').show();
				$('[type="submit"]').attr('disabled', 'disabled');
				valid = false;
			}
		}
		if(valid) {
			$('.error').hide();
			$('[type="submit"]').removeAttr('disabled');
			$('#calculate').modal('hide');
			$('.calculated').val($('#calculate textarea').val());
		}
	});

	$('#showData').click(function() {
		$('#filter_form').attr('action', '');
	});
	$('#exportData').click(function() {
		$('#filter_form').attr('action', '/tbl/export.php');
	});