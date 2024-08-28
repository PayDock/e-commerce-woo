<script>
  jQuery( function($) {
    $( window ).on( "beforeunload", function() {
      $('.form-field.wc-order-status').remove();
    } );
  } )
</script>
