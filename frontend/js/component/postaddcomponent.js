class PostAddComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.postadd, postsModel); 
    this.postsModel = postsModel; // posts

    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.ingredientsModel = new IngredientsModel();
    this.addModel('ingredients', this.ingredientsModel);

    var postsService = new PostsService();

     postsService.findAllIngredients().then((data) => {
      this.ingredientsModel.setIngredients(
        // create a Fronty.Model for each item retrieved from the backend
        data.map(
          (item) => new IngredientModel(item.name)
      ));
    });


    this.addEventListener('click', '#savebutton', () => {
      var newPost = {};
      newPost.title = $('#title').val();
      newPost.content = $('#content').val();
      newPost.author = this.userModel.currentUser;
      newPost.time = $('#time').val();
      newPost.date = $('#date').val();
      newPost.image =  document.getElementById('image').files[0].name; //Nombre imagen
      
      var reader = new FileReader();
      reader.readAsDataURL(document.getElementById('image').files[0]);
      reader.onload = function () {
        newPost.imgb64 = reader.result;
      

      postsService.addPost(newPost)
        .then(() => {
          router.goToPage('posts');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            postsModel.set(() => {
              postsModel.errors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
   } //function
  });
  }
}
