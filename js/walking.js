//перемещение между ячейками
	var rowindex = 0;
	var colindex = 0;

	$('tbody').on('focus', 'td[contenteditable]', function(){
		rowindex = $(this).closest('tr').index();
		colindex = $(this).closest('tr').find('td[contenteditable]').index($(this));
	});
	$(window).keydown(function(e) {
		if(e.keyCode == 40) { //to down
			let tr = $('tbody').find('tr').eq(++rowindex).find('td[contenteditable]').eq(colindex).focus();
		}
		if(e.keyCode == 38) { //to up
			let tr = $('tbody').find('tr').eq(--rowindex).find('td[contenteditable]').eq(colindex).focus();
		}
		if(e.keyCode == 39) { //to right
			let tr = $('tbody').find('tr').eq(rowindex).find('td[contenteditable]').eq(++colindex).focus();
		}
		if(e.keyCode == 37) { //to left
			let tr = $('tbody').find('tr').eq(rowindex).find('td[contenteditable]').eq(--colindex).focus();
		}
	});