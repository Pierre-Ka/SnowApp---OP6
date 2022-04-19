
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
    console.log(page);
    const displayElement = document.getElementById("content-tricks");
    displayElement.innerHTML += await getData(page);
    const pageCount = document.getElementById("load-data").getAttribute("data-page-count");
    console.log(pageCount);
    if (pageCount <= page) {
        console.log('oui je suis ici');
        document.getElementById("load-data-forward").classList.remove("btn-dark");
        document.getElementById("load-data-forward").classList.add("btn-light");
        document.getElementById("load-data-forward").classList.add("text-muted");
        document.getElementById("load-data-forward").classList.add("pe-none");
        document.getElementById("load-all-data").classList.add("d-none");
        document.getElementsByClassName("loaderTricks").classList.add("d-none");
        console.log('oui je finis de lire ici');
    }
    window.location.hash = "article :last-child";
    location.hash = "next";
}

const $reloadDataForward = document.getElementById('load-data-forward');

$reloadDataForward.addEventListener("click", function() {
    loadDataForward();
 });

/* *********************************** COLLECTION ON TRICK CREATE ***************************************** */



