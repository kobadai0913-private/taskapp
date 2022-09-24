/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/task.js ***!
  \******************************/
$(function () {
  $('#insert').click(function () {
    var count_start = document.querySelectorAll(".task_start_datetime").length;

    if (count_start >= 6) {
      document.getElementById("insert").setAttribute("disabled", true);
      var tr_form = "<font color='red' id='task_start'>※追加上限に達しました。5個まで追加可能です。</font><br>";
      $(tr_form).prependTo($('.task_start_date_append'));
    } else {
      var tr_form = '<br><input type="datetime-local" class="task_start_datetime" id="task_start_datetime' + '' + count_start + '" name="task_start_datetime' + '' + count_start + '" style="width: 190px;" value="{{old("task_start_datetime' + '' + count_start + '")}}">';
      document.getElementById("task_start_datetime_counter").value = count_start;
      $(tr_form).appendTo($('.task_start_date_append'));
    }

    var count_end = document.querySelectorAll(".task_end_datetime").length;

    if (count_end >= 6) {
      document.getElementById("insert").setAttribute("disabled", true);
      var tr_form = "<font color='red' id='task_end'>※追加上限に達しました。5個まで追加可能です。</font><br>";
      $(tr_form).prependTo($('.task_end_date_append'));
    } else {
      var tr_form = '<br><input type="datetime-local" class="task_end_datetime" id="task_end_datetime' + '' + count_end + '" name="task_end_datetime' + '' + count_end + '" style="width: 190px;" value="{{old("task_end_datetime' + '' + count_end + '")}}">';
      document.getElementById("task_end_datetime_counter").value = count_end;
      $(tr_form).appendTo($('.task_end_date_append'));
    }

    if (count_start >= 1) {
      var delete_document = document.getElementById("delete");
      delete_document.disabled = false;
      var status_document = document.getElementById("task_start_datetime_status");
      status_document.disabled = true;
    }
  });
  $('#delete').click(function () {
    var count_start = document.getElementById("task_start_datetime_counter").value;
    var count_end = document.getElementById("task_end_datetime_counter").value;

    if (count_start == 5) {
      var removeElem = document.getElementById("task_start");
      removeElem.remove();
      removeElem = document.getElementById("task_end");
      removeElem.remove();
    }

    if (count_start <= 0) {
      var delete_document = document.getElementById("delete");
      delete_document.disabled = true;
      var status_document = document.getElementById("task_start_datetime_status");
      status_document.disabled = false;
    } else {
      var delete_document = document.getElementById("insert");
      delete_document.disabled = false;
      var removeElem = document.getElementById("task_start_datetime" + '' + count_start);
      document.getElementById("task_start_datetime_counter").value = count_start - 1;
      removeElem.remove();
      removeElem = document.getElementById("task_end_datetime" + '' + count_end);
      document.getElementById("task_end_datetime_counter").value = count_end - 1;
      removeElem.remove();
      removeElem = document.getElementById("task_start_datetime_" + '' + count_start);
      removeElem.remove();
      removeElem = document.getElementById("task_end_datetime_" + '' + count_start);
      removeElem.remove();
    }
  });
  $('#task_start_datetime_status').click(function () {
    if (document.getElementById('task_start_datetime_status').checked) {
      var start_document = document.getElementById("task_start_datetime");
      start_document.disabled = true;
      var insert_document = document.getElementById("insert");
      insert_document.disabled = true;
    } else {
      var start_document = document.getElementById("task_start_datetime");
      start_document.disabled = false;
      var insert_document = document.getElementById("insert");
      insert_document.disabled = false;
    }
  });
  $('#task_date_findflg').click(function () {
    if (document.getElementById('task_date_findflg').checked){
        check_document = document.getElementById("task_find_date");   
        check_document.disabled = false; 
    } else {
        check_document = document.getElementById("task_find_date");   
        check_document.disabled = true; 
    }
  });
  $('#task_time_findflg').click(function () {
    if (document.getElementById('task_time_findflg').checked) {
      check_document = document.getElementById("task_find_time");   
      check_document.disabled = false; 
    } else {
      check_document = document.getElementById("task_find_time");   
      check_document.disabled = true; 
    }
  });
  $('#task_name_findflg').click(function () {
    if (document.getElementById('task_name_findflg').checked) {
      check_document = document.getElementById("task_find_name");   
      check_document.disabled = false; 
    } else {
      check_document = document.getElementById("task_find_name");   
      check_document.disabled = true; 
    }
  });
});
/******/ })()
;