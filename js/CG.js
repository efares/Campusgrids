
if (!localStorage.gridid || !localStorage.first_name || !localStorage.email || !localStorage.password || !localStorage.account_type) {
	localStorage.clear();
	window.location = "index.html";
}

function getKeyValue(key) {
    key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
    var match = location.search.match(new RegExp("[?&]" + key + "=([^&]+)(&|$)"));
    return match && decodeURIComponent(match[1].replace(/\+/g, " "));
}