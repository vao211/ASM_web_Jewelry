function loadProducts(search = '', category = '', minPrice = '', maxPrice = '') {
    fetch(`../backend/products.php?search=${search}&category=${category}&min_price=${minPrice}&max_price=${maxPrice}`)
        .then(response => response.json())
        .then(products => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';
            products.forEach(product => {
                productList.innerHTML += `
                    <div class="col-md-4 col-sm-6">
                        <a href="../backend/detail_product.php?id=${product.id}" style="text-decoration: none; color: inherit;">
                            <div class="card mb-4">
                                <img src="../uploads/${product.image}" class="card-img-top" alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text">${Number(product.price).toLocaleString('vi-VN')} VND</p>
                                    <p class="card-description">${product.description}</p>
                                    <button type="button" class="btn btn-primary" onclick="addToCart(${product.id}, event)">Add to cart</button>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

loadProducts();

document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const search = document.getElementById('search').value;
    const category = document.getElementById('category').value;
    const minPrice = document.getElementById('min-price').value;
    const maxPrice = document.getElementById('max-price').value;
    loadProducts(search, category, minPrice, maxPrice);
});

function addToCart(productId, event) {
    event.preventDefault();
    fetch('../backend/cart.php', {
        method: 'POST',
        body: JSON.stringify({ product_id: productId }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        const notification = document.getElementById('notification');
        
        if (data.status === 'success') {
            notification.textContent = 'Add to cart complete!';
            notification.style.display = 'block';
            notification.style.opacity = 1;

            setTimeout(() => {
                notification.style.opacity = 0; 
                setTimeout(() => {
                    notification.style.display = 'none';  
                }, 500);
            }, 3000); 
        } 
        else {
            notification.textContent = 'Login first';
            notification.style.display = 'block';
            notification.style.opacity = 1;
            setTimeout(() => {
                notification.style.opacity = 0;
                setTimeout(() => {
                    notification.style.display = 'none'; 
                }, 500);
            }, 3000); 
        }
    })
}

function swapImage(detailImg) {
    const mainImage = document.getElementById('mainImage');
    const detailImagesContainer = document.getElementById('detailImages');

    const mainImageSrc = mainImage.src;

    mainImage.src = detailImg.src;

    const newThumbnail = document.createElement('img');
    newThumbnail.src = mainImageSrc;
    newThumbnail.className = 'img-thumbnail detail-image';
    newThumbnail.style.width = '100px';
    newThumbnail.style.height = '100px';
    newThumbnail.style.objectFit = 'cover';
    newThumbnail.style.cursor = 'pointer';
    newThumbnail.alt = 'Detail Image';
    newThumbnail.onclick = function() { swapImage(this); };

    detailImagesContainer.replaceChild(newThumbnail, detailImg);
}