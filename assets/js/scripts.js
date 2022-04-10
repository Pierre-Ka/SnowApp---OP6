
/*!
* Start Bootstrap - Clean Blog v6.0.7 (https://startbootstrap.com/theme/clean-blog)
* Copyright 2013-2021 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-clean-blog/blob/master/LICENSE)
*/

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

/* ********** FIN ARRAY UP *************** */
/* *********** ESSAI JS DU BOUTON LOAD TRICKS ************ */

// async function getData(page) {
//     let res = await fetch('/reload_tricks/'+ page)
//         .then(async (response) => {
//             if (!response.ok) {
//                 throw new Error('error');
//             }
//             return response.text().then((data) => {
//                 return data;
//             });
//         })
//     return res;
// }
// let page = 1;
// async function loadDataForward(){
//     page ++;
//     const displayElement = document.getElementById("content-tricks");
//     displayElement.innerHTML = await getData(page);
// }
// async function loadDataBackward(){
//     page --;
//     const displayElement = document.getElementById("content-tricks");
//     displayElement.innerHTML = await getData(page);
// }

// const $reloadData = document.getElementById('reload-data');
//
// $reloadData.click(function () {
//     loadDataForward();
// });
//
// $reloadData.onclick = loadDataForward();
//
// $reloadData.on("click", function() {
//      loadDataForward();
// });
//
// $reloadData.addEventListener("click", function() {
//     loadDataForward();
//  });


/* ************ FIN DU MODAL ****************** */