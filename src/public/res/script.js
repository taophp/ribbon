$(function () {
    $("a[href^='http://']").click(function () {
        window.open(this.href);
        return false;
    });
    $("a[href^='https://']").click(function () {
        window.open(this.href);
        return false;
    });
    $('#searchbox').val('');
});

$('#searchbox').keyup(function (e) {
    if (e.which == 13) {
        e.preventDefault();
        return;
    }
    searchWords = $('#searchbox').val().split(' ');
    $("article").each(function(){
        searchableString = $(this).attr('data-searchable');
        found = true;
        for (i=0;i<searchWords.length;i++) {
            console.log(searchableString);
            console.log(searchWords[i]);
            console.log(searchableString.includes(searchWords[i]));
            if (!searchableString.includes(searchWords[i])) {
                found = false;
                break;
            }
        }
        if (found===true){
            $(this).fadeIn('fast');
        }else{
            $(this).fadeOut('fast');
        }
    });
});
