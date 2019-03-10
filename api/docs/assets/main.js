$(document).ready(function () {

    $("#searchField").on("input", function () {


        var list = $("#toc ul").find("li");
        var val = $("#searchField").val();

        if (val=="") {
            $("#toc ul").find("li").show();
        } else {
            for (var i = 0; i < list.length; i++) {
                var a = $(list[i]).find("a");
                if (a.text().toLowerCase().indexOf(val.toLowerCase()) !== -1) {
                    a.parent().show();
                } else {
                    a.parent().hide();
                }
            }
        }

    });




});