//--------------------------------------AFFICHER la popup "Contacter technicien support"--------------------------------------
function togglePopupContacterSupport() {
    document.getElementById("popup").classList.toggle("active");
    document.getElementById('msg-erreur-virement-compte').innerText = ""; //Vider les messages d'erreurs
}