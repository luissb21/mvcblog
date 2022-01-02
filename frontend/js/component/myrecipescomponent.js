class MyRecipesComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.myrecipes, postsModel, null, null);


    this.postsModel = postsModel;
    this.userModel = userModel;
    this.addModel('user', userModel);
    this.router = router;

    this.postsService = new PostsService();

  }

  onStart() {
    this.updatePosts();
  }

  updatePosts() {
    this.postsService.findMyRecipes().then((data) => {

      this.postsModel.setPosts(
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
