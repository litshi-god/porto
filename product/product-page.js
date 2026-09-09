const product = window.PRODUCT_PAGE;
const app = document.getElementById("app");

if (!product) {
  app.innerHTML = '<section class="not-found"><div class="eyebrow">Product not found</div><h1 class="title">Halaman produk tidak tersedia.</h1></section>';
} else {
  document.title = `${product.name} | LeonXLab`;
  const gallery = product.images.map((image, index) => `
    <figure class="gallery-item">
      <img src="${image}" alt="${product.name} dokumentasi ${index + 1}" loading="lazy">
      <figcaption>Dokumentasi ${String(index + 1).padStart(2, "0")}</figcaption>
    </figure>`).join("");

  app.innerHTML = `
    <section class="hero">
      <div>
        <div class="eyebrow">${product.type}</div>
        <h1 class="title">${product.name}</h1>
        <p class="intro">${product.description}</p>
        <div class="meta">${product.features.map(feature => `<span class="tag">${feature}</span>`).join("")}</div>
      </div>
      <div class="visual"><img src="${product.images[0]}" alt="${product.name} preview"></div>
    </section>
    <section class="details">
      <div><h2 class="section-title">Tentang produk</h2><p class="copy">${product.details}</p></div>
      <div><h2 class="section-title">Fitur utama</h2><ul class="feature-list">${product.features.map(feature => `<li>${feature}</li>`).join("")}</ul></div>
    </section>
    <section class="gallery">
      <div class="gallery-head"><h2 class="section-title">Dokumentasi visual</h2><p class="gallery-note">${product.images.length} tampilan dari proses dan produk.</p></div>
      <div class="gallery-grid">${gallery}</div>
    </section>`;
}