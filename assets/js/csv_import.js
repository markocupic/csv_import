document.addEvent('domready', function () {
    // scroll to top
    location.hash = "#main";

    if (document.id('saveNclose')) {
        document.id('saveNclose').setStyle('visibility', 'hidden');
    }
    if (document.id('saveNcreate')) {
        document.id('saveNcreate').setStyle('visibility', 'hidden');
    }
    if (document.id('save')) {
        document.id('save').setProperty('value', 'Daten in Tabelle importieren');
    }

    if ($$('.header_new')) {
        $$('.header_new').setProperty('title', 'start a new csv-import');
        $$('.header_new').set('text', 'Einen neuen CSV-Datenimport starten.');
    }

    if (document.id('ctrl_response_box')) {
        // show response box
        document.id('ctrl_response_box').style.display = "block";

        // hide submit buttons
        $$('.tl_submit_container input').each(function (el) {
            el.style.visibility = "hidden";
        });
    }
});




