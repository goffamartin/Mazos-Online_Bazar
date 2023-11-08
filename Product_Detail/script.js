document.addEventListener("DOMContentLoaded", function () {
    // Simulate loading product data from a server
    // Replace this with actual data fetching using AJAX or PHP
    const productData = {
        title: "Product Name",
        images: ["image1.jpg", "image2.jpg"],
        price: "$99.99",
        description: "Product description goes here",
        creationDate: "2023-11-08",
    };

    // Populate product details on the page
    document.getElementById("product-title").textContent = productData.title;
    const productImages = document.getElementById("product-images");
    productData.images.forEach((image) => {
        const img = document.createElement("img");
        img.src = "product_images/" + image; // Path to your product images
        productImages.appendChild(img);
    });
    document.getElementById("product-price").textContent = "Price: " + productData.price;
    document.getElementById("product-description").textContent = productData.description;
    document.getElementById("creation-date").textContent = "Date of Creation: " + productData.creationDate;
});