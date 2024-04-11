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

// changer logo au click de souris
let logo = document.getElementsByClassName('logo');
let logoImg = document.getElementById('logo-img');

logo.addEventListener('click', function() 
{
    let myAudio = document.createElement("audio");
    myAudio.src = "./bandesSons/bubbles.mp3";
    myAudio.play();

    let aleatoire = Math.floor(Math.random() * 100);

    logoImg.style.transform = 'rotate(360deg)';
    setTimeout(() => {

        if (aleatoire < 90) // 90% de chance
        {
            if (logoImg.src.match('./imagesCommunes/subnautica.png'))
            {
                logoImg.src = './imagesCommunes/logo-website.jpg';
            }
            else if (logoImg.src.match('./imagesCommunes/logo-website.jpg'))
            {
                logoImg.src = './imagesCommunes/requin.png';
            }
            else
            {
                logoImg.src = './imagesCommunes/logo-website.jpg';
            }
        } 
        else // 10% de chance
        {
            logoImg.src = './imagesCommunes/subnautica.png';
        }

        logoImg.style.transform = 'rotate(0deg)';
    }, 500);
});
