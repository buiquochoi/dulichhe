/**
 * Created by PMTB on 10/5/2017.
 */
$(document).ready(function () {


    $('#select-center').chosen({width:'100%'});
    $('#time .input-daterange').datepicker({
        format: "dd-mm-yyyy",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });

    $('#paging a').click(function (event) {
        event.preventDefault();
        var page = $(this).data('page');
        var url = "./?page=" + page;
        $("form").attr('action',url).submit();
    });
     
    $('.exportdata').click(function(){
	    
    });

    $(".search-button").click(function () {
        $("form#search").attr('action','./index.php').submit();
    });

    $('.export').click(function (event) {
        event.preventDefault();
        var href = $(this).attr('href');
        $("form").attr("action",href).submit();
    });

    $(".confirm").click(function () {
        var key = $(this).data('key');
        $("#confirm_button").attr('href','http://ketnoiyeuthuong.apaxenglish.com/index.php?cmd=confirm&key='+key);
    })
});