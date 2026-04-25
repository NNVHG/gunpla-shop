document.addEventListener('DOMContentLoaded', () => {
    // Xử lý nút ẩn/hiện mật khẩu
    const toggleBtns = document.querySelectorAll('.btn-toggle-pass');
    
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Tránh form submit
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                this.innerHTML = '👁️‍🗨️'; // Icon mở mắt
            } else {
                inputField.type = 'password';
                this.innerHTML = '👁️'; // Icon nhắm mắt
            }
        });
    });
});