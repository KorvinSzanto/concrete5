!function(t){"use strict";var e="vanderlee.tristate",i=t.fn.val;t.widget("vanderlee.tristate",{options:{state:void 0,value:void 0,checked:void 0,unchecked:void 0,indeterminate:void 0,change:void 0,init:void 0},_create:function(){var t,e=this;return this.element.click(function(t){switch(!t.isTrigger&&t.hasOwnProperty("which")||t.preventDefault(),e.options.state){case!0:e.options.state=null;break;case!1:e.options.state=!0;break;default:e.options.state=!1}e._refresh(e.options.change)}),this.options.checked=this.element.attr("checkedvalue")||this.options.checked,this.options.unchecked=this.element.attr("uncheckedvalue")||this.options.unchecked,this.options.indeterminate=this.element.attr("indeterminatevalue")||this.options.indeterminate,"undefined"==typeof this.options.state&&(this.options.state="undefined"!=typeof this.element.attr("indeterminate")?null:this.element.is(":checked")),"undefined"!=typeof this.options.value&&(t=this._parseValue(this.options.value),"undefined"!=typeof t&&(this.options.state=t)),this._refresh(this.options.init),this},_refresh:function(i){var n=this.value();this.element.data(e,n),this.element[null===this.options.state?"attr":"removeAttr"]("indeterminate","indeterminate"),this.element.prop("indeterminate",null===this.options.state),this.element.get(0).indeterminate=null===this.options.state,this.element[this.options.state?"attr":"removeAttr"]("checked",!0),this.element.prop("checked",this.options.state===!0),t.isFunction(i)&&i.call(this.element,this.options.state,this.value())},state:function(t){return"undefined"==typeof t?this.options.state:(t!==!0&&t!==!1&&null!==t||(this.options.state=t,this._refresh(this.options.change)),this)},_parseValue:function(t){return t===this.options.checked||t!==this.options.unchecked&&(t===this.options.indeterminate?null:void 0)},value:function(t){if("undefined"==typeof t){var t;switch(this.options.state){case!0:t=this.options.checked;break;case!1:t=this.options.unchecked;break;case null:t=this.options.indeterminate}return"undefined"==typeof t?this.element.attr("value"):t}var e=this._parseValue(t);"undefined"!=typeof e&&(this.options.state=e,this._refresh(this.options.change))}}),t.fn.val=function(t){var n=this.data(e);return"undefined"==typeof n?"undefined"==typeof t?i.call(this):i.call(this,t):"undefined"==typeof t?n:(this.data(e,t),this)},t.expr.filters.indeterminate=function(i){var n=t(i);return"undefined"!=typeof n.data(e)&&n.prop("indeterminate")},t.expr.filters.determinate=function(e){return!t.expr.filters.indeterminate(e)},t.expr.filters.tristate=function(i){return"undefined"!=typeof t(i).data(e)}}(jQuery);