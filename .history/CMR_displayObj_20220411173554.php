<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
       
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.min.js" integrity="sha384-PsUw7Xwds7x08Ew3exXhqzbhuEYmA2xnwc8BuD6SEr+UmEHlX8/MCltYEodzWA4u" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
            
            .map, .righ-panel {
                height: 300px;
                width: 50%;
                float: left;
            }
           
            .map, .righ-panel {
                height: 87vh;
                width: 74vw;
                float: left;
            }
            .map {
                border: 1px solid #000;
            }
            h1{
                text-align: center;
            }
            .infomation{
                display: flex;
                padding-top: 81px;
                padding-left: 30px;
            }
        </style>
    </head>
    <body onload="initialize_map();">

        <table>
            <tr>
                <td>
                    <h1>TÌM KIẾM BÃI ĐỖ XE</h1>
                    <div id="map" class="map"></div>
                    <!-- <div id="map" style="width: 80vw; height: 100vh;"></div> -->
                </td>
                <td class="infomation">
                    <div>
                        <div>
                            <span>Search</span><input type="text">
                        </div>
                        <br>
                        <form method="POST">
                            <input type="checkbox" name="showLine" id="showLine" > Hiện thị khu vực Hà Nội Việt Nam
                            <br>
                            <input type="checkbox" name="showPoint" id="showPoint"> Hiện thị các bãi đậu xe ở Hà Nội
                            <button type="submit" name="submit">Button</button>
                        </form>
                        <div id="info"></div>
                    </div>                    
                </td>
            </tr>
        </table>
        <?php include 'CMR_pgsqlAPI.php' ?>
        <?php
        if (isset($_POST['submit'])){

        }
        ?>
        <script>
        //$("#document").ready(function () {
            var format = 'image/png';
            var map;
            var minX = 102.107955932617;
            var minY = 8.30629730224609;
            var maxX = 109.505798339844;
            var maxY = 23.4677505493164;
            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 6;
            function initialize_map() {
                //*
                layerBG = new ol.layer.Tile({
                    source: new ol.source.OSM({})
                });
                //*/
                var pointhn = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/csdltest/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.0',
                            STYLES: '',
                            LAYERS: 'pointhn',
                            
                           
                        }
                    })
                });
                var linehn = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/csdltest/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.0',
                            STYLES: '',
                            LAYERS: 'danhgioihn',
                            
                           
                        }
                    })
                });

                var viewMap = new ol.View({
                    center: ol.proj.fromLonLat([mapLng, mapLat]),
                    zoom: mapDefaultZoom
                    //projection: projection
                });
                map = new ol.Map({
                    target: "map",
                    layers: [layerBG,linehn],
                    //layers: [layerCMR_adm1],
                    view: viewMap
                });
                
                // var styles = {
                //     'MultiPolygon': new ol.style.Style({
                //         stroke: new ol.style.Stroke({
                //             color: 'yellow', 
                //             width: 2
                //         })
                //     })
                // };
                var styleFunction = function (feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    //source: vectorSource,
                    style: styleFunction
                });
                map.addLayer(vectorLayer);

                function createJsonObj(result) {                    
                    var geojsonObject = '{'
                            + '"type": "FeatureCollection",'
                            + '"crs": {'
                                + '"type": "name",'
                                + '"properties": {'
                                    + '"name": "EPSG:4326"'
                                + '}'
                            + '},'
                            + '"features": [{'
                                + '"type": "Feature",'
                                + '"geometry": ' + result
                            + '}]'
                        + '}';
                    return geojsonObject;
                }
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function displayObjInfo(result, coordinate)
                {
                    //alert("result: " + result);
                    //alert("coordinate des: " + coordinate);
					$("#info").html(result);
                }
                map.on('click', function (evt) {
                    //alert("coordinate: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                  
                  //  alert("myPoint: " + myPoint);
                    //*
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'displayObjInfo', paPoint: myPoint},
                
                        success : function (result, status, erro) {
                            console.log("123");
                            displayObjInfo(result, evt.coordinate );
                            //   var myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
                            //     keyboard: true
                            //     })
                            //     // console.log(result);
                            //     if(result!='')
                            //     {

                            //         myModal.show();
                                
                            //         $('#info').html(result);
                            //     }
                                
                          //  console.log(1);
                        },
                        error: function (req, status, error) {
                            console.log(req + " " + status + " " + error);
                        }
                    });
                    //*/
                });
                $("#showPoint").click(function (){
                    if( $(this).is(':checked') ) {
                        // them point
                        // map = new ol.Map({
                        // target: "map",
                        // layers: [layerBG, pointhn,linehn],
                        // //layers: [layerCMR_adm1],
                        // view: viewMap
                        map.addLayer(pointhn);
                        //  });
                    }else{
                        map.removeLayer(pointhn);
                    }
                })
            };
        //});
        </script>
    </body>
</html>