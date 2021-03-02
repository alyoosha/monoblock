export let getters = {
    get_token_config: state => {
        return state.config.token;
    }
    // get_last_added_product_to_cart: state => {
    //     return state.user.cart.slice(-1).pop().articul;
    // },
    // get_total_products_in_cart: state => {
    //     return state.user.cart.length;
    // },
    // get_cart_products: state => {
    //     return state.user.cart;
    // },
    // get_watched_products: state => {
    //     return state.user.watched;
    // },
    // get_product_by_articul: state => articul => {
    //     let p = null;
    //
    //     state.user.cart.forEach((item) => {
    //         if(item.articul === articul) {
    //             return p = item;
    //         }
    //     });
    //
    //     return p;
    // },
    // get_complement: state => product => {
    //     let p = null;
    //
    //     state.user.cart.forEach((item) => {
    //         if(item.product_id_for_kit != undefined && item.parent_url && item.product_id_for_kit == product.product_id_for_kit) {
    //             return p = item;
    //         }
    //     });
    //
    //     return p;
    // }
};
