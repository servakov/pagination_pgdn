// я подумал, что было бы здорово, нажимая PgDn в конце страницы, попадать на следующую нумерованную страницу
// https://habr.com/en/sandbox/77604/
$(document).keydown(function(e){  // обработчик на каждую нажатую кнопку
    var next = $(".pagination-next");  // находим <a> "туда"
    if (next && (e.which == 34 || e.which == 32 || e.which == 39) && ($(window).scrollTop() + $(window).height() == $(document).height())) {
        // "туда" найден && код клавиши 34 (PgDn) &&  пробел 32 && стрелка вправо	39 && позиция scroll в конце страницы
        // http://www.javascripter.net/faq/keycodes.htm
        next[0].click();  // жмём на <a>
    }

    var prev = $(".pagination-prev");  // находим <a> "обратно"
    if (prev && (e.which == 8 || e.which == 33  || e.which == 37) && ($(window).scrollTop() == 0 )) {
         // "обратно" найден && BACK_SPACE 	8 && код клавиши 33 (PgUp) &&  стрелка влево 37 && позиция scroll в конце страницы
        // http://www.javascripter.net/faq/keycodes.htm
        prev[0].click();  // жмём на <a>
    }
});

