var btnSoumettre = document.getElementById("soumettre");

btnSoumettre.addEventListener("click", function() {
    
    Swal.fire({
        imageUrl: "",
        imageHeight: 80,
        title: "Message envoyé !",
        text: "Votre demande de support a été soumise avec succès !",
        customClass: {
            container: 'my-swal-container'
        }
    });
});