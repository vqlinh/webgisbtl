
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Webgis BƯU ĐIỆN HÀ NỘI</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
       
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.min.js" integrity="sha384-PsUw7Xwds7x08Ew3exXhqzbhuEYmA2xnwc8BuD6SEr+UmEHlX8/MCltYEodzWA4u" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <!-- <link rel="stylesheet" href="css/index.css"> -->
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

    </head>
    <body onload="initialize_map();">
    <!-- <div class="header-right col-6 text-end header-meta">
        
        
    </div> -->
    

        <table>
            <tr>
                <td>
                    
               


                    <div id="map" class="map">


                                <h1 class="map-title">BẢN ĐỒ BƯU ĐIỆN</h1>
                                            <form method="POST">
                                                <!-- <input type="text" name="search" class="input-search"   > -->
                                                <div class="search-box">
                                                    <div class="form-field">
                                                        <input class="search-box-input" name="search" type="text" placeholder=" " >
                                                        <label class="form-label" for="name">Tìm Kiếm</label>
                                                     <button class="search-box-btn" type="submit" name="submit">    
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                     </button>
                                                     <!-- <span class="btnspan">tìm kiếm</span> -->

                                                    </div>                                                                                                                                
                                                    <!-- <input type="submit" name="submit" value="Tìm" >   -->
                                                    <div class="btnshowall">
                                                        <input type="submit" name="submitAll" value="Hiển thị tất cả"  >  
                                                    </div>
                                                </div>
                                            
                                            </form>
                                            <div class="information">
                                            <form method="POST">
                                                <input type="checkbox" name="showLine"  id="showLine" > Hiện thị khu vực Hà Nội Việt Nam
                                                <br>
                                                <input type="checkbox" name="showPoint"class="showLine" id="showPoint"> Hiện thị các bưu điện
                                                <br>
                                                <div id="info" class="info"> </div>

                                                
                                            </form>
                                           

                                            </div> 
                    </div>
                </td>

            </tr>
        </table>


        <?php include 'CMR_pgsqlAPI.php' ?>
        <?php
        if (isset($_POST['submit'])){
            $search = $_POST['search'];
            if ($search !=""){
                getSearch($search);
            }
        }
        if (isset($_POST['submitAll'])){
            getSearch("");
        }
        ?>


        <script>  
        //$("#document").ready(function () {
            var format = 'image/png';
            var map;
            var minX = 105.281219482422;
            var minY = 20.5604095458984;
            var maxX = 106.023750305176;
            var maxY = 21.38938331604;
            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 10;
            function initialize_map() {
                //*
                layerBG = new ol.layer.Tile({
                    source: new ol.source.OSM({})
                });
                //*/
                var pointpo = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/example/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.0',
                            STYLES: '',
                            LAYERS: 'pointpo',
                            
                           
                        }
                    })
                });
                var linehn = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/example/wms?',
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
                    layers: [layerBG],
                    //layers: [layerCMR_adm1],
                    view: viewMap
                });

                // mousePosition
                var mousePosition= new ol.control.MousePosition({
                    class:'mousePosition',
                    projection:'EPSG:4326',
                    coordinateFormat: function(coordinate){return ol.coordinate.format(coordinate,'{y},{x}' ,6);}       
                });
                map.addControl(mousePosition);
                // scale 
                var scaleControl = new ol.control.ScaleLine({
                    bar:true,
                    text:true 

                });
                map.addControl(scaleControl);

                var styles = {
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'orange'
                        })
                    })
                };
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
                const model = document.querySelector(".info")

                function displayObjInfo(result, coordinate)
                {
                    model.style.display = "block";
                
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
                        data: {functionname: 'getInfoCMRToAjax', paPoint: myPoint},
                
                        success : function (result, status, erro) {
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
                        map.addLayer(pointpo);
                    }else{
                        map.removeLayer(pointpo);
                    }
                })
                $("#showLine").click(function (){
                    if( $(this).is(':checked') ) {
                        map.addLayer(linehn);
                    }else{
                        map.removeLayer(linehn);
                    }
                })
            };
        //});
        </script>
        <script src="jsindex/main.js"></script>

    </body>
</html>