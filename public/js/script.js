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
    //toolbar2: "addWidget addModule | print preview media | forecolor backcolor emoticons | responsivefilemanager",
    toolbar2: "addWidget addModule | print preview media | forecolor backcolor emoticons | responsivefilemanager",
    image_advtab: true,
    content_css: "/css/content.css",
    extended_valid_elements : "widget,module",
    custom_elements: "widget,module",
    external_filemanager_path:"/editor/filemanager/",
    filemanager_title:"Filemanager",
    filemanager_access_key: "Ond9yK4I27r6Od1FQ77UYlr289MCpvQf",
    external_plugins: { "filemanager" : "/editor/filemanager/plugin.min.js"},
    convert_urls: false
  });
}());
