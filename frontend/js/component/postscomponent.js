class PostsComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.poststable, postsModel, null, null);
    
    this.postsModelLikes = new PostsModel();
    this.addModel('postsLiked', this.postsModelLikes);

    this.postsModel = postsModel;
    this.userModel = userModel;
    this.addModel('user', userModel);
    this.router = router;

    this.postsService = new PostsService();

    


  }

  onStart() {
    this.updatePosts();
    this.updatePostsLiked();
  }

  updatePosts() {
    this.postsService.findAllPosts().then((data) => {

      this.postsModel.setPosts(
        // create a Fronty.Model for each item retrieved from the backend
        data.map(
          (item) => new PostModel(item.id, item.title, item.content, item.author, item.time, item.date, item.image)
      ));
    });
  }

  updatePostsLiked() {
  this.postsService.findPostsLiked().then((data) => {

    this.postsModelLikes.setPosts(
      // create a Fronty.Model for each item retrieved from the backend
      data.map(
        (item) => new PostModel(item.id, item.title, item.content, item.author, item.time, item.date, item.image,null,null,null,item.like)
    ));
  });
}

  // Override
  createChildModelComponent(className, element, id, modelItem) {
    return new PostRowComponent(modelItem, this.userModel, this.router, this);
  }
}

class PostRowComponent extends Fronty.ModelComponent {
  constructor(postModel, userModel, router, postsComponent) {
    super(Handlebars.templates.postrow, postModel, null, null);
    
    this.postsComponent = postsComponent;
   
    this.postModel = userModel;
    this.addModel('user', userModel); // a secondary model

    this.postsModelLikes = new PostsModel();

    this.router = router;


    this.addEventListener('click', '.remove-button', (event) => {
      if (confirm(I18n.translate('Are you sure?'))) {
        var postId = event.target.getAttribute('item');
        this.postsComponent.postsService.deletePost(postId)
          .fail(() => {
            alert('post cannot be deleted')
          })
          .always(() => {
            this.postsComponent.updatePosts();
          });
      }
    });

    this.addEventListener('click', '.edit-button', (event) => {
      var postId = event.target.getAttribute('item');
      this.router.goToPage('edit-post?id=' + postId);
    });
  }

}
