class TuplaModel extends Fronty.Model {

  constructor(ingr_name,cantidad) {
    super('TuplaModel'); //call super
    
    if (ingr_name) {
      this.ingr_name = ingr_name;
    }

    if (cantidad) {
      this.cantidad = cantidad;
    }
    

  }

}
