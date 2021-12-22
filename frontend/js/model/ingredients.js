
class IngredientsModel extends Fronty.Model {

  constructor() {
    super('IngredientsModel'); //call super

    // model attributes
    this.ingredients = [];
  }

  setSelectedIngredient(ingredient) {
    this.set((self) => {
      self.selectedIngredient = ingredient;
    });
  }

  setIngredients(ingredients) {
    this.set((self) => {
      self.ingredients = ingredients;
    });
  }
}
