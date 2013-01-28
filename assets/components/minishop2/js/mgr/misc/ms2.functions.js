function formatDate(formatDate, formatString) {
	var yyyy = formatDate.getFullYear();
	var yy = yyyy.toString().substring(2);
	var m = formatDate.getMonth() + 1;
	var mm = m < 10 ? "0" + m : m;
	var d = formatDate.getDate();
	var dd = d < 10 ? "0" + d : d;

	var h = formatDate.getHours();
	var hh = h < 10 ? "0" + h : h;
	var n = formatDate.getMinutes();
	var nn = n < 10 ? "0" + n : n;
	var s = formatDate.getSeconds();
	var ss = s < 10 ? "0" + s : s;

	formatString = formatString.replace(/YYYY/, yyyy);
	formatString = formatString.replace(/YY/, yy);
	formatString = formatString.replace(/MM/, mm);
	formatString = formatString.replace(/M/, m);
	formatString = formatString.replace(/DD/, dd);
	formatString = formatString.replace(/D/, d);
	formatString = formatString.replace(/HH/, hh);
	formatString = formatString.replace(/H/, h);
	formatString = formatString.replace(/NN/, nn);
	formatString = formatString.replace(/N/, n);
	formatString = formatString.replace(/SS/, ss);
	formatString = formatString.replace(/S/, s);

	return formatString;
}

Date.prototype.format = function(format) {
	return formatDate(this, format);
};