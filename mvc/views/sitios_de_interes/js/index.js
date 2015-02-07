$(document).ready(function() {

    // Start Main Content Slider
    $('#mainslider').nivoSlider();
    $('#logo').flexslider({
        animation: "slide",
        touch: true,
        initDelay: 0, //{NEW} Integer: Set an initialization delay, in milliseconds
        controlNav: false, //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        prevText: "<span class='fa fa-angle-left'></span>",
        nextText: "<span class='fa fa-angle-right'></span>",
        itemWidth: 190,
        itemMargin: 45,
        minItems: 1,
        maxItems: 5,
        move: 1,
        slideshowSpeed: 2300    // Time delay between animatios
    });
});