class PostsService {
  constructor() {

  }

  findAllPosts() {
    return $.get(AppConfig.backendServer+'/rest/post');
  }

  findMyRecipes() {
    return $.get(AppConfig.backendServer+'/rest/myrecipes');
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
