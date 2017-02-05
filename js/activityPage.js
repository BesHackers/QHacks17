moveBarStart = 0; // Naughty global

data = JSON.parse(localStorage.getItem("items"));
if(data == null) {
  data = {
    "Eggs (12)": {
      calories: 936,
      count: 0,
    },
    "Milk (1L)": {
      calories: 436,
      count: 0,
    },
    "Bread (1 Loaf)": {
      calories: 1786,
      count: 0,
    },
    "Bread (2 Loaf)": {
      calories: 1786*2,
      count: 0,
    },
  };
}

goal = document.querySelector("#goal");
goal.value = localStorage.getItem("goal");
goal.addEventListener("change", function(event) {
  updateItems();
});

updateGroceryList();
updateItems();

function AddItem() {
  name = document.querySelector("#new_name").value;
  document.querySelector("#new_name").value = "";
  calorie = document.querySelector("#new_calorie").value;
  document.querySelector("#new_calorie").value = 0;
  data[name] = {
    calories: calorie,
    count: 0,
  };
  updateGroceryList();
  updateItems();
}

function updateGroceryList() {
  grocery_list = document.querySelector("#grocery_list");
  grocery_list.innerHTML = "";
  for(name in data) {
    var row = document.createElement("div");
    row.className = "row";
    var d1 = document.createElement("div");
    d1.className = "col-md-6 text-center";
    var d2 = document.createElement("div");
    d2.className = "col-md-6 text-center";
    row.dataset.name = name;
    row.dataset.calories = data[name].calories;
    d1.appendChild(document.createTextNode(name));
    var input = document.createElement("input");
    input.className = "count";
    input.type = "number";
    input.onchange = function(event) {
      updateItems();
    };
    input.value = data[name].count;
    d2.appendChild(input);
    row.appendChild(d1);
    row.appendChild(d2);
    grocery_list.appendChild(row);
  }
}

function updateItems() {
  grocery_list = document.querySelector("#grocery_list");
  var sum = 0;
  for(el of grocery_list.children) {
    data[el.dataset.name].count = el.childNodes[1].childNodes[0].value;
    sum += data[el.dataset.name].count * el.dataset.calories;
  }
  localStorage.setItem("items", JSON.stringify(data));
  var goal = document.querySelector("#goal").value;
  localStorage.setItem("goal", goal);
  moveBar(100 * sum / goal);
}

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
    moveBar(100);
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
    moveBar(20)

}

function moveBar(end){
    var direction;

    if (moveBarStart < end) direction = 1;
    if (moveBarStart > end) direction = -1;

    var currentState = moveBarStart;
    var target = end;

    var innerBar = document.getElementById("myBar");
	var id = setInterval(frame, 10);

    //Animation
    function frame() {
        moveBarStart = end;
        if (currentState < target && direction > 0){
            currentState += direction;
        }else if (currentState > target && direction < 0) {
            currentState += direction;
        } else {
            clearInterval(id);
            currentState = target;
        }

        innerBar.style.width = currentState + '%';
        document.getElementById("label").innerHTML = currentState + '%';
	}
}
