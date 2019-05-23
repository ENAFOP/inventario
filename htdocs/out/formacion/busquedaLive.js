jQuery(document).ready(function(){
    var time=$("#time").val();
    //var proceso=$("#proceso").val();
    jQuery(".mySearch").ajaxlivesearch({
        loaded_at: time,
        max_input: 20,
        onResultClick: function(e, data) {
            // get the index 0 (first column) value
            var selectedOne = jQuery(data.selected).find('td').eq('0').text();

            // set the input value
            jQuery('#ls_query').val(selectedOne);

            // hide the result
            jQuery("#ls_query").trigger('ajaxlivesearch:hide_result');
        },
        onResultEnter: function(e, data) 
        {
            // do whatever you want
            alert("hola");
            // jQuery("#ls_query").trigger('ajaxlivesearch:search', {query: 'test'});
        },
        onAjaxComplete: function(e, data) {

        }
    });
})
