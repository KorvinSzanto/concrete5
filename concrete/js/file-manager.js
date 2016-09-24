!function(e,t){"use strict";function n(e,n){var r=this;n=t.extend({breadcrumbElement:"div.ccm-search-results-breadcrumb",bulkParameterName:"fID",searchMethod:"get",appendToOuterDialog:!0,selectMode:"multiple"},n),r.currentFolder=0,r.interactionIsDragging=!1,r.$breadcrumb=t(n.breadcrumbElement),r._templateFileProgress=_.template('<div id="ccm-file-upload-progress" class="ccm-ui"><div id="ccm-file-upload-progress-bar"><div class="progress progress-striped active"><div class="progress-bar" style="width: <%=progress%>%;"></div></div></div></div>'),ConcreteAjaxSearch.call(r,e,n),ConcreteTree.setupTreeEvents(),r.setupEvents(),r.setupAddFolder(),r.setupFileUploads(),r.setupFileDownloads()}n.prototype=Object.create(ConcreteAjaxSearch.prototype),n.prototype.setupRowDragging=function(){var e=this,n=e.$element.find("tr[data-file-manager-tree-node-type!=file_folder]"),r=navigator.appVersion,a=/android/gi.test(r),o=/iphone|ipad|ipod/gi.test(r),i=a||o||/(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent);i||(e.$element.find("tr[data-file-manager-tree-node-type]").each(function(){var r,a=t(this);switch(t(this).attr("data-file-manager-tree-node-type")){case"file_folder":r="ccm-search-results-folder";break;case"file":r="ccm-search-results-file"}r&&a.draggable({delay:300,start:function(r){e.interactionIsDragging=!0,t("html").addClass("ccm-search-results-dragging"),n.css("opacity","0.4"),r.altKey&&e.$element.addClass("ccm-search-results-copy"),e.$element.find(".ccm-search-select-hover").removeClass("ccm-search-select-hover"),t(window).on("keydown.concreteSearchResultsCopy",function(t){18==t.keyCode?e.$element.addClass("ccm-search-results-copy"):e.$element.removeClass("ccm-search-results-copy")}),t(window).on("keyup.concreteSearchResultsCopy",function(t){18==t.keyCode&&e.$element.removeClass("ccm-search-results-copy")})},stop:function(){t("html").removeClass("ccm-search-results-dragging"),t(window).unbind(".concreteSearchResultsCopy"),n.css("opacity",""),e.$element.removeClass("ccm-search-results-copy"),e.interactionIsDragging=!1},revert:"invalid",helper:function(){var n=e.$element.find(".ccm-search-select-selected");return t('<div class="'+r+' ccm-draggable-search-item"><span>'+n.length+"</span></div>").data("$selected",n)},cursorAt:{left:-20,top:5}})}),e.$element.find("tr[data-file-manager-tree-node-type=file_folder], ol[data-search-navigation=breadcrumb] a[data-file-manager-tree-node]").droppable({hoverClass:"ccm-search-select-active-droppable",drop:function(n,r){var a=r.helper.data("$selected"),o=[],i=t(this).data("file-manager-tree-node"),l=n.altKey;a.each(function(){var e=t(this),n=e.data("file-manager-tree-node");n==i?a=a.not(this):o.push(t(this).data("file-manager-tree-node"))}),0!==o.length&&(l||a.hide(),new ConcreteAjaxRequest({url:CCM_DISPATCHER_FILENAME+"/ccm/system/tree/node/drag_request",data:{ccm_token:e.options.upload_token,copyNodes:l?"1":0,sourceTreeNodeIDs:o,treeNodeParentID:i},success:function(e){l||a.remove(),ConcreteAlert.notify({message:e.message,title:e.title})},error:function(e){a.show();var t=e.responseText;e.responseJSON&&e.responseJSON.errors&&(t=e.responseJSON.errors.join("<br/>")),ConcreteAlert.dialog(ccmi18n.error,t)}}))}}))},n.prototype.setupBreadcrumb=function(e){var n=this;if(e.breadcrumb&&(n.$breadcrumb.html(""),e.breadcrumb.length)){var r=t('<ol data-search-navigation="breadcrumb" class="breadcrumb" />');t.each(e.breadcrumb,function(e,a){var o="";a.active&&(o=' class="active"'),r.append("<li"+o+'><a data-file-manager-tree-node="'+a.folder+'" href="'+a.url+'">'+a.name+"</a></li>"),r.find("li.active a").on("click",function(e){if(e.stopPropagation(),e.preventDefault(),a.menu){var o=t(a.menu);n.showMenu(r,o,e)}})}),r.appendTo(n.$breadcrumb),r.on("click.concreteSearchBreadcrumb","a",function(){return n.loadFolder(t(this).attr("data-file-manager-tree-node"),t(this).attr("href")),!1})}},n.prototype.setupFileDownloads=function(){var e=this;t("#ccm-file-manager-download-target").length?e.$downloadTarget=t("#ccm-file-manager-download-target"):e.$downloadTarget=t("<iframe />",{name:"ccm-file-manager-download-target",id:"ccm-file-manager-download-target"}).appendTo(document.body)},n.prototype.setupFileUploads=function(){var e=this,n=t("#ccm-file-manager-upload"),r=n.data("image-max-width"),a=n.data("image-max-height"),o=r>0&&a>0,i=n.data("image-quality"),l=[],c=[],s=_.template("<ul><% _(errors).each(function(error) { %><li><strong><%- error.name %></strong><p><%- error.error %></p></li><% }) %></ul>"),d={url:CCM_DISPATCHER_FILENAME+"/ccm/system/file/upload",dataType:"json",disableImageResize:!o,imageQuality:i>0?i:85,imageMaxWidth:r>0?r:1920,imageMaxHeight:a>0?a:1080,error:function(e){var t=e.responseText;try{t=jQuery.parseJSON(t).errors;var n=this.files[0].name;_(t).each(function(e){l.push({name:n,error:e})})}catch(r){}},progressall:function(n,r){var a=parseInt(r.loaded/r.total*100,10);t("#ccm-file-upload-progress-wrapper").html(e._templateFileProgress({progress:a}))},start:function(){l=[],t("<div />",{id:"ccm-file-upload-progress-wrapper"}).html(e._templateFileProgress({progress:100})).appendTo(document.body),t.fn.dialog.open({title:ccmi18n_filemanager.uploadProgress,width:400,height:50,onClose:function(e){e.jqdialog("destroy").remove()},element:t("#ccm-file-upload-progress-wrapper"),modal:!0})},done:function(e,t){c.push(t.result[0])},stop:function(){if(jQuery.fn.dialog.closeTop(),l.length)ConcreteAlert.dialog(ccmi18n_filemanager.uploadFailed,s({errors:l}));else{var t=!1;_.each(c,function(e){e.canEditFileProperties&&(t=!0)}),t?e._launchUploadCompleteDialog(c):e.reloadFolder(),c=[]}}};n.fileupload(d),n.bind("fileuploadsubmit",function(t,n){n.formData={currentFolder:e.currentFolder,ccm_token:e.options.upload_token}}),t("a[data-dialog=add-files]").on("click",function(){t.fn.dialog.open({width:620,height:500,modal:!0,title:ccmi18n_filemanager.addFiles,href:CCM_DISPATCHER_FILENAME+"/tools/required/files/import?currentFolder="+e.currentFolder})})},n.prototype.refreshResults=function(e){var t=this;t.loadFolder(this.currentFolder,!1,!0)},n.prototype._launchUploadCompleteDialog=function(e){var t=this;n.launchUploadCompleteDialog(e,t)},n.prototype.setupFolders=function(e){var n=this,r=n.$element.find("tbody tr");e.folder&&(n.currentFolder=e.folder.treeNodeID),n.$element.find("tbody tr").on("dblclick",function(){var e=r.index(t(this));if(e>-1){var a=n.getResult().items[e];a&&a.isFolder&&n.loadFolder(a.treeNodeID)}})},n.prototype.setupEvents=function(){var e=this;ConcreteEvent.subscribe("AjaxFormSubmitSuccess",function(t,n){"add-folder"==n.form&&e.reloadFolder()}),ConcreteEvent.unsubscribe("FileManagerAddFilesComplete"),ConcreteEvent.subscribe("FileManagerAddFilesComplete",function(t,n){e._launchUploadCompleteDialog(n.files)}),ConcreteEvent.unsubscribe("FileManagerDeleteFilesComplete"),ConcreteEvent.subscribe("FileManagerDeleteFilesComplete",function(t,n){e.reloadFolder()}),ConcreteEvent.unsubscribe("ConcreteTreeUpdateTreeNode.concreteTree"),ConcreteEvent.subscribe("ConcreteTreeUpdateTreeNode.concreteTree",function(t,n){e.reloadFolder()}),ConcreteEvent.unsubscribe("ConcreteTreeDeleteTreeNode.concreteTree"),ConcreteEvent.subscribe("ConcreteTreeDeleteTreeNode.concreteTree",function(t,n){e.reloadFolder()}),ConcreteEvent.unsubscribe("SavedSearchCreated"),ConcreteEvent.subscribe("SavedSearchCreated",function(t,n){e.ajaxUpdate(n.search.baseUrl,{})})},n.prototype.showMenu=function(e,t,n){var r=this,a=new ConcreteFileMenu(e,{menu:t,handle:"none",container:r});a.show(n)},n.prototype.activateMenu=function(e){var n=this;if(n.getSelectedResults().length>1&&e.find("a").on("click.concreteFileManagerBulkAction",function(e){var r=t(this).attr("data-bulk-action"),a=t(this).attr("data-bulk-action-type"),o=[];t.each(n.getSelectedResults(),function(e,t){o.push(t.fID)}),n.handleSelectedBulkAction(r,a,t(this),o)}),"choose"!=n.options.selectMode){var r=e.find("a[data-file-manager-action=clear]").parent();r.next("li.divider").remove(),r.remove()}},n.prototype.setupBulkActions=function(){var e=this;e.$element.on("click","button.btn-menu-launcher",function(n){var r=e.getResultMenu(e.getSelectedResults());if(r){r.find(".dialog-launch").dialog();var a=r.find("ul");a.attr("data-search-file-menu",r.attr("data-search-file-menu")),t(this).parent().find("ul").remove(),t(this).parent().append(a);var o=new ConcreteFileMenu;o.setupMenuOptions(t(this).parent()),ConcreteEvent.publish("ConcreteMenuShow",{menu:e,menuElement:t(this).parent()})}})},n.prototype.handleSelectedBulkAction=function(e,n,r,a){var o=this,i=[];"choose"==e?(ConcreteEvent.publish("FileManagerBeforeSelectFile",{fID:a}),ConcreteEvent.publish("FileManagerSelectFile",{fID:a})):"download"==e?(t.each(a,function(e,t){i.push({name:"item[]",value:t})}),o.$downloadTarget.get(0).src=CCM_TOOLS_PATH+"/files/download?"+jQuery.param(i)):ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this,e,n,r,a)},n.prototype.reloadFolder=function(){this.loadFolder(this.currentFolder)},n.prototype.setupAddFolder=function(){var e=this;e.$element.find("a[data-launch-dialog=add-file-manager-folder]").on("click",function(){t("div[data-dialog=add-file-manager-folder] input[name=currentFolder]").val(e.currentFolder),jQuery.fn.dialog.open({element:"div[data-dialog=add-file-manager-folder]",modal:!0,width:320,title:"Add Folder",height:"auto"})})},n.prototype.hoverIsEnabled=function(e){var t=this;return!t.interactionIsDragging},n.prototype.updateResults=function(e){var n=this;ConcreteAjaxSearch.prototype.updateResults.call(n,e),n.setupFolders(e),n.setupBreadcrumb(e),n.setupRowDragging(),"choose"==n.options.selectMode&&(n.$element.unbind(".concreteFileManagerHoverFile"),n.$element.on("mouseover.concreteFileManagerHoverFile","tr[data-file-manager-tree-node-type]",function(){t(this).addClass("ccm-search-select-hover")}),n.$element.on("mouseout.concreteFileManagerHoverFile","tr[data-file-manager-tree-node-type]",function(){t(this).removeClass("ccm-search-select-hover")}),n.$element.unbind(".concreteFileManagerChooseFile").on("click.concreteFileManagerChooseFile","tr[data-file-manager-tree-node-type=file]",function(e){return ConcreteEvent.publish("FileManagerBeforeSelectFile",{fID:t(this).attr("data-file-manager-file")}),ConcreteEvent.publish("FileManagerSelectFile",{fID:t(this).attr("data-file-manager-file")}),n.$downloadTarget.remove(),!1}),n.$element.unbind(".concreteFileManagerOpenFolder").on("click.concreteFileManagerOpenFolder","tr[data-file-manager-tree-node-type=search_preset],tr[data-file-manager-tree-node-type=file_folder]",function(e){e.preventDefault(),n.loadFolder(t(this).attr("data-file-manager-tree-node"))}))},n.prototype.loadFolder=function(e,n,r){var a=this,o=a.getSearchData();if(n)a.options.result.baseUrl=n;else var n=a.options.result.baseUrl;o.push({name:"folder",value:e}),a.options.result.filters&&t.each(a.options.result.filters,function(e,t){var n=t.data;o.push({name:"field[]",value:t.key});for(var r in n)o.push({name:r,value:n[r]})}),r&&(o.push({name:"ccm_order_by",value:"folderItemModified"}),o.push({name:"ccm_order_by_direction",value:"desc"})),a.currentFolder=e,a.ajaxUpdate(n,o),a.$element.find("#ccm-file-manager-upload input[name=currentFolder]").val(a.currentFolder)},n.prototype.getResultMenu=function(e){var t=this,n=ConcreteAjaxSearch.prototype.getResultMenu.call(this,e);return n&&t.activateMenu(n),n},n.launchDialog=function(e,n){var r,a=t(window).width()-100,o={},i={filters:[],multipleSelection:!1};if(t.extend(i,n),i.filters.length>0)for(o["field[]"]=[],r=0;r<i.filters.length;r++){var l=t.extend(!0,{},i.filters[r]);o["field[]"].push(l.field),delete l.field,t.extend(o,l)}t.fn.dialog.open({width:a,height:"90%",href:CCM_DISPATCHER_FILENAME+"/ccm/system/dialogs/file/search",modal:!0,data:o,title:ccmi18n_filemanager.title,onOpen:function(n){ConcreteEvent.unsubscribe("FileManagerSelectFile"),ConcreteEvent.subscribe("FileManagerSelectFile",function(n,r){var a="[object Array]"===Object.prototype.toString.call(r.fID);if(i.multipleSelection&&!a)r.fID=[r.fID];else if(!i.multipleSelection&&a){if(r.fID.length>1)return t(".ccm-search-bulk-action option:first-child").prop("selected","selected"),void alert(ccmi18n_filemanager.chosenTooMany);r.fID=r.fID[0]}jQuery.fn.dialog.closeTop(),e(r)})}})},n.getFileDetails=function(e,n){t.ajax({type:"post",dataType:"json",url:CCM_DISPATCHER_FILENAME+"/ccm/system/file/get_json",data:{fID:e},error:function(e){ConcreteAlert.dialog("Error",e.responseText)},success:function(e){n(e)}})},n.launchUploadCompleteDialog=function(e,n){if(e&&e.length&&e.length>0){var r="";_.each(e,function(e){r+="fID[]="+e.fID+"&"}),r=r.substring(0,r.length-1),t.fn.dialog.open({width:"660",height:"500",href:CCM_DISPATCHER_FILENAME+"/ccm/system/dialogs/file/upload_complete",modal:!0,data:r,onClose:function(){var e={filemanager:n};ConcreteEvent.publish("FileManagerUploadCompleteDialogClose",e)},onOpen:function(){var e={filemanager:n};ConcreteEvent.publish("FileManagerUploadCompleteDialogOpen",e)},title:ccmi18n_filemanager.uploadComplete})}},t.fn.concreteFileManager=function(e){return t.each(t(this),function(r,a){new n(t(this),e)})},e.ConcreteFileManager=n}(window,$),!function(e,t){"use strict";function n(e,n){var r=this,n=t.extend({chooseText:ccmi18n_filemanager.chooseNew,inputName:"concreteFile",fID:!1,filters:[]},n),a={};a.filters=n.filters,r.$element=e,r.options=n,r._chooseTemplate=_.template(r.chooseTemplate,{options:r.options}),r._loadingTemplate=_.template(r.loadingTemplate),r._fileLoadedTemplate=_.template(r.fileLoadedTemplate),r.$element.append(r._chooseTemplate),r.$element.on("click","div.ccm-file-selector-choose-new",function(){return ConcreteFileManager.launchDialog(function(e){r.loadFile(e.fID,function(){r.$element.closest("form").trigger("change")})},a),!1}),r.options.fID&&r.loadFile(r.options.fID)}n.prototype={chooseTemplate:'<div class="ccm-file-selector-choose-new"><input type="hidden" name="<%=options.inputName%>" value="0" /><%=options.chooseText%></div>',loadingTemplate:'<div class="ccm-file-selector-loading"><input type="hidden" name="<%=inputName%>" value="<%=fID%>"><img src="'+CCM_IMAGE_PATH+'/throbber_white_16.gif" /></div>',fileLoadedTemplate:'<div class="ccm-file-selector-file-selected"><input type="hidden" name="<%=inputName%>" value="<%=file.fID%>" /><div class="ccm-file-selector-file-selected-thumbnail"><%=file.resultsThumbnailImg%></div><div class="ccm-file-selector-file-selected-title"><div><%=file.title%></div></div><div class="clearfix"></div></div>',loadFile:function(e,n){var r=this;r.$element.html(r._loadingTemplate({inputName:r.options.inputName,fID:e})),ConcreteFileManager.getFileDetails(e,function(e){var a=e.files[0];r.$element.html(r._fileLoadedTemplate({inputName:r.options.inputName,file:a})),r.$element.find(".ccm-file-selector-file-selected").on("click",function(e){var n=a.treeNodeMenu;if(n){var o=new ConcreteFileMenu(t(this),{menuLauncherHoverClass:"ccm-file-manager-menu-item-hover",menu:t(n),handle:"none",container:r});o.show(e)}}),n&&n(e)})}},t.fn.concreteFileSelector=function(e){return t.each(t(this),function(r,a){new n(t(this),e)})},e.ConcreteFileSelector=n}(this,$),!function(e,t,n){"use strict";function r(e,n){var r=this,n=n||{};n=t.extend({container:!1},n),r.options=n,e&&ConcreteMenu.call(r,e,n)}r.prototype=Object.create(ConcreteMenu.prototype),r.prototype.setupMenuOptions=function(e){var r=this,a=ConcreteMenu.prototype,o=e.attr("data-search-file-menu"),i=r.options.container;a.setupMenuOptions(e),e.find("a[data-file-manager-action=clear]").on("click",function(){var e=ConcreteMenuManager.getActiveMenu();return e&&e.hide(),n.defer(function(){i.$element.html(i._chooseTemplate)}),!1}),e.find("a[data-file-manager-action=download]").on("click",function(e){e.preventDefault(),window.frames["ccm-file-manager-download-target"].location=CCM_TOOLS_PATH+"/files/download?fID="+o}),e.find("a[data-file-manager-action=duplicate]").on("click",function(){return t.concreteAjax({url:CCM_DISPATCHER_FILENAME+"/ccm/system/file/duplicate",data:{fID:o},success:function(e){"undefined"!=typeof i.refreshResults&&i.refreshResults()}}),!1})},t.fn.concreteFileMenu=function(e){return t.each(t(this),function(n,a){new r(t(this),e)})},e.ConcreteFileMenu=r}(this,$,_);