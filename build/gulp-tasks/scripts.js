const elixir = require('laravel-elixir');

// Core
elixir(function (mix) {

    // Image Editor
    mix.scripts([
            'build/core/image-editor/build/kinetic.prototype.js',
            'build/core/image-editor/build/imageeditor.js',
            'build/core/image-editor/build/history.js',
            'build/core/image-editor/build/events.js',
            'build/core/image-editor/build/elements.js',
            'build/core/image-editor/build/controls.js',
            'build/core/image-editor/build/save.js',
            'build/core/image-editor/build/extend.js',
            'build/core/image-editor/build/background.js',
            'build/core/image-editor/build/imagestage.js',
            'build/core/image-editor/build/image.js',
            'build/core/image-editor/build/actions.js',
            'build/core/image-editor/build/slideOut.js',
            'build/core/image-editor/build/jquerybinding.js',
            'build/core/image-editor/build/filters.js'
        ],
        '../concrete/js/image-editor.js');

    // App.js

    mix.scripts([
            'build/vendor/pnotify/pnotify.js',
            'build/core/app/json.js',
            'build/vendor/jquery-form/jquery-form.js',
            'build/vendor/jquery-mousewheel/jquery.mousewheel.js',
            'build/core/app/concrete5.js',
            'build/vendor/jquery-liveupdate/jquery-liveupdate.js',
            'build/vendor/autosize/autosize.js',
            'build/vendor/jquery-pep/jquery-pep.js',
            //needs some handholding
            // 'build/vendor/retinajs/retinajs.js',
            'build/core/app/base.js',
            'build/core/app/ajax-request/base.js',
            'build/core/app/ajax-request/form.js',
            'build/core/app/ajax-request/block.js',
            'build/vendor/jquery-cookie/jquery-cookie.js',
            'build/core/app/panels.js',
            'build/core/app/dialog.js',
            'build/core/app/alert.js',
            'build/core/app/newsflow.js',
            'build/core/editable-field/container.js',
            'build/core/app/page-reindexing.js',
            'build/core/app/in-context-menu.js',
            'build/vendor/jquery-liveupdate/quicksilver.js',
            'build/core/app/remote-marketplace.js',
            'build/core/app/search/table.js',
            'build/core/app/search/base.js',
            'build/core/app/progressive-operations.js',
            'build/core/app/custom-style.js',
            'build/core/app/tabs.js',
            'build/core/app/toolbar.js',
            'build/vendor/jquery-bootstrap-select-to-button/jquery-bootstrap-select-to-button.js',
            'build/vendor/nprogress/nprogress.js',
            'build/vendor/tourist/tourist.js',
            'build/core/app/help/dialog.js',
            'build/core/app/help/launcher.js',
            'build/core/app/help/guide-manager.js',
            'build/core/app/help/guides/toolbar.js',
            'build/core/app/help/guides/change-content.js',
            'build/core/app/help/guides/change-content-edit-mode.js',
            'build/core/app/help/guides/add-content.js',
            'build/core/app/help/guides/add-content-edit-mode.js',
            'build/core/app/help/guides/add-page.js',
            'build/core/app/help/guides/personalize.js',
            'build/core/app/help/guides/dashboard.js',
            'build/core/app/help/guides/location-panel.js',
            // Edit Mode
            'build/core/app/edit-mode/editmode.js',
            'build/core/app/edit-mode/block.js',
            'build/core/app/edit-mode/stackdisplay.js',
            'build/core/app/edit-mode/area.js',
            'build/core/app/edit-mode/layout.js',
            'build/core/app/edit-mode/dragarea.js',
            'build/core/app/edit-mode/blocktype.js',
            'build/core/app/edit-mode/stack.js',
            'build/core/app/edit-mode/duplicateblock.js',
            'build/core/app/edit-mode/stackblock.js',
            'build/core/stacks/menu.js'
        ],
        '../concrete/js/app.js');

    // File manager
    mix.scripts([
            'build/core/file-manager/search.js',
            'build/core/file-manager/selector.js',
            'build/core/file-manager/menu.js',
            //'build/core/file-manager/header.js' This one doesn't exist
        ],
        '../concrete/js/file-manager.js');

    // Express
    mix.scripts([
            'build/core/express/search.js',
            'build/core/express/selector.js'
        ],
        '../concrete/js/express.js');

    // Save coordinator for composer
    mix.scripts(
        "build/core/composer/save-coordinator.js",
        "../concrete/js/composer-save-coordinator.js");

    // Lightbox
    mix.scripts("build/core/lightbox.js", "../concrete/js/lightbox.js");

    // Style customizer
    mix.scripts([
            "build/core/style-customizer/palette.js",
            "build/core/style-customizer/image.js",
            "build/core/style-customizer/size.js",
            "build/core/style-customizer/typography.js",
            "build/core/style-customizer/inline-toolbar.js"
        ],
        "../concrete/js/style-customizer.js");

    // Events
    mix.scripts("build/core/events.js", "../concrete/js/events.js");

    // Sitemap
    mix.scripts([
            "build/core/sitemap/sitemap.js",
            "build/core/sitemap/menu.js",
            "build/core/sitemap/search.js",
            "build/core/sitemap/selector.js",
            "build/core/sitemap/sitemap-selector.js"
        ],
        "../concrete/js/sitemap.js");

    // User Selector
    mix.scripts("build/core/user/selector.js", "../concrete/js/users.js");

    // Notifications
    mix.scripts("build/core/notification/notification.js", "../concrete/js/notification.js");

    // Core Tree
    mix.scripts("build/core/tree.js", "../concrete/js/tree.js");

    // Core Layouts
    mix.scripts("build/core/layouts.js", "../concrete/js/layouts.js");

    // Core Conversations
    mix.scripts([
        "build/core/conversations/conversations.js",
        "build/core/conversations/attachments.js"
    ], "../concrete/js/conversations.js");

    // Parallax Image
    mix.scripts(
        "build/core/frontend/parallax-image.js",
        "../concrete/js/frontend/parallax-image.js");

    // Gathering
    mix.scripts([
        "build/vendor/packery/packery.js",
        "build/core/gathering.js"
    ], "../concrete/js/gathering.js");

    // Dashboard
    mix.scripts("build/core/dashboard.js", "../concrete/js/dashboard.js");

    // Account
    mix.scripts("build/core/account/account.js", "../concrete/js/account.js");

    // Translator
    mix.scripts("build/core/translator.js", "../concrete/js/translator.js");
});

