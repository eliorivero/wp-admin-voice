var WPAVoice,
	finalCommand = '',
	isListening = false,
	lastCommand = '';

if ( 'webkitSpeechRecognition' in window ) {
	var voiceRecognition = new webkitSpeechRecognition();
	voiceRecognition.continuous = WPAVoice.continuous;
	voiceRecognition.lang = WPAVoice.language;

	voiceRecognition.onstart = function() {
		isListening = true;
		micImage.src = WPAVoice.micAnimate;
	};

	voiceRecognition.onerror = function ( event ) {
		if ( 'no-speech' === event.error ) {
			micImage.src = WPAVoice.mic;
			results.innerHTML = WPAVoice.errors.noSpeech;
			startListening();
		}
		if ( 'audio-capture' === event.error ) {
			micImage.src = WPAVoice.mic;
			results.innerHTML = WPAVoice.errors.noAudio;
		}
		if ( 'not-allowed' === event.error ) {
			results.innerHTML = WPAVoice.errors.blocked;
		}
	};

	voiceRecognition.onend = function () {
		isListening = false;

		micImage.src = WPAVoice.mic;
		if ( ! finalCommand ) {
			return;
		}

		startListening();
	};

	voiceRecognition.onresult = function ( event ) {
		for ( var i = event.resultIndex; i < event.results.length; ++i ) {
			if ( event.results[i].isFinal ) {
				finalCommand = event.results[i][0].transcript;
			}
		}
		results.innerHTML = finalCommand;

		console.log( 'finalCommand ' + finalCommand + ' lastCommand ' + lastCommand );
		if ( finalCommand !== lastCommand ) {
			process( finalCommand.trim() );
		}
	};

	function deHighlight() {
		Array.prototype.forEach.call( document.querySelectorAll( '.wpavoice-active' ), function( item ) {
			item.classList.remove( 'wpavoice-active' );
		});
	}

	function process( currentCommand ) {
		if ( '' === currentCommand ) {
			return;
		}

		finalCommand = currentCommand.toLowerCase();
		var element = '',
			click = document.createEvent( 'HTMLEvents' );
		click.initEvent( 'click', true, false );

		if ( '' === lastCommand ) {

			// Take user to a different screen
			if ( WPAVoice.takemeto[ finalCommand ] ) {
				lastCommand = '';
				window.location = WPAVoice.base_url + WPAVoice.takemeto[ finalCommand ];
			}

			// Is this a command?
			console.log( 'finalCommand? ' + finalCommand + ' dothis ' + WPAVoice.dothis[ finalCommand ] );
			if ( WPAVoice.dothis[ finalCommand ] ) {
				element = WPAVoice.dothis[ finalCommand ];
				console.log( 'element ' + element );
				deHighlight();
				switch ( element ) {
					case 'title':
						lastCommand = finalCommand;
						document.getElementById( element ).classList.add( 'wpavoice-active' );
						document.getElementById( element ).dispatchEvent( click );
						break;

					case 'content':
					case 'content-new':
						lastCommand = finalCommand;
						document.getElementById( 'wp-content-editor-container' ).classList.add( 'wpavoice-active' );
						break;

					case 'publish':
						document.getElementById( element ).dispatchEvent( click );
						break;

					case 'save-post':
						document.getElementById( element ).dispatchEvent( click );
						break;

					case 'trash':
						document.querySelector( '#delete-action > .submitdelete' ).dispatchEvent( click );
						break;

					case 'trash-specific':
						lastCommand = finalCommand;
						break;

					case 'cancel':
						lastCommand = '';
						break;
				}
			}
		} else if ( finalCommand !== lastCommand ) {
			console.log( document.getElementById( WPAVoice.dothis[ lastCommand ] ) );

			element = WPAVoice.dothis[ lastCommand ];

			switch ( element ) {
				case 'title':
					document.getElementById( element ).value = currentCommand;
					break;

				case 'content':
					tinyMCE.activeEditor.execCommand( 'mceInsertContent', true, currentCommand );
					break;

				case 'content-new':
					tinyMCE.activeEditor.execCommand( 'mceSetContent', true, currentCommand );
					break;

				case 'trash-specific':
					var entryNumber = parseInt( finalCommand, 10 );
					console.log( 'entryNumber ' + entryNumber );
					if ( 'number' === typeof entryNumber ) {
						document.querySelector( '#the-list tr:nth-child(' + entryNumber + ') .submitdelete' ).dispatchEvent( click );
					}
					break;
			}

			deHighlight();
			lastCommand = '';
		}

		finalCommand = '';
	}

	function startListening() {
		if ( isListening ) {
			voiceRecognition.stop();
			return;
		}
		finalCommand = '';
		voiceRecognition.start();
		results.innerHTML = '';
		micImage.src = WPAVoice.micSlash;
	}

	window.addEventListener( 'load', startListening );

	document.getElementById( 'startListening' ).addEventListener( 'click', startListening );
} else {
	alert( WPAVoice.errors.unsupported );
}