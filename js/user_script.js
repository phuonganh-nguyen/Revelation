let profile = document.querySelector('.header .flex .profile-detail');
document.querySelector('#user-btn').onclick = () => {
    profile.classList.toggle('active');
    searchForm.classList.remove('active');
}

// const type = document.getElementById("type");
// const types = document.querySelectorAll(".type");

// type.addEventListener("click", function() {
//   const target = document.querySelector(".type[data-type='" + type.textContent + "']");
//   target.style.display = "block";
// });

// const type = document.getElementById("type");
// //Thêm sự kiện click cho phần tử userBtn 
// type.addEventListener('click', function(){
//     //thực hiện việc hiển thị hoặc ẩn phần tử
//     const userBox = document.querySelector('.type');
//     userBox.classList.toggle('active');
// })

let searchForm = document.querySelector('.header .flex .search-form');
document.querySelector('#search-btn').onclick = () => {
    searchForm.classList.toggle('active');
    profile.classList.remove('active'); 
}

let navbar = document.querySelector('.navbar');
document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
}

//                  home slide
const imgBox = document.querySelector('.slider-container');
const slides = document.getElementsByClassName('slideBox');
var i = 0;
function nextSlide() {
    slides[i].classList.remove('active');
    i = (i + 1) % slides.length;
    slides[i].classList.add('active');
}

setInterval(nextSlide, 7000);

function prevSlide() {
    slides[i].classList.remove('active');
    i--;
    if (i < 0) {
        i = slides.length - 1;
    }
    slides[i].classList.add('active');
} 
