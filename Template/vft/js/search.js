const filterInputs = document.querySelectorAll(".shop-filter__search");
if (filterInputs) {
    [...filterInputs].forEach((elem) => {
        console.log(elem);
    })
}
if (filterInputs) {
    [...filterInputs].forEach(
        (input) =>
        (input.addEventListener("keyup", () => {
            let filterList = input.nextSibling.nextSibling.getElementsByTagName('li');
            [...filterList].forEach(
                (item) => {
                    let text = item.querySelector('.ya-ne-umru-v-tualete')
                    item.style.display = 'none'
                    if (text.innerHTML.toUpperCase().includes(input.value.toUpperCase())) {
                        console.log(text.innerHTML)
                        item.style.display = 'block'
                    }
                }
            )
        })));
}