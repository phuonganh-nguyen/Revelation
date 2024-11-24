//Khai báo biến userBtn và gán cho nó giá trị của phần tử HTML có ID user-btn
const userBtn = document.querySelector('#user-btn');
//Thêm sự kiện click cho phần tử userBtn 
userBtn.addEventListener('click', function(){
    //thực hiện việc hiển thị hoặc ẩn phần tử
    const userBox = document.querySelector('.profile-detail');
    userBox.classList.toggle('active');
})

const toggle = document.querySelector('.toggle-btn');
toggle.addEventListener('click', function(){
    const sidebar = document.querySelector('.sidebar');
    //Sử dụng phương thức classList.toggle() để bật hoặc tắt lớp .active trên phần tử sidebar
    sidebar.classList.toggle('active');
})

const sizeSelect = document.getElementById('size-select');
const quantityInput = document.getElementById('quantity-input');

const imgBox = document.querySelector('.slider-container');
const slides = document.getElementsByClassName('slideBox');
var i = 0;
function nextSlide() {
    slides[i].classList.remove('active');
    i = (i + 1) % slides.length;
    slides[i].classList.add('active');
}
 
// setInterval(nextSlide, 7000);

function prevSlide() {
    slides[i].classList.remove('active');
    i--;
    if (i < 0) {
        i = slides.length - 1;
    }
    slides[i].classList.add('active');
} 




