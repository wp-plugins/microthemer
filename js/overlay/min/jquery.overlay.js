try{window.parent.document;if(window.parent.TvrSharedVars){(function(e){e.fn.extend({add_hover_overlay:function(){var t={};var n=e.extend(t,n);return this.each(function(){e(this).mouseenter(function(){if(!e(this).hasClass("tvr-static-overlay")){if(window.parent.TvrSharedVars.highlightEnabled){e(this).addClass("tvr-hover-overlay").add_overlay()}}})})},add_overlay:function(t){var n=["left","top","right","bottom"];var r={padding:true,margin:true,border:true,content:true};var t=e.extend(r,t);return this.each(function(){var r=window.TvrExportVars.overlayCount;var i=this;var o=e(this);if(!e(this).hasClass("tvr-element-overlay")&&e(this).is(":visible")){e(this).addClass("tvr-element-overlay");if(!e(this).hasClass("tvr-hover-overlay")){e(this).addClass("tvr-static-overlay")}var u={};var a=parseInt(o.css("font-size"));var f=e(this).offset();f.top=f.top+parseInt(o.css("border-top-width"));f.left=f.left+parseInt(o.css("border-left-width"));if(!window.parent.TvrSharedVars.highlightEnabled){var l="display:none;"}else{var l=""}e("body").prepend('<div id="over-cont-'+r+'" class="tvr-overlay tvr-container-overlay" style="width:'+e(this).innerWidth()+"px;height:"+e(this).innerHeight()+"px;"+l+'"></div>');var c=e("#over-cont-"+r);if(e(this).hasClass("tvr-hover-overlay")){c.mouseleave(function(){c.remove();e(i).removeClass("tvr-element-overlay tvr-hover-overlay")})}c.offset({top:f.top,left:f.left});if(t.margin){c.append('<div class="tvr-overlay tvr-margin-overlay"></div>');for(s in n){var h=0-parseInt(o.css("margin-"+n[s]))-parseInt(o.css("border-"+n[s]+"-width"));u[n[s]]=h/a+"em"}c.find(".tvr-margin-overlay").css(u)}if(t.border){c.append('<div class="tvr-overlay tvr-border-overlay"></div>');c.find(".tvr-border-overlay").css({top:0-parseInt(o.css("border-top-width"))/a+"em",right:0-parseInt(o.css("border-right-width"))/a+"em",bottom:0-parseInt(o.css("border-bottom-width"))/a+"em",left:0-parseInt(o.css("border-left-width"))/a+"em"})}if(t.padding){c.append('<div class="tvr-overlay tvr-padding-overlay"></div>')}if(t.content){c.append('<div class="tvr-overlay tvr-content-overlay"></div>');u={};for(s in n){u[n[s]]=parseInt(o.css("padding-"+n[s]))/a+"em"}c.find(".tvr-content-overlay").css(u)}window.TvrExportVars.overlayCount++}})}})})(jQuery);LazyLoad=function(e){function t(t,n){var r=e.createElement(t),i;for(i in n){if(n.hasOwnProperty(i)){r.setAttribute(i,n[i])}}return r}function n(e){var t=f[e],n,r;if(t){n=t.callback;r=t.urls;r.shift();l=0;if(!r.length){n&&n.call(t.context,t.obj);f[e]=null;c[e].length&&i(e)}}}function r(){var t=navigator.userAgent;u={async:e.createElement("script").async===true};(u.webkit=/AppleWebKit\//.test(t))||(u.ie=/MSIE|Trident/.test(t))||(u.opera=/Opera/.test(t))||(u.gecko=/Gecko\//.test(t))||(u.unknown=true)}function i(i,l,h,p,d){var v=function(){n(i)},m=i==="css",g=[],y,b,w,E,S,x;u||r();if(l){l=typeof l==="string"?[l]:l.concat();if(m||u.async||u.gecko||u.opera){c[i].push({urls:l,callback:h,obj:p,context:d})}else{for(y=0,b=l.length;y<b;++y){c[i].push({urls:[l[y]],callback:y===b-1?h:null,obj:p,context:d})}}}if(f[i]||!(E=f[i]=c[i].shift())){return}a||(a=e.head||e.getElementsByTagName("head")[0]);S=E.urls.concat();for(y=0,b=S.length;y<b;++y){x=S[y];if(m){w=u.gecko?t("style"):t("link",{href:x,rel:"stylesheet"})}else{w=t("script",{src:x});w.async=false}w.className="lazyload";w.setAttribute("charset","utf-8");if(u.ie&&!m&&"onreadystatechange"in w&&!("draggable"in w)){w.onreadystatechange=function(){if(/loaded|complete/.test(w.readyState)){w.onreadystatechange=null;v()}}}else if(m&&(u.gecko||u.webkit)){if(u.webkit){E.urls[y]=w.href;o()}else{w.innerHTML='@import "'+x+'";';s(w)}}else{w.onload=w.onerror=v}g.push(w)}for(y=0,b=g.length;y<b;++y){a.appendChild(g[y])}}function s(e){var t;try{t=!!e.sheet.cssRules}catch(r){l+=1;if(l<200){setTimeout(function(){s(e)},50)}else{t&&n("css")}return}n("css")}function o(){var e=f.css,t;if(e){t=h.length;while(--t>=0){if(h[t].href===e.urls[0]){n("css");break}}l+=1;if(e){if(l<200){setTimeout(o,50)}else{n("css")}}}}var u,a,f={},l=0,c={css:[],js:[]},h=e.styleSheets;return{css:function(e,t,n,r){i("css",e,t,n,r)},js:function(e,t,n,r){i("js",e,t,n,r)}}}(this.document);window.TvrExportVars={};jQuery(document).ready(function(e){e(window).keypress(function(e){if(!(e.which==115&&e.ctrlKey)&&!(e.which==19))return true;e.preventDefault();return false});e(document).keydown(function(e){if(e.which==83&&e.ctrlKey==true){window.parent.TvrSharedVars.ajax_save_settings("save");e.preventDefault();return false}});if(window.parent.TvrSharedVars.context=="visual"){if(window.parent.TvrSharedVars.progCount>0){window.parent.TvrSharedVars.update_prog_indicator("minus")}e("body").attr("title","Double-click an element to create editing options for it").addClass("tvr-crosshair");var t={};var n={stylesheetUrl:"",init:function(){n.stylesheetUrl=e("#microthemer-css").attr("href")},refreshCSS:function(){var t=n.stylesheetUrl.replace(/\?.*|$/,"?reload="+(new Date).getTime());var r=e("head").find(".lazyload");LazyLoad.css(t,function(t){$origStylesheet=e("#microthemer-css");if($origStylesheet){$origStylesheet.remove()}if(r){r.remove()}n.get_computed_styles(window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.curSelCSS),"str");window.parent.TvrSharedVars.refresh_comp_css();if(window.parent.TvrSharedVars.progCount>0){window.parent.TvrSharedVars.update_prog_indicator("minus")}},"hello")},clean_classes:function(e){e=e.replace("  "," ").replace("tvr-crosshair","").replace("  "," ").replace("tvr-element-overlay","").replace("  "," ").replace("tvr-static-overlay","").replace("  "," ");return e.replace(/^\s+|\s+$/g,"")},build_selector_suggestions:function(e){for(var r in t)delete t[r];var i={};i["a"]={};i["a"][":visited"]="visited link";i["a"][":hover"]="hover state link";i["a"][":active"]="active state link";var s=e.prop("tagName").toLowerCase();if(s=="input"){var o=e.attr("type")}var u=e.attr("id");var a=e.attr("class");if(a){a=n.clean_classes(a)}var f=e.parent().not("html");var l=0;var c="";var h=[];var p=-1;var d="&#60;"+s;if(s=="input"){d+=" type=&#34;"+o+"&#34;"}var v="Element Type Targeting (very broad)";t[v]={};t[v][s]="all <"+s+">s";if(s=="input"){t[v][s+"[type=&#34;"+o+"&#34;]"]="<"+s+'>s of type: "'+o+'"'}else if(s=="a"){for(key in i[s]){t[v][s+key]="<"+s+">s ("+i[s][key]+")"}}++l;if(a){d+=' class="'+a+'"';v="Element With Class Targeting";t[v]={};var m=a.split(" ");for(var g=0;g<m.length;g++){c=s+"."+m[g];t[v][c]="<"+s+'>s with a class of: "'+m[g]+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+'>s with a class of: "'+m[g]+" ("+i[s][key]+")"}}if(g==0){h[++p]=c}}var y=true;var b=true;++l}if(f.prop("tagName")){var w=n.find_parent_attributes(f,"class");if(w){v="Parent With Class Targeting";t[v]={};var E=w.prop("tagName").toLowerCase();var S=n.clean_classes(w.attr("class"));var x=S.split(" ");for(var g=0;g<x.length;g++){c=E+"."+x[g]+" "+s;t[v][c]="<"+s+">s with parent <"+E+'>s that have a class of "'+x[g]+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+">s with parent <"+E+'>s that have a class of "'+x[g]+'" ('+i[s][key]+")"}}if(!b&&g==0){h[++p]=c}}var T=true;++l}}if(y&&T){v="Parent & Element With Class Targeting";t[v]={};for(var g=0;g<x.length;g++){for(var N=0;N<m.length;N++){c=E+"."+x[g]+" "+s+"."+m[N];t[v][c]="<"+s+'>s that have a class of "'+m[N]+'" with parent <'+E+'>s that have a class of "'+x[g]+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+'>s that have a class of "'+m[N]+'" with parent <'+E+'>s that have a class of "'+x[g]+'" ('+i[s][key]+")"}}if(g==0){h[++p]=c}}}++l}if(window.parent.TvrSharedVars.hasValue(u)){d+=' id="'+u+'"';v="Element ID Targeting";t[v]={};c=s+"#"+u;t[v][c]="<"+s+'> with the unique id of "'+u+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+'> with the unique id of "'+u+'" ('+i[s][key]+")"}}h[++p]=c;var C=true;var k=true;++l}if(f.prop("tagName")){var L=n.find_parent_attributes(f,"id");if(L){v="Parent With ID Targeting";t[v]={};var A=L.prop("tagName").toLowerCase();var O=L.attr("id");c=A+"#"+O+" "+s;t[v][c]="<"+s+">s with parent <"+A+'> that has a unique id of "'+O+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+">s with parent <"+A+'> that has a unique id of "'+O+'" ('+i[s][key]+")"}}if(!k){h[++p]=c}var M=true;++l}}if(y&&M){v="Parent With ID & Element With Class Targeting";t[v]={};for(var N=0;N<m.length;N++){c=A+"#"+O+" "+s+"."+m[N];t[v][c]="<"+s+'>s that have a class of "'+m[N]+'" with parent <'+A+'> that has a unique id of "'+O+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+'>s that have a class of "'+m[N]+'" with parent <'+A+'> that has a unique id of "'+O+'" ('+i[s][key]+")"}}if(N==0){h[++p]=c}}++l}if(C&&M){v="Parent & Element With ID Targeting";t[v]={};c=A+"#"+O+" "+s+"#"+u;t[v][c]="<"+s+'> that has a unique id of "'+u+'" with parent <'+A+'> that has a unique id of "'+O+'"';if(s=="a"){for(key in i[s]){t[v][c+key]="<"+s+'> that has a unique id of "'+u+'" with parent <'+A+'> that has a unique id of "'+O+'" ('+i[s][key]+")"}}h[++p]=c;++l}if(p>-1){window.parent.TvrSharedVars.intelli_css=h[p]}else{window.parent.TvrSharedVars.intelli_css=s}t["Intelli"]={};t["Intelli"]["ogCount"]=l;var _=e.children().not(".tvr-container-overlay, script").filter(":first");var D=e.prevAll().not(".tvr-container-overlay, script, head").filter(":first");var P=e.nextAll().not(".tvr-container-overlay, script").filter(":first");t["Intelli"]["parent"]=f.prop("tagName")?f:false;t["Intelli"]["child"]=_.prop("tagName")?_:false;t["Intelli"]["prev"]=D.prop("tagName")?D:false;t["Intelli"]["next"]=P.prop("tagName")?P:false;d=n.finish_tag_string(s,d);t["Intelli"]["htmlString"]=d;n.switch_highlight("intelli");if(!window.parent.TvrSharedVars.highlightEnabled){window.parent.TvrSharedVars.tmpHighlighting=true;window.parent.TvrSharedVars.toggle_highlighting()}window.parent.TvrSharedVars.TvrSugCSS=t;if(!window.parent.TvrSharedVars.intelli){window.parent.TvrSharedVars.display_visual_add_sec_sel("Selector",true)}else{window.parent.TvrSharedVars.update_selector_wizard()}},finish_tag_string:function(e,t){if(e!="area"&&e!="base"&&e!="br"&&e!="col"&&e!="command"&&e!="embed"&&e!="hr"&&e!="img"&&e!="input"&&e!="meta"&&e!="param"&&e!="source"){t+="&#62;...&#60;/"+e+"&#62;"}else{t+=" /&#62;"}return t},find_parent_attributes:function(e,t){if(e.attr(t)&&n.clean_classes(e.attr(t))){return e}else{e=e.parent().not("html");if(e.prop("tagName")){return n.find_parent_attributes(e,t)}else{return false}}},allocateOverlayMode:function(t){if(t=="reg"){var r=window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.jquery_escape_quotes(window.parent.TvrSharedVars.curSelCSS))}else if(t=="intelli"){var r=window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.jquery_escape_quotes(window.parent.TvrSharedVars.intelli_css))}r=n.strip_pseudo(r);window.TvrExportVars.overlayCount=0;try{if(t!="mixed-hover"){var i=e(r).not("#wpadminbar "+r)}else{var i=n.sel_elements.eq(window.parent.TvrSharedVars.mixedElKey);e("html, body").animate({scrollTop:i.offset().top-36},250)}i.add_overlay();n.get_computed_styles(i,"cached")}catch(s){}},format_mixed_array:function(e,t){var r={};for(var i=0;i<e.length;i++){var s=t[i]["atts"];var o="&#60;"+s["type"];if(s["id"]){o+=' id="'+s["id"]+'"'}if(s["class"]){s["class"]=s["class"].replace("tvr-element-overlay","").replace("tvr-static-overlay","").replace("  "," ");if(/\S/.test(s["class"])){o+=' class="'+s["class"]+'"'}}o=n.finish_tag_string(s["type"],o);r[i]=[];r[i]["val"]=e[i];r[i]["htmlString"]=o}return r},compProps:{},rgbToHex:function(e){e=e.toString();if(e.indexOf("rgb")<0){return e}if(e.indexOf("rgba(0, 0, 0, 0)")>-1){return e.replace(/rgba\(0, 0, 0, 0\)/g,"transparent")}if(e.indexOf("rgba")>-1){var t="rgba";var n=/(.*?)rgba\((\d+), (\d+), (\d+), (\d+(\.\d{1,2})?)\)/;var r=/rgba\(.+[^\)]\)/g}else{var t="rgb";var n=/(.*?)rgb\((\d+), (\d+), (\d+)\)/;var r=/rgb\(.+[^\)]\)/g}var i=e.split(t);var s="";for(var o in i){if(i[o]){if(o>0){var u=t+i[o]}else{s+=i[o];continue}}else{continue}var a=n.exec(u);var f=parseInt(a[2]);var l=parseInt(a[3]);var c=parseInt(a[4]);if(a[5]&&t=="rgba"){var h=a[5]}var p="#"+((1<<24)+(f<<16)+(l<<8)+c).toString(16).slice(1).toUpperCase();u=u.replace(r,p);if(a[5]&&t=="rgba"){u+=" ("+a[5]+" alpha)"}s+=u}return s},strip_pseudo:function(e){return e.replace(/:hover/g,"").replace(/:active/g,"").replace(/:visited/g,"").replace(/:link/g,"")},sel_elements:{},get_computed_styles:function(t,r){if(r=="str"){t=n.strip_pseudo(t);var i=e(t).not("#wpadminbar "+t)}else{var i=t}n.sel_elements=i;if(!i.length){window.parent.TvrSharedVars.compProps={};return false}var s=window.parent.TvrSharedVars.cssProps;var o=i.length;if(o>10){o=10}var u={ltr:{start:"left",end:"right"},rtl:{start:"right",end:"left"}};var a=[];n.compProps={};var f=0;i.each(function(t){++f;if(t>o){return false}a[t]=[];var r=e(this);var i=r.css(s);for(var l in i){if(!i[l]){i[l]=i[l]}else{if(l=="text-align"&&(i[l]=="start"||i[l]=="end")){i[l]=u[i["direction"]][i[l]]}if(l=="text-decoration"){var c=i[l].split(" ");i[l]=c[0]}i[l]=n.rgbToHex(i[l]);if(l=="background-position"||l=="background-repeat"||l=="background-attachment"){var c=i[l].split(",");i[l]=c[0]}if(l=="background-image"){i["background-img-full"]=i[l];i["extracted-gradient"]=window.parent.TvrSharedVars.extract_str("gradient",i[l]);i[l]=window.parent.TvrSharedVars.extract_str("bg-image",i[l])}}}a[t]["propsList"]=i;a[t]["atts"]=[];a[t]["atts"]["type"]=r.prop("tagName").toLowerCase();a[t]["atts"]["id"]=r.attr("id");a[t]["atts"]["class"]=r.attr("class")});for(var l in s){var c=s[l];var h=a[0]["propsList"][c];var p=new Array;var d=false;for(var v in a){p[v]=a[v]["propsList"][c];if(p[v]!=h){d=true}}if(!d){n.compProps[c]=h}else{n.compProps[c]=n.format_mixed_array(p,a)}}window.parent.TvrSharedVars.compProps=n.compProps},switch_highlight:function(t){e(".tvr-container-overlay").remove();e(".tvr-element-overlay").removeClass("tvr-element-overlay tvr-static-overlay");n.allocateOverlayMode(t)},showOverlays:function(){e(".tvr-container-overlay").show()},hideOverlays:function(){e(".tvr-container-overlay").hide()},singleClick:function(e,t,n,r,i,s){t.preventDefault();setTimeout(function(){var t=parseInt(e.data("double"),10);if(t>0){e.data("double",t-1);if(s){return false}}else{if(n=="a"){window.location=e.attr("href")}else if(s){window.location=i.attr("href")}else if(r=="submit"){var o=e.parents("form");if(o){o.trigger("submit")}}}},300)}};e("body").click(function(t){var r=e(t.target);var i=r.parent();if(i.prop("tagName")&&i.prop("tagName").toLowerCase()=="a"){parentLink=true}else{parentLink=false}var s=r.prop("tagName").toLowerCase();var o=r.attr("type");if(o){o=o.toLowerCase()}if(s=="a"||parentLink||o=="submit"){n.singleClick(r,t,s,o,i,parentLink)}}).dblclick(function(t){t.preventDefault();var r=e(t.target);r.data("double",2);if(window.parent.TvrSharedVars.totalSecs<1){alert("Please create a section for adding selectors to first");window.parent.TvrSharedVars.display_visual_add_sec_sel("Section");return false}if(r.hasClass("tvr-overlay")){alert("Please disable highlighting (light bulb icon) to create editing options for the currently highlighted element");return false}var i=n.build_selector_suggestions(r)});window.TvrExportVars.switch_highlight=n.switch_highlight;window.TvrExportVars.showOverlays=n.showOverlays;window.TvrExportVars.hideOverlays=n.hideOverlays;window.TvrExportVars.build_selector_suggestions=n.build_selector_suggestions;window.TvrExportVars.refreshCSS=n.refreshCSS;window.TvrExportVars.get_computed_styles=n.get_computed_styles;e(window).load(function(){n.init();if(window.parent.TvrSharedVars.intelli){n.allocateOverlayMode("intelli")}else{n.allocateOverlayMode("reg")}window.parent.TvrSharedVars.refresh_comp_css();window.parent.TvrSharedVars.remember_page_viewed()})}})}}catch(e){}