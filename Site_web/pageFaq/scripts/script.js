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
    }, 300);

    if (clickCount === 3) {

        mainContainer.classList.add('trembler');
        setTimeout(() => {
            mainContainer.classList.remove('trembler');
        }, 2000);

        setTimeout(() => {
            poissons.forEach((poisson) => {
                poisson.classList.add('nager');
            });
        }, 2000);

        setTimeout(() => {
            poissons.forEach((poisson) => {
                poisson.classList.remove('nager');
            });
        }, 4000);

        clickCount = 0;
    }
});
