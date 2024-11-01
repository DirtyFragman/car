<!-- cart.php -->
<div id="slide-cart">
    <div class="cart_style" id="cart-content">
        <h1>Cart</h1>
        <ul id="cart-items"></ul>
        <p>Total: <span id="cart-total">$0</span></p>
        <button class="cart_button" id="clear-cart">Clear Cart</button>
        <button class="cart_button" id="close-cart">Close Cart</button>
    </div>
    <button onclick="location.href='payment.php'" class="purchase_cart">Purchase</button>
</div>

<script>
const openCartButton = document.getElementById('open-cart');
const closeCartButton = document.getElementById('close-cart');
const clearCartButton = document.getElementById('clear-cart');
const slideCart = document.getElementById('slide-cart');
const cartItemsElement = document.getElementById('cart-items');
const cartTotalElement = document.getElementById('cart-total');
let cartItems = <?php echo json_encode($_SESSION['cart'] ?? []); ?>;
let cartTotal = cartItems.reduce((total, item) => total + parseFloat(item.price), 0);

function updateCart() {
    cartItemsElement.innerHTML = '';
    cartTotalElement.textContent = '$' + cartTotal.toFixed(2);
    cartItems.forEach(item => {
        const listItem = document.createElement('li');
        listItem.textContent = `${item.brand_name} ${item.model} - $${parseFloat(item.price).toFixed(2)}`;

        const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.classList.add('cart_button');
        removeButton.addEventListener('click', () => {
            removeFromCart(item.id);
        });
        
        listItem.appendChild(removeButton);
        cartItemsElement.appendChild(listItem);
    });
}

function addToCart(carId, model, price, brand_name) {
    cartItems.push({ id: carId, model, price, brand_name });
    cartTotal += price;
    updateCart();
    // Send to server if needed
    $.post('update_cart.php', { cartItems: JSON.stringify(cartItems) });
}

function removeFromCart(carId) {
    cartItems = cartItems.filter(item => item.id !== carId);
    cartTotal -= cartItems.find(item => item.id === carId).price; // Adjust total
    updateCart();
    // Send to server if needed
    $.post('update_cart.php', { cartItems: JSON.stringify(cartItems) });
}

document.addEventListener('DOMContentLoaded', updateCart);

closeCartButton.addEventListener('click', () => {
    slideCart.style.right = '-600px';
});

clearCartButton.addEventListener('click', () => {
    if (confirm('Are you sure you want to clear your cart?')) {
        cartItems = [];
        cartTotal = 0;
        updateCart();
        // Send to server if needed
        $.post('update_cart.php', { cartItems: JSON.stringify(cartItems) });
    }
});
</script>
