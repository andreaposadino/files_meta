/** 
 * this below is the function you need from:
 * http://stackoverflow.com/questions/1865563/
 * set-cursor-at-a-length-of-14-onfocus-of-a-textbox
 */

// Funny stuff found in stackoverflow: http://stackoverflow.com/a/646643/235125
// currently is not used
/*if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}
*/

function setCursor(node,pos){
    var node = (typeof node == "string" || 
        node instanceof String) ? document.getElementById(node) : node;
    if(!node){
        return false;
    }else if(node.createTextRange){
        var textRange = node.createTextRange();
        textRange.collapse(true);
        textRange.moveEnd(pos);
        textRange.moveStart(pos);
        textRange.select();
        return true;
    }else if(node.setSelectionRange){
        node.setSelectionRange(pos,pos);
        return true;
    }
    return false;
};



OC.Meta={
    general:'none',
    shared:'none',
    description:'none',
    filename:'none',
    loadMeta:function(item) {
        $.ajax({
            type: 'GET', 
            url: OC.filePath('files_meta', 'ajax', 'getMeta.php'), 
            data: {
                item: item
            }, 
            async: false, 
            success: function(result) {
                
                if (result && result.status === 'success') {
                    var item = result.data;
                    console.log(result);
                    OC.Meta.data=result.data.general;
                    OC.Meta.shared=result.shared;
                    OC.Meta.description=result.description;
                }
            }
        });
    },
    showDropDown:function(item, appendTo) {
        OC.Meta.filename=item;
        OC.Meta.loadMeta(item);
        //if(OC.Meta.data=='none')return;        
        var html = '<div id="metadropdown" class="metadropdown" data-item="'+item+'">';
        html += 'Description:<br />';//<span id="spdescription" style="width:65%;" >'+OC.Meta.description+'</span>';
        html += '<div id="editable" class="editable wordwrap">'+OC.Meta.description+
        '</div><br /><span class="metasave" title="save">save</span><br />';
        html += '<span id="more" class="more ui-icon ui-icon-circle-triangle-e"></span>';
        html += '<div id="slider" style="display:none">';
        html += '<div >';
        
        /* $.each(OC.Meta.data, function(key, value) { 
            html +=key + ': ' + value+'<br />'; 
        });                
        html += '</div>';*/
        
            
        html += '<br />';
        html += 'Shared:'+OC.Meta.shared;
        html += '</div></div>';
        $(html).appendTo(appendTo);
        
        if (OC.Meta.itemUsers) {
            $.each(OC.Meta.itemUsers, function(index, user) {
                if (user.parentFolder) {
                    OC.Meta.addSharedWith(user.uid, user.permissions, false, user.parentFolder);
                } else {
                    OC.Meta.addSharedWith(user.uid, user.permissions, false, false);
                }
            });
        }
        
        if (OC.Meta.itemGroups) {
            $.each(OC.Meta.itemGroups, function(index, group) {
                if (group.parentFolder) {
                    OC.Meta.addSharedWith(group.gid, group.permissions, group.users, group.parentFolder);
                } else {
                    OC.Meta.addSharedWith(group.gid, group.permissions, group.users, false);
                }
            });
        }
        if (OC.Meta.itemPrivateLink) {
            OC.Meta.showPrivateLink(item, OC.Meta.itemPrivateLink);
        }
        
        $(".metasave").button({
            icons: {
                primary: "ui-icon-disk"
            }
        });
        $(".metasave").button('disable');
        
        if($('#dir').val() == "/Shared"){
                $(".metasave").button().hide();                
            };
        
        $('#metadropdown').show('blind');
    //$('#share_with').chosen();
    },
    hideDropDown:function(callback) {
        $('#metadropdown').hide('blind', function() {
            $('#metadropdown').remove();
            if (callback) {
                callback.call();
            }
        });
    },
    updateDescription:function(item, description,longdescription, callback) {
        $.post(OC.filePath('files_meta', 'ajax', 'setMeta.php'), {
            item: item, 
            description: description,
            longdescription: longdescription
        }, function(result) {
            if (result && result.status === 'success') {
                if (callback) {
                    callback(result.data);
                }
            } else {
                OC.dialogs.alert(result.data.message, 'Error while updating metadata');
            }
        });
    }

};







