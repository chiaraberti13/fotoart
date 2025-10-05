(function ($) {
    $.fn.imageLens = function (options) {
        var defaults = {
            lensSize: 100,
            borderSize: 4,
            borderColor: "#888"
        };

        options = $.extend(defaults, options);

        var lensRadius = options.lensSize / 2 + options.borderSize,
        br = "border-radius:",
        lensStyle = "width:" + options.lensSize + "px;height:" + options.lensSize + "px;display: none;"
            + "-moz-" + br + lensRadius + "px;"    // <FF4
            + "-webkit-" + br + lensRadius + "px;" // Older webkit versions
            + br + lensRadius + "px;"
            + "border:" + options.borderSize + "px solid " + options.borderColor  + ";"
            + "background-repeat:no-repeat;position:absolute;z-index:9999";

        return this.each(function () {
            var img = $(this).wrap("<span></span>"),
                width = img.width(),
                height = img.height(),
                setPosition = function ( e ) {
                    var offset = img.offset(),
                        x = (e.pageX - offset.left),
                        y = (e.pageY - offset.top),
						m = 'px ',
                        bgLeftPos = -(x * widthRatio - lRadius),
                        bgTopPos = -(y * heightRatio - lRadius);

                    lens.css({
                        left: x - lRadius,
                        top: y - lRadius,
                        backgroundPosition: bgLeftPos + m + bgTopPos + m,
                        cursor: 'none',
                        display: x < 0 || y < 0 || x > width || y > height ? 'none' : ''
                    });
                },
                parent = img.parent().css({position:"relative",display: "inline-block", width: width,height: height}).mousemove(setPosition),
                imageSrc = options.imageSrc || $(this).attr("src"),
                bigImg = new Image(),
                lens = $('<div style="' + lensStyle + '">&nbsp;</div>').appendTo(parent).addClass(options.lensCss).css({ backgroundImage: "url('" + imageSrc + "')" }),
                widthRatio, heightRatio,
                lRadius = options.lensSize / 2; //Lens Radius

            bigImg.onload = function() {
                widthRatio = this.width / width;
                heightRatio = this.height / height;
            };
            bigImg.src = imageSrc;

        });
    };
})(jQuery);