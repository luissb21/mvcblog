class IngredientModel extends Fronty.Model {

    constructor(name) {
      super('IngredientModel'); //call super
      
      if (name) {
        this.name = name;
      }
        
    }
  
  }
  