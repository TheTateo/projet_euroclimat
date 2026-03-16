let graphique;

function creerGraphique(labels, data, label, couleur){

    if(graphique){
        graphique.destroy();
    }

    const ctx = document.getElementById('graphique').getContext('2d');

    graphique = new Chart(ctx,{
        type:'line',
        data:{
            labels:labels,
            datasets:[{
                label:label,
                data:data,
                borderColor:couleur,
                fill:false,
                tension:0.1
            }]
        }
    });
}