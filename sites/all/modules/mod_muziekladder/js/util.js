/* 
 * polyfill 
 * localstorage
 */
(function (isStorage) {
    if (!isStorage) {
        var data = {},
            undef;
        window.localStorage = {
            setItem     : function(id, val) { return data[id] = String(val); },
            getItem     : function(id) { return data.hasOwnProperty(id) ? data[id] : undef; },
            removeItem  : function(id) { return delete data[id]; },
            clear       : function() { return data = {}; }
        };
    }
})((function () {
    try {
        return "localStorage" in window && window.localStorage != null;
    } catch (e) {
        return false;
    }
})());
/*
 * global utility functions
 * */
hC.isArray = function (obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}

hC.urlStringGet=function(){
	var rv = {},s=[];
	var gets = location.href.match(/[a-zA-Z0-9-_]+=[^&]+/g);
	if (gets){
		var l = gets.length; 
		for(var i =0; i<l; i++){
			s = gets[i].split('='); 
			rv[s[0]]=s[1];
		}
	}
	return rv; 
}

hC.objectKeys = function(obj){
	var rv = [];
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {
			rv.push(key);
		}
	}
	return rv; 
}
