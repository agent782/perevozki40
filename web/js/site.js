//Индикация загрузки
function endLoading() {
    $('#loader').hide();
}

function startLoading() {
    $('#loader').show();
}

var prevScrollpos = window.pageYOffset;
window.onscroll = function() {
    var currentScrollPos = window.pageYOffset;
    if (prevScrollpos > currentScrollPos) {
        document.getElementById("menu-mobile").style.top = "-10px";
    } else {
        document.getElementById("menu-mobile").style.top = "-200px";
    }
    prevScrollpos = currentScrollPos;
}

$(document).on('beforeSubmit', 'form', function(event) {
    $(this).find('[type=submit]').attr('disabled', true).addClass('disabled');
});

// $(function () {
//     $(":submit").on("click", function(){
        // $(this).attr("disabled", "disabled");
        // startLoading();
    // })
// })


