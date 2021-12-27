class PostEditComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.postedit, postsModel);
    this.postsModel = postsModel; // posts
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;
    var router = router;

    this.ingredientsModel = new IngredientsModel();
    this.addModel('ingredients', this.ingredientsModel);

    this.postsService = new PostsService();
    var postsService = new PostsService();
    var postsModel = postsModel;

    //Listado de Ingredientes en la BD
    postsService.findAllIngredients().then((data) => {
      this.ingredientsModel.setIngredients(
        // create a Fronty.Model for each item retrieved from the backend
        data.map(
          (item) => new IngredientModel(item.name)
        ));
    });

    this.tuplasModel = new TuplasModel(); //Objetos post_ingr (ingrediente, cantidad)
    this.addModel('tuplas', this.tuplasModel);




    this.addEventListener('click', '#savebutton', () => {
      postsModel.selectedPost.title = $('#title').val();
      postsModel.selectedPost.content = $('#content').val();
      postsModel.selectedPost.time = $('#time').val();
      postsModel.selectedPost.date = $('#date').val();
      postsModel.selectedPost.image = document.getElementById('image').files[0].name;

      var reader = new FileReader();
      reader.readAsDataURL(document.getElementById('image').files[0]);

      reader.onload = function () {
        postsModel.selectedPost.imgb64 = reader.result;

        postsService.savePost(postsModel.selectedPost)
          .then(() => {
            postsModel.set((model) => {
              model.errors = []
            });
            router.goToPage('posts');
          })
          .fail((xhr, errorThrown, statusText) => {
            if (xhr.status == 400) {
              postsModel.set((model) => {
                model.errors = xhr.responseJSON;
              });
            } else {
              alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
            }
          });
      }
    });

  }

  onStart() {
    var selectedId = this.router.getRouteQueryParam('id');
    if (selectedId != null) {
      this.postsService.findPost(selectedId)
        .then((post) => {
          this.postsModel.setSelectedPost(post);
        });

      //Ingredientes con sus Cantidades de una receta
      this.postsService.findRecipeIngredients(selectedId).then((data) => {
        this.tuplasModel.setTuplas(
          // create a Fronty.Model for each item retrieved from the backend
          data.map(
            (item) => new TuplaModel(item.ingr_name, item.cantidad)
          ));
      });

    }
  }
}
