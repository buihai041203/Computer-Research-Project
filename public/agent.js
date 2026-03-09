(function(){

let panel = "https://yourpanel.com/api/agent/collect";

let key = document.currentScript.getAttribute("data-key");

fetch(panel,{
method:"POST",
headers:{
"Content-Type":"application/json"
},
body:JSON.stringify({
domain:location.hostname,
key:key,
url:location.href,
referrer:document.referrer
})
});

})();
