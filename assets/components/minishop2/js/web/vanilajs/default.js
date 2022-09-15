document.addEventListener('DOMContentLoaded', MiniShopStart);

async function MiniShopStart() {
    if (miniShop2Config) {
        const {default: MiniShop} = await import("./modules/minishop.class.js");
        window.miniShop2 = new MiniShop(miniShop2Config);
    }
}
