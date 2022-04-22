/* *********************************** COLLECTION ON TRICK CREATE ***************************************** */

async function removeItem(e) {
    e.currentTarget.closest(".removal").remove();
}

const VideosCollectionHolder = document.querySelector("#space_where_prototype_videos_is_added");
let indexVideos = VideosCollectionHolder.querySelectorAll("fieldset").length;
document.querySelector("#new_video").addEventListener("click", function() {
    const item = document.createElement("div");
    item.classList.add("removal");
    item.innerHTML = VideosCollectionHolder.dataset.prototype.replace(/__name__/g, indexVideos);
    indexVideos ++;
    item.querySelector(".btn-remove").addEventListener("click", function(e) {
        removeItem(e);
    });
    VideosCollectionHolder.appendChild(item);
});
const PicturesCollectionHolder = document.querySelector("#space_where_prototype_pictures_is_added");
let indexPictures = PicturesCollectionHolder.querySelectorAll("fieldset").length;
document.querySelector("#new_picture").addEventListener("click", function() {
    const item = document.createElement("div");
    item.classList.add("removal");
    item.innerHTML = PicturesCollectionHolder.dataset.prototype.replace(/__name__/g, indexPictures);
    indexPictures ++;
    item.querySelector(".btn-remove").addEventListener("click", function(e) {
        removeItem(e);
    });
    PicturesCollectionHolder.appendChild(item);
});

document.querySelectorAll(".btn-remove").forEach(eachOne => {
    eachOne.addEventListener('click', function(e) {
        removeItem(e);
    });
});