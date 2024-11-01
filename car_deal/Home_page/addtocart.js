// addtocart.js
function addToCart(carId) {
    // Get the button that was clicked
    const button = event.target;
    const brand = button.dataset.brand;
    const model = button.dataset.model;
    const price = button.dataset.price;

    $.ajax({
        url: '../components/add_to_cart.php',
        type: 'POST',
        data: {
            car_id: carId
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.error) {
                    alert(result.error);
                } else {
                    alert(`${brand} ${model} added to your garage!`);
                    // Refresh cart contents
                    loadCartItems();
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        },
        error: function() {
            alert('Error adding car to garage');
        }
    });
}