<?php 
    include 'geoHash.php';
    $ticketmasterapikey = "h7qBbTvXHC4xKc7pcXrFAjzTwLVT0DD5";
    $googlemapsapikey = "AIzaSyByKVdDMj68rGt73isluIxpNu82gX0-UJo";
    $url="https://app.ticketmaster.com/discovery/v2/events.json?";
    if(isset($_POST['submit']))
    {
        $keyword = urlencode($_POST['keyword']);
        $radius = 10;
        if($_POST['distance'])
        {
            $radius = $_POST['distance'];
        }
        $latitude = $_POST['lat'];
        $longitude = $_POST['lon'];
        if($_POST['location']=='strLocation')
        {
            $loc_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($_POST['location2'])."&key=".$googlemapsapikey;
            $urlContent = json_decode(file_get_contents($loc_url), true);
            $latitude = $urlContent['results'][0]['geometry']['location']['lat'];
            $longitude = $urlContent['results'][0]['geometry']['location']['lng'];
        }
        echo "<script> globalLat= ".$latitude."; globalLon=".$longitude.";</script>";
        $hashValue = encode($latitude,$longitude);
        $category = $_POST['type'];
        if($category=='music')
            $segId='KZFzniwnSyZfZ7v7nJ';
        else if($category=='sports')
            $segId='KZFzniwnSyZfZ7v7nE';
        else if($category=='artsandtheater')
            $segId='KZFzniwnSyZfZ7v7na';
        else if($category=='film')
            $segId='KZFzniwnSyZfZ7v7nn';
        else if($category=='miscellaneous')
            $segId='KZFzniwnSyZfZ7v7n1';
        else
            $segId='';
        $segId = urlencode($segId);
        $hashValue = urlencode($hashValue);
        $request = $url."apikey=".$ticketmasterapikey."&keyword=".$keyword."&segmentId=".$segId."&radius=".$radius."&unit=miles&geoPoint=".$hashValue;
        $response = file_get_contents($request);
    }

    if(isset($_GET['id']))
    {
        $url2 = "https://app.ticketmaster.com/discovery/v2/events/".$_GET['id']."?apikey=h7qBbTvXHC4xKc7pcXrFAjzTwLVT0DD5";    
        $response2 = file_get_contents($url2);
        exit(($response2));
    }

    if(isset($_GET['name']))
    {
        $url3 = "https://app.ticketmaster.com/discovery/v2/venues/?apikey=h7qBbTvXHC4xKc7pcXrFAjzTwLVT0DD5&keyword=".urlencode($_GET['name']);
        $response3 = file_get_contents($url3);
        exit(($response3));
    }

