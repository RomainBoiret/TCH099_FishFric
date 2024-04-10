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
let logo = document.getElementById('logo');
let logoImg = document.getElementById('logo-img');

logo.addEventListener('click', function() 
{
    let aleatoire = Math.floor(Math.random() * 100);

    if (aleatoire < 90) // 90% de chance
    {
        if (logoImg.src.match('./Images/subnautica.png'))
        {
            logoImg.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                logoImg.src = './Images/logo-website.jpg';
                logoImg.style.transform = 'rotate(0deg)';
            }, 500);
        }
        else if (logoImg.src.match('./Images/logo-website.jpg')) 
        {
            logoImg.style.transform = 'scale(1.2)';
            setTimeout(() => {
                logoImg.src = './Images/requin.png';
                logoImg.style.transform = 'scale(1)';
            }, 500);
        } 
        else 
        {
            logoImg.style.transform = 'scale(1.2)';
            setTimeout(() => {
                logoImg.src = './Images/logo-website.jpg';
                logoImg.style.transform = 'scale(1)';
            }, 500);
        }
    } 
    else 
    {
        logoImg.style.transform = 'rotate(360deg)';
        setTimeout(() => {
            logoImg.src = './Images/subnautica.png'; // 10% de chance
            logoImg.style.transform = 'rotate(0deg)';
        }, 500);
    }
});
