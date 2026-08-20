 /**
     * Switcing Form:: ShowLogin / ShowRegister
     */
 const loginContainer = document.querySelector('.login-container');
 const registerContainer = document.querySelector('.register-container');

 registerContainer.style.display = 'none';

 function showLogin() {
     loginContainer.style.display = 'block';
     registerContainer.style.display = 'none';
 }

 function showRegister() {
     loginContainer.style.display = 'none';
     registerContainer.style.display = 'block';
 }

 document.querySelector('#loginModal .modal-body').addEventListener('click', (e) => {
     if (e.target.id === 'showRegister') {
         showRegister();
     } else if (e.target.id === 'showLogin') {
         showLogin();
     }
 });

 /**
  *  Pop Up ANimation
  */

 function animateOnScroll() {
    const cards = document.querySelectorAll('.card');
    const windowHeight = window.innerHeight;

    cards.forEach((card) => {
        const cardTop = card.getBoundingClientRect().top;

        if (cardTop < windowHeight - 50) {
            card.classList.add('show');
        }
    });
}

window.addEventListener('scroll', animateOnScroll);
document.addEventListener('DOMContentLoaded', animateOnScroll);

