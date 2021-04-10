$('.responsive-image').each(function() {
    $(this).css({
        backgroundImage: `url(${$(this).attr('data-image-url')})`,
        height: $(this).attr('data-height')
    });
});