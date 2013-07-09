
if (!localStorage.gridid || !localStorage.first_name || !localStorage.email || !localStorage.password || !localStorage.account_type) {
	localStorage.clear();
	window.location = "index.html";
}

$('input:checkbox').change(function (){
	if (this.checked)
		$('label[for="' + this.id + '"]').addClass('ui-btn-active');
	else
		$('label[for="' + this.id + '"]').removeClass('ui-btn-active');
});

function getKeyValue(key) {
    key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
    var match = location.search.match(new RegExp("[?&]" + key + "=([^&]+)(&|$)"));
    return match && decodeURIComponent(match[1].replace(/\+/g, " "));
}