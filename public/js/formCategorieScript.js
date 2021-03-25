function fct1() {
    
  var checkBox = document.getElementById("thema");
  if (checkBox.checked == true){
     $("#thema1").css({"display": "block"});
     $("#matiere1").css({"display": "none"});
      //document.getElementById('ici').innerHTML ='<label>Thematique</label><input type="text" name="Thematique"/>';
  } 
}
function fct2() {
  var checkBox = document.getElementById("matiere");
  if (checkBox.checked == true){
    $("#matiere1").css({"display": "block"});
    $("#thema1").css({"display": "none"});
    //document.getElementById('ici').innerHTML ='<label>Matiere</label><input type="text" name="Matiere"/>';
  } 
}
    
    /*$(document).ready(function() {
      $("select.Type").change(function() {
        var selectedCountry = $(".Type option:selected").text();
        if (selectedCountry == "Vrai_Faux") {
          alert("You have selected the language - Hindi");
        } else if (selectedCountry == "Nepal") {
          alert("You have selected the language - Nepali");
        }
      });
    });
    */
function Clk() {
    alert("ok");
}