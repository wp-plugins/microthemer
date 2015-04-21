// GruntPackage - v1.0.0 (2015-04-21)
jQuery("html").addClass("js"),jQuery(document).ready(function(a){var b={init:function(){TvrCombo.packTypes=["Theme Skin","Plugin Skin","Theme Scaffold","Plugin Scaffold"],window.parent.TvrSharedVars&&window.parent.TvrSharedVars.isDomEl(a("#update-packs-list"))&&window.parent.TvrSharedVars.update_packs_obj(a("#update-packs-list").attr("rel")),a("input.combobox").live("focus",function(){var b=a(this);b.autocomplete({source:TvrCombo[b.attr("rel")],delay:0,minLength:0,appendTo:b.parent(".input-wrap"),open:function(){b.next(".combo-arrow").addClass("on")},select:function(){b.parent(".input-wrap").addClass("manual-val").find(".comp-style").addClass("hidden").end().find(".combo-arrow").removeClass("on"),b.hasClass("property-input")&&TvrUi.ajax_save_settings("save")},search:function(){b.addClass("adjusted-corner-radius")},close:function(){b.removeClass("adjusted-corner-radius").next(".combo-arrow").removeClass("on")}})}),a("span.combo-arrow").live("mousedown",function(){a(this).siblings(".ui-menu").is(":visible")&&(b.wasOpen=!0)}),a("span.combo-arrow").live("click",function(){var c=a(this),d=c.prev();return b.wasOpen?(c.removeClass("on"),void(b.wasOpen=!1)):(c.addClass("on"),void d.focus().autocomplete("search",""))});var c=a("#tvr");c.click(function(c){var d=a(c.target);try{if(e=d.attr("class"),!e)var e="tvr-undefined"}catch(f){}if(e.indexOf("pack-action")>=0||e.indexOf("pack-icon")>=0||e.indexOf("action-text")>=0){var g=d.closest(".pack-action");b.pack_button_action(g)}else e.indexOf("delete-pack-multi")>=0?b.delete_pack(d,"multi"):e.indexOf("download-pack-multi")>=0?b.download_pack(d,"multi"):e.indexOf("combobox")>=0?b.combobox_clicked(d):e.indexOf("delete-file")>=0?b.delete_file(d,c):e.indexOf("show-dialog")>=0?b.show_dialog(d):e.indexOf("show-parent-dialog")>=0?window.parent.TvrSharedVars.show_dialog(d):e.indexOf("close-dialog")>=0||e.indexOf("tvr-dialog")>=0?b.close_dialog(d):e.indexOf("color-img")>=0?b.color_img(d):e.indexOf("fake-checkbox")>=0||e.indexOf("fake-radio")>=0?b.toggle_radio_checkbox(d):e.indexOf("upload-pack")>=0&&(a("#upload_pack_input").val()||(alert("Please select a design pack zip file to upload"),c.preventDefault()))})},color_img:function(b){a.colorbox({maxWidth:"97%",maxHeight:"90%",href:b.attr("src"),open:!0})},combobox_clicked:function(a){a.autocomplete("search","").next(".combo-arrow").addClass("on")},pack_button_action:function(c){var d=c.attr("rel");if("info"==d||"attachments"==d||"instructions"==d){{a("#main-theme-actions").find(".pack-action").removeClass("on"),a("#single-content-areas").find(".manage-content-wrap").removeClass("show")}a("#main-theme-actions").find("li.pack-action-"+d).addClass("on"),a("#edit-"+d).addClass("show")}else"download"==d?b.download_pack(c,"single"):"delete"==d&&b.delete_pack(c,"single")},download_pack:function(c,d){var e=a("#zip-folder").attr("rel"),f=c.attr("data-dir-name"),g=e+f+".zip";"single"==d?c.after(a("#loading-gif-template-wbg").clone().attr("id","loading-gif-active")):c.before(a("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"));var h=a("#ajaxUrl").attr("rel")+"&action=tvr_download_pack&dir_name="+f;a.ajax({type:"GET",url:h,async:!0,timeout:2e4,error:function(c,d){var e=a("#log-item-template").clone().removeAttr("id");e.find(".short").text("Download failed").end().find(".long").html("<p>Microthemer was not able to download the design pack</p><p>"+b.handle_ajax_error(c,d)+"</p>"),a("#full-logs").html('<ul class="logs"></ul>').find("ul").append(e).focus()},success:function(b){var c=a(b).find("#microthemer-notice"),d=a(b).find("#download-status").attr("rel");"1"===d?location.href=g:a("#full-logs").html(c.html()).focus()},complete:function(){a("#loading-gif-active").remove()}})},delete_pack:function(c,d){var e=confirm("Are you sure you want to delete this design pack?");if(!e)return!1;"single"==d?c.after(a("#loading-gif-template-wbg").clone().attr("id","loading-gif-active")):c.before(a("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"));var f=c.attr("data-dir-name"),g=a("#ajaxUrl").attr("rel")+"&action=tvr_delete_micro_theme&dir_name="+f,h=a("#delete-ok").attr("rel");a.ajax({type:"GET",url:g,async:!0,timeout:2e4,error:function(c,d){var e=a("#log-item-template").clone().removeAttr("id");e.find(".short").text("Delete failed").end().find(".long").html("<p>Microthemer was not able to delete the design pack</p><p>"+b.handle_ajax_error(c,d)+"</p>"),a("#full-logs").html('<ul class="logs"></ul>').find("ul").append(e).focus(),a("#loading-gif-active").remove()},success:function(b){var e=a(b).find("#microthemer-notice"),g=a(b).find("#delete-status").attr("rel");if("1"===g)if("single"==d)window.location=h;else{a("#loading-gif-active").remove(),c.closest("li").fadeOut("slow");var i=a("li.last-modified");i.attr("rel")==f&&i.remove()}else a("#full-logs").html(e.html()).focus()},complete:function(){}})},handle_ajax_error:function(a,b){return 0==a.status?"Connection Error. Request Failed. Please Try again.":404==a.status?"Requested URL not found.":500==a.status?"Internel Server Error.":"parsererror"==b?"Error.\nParsing JSON Request failed.":"timeout"==b?"Request Timed out.":"Unknow Error.\n"+a.responseText},export_zip_button:function(b,c){if("warn"==a("span#external-notice").text()){var d=confirm("Warning: this theme links to external images. If someone else installs your zipped theme the background images won't show.");d||c.preventDefault()}},delete_file:function(a){var b=confirm("Are you sure you want to delete this file?");b&&(window.location=a.attr("data-href"))},show_dialog:function(c){b.close_dialog();var d=c.attr("rel");if("not-yet"==d)return!1;var e=c.attr("data-ext"),f=c.attr("data-href")+"?nocache="+(new Date).getTime();if("view-pack-file"==d)if("image"==e)a.colorbox({maxWidth:"97%",maxHeight:"90%",href:f,open:!0});else{var g=["txt","json","css","html"];jQuery.inArray(e,g)<0?alert("This file could not be opened in a browser."):b.ajax_load_pack_file(c,f)}},close_dialog:function(){a("#tvr").find("div.tvr-dialog").removeClass("show")},toggle_radio_checkbox:function(a){var b=a.prev();if(b.is(":checked")){if(a.hasClass("fake-radio"))return!1;b.prop("checked",!1),a.removeClass("on")}else a.hasClass("fake-radio")&&a.closest(".fake-radio-parent").find('input[type="radio"]').prop("checked",!1).end().find(".fake-radio").removeClass("on"),b.prop("checked",!0),a.addClass("on")},ajax_load_pack_file:function(c,d){c.attr("data-base-name");c.after(a("#loading-gif-template-mgbg").clone().attr("id","loading-gif-active")),a.ajax({type:"GET",url:d,async:!1,dataType:"text",timeout:2e4,error:function(c,e){var f=a("#log-item-template").clone().removeAttr("id");f.find(".short").text("Delete failed").end().find(".long").html("<p>Microthemer was not able to load: "+d+"</p><p>"+b.handle_ajax_error(c,e)+"</p>"),a("#full-logs").html('<ul class="logs"></ul>').find("ul").append(f).focus()},success:function(b){var c="#pack-file-content";a(c).html(b),a.colorbox({width:"80%",height:"80%",html:a(c).clone().attr("id","pack-file-content-active"),onClosed:function(){},open:!0})},complete:function(){a("#loading-gif-active").remove()}})}};b.init();var c={init:function(){var b=a("#tvr-preferences");b.click(function(b){var c=a(b.target);try{if(d=c.attr("class"),!d)var d="tvr-undefined"}catch(e){}})}};c.init()});