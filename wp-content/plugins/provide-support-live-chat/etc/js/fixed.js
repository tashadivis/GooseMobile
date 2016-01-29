$ = jQuery.noConflict();
var BUTTONTEXT = 'ProvideSupport : chat button code loaded';
var HIDDENTEXT = 'ProvideSupport : hidden monitoring code loaded';

$(document).ready(function(){
//console.log('fixed.js load');
	var loaded = false;
	var f7s = f7php.settings;

	showChat = function(code){
		code = code || '';
		var codeBlock = document.createElement('div');
		codeBlock.className = 'f7x';
		document.body.appendChild(codeBlock);
			$('.f7x').html(code);

			if(f7s.buttonLocation == 'fixed'){

			/*
			conf = {
				'position'	: 'fixed',
				'width' 	: 'auto',
				'height'	: 'auto',
				'z-index'	: 10000
				}
			$('.f7x').css(conf);
			*/

			$('.f7x').css(f7s.buttonLocationHorizontalFrom,f7s.buttonLocationHorizontalValue + f7s.buttonLocationHorizontalBy);
			$('.f7x').css(f7s.buttonLocationVerticalFrom,f7s.buttonLocationVerticalValue + f7s.buttonLocationVerticalBy);

			}
		loaded = true;

	}
//console.log(f7php);

	if(f7s.buttonLocation == 'fixed'){if(f7php.page == '1'){
		if(f7s.buttonAvailablePages+'' == 'true'){
			if(f7s.buttonAvailablePagesWhich == 'all'){
				showChat(f7php.code);
				// console.log(BUTTONTEXT);
			}else{
				for(pageid in f7s.buttonAvailablePagesList){
					if(f7php.pageid == f7s.buttonAvailablePagesList[pageid]){showChat(f7php.code);
						// console.log(BUTTONTEXT);
					}
					}
				}
			}
		}
		if(f7s.buttonAvailablePosts+'' == 'true' && f7php.single == '1' && !loaded){showChat(f7php.code);
			// console.log(BUTTONTEXT);
		}
		if(f7s.buttonAvailableOther+'' == 'true' && f7php.other == '1' && !loaded){showChat(f7php.code);
			// console.log(BUTTONTEXT);
		}
		if(f7s.buttonAvailableMain+'' == 'true' && f7php.frontpage == '1' && !loaded){showChat(f7php.code);
			// console.log(BUTTONTEXT);
		}
	}
	if(typeof($('#f7widgetBlock').html()) !== 'undefined') loaded = true;
	if(f7s.buttonAvailableWhole+'' == 'true' && !loaded){
		showChat(f7php.hiddencode);
		// console.log(HIDDENTEXT);
		}
	});
