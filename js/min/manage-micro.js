jQuery("html").addClass("js");jQuery(document).ready(function(b){b("#dissect-video, #pref-video").colorbox({iframe:true,innerWidth:980,innerHeight:550});var a={init:function(){var c=b("#tvr-manage");c.click(function(h){var d=b(h.target);try{g=d.attr("class");if(!g){var g="tvr-undefined"}}catch(f){}if(g.indexOf("reveal-add-micro")>=0){a.reveal_add_micro(d,h)}else{if(g.indexOf("reveal-upload-micro")>=0){a.reveal_upload_micro(d,h)}else{if(g.indexOf("delete-micro")>=0){a.delete_micro(d,h)}else{if(g.indexOf("reveal-edit-meta")>=0){a.reveal_edit_meta(d,h)}else{if(g.indexOf("reveal-edit-readme")>=0){a.reveal_edit_readme(d,h)}else{if(g.indexOf("resize-image")>=0){a.resize_image(d,h)}else{if(g.indexOf("export-zip-button")>=0){a.export_zip_button(d,h)}else{if(g.indexOf("delete-file")>=0){a.delete_file(d,h)}}}}}}}}});b("a[rel='bg-image']",c).colorbox()},reveal_add_micro:function(c,d){d.preventDefault();b("#add-micro").slideToggle("slow",function(){if(c.text()=="Create New Settings Pack"){c.text("Cancel")}else{c.text("Create New Settings Pack")}})},reveal_upload_micro:function(c,d){d.preventDefault();b("#upload-micro").slideToggle("slow",function(){if(c.text()=="+ Install New Settings Pack"){c.text("Cancel")}else{c.text("+ Install New Settings Pack")}})},delete_micro:function(c,f){var d=confirm("Are you sure you want to delete this theme?");if(!d){f.preventDefault()}},reveal_edit_meta:function(c,d){d.preventDefault();b("#edit-meta").slideToggle("slow",function(){if(c.text()=="Edit Meta Info"){c.text("Cancel")}else{c.text("Edit Meta Info")}})},reveal_edit_readme:function(c,d){d.preventDefault();b("#edit-readme").slideToggle("slow",function(){if(c.text()=="Edit readme.txt"){c.text("Cancel")}else{c.text("Edit readme.txt")}})},resize_image:function(c,d){b("#resize-dimensions").toggle("slow")},export_zip_button:function(c,f){if(b("span#external-notice").text()=="warn"){var d=confirm("Warning: this theme links to external images. If someone else installs your zipped theme the background images won't show.");if(!d){f.preventDefault()}}},delete_file:function(c,f){var d=confirm("Are you sure you want to delete this file?");if(!d){f.preventDefault()}}};a.init()});