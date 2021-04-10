$('.category-badge').each(function() {
    $(this).css({
        color: $(this).attr('data-color'),
        borderColor: $(this).attr('data-color')
    });
});