function setProgressBar(value, total) {
  document.querySelector("#myBar").style.width = (value/total)*100 + "%";
  console.log((value/total) + "%");
}

setProgressBar(20, 100);

var x=0;
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  var data=ev.dataTransfer.getData("text");
	/* If you use DOM manipulation functions, their default behaviour it not to
     copy but to alter and move elements. By appending a ".cloneNode(true)",
     you will not move the original element, but create a copy. */
	var nodeCopy = document.getElementById(data).cloneNode(true);
	nodeCopy.id = "newId" + (x++); /* We cannot use the same ID */
	ev.target.appendChild(nodeCopy);
}

function drop1(ev){
	ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
	var el = document.getElementById(data);
	el.parentNode.removeChild(el);
}
