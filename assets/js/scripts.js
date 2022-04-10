
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

async function getData($compte) {
    let res = await fetch('/reload_tricks/'+ numberOfClic)
        .then(async (response) => {
            if (!response.ok) {
                throw new Error('error');
            }
            return response.text().then((data) => {
                return data;
            });
        })
        .catch((error) => {
            console.log('error');
        })
    return res;
}
let numberOfClic = 0;
async function loadData(){
    console.log('ici');
    numberOfClic ++;
    const displayElement = document.getElementById("content-tricks");
    displayElement.innerHTML += await getData(numberOfClic);
    /* Uncaught (in promise) ReferenceError: displayElement is not defined */
}
const $reloadData = document.getElementById('reload-data');
console.log($reloadData);
 /* Uncaught TypeError: Cannot set properties of null (setting 'onclick') */
// $reloadData.click(function () {
//     loadData();
// });
$reloadData.onclick = loadData();

// $reloadData.on("click", function() {
//
// });


/* ************ FIN DU MODAL ****************** */