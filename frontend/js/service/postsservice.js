class PostsService {
  constructor() {

  }

  findAllPosts() {//Public HOME (12 last post)
    return $.get(AppConfig.backendServer+'/rest/post');
  }

  findPostsLiked() {//Public HOME (12 last post)
    return $.get(AppConfig.backendServer+'/rest/likes');
  }

  findMyRecipes() {
    return $.get(AppConfig.backendServer+'/rest/myrecipes');
  }

  findAllRecipes() {
    return $.get(AppConfig.backendServer+'/rest/allrecipes');
  }

  countLikes() {
    return $.get(AppConfig.backendServer+'/rest/count');
  }

  filters(filters) {
    return $.get(AppConfig.backendServer+'/rest/filters/' + filters);
  }

  findAllIngredients() {
    return $.get(AppConfig.backendServer+'/rest/ingredients');
  }

  findRecipeIngredients(id) {
    return $.get(AppConfig.backendServer+'/rest/recingr/' + id);
  }

  findPost(id) {
    return $.get(AppConfig.backendServer+'/rest/post/' + id);
  }

  deletePost(id) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/post/' + id,
      method: 'DELETE'
    });
  }

  savePost(post) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/post/' + post.id,
      method: 'PUT',
      data: JSON.stringify(post),
      contentType: 'application/json'
    });
  }

  addPost(post) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/post',
      method: 'POST',
      data: JSON.stringify(post),
      contentType: 'application/json'
    });
  }

  // createComment(postid, comment) {
  //   return $.ajax({
  //     url: AppConfig.backendServer+'/rest/post/' + postid + '/comment',
  //     method: 'POST',
  //     data: JSON.stringify(comment),
  //     contentType: 'application/json'
  //   });
  // }

  

}
