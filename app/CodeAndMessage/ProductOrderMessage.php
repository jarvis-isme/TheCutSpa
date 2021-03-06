<?php

namespace App\CodeAndMessage;

class ProductOrderMessage
{
    // error code
    const CART_IS_EMPTY = 'ERR400030';
    const CHECKOUT_FAILED = 'ERR400031';
    const NOT_ENOUGH_QUANTITY_PRODUCT = 'ERR400032';

    //internal error code
    const EXW_CHECKOUT = 'EX500017';

    // error message
    const M_CART_IS_EMPTY= 'Checkout failed - Cart is empty.';
    const M_CHECKOUT_FAILED = 'Checkout failed - Something went wrong.';
    const M_NOT_ENOUGH_QUANTITY_PRODUCT = 'There is(are) product(s) have not enough quantity in cart';

    // success code
    const CHECKOUT_SUCCESS = 'ST200018';

    // success message
    const M_CHECKOUT_SUCCESS = 'Checkout successfully.';
}
