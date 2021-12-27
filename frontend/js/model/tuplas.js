class TuplasModel extends Fronty.Model {

  constructor() {
    super('TuplasModel'); //call super

    // model attributes
    this.tuplas = [];
  }

  setSelectedTupla(tupla) {
    this.set((self) => {
      self.selectedPost = tupla;
    });
  }

  setTuplas(tuplas) {
    this.set((self) => {
      self.tuplas = tuplas;
    });
  }
}
