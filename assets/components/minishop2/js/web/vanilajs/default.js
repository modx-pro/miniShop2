import MiniShop from "./modules/minishop.class.js";

if (miniShop2Config) {
    miniShop2Config.inputNumber = {
        wrapperSelector: '.ms-input-number-wrap',
        minusSelector: '.ms-input-number-minus',
        plusSelector: '.ms-input-number-plus',
        min: 0,
        max: 1000,
        step: 10,
        negative: false,
    }
    window.miniShop2 = new MiniShop(miniShop2Config);
}
