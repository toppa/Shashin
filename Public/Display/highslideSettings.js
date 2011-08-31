// The "-0" and "!!" are for type casting, as all vars brought over
// from wp_localize_script come in as strings
hs.graphicsDir = highslideSettings.graphicsDir;
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = ((highslideSettings.outlineType == "none") ? null : highslideSettings.outlineType);
hs.fadeInOut = true;
hs.dimmingOpacity = highslideSettings.dimmingOpacity-0;

// Add the controlbar for slideshows
function addHSSlideshow(groupID) {
    hs.addSlideshow({
        slideshowGroup: groupID,
        interval: highslideSettings.interval-0,
        repeat: !!(highslideSettings.repeat-0),
        useControls: true,
        fixedControls: true,
        overlayOptions: {
            opacity: .75,
            position: highslideSettings.position,
            hideOnMouseOut: !!(highslideSettings.hideController-0)
        }
    });
}

// for Flash
hs.outlineWhileAnimating = true;
hs.allowSizeReduction = false;
// always use this with flash, else the movie will not stop on close:
hs.preserveContent = false;
