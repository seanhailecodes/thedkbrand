(function ($) {
    'use strict';

    // ELEMENTS
    var $colorPicker = $('#wpSirColorPicker'),
        $sizesSelect = $('#wpSirResizeSizes'),
        $compressImageSlider = $('.wpSirSlider'),
        toggleButtons = document.getElementsByClassName('wp-sir-as-toggle');


    // ------------------------------------------------------------------------------------------
    // INITILIAZE COLOR PICKER
    // ------------------------------------------------------------------------------------------

    $colorPicker.wpColorPicker();

    // ------------------------------------------------------------------------------------------
    // INITILIAZE MULTI SELECT
    // ------------------------------------------------------------------------------------------

    $sizesSelect.multipleSelect();

    // ------------------------------------------------------------------------------------------
    // INITILIAZE COMPRESSION SLIDER.
    // ------------------------------------------------------------------------------------------

    $compressImageSlider.each(function () {
        var handle = $(this).find('.wpSirSliderHandler');
        var inputElement = $('.' + $(this).data('input'));
        $(this).slider({
            create: function () {
                $(this).slider('value', inputElement.val());
                handle.text($(this).slider('value') + '%');
            },
            slide: function (event, ui) {
                handle.text(ui.value + '%');
                inputElement.val(ui.value);
            },
            change: function (event, ui) {
                handle.text(ui.value + '%');
            }
        });
    });

    // ------------------------------------------------------------------------------------------
    // INITILIAZE TOGGLE BUTTON.
    // ------------------------------------------------------------------------------------------

    for (let i = 0; i < toggleButtons.length; i++) {
        const span = document.createElement('span');

        if (toggleButtons[i].disabled) {
            span.classList.add('wp-sir-is-disabled');
        }
        span.classList.add('wp-sir-toggle-button');

        if (toggleButtons[i].checked) {
            span.classList.add('wp-sir-is-checked');
        }
        if (!toggleButtons[i].disabled) {
            span.addEventListener('click', (e) => {
                toggleButtons[i].checked = !toggleButtons[i].checked;
                toggleButtons[i].click();
                span.classList.toggle('wp-sir-is-checked');
            });
        }
        toggleButtons[i].parentNode.insertBefore(span, toggleButtons[i]);
    }

})(jQuery);