?>
<html>
    <head>
        <title>HW6-CSCI571</title>
        <style>
            #map {
               width: 100%;
               height: 400px;
               background-color: grey;
             }
            
            a {
                text-decoration: none !important;
                color: black;
            }
            
            a:hover { 
                color: #828282;
            }
            
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
              }

            table {
                 border-collapse: collapse;
            }

            table, th, td {
                 border: 1px solid #bababa;
            }
            td { 
                padding: 5px;
            }
            hr{
                padding: 0px;
                margin: 0px; 
                background-color: transparent;
                border-top: 1px solid #bababa;
            }
            </style>
        <script
              src="https://maps.googleapis.com/maps/api/js?key=AIzaSyByKVdDMj68rGt73isluIxpNu82gX0-UJo">
        </script>
       
       
        <script>
            window.onload = function(){
                xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET","http://ip-api.com/json",false);
                xmlhttp.send();
                response = xmlhttp.responseText;
                jsonOb = JSON.parse(response);
                if(jsonOb.status!="fail")
                {
                    document.getElementById("search_button").removeAttribute("disabled");
                }
                lat = jsonOb.lat;
                lon = jsonOb.lon;
                document.getElementById("loc1").setAttribute("value",lat);
                document.getElementById("loc2").setAttribute("value",lon);
                if(!document.getElementById("location1").checked)
                {
                    document.getElementById("location2").setAttribute("disabled","true");
                }
            }
            
            function clearForm()
            {
                document.getElementById("keyword").value = "";
                document.getElementById("distance").value = "";
                document.getElementById("location2").value = "";
                document.getElementById("rd1").checked = "true";
                document.getElementById("type").value = "default";
                document.getElementById("overLayer").style.display="none";
                document.getElementById("overlayMap").style.display="none";
                document.getElementById("tableDiv").style.display="none";
                document.getElementById("eventName").style.display="none";
                document.getElementById("eventInfo").style.display="none";
                document.getElementById("eventImage").style.display="none";
                document.getElementById("venueinfo").style.display="none";
                document.getElementById("actualinfo").style.display="none";
                document.getElementById("venueimage").style.display="none";
                document.getElementById("actualimage").style.display="none";
                document.getElementById("overLayer").style.display="none";
                document.getElementById("myd1").style.display="none";
                document.getElementById("myd2").style.display="none";
                document.getElementById("location2").setAttribute("disabled","true");
            }
            
            function activatelocation()
            {
                document.getElementById("location2").removeAttribute("disabled");
            }
            
            function deactivatelocation()
            {
                document.getElementById("location2").setAttribute("disabled","");
            }
                 
            function getEventDetails(id)
            {
                var xmlhttpreq = new XMLHttpRequest();
                var url = "events.php?id="+id;
                xmlhttpreq.open("GET", url, false);
                xmlhttpreq.send();
                var res = xmlhttpreq.responseText;
                jsonInfo = JSON.parse(res);
                
                try
                {
                    if(jsonInfo)
                    {
                        document.getElementById("overLayer").style.display="none";
                        document.getElementById('tableDiv').setAttribute("style","display:none");
                        var htmltext = "";
                        var ename = "";
                        if(jsonInfo.name)
                        {
                            ename+="<h3>"+jsonInfo.name+"</h3>";
                            document.getElementById('eventName').innerHTML=ename;
                        }

                        if(jsonInfo.dates.start.localDate&&jsonInfo.dates.start.localTime)
                        {
                            htmltext+="<b>Date</b><br>"+jsonInfo.dates.start.localDate+" "+jsonInfo.dates.start.localTime+"<br><br>";

                        }

                        if(jsonInfo._embedded.attractions)
                        {
                            htmltext+="<b>Artist/Team</b><br>";
                            var artists = jsonInfo._embedded.attractions;
                            for(var i=0;i<artists.length;i++)
                                {
                                    htmltext+="<a href='"+artists[i].url+"' target='_blank''>"+artists[i].name+"</a>";
                                    if(i!=artists.length-1)
                                        htmltext+=" | ";
                                }
                            htmltext+="<br><br>";
                        }
                        if(jsonInfo._embedded.venues[0].name)
                        {
                            htmltext+="<b>Venue</b><br>"+jsonInfo._embedded.venues[0].name+"<br><br>";
                        }

                        if(jsonInfo.classifications)
                        {

                            try
                            {
                                if(jsonInfo.classifications[0].genre.name!="Undefined")
                                {
                                    htmltext+="<b>Genres</b><br>"+jsonInfo.classifications[0].genre.name;
                                }
                                if(jsonInfo.classifications[0].segment.name&&jsonInfo.classifications[0].segment.name!="Undefined")
                                {
                                        htmltext+=" | "+jsonInfo.classifications[0].segment.name;
                                }
                                if(jsonInfo.classifications[0].subGenre.name&&jsonInfo.classifications[0].subGenre.name!="Undefined")
                                {
                                    htmltext+=" | "+jsonInfo.classifications[0].subGenre.name;
                                }
                                if(jsonInfo.classifications[0].type&&jsonInfo.classifications[0].type!="Undefined")
                                {
                                    htmltext+=" | "+jsonInfo.classifications[0].type.name;
                                }
                                if(jsonInfo.classifications[0].subType&&jsonInfo.classifications[0].subType!="Undefined")
                                {
                                    htmltext+=" | "+jsonInfo.classifications[0].subType.name;

                                }
                            }

                            catch(err)
                            {
                                htmltext+="";
                            }

                            htmltext+="<br><br>";
                            if(jsonInfo.priceRanges)
                            {
                                htmltext+="<b>Price Ranges</b><br>"+jsonInfo.priceRanges[0].min+" - "+jsonInfo.priceRanges[0].max+" "+jsonInfo.priceRanges[0].currency+"<br><br>";
                            }
                            if(jsonInfo.dates.status.code)
                            {
                                htmltext+="<b>Status</b><br>"+jsonInfo.dates.status.code+"<br><br>";
                            }

                            if(jsonInfo.url)
                            {
                                htmltext+="<b>Buy Tickets at</b><br><a href='"+jsonInfo.url+"' target='_blank''>Ticketmaster</a><br><br>";
                            }
                            if(jsonInfo.seatmap)
                            {
                                    var y = document.createElement("img");
                                    y.src = jsonInfo.seatmap.staticUrl;
                                    y.setAttribute("width","600px");
                                    document.getElementById("eventImage").appendChild(y);
                            }
                        }

                        document.getElementById('eventInfo').innerHTML=htmltext;
                    }
                }
                catch(e)
                {
                   document.getElementById('eventInfo').innerHTML="No information to display for this event<br>"; 
                }
                var t = document.createTextNode("click to show venue info");
                var br = document.createElement("br");
                var v = document.getElementById('venueinfo');
                v.appendChild(t);
                v.appendChild(br);
                var darrowimage = document.createElement("img");
                darrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_down.png");
                darrowimage.setAttribute("id","downarrow");
                darrowimage.setAttribute("style","width:30px");
                darrowimage.setAttribute("onclick","showVenueInfo('"+jsonInfo._embedded.venues[0].name+"')");
                v.appendChild(darrowimage);
                
                var t = document.createTextNode("click to show venue photos");
                var br = document.createElement("br");
                var v = document.getElementById('venueimage');
                v.appendChild(t);
                v.appendChild(br);
                var darrowimage = document.createElement("img");
                darrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_down.png");
                darrowimage.setAttribute("id","downarrow2");
                darrowimage.setAttribute("style","width:30px");
                darrowimage.setAttribute("onclick","showVenuePhotos('"+jsonInfo._embedded.venues[0].name+"')");
                v.appendChild(darrowimage);
            }
            
            function showVenueInfo(name)
            {
                var v = document.getElementById('venueinfo');
                v.innerHTML="";
                var t = document.createTextNode("click to hide venue info");
                v.appendChild(t);
                v.appendChild(document.createElement("br"));
                var uarrowimage = document.createElement("img");
                uarrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_up.png");
                uarrowimage.setAttribute("id","uparrow");
                uarrowimage.setAttribute("style","width:30px");
                uarrowimage.setAttribute("onclick","hideVenueInfo('"+name+"')");
                v.appendChild(uarrowimage);
                var xmlhttpreq = new XMLHttpRequest();
                var url = "events.php?name="+name;
                xmlhttpreq.open("GET", url, false);
                xmlhttpreq.send();
                response7 = xmlhttpreq.responseText;
                jsonText = JSON.parse(response7);
                if(jsonText._embedded.venues[0].address.line1||jsonText._embedded.venues[0].city.name||jsonText._embedded.venues[0].postalCode||jsonText._embedded.venues[0].url)
               {
                    var x = document.createElement("TABLE");
                    x.setAttribute("id", "venueTable");
                    x.setAttribute("border", "1");
                    x.setAttribute("width","800px");
                    document.getElementById("actualinfo").appendChild(x);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    a.setAttribute("style","width:200px;");
                    var t = document.createTextNode("Name");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","text-align:center");
                    var t = document.createTextNode(name);
                    a.appendChild(t);
                    y.appendChild(a);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    var t = document.createTextNode("Map");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","width:600px;text-align:center");
                    var d1 = document.createElement("div");
                    var br = document.createElement("br");
                    d1.setAttribute("id","map");
                    d1.setAttribute("style","height:300px;width:500px;display:inline-block;");
                    var d3 = document.createElement("div");
                    d3.setAttribute("style","display:inline-block; width:20%;vertical-align:top; text-align:center;");
                    var d2 = document.createElement("div");
                    d2.setAttribute("style","display:inline-block; width:80px;vertical-align:top; text-align:center;background-color:#e5e5e5;");
                    d3.appendChild(document.createElement("br"));
                    d3.appendChild(d2);
                    var a1 = document.createElement("a");
                    a1.setAttribute("onclick","polyMarkMap('WALKING')");
                    var a2 = document.createElement("a");
                    a2.setAttribute("onclick","polyMarkMap('BICYCLING')");
                    var a3 = document.createElement("a");
                    a3.setAttribute("onclick","polyMarkMap('DRIVING')");
                    var t1 = document.createTextNode("Walk there");
                    var t2 = document.createTextNode("Bike there");
                    var t3 = document.createTextNode("Drive there");
                    a1.appendChild(t1);
                    a2.appendChild(t2);
                    a3.appendChild(t3);
                    d2.appendChild(a1);
                    d2.appendChild(document.createElement("br"));
                    d2.appendChild(a2);
                    d2.appendChild(document.createElement("br"));
                    d2.appendChild(a3);
                    a.appendChild(d2);
                    a.appendChild(d1);
                    y.appendChild(a);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    var t = document.createTextNode("Address");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","text-align:center");
                    if(jsonText._embedded.venues[0].address.line1)
                       var t = document.createTextNode(jsonText._embedded.venues[0].address.line1);
                    else
                        var t = document.createTextNode("N/A");    
                    a.appendChild(t);
                    y.appendChild(a);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    var t = document.createTextNode("City");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","text-align:center");
                    if(jsonText._embedded.venues[0].city.name&&jsonText._embedded.venues[0].state.stateCode)
                       var t = document.createTextNode(jsonText._embedded.venues[0].city.name+", "+jsonText._embedded.venues[0].state.stateCode);
                    else if(jsonText._embedded.venues[0].city.name)
                        var t = document.createTextNode(jsonText._embedded.venues[0].city.name);    
                    else
                        var t = document.createTextNode("N/A");  
                    a.appendChild(t);
                    y.appendChild(a);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    var t = document.createTextNode("Postal Code");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","text-align:center");
                    if(jsonText._embedded.venues[0].postalCode)
                       var t = document.createTextNode(jsonText._embedded.venues[0].postalCode);
                    else
                        var t = document.createTextNode("N/A"); 
                    a.appendChild(t);
                    y.appendChild(a);

                    var y = document.createElement("tr");
                    y.setAttribute("id", "mytr");
                    x.appendChild(y);

                    var a = document.createElement("th");
                    var t = document.createTextNode("Upcoming Events");
                    a.setAttribute("style","text-align:right");
                    a.appendChild(t);
                    y.appendChild(a);

                    var a = document.createElement("td");
                    a.setAttribute("style","text-align:center");
                    var anch = document.createElement("a");
                    anch.setAttribute("href",jsonText._embedded.venues[0].url);
                    anch.setAttribute("target","_blank");
                    var t = document.createTextNode(name+" Tickets");
                    anch.appendChild(t);
                    a.appendChild(anch);
                    y.appendChild(a);
                   
                    l1 = jsonText._embedded.venues[0].location.latitude;
                    l2 = jsonText._embedded.venues[0].location.longitude;
                   
                    map = new google.maps.Map(document.getElementById('map'), {
                      center: {lat: parseFloat(l1), lng: parseFloat(l2)},
                      zoom: 16
                    });
                   
                   var marker = new google.maps.Marker({
                      position: {lat: parseFloat(l1), lng: parseFloat(l2)},
                      map: map
                    });
               }
                else
                {
                    document.getElementById("actualinfo").innerHTML = "No Venue information to display.";
                }

            }
            
            function polyMarkMap(mode)
            {
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var directionsService = new google.maps.DirectionsService;
                var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 14,
                  center: {lat: parseFloat(l1), lng: parseFloat(l2)}
                });
                directionsDisplay.setMap(map);
                directionsService.route({
                  origin: {lat: globalLat, lng: globalLon},  
                  destination: {lat: parseFloat(l1), lng: parseFloat(l2)},
                  travelMode: mode
                }, function(response, status) {
                  if (status == 'OK') {
                    directionsDisplay.setDirections(response);
                  } else {
                    window.alert('Directions request failed due to ' + status);
                  }
                });
            }
            
            function overlayMap(event,lat1,lon1)
            {
                var c1 = event.screenX-40;
                var c2 = event.screenY-70; 
                var x = document.getElementById("overLayer");
                var y = document.getElementById("fixedDiv");
                y.style.left = c1;
                y.style.top = c2;
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
                var ele = document.getElementById('overlayMap');
                ele.setAttribute("lat4", lat1);
                ele.setAttribute("lon4", lon1);
                mapOver = new google.maps.Map(document.getElementById('overlayMap'), {
                      center: {lat: lat1, lng: lon1},
                      zoom: 16
                    });
                   
               var marker = new google.maps.Marker({
                  position: {lat: lat1, lng: lon1},
                  map: mapOver
                });            
            }
            
            function showVenuePhotos(name)
            {
                var xmlhttpreq = new XMLHttpRequest();
                var url = "events.php?name="+name;
                xmlhttpreq.open("GET", url, false);
                xmlhttpreq.send();
                response9 = xmlhttpreq.responseText;
                jsonText = JSON.parse(response9);
                var v = document.getElementById('venueimage');
                document.getElementById('actualimage').setAttribute("style","border:1px solid #bababa; width:800px");
                v.innerHTML="";
                var t = document.createTextNode("click to hide venue photos");
                v.appendChild(t);
                v.appendChild(document.createElement("br"));
                var uarrowimage = document.createElement("img");
                uarrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_up.png");
                uarrowimage.setAttribute("id","uparrow2");
                uarrowimage.setAttribute("style","width:30px");
                uarrowimage.setAttribute("onclick","hideVenuePhoto('"+name+"')");
                v.appendChild(uarrowimage);
                jsonText = JSON.parse(response9);
                var a = document.getElementById('actualimage');
                if(jsonText._embedded.venues[0].images)
                {
                    for(var j=0;j<jsonText._embedded.venues[0].images.length;j++)
                    {
                        var i = document.createElement("img");
                        var br = document.createElement("br");
                        i.setAttribute("src",jsonText._embedded.venues[0].images[j].url);
                        i.setAttribute("style","max-width: 600px;");
                        a.appendChild(i);
                        if(j<jsonText._embedded.venues[0].images.length-1)
                            a.appendChild(document.createElement("hr"));
                    }
                }
                else
                {
                    var t = document.createTextNode("No Venue Photos Found");
                    a.appendChild(t);
                }
                a.scrollTop = a.scrollHeight;
                
            }
            
            function hideVenuePhoto(name)
            {
                document.getElementById('actualimage').innerHTML="";
                document.getElementById('actualimage').setAttribute("display","none");
                var v = document.getElementById('venueimage');
                v.innerHTML = "";
                v.appendChild(document.createTextNode("click here to show venue photos"));
                v.appendChild(document.createElement("br"));
                var uarrowimage = document.createElement("img");
                uarrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_down.png");
                uarrowimage.setAttribute("id","downarrow2");
                uarrowimage.setAttribute("style","width:30px");
                uarrowimage.setAttribute("onclick","showVenuePhotos('"+name+"')");
                v.appendChild(uarrowimage);
            }
            
            function hideVenueInfo(name)
            {
                document.getElementById('actualinfo').innerHTML="";
                var v = document.getElementById('venueinfo');
                v.innerHTML = "";
                v.appendChild(document.createTextNode("click here to show venue info"));
                v.appendChild(document.createElement("br"));
                var uarrowimage = document.createElement("img");
                uarrowimage.setAttribute("src","http://csci571.com/hw/hw6/images/arrow_down.png");
                uarrowimage.setAttribute("id","downarrow");
                uarrowimage.setAttribute("style","width:30px");
                uarrowimage.setAttribute("onclick","showVenueInfo('"+name+"')");
                v.appendChild(uarrowimage);
            }
            
            function search(jsonD)
            {
                    var jsonData = JSON.parse(jsonD);
                    root = jsonData.documentElement;
                    if(!jsonData._embedded)
                    {
                        var t = document.createTextNode("No search results were found");
                        document.getElementById('tableDiv').appendChild(t);
                    }
                    else
                    {
                        events = jsonData._embedded.events;
                        if(events.length!=0&&document.getElementById('tableDiv').childElementCount==0)
                        {
                            var x = document.createElement("TABLE");
                            x.setAttribute("id", "dataTable");
                            x.setAttribute("border", "1");
                            document.getElementById("tableDiv").appendChild(x);

                            var x = document.createElement("tr");
                            x.setAttribute("id", "mytr");
                            document.getElementById("dataTable").appendChild(x);

                            var x = document.createElement("th");
                            var t = document.createTextNode("Date");
                            x.appendChild(t);
                            document.getElementById("mytr").appendChild(x);

                            var x = document.createElement("th");
                            var t = document.createTextNode("Icon");
                            x.appendChild(t);
                            document.getElementById("mytr").appendChild(x);

                            var x = document.createElement("th");
                            var t = document.createTextNode("Event");
                            x.appendChild(t);
                            document.getElementById("mytr").appendChild(x);

                            var x = document.createElement("th");
                            var t = document.createTextNode("Genre");
                            x.appendChild(t);
                            document.getElementById("mytr").appendChild(x);

                            var x = document.createElement("th");
                            var t = document.createTextNode("Venue");
                            x.appendChild(t);
                            document.getElementById("mytr").appendChild(x);

                            for(var j=0;j<events.length;j++)
                            {
                                var eventNode = events[j];
                                lat1 = parseFloat(eventNode._embedded.venues[0].location.latitude);
                                lon2 = parseFloat(eventNode._embedded.venues[0].location.longitude);
                                var eventid = eventNode.id;
                                var x = document.createElement("tr");
                                x.setAttribute("id", "mytr"+j);
                                document.getElementById("dataTable").appendChild(x);

                                var x = document.createElement("td");
                                var y = document.createElement("br");
                                var t = document.createTextNode(eventNode.dates.start.localDate);
                                x.appendChild(t);
                                x.appendChild(y);
                                var t2 = document.createTextNode("N/A");
                                if(eventNode.dates.start.localTime)
                                {
                                    var t2 = document.createTextNode(eventNode.dates.start.localTime);
                                    x.appendChild(t2);
                                }
                                document.getElementById("mytr"+j).appendChild(x);

                                var x = document.createElement("td");
                                var y = document.createElement("img");
                                y.src = eventNode.images[0].url;
                                y.setAttribute("width","80px");
                                x.appendChild(y);
                                document.getElementById("mytr"+j).appendChild(x);

                                var x = document.createElement("td");
                                var a = document.createElement("a");
                                a.setAttribute("href","javascript:;");
                                a.setAttribute("onclick","getEventDetails('"+eventid+"')");
                                var t = document.createTextNode(eventNode.name);
                                a.appendChild(t);
                                x.appendChild(a);
                                document.getElementById("mytr"+j).appendChild(x);

                                var x = document.createElement("td");
                                var t = document.createTextNode("N/A");
                                if(eventNode.classifications)
                                {
                                    var t = document.createTextNode(eventNode.classifications[0].segment.name);   
                                }
                                x.appendChild(t);
                                document.getElementById("mytr"+j).appendChild(x);

                                var x = document.createElement("td");
                                var anchor = document.createElement("a");
                                var t = document.createTextNode("N/A");
                                if(eventNode._embedded.venues)
                                {
                                    anchor.setAttribute("href","javascript:;");
                                    anchor.setAttribute("onclick","overlayMap(event,"+lat1+","+lon2+")");
                                    var t = document.createTextNode(eventNode._embedded.venues[0].name);
                                }
                                anchor.appendChild(t);
                                x.appendChild(anchor);
                                document.getElementById("mytr"+j).appendChild(x);
                            }
                        var ab = document.createElement("div");
                        ab.setAttribute("style","height:220px");
                        document.getElementById('tableDiv').appendChild(ab);
                        }
                    }
            }
            
            function polyMarkMapOverlay(mode)
            {
                var lat5 = parseFloat(document.getElementById('overlayMap').getAttribute('lat4'));
                var lon5 = parseFloat(document.getElementById('overlayMap').getAttribute('lon4'));
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var directionsService = new google.maps.DirectionsService;
                var map3 = new google.maps.Map(document.getElementById('overlayMap'), {
                  zoom: 14,
                  center: {lat: lat5, lng: lon5}
                });
                directionsDisplay.setMap(map3);
                directionsService.route({
                  origin: {lat: globalLat, lng: globalLon},  
                  destination: {lat: lat5, lng: lon5},
                  travelMode: mode
                }, function(response, status) {
                  if (status == 'OK') {
                    directionsDisplay.setDirections(response);
                  } else {
                    window.alert('Directions request failed due to ' + status);
                  }
                });
            }
            
        </script>
    </head>
    
    <body>
        <center>
            <form name="myform" id="myform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="width:50%;text-align:left">
                <fieldset style="text-align:left">
                    <center><h1><I>Events Search</I></h1></center>
                    <hr>
                    <b>Keyword</b><input type="text" id="keyword" name="keyword" required value="<?php echo isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>">
                    <br>
                    <b>Category</b>
                    <input type="hidden" name="lat" id="loc1" value="<?php echo isset($_POST['lat']) ? $_POST['lat'] :''  ?>">
                    <input type="hidden" name="lon" id="loc2" value="<?php echo isset($_POST['lon']) ? $_POST['lon'] :''  ?>">

                    <select name="type" id="type">
                        <option value="default" <?php if($_POST['type']=="default") echo "selected='selected'"?>>Default</option>
                        <option value="music" <?php if($_POST['type']=="music") echo "selected='selected'"?>>Music</option>
                        <option value="sports" <?php if($_POST['type']=="sports") echo "selected='selected'"?>>Sports</option>
                        <option value="artsandtheater" <?php if($_POST['type']=="artsandtheater") echo "selected='selected'"?>>Arts & Theatre</option>
                        <option value="film" <?php if($_POST['type']=="film") echo "selected='selected'"?>>Film</option>
                        <option value="miscellaneous" <?php if($_POST['type']=="miscellaneous") echo "selected='selected'"?>>Miscellaneous</option>   
                    </select>
                    <br>
                    <b>Distance(miles)</b><input type="text" id="distance" name="distance" placeholder="10" value="<?php echo isset($_POST['distance']) ? $_POST['distance'] : '' ?>">
                    <b>from</b><input type="radio" id="rd1" name="location" checked id="here" value = "here" onclick="deactivatelocation()" <?php if(isset($_POST['location'])&&$_POST[location]=='here')  echo 'checked';?>>Here
                    <br> 
                    
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    
                    <input type="radio" name="location" id="location1" value="strLocation" onclick="activatelocation()" <?php if(isset($_POST['location'])&&$_POST[location]=='strLocation')  echo 'checked';?>>
                    <input type="text" id="location2" name="location2" placeholder="location" required value="<?php echo isset($_POST['location2']) ? $_POST['location2'] : '' ?>">
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="submit" value="Search" name="submit" id="search_button" onclick="search()" disabled>
                    <input type="button" value="Clear" onclick="clearForm()">
                </fieldset>
                <br><br>
              </form>
               
               <div id="megaDiv" style="text-align:center max-width:80%;">
                    <div id="tableDiv"></div>
                    <div id="fixedDiv" style="position:fixed">
                        <div id="overLayer" style="width:400px;height:250px;position:relative;display:none">
                            <div id="overlayMap" style="width:400px;height:250px;position:absolute;left:0;z-index:2"></div>
                            <div id="myd1" style="position:absolute;left:0;width:90px;background-color:#c9c7c7;z-index:5">
                                <a href="javascript:;" onclick="polyMarkMapOverlay('WALKING')">Walk there</a></div><br>
                            <div id="myd2" style="position:absolute;left:0;width:90px;background-color:#c9c7c7;z-index:5">
                                <a href="javascript:;" onclick="polyMarkMapOverlay('BICYCLING')">Bike there</a></div><br>
                            <div id="myd3" style="position:absolute;left:0;width:90px;background-color:#c9c7c7;z-index:5">
                                <a href="javascript:;" onclick="polyMarkMapOverlay('DRIVING')">Drive there</a></div>
                        </div>
                    </div>
                    <div id="eventName"></div>
                    <div style=" display: flex; align-items: center;justify-content: center">
                        <div id="eventInfo" style="display:inline-block; width:300px; text-align:left;padding-left:30px;"></div>
                        <div id="eventImage" style="display:inline-block; max-width:800px; text-align:left;"></div>
                    </div>
                </div>
                
                <div id="venueinfo" style="text:align:center"></div>
                <div id="actualinfo"></div>
                <br>
                <div id="venueimage" style="text:align:center"></div>
                <div id="actualimage"></div>
            <script>
                <?php
                    echo 'var jsonD = ' . json_encode($response) . ';';
                    echo 'search(jsonD);';
                ?>
            </script>
        </center>
    </body>
</html>