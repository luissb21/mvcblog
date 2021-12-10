class PostModel extends Fronty.Model {

  constructor(id, title, content,author,time,date,image) {
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

    if (time) {
      this.time = time;
    }

    if (date) {
      this.date = date;
    }

    if (image) {
      this.image = image;
    }
  }

  setTitle(title) {
    this.set((self) => {
      self.title = title;
    });
  }
  
  setContent(content) {
    this.set((self) => {
      self.content = content;
    });
  }

  setAuthor(author) {
    this.set((self) => {
      self.author = author;
    });
  }

  setTime(time) {
    this.set((self) => {
      self.time = time;
    });
  }

  setDate(date) {
    this.set((self) => {
      self.date = date;
    });
  }

  setImage(image) {
    this.set((self) => {
      self.image = image;
    });
  }


}
