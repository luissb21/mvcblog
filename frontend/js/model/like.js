class LikeModel extends Fronty.Model {

    constructor(id, numLikes) {
      super('LikeModel'); //call super
      
      if (id) {
        this.id = id;
      }

      if (numLikes) {
        this.numLikes = numLikes;
      }
        
    }
  
  }
  