<script>
	var mySwiper = new Swiper('.swiper-container', {
    	speed: 400,
    	spaceBetween: 50,
    	scrollbar: '.swiper-scrollbar',
    	nextButton: '.swiper-button-next',
    	prevButton: '.swiper-button-prev',
    	keyboardControl: true,
        scrollbarDraggable: true,
        scrollbarSnapOnRelease: true,
        preloadImage: true,
        slidesPerView: 'auto',
        lazyLoading: true
	});
	
	function swiperUpdate() {
		$$('.popup-swiper').on('open', function () {
		var mySwiper = $$('.swiper-container')[0].swiper;
		mySwiper.update();
		});
	}

</script>