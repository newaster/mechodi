//[custom Javascript]
//Project:	Aero - Responsive Bootstrap 4 Template
//Version:  1.0
//Last change:  15/12/2019
//Primary use:	Aero - Responsive Bootstrap 4 Template
//should be included in all pages. It controls some layout
$(function() {
    "use strict";
    initSparkline();
    initC3Chart();    
});

function initSparkline() {
    $(".sparkline").each(function() {
        var $this = $(this);
        $this.sparkline('html', $this.data());
    });
}

function initC3Chart() {
    setTimeout(function(){ 
        $(document).ready(function(){
            var chart = c3.generate({
                bindto: '#chart-area-spline-sracked', // id of chart wrapper
                data: {
                    columns: [
                        // each columns data
                        ['data1', 21, 8, 32, 18, 19, 17, 23, 12, 25, 37],
                        ['data2', 7, 11, 5, 7, 9, 16, 15, 23, 14, 55],
                        ['data3', 13, 7, 9, 15, 9, 31, 8, 27, 42, 18],
                    ],
                    type: 'area-spline', // default type of chart
                    groups: [
                        [ 'data1', 'data2', 'data3']
                    ],
                    colors: {
                        'data1': Aero.colors["gray"],
                        'data2': Aero.colors["teal"],
                        'data3': Aero.colors["lime"],
                    },
                    names: {
                        // name of each serie
                        'data1': 'Revenue',
                        'data2': 'Returns',
                        'data3': 'Queries',
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        // name of each category
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Oct']
                    },
                },
                legend: {
                    show: true, //hide legend
                },
                padding: {
                    bottom: 0,
                    top: 0,
                },
            });
        });    
        $(document).ready(function(){
           
        });
        $(document).ready(function(){
           
        });
}, 500);
}
setTimeout(function(){
    "use strict";
     var plants = [
    {name: 'VAK', coords: [21.00, 77.52], status: 'closed' },
    {name: 'MZFR', coords: [26.20, 78.52], status: 'closed' },
    {name: 'AVR', coords: [26.50, 75.52], status: 'closed'},
    {name: 'KKR', coords: [22.00, 75.22], status: 'closed'},
    {name: 'KRB', coords: [21.55, 78.12], status: 'active'}, 
    {name: 'THTR', coords: [24.00, 76.52], status: 'closed'},
    {name: 'KKE', coords: [23.00, 78.52], status: 'active' }
  ];
    
    
    var mapData = {
         
        "IN": 2000000, 
    };	
    if( $('#world-map-markers').length > 0 ){
        $('#world-map-markers').vectorMap({
            
            map: 'in_mill',
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderOpacity: 0.25, 
            color: '#e6e6e6',
            regionStyle : {
                initial : {
                fill : '#60bafd'
                }
            },
             

            markerStyle: {
            initial: {
                         r: 5,
                        'fill': '#fff',
                        'fill-opacity':1,
                        'stroke': '#000',
                        'stroke-width' : 1,
                        'stroke-opacity': 0.4
                    },
                },
        
            markers : plants.map(function(h){ return {name: h.name, latLng: h.coords} }),

             series: {
                    markers: [{
                    attribute: 'image',
                    scale: {
                      closed: 'http://localhost/mbr/assets/images/map_pins/icon-np-3.png',
                      active: 'http://localhost/mbr/assets/images/map_pins/icon-np-2.png'
                    },
                    values: plants.reduce(function(p, c, i){ p[i] = c.status; return p }, {}),
                    legend: {
                      horizontal: true,
                      title: 'Plant status',
                      labelRender: function(v){
                        return {
                          closed: 'Closed',
                          active: 'Active' 
                        }[v];
                      }
                    }
                  }] 
                },
                
            hoverOpacity: null,
             
            normalizeFunction: 'linear',
            zoomOnScroll: true,
            scaleColors: ['#000000', '#000000'],
            selectedColor: '#000000',
            selectedRegions: [],
            enableZoom: true,
            hoverColor: '#fff', 
            onLoad: function(event, map)
            {
                //$('#world-map-markers').vectorMap('zoomIn');
            },
            onRegionClick: function(e,  code,  isSelected,  selectedRegions){
                $('#world-map-markers').vectorMap('get','mapObject').setFocus({region: code});
            }
        });
    }
}, 800);

