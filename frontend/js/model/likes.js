class LikesModel extends Fronty.Model {

    constructor() {
      super('LikesModel'); //call super
  
      // model attributes
      this.likes = [];
    }
  
    setSelectedLike(like) {
      this.set((self) => {
        self.selectedLike = like;
      });
    }
  
    setLikes(likes) {
      this.set((self) => {
        self.likes = likes;
      });
    }
  }
  