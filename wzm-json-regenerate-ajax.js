/**
*   1. 点击按钮“立即生成”发出AJAX请求
*/
jQuery(document).ready( function($){
    $('#wzm-button-regenerate').click( function () {
        $.ajax({
            method: 'POST',
            url: ajaxurl,
            data: {
                action: 'wzm_button_regenerate',
            },
            success: function() {
                $("#wzm-ajax-regenerate-status").css('visibility','visible');
            }
        });
    });
});