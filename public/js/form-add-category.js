let categoriesContainer = document.getElementById('category-list-container');
let categoryCollectionContainer = document.getElementById('categories-list');
categoryCollectionContainer.setAttribute('data-index', 1);

let addCategoryButton = document.createElement("button");
let addCategoryButtonText = document.createTextNode("Add category");

addCategoryButton.appendChild(addCategoryButtonText);
addCategoryButton.type = "button";
addCategoryButton.setAttribute("class", "btn secondary");
addCategoryButton.addEventListener('click',addCategoryForm);

categoriesContainer.appendChild(addCategoryButton);

function addCategoryForm() {
    let prototype = categoryCollectionContainer.dataset.prototype;
    let index = categoryCollectionContainer.dataset.index;
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    categoryCollectionContainer.dataset.index =  1 + parseInt(index);

    let removeButton = document.createElement("button");
    let removeButtonText = document.createTextNode("×");
    removeButton.appendChild(removeButtonText);
    removeButton.type = "button";
    removeButton.setAttribute("id", "remove_" + index);
    removeButton.setAttribute("class", "btn danger");
    removeButton.addEventListener('click', removeCategoryForm);

    let newFormLi = document.createElement('li');
    newFormLi.innerHTML = newForm;
    newFormLi.appendChild(removeButton);
    categoryCollectionContainer.appendChild(newFormLi);
}

function removeCategoryForm(event) {
    let elementRemove = event.target;
    let parentRemove = elementRemove.parentElement;
    parentRemove.parentNode.removeChild(parentRemove);
}