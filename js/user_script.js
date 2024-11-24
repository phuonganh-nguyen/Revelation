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

document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.header');

    // Kiểm tra xem phần tử header có tồn tại không
    if (header) {
        console.log('Header found!');

        // Lắng nghe sự kiện khi chuột vào header
        header.addEventListener('mouseenter', () => {
            console.log('Mouse entered header!');
            header.classList.add('visible');  // Thêm class 'visible' khi di chuột vào
        });

        // Lắng nghe sự kiện khi chuột ra khỏi header
        header.addEventListener('mouseleave', () => {
            console.log('Mouse left header!');
            header.classList.remove('visible');  // Loại bỏ class 'visible' khi chuột ra khỏi header
        });
    } else {
        console.log('Header not found!');
    }
});
// const header = document.querySelector('header');
// function fixedNavbar(){
//     header.classList.toggle('scrolled', window.pageYOffset > 0)
// }
// fixedNavbar();
// window.addEventListener('scroll', fixedNavbar)

// // Chọn phần tử header
// var header = document.querySelector('.header');

// // Thêm sự kiện cuộn trang
// window.addEventListener('scroll', function() {
//     // Kiểm tra vị trí cuộn của trang
//     if (window.scrollY > 0) {
//         // Nếu vị trí cuộn lớn hơn 0, thêm class "scrolled" vào phần tử header
//         header.classList.add('scrolled');
//     } else {
//         // Nếu vị trí cuộn nhỏ hơn hoặc bằng 0, loại bỏ class "scrolled" khỏi phần tử header
//         header.classList.remove('scrolled');
//     }
// });


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

// Lấy tất cả các thẻ hình ảnh thu nhỏ
const thumbs = document.querySelectorAll('.thumb-list img');
const mainImage = document.querySelector('#mainImage');

// Lặp qua từng hình ảnh thu nhỏ và thêm sự kiện click
thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
        // Thay đổi hình ảnh chính thành đường dẫn của hình ảnh thu nhỏ được nhấp vào
        mainImage.src = thumb.src;
    });
});

function toggleColor(button) {
    button.classList.toggle('clicked'); // Thêm hoặc xóa lớp 'clicked' khi nút được click
}

$(document).ready(function() {
    // Lấy tỉnh thành
    $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {	       
        if (data_tinh.error == 0) {
            $.each(data_tinh.data, function(key_tinh, val_tinh) {
                $("#tinh").append('<option value="' + val_tinh.id + '">' + val_tinh.full_name + '</option>');
            });
            $("#tinh").change(function(e) {
                var idtinh = $(this).val();
                // Lấy quận huyện
                $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {	       
                    if (data_quan.error == 0) {
                        $("#quan").html('<option value="0">Quận Huyện</option>');  
                        $("#phuong").html('<option value="0">Phường Xã</option>');   
                        $.each(data_quan.data, function(key_quan, val_quan) {
                            $("#quan").append('<option value="' + val_quan.id + '">' + val_quan.full_name + '</option>');
                        });
                        // Lấy phường xã  
                        $("#quan").change(function(e) {
                            var idquan = $(this).val();
                            $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {	       
                                if (data_phuong.error == 0) {
                                    $("#phuong").html('<option value="0">Phường Xã</option>');   
                                    $.each(data_phuong.data, function(key_phuong, val_phuong) {
                                        $("#phuong").append('<option value="' + val_phuong.id + '">' + val_phuong.full_name + '</option>');
                                    });
                                }
                            });
                        });
                    }
                });
            });   
        }
    });
});