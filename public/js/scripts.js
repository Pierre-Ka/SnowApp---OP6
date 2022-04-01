/*!
* Start Bootstrap - Clean Blog v6.0.7 (https://startbootstrap.com/theme/clean-blog)
* Copyright 2013-2021 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-clean-blog/blob/master/LICENSE)
*/

/* *********** ESSAI JS DU BOUTON LOAD TRICKS ************ */

async function getData($compte) {
    let res = await fetch('https://localhost:8000//reload_tricks/'.$compte)
        .then(async (response) => {
            if (!response.ok) {
                throw new Error('error');
            }
            return response.text().then((data) => {
                return $data;
            });
        })
        .catch((error) => {
            console.log('error');
        })
    return res;
}
var $compte = 1;
let reloadData = document.getElementById('reloadData');
reloadData.onclick = loadData();
async function loadData(){
    $compte += 1;
    displayElement.textContent = await getData($compte);
}

/* ********* ARRAY UP ************************** */
var btntop = $('#buttonToTheTop');

$(window).scroll(function() {
    if ($(window).scrollTop() > 300) {
        btntop.addClass('showbutton');
    } else {
        btntop.removeClass('showbutton');
    }
});

btntop.on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({scrollTop:0}, '300');
});
/* ************************* */

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
