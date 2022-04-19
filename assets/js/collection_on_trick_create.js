/* *********************************** COLLECTION ON TRICK CREATE ***************************************** */

const VideosCollectionHolder = document.querySelector("#global_prototype_videos");
let indexVideos = VideosCollectionHolder.querySelectorAll("fieldset").length;
document.querySelector("#new_video").addEventListener("click", function() {
    VideosCollectionHolder.innerHTML += VideosCollectionHolder.dataset.prototype.replace(/__name__/g, indexVideos);
    indexVideos ++;
});
const PicturesCollectionHolder = document.querySelector("#global_prototype_pictures");
let indexPictures = PicturesCollectionHolder.querySelectorAll("fieldset").length;
document.querySelector("#new_picture").addEventListener("click", function() {
    PicturesCollectionHolder.innerHTML += PicturesCollectionHolder.dataset.prototype.replace(/__name__/g, indexPictures);
    console.log('la');
    indexPictures ++;
});
