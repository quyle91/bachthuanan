document.querySelectorAll('.bta_wish').forEach((wish) => {
    wish.addEventListener('click', () => {
        const box = wish.closest(".box");
        const target_button = box.querySelector(".wishlist-button");
        box.querySelector(".box-image").classList.add("processing");
        target_button.click();
    });
});

document.querySelectorAll('.bta_add_to_cart').forEach((wish) => {
    wish.addEventListener('click', () => {
        const box = wish.closest(".box");
        const target_button = box.querySelector(".ajax_add_to_cart");
        box.querySelector(".box-image").classList.add("processing");
        target_button.click();
    });
});