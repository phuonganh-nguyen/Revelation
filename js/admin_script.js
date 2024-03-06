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

