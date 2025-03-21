<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .image-gallery {
            width: 40%;
            padding-right: 20px;
        }

        .main-image img {
            width: 100%;
            display: block;
            margin-bottom: 10px;
        }

        .thumbnail-images {
            display: flex;
            justify-content: flex-start;
            gap: 5px;
        }

        .thumbnail-images img {
            width: 80px;
            height: 100px;
            cursor: pointer;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .product-details {
            width: 60%;
            padding-left: 20px;
        }

        h1 {
            font-size: 1.5em;
            margin-bottom: 5px;
        }

        .price {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .price span {
            text-decoration: line-through;
            color: #777;
            margin-right: 5px;
        }

        .color-selector,
        .size-selector {
            margin-bottom: 15px;
        }

        .color-selector label,
        .size-selector label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .color-options,
        .size-options {
            display: flex;
            gap: 5px;
        }

        .color-option {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .color-option-bg {
            background-color: auto;
        }

        .size-option {
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .size-option:hover {
            background-color: #f0f0f0;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .quantity-selector button {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #8994c4;
            cursor: pointer;
        }

        .quantity-selector input {
            width: 40px;
            text-align: center;
            margin: 0 5px;
            border: 1px solid #ccc;
            padding: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #858eb6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #5e6b99;
        }

        .description {
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .image-gallery,
            .product-details {
                width: 100%;
                padding-right: 0;
                padding-left: 0;
            }

            .thumbnail-images {
                justify-content: space-around;
            }
        }

        /* Additional styles to match the image */
        .price {
            font-size: 1.5em;
        }

        .description {
            font-size: 0.9em;
            line-height: 1.5;
        }

        .size-options {
            gap: 5px;
        }

        .size-option {
            padding: 5px 8px;
            font-size: 0.9em;
        }

        /* Hover effect for size options */
        .size-option:hover {
            background-color: #e9ecef;
        }

        .size-option.selected {
            background-color: #dee2e6;
        }

        .color-option {
            width: 30px;
            height: 30px;
        }

        .color-option:hover {
            border: 2px solid #5e6b99;
        }

        .fav-button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5em;
            color: #333;
            padding: 0;
            margin-left: auto;
            margin-bottom: 10px
        }

        .fav-button:hover {
            color: #e44d26;
            /* Example hover color */
        }

        .discount-text {
            color: #28a745;
            /* Green color */
            margin-bottom: 10px;
        }

        .link-button {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="image-gallery">
            <div class="main-image">
                @php
                    $imagesArray = json_decode($product->images, true);
                @endphp
                
                @if(!empty($imagesArray) && is_array($imagesArray))
                    <img src="{{ asset($imagesArray[0]['name']) }}" alt="Main Product Image">
                @else
                    <img src="{{ asset('placeholder.jpg') }}" alt="No Image Available">
                @endif
            </div>
            <div class="thumbnail-images">
                @if(!empty($imagesArray) && is_array($imagesArray))
                    @foreach($imagesArray as $image)
                        <img src="{{ asset($image['name']) }}" alt="image not available">
                    @endforeach
                @endif
            </div>
        </div>

        <div class="product-details">
            <div style="display:flex; justify-content: space-between; align-items: center">
                <h1>{{ $product->name }}</h1>
                <button class="fav-button">♡</button>
            </div>
            <p>{{ $product->description }}</p>
            <p class="price">
                <span>${{ number_format($product->price * 1.2, 2) }}</span> 
                ${{ number_format($product->price, 2) }}
            </p>
            <p>4 interest-free payments of ${{ number_format($product->price / 4, 2) }} CAD with Klarna. or afterpay <i style="font-style: normal;">ⓘ</i></p>
            <p>★★★★★ 122 reviews</p>

            <div class="color-selector">
                @php
                    $colors = json_decode($product->color, true);
                    $selectedColor = isset($colors[0]) ? $colors[0] : 'Black';
                @endphp
                <label>Color: {{ $selectedColor }}</label>
                <div class="color-options">
                    @if(is_array($colors))
                        @foreach($colors as $color)
                            <div class="color-option-bg" data-color="{{ $color }}">
                                {{ $color }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="size-selector">
                <label>Size:</label>
                <div class="size-options">
                    @php
                        $sizes = json_decode($product->size, true);
                    @endphp
                    @if(is_array($sizes))
                        @foreach($sizes as $size)
                            <div class="size-option">{{ $size }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <button class="link-button">Size Guide</button>
            <p class="discount-text"><span>•</span> 50% Off Archive Sales. Discount applied in cart.</p>

            <div class="quantity-selector">
                <button class="quantity-btn minus">-</button>
                <input type="text" value="1" min="1" max="{{ $product->quantity }}" class="quantity-input">
                <button class="quantity-btn plus">+</button>
            </div>

            <button class="add-to-cart-btn">ADD TO CART</button>

            <div class="description">
                <p><strong>DESCRIPTION</strong></p>
                <p>{{ $product->description }}</p>
            </div>
        </div>
    </div>

    <script>
        // Basic JavaScript for quantity selector
        document.addEventListener('DOMContentLoaded', function() {
            const minusBtn = document.querySelector('.minus');
            const plusBtn = document.querySelector('.plus');
            const quantityInput = document.querySelector('.quantity-input');
            const maxQuantity = {{ $product->quantity }};
            
            minusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });
            
            plusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value < maxQuantity) {
                    quantityInput.value = value + 1;
                }
            });
            
            // Color selection
            const colorOptions = document.querySelectorAll('.color-option-bg');
            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const color = this.getAttribute('data-color');
                    document.querySelector('.color-selector label').textContent = 'Color: ' + color;
                    
                    // Remove active class from all options
                    colorOptions.forEach(opt => opt.classList.remove('active'));
                    // Add active class to selected option
                    this.classList.add('active');
                });
            });
            
            // Size selection
            const sizeOptions = document.querySelectorAll('.size-option');
            sizeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    sizeOptions.forEach(opt => opt.classList.remove('active'));
                    // Add active class to selected option
                    this.classList.add('active');
                    
                    // Change button text when size is selected
                    document.querySelector('.add-to-cart-btn').textContent = 'ADD TO CART';
                });
            });
        });
    </script>
</body>

</html>