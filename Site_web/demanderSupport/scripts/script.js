var btnSoumettre = document.getElementById("soumettre");

btnSoumettre.addEventListener("click", function() {
    
    Swal.fire({
        imageUrl: "https://github.com/RomainBoiret/TCH099_FishFric/blob/main/Site_web/demanderSupport/Images/queue-baleine.png?raw=true",
        imageHeight: 80,
        title: "Message envoyé !",
        text: "Votre demande de support a été soumise avec succès !",
        customClass: {
            container: 'my-swal-container'
        }
    });
});