function wpmr_UpdateCat(catName) {
    if (document.getElementById(catName.replace(" ","_")).className == "" ) {
        document.getElementById(catName.replace(" ","_")).className = "wpmr_SelectedCat";

	var newValue = document.getElementById('wpmr_linkTypes').value;
	newValue = newValue.concat(",");
	newValue = newValue.concat(catName);

        document.getElementById('wpmr_linkTypes').value = newValue;

    } else {
        document.getElementById(catName.replace(" ","_")).className = "";

        var currentText = document.getElementById('wpmr_linkTypes').value;

        currentText = currentText.replace("," + catName,"");


        document.getElementById('wpmr_linkTypes').value = currentText;
    }
}