
// password_security.addEventListener('load', function(){
//     password_security.classList.remove("isActive");
// })

function password_security($pass_check){
    if($pass_check == 1) {
        let password_security = document.querySelector(".pass_security.isActive");
        password_security.classList.remove("isActive");
    } else {
        return;
    }
    
}