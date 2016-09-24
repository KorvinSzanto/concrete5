!function(e,t){"use strict";function i(e,i){var n=this;i=t.extend({inputName:!1,unit:"px",appendTo:document.body},i),n.options=i,n.opened=!1,n.$element=e,n.$container=t(n.options.appendTo),n._chooseTemplate=_.template(n.chooseTemplate,{options:n.options,i18n:ccmi18n}),n._selectorTemplate=_.template(n.selectorWidgetTemplate,{options:n.options,i18n:ccmi18n}),n.$element.append(n._chooseTemplate),n.$widget=t(n._selectorTemplate),n.$container.append(n.$widget),n.$widget.find(".launch-tooltip").tooltip(),n.$widget.find("div.ccm-style-customizer-palette-actions button").on("click.style-customizer-palette",function(e){return n.save(e),!1}),n.$element.on("click.style-customizer-palette","[data-launch=style-customizer-palette]",function(e){if(n.opened)n.closeSelector(e);else{var i=n.getPosition();n.$widget.css({top:i.top,left:i.left}).show().on("click.style-customizer-palette",function(e){e.stopPropagation()}),t(document).on("click.style-customizer-palette",function(e){n.closeSelector(e)}),n.opened=!0}return!1})}i.prototype={setValue:function(e,t){var i=this;i.$element.find("input[data-style-customizer-input="+e+"]").val(t)},getPosition:function(){var e=this,t=e.getOffset(e.$widget,e.$element);return t},getOffset:function(e,i){var n=-5,s=e.outerWidth(),a=e.outerHeight(),o=i.outerHeight(),l=e[0].ownerDocument,c=l.documentElement,r=c.clientWidth+t(l).scrollLeft(),p=c.clientHeight+t(l).scrollTop(),d=i.offset();return d.top+=o,d.left-=Math.min(d.left,d.left+s>r&&r>s?Math.abs(d.left+s-r):0),d.top-=Math.min(d.top,d.top+a>p&&p>a?Math.abs(a+o-n):n),d},getValue:function(e){var t=this;return t.$element.find("input[data-style-customizer-input="+e+"]").val()},closeSelector:function(e){var i=this;i.$widget.hide(),i.opened=!1,t(document).unbind("click.style-customizer-palette")},updateSwatch:function(){alert("You must implement this method updateSwatch.")},save:function(e){var t=this;t.updateSwatch(),t.closeSelector(e),ConcreteEvent.publish("StyleCustomizerControlUpdate")}},e.ConcreteStyleCustomizerPalette=i}(this,$),function(e,t){"use strict";function i(e,i){var n=this;i=t.extend({inputName:!1,value:!1},i),ConcreteStyleCustomizerPalette.call(n,e,i),n.$widget.find("div[data-style-customizer-field=image]").concreteFileSelector({inputName:n.options.inputName})}i.prototype=Object.create(ConcreteStyleCustomizerPalette.prototype),i.prototype.chooseTemplate='<span data-launch="style-customizer-palette" class="ccm-style-customizer-display-swatch"><input type="hidden" value="<%=options.value%>" name="<%=options.inputName%>[image]" data-style-customizer-input="image" /><span class="ccm-ui"><i class="fa fa-picture-o"></i></span></span>',i.prototype.selectorWidgetTemplate='<div class="ccm-ui ccm-style-customizer-palette"><% if (options.value) { %><div><label><%=i18n.currentImage%></label><div><img style="width: 100%" src="<%=options.value%>" /></div></div><% } %><div><label><%=i18n.image%></label><div data-style-customizer-field="image" class="ccm-file-selector"></div></div><div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary"><%=i18n.save%></button></div></div>',i.prototype.save=function(e){var t,i=this,n=i.$widget.find("div.ccm-file-selector-file-selected");n.length&&(t=n.find("input[type=hidden]").val()),i.setValue("image",t),ConcreteEvent.publish("StyleCustomizerControlUpdate"),i.closeSelector(e)},t.fn.concreteStyleCustomizerImageSelector=function(e){return t.each(t(this),function(n,s){new i(t(this),e)})},e.ConcreteStyleCustomizerImageSelector=i}(this,$),function(e,t){"use strict";function i(e,i){var n=this,s=1;i=t.extend({inputName:!1,unit:"px",value:0,appendTo:document.body},i),ConcreteStyleCustomizerPalette.call(n,e,i),n.$slider=n.$widget.find("div.ccm-style-customizer-slider"),"em"==n.options.unit&&(s=.1),n.$slider.slider({min:0,max:400,step:s,value:n.options.value,create:function(e,i){t(this).parent().find("span").html(n.options.value+n.options.unit)},slide:function(e,i){t(this).parent().find("span").html(i.value+n.options.unit)}})}i.prototype=Object.create(ConcreteStyleCustomizerPalette.prototype),i.prototype.chooseTemplate='<span data-launch="style-customizer-palette"><input type="hidden" name="<%=options.inputName%>[size]" value="<%=options.value%>" data-style-customizer-input="size" /><input type="hidden" name="<%=options.inputName%>[unit]" value="<%=options.unit%>" /><span><%=options.value + options.unit%></span></span>',i.prototype.selectorWidgetTemplate='<div class="ccm-ui ccm-style-customizer-palette ccm-style-customizer-palette-large"><div><label><%=i18n.size%></label><div data-style-customizer-field="size"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><%=options.value%><%=options.unit%></span></div></div><div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary"><%=i18n.save%></button></div></div>',i.prototype.updateSwatch=function(){var e=this,t=e.$element.find("span[data-launch=style-customizer-palette] span");t.html(e.getValue("size")+e.options.unit)},i.prototype.save=function(e){var t=this;t.setValue("size",t.$widget.find("div[data-style-customizer-field=size] div.ccm-style-customizer-slider").slider("value")),t.updateSwatch(),ConcreteEvent.publish("StyleCustomizerControlUpdate"),t.closeSelector(e)},t.fn.concreteSizeSelector=function(e){return t.each(t(this),function(n,s){new i(t(this),e)})},e.ConcreteSizeSelector=i}(this,$),function(e,t){"use strict";function i(e,t){return this instanceof i==!1?new i(e,t):(this.display=e,void(this.css=t))}function n(e,n){var s,a,o=this;if(n=t.extend({inputName:!1,fontFamily:-1,color:-1,italic:-1,underline:-1,uppercase:-1,fontSizeValue:-1,fontSizeUnit:"px",fontWeight:-1,letterSpacingValue:-1,letterSpacingUnit:"px",lineHeightValue:-1,lineHeightUnit:"px"},n),ConcreteStyleCustomizerPalette.call(o,e,n),o.$fontMenu=o.$widget.find("select[data-style-customizer-field=font]"),o.$sliders=o.$widget.find("div.ccm-style-customizer-slider"),o.$sliders.slider({min:0,max:64,value:0,create:function(e,i){t(this).parent().find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html("0")},slide:function(e,i){t(this).parent().find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html(i.value)}}),o.$colorpicker=o.$widget.find("input[data-style-customizer-field=color]"),o.$colorpicker.spectrum({preferredFormat:"rgb",showAlpha:!0,className:"ccm-widget-colorpicker",showInitial:!0,showInput:!0}),o.$fontMenu.on("change",function(){var e=o.fonts[t(this).val()];t(this).css("font-family",e)}),t.each(o.fonts,function(e,t){o.$fontMenu.append('<option value="'+t+'">'+t+"</option>")}),o.options.fontFamily!=-1){var l=o.options.fontFamily.split(",")[0].replace("'","").replace("'","");"undefined"==typeof o.fonts[l]&&(o.fonts[l]=new i(l,o.options.fontFamily),o.$fontMenu.append(t("<option>",{value:l,text:l}))),o.setValue("font-family",o.fonts[l].css),o.$fontMenu.val(l),o.$fontMenu.css("font-family",o.fonts[l].css)}else o.$widget.find("[data-wrapper=fontFamily]").html(""),o.$element.find("[data-wrapper=fontFamily]").remove();o.options.color!=-1?(o.$colorpicker.spectrum("set",o.options.color),o.setValue("color",o.options.color)):(o.$widget.find("[data-wrapper=color]").remove(),o.$element.find("[data-wrapper=color]").remove()),o.options.underline!=-1?(o.$widget.find("input[data-style-customizer-field=underline]").prop("checked",o.options.underline),o.setValue("underline",o.options.underline?1:0)):(o.$widget.find("[data-wrapper=underline]").remove(),o.$element.find("[data-wrapper=underline]").remove()),o.options.uppercase!=-1?(o.$widget.find("input[data-style-customizer-field=uppercase]").prop("checked",o.options.uppercase),o.setValue("uppercase",o.options.uppercase?1:0)):(o.$widget.find("[data-wrapper=uppercase]").remove(),o.$element.find("[data-wrapper=uppercase]").remove()),o.options.italic!=-1?(o.$widget.find("input[data-style-customizer-field=italic]").prop("checked",o.options.italic),o.setValue("italic",o.options.italic?1:0)):(o.$widget.find("[data-wrapper=italic]").remove(),o.$element.find("[data-wrapper=italic]").remove()),o.options.fontSizeValue!=-1?(s=o.$widget.find("div[data-style-customizer-field=font-size]"),a=s.find("div.ccm-style-customizer-slider"),a.slider("value",o.options.fontSizeValue),"em"==o.options.fontSizeUnit&&(a.slider("option","step",.1),a.slider("option","max",10)),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html(o.options.fontSizeValue),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit").html(o.options.fontSizeUnit),o.setValue("font-size",o.options.fontSizeValue)):(o.$widget.find("[data-wrapper=fontSize]").remove(),o.$element.find("[data-wrapper=fontSize]").remove()),o.options.fontWeight!=-1?(s=o.$widget.find("div[data-style-customizer-field=font-weight]"),a=s.find("div.ccm-style-customizer-slider"),a.slider("option","step",100),a.slider("option","max",900),a.slider("option","min",100),a.slider("value",o.options.fontWeight),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html(o.options.fontWeight),o.setValue("font-weight",o.options.fontWeight)):(o.$widget.find("[data-wrapper=fontWeight]").remove(),o.$element.find("[data-wrapper=fontWeight]").remove()),o.options.letterSpacingValue!=-1?(s=o.$widget.find("div[data-style-customizer-field=letter-spacing]"),a=s.find("div.ccm-style-customizer-slider"),a.slider("value",o.options.letterSpacingValue),"em"==o.options.letterSpacingUnit&&(a.slider("option","step",.1),a.slider("option","max",10)),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html(o.options.letterSpacingValue),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit").html(o.options.letterSpacingUnit),o.setValue("letter-spacing",o.options.letterSpacingValue)):(o.$widget.find("[data-wrapper=letterSpacing]").remove(),o.$element.find("[data-wrapper=letterSpacing]").remove()),o.options.lineHeightValue!=-1?(s=o.$widget.find("div[data-style-customizer-field=line-height]"),a=s.find("div.ccm-style-customizer-slider"),a.slider("value",o.options.lineHeightValue),"em"==o.options.lineHeightUnit&&(a.slider("option","step",.1),a.slider("option","max",10)),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-number").html(o.options.lineHeightValue),s.find("span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit").html(o.options.lineHeightUnit),o.setValue("line-height",o.options.lineHeightValue)):(o.$widget.find("[data-wrapper=lineHeight]").remove(),o.$element.find("[data-wrapper=lineHeight]").remove()),o.updateSwatch()}i.prototype.toString=function(){return this.display},n.prototype=Object.create(ConcreteStyleCustomizerPalette.prototype),n.prototype.fonts={Arial:new i("Arial","Arial, sans-serif"),Helvetica:new i("Helvetica","Helvetica, sans-serif"),Georgia:new i("Georgia","Georgia, serif"),Verdana:new i("Verdana","Verdana, sans-serif"),"Trebuchet MS":new i("Trebuchet MS","Trebuchet MS, sans-serif"),"Book Antiqua":new i("Book Antiqua","Book Antiqua, serif"),Tahoma:new i("Tahoma","Tahoma, sans-serif"),"Times New Roman":new i("Times New Roman","Times New Roman, serif"),"Courier New":new i("Courier New","Courier New, monospace"),"Arial Black":new i("Arial Black","Arial Black, sans-serif"),"Comic Sans MS":new i("Comic Sans MS","Comic Sans MS, sans-serif")},n.prototype.chooseTemplate='<span class="ccm-style-customizer-display-swatch" data-launch="style-customizer-palette"><div data-wrapper="fontFamily"><input type="hidden" name="<%=options.inputName%>[font-family]" data-style-customizer-input="font-family" /></div><div data-wrapper="color"><input type="hidden" name="<%=options.inputName%>[color]" data-style-customizer-input="color" /></div><div data-wrapper="italic"><input type="hidden" name="<%=options.inputName%>[italic]" data-style-customizer-input="italic" /></div><div data-wrapper="underline"><input type="hidden" name="<%=options.inputName%>[underline]" data-style-customizer-input="underline" /></div><div data-wrapper="uppercase"><input type="hidden" name="<%=options.inputName%>[uppercase]" data-style-customizer-input="uppercase" /></div><div data-wrapper="fontWeight"><input type="hidden" name="<%=options.inputName%>[font-weight]" data-style-customizer-input="font-weight" /></div><div data-wrapper="fontSize"><input type="hidden" name="<%=options.inputName%>[font-size][size]" data-style-customizer-input="font-size" /><input type="hidden" name="<%=options.inputName%>[font-size][unit]" value="<%=options.fontSizeUnit%>" /></div><div data-wrapper="letterSpacing"><input type="hidden" name="<%=options.inputName%>[letter-spacing][size]" data-style-customizer-input="letter-spacing" /><input type="hidden" name="<%=options.inputName%>[letter-spacing][unit]" value="<%=options.letterSpacingUnit%>" /></div><div data-wrapper="lineHeight"><input type="hidden" name="<%=options.inputName%>[line-height][size]" data-style-customizer-input="line-height" /><input type="hidden" name="<%=options.inputName%>[line-height][unit]" value="<%=options.lineHeightUnit%>" /></div><span>T</span></span>',n.prototype.selectorWidgetTemplate='<div class="ccm-ui ccm-style-customizer-palette"><div><select data-style-customizer-field="font" data-wrapper="fontFamily"><option value=""><%=i18n.chooseFont%></option></select> <span data-wrapper="color"><input type="text" data-style-customizer-field="color"></span></div><div data-wrapper="italic" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="italic"> <%=i18n.italic%></label></div><div data-wrapper="underline" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="underline"> <%=i18n.underline%></label></div><div data-wrapper="uppercase" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="uppercase"> <%=i18n.uppercase%></label></div><div data-wrapper="fontSize"><label><%=i18n.fontSize%></label><div data-style-customizer-field="font-size"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div><div data-wrapper="fontWeight"><label><%=i18n.fontWeight%> <i class="fa fa-question-circle launch-tooltip" title="400 = Normal, 700 = Bold"></i></label><div data-style-customizer-field="font-weight"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span></span></div></div><div data-wrapper="letterSpacing"><label><%=i18n.letterSpacing%></label><div data-style-customizer-field="letter-spacing"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div><div data-wrapper="lineHeight"><label><%=i18n.lineHeight%></label><div data-style-customizer-field="line-height"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div><div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary"><%=i18n.save%></button></div></div>',n.prototype.updateSwatch=function(){var e=this,t=e.$element.find("span.ccm-style-customizer-display-swatch");e.getValue("font-family")&&t.css("font-family",e.getValue("font-family")),e.getValue("color")&&t.css("color",e.getValue("color")),t.css("font-weight","inherit"),t.css("font-style","inherit"),t.css("text-decoration","inherit"),t.css("text-transform","inherit"),"1"===e.getValue("italic")&&t.css("font-style","italic"),"1"===e.getValue("underline")&&t.css("text-decoration","underline"),t.css("font-weight",e.getValue("font-weight")),"1"===e.getValue("uppercase")&&t.css("text-transform","uppercase"),t.css("font-size","14px")},n.prototype.save=function(e){var t=this;t.setValue("font-family",t.options.fontFamily!=-1?t.fonts[t.$fontMenu.val()].css:""),t.setValue("color",t.$widget.find("input[data-style-customizer-field=color]").spectrum("get")),t.setValue("italic",t.$widget.find("input[data-style-customizer-field=italic]").is(":checked")?"1":0),t.setValue("underline",t.$widget.find("input[data-style-customizer-field=underline]").is(":checked")?"1":0),t.setValue("uppercase",t.$widget.find("input[data-style-customizer-field=uppercase]").is(":checked")?"1":0),t.setValue("font-size",t.$widget.find("div[data-style-customizer-field=font-size] div.ccm-style-customizer-slider").slider("value")),t.setValue("font-weight",t.$widget.find("div[data-style-customizer-field=font-weight] div.ccm-style-customizer-slider").slider("value")),t.setValue("letter-spacing",t.$widget.find("div[data-style-customizer-field=letter-spacing] div.ccm-style-customizer-slider").slider("value")),t.setValue("line-height",t.$widget.find("div[data-style-customizer-field=line-height] div.ccm-style-customizer-slider").slider("value")),t.updateSwatch(),ConcreteEvent.publish("StyleCustomizerControlUpdate"),t.closeSelector(e)},t.fn.concreteTypographySelector=function(e){return t.each(t(this),function(i,s){new n(t(this),e)})},e.ConcreteTypographySelector=n}(this,$),function(e,t){"use strict";function i(e,i){var n=this;i=t.extend({},i),n.options=i,n.$element=e,n.$toolbar=n.$element.find(">ul"),n.$toolbar.find("div.dropdown-menu").on("click",function(e){return!!t(e.target).is("button")||void e.stopPropagation()}),n.setupForm(),n.setupButtons(),n.setupSliders()}function n(e,t){var n=this;i.call(n,e,t)}function s(e,t){var n=this;i.call(n,e,t)}i.prototype={refreshStyles:function(e){e.oldIssID&&t("head").find("style[data-style-set="+e.oldIssID+"]").remove(),e.issID&&e.css&&t("head").append(e.css)},setupForm:function(){var e=this;e.$element.find(".launch-tooltip").tooltip(),e.$element.concreteAjaxForm({success:function(t){e.handleResponse(t)},error:function(t){e.$toolbar.prependTo("#ccm-inline-toolbar-container").show()}})},setupButtons:function(){var e=this;e.$toolbar.on("click.inlineStyleCustomizer","button[data-action=cancel-design]",function(){return e.$element.hide(),ConcreteEvent.fire("EditModeExitInline"),!1}),e.$toolbar.on("click.inlineStyleCustomizer","button[data-action=reset-design]",function(){return t.concreteAjax({url:t(this).attr("data-reset-action"),success:function(t){e.handleResponse(t)}}),!1}),e.$toolbar.on("click.inlineStyleCustomizer","button[data-action=save-design]",function(){return e.$toolbar.hide().prependTo(e.$element),e.$element.submit(),ConcreteEvent.unsubscribe("EditModeExitInlineComplete"),!1})},setupSliders:function(){var e=this;e.$toolbar.find(".ccm-inline-style-sliders").each(function(){var e=t(this).next().children(".ccm-inline-style-slider-value"),i=e.attr("data-value-format"),n=t(this),s=parseInt(t(this).attr("data-style-slider-min")),a=parseInt(t(this).attr("data-style-slider-max")),o=t(this).attr("data-style-slider-default-setting"),l=function(){return parseInt(e.val().replace(/\D\-/g,""))},c=function(){(parseInt(o)===l()||isNaN(l()))&&e.prop("disabled",!0).val(o+i)};n.slider({min:s,max:a,value:l(),slide:function(t,n){e.prop("disabled",!1),e.val(n.value+i),c()}}),e.change(function(){var e=l();e>a?e=a:e<s?e=s:isNaN(e)&&(e=o),t(this).val(e+i),n.slider("value",e),c()}).blur(function(){c()}).parent().click(function(){e.prop("disabled")&&e.prop("disabled",!1).select()}),c()})}},n.prototype=Object.create(i.prototype),n.prototype.handleResponse=function(e){var i=this,n=new Concrete.getEditMode,s=n.getAreaByID(e.aID),a=s.getBlockByID(parseInt(e.originalBlockID)),o=s.getEnableGridContainer()?1:0,l=CCM_DISPATCHER_FILENAME+"/ccm/system/block/render";t.get(l,{arHandle:s.getHandle(),cID:e.cID,bID:e.bID,arEnableGridContainer:o},function(s){ConcreteToolbar.disableDirectExit();var o=a.replace(s);ConcreteAlert.notify({message:e.message}),i.refreshStyles(e),ConcreteEvent.fire("EditModeExitInline",{action:"save_inline",block:o}),ConcreteEvent.fire("EditModeExitInlineComplete",{block:o}),t.fn.dialog.hideLoader(),n.destroyInlineEditModeToolbars(),n.scanBlocks()})},s.prototype=Object.create(i.prototype),s.prototype.handleResponse=function(e){var t=this,i=new Concrete.getEditMode,n=i.getAreaByID(e.aID);t.refreshStyles(e),n.getElem().removeClassExcept("ccm-area ccm-global-area"),e.containerClass&&n.getElem().addClass(e.containerClass),i.destroyInlineEditModeToolbars()},t.fn.concreteBlockInlineStyleCustomizer=function(e){return t.each(t(this),function(i,s){new n(t(this),e)})},t.fn.concreteAreaInlineStyleCustomizer=function(e){return t.each(t(this),function(i,n){new s(t(this),e)})},t.fn.removeClassExcept=function(e){return this.each(function(i,n){for(var s=e.split(" "),a=[],o=t(n),l=0;l<s.length;l++)o.hasClass(s[l])&&a.push(s[l]);o.removeClass().addClass(a.join(" "))})},e.ConcreteBlockInlineStyleCustomizer=n,e.ConcreteAreaInlineStyleCustomizer=s}(this,$);