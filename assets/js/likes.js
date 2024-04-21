$(document).ready(function() {

	$('.userLikesVideo').show();
	$('.userDoesNotLikeVideo').show();
	$('.noActionYet').show();
	// var str = '12';
	// var numb = parseInt(str);
	// console.log(numb);

	// var text = 'labas vakaras';
	// var rep = text.replace('vakaras', 'diena');
	// console.log(rep);

	$('.toggle-likes').on('click', function(e) {

		e.preventDefault();

		var $link = $(e.currentTarget);

		$.ajax({

			method: 'POST',
			url: $link.attr('href')

		}).done(function(data){

			switch (data.action)
			{
			case 'liked':

				var number_of_likes_str = $('.number-of-likes-' + data.id);
				// console.log(number_of_likes_str.html().replace(/[^0-9.]/g, ""));

				var number_of_likes = parseInt(number_of_likes_str.html()) + 1;

				number_of_likes_str.html(number_of_likes);
				$('.likes-video-id-'+data.id).show();
				$('.dislikes-video-id-'+data.id).hide();
				$('.video-id-'+data.id).hide();
				break;

			case 'disliked':

				var number_of_dislikes_str = $('.number-of-dislikes-' + data.id);
				// var number_of_dislikes = parseInt( number_of_dislikes_str.html().replace(/\D/g,'') ) + 1;
				var number_of_dislikes = parseInt(number_of_dislikes_str.html()) + 1;

				number_of_dislikes_str.html(number_of_dislikes);
				$('.likes-video-id-'+data.id).hide();
				$('.dislikes-video-id-'+data.id).show();
				$('.video-id-'+data.id).hide();
				break;

			case 'undo liked':

				var number_of_likes_str = $('.number-of-likes-' + data.id);
				var number_of_likes = parseInt(number_of_likes_str.html()) - 1;

				number_of_likes_str.html(number_of_likes);
				$('.likes-video-id-'+data.id).hide();
				$('.dislikes-video-id-'+data.id).hide();
				$('.video-id-'+data.id).show();
				break;

			case 'undo disliked':

				var number_of_dislikes_str = $('.number-of-dislikes-' + data.id);
				var number_of_dislikes = parseInt(number_of_dislikes_str.html()) - 1;

				number_of_dislikes_str.html(number_of_dislikes);
				$('.likes-video-id-'+data.id).hide();
				$('.dislikes-video-id-'+data.id).hide();
				$('.video-id-'+data.id).show();
				break;

			}			

		})

	});

});