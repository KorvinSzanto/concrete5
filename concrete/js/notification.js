!function(t,e){"use strict";function a(t,a){var i=this,a=e.extend({},a);i.$element=t,i.options=a,i.$element.on("click","[data-notification-action=archive]",function(t){t.preventDefault();var a=e(this).closest("div[data-notification-alert-id]"),n=a.attr("data-notification-alert-id"),o=a.attr("data-token");e.ajax({url:CCM_DISPATCHER_FILENAME+"/ccm/system/notification/alert/archive",dataType:"json",data:{naID:n,ccm_token:o},type:"post"}),a.queue(function(){a.addClass("animated fadeOut"),a.dequeue()}).delay(500).queue(function(){a.remove(),a.dequeue(),i.handleEmpty()})}),i.$element.on("click","a[data-workflow-task]",function(t){var a=e(this).attr("data-workflow-task"),n=e(this).closest("form"),o=e(this).closest("div[data-notification-alert-id]");t.preventDefault(),n.append('<input type="hidden" name="action_'+a+'" value="'+a+'">'),n.ajaxSubmit({dataType:"json",beforeSubmit:function(){jQuery.fn.dialog.showLoader()},success:function(t){o.addClass("animated fadeOut"),jQuery.fn.dialog.hideLoader(),setTimeout(function(){o.remove(),i.handleEmpty()},500)}})})}a.prototype={handleEmpty:function(){var t=this,e=t.$element.find("div[data-notification-alert-id]");e.length<1&&t.$element.find("[data-notification-description=empty]").show()}},e.fn.concreteNotificationList=function(t){return e.each(e(this),function(i,n){new a(e(this),t)})},t.ConcreteNotificationList=a}(this,$);