f(){|a|}

function showit(n, block) {
	alert(block());
}

showit(1) {
	return 1 + 2;
};

function reduce(ar, n, f) {
	for (var i in ar)
		n = f(ar[i]);
}

var sum = reduce([1,2,3,4], 0) {|a,b| return a+b};
alert(sum);