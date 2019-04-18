$ = jQuery.noConflict();
$(document).ready(function(){
    console.log(url_eliminar);
    $('.borrar_registro').on('click', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-reservacion');
        swal({
            title: 'Estas seguro?',
            text: "Esta acción no se puede revertir!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si! Eliminar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
               if(result.value) {
                  $.ajax({
                      type: 'post',
                      data: {
                          'action': 'lapizzeria_eliminar',
                          'id': id,
                          'tipo': 'eliminar'
                      },
                      url: url_eliminar.ajaxurl,
                      success: function(data) {
                          var resultado = JSON.parse(data);
                          if(resultado.respuesta == 1) {
                              swal(
                                  'Eliminado!',
                                  'Your file has been deleted.',
                                  'success'
                              )
                              jQuery("[data-reservacion='"+resultado.id+"'").parent().parent().remove();
                          } else {
                              swal(
                                  'Error!',
                                  'No se pudo eliminar, intente de nuevo.',
                                  'error',
                              );
                          }

                      }
                  });
               } else if (result.dismiss === 'cancel') {
                      swal(
                        'Cancelado',
                        'No se Eliminó',
                        'error'
                     )
               }
          });
    });
});
