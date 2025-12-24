

    // function[s].
    function getCardRowCount(card) {
        var width       = $('.card').first().width();
        var parentWidth = $('.card').first().offsetParent().width();
        var percent     = Math.round(100 * width / parentWidth);
        if( 0 < percent && percent < 40 ){
            return 3;
        } else if( 40 < percent && percent < 60 ) {
            return 2;
        } else {
            return 1;
        }
    }
    function updatePage() {
        var cardRowCount = getCardRowCount();
        if( cardRowCount == 1 ){
            $("body").removeClass("desktop");
            $("body").removeClass("tablet");
            $("body").addClass("mobile");
            $(".card-desc").show();
            updateCardHoverEvents()
        } 
        if( cardRowCount == 2  ){
            $("body").removeClass("mobile");
            $("body").removeClass("desktop");
            $("body").addClass("tablet");
            $(".card-desc").show();
            updateCardHoverEvents()
        }
        if( cardRowCount == 3 ){
            $("body").removeClass("mobile");
            $("body").removeClass("tablet");
            $("body").addClass("desktop");
            $(".card-desc").show();
            updateCardHoverEvents()
        }
    }
    function updateCardHoverEvents() {
        if( $('body').hasClass('desktop') ){
            $(".card").unbind('mouseenter mouseleave');
            $(".card").hover(
                function(){
                    $(this).find(".card-content").removeClass('darken')
                },
                function(){
                    $(this).find(".card-content").addClass('darken');
                }
            );
        }
        if( $('body').hasClass('tablet') ){
            $(".card").unbind('mouseenter mouseleave');
            $(".card").hover(
                function(){
                    $(this).find(".card-content").removeClass('darken')
                },
                function(){
                    $(this).find(".card-content").addClass('darken');
                }
            );
        }
        if( $('body').hasClass('mobile') ){
            $(".card").hover(
                function(){
                    $(this).find(".card-content").removeClass('darken')
                },
                function(){
                    $(this).find(".card-content").addClass('darken');
                }
            );
        }
    }
    function updateCardSpacerHeights(height = undefined) {
        if( height != undefined ){
            $('.card-spacer').css('height', height + 'px');
        } else {
            var heightMax = -1;
            $('.card').each(function() {
                heightMax = heightMax > $(this).height() ? heightMax : $(this).height();
            });
            $('.card').each(function() {
                heightCurrent    = $(this).css('height').replace(/px/g,'');
                heightDifference = heightMax - heightCurrent;
                $(this).find('.card-spacer').css('height', heightDifference + 'px');
            });
        }
    }


    // card effect[s]
    $(document).ready(function() {
        updatePage();
    });
    $(window).resize(function() {
        setTimeout(function() {
            updatePage();
        }, 1000);
    });


    // card select[s]
    $(document).ready(function() {

        // card select[ed]
        cardSelectedIndex = 0;
        cardSelectedMax   = $('.card').length - 1;

        // card select[ed] select first
        $('.card').eq(cardSelectedIndex).addClass("selected")

        // key[s]
        keyBack  =  8;
        keyEnter = 13;
        keyAlt   = 18;
        keyLeft  = 37;
        keyUp    = 38;
        keyRight = 39;
        keyDown  = 40;

        // loaded
        $('.container').removeClass('wide');
        setTimeout(function() {
            $('body').addClass('loaded');
        }, 0);

        // mouse event[s]
        $('.card').mouseover(function(event) {
            $(this).addClass("selected");
            $('.card').not(this).removeClass("selected");
        });
        $('.card').mouseleave(function(event) {
            $('.card').removeClass("selected");
        });        

        // key[s] press.
        $(document).keydown(function(event) {

            // row count.
            row_count = getCardRowCount();

            // navigate enter.
            if( event.which == keyEnter ){
                event.preventDefault();
                $('.card').eq(cardSelectedIndex).click();
            }

            // navigate back
            if( event.which == keyBack ){
                event.preventDefault();
                window.history.back();
            }

            // navigate back alt
            if( event.altKey && event.which === keyLeft ){
                event.preventDefault();
                window.history.back();
            }

            // navigate forward alt
            if( event.altKey && event.which === keyRight ){
                event.preventDefault();
                window.history.forward();
            }            

            // navigate key up/left
            if ( event.which === keyUp || event.which === keyLeft ){
                event.preventDefault();
                if( cardSelectedIndex == 0 ){
                    cardSelectedIndex = cardSelectedMax
                } else {
                    cardSelectedIndex --;
                }
            }

            // navigate key down/right
            if ( event.which === keyDown || event.which === keyRight ){
                event.preventDefault();
                if( cardSelectedIndex == cardSelectedMax ){
                    cardSelectedIndex = 0
                } else {
                    offset = -100
                    cardSelectedIndex ++;
                }
            }

            // navigate card selected.
            $('.card').not($('.card').eq(cardSelectedIndex)).removeClass("selected");
            $('.card').eq(cardSelectedIndex).addClass("selected")
            $('html, body').animate({
                scrollTop:  $('.card').eq(cardSelectedIndex).offset().top
            }, 200);

        });

    });
