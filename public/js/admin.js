(function () {
    'use strict';

    tinymce.init({
        selector: "textarea.tinymce",
        theme: "silver",
        language: TRANS_LOCALE,
        height: 300,
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern responsivefilemanager imagetools"
        ],
        toolbar1: "undo redo | styleselect fontselect fontsizeselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "addElement | print preview media | forecolor backcolor emoticons | responsivefilemanager",
        image_advtab: true,
        content_css: "/css/content.css",
        extended_valid_elements: "widget,module",
        custom_elements: "widget,module",
        external_filemanager_path: "/editor/filemanager/",
        filemanager_title: "Filemanager",
        filemanager_access_key: "Ond9yK4I27r6Od1FQ77UYlr289MCpvQf",
        external_plugins: {"filemanager": "/editor/filemanager/plugin.min.js"},
        convert_urls: false,
        setup: function (editor) {
            editor.ui.registry.addMenuButton('addElement', {
                text: TMCE_TRANS.addElement,
                fetch: function (callback) {
                    var items = [
                        {
                            type: 'menuitem',
                            text: TMCE_TRANS.posts,
                            onAction: function () {
                                let elem = $('#insertPostsModal');
                                if (elem) {
                                    (new bootstrap.Modal(elem)).show();
                                }
                            }
                        },
                        {
                            type: 'menuitem',
                            text: TMCE_TRANS.module,
                            onAction: function () {
                                let elem = $('#insertModuleModal');
                                if (elem) {
                                    (new bootstrap.Modal(elem)).show();
                                }
                            }
                        },
                        {
                            type: 'menuitem',
                            text: TMCE_TRANS.widget,
                            onAction: function () {
                                let elem = $('#insertWidgetModal');
                                if (elem) {
                                    (new bootstrap.Modal(elem)).show();
                                }
                            }
                        },
                    ];
                    callback(items);
                }
            });
        }
    });

    $('body').on('click', '#insertModuleModal .modal-footer .btn-primary', function () {
        tinymce.activeEditor.execCommand('mceInsertContent', false, '<pre class="module">' + $('#input-module-name').val() + '</pre>');
        $('#insertModuleModal').modal('hide');
    });
    $('body').on('click', '#insertWidgetModal .modal-footer .btn-primary', function () {
        tinymce.activeEditor.execCommand('mceInsertContent', false, '<pre class="widget">' + $('#input-widget-name').val() + '</pre>');
        $('#insertWidgetModal').modal('hide');
    });
    $('body').on('click', '#insertPostsModal .modal-footer .btn-primary', function () {
        tinymce.activeEditor.execCommand('mceInsertContent', false, '<pre class="posts">' + $('#input-category').val() + '</pre>');
        $('#insertPostsModal').modal('hide');
    });
    $('#toggle-side-navigation').click(function () {
        var $nav = $('#side-navigation');
        var $main = $('main');
        if ($nav.hasClass('d-none')) {
            $nav.removeClass('d-none');
            $main.addClass('d-none');
            $(this).addClass('active');
        } else {
            $nav.addClass('d-none');
            $main.removeClass('d-none');
            $(this).removeClass('active');
        }
    });

    $('#filemanagerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var field = button.data('field');
        var modal = $(this);
        modal.find('iframe').attr('src', modal.find('iframe').attr('src').replace(/field_id=.*/, 'field_id=' + field));
    });

    $('.datatable').DataTable({
        language: {
            url: BASE_URL + '/js/dataTables/' + TRANS_LOCALE + '.json'
        }
    });

    $('.custom-file-input').change(function() {
        let input = $(this)[0];
        let $label = $(this).closest('.custom-file').find('.custom-file-label');
        if (input.files.length > 0) {
            $label.text(input.files[0].name);
        } else {
            $label.text($label.data('placeholder'));
        }
    });
}());

function autocompleteWidgets(widgets) {
    $('#autocomplete-widget-list').empty();
    var input = $('#input-widget-name').val();
    for (var i = 0; i < widgets.length; i++) {
        if (widgets[i].indexOf(input) !== -1) {
            $('#autocomplete-widget-list').append('<a href="#" class="list-group-item list-group-item-action" onclick="return insertWidgetName(\'' + widgets[i] + '\');">' + widgets[i] + '</a>');
        }
    }
}

function insertWidgetName(name) {
    $('#input-widget-name').val(name);
    return false;
}

function autocompleteWidgetsShow() {
    $('#autocomplete-widget-list').removeClass('d-none');
}

function autocompleteWidgetsHide() {
    setTimeout(function () {
        $('#autocomplete-widget-list').addClass('d-none');
    }, 300);
}

function autocompleteModules(modules) {
    $('#autocomplete-module-list').empty();
    var input = $('#input-module-name').val();
    for (var i = 0; i < modules.length; i++) {
        if (modules[i].indexOf(input) !== -1) {
            $('#autocomplete-module-list').append('<a href="#" class="list-group-item list-group-item-action" onclick="return insertModuleName(\'' + modules[i] + '\');">' + modules[i] + '</a>');
        }
    }
}

function insertModuleName(name) {
    $('#input-module-name').val(name);
    return false;
}

function autocompleteModulesShow() {
    $('#autocomplete-module-list').removeClass('d-none');
}

function autocompleteModulesHide() {
    setTimeout(function () {
        $('#autocomplete-module-list').addClass('d-none');
    }, 300);
}
