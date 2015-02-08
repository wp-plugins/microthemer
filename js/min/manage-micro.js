jQuery("html").addClass("js");jQuery(document).ready(function(e){var t={init:function(){TvrCombo.packTypes=["Theme Skin","Plugin Skin","Theme Scaffold","Plugin Scaffold"];if(window.parent.TvrSharedVars&&window.parent.TvrSharedVars.isDomEl(e("#update-packs-list"))){window.parent.TvrSharedVars.update_packs_obj(e("#update-packs-list").attr("rel"))}e("input.combobox").live("focus",function(){var t=e(this);t.autocomplete({source:TvrCombo[t.attr("rel")],delay:0,minLength:0,appendTo:t.parent(".input-wrap"),open:function(e,n){t.next(".combo-arrow").addClass("on")},select:function(e,n){t.parent(".input-wrap").addClass("manual-val").find(".comp-style").addClass("hidden").end().find(".combo-arrow").removeClass("on");if(t.hasClass("property-input")){TvrUi.ajax_save_settings("save")}},search:function(e,n){t.addClass("adjusted-corner-radius")},close:function(e,n){t.removeClass("adjusted-corner-radius").next(".combo-arrow").removeClass("on")}})});e("span.combo-arrow").live("mousedown",function(){if(e(this).siblings(".ui-menu").is(":visible")){t.wasOpen=true}});e("span.combo-arrow").live("click",function(){var n=e(this);var r=n.prev();if(t.wasOpen){n.removeClass("on");t.wasOpen=false;return}else{n.addClass("on");r.focus().autocomplete("search","")}});var n=e("#tvr");n.click(function(n){var r=e(n.target);try{i=r.attr("class");if(!i){var i="tvr-undefined"}}catch(s){}if(i.indexOf("pack-action")>=0||i.indexOf("pack-icon")>=0||i.indexOf("action-text")>=0){var o=r.closest(".pack-action");t.pack_button_action(o)}else if(i.indexOf("delete-pack-multi")>=0){t.delete_pack(r,"multi")}else if(i.indexOf("download-pack-multi")>=0){t.download_pack(r,"multi")}else if(i.indexOf("combobox")>=0){t.combobox_clicked(r)}else if(i.indexOf("delete-file")>=0){t.delete_file(r,n)}else if(i.indexOf("show-dialog")>=0){t.show_dialog(r)}else if(i.indexOf("show-parent-dialog")>=0){window.parent.TvrSharedVars.show_dialog(r)}else if(i.indexOf("close-dialog")>=0||i.indexOf("tvr-dialog")>=0){t.close_dialog(r)}else if(i.indexOf("color-img")>=0){t.color_img(r)}else if(i.indexOf("fake-checkbox")>=0||i.indexOf("fake-radio")>=0){t.toggle_radio_checkbox(r)}else if(i.indexOf("upload-pack")>=0){if(!e("#upload_pack_input").val()){alert("Please select a design pack zip file to upload");n.preventDefault()}}})},color_img:function(t){e.colorbox({maxWidth:"97%",maxHeight:"90%",href:t.attr("src"),open:true})},combobox_clicked:function(e){e.autocomplete("search","").next(".combo-arrow").addClass("on")},pack_button_action:function(n){var r=n.attr("rel");if(r=="info"||r=="attachments"||r=="instructions"){var i=e("#main-theme-actions").find(".pack-action").removeClass("on");var s=e("#single-content-areas").find(".manage-content-wrap").removeClass("show");e("#main-theme-actions").find("li.pack-action-"+r).addClass("on");e("#edit-"+r).addClass("show")}else if(r=="download"){t.download_pack(n,"single")}else if(r=="delete"){t.delete_pack(n,"single")}},download_pack:function(n,r){var i=e("#zip-folder").attr("rel");var s=n.attr("data-dir-name");var o=i+s+".zip";if(r=="single"){n.after(e("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"))}else{n.before(e("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"))}var u=e("#ajaxUrl").attr("rel")+"&action=tvr_download_pack&dir_name="+s;e.ajax({type:"GET",url:u,async:true,timeout:2e4,error:function(n,r){var i=e("#log-item-template").clone().removeAttr("id");i.find(".short").text("Download failed").end().find(".long").html("<p>Microthemer was not able to download the design pack</p>"+"<p>"+t.handle_ajax_error(n,r)+"</p>");e("#full-logs").html('<ul class="logs"></ul>').find("ul").append(i).focus()},success:function(t){var n=e(t).find("#microthemer-notice");var r=e(t).find("#download-status").attr("rel");if(r==="1"){location.href=o}else{e("#full-logs").html(n.html()).focus()}},complete:function(){e("#loading-gif-active").remove()}})},delete_pack:function(n,r){var i=confirm("Are you sure you want to delete this design pack?");if(!i){return false}else{if(r=="single"){n.after(e("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"))}else{n.before(e("#loading-gif-template-wbg").clone().attr("id","loading-gif-active"))}var s=n.attr("data-dir-name");var o=e("#ajaxUrl").attr("rel")+"&action=tvr_delete_micro_theme&dir_name="+s;var u=e("#delete-ok").attr("rel");e.ajax({type:"GET",url:o,async:true,timeout:2e4,error:function(n,r){var i=e("#log-item-template").clone().removeAttr("id");i.find(".short").text("Delete failed").end().find(".long").html("<p>Microthemer was not able to delete the design pack</p>"+"<p>"+t.handle_ajax_error(n,r)+"</p>");e("#full-logs").html('<ul class="logs"></ul>').find("ul").append(i).focus();e("#loading-gif-active").remove()},success:function(t){var i=e(t).find("#microthemer-notice");var o=e(t).find("#delete-status").attr("rel");if(o==="1"){if(r=="single"){window.location=u}else{e("#loading-gif-active").remove();n.closest("li").fadeOut("slow");var a=e("li.last-modified");if(a.attr("rel")==s){a.remove()}}}else{e("#full-logs").html(i.html()).focus()}},complete:function(){}})}},handle_ajax_error:function(e,t){if(e.status==0){return"Connection Error. Request Failed. Please Try again."}else if(e.status==404){return"Requested URL not found."}else if(e.status==500){return"Internel Server Error."}else if(t=="parsererror"){return"Error.\nParsing JSON Request failed."}else if(t=="timeout"){return"Request Timed out."}else{return"Unknow Error.\n"+e.responseText}},export_zip_button:function(t,n){if(e("span#external-notice").text()=="warn"){var r=confirm("Warning: this theme links to external images. If someone else installs your zipped theme the background images won't show.");if(!r){n.preventDefault()}}},delete_file:function(e,t){var n=confirm("Are you sure you want to delete this file?");if(n){window.location=e.attr("data-href")}},show_dialog:function(n){t.close_dialog();var r=n.attr("rel");if(r=="not-yet"){return false}var i=n.attr("data-ext");var s=n.attr("data-href")+"?nocache="+(new Date).getTime();if(r=="view-pack-file"){if(i=="image"){e.colorbox({maxWidth:"97%",maxHeight:"90%",href:s,open:true})}else{var o=["txt","json","css","html"];if(jQuery.inArray(i,o)<0){alert("This file could not be opened in a browser.")}else{t.ajax_load_pack_file(n,s)}}}},close_dialog:function(){e("#tvr").find("div.tvr-dialog").removeClass("show")},toggle_radio_checkbox:function(e){var t=e.prev();if(t.is(":checked")){if(e.hasClass("fake-radio")){return false}t.prop("checked",false);e.removeClass("on")}else{if(e.hasClass("fake-radio")){e.closest(".fake-radio-parent").find('input[type="radio"]').prop("checked",false).end().find(".fake-radio").removeClass("on")}t.prop("checked",true);e.addClass("on")}},ajax_load_pack_file:function(n,r){var i=n.attr("data-base-name");n.after(e("#loading-gif-template-mgbg").clone().attr("id","loading-gif-active"));e.ajax({type:"GET",url:r,async:false,dataType:"text",timeout:2e4,error:function(n,i){var s=e("#log-item-template").clone().removeAttr("id");s.find(".short").text("Delete failed").end().find(".long").html("<p>Microthemer was not able to load: "+r+"</p>"+"<p>"+t.handle_ajax_error(n,i)+"</p>");e("#full-logs").html('<ul class="logs"></ul>').find("ul").append(s).focus()},success:function(t){var n="#pack-file-content";e(n).html(t);e.colorbox({width:"80%",height:"80%",html:e(n).clone().attr("id","pack-file-content-active"),onClosed:function(){},open:true})},complete:function(){e("#loading-gif-active").remove()}})}};t.init();var n={init:function(){var t=e("#tvr-preferences");t.click(function(t){var n=e(t.target);try{r=n.attr("class");if(!r){var r="tvr-undefined"}}catch(i){}})}};n.init()})