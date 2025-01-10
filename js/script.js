// Navbar and Profile toggle functionality
let navbar = document.querySelector('.header .flex .navbar');
let profile = document.querySelector('.header .flex .profile');

document.querySelector('#menu-btn').onclick = () => {
   navbar.classList.toggle('active');
   profile.classList.remove('active');
};

document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active');
   navbar.classList.remove('active');
};

window.onscroll = () => {
   navbar.classList.remove('active');
   profile.classList.remove('active');
};

// Home Swiper functionality with hover and slide
let homeSwiper = new Swiper('.home-slider', {
   loop: true,
   spaceBetween: 20,
   autoplay: {
      delay: 3000, // Time between slides (milliseconds)
      disableOnInteraction: false, // Autoplay continues after interaction
   },
   pagination: {
      el: '.swiper-pagination',
      clickable: true,
   },
});

// Category Swiper functionality with hover and slide
let categorySwiper = new Swiper('.category-slider', {
   loop: true,
   spaceBetween: 20,
   autoplay: {
      delay: 3000,
      disableOnInteraction: false,
   },
   pagination: {
      el: '.swiper-pagination',
      clickable: true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
      },
      650: {
         slidesPerView: 3,
      },
      768: {
         slidesPerView: 4,
      },
      1024: {
         slidesPerView: 5,
      },
   },
});

// Products Swiper functionality with hover and slide
let productsSwiper = new Swiper('.products-slider', {
   loop: true,
   spaceBetween: 20,
   autoplay: {
      delay: 3000,
      disableOnInteraction: false,
   },
   pagination: {
      el: '.swiper-pagination',
      clickable: true,
   },
   breakpoints: {
      550: {
         slidesPerView: 2,
      },
      768: {
         slidesPerView: 2,
      },
      1024: {
         slidesPerView: 3,
      },
   },
});

// Pause autoplay on hover for all Swipers
document.querySelectorAll('.swiper').forEach(swiperContainer => {
   swiperContainer.addEventListener('mouseenter', () => {
      swiperContainer.swiper.autoplay.stop();
   });
   swiperContainer.addEventListener('mouseleave', () => {
      swiperContainer.swiper.autoplay.start();
   });
});

// Quick view image toggle functionality
let mainImage = document.querySelector('.quick-view .box .row .image-container .main-image img');
let subImages = document.querySelectorAll('.quick-view .box .row .image-container .sub-image img');

subImages.forEach(images => {
   images.onclick = () => {
      let src = images.getAttribute('src');
      mainImage.src = src;
   };
});
