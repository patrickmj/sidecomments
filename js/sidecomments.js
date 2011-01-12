SideComments = {

  pathData : {},
  pathHeight: 100,
	openDialogs: {
    size : function() {
      var i = 0;
      for (var prop in this ) {
        if(typeof this[prop] != 'function') {         
          i++;  
        }               
      }
      return i;
    }		
	},
	
  urlBase: "",

  init : function () {
    //setup dialog
    SideComments.dialog = document.getElementById('ui-dialog');
    jQuery(SideComments.dialog).dialog({ autoOpen: false, stack: true });
    this.buildPath();     
  },

  buildPath : function() {
  
    this.pathUL = document.getElementById('sidecomments-path');
    for (var i = 0; i < this.pathData.length; i++ ) {
    
      newLI = this.buildPathLI(this.pathData[i]);
      if(i == 0) {
        jQuery(newLI).addClass('sidecomments-item-first');
      }
      if(i == this.pathData.length - 1) {
        jQuery(newLI).addClass('sidecomments-item-last');       
      }
      this.pathUL.appendChild(newLI);
    }
  },

  buildPathLI : function(pathItem) {
    newLI = document.createElement('li');
    newLI.setAttribute('class', 'sidecomments-path-item');
    jQuery(newLI).click(SideComments.pathItemShow);
		jQuery(newLI).mouseover(SideComments.pathItemHighlight);
    newLI.data = pathItem;
    var newSpan = document.createElement('span');
    newSpan.innerHTML = pathItem.title;
    newSpan.setAttribute('title', pathItem.title); 
    newLI.appendChild(newSpan);
    return newLI;
  
  },
  
  pathItemShow : function(event) {    
    data = event.target.data ? event.target.data : event.target.parentNode.data;
		if(typeof SideComments.openDialogs[data.id] == 'object') {
			return false;
		}
    dialog = jQuery("<div class='ui-dialog'>" + data.content + "</div>").dialog({ title : data.link , close : SideComments.pathItemClose});
    jQuery(dialog).data('id', data.id);
		jQuery(dialog).dialog('open');
		jQuery(dialog).dialog('option', 'position', [10, 10 + 40 * SideComments.openDialogs.size()]);
		SideComments.openDialogs[data.id] = dialog;
		return false;        
  },
  
	pathItemHighlight : function(event) {
		data = event.target.data ? event.target.data : event.target.parentNode.data;
		dialog = SideComments.openDialogs[data.id];
		if(dialog) {
		  jQuery(dialog).dialog('moveToTop');
			jQuery(dialog).addClass('sidecomments-highlight');			
		}						
	},
	
  pathItemClose : function(event, ui) {    
		id = jQuery(event.target).data('id');
    delete SideComments.openDialogs[id] ; 		
  },
  
 };





