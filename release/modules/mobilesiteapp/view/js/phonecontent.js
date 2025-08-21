/**
 * Смена табов, смена цвета текущего таба
 */
document.addEventListener('click', e => {
    const el = e.target;
    const tabs = document.querySelectorAll('[data-tab]');

    if (el.dataset.changeTab) {
        const tabButton = el.closest('#' + el.dataset.changeTab);
        const buttons = document.querySelector('.clientSiteApp_buttons');
        const colorPrimary = buttons.dataset.colorPrimary;
        const colorDark = buttons.dataset.colorDark;

        tabs.forEach(tab => {
            tab.classList.add('hidden');
            if (tab.dataset.tab === el.dataset.changeTab) {
                tab.classList.remove('hidden');
            }
        });

        if (buttons) {
            const colorTypeNodes = buttons.querySelectorAll('[data-change-color]')
            if (colorTypeNodes) {
                colorTypeNodes.forEach(nod => {
                    nod.style[nod.dataset.changeColor] = colorDark;
                });
            }
        }

        if (tabButton) {
            const currentColorTypeNodes = tabButton.querySelectorAll('[data-change-color]');
            if (currentColorTypeNodes) {
                currentColorTypeNodes.forEach(nod => {
                    nod.style[nod.dataset.changeColor] = colorPrimary;
                });
            }
        }
    }
});