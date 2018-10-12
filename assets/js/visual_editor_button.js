(function () {
    tinymce.create("tinymce.plugins.select_slider_toolbar_btn", {
        init: function (ed, url) {
            ed.addButton("select_slider_btn", {
                title: "RT Sliders",
                cmd: "select_slider",
                image: "https://image.flaticon.com/icons/svg/1159/1159381.svg",
            });
            ed.addCommand("select_slider", function () {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {action: 'get_all_sliders'},
                    success: function (response) {
                        response = JSON.parse(response);
                        ed.windowManager.open({
                            title: 'RT Sliders',
                            body: [
                                {
                                    type: 'listbox',
                                    name: 'sliderid',
                                    label: 'Choose slider to display',
                                    values: response
                                }],
                            onsubmit: function (e) {
                                ed.insertContent(e.data.sliderid);
                            }
                        });
                    },
                });
            });
        }
    });
    tinymce.PluginManager.add("select_slider_toolbar_btn", tinymce.plugins.select_slider_toolbar_btn);
})();