export let mutations = {
    add_token_config (state, config) {
        state.config.token = config;
    }

    // add_to_favorites (state, product) {
    //     state.user.favorites.push(product);
    // },
    // remove_from_favorites (state, product) {
    //     state.user.favorites.forEach((item, key) => {
    //         if(item === product) {
    //             state.user.favorites.splice(key, 1);
    //         }
    //     });
    // },
    // add_to_cart (state, product) {
    //     state.user.cart.push(product);
    // },
    // update_product_counter(state, product) {
    //     state.user.cart.forEach((item) => {
    //         if(item.articul === product.articul) {
    //             item.count = Number(item.count) + Number(product.count);
    //         }
    //     });
    // },
    // update_kit_counter(state, product) {
    //     state.user.cart.forEach((item) => {
    //         if(item.product_id_for_kit == product.product_id_for_kit && item.parent_url) {
    //             item.count +=  Number(product.count);
    //         }
    //     });
    // },
    // move_product_to_end(state, articul) {
    //     let p;
    //     state.user.cart.forEach((item, key) => {
    //         if(item.articul === articul) {
    //             p = state.user.cart.splice(key, 1);
    //         }
    //     });
    //
    //     state.user.cart.push(p[0]);
    // },
    // remove_from_cart(state, articul) {
    //     state.user.cart.forEach((item, key) => {
    //         if(item.articul === articul) {
    //             state.user.cart.splice(key, 1);
    //         }
    //     });
    // },
    // remove_from_cart_kit(state, articul) {
    //     state.user.cart.forEach((item, key) => {
    //         if(item.product_id_for_kit === articul && item.parent_url) {
    //             state.user.cart.splice(key, 1);
    //         }
    //     });
    // },
    // set_product_counter_kit(state, articul, count) {
    //     state.user.cart.forEach((item) => {
    //         if(item.product_id_for_kit === articul && item.parent_url) {
    //             item.count = count;
    //         }
    //     });
    // },
    // set_product_counter(state, articul, count) {
    //     state.user.cart.forEach((item) => {
    //         if(item.articul === articul) {
    //             item.count = count;
    //         }
    //     });
    // },
    // decrease_product_counter(state, productInfo) {
    //     state.user.cart.forEach((item) => {
    //         if(item.articul === productInfo.articul && item.product_id_for_kit === productInfo.product_id_for_kit) {
    //             item.count = Number.parseInt(item.count) - 1;
    //         }
    //     });
    // },
    // decrease_product_counter_kit(state, articul) {
    //     state.user.cart.forEach((item) => {
    //         if(item.product_id_for_kit === articul && item.parent_url) {
    //             item.count = Number.parseInt(item.count) - 1;
    //         }
    //     });
    // },
    // increase_product_counter(state, productInfo) {
    //     state.user.cart.forEach((item) => {
    //         if(item.articul === productInfo.articul && item.product_id_for_kit === productInfo.product_id_for_kit) {
    //             item.count = Number.parseInt(item.count) + 1;
    //         }
    //     });
    // },
    // increase_product_counter_kit(state, articul) {
    //     state.user.cart.forEach((item) => {
    //         if(item.product_id_for_kit === articul && item.parent_url) {
    //             item.count = Number.parseInt(item.count) + 1;
    //         }
    //     });
    // },
    // clear_cart(state) {
    //     state.user.cart = [];
    // },
    // add_to_watched(state, product) {
    //     if(state.user.watched.length === 10) {
    //         let slicedWatchedList = state.user.watched.slice(1);
    //         slicedWatchedList.push(product);
    //
    //         state.user.watched = slicedWatchedList;
    //     } else {
    //         state.user.watched.push(product);
    //     }
    // },
    // updateCartPath(state, path) {
    //     state.cart_path = path;
    // }
};
