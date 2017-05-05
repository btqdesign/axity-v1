/*
(function ($) {
    $(document).ready(function () {
        $("[id^='dmst_mc_subscribe_canSubscribe']").click(function () {
            var tail = $(this).attr("id").replace("dmst_mc_subscribe_canSubscribe", "");
            var name = $("#dmst_mc_subscribe_displayName" + tail).parent().parent();
            var title = $("#dmst_mc_subscribe_widgetTitle" + tail).parent().parent();
            var links = $("[name^='dmst_mc_subscribe_categories" + tail + "']").parent().parent();
            if ($(this).attr("checked") === "checked") {
                name.show();
                links.show();
                title.show();
            } else {
                name.hide();
                links.hide();
                title.hidde();
            }
        });
        $("[id^='dmst_mc_subscribe_canSubscribe']").each(function () {
            if ($(this).attr("checked") !== "checked") {
                var tail = $(this).attr("id").replace("dmst_mc_subscribe_canSubscribe", "");
                var name = $("#dmst_mc_subscribe_displayName" + tail).parent().parent();
                var title = $("#dmst_mc_subscribe_widgetTitle" + tail).parent().parent();
                var links = $("[name^='dmst_mc_subscribe_categories" + tail + "']").parent().parent();
                name.hide();
                links.hide();
                title.hide();
            }
        });

    });
})(jQuery);
*/
function dms3immediateListsLoaded() {
    location.reload();
}