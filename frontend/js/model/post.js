class PostModel extends Fronty.Model {

  constructor(id, title, content,author) {
    super('PostModel'); //call super
    
    if (id) {
      this.id = id;
    }
    
    if (title) {
      this.title = title;
    }

    if (content) {
      this.content = content;
    }
    
    if (author) {
      this.author = author;
    }
  }

  setTitle(title) {
    this.set((self) => {
      self.title = title;
    });
  }

  setAuthor(author) {
    this.set((self) => {
      self.author = author;
    });
  }
}
