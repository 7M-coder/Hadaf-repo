$(function() {

	'use strict';

	$('.confirm').click(function() {

		return confirm('Are You Sure ?');
	})

	// turn on select box it
  	$('select').selectBoxIt({

  		autoWidth: false

  	});

})