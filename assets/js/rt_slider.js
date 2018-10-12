jQuery(function ($) {
    var rt_slider = {
        init: function () {
            var settings = JSON.parse($("#rtsettings").attr("data-settings"));
            var status = $("#rtstatus").attr("data-status");
            var slide_to_show = settings.items == true ? settings.slide_to_show : 1;
            var slide_to_scroll = settings.items == true ? settings.slide_to_scroll : 1;
            var fade = settings.items == false && settings.fade != false ? true : false;
            var lazyload = settings.items == true && settings.lazy_load != false ? 'ondemand' : false;
            var center = settings.items == true && settings.center_mode != false ? true : false;
            if (status == "active") {
                $('.rt_slider').slick({
                    dots: settings.bullets,
                    arrows: settings.arrows,
                    infinite: true,
                    autoplay: settings.autoplay,
                    speed: settings.speed,
                    autoplaySpeed: 1000,
                    adaptiveHeight: true,
                    slidesToShow: slide_to_show,
                    slidesToScroll: slide_to_scroll,
                    centerMode: center,
                    centerPadding: '60px',
                    lazyLoad: lazyload,
                    fade: fade,
                    responsive: [{
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: slide_to_show,
                                slidesToScroll: slide_to_show,
                                centerMode: center,
                                centerPadding: '40px',
                                infinite: true,
                            }
                        }, {
                            breakpoint: 700,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                centerMode: center,
                                centerPadding: '40px',
                                infinite: true,
                                dots: false,
                            }
                        }, {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                centerMode: center,
                                centerPadding: '40px',
                                infinite: true,
                                dots: false,
                            }
                        }]
                });
                $('head').append('<style>.slick-prev:before, .slick-next:before{color: ' + settings.arrow_color + ' !important;}.slick-dots li.slick-active button:before,.slick-dots li button:before{color:'+settings.bullet_color+'}.rt_slider{width:' + settings.width + 'px;height:' + settings.height + 'px;}.slick-initialized .slick-slide{height:' + settings.height + 'px;}</style>');
            }
        }
    }
    rt_slider.init();

});


   