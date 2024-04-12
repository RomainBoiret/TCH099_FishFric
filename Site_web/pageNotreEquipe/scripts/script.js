const stickersUrls = [
    './images/etoile-de-mer.png',
    './images/queue-baleine.png',
    './images/baleine.png',
    './images/hippocampe.png',
    './images/la-vie-marine.png',
    './images/recif-de-corail.png',
    './images/tortue.png',
    './images/pieuvre.png'
];

document.onclick = function(e) {

    let stickerIndex = Math.floor(Math.random() * stickersUrls.length);

    let x = e.pageX;
    let y = e.pageY;

    let span = document.createElement('span');
    span.classList.add("click_effect");
    span.style.background = "url('" + stickersUrls[stickerIndex] + "') no-repeat";
    span.style.backgroundSize = 'cover';
    span.style.backgroundPosition = 'center';
    span.style.left =  x + "px";
    span.style.top = y + "px";
    document.body.appendChild(span);

    setTimeout(() => {
        span.classList.add("disappear");
        setTimeout(() => {
            span.remove();
        }, 500);
    }, 4000);
}