// Vendor
elixir(function (mix) {

    // Redactor
    mix.scripts([
            'build/vendor/redactor/redactor.js',
            'build/vendor/redactor/fontcolor.js',
            'build/vendor/redactor/fontfamily.js',
            'build/vendor/redactor/fontsize.js',
            'build/vendor/redactor/table.js',
            'build/core/redactor/undoredo.js',
            'build/core/redactor/lightbox.js',
            'build/core/redactor/underline.js',
            'build/core/redactor/inline.js',
            'build/core/redactor/magic.js',
            'build/core/redactor/specialcharacters.js'
        ],
        '../concrete/js/redactor.js');

    // Select2
    mix.scripts(
        'build/vendor/select2/select2.js',
        '../concrete/js/select2.js'
    );

    // Bootstrap
    // Alert
    mix.scripts("build/vendor/bootstrap/alert.js", "../concrete/js/bootstrap/alert.js")
    // Button
    mix.scripts("build/vendor/bootstrap/button.js", "../concrete/js/bootstrap/button.js");
    // Dropdown
    mix.scripts("build/vendor/bootstrap/dropdown.js", "../concrete/js/bootstrap/dropdown.js");
    // Popover
    mix.scripts("build/vendor/bootstrap/popover.js", "../concrete/js/bootstrap/popover.js");
    // Tooltip
    mix.scripts("build/vendor/bootstrap/tooltip.js", "../concrete/js/bootstrap/tooltip.js");
    // Transition
    mix.scripts("build/vendor/bootstrap/transition.js", "../concrete/js/bootstrap/transition.js");

    // Underscore
    mix.scripts("build/vendor/underscore/underscore.js", "../concrete/js/underscore.js");

    // Jquery Cookie
    mix.scripts("build/vendor/jquery-cookie/jquery-cookie.js", "../concrete/js/jquery-cookie.js");

    // Jquery Tristate
    mix.scripts("build/vendor/jquery-tristate/jquery-tristate.js", "../concrete/js/jquery-tristate.js");

    // Jquery Fileupload
    mix.scripts([
            "build/vendor/jquery-fileupload/load-image.js",
            "build/vendor/jquery-fileupload/load-image-ios.js",
            "build/vendor/jquery-fileupload/load-image-orientation.js",
            "build/vendor/jquery-fileupload/load-image-meta.js",
            "build/vendor/jquery-fileupload/load-image-exif.js",
            "build/vendor/jquery-fileupload/load-image-exif-map.js",
            "build/vendor/jquery-fileupload/javascript-canvas-to-blob.js",
            "build/vendor/jquery-fileupload/javascript-canvas-to-blob.js",
            "build/vendor/jquery-fileupload/jquery-iframe-transport.js",
            "build/vendor/jquery-fileupload/jquery-fileupload.js",
            "build/vendor/jquery-fileupload/jquery-fileupload-process.js",
            "build/vendor/jquery-fileupload/jquery-fileupload-image.js"
        ],
        "../concrete/js/jquery-fileupload.js");

    // Dropzone
    mix.scripts("build/vendor/dropzone/dropzone.js", "../concrete/js/dropzone.js");

    // Jquery Form
    mix.scripts("build/vendor/jquery-form/jquery-form.js", "../concrete/js/jquery-form.js");

    // Jquery Magnific Popup
    mix.scripts(
        "build/vendor/jquery-magnific-popup/jquery-magnific-popup.js",
        "../concrete/js/jquery-magnific-popup.js");

    // Spectrum
    mix.scripts("build/vendor/spectrum/spectrum.js", "../concrete/js/spectrum.js");

    // Kineticjs
    mix.scripts("build/vendor/kinetic/kinetic.js", "../concrete/js/kinetic.js");

    // jQuery Backstrtch
    mix.scripts("build/vendor/backstretch/backstretch.js", "../concrete/js/backstretch.js");

    // Jquery Awesome Rating
    mix.scripts(
        "build/vendor/jquery-awesome-rating/jquery-awesome-rating.js",
        "../concrete/js/jquery-awesome-rating.js");

    // jQuery FancyTree
    mix.scripts(
        "build/vendor/jquery-fancytree/jquery.fancytree-all.js",
        "../concrete/js/fancytree.js");


    // Bootstrap Editable
    mix.scripts([
        "build/vendor/bootstrap-editable/bootstrap3-editable.js",
        "build/core/editable-field/attribute.js"
    ], "../concrete/js/bootstrap-editable.js");

    // Text counter
    mix.scripts("build/vendor/jquery-text-counter/textcounter.js", "../concrete/js/textcounter.js");
});
