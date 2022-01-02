Handlebars.registerHelper('if_eq', function(a, b, opts) {
  if (a == b)
    return opts.fn(this);
  else
    return opts.inverse(this);
});


Handlebars.registerHelper('add_ingr', function() {
  $(document).ready(function() {
    $(".add_item_btn").click(function(e) {
      e.preventDefault();
      $("#show_item").prepend(`<div class="row">
                <div class="col-md-4 mb-3">
                  <input type="text" list="ingredientsList" name="ingredients[]" class="form-control form_input" placeholder="Ingredient">
                </div>
  
                <div class="col-md-3 mb-3">
                  <input type="text" name="cantidad[]" class="form-control form_input" placeholder="Amount">
                </div>
  
                <div class="col-md-2 mb-3 d-grid">
                  <button class="btn btn-danger remove_item_btn">Remove</button>
                </div>
              </div>`);
    });
  
    $(document).on('click', '.remove_item_btn', function(e) {
      e.preventDefault();
      let row_item = $(this).parent().parent();
      $(row_item).remove();
    });
  
  });
});

// Verifica que value este contenido en array
Handlebars.registerHelper("contains", function( value, array, options ){
	array = ( array instanceof Array ) ? array : [array];
	return (array.indexOf(value) > -1) ? options.fn( this ) : "";
});


