var btnSoumettre = document.getElementById("soumettre");

btnSoumettre.addEventListener("click", function() {
    
    Swal.fire({
        title: "Message envoyé !",
        text: "Votre demande de support a été soumise avec succès !",
        icon: "success",
        customClass: {
            container: 'my-swal-container'
        }
    });
});