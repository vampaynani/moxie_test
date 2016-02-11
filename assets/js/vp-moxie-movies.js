Vue.transition('scaleIn', {
	css: false,
	enter: function(el, done) {
		TweenLite.from(el, 0.5, {x: 100, scale: 0, opacity: 0, onComplete: done});
	},
	enterCancelled: function(el){
		TweenLite.killAll();
	},
	leave: function(el, done){
		TweenLite.to(el, 0.5, {y: 10, opacity: 0, onComplete: done});
	},
	leaveCancelled: function(el){
		TweenLite.killAll();
	}
})
new Vue({
	el: '#vp-moxie-movies',
	data: {
		movies: []
	},
	error: null,
	ready: function() {
		this.$http({url: 'movies.json', method: 'GET'}).then(function (response){
			var li = document.querySelectorAll('#vp-moxie-movies-list li');
			console.log(li);
			if(response.data.length > 0){
				this.$set('movies', response.data);
			}else{
				this.$set('error', 'There are no movies available, come back later =)');
				TweenLite.from(this.$el, 0.4, {opacity: 0, y: -10, ease: Back.easeOut});
			};
		}, function(response){
			this.$set('error', 'There was an error getting the movies =(');
			TweenLite.from(this.$el, 0.4, {opacity: 0, y: -10, ease: Back.easeOut});
		});
	},
	template: '<div id="vp-moxie-movies">{{error}}<ul id="vp-moxie-movies-list"><li v-for="movie in movies" transition="scaleIn" stagger="50"><h1>{{movie.title}}</h1><img v-bind:src=movie.poster_url><p>{{movie.short_description}}</p><div class="footer"><span><i v-if="movie.rating >= 1" class="ion-ios-star"></i><i v-if="movie.rating >= 2" class="ion-ios-star"></i><i v-if="movie.rating >= 3" class="ion-ios-star"></i><i v-if="movie.rating >= 4" class="ion-ios-star"></i><i v-if="movie.rating >= 5" class="ion-ios-star"></i></span><span><i class="ion-film-marker"></i>{{movie.year}}</span></div></li></ul></div>'
})