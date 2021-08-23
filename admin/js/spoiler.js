function open_close(id_spol) {
var obj = "";
if (document.getElementById) obj = document.getElementById(id_spol).style;
else if (document.all) obj = document.all[id_spol];
else if (document.layers) obj = document.layers[id_spol];
else return 1;

if (obj.display == "") obj.display = "none";
else if (obj.display != "none") obj.display = "none";
else obj.display = "block";
}