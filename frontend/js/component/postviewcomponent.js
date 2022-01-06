class PostViewComponent extends Fronty.ModelComponent {
  constructor(postsModel, userModel, router) {
    super(Handlebars.templates.postview, postsModel);

    this.postsModel = postsModel; // posts
    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.postsService = new PostsService();

    
    //console.log(selectedId);

    this.addEventListener('click', '#likeBton', () => { //La anicmacion de like funciona sin estar logeado, pero no se añade el like en back
      var selectedId = this.router.getRouteQueryParam('id');
      var btn = document.getElementById('likeBton');
      if (btn.classList.contains("far")) {//Accion de Like
        btn.classList.remove("far");
        btn.classList.add("fas");
        this.postsService.addLike(selectedId);
      } else {//Accion de unlike
        btn.classList.remove("fas");
        btn.classList.add("far");
        this.postsService.deleteLike(selectedId);
      }

     
  });


  }



  onStart() {
    var selectedId = this.router.getRouteQueryParam('id');
    this.loadPost(selectedId);
  }

  loadPost(postId) {
    if (postId != null) {
      this.postsService.findPost(postId)
        .then((post) => {
          this.postsModel.setSelectedPost(post);
        });
    }
  }
}
