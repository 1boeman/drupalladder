var laad = (function(){
	var loadedFiles = [],
		pub = {"pathConfig":{}},
		loading = 0, //number of files currently loading
		globalCallbacks = [],waitingToLoad=[],
		readyHandler = function(src,callback){
			loadedFiles[src] = 1;
			if (callback)  callback();
			loading--;
			if (!loading){
				if (globalCallbacks.length){
					for (var x in globalCallbacks) globalCallbacks[x]();
					globalCallbacks = [];
				}
				if(waitingToLoad.length){
					var args = waitingToLoad.shift();
					pub.js(args[0],args[1]);
				} 	
			}
		},getPath = function(str){
			if (typeof pub.pathConfig[str] !== 'undefined'){
				return pub.pathConfig[str];
			}
			return str; 
		}
		loadFile = function(src,callback){
			if (loadedFiles[src] === undefined){
				var js = document.createElement('script');
				if (js.addEventListener) { // normal browsers
					js.addEventListener('load', function(){
						readyHandler(src,callback);
				  }, false);
				} else {
					js.onreadystatechange = function() { // old IEs
					if (js.readyState in {loaded: 1, complete: 1}) {
						js.onreadystatechange = null;
						readyHandler(src,callback);
					}
				  };
				}
				js.async = true;
				js.src = src;
				loading++;
				var first_js = document.getElementsByTagName('script')[0];
				first_js.parentNode.insertBefore(js, first_js);
			}else{
				if (callback) callback();
			}
		}; 
	
	/*	Use the wait method to load files after 
	 *  all previously started laad-downloads have finished.
	 *  This may be necessary in case of file dependencies
	 */

	pub.wait = function(src,callback){
		if (!loading) {
			pub.js(src,callback);
		}else{
			waitingToLoad.push([src,callback]);
		}
	}; 

	/* Use js method to load javascript files */

	pub.js = function(src,callback){
		if (typeof src === 'undefined'){
			throw new Error("loadJS argument error");
		}else if(typeof src === 'string'){
			src = getPath(src);
			loadFile(src,callback);
		}else if (Object.prototype.toString.call(src) === '[object Array]' && src.length){
			if  (callback) globalCallbacks.push(callback); 
		
			for (var i = 0; i < src.length; i++){
				loadFile(getPath(src[i]));
			}
		}
	}; 
	
	return pub;
}());
