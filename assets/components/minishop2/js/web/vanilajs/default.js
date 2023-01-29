import MiniShop from "./modules/minishop.class.js";

if (miniShop2Config) {
    window.miniShop2 = new MiniShop(miniShop2Config);
}
/*document.addEventListener('DOMContentLoaded', (event) => {
    if (typeof miniShop2 != "undefined") {
        miniShop2.Callbacks.add('Cart.add.response.success', 'cartAddCallbacks', function (response) {
            console.log(response)
        });
        miniShop2.Callbacks.add('Cart.remove.response.success', 'cartRemoveCallbacks', function (response) {
            console.log(response)
        });
        miniShop2.Callbacks.add('Cart.change.response.success', 'cartChangeCallbacks', function (response) {
            console.log(response)
        });
    }
});*/
