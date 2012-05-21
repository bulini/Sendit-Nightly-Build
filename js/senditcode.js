(function() {  
    tinymce.create('tinymce.plugins.quote', {  
        init : function(ed, url) {  
            ed.addButton('quote', {  
                title : 'Add a Quote',  
                image : url+'/images/senditfield.png',  
                onclick : function() {  
                    var userDataInput = prompt("Insert the name of Sendit Field", "");
                     if (userDataInput != null && userDataInput != 'undefined')
						ed.execCommand('mceInsertContent', false, '[senditfield fieldname="'+userDataInput +'"]');  

                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('quote', tinymce.plugins.quote);  
})();
