// selectfile.js v1.0
//
// Copyright (c) 2007 Cyril Pereira
// Author: Cyril Pereira | http://www.cyrilpereira.com
// 
// selectfile is freely distributable under the terms of an MIT-style license.
//
/*-----------------------------------------------------------------------------------------------*/

var SelectFile = Class.create();
SelectFile.prototype = {
	initialize: function(objConfiguration) {
		FileDefault="aucun fichier";
		this.currentdir;
		this.selectfile;
		this.validExt=objConfiguration.ValidExtension;;
		this.inputSelect=objConfiguration.InputId;
		this.idManager=objConfiguration.DivId;
		this.selectfileOBJ=objConfiguration.SourceObjName;
		this._source="../../../"+objConfiguration.SourceDir; // path from the php file
		this.currentdir;
		this.selectfile;
		this._currentdir  = this._source;
		this._currentdisplay  = "";
		this.contentDirJSON=new Object();
		this.Hkey=this.makeHashKey();
		div_display = Builder.node('div',{});
		div_select = Builder.node('div',{style:"",id:"fileBrowser_"+this.Hkey});
		div_select_container = Builder.node('div',{style:"position:absolute;background:white;display:none;border:1px #cbcbcb solid;height:150px;font-size:10pt;font-family:helvetica;width:202px;overflow:auto;overflow-x:hidden;",id:"fileBrowserContainer_"+this.Hkey});
		div_file = Builder.node('div',{id:"fileinput_"+this.Hkey,style:"background:url('images/back_select.gif');background-repeat:no-repeat;height:22px;padding-top:3px;padding-left:5px;width:203px;font-family:helvetica;font-size:9pt;",onClick:this.selectfileOBJ+".browseFile();"},String(FileDefault.truncate(25)));
		
		div_file.onmouseover=function(){this.style.background="url('images/back_select_over.gif') no-repeat";}
		div_file.onmouseout=function(){this.style.background="url('images/back_select.gif') no-repeat";}
		
		div_display.appendChild(div_file);
		$(this.idManager).appendChild(div_display);
		div_select_container.appendChild(div_select);
		$(this.idManager).appendChild(div_select_container);
		this.updateDirList();
	},
	updateDirList:function(){
		new Ajax.Request('includes/php/filemanager/getjson.php', {
			method: 'post', 
			parameters:{_currentdir:this._currentdir,validExt:this.validExt},
			onComplete: this.displaydir.bind(this)
		});
	},
	enterDirectory:function(newdir){
		this._currentdisplay+=newdir+"/";
		this._currentdir  +=newdir+"/";
		this.updateDirList();
	},
	parentDirectory:function(){
		this._currentdir=this.downParentPosition(this._source,this._currentdir);
		this._currentdisplay=this._currentdir.substr(this._source.length,this._currentdir.length);
		this.updateDirList();
	},
	downParentPosition:function(start,dir){
		var tmpdir=dir.split("/");
		this._currentdisplay="";
		dir="";//start;
		for (aa=0;aa<tmpdir.length-2;aa++){
			dir+=tmpdir[aa]+"/";
		}
		return dir;
	},
	selectFile:function(fileElt){
		$(this.inputSelect).value=(this._currentdisplay+fileElt);
		$("fileinput_"+this.Hkey).update(fileElt.truncate(25));
		this.browseFile();
	},
	displaydir:function(e){
		this.contentDirJSON=eval(e.responseText)[0];
		$("fileBrowser_"+this.Hkey).update("");
		if (this._currentdir!=this._source){
			div = Builder.node('div',{onClick:this.selectfileOBJ+".parentDirectory();",style:"cursor:pointer"},"parent");
			this.makeMouseOver(div);
			$("fileBrowser_"+this.Hkey).appendChild(div);
		}
		for (aa=0;aa<this.contentDirJSON.DIR.COUNT;aa++){
			div = Builder.node('div',{onClick:this.selectfileOBJ+".enterDirectory('"+this.contentDirJSON.DIR.NAME[aa]+"');",style:"cursor:pointer"});
			this.makeMouseOver(div);
			img = Builder.node('img',{align:"left",src:"includes/php/filemanager/"+this.contentDirJSON.DIR.ICON[aa]});
			div.appendChild(img);
			div.innerHTML+=String(this.contentDirJSON.DIR.NAME[aa]).truncate(25);
			$("fileBrowser_"+this.Hkey).appendChild(div);
		}
		for (aa=0;aa<this.contentDirJSON.FILE.COUNT;aa++){
			div = Builder.node('div',{id:this.contentDirJSON.FILE.NAME[aa],onClick:this.selectfileOBJ+".selectFile (this.id);",style:"clear:both;cursor:pointer"});
			this.makeMouseOver(div);
			img = Builder.node('img',{align:"left",src:"includes/php/filemanager/"+this.contentDirJSON.FILE.ICON[aa]});
			div.appendChild(img);
			div.innerHTML+=String(this.contentDirJSON.FILE.NAME[aa]).truncate(25);
			$("fileBrowser_"+this.Hkey).appendChild(div);
		}
	},
	makeMouseOver:function(elt){
		Event.observe(elt,'mouseover', function(e){
			var elt = Event.element(e);
			elt.setStyle({
				backgroundColor:'#eee'
			});
		});
		Event.observe(elt,'mouseout', function(e){
			var elt = Event.element(e);
			elt.setStyle({
				backgroundColor:'#fff'
			});
		});
	},
	browseFile:function(){
		var objFileSelectContent="fileBrowserContainer_"+this.Hkey;
		if ($(objFileSelectContent).visible()==true){
			//new Effect.Fade(objFileSelectContent,{duration:0.2});
			$(objFileSelectContent).hide();
		}else{
			$(objFileSelectContent).show();
			//new Effect.Appear(objFileSelectContent,{duration:0.2});
		}
	},
	makeHashKey:function makeHashKey(){
		var KeyH = new Array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		var HK = "";
		for (var bb = 0; bb<6; bb++) {
			var rand = Math.round(Math.random()*KeyH.length-2);
			if (rand<0) {
				rand = 0;
			} else if (rand>KeyH.length-2) {
				rand = KeyH.length-2;
			}
			HK += KeyH[rand];
		}
		return HK;
	}
};