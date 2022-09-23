import MiniShop from "./modules/minishop.class.js";

if (miniShop2Config) {
    window.miniShop2 = new MiniShop(miniShop2Config);
    miniShop2.Callbacks.add('Cart.change.response.success', 'test', (response)=>{
        console.log(response);
        miniShop2.Message.success('Количество изменено');
        response.message = '';
    });
}
