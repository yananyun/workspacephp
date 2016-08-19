if(window.screen.width>0 && /android\s/.test(navigator.userAgent.toLowerCase())){
	var standardDpi,dpi,w;
	w = window.screen.width;
	if(w>0){
		if(w < 320){
			standardDpi = 120;
		}else if(w < 480){
			standardDpi = 160;
		}else if(w < 640){
			standardDpi = 240;
		}else if(w < 960){
			standardDpi = 320;
		}else if(w < 1280){
			standardDpi = 480;
		}else{
			standardDpi = 640;
		}
	}
	dpi = 800*standardDpi/w;
	   document.querySelector("meta[name=viewport]").setAttribute('content','width=640,initial-scale=1.0, maximum-scale=3.0, minimum-scale=1.0,target-densitydpi='+dpi+', user-scalable=0');
}
if ("-ms-user-select" in document.documentElement.style && navigator.userAgent.match(/IEMobile\/10\.0/)) {
		var msViewportStyle = document.createElement("style");
		msViewportStyle.appendChild(
			document.createTextNode("@-ms-viewport{width:640px!important;height:auto!important;}")
		);
		document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
	}

	window.onload = function(){
		$(".loadingbox").hide(0);
	}
