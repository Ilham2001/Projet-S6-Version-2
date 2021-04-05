function fct1() {
    
  var checkBox = document.getElementById("thema");
  if (checkBox.checked == true){
     $("#thema1").css({"display": "block"});
     $("#matiere1").css({"display": "none"});
  } 
}
function fct2() {
  var checkBox = document.getElementById("matiere");
  if (checkBox.checked == true){
    $("#matiere1").css({"display": "block"});
    $("#thema1").css({"display": "none"});

  } 
}

function Clk() {
    alert("ok");
}