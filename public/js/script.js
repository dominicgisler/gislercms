(function () {
  'use strict';

  tinymce.init({
    selector: "textarea.tinymce",
    theme: "silver",
    language: "de",
    height: 300,
    plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table contextmenu directionality",
      "emoticons template paste textcolor colorpicker textpattern responsivefilemanager imagetools"
    ],
    toolbar1: "undo redo | styleselect fontselect fontsizeselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    toolbar2: "insertWidget insertModule | print preview media | forecolor backcolor emoticons | responsivefilemanager",
    image_advtab: true,
    content_css: "/css/content.css",
    extended_valid_elements : "widget,module",
    custom_elements: "widget,module",
    external_filemanager_path:"/editor/filemanager/",
    filemanager_title:"Filemanager",
    filemanager_access_key: "Ond9yK4I27r6Od1FQ77UYlr289MCpvQf",
    external_plugins: { "filemanager" : "/editor/filemanager/plugin.min.js"},
    convert_urls: false,
    setup: function(editor) {
      // additional buttons
      editor.ui.registry.addButton('insertWidget', {
        text: 'Widget+',
        tooltip: 'Widget hinzufügen',
        onAction: function() {
          var elem = $('#insertWidgetModal');
          if (elem) {
            elem.modal();
          }
        }
      });
      editor.ui.registry.addButton('insertModule', {
        text: 'Modul+',
        tooltip: 'Modul hinzufügen',
        onAction: function() {
          var elem = $('#insertModuleModal');
          if (elem) {
            elem.modal();
          }
        }
      });
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
  setTimeout(function() {
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
  setTimeout(function() {
    $('#autocomplete-module-list').addClass('d-none');
  }, 300);
}
