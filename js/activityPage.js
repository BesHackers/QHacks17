function setProgressBar(value, total) {
  document.querySelector("#myBar").style.width = (value/total)*100 + "%";
}

setProgressBar(20, 100);

var x=0;
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("parent_id", ev.target.parentNode.id);
    ev.dataTransfer.setData("element_id", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  // Don't allow drags to the same box
  if(ev.target.id == ev.dataTransfer.getData("parent_id")) {
    return;
  }

  var data=ev.dataTransfer.getData("element_id");
	/* If you use DOM manipulation functions, their default behaviour it not to
     copy but to alter and move elements. By appending a ".cloneNode(true)",
     you will not move the original element, but create a copy. */
  if(ev.target.id != "div2") {
    ev = {
      target: ev.target.parentNode,
      dataTransfer: ev.dataTransfer,
      preventDefault: function(){},
    };
    drop(ev)
  } else {
    var nodeCopy = document.getElementById(data).cloneNode(true);
    nodeCopy.id = "newId" + (x++); /* We cannot use the same ID */
    ev.target.appendChild(document.createTextNode(" "));
  	ev.target.appendChild(nodeCopy);
	console.log("GOING TO THE GAME!");
    moveBar(10, 100);
  }
}

function drop1(ev){
    ev.preventDefault();
    // Don't allow drags to the same box
    if(ev.target.id == ev.dataTransfer.getData("parent_id")) {
        return;
    }

    var data = ev.dataTransfer.getData("element_id");
	var el = document.getElementById(data);
	el.parentNode.removeChild(el);
    console.log("GOING HOME");
    moveBar(100, 10)
	
}

function moveBar(startPercent, endPercent){
    var direction;
    
    if (startPercent < endPercent){  //0 to 100
        direction = 1;
    }else{
        direction = -1;
    }
    var currentState = startPercent;
    var target = endPercent;
    var innerBar = document.getElementById("myBar");
	var id = setInterval(frame, 10);
    
    //Animation 
    function frame() {
        console.log("pew!" + currentState + "|" + direction + "|" + target);
        if (currentState < target && direction > 0){
            currentState += direction;        
        } else if (currentState > target && direction < 0) {
            currentState += direction;
        } else {
            clearInterval(id);
        }
        
        innerBar.style.width = currentState + '%'; 
        document.getElementById("label").innerHTML = currentState + '%';
	}
}