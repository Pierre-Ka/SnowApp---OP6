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
    displayElement.innerHTML += await getData(page);
    const pageCount = document.getElementById("load-data").getAttribute("data-page-count");
    if (pageCount <= page) {
        document.getElementById("load-data-forward").classList.remove("btn-dark");
        document.getElementById("load-data-forward").classList.add("btn-light");
        document.getElementById("load-data-forward").classList.add("text-muted");
        document.getElementById("load-data-forward").classList.add("pe-none");
        document.getElementById("load-all-data").classList.add("d-none");
        document.getElementsByClassName("loaderTricks").classList.add("d-none");
    }
    window.location.hash = "article :last-child";
    location.hash = "loadMore";
}

var $reloadDataForward = document.getElementById('load-data-forward');
if($reloadDataForward){
    $reloadDataForward.addEventListener("click", function() {
        loadDataForward();
    });
}