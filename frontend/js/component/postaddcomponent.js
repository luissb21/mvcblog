class PostAddComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.postadd, postsModel); //Cambiar postedit por postadd y añadir en appjs
    this.postsModel = postsModel; // posts
    
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.postsService = new PostsService();

    //Obtencion y redireccionamiento a la carpeta /res de las imagenes subidas al crear una receta
    var selectedImg = null;
    this.addEventListener('change', '#image', (event) => {
      //console.log(event);
      selectedImg = event.target.files[0];
      //console.log(selectedImg);

});


    this.addEventListener('click', '#savebutton', () => {
      var newPost = {};
      newPost.title = $('#title').val();
      newPost.content = $('#content').val();
      newPost.author = this.userModel.currentUser;
      newPost.time = $('#time').val();
      newPost.date = $('#date').val();
      newPost.image = $('#image').val();
      this.postsService.addPost(newPost)
        .then(() => {
          this.router.goToPage('posts');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.postsModel.set(() => {
              this.postsModel.errors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });
  }
  
  onStart() {
    this.postsModel.setSelectedPost(new PostModel());
  }
}
