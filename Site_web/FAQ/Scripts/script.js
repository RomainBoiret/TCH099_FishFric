// toggle icon navigation
let menuIcon = document.querySelector('#menu-icon');
let navigation = document.querySelector('.navigation');

menuIcon.onclick = () => {
    menuIcon.classList.toggle('bx-x');
    navigation.classList.toggle('active');
}

// remove toggle icon and navbar when click navigation links (scroll)
menuIcon.classList.remove('bx-x');
navigation.classList.remove('active');

// faire nager les poissons quand on clique 3 fois de suite sur l'écran
const mainContainer = document.getElementById('main-container');
const poissons = document.querySelectorAll('#poisson');
let clickCount = 0;
let timer;

document.addEventListener('click', function() {
    clickCount++;
    clearTimeout(timer);
    timer = setTimeout(() => {
        clickCount = 0;
    }, 300); // Réinitialise le compteur après 300ms

    if (clickCount === 3) {
        // Tremblement de la section contenant l'id 'main-container'
        mainContainer.classList.add('trembler');
        setTimeout(() => {
            mainContainer.classList.remove('trembler');
        }, 2000); // Retire la classe 'trembler' après 600ms

        setTimeout(() => {
            poissons.forEach((poisson) => {
                poisson.classList.add('nager'); // Ajoute la classe 'nager' à chaque image pour l'animation de nage
            });
        }, 2000); // Commence l'animation de nage après 600ms

        setTimeout(() => {
            poissons.forEach((poisson) => {
                poisson.classList.remove('nager'); // Supprime la classe 'nager' après 2 secondes
            });
        }, 4000); // Arrête l'animation de nage après 2.6 secondes

        clickCount = 0; // Réinitialise le compteur après avoir déclenché l'animation
    }
});
