/* *********************************** COLLECTION ON TRICK CREATE ***************************************** */

let indexVideos = document.querySelector("#global_prototype_videos").querySelectorAll("fieldset").length;
document.querySelector("#new_video").addEventListener("click", function() {
    const VideosCollectionHolder = document.querySelector("#global_prototype_videos");
    VideosCollectionHolder.innerHTML += VideosCollectionHolder.dataset.prototype.replace(/__name__/g, indexVideos);
    indexVideos ++;
});
const PicturesCollectionHolder = document.querySelector("#global_prototype_pictures");
let indexPictures = PicturesCollectionHolder.querySelectorAll("fieldset").length;
document.querySelector("#new_picture").addEventListener("click", function() {
    PicturesCollectionHolder.innerHTML += PicturesCollectionHolder.dataset.prototype.replace(/__name__/g, indexPictures);
    indexPictures ++;
});
