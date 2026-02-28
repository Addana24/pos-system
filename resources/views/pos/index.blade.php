<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .product-card {
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .cart-container {
            height: calc(100vh - 250px);
            overflow-y: auto;
        }
        .category-btn {
            white-space: nowrap;
        }
        .categories-container {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">POS System</h2>
                    <div>
                        <a href="/admin" class="btn btn-outline-primary">Admin Panel</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Products Section -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="search-product" class="form-control" placeholder="Search products...">
                                    <button class="btn btn-outline-secondary" type="button" id="search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="categories-container">
                                    <button class="btn btn-sm btn-outline-primary me-2 category-btn" data-id="all">All</button>
                                    @foreach($categories as $category)
                                        <button class="btn btn-sm btn-outline-primary me-2 category-btn" data-id="{{ $category->id }}">{{ $category->name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="products-container">
                            @foreach($products as $product)
                                <div class="col-md-3 mb-4">
                                    <div class="card product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                        <div class="card-body text-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-2" style="height: 100px; object-fit: contain;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 100px;">
                                                    <i class="fas fa-box fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                            <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                            <p class="card-text text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            <small class="text-muted">Stock: {{ $product->stock }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Shopping Cart</h5>
                    </div>
                    <div class="card-body">
                        <div class="cart-container mb-3" id="cart-items">
                            <!-- Cart items will be added here dynamically -->
                            <div class="text-center py-5 text-muted" id="empty-cart-message">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>Your cart is empty</p>
                            </div>
                        </div>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (10%):</span>
                                <span id="tax">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold" id="total">Rp 0</span>
                            </div>
                            
                            <button class="btn btn-primary w-100" id="checkout-btn" disabled>
                                <i class="fas fa-cash-register me-2"></i> Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Checkout Modal -->
    <div class="modal fade" id="checkout-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Total Amount</label>
                        <div class="form-control bg-light" id="modal-total">Rp 0</div>
                    </div>
                    <div class="mb-3">
                        <label for="payment-method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment-method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div class="mb-3" id="cash-payment-section">
                        <label for="paid-amount" class="form-label">Paid Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="paid-amount">
                        </div>
                    </div>
                    <div class="mb-3" id="change-section" style="display: none;">
                        <label class="form-label">Change</label>
                        <div class="form-control bg-light" id="change-amount">Rp 0</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="process-payment-btn">Process Payment</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Receipt Modal -->
    <div class="modal fade" id="receipt-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="receipt-content">
                    <!-- Receipt content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="print-receipt-btn">
                        <i class="fas fa-print me-2"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize variables
            let cart = [];
            let subtotal = 0;
            let tax = 0;
            let total = 0;
            
            // Format number to currency
            function formatCurrency(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
            
            // Update cart display
            function updateCart() {
                const cartContainer = $('#cart-items');
                const emptyCartMessage = $('#empty-cart-message');
                
                if (cart.length === 0) {
                    cartContainer.html(emptyCartMessage);
                    $('#checkout-btn').prop('disabled', true);
                    return;
                }
                
                $('#checkout-btn').prop('disabled', false);
                
                let cartHtml = '';
                subtotal = 0;
                
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    cartHtml += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">${item.name}</h6>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button class="btn btn-outline-secondary decrease-qty" data-index="${index}" type="button">-</button>
                                    <input type="text" class="form-control text-center item-qty" value="${item.quantity}" data-index="${index}" readonly>
                                    <button class="btn btn-outline-secondary increase-qty" data-index="${index}" type="button">+</button>
                                </div>
                                <div class="text-end">
                                    <div>Rp ${formatCurrency(item.price)} x ${item.quantity}</div>
                                    <div class="fw-bold">Rp ${formatCurrency(itemTotal)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                cartContainer.html(cartHtml);
                
                // Calculate tax and total
                tax = subtotal * 0.1;
                total = subtotal + tax;
                
                // Update summary
                $('#subtotal').text(`Rp ${formatCurrency(subtotal)}`);
                $('#tax').text(`Rp ${formatCurrency(tax)}`);
                $('#total').text(`Rp ${formatCurrency(total)}`);
                $('#modal-total').text(`Rp ${formatCurrency(total)}`);
                
                // Attach event handlers to new elements
                $('.remove-item').on('click', function() {
                    const index = $(this).data('index');
                    cart.splice(index, 1);
                    updateCart();
                });
                
                $('.decrease-qty').on('click', function() {
                    const index = $(this).data('index');
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCart();
                    }
                });
                
                $('.increase-qty').on('click', function() {
                    const index = $(this).data('index');
                    if (cart[index].quantity < cart[index].stock) {
                        cart[index].quantity++;
                        updateCart();
                    }
                });
                
                $('.item-qty').on('change', function() {
                    const index = $(this).data('index');
                    const qty = parseInt($(this).val());
                    
                    if (qty > 0 && qty <= cart[index].stock) {
                        cart[index].quantity = qty;
                        updateCart();
                    } else {
                        $(this).val(cart[index].quantity);
                    }
                });
            }
            
            // Add product to cart
            $(document).on('click', '.product-card', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = $(this).data('price');
                const stock = $(this).data('stock');
                
                // Check if product already in cart
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    if (existingItem.quantity < stock) {
                        existingItem.quantity++;
                    }
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        quantity: 1,
                        stock
                    });
                }
                
                updateCart();
            });
            
            // Filter products by category
            $('.category-btn').on('click', function() {
                const categoryId = $(this).data('id');
                
                $('.category-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                
                // Get products by category
                $.ajax({
                    url: '{{ route("pos.products") }}',
                    type: 'GET',
                    data: {
                        category_id: categoryId === 'all' ? '' : categoryId,
                        search: $('#search-product').val()
                    },
                    success: function(response) {
                        renderProducts(response);
                    }
                });
            });
            
            // Search products
            $('#search-btn').on('click', function() {
                const categoryBtn = $('.category-btn.btn-primary');
                const categoryId = categoryBtn.data('id');
                
                $.ajax({
                    url: '{{ route("pos.products") }}',
                    type: 'GET',
                    data: {
                        category_id: categoryId === 'all' ? '' : categoryId,
                        search: $('#search-product').val()
                    },
                    success: function(response) {
                        renderProducts(response);
                    }
                });
            });
            
            $('#search-product').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#search-btn').click();
                }
            });
            
            // Render products
            function renderProducts(products) {
                let html = '';
                
                if (products.length === 0) {
                    html = '<div class="col-12 text-center py-5"><p>No products found</p></div>';
                } else {
                    products.forEach(product => {
                        html += `
                            <div class="col-md-3 mb-4">
                                <div class="card product-card" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}" data-stock="${product.stock}">
                                    <div class="card-body text-center">
                                        ${product.image ? 
                                            `<img src="/storage/${product.image}" alt="${product.name}" class="img-fluid mb-2" style="height: 100px; object-fit: contain;">` : 
                                            `<div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 100px;"><i class="fas fa-box fa-2x text-muted"></i></div>`
                                        }
                                        <h6 class="card-title mb-1">${product.name}</h6>
                                        <p class="card-text text-primary mb-0">Rp ${formatCurrency(product.price)}</p>
                                        <small class="text-muted">Stock: ${product.stock}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                $('#products-container').html(html);
            }
            
            // Checkout button
            $('#checkout-btn').on('click', function() {
                $('#paid-amount').val('');
                $('#change-section').hide();
                $('#checkout-modal').modal('show');
            });
            
            // Calculate change
            $('#paid-amount').on('input', function() {
                const paidAmount = parseFloat($(this).val()) || 0;
                const change = paidAmount - total;
                
                if (change >= 0) {
                    $('#change-amount').text(`Rp ${formatCurrency(change)}`);
                    $('#change-section').show();
                    $('#process-payment-btn').prop('disabled', false);
                } else {
                    $('#change-section').hide();
                    $('#process-payment-btn').prop('disabled', true);
                }
            });
            
            // Payment method change
            $('#payment-method').on('change', function() {
                const method = $(this).val();
                
                if (method === 'cash') {
                    $('#cash-payment-section').show();
                    $('#process-payment-btn').prop('disabled', true);
                } else {
                    $('#cash-payment-section').hide();
                    $('#change-section').hide();
                    $('#process-payment-btn').prop('disabled', false);
                }
            });
            
            // Process payment
            $('#process-payment-btn').on('click', function() {
                const paymentMethod = $('#payment-method').val();
                const paidAmount = parseFloat($('#paid-amount').val()) || total;
                const notes = $('#notes').val();
                
                // Prepare items for checkout
                const items = cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity
                }));
                
                // Send checkout request
                $.ajax({
                    url: '{{ route("pos.checkout") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        items: items,
                        payment_method: paymentMethod,
                        paid_amount: paidAmount,
                        notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close checkout modal
                            $('#checkout-modal').modal('hide');
                            
                            // Load receipt
                            $.get(`/pos/receipt/${response.transaction.id}`, function(html) {
                                $('#receipt-content').html(html);
                                $('#receipt-modal').modal('show');
                                
                                // Clear cart
                                cart = [];
                                updateCart();
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        alert(response.message || 'An error occurred during checkout');
                    }
                });
            });
            
            // Print receipt
            $('#print-receipt-btn').on('click', function() {
                const receiptWindow = window.open('', '_blank');
                receiptWindow.document.write('<html><head><title>Receipt</title>');
                receiptWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">');
                receiptWindow.document.write('<style>body { font-family: Arial, sans-serif; } .receipt { max-width: 80mm; margin: 0 auto; padding: 10px; }</style>');
                receiptWindow.document.write('</head><body>');
                receiptWindow.document.write('<div class="receipt">' + $('#receipt-content').html() + '</div>');
                receiptWindow.document.write('</body></html>');
                receiptWindow.document.close();
                
                setTimeout(function() {
                    receiptWindow.print();
                    receiptWindow.close();
                }, 500);
            });
            
            // Initialize cart
            updateCart();
        });
    </script>
</body>
</html>