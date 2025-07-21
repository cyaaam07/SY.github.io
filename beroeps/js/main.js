document.addEventListener('DOMContentLoaded', function() {

    var map = L.map('mapid').setView([52.0907, 5.1214], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);



    var versteningData = [

        L.marker([51.9225, 4.4792]).bindPopup("<b>Rotterdam Centrum</b><br>Hoge verstening. Veel potentieel voor groene daken!"),

    ];

    var potentieelData = [

        L.marker([52.3555, 4.8875]).bindPopup("<b>Amsterdam Zuid-Oost</b><br>Woonwijk met veel tuinen die vergroend kunnen worden."),

    ];

    var succesverhalenData = [

        L.marker([52.0917, 5.1097]).bindPopup("<b>Groen Dak Project Utrecht</b><br>Succesvolle transformatie van een kantoordak.<br><img src='images/project1.jpg' style='width:100px;'> <a href='projecten/project1.html'>Meer info</a>"),

    ];


    var versteningLayer = L.layerGroup(versteningData).addTo(map); // Standaard actief
    var potentieelLayer = L.layerGroup(potentieelData);
    var succesverhalenLayer = L.layerGroup(succesverhalenData);


    var filterButtons = document.querySelectorAll('.filter-button');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {

            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');


            map.removeLayer(versteningLayer);
            map.removeLayer(potentieelLayer);
            map.removeLayer(succesverhalenLayer);


            var layerType = this.dataset.layer;
            switch(layerType) {
                case 'verstening':
                    versteningLayer.addTo(map);
                    break;
                case 'potentieel':
                    potentieelLayer.addTo(map);
                    break;
                case 'succesverhalen':
                    succesverhalenLayer.addTo(map);
                    break;
            }
        });
    });


    document.querySelectorAll('nav ul li a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });


    document.querySelector('.button-primary[href="#map-section"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});