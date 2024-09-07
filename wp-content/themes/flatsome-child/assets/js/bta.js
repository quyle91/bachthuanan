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

document.querySelectorAll('.bta_tabs').forEach(bta_tab => {
    const nav = bta_tab.querySelector('.nav');
    
    // left
    const leftButton = document.createElement("li");
    leftButton.classList.add("hide-for-small", "button", "icon", "is-outline","circle");
    leftButton.innerHTML = '<i class="icon-angle-left"></i>';
    nav.appendChild(leftButton);
    leftButton.addEventListener("click", function(){
        const activeTab = nav.querySelector('.active');
        const tabs = Array.from(nav.children);
        const activeIndex = tabs.indexOf(activeTab);
        if (activeIndex === 0) {
            return;
        }
        const previousTab = tabs[activeIndex - 1].querySelector("a");
        previousTab.click();
    });

    // right
    const rightButton = document.createElement("li");
    rightButton.classList.add("hide-for-small", "button", "icon", "is-outline", "circle");
    rightButton.innerHTML = '<i class="icon-angle-right"></i>';
    nav.appendChild(rightButton);
    rightButton.addEventListener("click", function () {
        const activeTab = nav.querySelector('.active');
        const tabs = Array.from(nav.children);
        const activeIndex = tabs.indexOf(activeTab);

        // Nếu là tab cuối cùng, không làm gì thêm
        if (activeIndex === tabs.length - 1) {
            return;
        }

        // Chuyển sang tab tiếp theo
        const nextTab = tabs[activeIndex + 1].querySelector("a");
        nextTab.click();
    });
})