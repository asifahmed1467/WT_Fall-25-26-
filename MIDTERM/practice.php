<!DOCTYPE html>
 
<html>
<head>
<title>
DOM Practice
</title>
</head>
    <body>
       
<h1 id="pagetitle">Light Mood </h1>
<button id="switchmotion" onclick="toggle()">Switch </button>
 
 
<script>
 
function toggle()
 
{
var title= document.getElementById("pagetitle");
var button= document.getElementById("switchmotion");
var body =document.body;
 
 
if(body.style.backgroundColor==="black")
{
 
body.style.backgroundColor="white";
title.style.color="black";
title.innerHTML="Light Mood";
button.innerHTML = "Swith to Dark Mood";
 
}
 
else
{
body.style.backgroundColor="black";
title.style.color="white";
title.innerHTML="Dark Mood";
button.innerHTML = "Swith to Light Mood";
 
}
}
</script>
 
</body>
 
 
</html>