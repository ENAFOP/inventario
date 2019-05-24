jQuery(document).ready(function(){
   var tiempito=$("#tiempito").val();
   var tokencito=$("#tokencito").val();
   //alert("tokencito: "+tokencito);
    //var proceso=$("#proceso").val();
    jQuery(".mySearch").ajaxlivesearch({
        loaded_at: tiempito,
        token: tokencito,
        max_input: 20,
        onResultClick: function(e, data) {
            // get the index 0 (first column) value
           // get the index 1 (second column) value
            var selectedOne = jQuery(data.selected).find('td').eq('1').html();

            // set the input value
            jQuery('#ls_query').val(selectedOne);

            // hide the result
            jQuery("#ls_query").trigger('ajaxlivesearch:hide_result');
        },
        onResultEnter: function(e, data) 
        {
            // do whatever you want
            alert("hola");
            jQuery("#ls_query").trigger('ajaxlivesearch:search', {query: 'test'});
        },
        onAjaxComplete: function(e, data) {

        }
    });
})