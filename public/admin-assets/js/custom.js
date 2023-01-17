// Notifications show more and less code here
$(".more").on('click', function () {
    var show_less = $(".more").attr('data'); //o for all and 1 for less
    var fields = $('.not-list-sett li');
    if (show_less == 0) {
        $(".more").text('Show less');
        $.each(fields, function (index, value) {
            if (index > 4) {
                $(this).css('display', 'block');
            }
        });
        $(".more").attr('data', 1);
    }
    else {
        $(".more").text('Show all');
        $.each(fields, function (index, value) {
            if (index > 4) {
                $(this).css('display', 'none');
            }
        });
        $(".more").attr('data', 0);
    }
    return false;
});


