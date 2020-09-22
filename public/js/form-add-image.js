var imagesContainer = document.getElementById('image-list-container');
var formReqImgContainer = document.getElementById('blog_post_blogImages');
var collectionContainer = document.getElementById('images-list');
collectionContainer.setAttribute('data-index', 1);

var addButton = document.createElement("button");
var addButtonText = document.createTextNode("Add image");

addButton.appendChild(addButtonText);
addButton.type = "button";
addButton.setAttribute("class", "btn secondary");
addButton.addEventListener('click',addImageForm);

imagesContainer.appendChild(addButton)
formReqImgContainer.appendChild(imagesContainer);

function addImageForm() {
    let prototype = collectionContainer.dataset.prototype;
    let index = collectionContainer.dataset.index;
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    collectionContainer.dataset.index =  1 + parseInt(index);

    let removeButton = document.createElement("button");
    let removeButtonText = document.createTextNode("×");
    removeButton.appendChild(removeButtonText);
    removeButton.type = "button";
    removeButton.setAttribute("id", "remove_" + index);
    removeButton.setAttribute("class", "btn danger");
    removeButton.addEventListener('click', removeImageForm);

    let newFormLi = document.createElement('li');
    newFormLi.innerHTML = newForm;
    newFormLi.appendChild(removeButton);
    collectionContainer.appendChild(newFormLi);
}

function removeImageForm(event) {
    let elementRemove = event.target;
    let parentRemove = elementRemove.parentElement;
    parentRemove.parentNode.removeChild(parentRemove);
}