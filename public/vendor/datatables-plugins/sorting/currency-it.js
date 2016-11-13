jQuery.extend(jQuery.fn.dataTableExt.oSort, {
	"currency-it-pre": function (a) {
		a = (a==="-") ? 0 : a.replace(/[^\d\-\,]/g, "");
		return parseFloat(a.replace(',', '.'));
	},
	"currency-it-asc": function (a, b) {
		return a - b;
	},
	"currency-it-desc": function (a, b) {
		return b - a;
	}
});