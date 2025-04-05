document.addEventListener('DOMContentLoaded', function () {
    var cartModal = document.getElementById('cartModal');
    cartModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        // Get data attributes from the button that triggered the modal
        var foodId = button.getAttribute('data-food-id');
        var foodName = button.getAttribute('data-food-name');
        var foodPrice = button.getAttribute('data-food-price');
        var foodDescription = button.getAttribute('data-food-description');
        var foodImage = button.getAttribute('data-food-image');

        // Modal elements
        var modalFoodId = document.getElementById('modal-food-id');
        var modalFoodName = document.getElementById('modal-food-name');
        var modalFoodPrice = document.getElementById('modal-food-price');
        var modalDescription = document.getElementById('modal-food-description');
        var modalFoodImage = document.getElementById('modal-food-image');

        // Hidden inputs for form submission
        var modalFoodNameInput = document.getElementById('modal-food-name-input');
        var modalFoodPriceInput = document.getElementById('modal-food-price-input');
        var modalFoodImageInput = document.getElementById('modal-food-image-input');

        // Set modal content
        modalFoodId.value = foodId;
        modalFoodName.textContent = foodName;
        modalFoodPrice.textContent = 'Ksh ' + foodPrice;
        modalDescription.textContent = foodDescription;
        modalFoodImage.setAttribute('src', foodImage);

        // Update hidden inputs
        modalFoodNameInput.value = foodName;
        modalFoodPriceInput.value = foodPrice;
        modalFoodImageInput.value = foodImage;
    });

    document.getElementById('cartForm').addEventListener('submit', function (event) {
        var foodId = document.getElementById('modal-food-id').value;
        var foodName = document.getElementById('modal-food-name-input').value;
        var price = document.getElementById('modal-food-price-input').value;
        var foodImage = document.getElementById('modal-food-image-input').value;

        if (!foodId || !foodName || !price || !foodImage) {
            event.preventDefault();
            alert('All fields must be filled out');
        }
        console.log('foodId:', foodId);
        console.log('foodName:', foodName);
        console.log('foodPrice:', foodPrice);
        console.log('foodDescription:', foodDescription);
        console.log('foodImage:', foodImage);
    });
});


// Function to increment the quantity
function incrementQuantity(foodId) {
    let quantityInput = document.getElementById('quantity-display-' + foodId);
    quantityInput.value = parseInt(quantityInput.value) + 1;
    updateTotalPrice(foodId);
}

// Function to decrement the quantity
function decrementQuantity(foodId) {
    let quantityInput = document.getElementById('quantity-display-' + foodId);
    if (quantityInput.value > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updateTotalPrice(foodId);
    }
}

function updateTotalPrice(foodId) {
    let quantityInput = document.getElementById('quantity-display-' + foodId);
    let price = document.getElementById('food-price-' + foodId).value;
    let totalPrice = quantityInput.value * price;
    document.getElementById('total-price-' + foodId).innerText = 'Ksh' + totalPrice;
    document.getElementById('total-price-input-' + foodId).value = totalPrice;

    updateSubtotal();
}

// Function to update the subtotal
function updateSubtotal() {
    let subtotal = 0;
    let totalPriceInputs = document.querySelectorAll('input[name^="total_price"]');
    totalPriceInputs.forEach(input => {
        subtotal += parseFloat(input.value);
    });
    document.getElementById('subtotal').innerText = 'Ksh ' + subtotal;
    document.getElementById('subtotal-input').value = subtotal;
}

// Function to show success message
function showSuccessMessage(message) {
    var successMessage = document.getElementById('successMessage');
    successMessage.textContent = message;
    successMessage.classList.remove('hidden');
    setTimeout(function() {
        successMessage.classList.add('hidden');
    }, 5000); // Hide after 5000ms (5 seconds)
}

// phone number to be 254 

document.getElementById('phone_number').addEventListener('input', function (e) {
    let input = e.target.value;
    let phoneError = document.getElementById('phone_error');

    phoneError.style.display = 'none';

    if (input.length === 10 && input.startsWith('07')) {
        e.target.value = '254' + input.substring(1);
    } else if (input.length === 10 && !input.startsWith('07')) {
        phoneError.textContent = 'Invalid Phone Number Format';
        phoneError.style.display = 'block';
    } else if (input.length !== 10 && !input.startsWith('254')) {
        phoneError.textContent = 'Phone Number must be 10 digits long and start with "07".';
        phoneError.style.display = 'block';
    }
});