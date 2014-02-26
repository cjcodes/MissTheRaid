moment.lang('en', {
    calendar : {
        lastDay : '[Yesterday]',
        sameDay : '[Today]',
        nextDay : '[Tomorrow]',
        lastWeek : '[last] dddd',
        nextWeek : 'dddd',
        sameElse : 'L'
    }
});

$('.pretty-date').each(function () {
    var d = moment($(this).text());
    $(this).text(d.calendar());
});