
/*!
* Start Bootstrap - Clean Blog v6.0.7 (https://startbootstrap.com/theme/clean-blog)
* Copyright 2013-2021 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-clean-blog/blob/master/LICENSE)
*/
/* **************** JS HERITER DU THEME BOOTSTRAP ************************* */
window.addEventListener('DOMContentLoaded', () => {
    let scrollPos = 0;
    const mainNav = document.getElementById('mainNav');
    const headerHeight = mainNav.clientHeight;
    window.addEventListener('scroll', function() {
        const currentTop = document.body.getBoundingClientRect().top * -1;
        if ( currentTop < scrollPos) {
            // Scrolling Up
            if (currentTop > 0 && mainNav.classList.contains('is-fixed')) {
                mainNav.classList.add('is-visible');
            } else {
                console.log(123);
                mainNav.classList.remove('is-visible', 'is-fixed');
            }
        } else {
            // Scrolling Down
            mainNav.classList.remove(['is-visible']);
            if (currentTop > headerHeight && !mainNav.classList.contains('is-fixed')) {
                mainNav.classList.add('is-fixed');
            }
        }
        scrollPos = currentTop;
    });
})

/* ********* ARRAY UP ************************** */
var btntop = $('#buttonToTheTop');

$(window).scroll(function() {
    if ($(window).scrollTop() > 900) {
        btntop.addClass('showbutton');
    } else {
        btntop.removeClass('showbutton');
    }
});

btntop.on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({scrollTop:0}, '900');
});


/* *********** LOAD MORE BUTTON ************ */

async function getData(page) {
    let res = await fetch('/reload_tricks/'+ page)
        .then(async (response) => {
            if (!response.ok) {
                console.log('erreur');
            }
            return response.text().then((data) => {
                return data;
            });
        })
    return res;
}
let page = 1;
async function loadDataForward(){
    page ++;
    const displayElement = document.getElementById("content-tricks");
    displayElement.innerHTML = await getData(page);
    document.getElementById("load-data-backward").classList.remove("btn-light");
    document.getElementById("load-data-backward").classList.add("btn-primary");
    document.getElementById("load-data-backward").classList.remove("text-muted");
    document.getElementById("load-data-backward").classList.remove("pe-none");
    const pageCount = document.getElementById("load-data").getAttribute("data-page-count");
    if (pageCount <= page) {
        document.getElementById("load-data-forward").classList.add("btn-light");
        document.getElementById("load-data-forward").classList.add("text-muted");
        document.getElementById("load-data-forward").classList.add("pe-none");
    }
    window.location.hash = "#tricks";
    location.hash = "next";
}
async function loadDataBackward(){
    page --;
    const displayElement = document.getElementById("content-tricks");
    displayElement.innerHTML = await getData(page);
    document.getElementById("load-data-forward").classList.remove("btn-light");
    document.getElementById("load-data-forward").classList.remove("text-muted");
    document.getElementById("load-data-forward").classList.remove("pe-none");
    if (1 >= page) {
        document.getElementById("load-data-backward").classList.remove("btn-primary");
        document.getElementById("load-data-backward").classList.add("btn-light");
        document.getElementById("load-data-backward").classList.add("text-muted");
        document.getElementById("load-data-backward").classList.add("pe-none");
    }
    window.location.hash = "#tricks";
    location.hash = "previous";
}

const $reloadDataForward = document.getElementById('load-data-forward');
const $reloadDataBackward = document.getElementById('load-data-backward');

$reloadDataForward.addEventListener("click", function() {
    loadDataForward();
 });
$reloadDataBackward.addEventListener("click", function() {
    loadDataBackward();
 });