$(document).ready(function(){
    if (typeof FileActions !== 'undefined') {
        // Add history button to files/index.php
        FileActions.register('all','Meta',function(){
            if (scanFiles.scanning) {
                return;
            } 
            return OC.imagePath('core','actions/info');
        },function(filename){

            if (scanFiles.scanning){
                return;
            }//workaround to prevent additional http request block scanning feedback
			
            var file = $('#dir').val()+'/'+filename;
            // Check if drop down is already visible for a different file
            //console.log(file);
            
            var appendTo = $('tr').filterAttr('data-file',filename).find('td.filename');
            //$('tr .fileactions a:contains("Meta")').css('meta');//attr('');//append("<span class='meta' title='Helo'></span>");	
		
            if (($('#metadropdown').length > 0)) {
                if (file != $('#metadropdown').data('file')) {
                    OC.Meta.hideDropDown(function () {
                        $('tr').removeClass('mouseOver');
                        $('tr').filterAttr('data-file',filename).addClass('mouseOver');
                        OC.Meta.showDropDown(file, appendTo);
                    });
                    console.log("Try to remove metadrop");
                }
            } else {
                console.log("let us show drop");
                OC.Meta.showDropDown(file, appendTo);
            //createMetaDropdown(filename, file);
            }
        });
    }
    
    $(this).click(function(event) {
        //console.log('close event');              
        if($(event.target).hasClass('editable')){
          //  console.log($('#dir').val());
            
            var editableText = $('<textarea id="description" style="width:90%"/>');
            var edit = $(event.target);//$(this);
           
            if ($('#description').length == 0) {
                //console.log("Create  editable");
                if($('#dir').val() == "/Shared")return;
                var divHtml = edit.html();
                
                me=editableText.clone(true);
                edit.replaceWith(editableText);
                editableText.val(divHtml);
                editableText.focus();
                setCursor('description', editableText.val().length); //set caret
              
                //$(this).parent().append(this.value);                    
                //    $("#description").replaceWith(me);
              
                //console.log("Try to hide");
              
                // console.log("getout  editable");
                $(".metasave").button('enable');
                return;
            }                      
          
        };
        
        if($('#description').length!=0){      
            text=$("#description").val();
            var editable= $('<div id="editable" class="editable wordwrap">'+text+'</div>');            
            $("#description").replaceWith(editable);
            $(editable).val(text);
            $(".metasave").button('enable');
           
            //return;
        }
        
        
        
        if($(event.target).parent().hasClass('metasave') && !$(event.target).parent().hasClass('disabled')){
            
            //    console.log(OC.Meta.filename);
            var description='';
            
            description=$('#editable').val();            
            //console.log("Report :disabled");
            //console.log();
            
            //console.log($('#editable'));
        
            OC.Meta.updateDescription(OC.Meta.filename, description,function(){
                //alert('Description saved!!!');            
                $(".metasave").button('disable');
            
            }); 
        }

        if($(event.target).hasClass('more')){
            //$('#more').click(function(){
            if ($("#slider").is(":hidden")) {
                $("#slider").slideDown("slow");
                // $("#more").text('less -');
                $("#more").removeClass("ui-icon-circle-triangle-e").addClass("ui-icon-circle-triangle-s");
            } else {
                $("#slider").hide();
                $("#more").removeClass("ui-icon-circle-triangle-s").addClass("ui-icon-circle-triangle-e");
            }
        //});
        }

            
        if (!($(event.target).hasClass('metadropdown')) && $(event.target).parents().index($('#metadropdown')) == -1) {
           
            if ($('#metadropdown').is(':visible')) {
                OC.Meta.hideDropDown(function() {
                    $('tr').removeClass('mouseOver');
                });
            }
        }
    });
});
