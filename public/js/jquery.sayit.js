//Based on https://github.com/jquery-boilerplate/jquery-boilerplate
;( function( $, window, document, undefined ) {
    "use strict";
    
    // Global object for the Google Audio player 
    window.gAudio = null;

    // The actual plugin constructor
    function Plugin ( element, options, gAudio ) {
        this.el = $(element);
        this.msg = null;
        this.loop = 0;
        this.alt_mp3_src = this.el.attr('alt-file');
        this.has_alt_mp3 = (this.alt_mp3_src)?true:false;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend( Plugin.prototype, {
        init: function() {
            if(this.has_alt_mp3){
                this.initMp3file();
            }else{
                this.initHTML5Speak();
            }
        },
        initHTML5Speak: function() {
            this.msg = new SpeechSynthesisUtterance();

            // Set the Speech
            this.msg.text = this.el.attr('data-say-content') || this.el.text();
            this.msg.rate = this.el.attr('data-speed') || 0.8;
            this.msg.lang = this.el.attr('data-lang') || 'en-US';

            // Bind function
            this.msg.onstart = (e) => this.el.addClass('active');
            this.msg.onend = (e) => this.el.removeClass('active');
            
            this.el.on('click', this.HTML5Speak.bind(this));
        },
        initMp3file: function() {
            this.el.on('click', this.AltMp3Speak.bind(this));
        },
        AltMp3Speak: function(e) {
            e.stopPropagation();
            if(window.gAudio){
                window.gAudio.pause();
            }
            console.log('mp3 say...');
            this.loop++;
            window.gAudio = new Audio(this.alt_mp3_src);
            window.gAudio.playbackRate = (this.loop%2)?1:0.70;
            window.gAudio.onplay = () => this.el.addClass('active');
            window.gAudio.onended = () => this.el.removeClass('active');
            window.gAudio.onpause = () => this.el.removeClass('active');
            window.gAudio.play();
            return;
        },
        HTML5Speak: function(e) {
            e.stopPropagation();
            // Speacking before ? Stop !
            if(speechSynthesis.speaking) speechSynthesis.cancel()        
            // Queue this utterance.
            speechSynthesis.speak(this.msg);
        }
        
    } );

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn.sayIt = function( options ) {
        let gAudio = null;
        return this.each( function() {
            if ( !$.data( this, "plugin_sayIt" ) ) {
                $.data( this, "plugin_sayIt", new Plugin( this, options, gAudio ) );
            }
        } );
    };
    
    $(function(){
        $('.sayit').sayIt();

        $('body').on('click', function(){
            if(speechSynthesis.speaking) speechSynthesis.cancel()
            if(window.gAudio) window.gAudio.pause();
        })
        window.addEventListener('blur', function(){
            if(speechSynthesis.speaking) speechSynthesis.cancel()
            if(window.gAudio) window.gAudio.pause();
        });
    })


} )( jQuery, window, document );