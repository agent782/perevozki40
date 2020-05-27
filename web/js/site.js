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
        document.getElementById("menu").style.top = "-10px";
    } else {
        document.getElementById("menu-mobile").style.top = "-200px";
        document.getElementById("menu").style.top = "-200px";
    }
    prevScrollpos = currentScrollPos;
}

