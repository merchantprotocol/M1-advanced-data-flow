function initEnigmaall(){
    if(_section == 'enigmaall'){
        $('enigmaall_extensions').update($('enigmaall_extensions_table').innerHTML)
    }
    if(_section == 'enigmastore'){
       $('enigmastore_extensions').update($('enigmaall_store_response').innerHTML)
    }
}
Event.observe(window, 'load', function() {
   initEnigmaall();
});
